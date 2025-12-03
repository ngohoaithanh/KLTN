<?php
header('Content-Type: application/json; charset=utf-8');
include_once('../../config/database.php');
include_once('../../config/auth_check.php');

$db = new clsKetNoi();
$conn = $db->moKetNoi();
$response = ['success' => false];

$userId = $_SESSION['user_id'];

if ($conn) {
    // Đánh dấu tất cả thông báo của user này là Đã đọc (IsRead = 1)
    $stmt = $conn->prepare("UPDATE notifications SET IsRead = 1 WHERE UserID = ? AND IsRead = 0");
    $stmt->bind_param("i", $userId);
    
    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = "Đã đánh dấu đã đọc.";
    }
    $stmt->close();
}

$db->dongKetNoi($conn);
echo json_encode($response);
?>