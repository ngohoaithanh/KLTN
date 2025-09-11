<?php
header('Content-Type: application/json; charset=utf-8');

include_once('../../config/database.php');
$db = new clsKetNoi();
$conn = $db->moKetNoi();

$response = [];

if (
    isset($_REQUEST['username'], $_REQUEST['phone'], $_REQUEST['email'], $_REQUEST['password']) &&
    !empty($_REQUEST['username']) && !empty($_REQUEST['phone']) && !empty($_REQUEST['email']) &&
    !empty($_REQUEST['password'])
) {
    $username = trim($_REQUEST['username']);
    $phone    = trim($_REQUEST['phone']);
    $email    = trim($_REQUEST['email']);
    $role     = intval($_REQUEST['role']);
    // $role = isset($_REQUEST['role']) && $_REQUEST['role'] !== '' ? intval($_REQUEST['role']) : 'NULL';
    $password = md5(trim($_REQUEST['password'])); // Hash password
    // $warehouse_id     = trim($_REQUEST['warehouse_id']);
    $warehouse_id = isset($_REQUEST['warehouse_id']) && $_REQUEST['warehouse_id'] !== '' ? intval($_REQUEST['warehouse_id']) : 'NULL';

    $note = isset($_REQUEST['note']) ? trim($_REQUEST['note']) : '';

    // Kiểm tra username đã tồn tại chưa
    $check = $conn->query("SELECT * FROM users WHERE Email = '$email'");
    if ($check && $check->num_rows > 0) {
        echo json_encode(['success' => false, 'error' => 'Email đã tồn tại!']);
    } else {
        $sql = "INSERT INTO users (Username, Email, Password, PhoneNumber, Role, Note, warehouse_id) VALUES ('$username', '$email', '$password', '$phone', $role, '$note', $warehouse_id)";
        $result = $conn->query($sql);
        echo json_encode(['success' => $result]);
    }
} else {
    $response = ['success' => false, 'error' => 'Thiếu dữ liệu cần thiết'];
}

// echo json_encode($response);
$db->dongKetNoi($conn);
