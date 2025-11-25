<?php
header('Content-Type: application/json; charset=utf-8');

include_once('../../config/database.php');
$db = new clsKetNoi();
$conn = $db->moKetNoi();

// Kiểm tra phương thức request phải là POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['success' => false, 'error' => 'Phương thức request không hợp lệ.']);
    exit;
}

// Kiểm tra các trường dữ liệu bắt buộc
$required_fields = ['username', 'phone', 'email', 'password', 'role'];
foreach ($required_fields as $field) {
    if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
        http_response_code(400); // Bad Request
        echo json_encode(['success' => false, 'error' => "Thiếu dữ liệu bắt buộc: {$field}"]);
        $db->dongKetNoi($conn);
        exit;
    }
}

// Xử lý dữ liệu đầu vào
$username = trim($_POST['username']);
$phone    = trim($_POST['phone']);
$email    = trim($_POST['email']);
$role     = intval($_POST['role']);
$password = trim($_POST['password']);
$note     = isset($_POST['note']) ? trim($_POST['note']) : '';
$avatar_url = isset($_POST['avatar_url']) ? trim($_POST['avatar_url']) : null;
$hashed_password = md5($password);

try {
    // 1. Kiểm tra email đã tồn tại chưa
    $stmt = $conn->prepare("SELECT ID FROM users WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        http_response_code(409); // Conflict
        echo json_encode(['success' => false, 'error' => 'Email đã tồn tại!']);
        $stmt->close();
        $db->dongKetNoi($conn);
        exit;
    }
    $stmt->close();

    // ===============================================
    // === PHẦN MỚI: KIỂM TRA SỐ ĐIỆN THOẠI TỒN TẠI ===
    // ===============================================
    $stmt_phone = $conn->prepare("SELECT ID FROM users WHERE PhoneNumber = ?");
    $stmt_phone->bind_param("s", $phone);
    $stmt_phone->execute();
    $stmt_phone->store_result();

    if ($stmt_phone->num_rows > 0) {
        http_response_code(409); // Conflict
        echo json_encode(['success' => false, 'error' => 'Số điện thoại đã tồn tại!']);
        $stmt_phone->close();
        $db->dongKetNoi($conn);
        exit;
    }
    $stmt_phone->close();
    
    // 2. Nếu tất cả đều hợp lệ, thêm người dùng mới
    $stmt_insert = $conn->prepare(
        "INSERT INTO users (Username, Email, Password, PhoneNumber, Avatar, Role, Note, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())"
    );
    $stmt_insert->bind_param("sssssis", $username, $email, $hashed_password, $phone, $avatar_url, $role, $note);

    if ($stmt_insert->execute()) {
        $new_user_id = $stmt_insert->insert_id;
        http_response_code(201); // Created
        echo json_encode([
            'success' => true,
            'message' => 'Thêm người dùng thành công',
            'new_user_id' => $new_user_id
        ]);
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(['success' => false, 'error' => 'Lỗi khi thêm vào CSDL: ' . $stmt_insert->error]);
    }
    $stmt_insert->close();

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Lỗi máy chủ: ' . $e->getMessage()]);
}

$db->dongKetNoi($conn);
?>