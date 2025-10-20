<?php
// Thiết lập header để đảm bảo client hiểu đây là JSON
header('Content-Type: application/json; charset=utf-8');

// Sử dụng đường dẫn tương đối để include file database.php
include_once('../../config/database.php');

// Khởi tạo đối tượng response
$response = array();

// --- BƯỚC 1: KIỂM TRA DỮ LIỆU ĐẦU VÀO ---
// Kiểm tra các tham số BẮT BUỘC
if (isset($_POST['order_id']) && isset($_POST['new_status'])) {

    // --- BƯỚC 2: KẾT NỐI CSDL THEO CÁCH CỦA BẠN ---
    $db = new clsKetNoi();
    $conn = $db->moKetNoi();

    // Kiểm tra kết nối có thành công không
    if ($conn) {
        $orderId = intval($_POST['order_id']);
        $newStatus = $_POST['new_status'];
        
        // Lấy tham số Reason (TÙY CHỌN)
        $reason = isset($_POST['reason']) ? trim($_POST['reason']) : null; 

        // --- BƯỚC 3: SỬ DỤNG TRANSACTION ĐỂ ĐẢM BẢO AN TOÀN DỮ LIỆU ---
        $conn->begin_transaction();

        try {
            // --- Cập nhật bảng `orders` (Giữ nguyên) ---
            $stmt_update = $conn->prepare("UPDATE orders SET status = ? WHERE ID = ?");
            $stmt_update->bind_param("si", $newStatus, $orderId);
            $stmt_update->execute();

            // --- Thêm vào bảng `trackings` ---
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
                    // XỬ LÝ LÝ DO THẤT BẠI TẠI ĐÂY
                    $trackingMessage = "Giao hàng không thành công.";
                    if (!empty($reason)) {
                        $trackingMessage .= " Lý do: " . $reason;
                    }
                    break;
            }

            if (!empty($trackingMessage)) {
                $stmt_insert = $conn->prepare("INSERT INTO trackings (OrderID, Status) VALUES (?, ?)");
                $stmt_insert->bind_param("is", $orderId, $trackingMessage);
                $stmt_insert->execute();
            }

            // =======================================================
            // ## THÊM CODE GHI GIAO DỊCH VÀO ĐÂY ##
            // =======================================================

            if ($newStatus == 'delivered') {
            // Lấy thông tin chi tiết của đơn hàng để ghi giao dịch
            $stmt_get_order = $conn->prepare("SELECT ShipperID, Shippingfee, COD_amount, CODFee FROM orders WHERE ID = ?");
            $stmt_get_order->bind_param("i", $orderId);
            $stmt_get_order->execute();
            $order_details = $stmt_get_order->get_result()->fetch_assoc();
            $stmt_get_order->close();

            if ($order_details && $order_details['ShipperID'] != null) { // Chỉ ghi giao dịch nếu có shipper
                $shipperId = $order_details['ShipperID'];
                $shippingFee = $order_details['Shippingfee'];
                $codAmount = $order_details['COD_amount'];
                $codFee = $order_details['CODFee'];

                // 1. Ghi nhận thu nhập Phí Ship
                $stmt_ship = $conn->prepare("INSERT INTO transactions (UserID, OrderID, Type, Amount, Status) VALUES (?, ?, 'shipping_fee', ?, 'completed')");
                // "iid" là integer, integer, double
                $stmt_ship->bind_param("iid", $shipperId, $orderId, $shippingFee);
                $stmt_ship->execute();
                $stmt_ship->close();

                // 2. Ghi nhận việc Thu COD (BAO GỒM CẢ PHÍ COD)
                if ($codAmount > 0 || $codFee > 0) {
                    $totalCodCollected = $codAmount + $codFee;

                    $stmt_cod = $conn->prepare("INSERT INTO transactions (UserID, OrderID, Type, Amount, Status) VALUES (?, ?, 'collect_cod', ?, 'completed')");
                    // "iid" là integer, integer, double
                    $stmt_cod->bind_param("iid", $shipperId, $orderId, $totalCodCollected);
                    $stmt_cod->execute();
                    $stmt_cod->close();
                }
            }
        }

            
            // Nếu mọi thứ thành công, xác nhận transaction
            $conn->commit();
            $response['success'] = true;
            $response['message'] = "Cập nhật trạng thái và tracking thành công!";

        } catch (Exception $e) {
            // Nếu có lỗi, hủy bỏ mọi thay đổi
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