<?php
session_start(); // Khởi tạo session
header('Content-Type: application/json; charset=utf-8');

require_once '../../config/database.php'; // Cấu hình kết nối DB
$db = new clsKetNoi();
$conn = $db->moKetNoi();

if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'GET') {
    // Đổi email -> phonenumber (giữ nguyên $_REQUEST để test form-data)
    $phonenumber = isset($_REQUEST['phonenumber']) ? trim($_REQUEST['phonenumber']) : '';
    $password    = isset($_REQUEST['password'])    ? $_REQUEST['password']           : '';

    if ($phonenumber === '' || $password === '') {
        echo json_encode(['success' => false, 'message' => 'Thiếu thông tin đăng nhập']);
        exit;
    }

    // Query theo cột PhoneNumber
    $stmt = $conn->prepare("SELECT ID, Username, Role, PhoneNumber, Password, rating FROM users WHERE PhoneNumber = ? LIMIT 1");
    $stmt->bind_param("s", $phonenumber);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res && $res->num_rows === 1) {
        $user = $res->fetch_assoc();

        // DB đang lưu MD5 → so sánh md5($password)
        if (md5($password) === $user['Password']) {
            // Lưu vào session
            $_SESSION['username'] = $user['Username'];
            $_SESSION['role']     = $user['Role'];
            $_SESSION['user_id']  = $user['ID'];
            $_SESSION['rating']  = $user['rating'];

            unset($user['Password']); // Ẩn mật khẩu khi trả về
            echo json_encode(['success' => true, 'user' => $user]);
            exit;
        }
    }

    echo json_encode(['success' => false, 'message' => 'Sai số điện thoại hoặc mật khẩu']);
} else {
    echo json_encode(['success' => false, 'message' => 'Sai phương thức']);
}

$db->dongKetNoi($conn);