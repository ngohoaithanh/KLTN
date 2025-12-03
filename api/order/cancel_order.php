<?php
header('Content-Type: application/json; charset=utf-8');
include_once('../../config/database.php'); // Đảm bảo đường dẫn này đúng
include_once('../../config/auth_check.php');
$db = new clsKetNoi();
$conn = $db->moKetNoi();
$response = [];

// Lấy dữ liệu từ POST request
if (isset($_POST['order_id'])) {
    $orderId = intval($_POST['order_id']);

    if ($conn) {
        $conn->begin_transaction();
        try {
            // CÂU LỆNH QUAN TRỌNG: Chỉ cho phép hủy khi trạng thái là 'pending'
            // Điều này giúp tránh trường hợp khách hàng hủy khi shipper vừa nhận đơn
            $stmt_update = $conn->prepare("UPDATE orders SET status = 'cancelled' WHERE ID = ? AND status = 'pending'");
            $stmt_update->bind_param("i", $orderId);
            $stmt_update->execute();

            // Kiểm tra xem có đơn hàng nào thực sự được cập nhật không
            if ($stmt_update->affected_rows > 0) {
                // Thêm vào bảng tracking để khách hàng biết
                $trackingMessage = "Đơn hàng đã được hủy bởi khách hàng.";
                $stmt_insert = $conn->prepare("INSERT INTO trackings (OrderID, Status) VALUES (?, ?)");
                $stmt_insert->bind_param("is", $orderId, $trackingMessage);
                $stmt_insert->execute();

                // 1. Lấy ID Shipper đang nhận đơn này (nếu có)
                $stmt_get_shipper = $conn->prepare("SELECT ShipperID FROM orders WHERE ID = ?");
                $stmt_get_shipper->bind_param("i", $orderId);
                $stmt_get_shipper->execute();
                $res = $stmt_get_shipper->get_result()->fetch_assoc();
                $shipperId = $res['ShipperID'];
                $stmt_get_shipper->close();

                // 2. Nếu đã có Shipper nhận, gửi thông báo cho họ
                if ($shipperId) {
                    $title = "Đơn hàng đã bị hủy";
                    $msg = "Khách hàng đã hủy đơn hàng #" . $orderId . ". Bạn không cần đến lấy hàng nữa.";
                    $type = "order_cancel"; // Icon màu đỏ

                    $stmt_noti = $conn->prepare("INSERT INTO notifications (UserID, Title, Message, Type, ReferenceID) VALUES (?, ?, ?, ?, ?)");
                    $stmt_noti->bind_param("isssi", $shipperId, $title, $msg, $type, $orderId);
                    $stmt_noti->execute();
                    $stmt_noti->close();
                }

                $conn->commit();
                $response = ['success' => true, 'message' => 'Hủy đơn hàng thành công.'];
            } else {
                // Không có dòng nào được cập nhật, có thể do đơn hàng không ở trạng thái 'pending'
                throw new Exception('Không thể hủy đơn. Đơn hàng có thể đã được shipper chấp nhận.');
            }
        } catch (Exception $e) {
            $conn->rollback();
            $response = ['success' => false, 'error' => $e->getMessage()];
        }
    } else {
        $response = ['success' => false, 'error' => 'Không thể kết nối đến database.'];
    }
} else {
    $response = ['success' => false, 'error' => 'Thiếu thông tin order_id.'];
}

$db->dongKetNoi($conn);
echo json_encode($response);
?>