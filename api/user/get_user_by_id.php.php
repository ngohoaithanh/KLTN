<?php
header('Content-Type: application/json; charset=utf-8');
include_once('../../config/database.php');

$db = new clsKetNoi();
$conn = $db->moKetNoi();

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);
    $query = "SELECT * FROM users WHERE ID = $id";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        echo json_encode($user);
    } else {
        echo json_encode(['success' => false, 'error' => 'Không tìm thấy người dùng']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Thiếu ID hợp lệ']);
}

$db->dongKetNoi($conn);
?>
