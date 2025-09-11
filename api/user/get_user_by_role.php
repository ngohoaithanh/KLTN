<?php
header('Content-Type: application/json; charset=utf-8');
include_once('../../config/database.php');

$db = new clsKetNoi();
$conn = $db->moKetNoi();

if (isset($_GET['role'])) {
    $role = intval($_GET['role']);
    $response = [];

    $sql = "SELECT ID, Username, Email, PhoneNumber, Role, Note FROM users WHERE Role = $role";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $response[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $response]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy người dùng nào với vai trò này.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Thiếu tham số role.']);
}

$db->dongKetNoi($conn);
?>
