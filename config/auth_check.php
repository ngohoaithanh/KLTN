<?php
// File này sẽ được include ở đầu mỗi API cần bảo vệ
ini_set('session.cookie_path', '/KLTN/api/');
// Bắt đầu session nếu chưa có
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 1. Kiểm tra xem có user_id trong session không
if (!isset($_SESSION['user_id'])) {
    http_response_code(401); // 401 Unauthorized
    echo json_encode(['success' => false, 'error' => 'Chưa đăng nhập.', 'error_code' => 'NOT_LOGGED_IN']);
    exit();
}

// 2. Kiểm tra trạng thái tài khoản TRỰC TIẾP từ database
$db_auth = new clsKetNoi();
$conn_auth = $db_auth->moKetNoi();
$userId = $_SESSION['user_id'];

$stmt_auth = $conn_auth->prepare("SELECT account_status FROM users WHERE ID = ? AND hidden = 1 LIMIT 1");
$stmt_auth->bind_param("i", $userId);
$stmt_auth->execute();
$result_auth = $stmt_auth->get_result()->fetch_assoc();
$stmt_auth->close();
$db_auth->dongKetNoi($conn_auth);

if (!$result_auth || $result_auth['account_status'] != 'active') {
    // Nếu tài khoản không tồn tại, bị ẩn, hoặc bị khóa
    http_response_code(403); // 403 Forbidden
    echo json_encode([
        'success' => false, 
        'error' => 'Tài khoản của bạn đã bị khóa hoặc không tồn tại.',
        'error_code' => 'ACCOUNT_LOCKED' // Mã lỗi quan trọng
    ]);
    exit();
}

// Nếu mọi thứ OK, script PHP sẽ tiếp tục chạy
?>