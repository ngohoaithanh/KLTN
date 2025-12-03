<?php
// Thiết lập header để đảm bảo client hiểu đây là JSON
header('Content-Type: application/json; charset=utf-8');

// Sử dụng đường dẫn tương đối để include file database.php
include_once('../../config/database.php');
include_once('../../config/auth_check.php');

// Khởi tạo đối tượng response
$response = array();

// --- BƯỚC 1: KIỂM TRA DỮ LIỆU ĐẦU VÀO ---
if (isset($_POST['order_id']) && isset($_POST['new_status'])) {

    // --- BƯỚC 2: KẾT NỐI CSDL ---
    $db = new clsKetNoi();
    $conn = $db->moKetNoi();

    if ($conn) {
        $orderId = intval($_POST['order_id']);
        $newStatus = $_POST['new_status'];
        
        // Lấy tham số Reason (TÙY CHỌN, dùng cho delivery_failed)
        $reason = isset($_POST['reason']) ? trim($_POST['reason']) : null;
        // Lấy tham số Photo URL (TÙY CHỌN, dùng cho picked_up/delivered)
        $photoUrl = isset($_POST['photo_url']) ? trim($_POST['photo_url']) : null; 
        
        // --- BƯỚC 3: SỬ DỤNG TRANSACTION ---
        $conn->begin_transaction();

        try {
            // 1. XÁC ĐỊNH CỘT ẢNH CẦN CẬP NHẬT
            $photoColumn = null;
            if ($newStatus === 'picked_up') {
                $photoColumn = 'PickUp_Photo_Path';
            } else if ($newStatus === 'delivered') {
                $photoColumn = 'Delivery_Photo_Path';
            }

            // 2. TẠO CÂU LỆNH SQL CẬP NHẬT `orders` (Linh hoạt cho status và photo)
            $setClauses = ["status = ?"];
            $bindTypes = "s";
            $bindParams = [&$newStatus]; // Tham số status BẮT BUỘC
            
            // Xử lý tham số ảnh (Nếu có URL và trạng thái yêu cầu)
            if ($photoColumn && !empty($photoUrl)) {
                $setClauses[] = "$photoColumn = ?";
                $bindTypes .= "s";
                $bindParams[] = &$photoUrl; // Tham số photoUrl
            }
            // Không cần xử lý reason ở đây, vì bạn đã quyết định lưu reason vào bảng trackings

            // Hoàn thành câu lệnh SQL
            $sql = "UPDATE orders SET " . implode(', ', $setClauses) . " WHERE ID = ?";
            $bindTypes .= "i"; // Thêm kiểu cho orderId
            $bindParams[] = &$orderId; // Tham số orderId

            // Chuẩn bị và thực thi câu lệnh SQL
            $stmt_update = $conn->prepare($sql);
            
            // Chèn $bindTypes vào đầu mảng $bindParams để sử dụng trong bind_param
            array_unshift($bindParams, $bindTypes);
            
            // Gọi bind_param động
            if ($stmt_update) {
                call_user_func_array(array($stmt_update, 'bind_param'), $bindParams);
                $stmt_update->execute();
                $stmt_update->close();
            } else {
                 throw new Exception("Lỗi khi chuẩn bị câu lệnh UPDATE: " . $conn->error);
            }

            // 3. THÊM VÀO BẢNG `trackings`
            $trackingMessage = "";
            switch ($newStatus) {
                case 'accepted':
                    $trackingMessage = "Shipper đã chấp nhận đơn hàng và đang đến lấy hàng.";
                    break;
                case 'picked_up':
                    $trackingMessage = "Shipper đã lấy hàng thành công.";
                    break;
                case 'in_transit':
                    $trackingMessage = "Đơn hàng đang trên đường giao đến bạn.";
                    break;
                case 'delivered':
                    $trackingMessage = "Giao hàng thành công!";
                    break;
                case 'delivery_failed':
                    $trackingMessage = "Giao hàng không thành công.";
                    if (!empty($reason)) {
                        $trackingMessage .= " Lý do: " . $reason;
                    }
                    break;
            }

            $stmt_get_cust = $conn->prepare("SELECT CustomerID FROM orders WHERE ID = ?");
            $stmt_get_cust->bind_param("i", $orderId);
            $stmt_get_cust->execute();
            $custRes = $stmt_get_cust->get_result()->fetch_assoc();
            $customerId = $custRes['CustomerID'];
            $stmt_get_cust->close();

            if ($customerId) {
                $title = ""; $msg = "";
                
                if ($newStatus == 'picked_up') {
                    $title = "Đã lấy hàng";
                    $msg = "Tài xế đã lấy hàng và đang đi giao.";
                } elseif ($newStatus == 'delivered') {
                    $title = "Giao hàng thành công";
                    $msg = "Đơn hàng #" . $orderId . " đã được giao thành công. Hãy đánh giá tài xế nhé!";
                } elseif ($newStatus == 'delivery_failed') {
                    $title = "Giao hàng thất bại";
                    $msg = "Rất tiếc, đơn hàng #" . $orderId . " giao không thành công.";
                    if (!empty($reason)) {
                        $msg .= " Lý do: " . $reason;
                    }
                    // Type = 'warning' (để hiển thị icon cảnh báo vàng/cam)
                }
                
                if (!empty($title)) {
                    $stmt_noti = $conn->prepare("INSERT INTO notifications (UserID, Title, Message, Type, ReferenceID) VALUES (?, ?, ?, ?, ?)");
                    $stmt_noti->bind_param("isssi", $customerId, $title, $msg, "order", $orderId);
                    $stmt_noti->execute();
                    $stmt_noti->close();
                }
            }

            if (!empty($trackingMessage)) {
                $stmt_insert = $conn->prepare("INSERT INTO trackings (OrderID, Status) VALUES (?, ?)");
                $stmt_insert->bind_param("is", $orderId, $trackingMessage);
                $stmt_insert->execute();
                $stmt_insert->close();
            }

            // 4. GHI GIAO DỊCH (CHỈ KHI DELIVERED) (Logic giữ nguyên)
            if ($newStatus == 'delivered') {
                // ... (Logic ghi transactions giữ nguyên) ...
                $stmt_get_order = $conn->prepare("SELECT ShipperID, Shippingfee, COD_amount, CODFee FROM orders WHERE ID = ?");
                $stmt_get_order->bind_param("i", $orderId);
                $stmt_get_order->execute();
                $order_details = $stmt_get_order->get_result()->fetch_assoc();
                $stmt_get_order->close();

                if ($order_details && $order_details['ShipperID'] != null) { 
                    $shipperId = $order_details['ShipperID'];
                    $shippingFee = $order_details['Shippingfee'];
                    $codAmount = $order_details['COD_amount'];
                    $codFee = $order_details['CODFee'];

                    // Ghi nhận thu nhập Phí Ship
                    $stmt_ship = $conn->prepare("INSERT INTO transactions (UserID, OrderID, Type, Amount, Status) VALUES (?, ?, 'shipping_fee', ?, 'completed')");
                    $stmt_ship->bind_param("iid", $shipperId, $orderId, $shippingFee);
                    $stmt_ship->execute();
                    $stmt_ship->close();

                    // Ghi nhận việc Thu COD
                    if ($codAmount > 0 || $codFee > 0) {
                        $totalCodCollected = $codAmount + $codFee;
                        $stmt_cod = $conn->prepare("INSERT INTO transactions (UserID, OrderID, Type, Amount, Status) VALUES (?, ?, 'collect_cod', ?, 'completed')");
                        $stmt_cod->bind_param("iid", $shipperId, $orderId, $totalCodCollected);
                        $stmt_cod->execute();
                        $stmt_cod->close();
                    }
                }
            }
            
            // Commit Transaction
            $conn->commit();
            $response['success'] = true;
            $response['message'] = "Cập nhật trạng thái và tracking thành công!";

        } catch (Exception $e) {
            // Rollback Transaction
            $conn->rollback();
            $response['success'] = false;
            $response['message'] = "Có lỗi xảy ra trong quá trình cập nhật: " . $e->getMessage();
        }

        // Đóng kết nối
        $db->dongKetNoi($conn);

    } else {
        $response['success'] = false;
        $response['message'] = "Không thể kết nối đến cơ sở dữ liệu.";
    }
} else {
    $response['success'] = false;
    $response['message'] = "Thiếu tham số `order_id` hoặc `new_status`.";
}

// --- BƯỚC 4: TRẢ KẾT QUẢ VỀ CHO ANDROID ---
echo json_encode($response);
?>