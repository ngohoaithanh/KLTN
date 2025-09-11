<?php
// Thiết lập header để trả về JSON và mã hóa UTF-8
header('Content-Type: application/json; charset=utf-8');

// Import file cấu hình database (nên đặt các hằng số cấu hình ở đây)
require_once('../../config/database.php');

try {
    // Tạo kết nối PDO (nên sử dụng PDO thay vì MySQLi)
    $db = new clsKetNoi();
    $conn = $db->moKetNoi(); // Hàm này nên trả về đối tượng PDO

    // Kiểm tra xem order_id và status đã được gửi và không rỗng
    if (isset($_REQUEST['order_id'], $_REQUEST['status']) &&
        !empty($_REQUEST['order_id']) && !empty($_REQUEST['status'])) {

        $order_id = filter_var($_REQUEST['order_id'], FILTER_VALIDATE_INT); // Validate order_id
        $status = trim($_REQUEST['status']);
        // $location = isset($_REQUEST['location']) ? trim($_REQUEST['location']) : null; // Không cần ?? cho PHP >= 7

        // Kiểm tra đầu vào hợp lệ
        if ($order_id === false) {
            throw new Exception('order_id không hợp lệ'); // Sử dụng Exception để xử lý lỗi
        }
        if (strlen($status) > 1000)
        {
            throw new Exception('Status quá dài');
        }

        // Kiểm tra sự tồn tại của đơn hàng
        $checkOrderSql = "SELECT ID FROM orders WHERE ID = :order_id";
        $checkOrderStmt = $conn->prepare($checkOrderSql);
        $checkOrderStmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
        $checkOrderStmt->execute();

        if ($checkOrderStmt->rowCount() === 0) {
            throw new Exception('Đơn hàng không tồn tại');
        }

        // Thêm thông tin theo dõi
        $insertTrackingSql = "INSERT INTO trackings (OrderID, Status, Updated_at) VALUES (:order_id, :status, NOW())";
        $insertTrackingStmt = $conn->prepare($insertTrackingSql);
        $insertTrackingStmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
        $insertTrackingStmt->bindParam(':status', $status, PDO::PARAM_STR);
        $insertTrackingStmt->execute();


        $response = [
            'success' => true,
            'tracking_id' => $conn->lastInsertId(), // Use PDO's lastInsertId()
        ];
        echo json_encode($response);


    } else {
        throw new Exception('Thiếu dữ liệu bắt buộc (order_id và status)');
    }

} catch (Exception $e) {
    // Xử lý lỗi tập trung
    $response = [
        'success' => false,
        'error' => $e->getMessage(), // Lấy thông báo lỗi từ Exception
    ];
    echo json_encode($response);

} finally {
    // Đóng kết nối trong khối finally để đảm bảo luôn được thực hiện
    if ($conn) {
        $db->dongKetNoi($conn);
    }
}
?>
