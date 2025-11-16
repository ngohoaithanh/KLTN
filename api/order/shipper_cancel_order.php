<?php
// Thiết lập header
header('Content-Type: application/json; charset=utf-8');

// Include các file cấu hình
include_once('../../config/database.php');
include_once('../../config/auth_check.php'); // 1. Bắt buộc: Xác thực shipper

// Khởi tạo
$db = new clsKetNoi();
$conn = $db->moKetNoi();
$response = [];

// Lấy ShipperID từ session (đã được auth_check.php xác thực)
$shipperId = $_SESSION['user_id'];

// Kiểm tra dữ liệu đầu vào
if (isset($_POST['order_id']) && isset($_POST['reason'])) {
    $orderId = intval($_POST['order_id']);
    $reason = trim($_POST['reason']);

    if (empty($reason)) {
        $reason = "Lý do không xác định";
    }

    // Bắt đầu Transaction để đảm bảo an toàn dữ liệu
    $conn->begin_transaction();

    try {
        // --- BƯỚC 1: Cập nhật trạng thái Đơn hàng ---
        $stmt_update = $conn->prepare("
            UPDATE orders 
            SET status = 'cancelled' 
            WHERE ID = ? 
              AND ShipperID = ? 
              AND status IN ('accepted', 'picked_up', 'in_transit')
        ");
        $stmt_update->bind_param("ii", $orderId, $shipperId);
        $stmt_update->execute();

        if ($stmt_update->affected_rows == 0) {
            throw new Exception('Không thể hủy đơn hàng này (có thể đơn đã hoàn thành hoặc bị hủy trước đó).');
        }
        $stmt_update->close();

        // --- BƯỚC 2: Thêm vào Lịch sử Tracking ---
        $trackingMessage = "Shipper đã hủy đơn. Lý do: " . $reason;
        $stmt_track = $conn->prepare("INSERT INTO trackings (OrderID, Status) VALUES (?, ?)");
        $stmt_track->bind_param("is", $orderId, $trackingMessage);
        $stmt_track->execute();
        $stmt_track->close();

        // --- BƯỚC 3: Xử lý Phạt Rating cho Shipper ---
        // Logic: Coi như shipper vừa nhận 1 đánh giá 1-sao
        $penaltyRating = 1; 

        $stmt_get_rating = $conn->prepare("SELECT rating_sum, rating_count FROM users WHERE ID = ? FOR UPDATE");
        $stmt_get_rating->bind_param("i", $shipperId);
        $stmt_get_rating->execute();
        $rating_result = $stmt_get_rating->get_result()->fetch_assoc();

        $new_sum = $rating_result['rating_sum'] + $penaltyRating;
        $new_count = $rating_result['rating_count'] + 1;
        $new_average = $new_sum / $new_count;

        $stmt_get_rating->close();

        // Cập nhật lại rating mới
        $stmt_update_rating = $conn->prepare("UPDATE users SET rating_sum = ?, rating_count = ?, rating = ? WHERE ID = ?");
        $stmt_update_rating->bind_param("iidi", $new_sum, $new_count, $new_average, $shipperId);
        $stmt_update_rating->execute();
        $stmt_update_rating->close();

        // --- HOÀN TẤT ---
        $conn->commit();
        $response['success'] = true;
        $response['message'] = "Đã hủy đơn hàng. Đánh giá của bạn đã bị ảnh hưởng.";

    } catch (Exception $e) {
        $conn->rollback();
        $response['success'] = false;
        $response['error'] = $e->getMessage();
    }

} else {
    $response['success'] = false;
    $response['error'] = "Thiếu thông tin 'order_id' hoặc 'reason'.";
}

$db->dongKetNoi($conn);
echo json_encode($response);
?>