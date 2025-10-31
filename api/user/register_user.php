<?php
header('Content-Type: application/json; charset=utf-8');
include_once('../../config/database.php'); // Sửa lại đường dẫn nếu cần

$db = new clsKetNoi();
$conn = $db->moKetNoi();
$response = [];

// Lấy dữ liệu POST
if (isset($_POST['phone_number']) && isset($_POST['full_name']) && isset($_POST['password'])) {
    $phone = trim($_POST['phone_number']);
    $fullName = trim($_POST['full_name']);
    $password = trim($_POST['password']); // Nhận mật khẩu thô

    // 1. Mã hóa mật khẩu (Rất quan trọng)
    // Lưu ý: database của bạn đang dùng MD5, nhưng bạn NÊN dùng BCRYPT.
    // Dưới đây là code cho cả hai:

    // Cách 1: Dùng MD5 (Giống code cũ của bạn)
    $hashedPassword = md5($password);

    // Cách 2: Dùng BCRYPT (Khuyên dùng - an toàn hơn)
    // $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // 2. Tạo email giả (nếu cần)
    $fakeEmail = 'guest_' . time() . '@fake.local';
    $role = 7; // Role 7 là Khách hàng

    try {
        $stmt = $conn->prepare("INSERT INTO users (Username, Email, Password, PhoneNumber, Role) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $fullName, $fakeEmail, $hashedPassword, $phone, $role);

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = "Đăng ký thành công!";
        } else {
            // Xử lý lỗi trùng lặp SĐT (nếu bạn có UNIQUE KEY trên PhoneNumber)
            if ($conn->errno == 1062) {
                 $response = ['success' => false, 'error' => 'Số điện thoại này đã tồn tại.'];
            } else {
                 $response = ['success' => false, 'error' => 'Lỗi khi tạo tài khoản: ' . $stmt->error];
            }
        }
        $stmt->close();
    } catch (Exception $e) {
        $response = ['success' => false, 'error' => $e->getMessage()];
    }
} else {
    $response = ['success' => false, 'error' => 'Thiếu thông tin bắt buộc.'];
}

$db->dongKetNoi($conn);
echo json_encode($response);
?>