<?php
// FILE: api/notification/send.php
header('Content-Type: application/json; charset=utf-8');
include_once("../../config/database.php");
include_once("../../controllers/cNotification.php"); // Sử dụng lại controller đã làm

$db = new clsKetNoi();
$conn = $db->moKetNoi();

$data = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Sai phương thức']);
    exit;
}

$title = isset($data['title']) ? trim($data['title']) : '';
$message = isset($data['message']) ? trim($data['message']) : '';
$target_type = isset($data['target_type']) ? $data['target_type'] : ''; // 'individual', 'all_shippers', 'all_customers'
$user_id = isset($data['user_id']) ? intval($data['user_id']) : 0;

if (empty($title) || empty($message) || empty($target_type)) {
    echo json_encode(['success' => false, 'error' => 'Vui lòng nhập đủ tiêu đề, nội dung và đối tượng gửi.']);
    exit;
}

try {
    $count = 0;

    if ($target_type == 'individual') {
        // Gửi cho 1 người
        if ($user_id <= 0) throw new Exception("Chưa nhập ID người nhận.");
        controlNotification::add($user_id, $title, $message, 'system');
        $count = 1;

    } elseif ($target_type == 'all_shippers') {
        // Gửi cho TẤT CẢ Shipper (Role = 6)
        $result = $conn->query("SELECT ID FROM users WHERE Role = 6");
        while ($row = $result->fetch_assoc()) {
            controlNotification::add($row['ID'], $title, $message, 'system');
            $count++;
        }

    } elseif ($target_type == 'all_customers') {
        // Gửi cho TẤT CẢ Khách hàng (Role = 7)
        $result = $conn->query("SELECT ID FROM users WHERE Role = 7");
        while ($row = $result->fetch_assoc()) {
            controlNotification::add($row['ID'], $title, $message, 'system');
            $count++;
        }
    }

    echo json_encode(['success' => true, 'message' => "Đã gửi thông báo thành công cho $count người dùng."]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

$db->dongKetNoi($conn);
?>