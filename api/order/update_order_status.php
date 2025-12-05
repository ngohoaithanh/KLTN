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

    $db = new clsKetNoi();
    $conn = $db->moKetNoi();

    if ($conn) {
        $orderId   = intval($_POST['order_id']);
        $newStatus = trim($_POST['new_status']);

        // Lý do (cho delivery_failed)
        $reason  = isset($_POST['reason'])    ? trim($_POST['reason'])    : null;
        // URL ảnh minh chứng (cho picked_up / delivered)
        $photoUrl = isset($_POST['photo_url']) ? trim($_POST['photo_url']) : null;

        // BẮT ĐẦU TRANSACTION
        $conn->begin_transaction();

        try {
            /* ==================== 1. UPDATE BẢNG orders ==================== */

            // Xác định cột ảnh cần cập nhật (nếu có)
            $photoColumn = null;
            if ($newStatus === 'picked_up') {
                $photoColumn = 'PickUp_Photo_Path';
            } elseif ($newStatus === 'delivered') {
                $photoColumn = 'Delivery_Photo_Path';
            }

            if ($photoColumn && !empty($photoUrl)) {
                // Có ảnh & có cột tương ứng
                $sql = "UPDATE orders SET status = ?, $photoColumn = ? WHERE ID = ?";
                $stmt_update = $conn->prepare($sql);
                if (!$stmt_update) {
                    throw new Exception("Lỗi chuẩn bị UPDATE (có ảnh): " . $conn->error);
                }
                $stmt_update->bind_param("ssi", $newStatus, $photoUrl, $orderId);
            } else {
                // Không có ảnh (hoặc trạng thái không cần ảnh)
                $sql = "UPDATE orders SET status = ? WHERE ID = ?";
                $stmt_update = $conn->prepare($sql);
                if (!$stmt_update) {
                    throw new Exception("Lỗi chuẩn bị UPDATE (không ảnh): " . $conn->error);
                }
                $stmt_update->bind_param("si", $newStatus, $orderId);
            }

            $stmt_update->execute();
            $stmt_update->close();

            /* ==================== 2. Tracking message ==================== */

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

            // Lấy CustomerID để bắn notification
            $customerId = null;
            $stmt_get_cust = $conn->prepare("SELECT CustomerID FROM orders WHERE ID = ?");
            if ($stmt_get_cust) {
                $stmt_get_cust->bind_param("i", $orderId);
                $stmt_get_cust->execute();
                $resultCust = $stmt_get_cust->get_result();
                if ($resultCust) {
                    $custRes = $resultCust->fetch_assoc();
                    if ($custRes && isset($custRes['CustomerID'])) {
                        $customerId = $custRes['CustomerID'];
                    }
                }
                $stmt_get_cust->close();
            }

            if (!empty($customerId)) {
                $title = "";
                $msg   = "";
                $type  = "order";

                if ($newStatus == 'picked_up') {
                    $title = "Đã lấy hàng";
                    $msg   = "Tài xế đã lấy hàng và đang đi giao.";
                } elseif ($newStatus == 'delivered') {
                    $title = "Giao hàng thành công";
                    $msg   = "Đơn hàng #" . $orderId . " đã được giao thành công. Hãy đánh giá tài xế nhé!";
                } elseif ($newStatus == 'delivery_failed') {
                    $title = "Giao hàng thất bại";
                    $msg   = "Rất tiếc, đơn hàng #" . $orderId . " giao không thành công.";
                    if (!empty($reason)) {
                        $msg .= " Lý do: " . $reason;
                    }
                }

                if (!empty($title)) {
                    $stmt_noti = $conn->prepare("INSERT INTO notifications (UserID, Title, Message, Type, ReferenceID) VALUES (?, ?, ?, ?, ?)");
                    if ($stmt_noti) {
                        $stmt_noti->bind_param("isssi", $customerId, $title, $msg, $type, $orderId);
                        $stmt_noti->execute();
                        $stmt_noti->close();
                    }
                }
            }

            // Ghi vào trackings (nếu có message)
            if (!empty($trackingMessage)) {
                $stmt_insert = $conn->prepare("INSERT INTO trackings (OrderID, Status) VALUES (?, ?)");
                if ($stmt_insert) {
                    $stmt_insert->bind_param("is", $orderId, $trackingMessage);
                    $stmt_insert->execute();
                    $stmt_insert->close();
                }
            }

            /* ==================== 3. Transactions khi DELIVERED ==================== */

            if ($newStatus == 'delivered') {
                $stmt_get_order = $conn->prepare("SELECT ShipperID, Shippingfee, COD_amount, CODFee FROM orders WHERE ID = ?");
                if ($stmt_get_order) {
                    $stmt_get_order->bind_param("i", $orderId);
                    $stmt_get_order->execute();
                    $order_details = $stmt_get_order->get_result()->fetch_assoc();
                    $stmt_get_order->close();

                    if ($order_details && !empty($order_details['ShipperID'])) {
                        $shipperId   = (int)$order_details['ShipperID'];
                        $shippingFee = (float)$order_details['Shippingfee'];
                        $codAmount   = (float)$order_details['COD_amount'];
                        $codFee      = (float)$order_details['CODFee'];

                        // Ghi nhận thu nhập Phí Ship
                        $stmt_ship = $conn->prepare("INSERT INTO transactions (UserID, OrderID, Type, Amount, Status) VALUES (?, ?, 'shipping_fee', ?, 'completed')");
                        if ($stmt_ship) {
                            $stmt_ship->bind_param("iid", $shipperId, $orderId, $shippingFee);
                            $stmt_ship->execute();
                            $stmt_ship->close();
                        }

                        // Ghi nhận việc Thu COD
                        if ($codAmount > 0 || $codFee > 0) {
                            $totalCodCollected = $codAmount + $codFee;
                            $stmt_cod = $conn->prepare("INSERT INTO transactions (UserID, OrderID, Type, Amount, Status) VALUES (?, ?, 'collect_cod', ?, 'completed')");
                            if ($stmt_cod) {
                                $stmt_cod->bind_param("iid", $shipperId, $orderId, $totalCodCollected);
                                $stmt_cod->execute();
                                $stmt_cod->close();
                            }
                        }
                    }
                }
            }

            // Commit Transaction
            $conn->commit();
            $response['success'] = true;
            $response['message'] = "Cập nhật trạng thái và tracking thành công!";

        } catch (Exception $e) {
            $conn->rollback();
            $response['success'] = false;
            $response['message'] = "Có lỗi xảy ra trong quá trình cập nhật: " . $e->getMessage();
        }

        $db->dongKetNoi($conn);

    } else {
        $response['success'] = false;
        $response['message'] = "Không thể kết nối đến cơ sở dữ liệu.";
    }
} else {
    $response['success'] = false;
    $response['message'] = "Thiếu tham số `order_id` hoặc `new_status`.";
}

// Trả JSON về cho Android
echo json_encode($response);
