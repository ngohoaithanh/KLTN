<?php
header('Content-Type: application/json; charset=utf-8');
include_once('../../config/database.php');

$db = new clsKetNoi();
$conn = $db->moKetNoi();

// Kiểm tra phương thức request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Phương thức không hợp lệ']);
    exit;
}

// Kiểm tra và xử lý dữ liệu đầu vào
$required_fields = ['id', 'username', 'phone', 'email', 'role'];
foreach ($required_fields as $field) {
    if (!isset($_REQUEST[$field]) || empty(trim($_REQUEST[$field]))) {
        echo json_encode(['success' => false, 'error' => "Thiếu trường $field"]);
        exit;
    }
}

$id = intval($_REQUEST['id']);
$username = trim($_REQUEST['username']);
$phone = trim($_REQUEST['phone']);
$email = trim($_REQUEST['email']);
$role = intval($_REQUEST['role']);
$note = isset($_REQUEST['note']) ? trim($_REQUEST['note']) : '';
$warehouse_id = isset($_REQUEST['warehouse_id']) && !empty($_REQUEST['warehouse_id']) ? intval($_REQUEST['warehouse_id']) : null;

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'error' => 'Email không hợp lệ']);
    exit;
}

// Kiểm tra email trùng (sử dụng prepared statement để tránh SQL injection)
$stmt = $conn->prepare("SELECT ID FROM users WHERE Email = ? AND ID != ?");
$stmt->bind_param("si", $email, $id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(['success' => false, 'error' => 'Email đã tồn tại!']);
    exit;
}

// Cập nhật thông tin user (sử dụng prepared statement)
$sql = "UPDATE users SET 
        Username = ?, 
        Email = ?, 
        PhoneNumber = ?, 
        Role = ?, 
        Note = ?, 
        warehouse_id = ?
        WHERE ID = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssissi", $username, $email, $phone, $role, $note, $warehouse_id, $id);
$result = $stmt->execute();

if ($result) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $conn->error]);
}

$stmt->close();
$db->dongKetNoi($conn);
?>