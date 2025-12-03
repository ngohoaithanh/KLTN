<?php
header('Content-Type: application/json; charset=utf-8');
include_once('../../config/database.php');
include_once('../../config/auth_check.php'); // Bắt buộc đăng nhập

$db = new clsKetNoi();
$conn = $db->moKetNoi();
$response = ['count' => 0];

$userId = $_SESSION['user_id'];

if ($conn) {
    // Đếm số dòng có IsRead = 0
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM notifications WHERE UserID = ? AND IsRead = 0");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    
    $response['count'] = intval($result['total']);
    $stmt->close();
}

$db->dongKetNoi($conn);
echo json_encode($response);
?>