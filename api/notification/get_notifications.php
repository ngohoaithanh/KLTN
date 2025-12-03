<?php
header('Content-Type: application/json; charset=utf-8');
include_once('../../config/database.php');
include_once('../../config/auth_check.php'); // Bảo mật

$db = new clsKetNoi();
$conn = $db->moKetNoi();

// Lấy UserID từ Session (đảm bảo bảo mật) hoặc từ GET
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : (isset($_GET['user_id']) ? intval($_GET['user_id']) : 0);

if ($userId > 0) {
    // 1. Lấy tham số phân trang từ App
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1; // Mặc định trang 1
    $limit = isset($_GET['limit']) ? max(1, intval($_GET['limit'])) : 20; // Mặc định 20 tin/trang
    $offset = ($page - 1) * $limit; // Tính vị trí bắt đầu

    // 2. Truy vấn có LIMIT và OFFSET
    // Cú pháp MySQL: LIMIT offset, limit
    $stmt = $conn->prepare("SELECT * FROM notifications WHERE UserID = ? ORDER BY Created_at DESC LIMIT ?, ?");
    
    // Bind params: UserID (i), Offset (i), Limit (i)
    $stmt->bind_param("iii", $userId, $offset, $limit);
    
    $stmt->execute();
    $result = $stmt->get_result();

    $notifications = [];
    while ($row = $result->fetch_assoc()) {
        $notifications[] = $row;
    }
    
    echo json_encode($notifications);
    $stmt->close();
} else {
    echo json_encode([]);
}

$db->dongKetNoi($conn);
?>