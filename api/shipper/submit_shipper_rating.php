<?php
header('Content-Type: application/json; charset=utf-8');
include_once('../../config/database.php'); // Đảm bảo đường dẫn này đúng
include_once('../../config/auth_check.php');
$db = new clsKetNoi();
$conn = $db->moKetNoi();
$response = [];

// Kiểm tra dữ liệu đầu vào từ POST request
if (isset($_POST['shipper_id']) && isset($_POST['rating']) && isset($_POST['order_id'])) {
    $shipperId = intval($_POST['shipper_id']);
    $orderId = intval($_POST['order_id']); // Lấy order_id
    $newRating = intval($_POST['rating']);

    if ($newRating >= 1 && $newRating <= 5) {
        if ($conn) {
            $conn->begin_transaction();
            try {
                // Bước 1: Lấy CustomerID từ đơn hàng để lưu vào bảng ratings
                $stmt_get_customer = $conn->prepare("SELECT CustomerID FROM orders WHERE ID = ?");
                $stmt_get_customer->bind_param("i", $orderId);
                $stmt_get_customer->execute();
                $customer_result = $stmt_get_customer->get_result()->fetch_assoc();
                
                if (!$customer_result) {
                    throw new Exception('Không tìm thấy đơn hàng để lấy thông tin khách hàng.');
                }
                $customerId = $customer_result['CustomerID'];

                // =======================================================
                // ## BƯỚC 2 (MỚI): GHI DỮ LIỆU VÀO BẢNG ratings ##
                // =======================================================
                // UNIQUE KEY trên `order_id` sẽ tự động ngăn việc đánh giá lại
                $stmt_insert_rating = $conn->prepare("INSERT INTO ratings (order_id, shipper_id, customer_id, rating_value) VALUES (?, ?, ?, ?)");
                $stmt_insert_rating->bind_param("iiii", $orderId, $shipperId, $customerId, $newRating);
                $stmt_insert_rating->execute();

                // --- PHẦN 1: TÍNH TOÁN VÀ CẬP NHẬT ĐIỂM TRUNG BÌNH CHO SHIPPER ---
                $stmt_user = $conn->prepare("SELECT rating_sum, rating_count FROM users WHERE ID = ? FOR UPDATE");
                $stmt_user->bind_param("i", $shipperId);
                $stmt_user->execute();
                $result = $stmt_user->get_result()->fetch_assoc();
                
                if ($result) {
                    $new_sum = $result['rating_sum'] + $newRating;
                    $new_count = $result['rating_count'] + 1;
                    $new_average = $new_sum / $new_count;

                    $stmt_update_user = $conn->prepare("UPDATE users SET rating_sum = ?, rating_count = ?, rating = ? WHERE ID = ?");
                    $stmt_update_user->bind_param("iidi", $new_sum, $new_count, $new_average, $shipperId);
                    $stmt_update_user->execute();

                    // =======================================================
                    // ## PHẦN 2 (BỊ THIẾU): ĐÁNH DẤU ĐƠN HÀNG ĐÃ ĐƯỢC ĐÁNH GIÁ ##
                    // =======================================================
                    $stmt_update_order = $conn->prepare("UPDATE orders SET is_rated = TRUE WHERE ID = ?");
                    $stmt_update_order->bind_param("i", $orderId);
                    $stmt_update_order->execute();

                    $conn->commit();
                    $response = ['success' => true, 'message' => 'Cảm ơn bạn đã đánh giá!'];
                } else {
                     throw new Exception('Không tìm thấy shipper.');
                }

            } catch (Exception $e) {
                $conn->rollback();
                $response = ['success' => false, 'error' => 'Có lỗi xảy ra: ' . $e->getMessage()];
            }
        } else {
            $response = ['success' => false, 'error' => 'Không thể kết nối đến database.'];
        }
    } else {
        $response = ['success' => false, 'error' => 'Điểm đánh giá không hợp lệ.'];
    }
} else {
    $response = ['success' => false, 'error' => 'Thiếu thông tin shipper_id, order_id hoặc rating.'];
}

$db->dongKetNoi($conn);
echo json_encode($response);
?>