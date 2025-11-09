<?php
header('Content-Type: application/json; charset=utf-8');
include_once('../../config/database.php');

$db = new clsKetNoi();
$conn = $db->moKetNoi();

// Chỉ cho phép POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Phương thức không hợp lệ']);
    $db->dongKetNoi($conn);
    exit;
}

// Dùng $_POST thay vì $_REQUEST
$required_fields = ['id', 'username', 'phone', 'email', 'role'];
foreach ($required_fields as $field) {
    if (!isset($_POST[$field]) || trim($_POST[$field]) === '') {
        echo json_encode(['success' => false, 'error' => "Thiếu trường $field"]);
        $db->dongKetNoi($conn);
        exit;
    }
}

$id       = (int) $_POST['id'];
$username = trim($_POST['username']);
$phone    = trim($_POST['phone']);
$email    = trim($_POST['email']);
$role     = (int) $_POST['role'];
$note     = isset($_POST['note']) ? trim($_POST['note']) : '';

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'error' => 'Email không hợp lệ']);
    $db->dongKetNoi($conn);
    exit;
}

// Kiểm tra email trùng
if (!($stmt = $conn->prepare("SELECT ID FROM users WHERE Email = ? AND ID != ?"))) {
    echo json_encode(['success' => false, 'error' => 'Lỗi prepare: '.$conn->error]);
    $db->dongKetNoi($conn);
    exit;
}
$stmt->bind_param("si", $email, $id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->close();
    $db->dongKetNoi($conn);
    echo json_encode(['success' => false, 'error' => 'Email đã tồn tại!']);
    exit;
}
$stmt->close();

// Cập nhật thông tin user
$sql = "UPDATE users SET 
            Username = ?, 
            Email = ?, 
            PhoneNumber = ?, 
            `Role` = ?, 
            Note = ?
        WHERE ID = ?";

if (!($stmt = $conn->prepare($sql))) {
    echo json_encode(['success' => false, 'error' => 'Lỗi prepare: '.$conn->error]);
    $db->dongKetNoi($conn);
    exit;
}

$stmt->bind_param("sssisi", $username, $email, $phone, $role, $note, $id);
$result = $stmt->execute();

if ($result) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $stmt->error]);
}

$stmt->close();
$db->dongKetNoi($conn);
