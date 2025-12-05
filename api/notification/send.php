<?php
// FILE: api/notification/send.php
header('Content-Type: application/json; charset=utf-8');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once("../../config/database.php");
// include_once("../../config/auth_check.php");
include_once("../../controllers/cNotification.php");
include_once("../../controllers/cLog.php");
// $current_role     = $_SESSION['role'] ?? 0;
// if ($current_role != 1 && $current_role != 2) {
//     echo json_encode(['success' => false, 'error' => 'Bạn không có quyền gửi thông báo.']);
//     exit;
// }

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
    $log_description = "";
    if ($target_type == 'individual') {
        // Gửi cho 1 người
        if ($user_id <= 0) throw new Exception("Chưa nhập ID người nhận.");
        controlNotification::add($user_id, $title, $message, 'system');
        $count = 1;
        $log_description = "Gửi thông báo cá nhân tới User #$user_id. Tiêu đề: '$title'";
    } elseif ($target_type == 'all_shippers') {
        // Gửi cho TẤT CẢ Shipper (Role = 6)
        $result = $conn->query("SELECT ID FROM users WHERE Role = 6");
        while ($row = $result->fetch_assoc()) {
            controlNotification::add($row['ID'], $title, $message, 'system');
            $count++;
        }
        $log_description = "Gửi thông báo hàng loạt tới TOÀN BỘ SHIPPER ($count người). Tiêu đề: '$title'";
    } elseif ($target_type == 'all_customers') {
        // Gửi cho TẤT CẢ Khách hàng (Role = 7)
        $result = $conn->query("SELECT ID FROM users WHERE Role = 7");
        while ($row = $result->fetch_assoc()) {
            controlNotification::add($row['ID'], $title, $message, 'system');
            $count++;
        }
        $log_description = "Gửi thông báo hàng loạt tới TOÀN BỘ KHÁCH HÀNG ($count người). Tiêu đề: '$title'";
    } else {
        throw new Exception("Loại đối tượng nhận không hợp lệ.");
    }
    
    $current_admin_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
    if ($current_admin_id > 0) {
        
        $log_target_table = 'notifications';
        $log_target_id = null;

        // Nếu gửi CÁ NHÂN
        if ($target_type == 'individual') {
            $log_target_table = 'users';
            $log_target_id = $user_id;
        }

        controlLog::record(
            $current_admin_id, 
            'SEND_NOTIFICATION', 
            $log_target_table, 
            $log_target_id, 
            $log_description
        );
    }

    echo json_encode(['success' => true, 'message' => "Đã gửi thông báo thành công cho $count người dùng."]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

$db->dongKetNoi($conn);
?>