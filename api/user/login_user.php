<?php
session_start(); // Khởi tạo session

require_once '../../config/database.php'; // Cấu hình kết nối DB
$db = new clsKetNoi();
$conn = $db->moKetNoi();

if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'GET') {
    $email = $_REQUEST['email'] ?? '';
    $password = $_REQUEST['password'] ?? '';

    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Thiếu thông tin đăng nhập']);
        exit;
    }

    $stmt = $conn->prepare("SELECT * FROM users WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 1) {
        $user = $res->fetch_assoc();
        if ($password === $user['Password']) {
            // Lưu vào session
            $_SESSION['username'] = $user['Username'];
            $_SESSION['role'] = $user['Role'];
            $_SESSION['user_id'] = $user['ID'];
            

            unset($user['Password']); // Ẩn mật khẩu khi trả về
            echo json_encode(['success' => true, 'user' => $user]);
            exit;
        }
    }

    echo json_encode(['success' => false, 'message' => 'Sai email hoặc mật khẩu']);
} else {
    echo json_encode(['success' => false, 'message' => 'Sai phương thức']);
}

$db->dongKetNoi($conn);
