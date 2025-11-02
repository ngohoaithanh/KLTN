<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once '../../config/database.php';
$db = new clsKetNoi();
$conn = $db->moKetNoi();

if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'GET') {
    $phonenumber = isset($_REQUEST['phonenumber']) ? trim($_REQUEST['phonenumber']) : '';
    $password    = isset($_REQUEST['password'])    ? $_REQUEST['password']          : '';

    if ($phonenumber === '' || $password === '') {
        echo json_encode(['success' => false, 'message' => 'Thiếu thông tin đăng nhập']);
        exit;
    }

    // BƯỚC 1: BỎ kiểm tra `account_status` ở đây, nhưng LẤY nó ra
    $stmt = $conn->prepare("
        SELECT ID, Username, Role, PhoneNumber, Password, rating, account_status 
        FROM users 
        WHERE PhoneNumber = ? AND hidden = 1 
        LIMIT 1
    ");
    $stmt->bind_param("s", $phonenumber);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res && $res->num_rows === 1) {
        $user = $res->fetch_assoc();

        // BƯỚC 2: Kiểm tra mật khẩu
        if (md5($password) === $user['Password']) {
            
            // BƯỚC 3: KIỂM TRA TRẠNG THÁI TÀI KHOẢN
            if ($user['account_status'] === 'active') {
                // TRƯỜNG HỢP 1: HOẠT ĐỘNG -> Đăng nhập thành công
                $_SESSION['username'] = $user['Username'];
                $_SESSION['role']     = $user['Role'];
                $_SESSION['user_id']  = $user['ID'];
                $_SESSION['rating']   = $user['rating'];

                unset($user['Password']);
                echo json_encode(['success' => true, 'user' => $user]);
                exit;
            } else {
                // TRƯỜNG HỢP 2: BỊ KHÓA -> Trả về mã lỗi đặc biệt
                echo json_encode([
                    'success' => false, 
                    'message' => 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ hỗ trợ.',
                    'error_code' => 'ACCOUNT_LOCKED' // <-- Mã lỗi quan trọng
                ]);
                exit;
            }
        }
    }

    // TRƯỜNG HỢP 3: Sai SĐT hoặc Sai mật khẩu
    echo json_encode(['success' => false, 'message' => 'Sai số điện thoại hoặc mật khẩu']);
} else {
    echo json_encode(['success' => false, 'message' => 'Sai phương thức']);
}

$db->dongKetNoi($conn);
?>