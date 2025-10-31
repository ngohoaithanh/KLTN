<?php
header('Content-Type: application/json; charset=utf-8');
include_once('../../config/database.php'); // Sửa lại đường dẫn nếu cần

$db = new clsKetNoi();
$conn = $db->moKetNoi();
$response = [];

// Lấy dữ liệu POST
if (isset($_POST['user_id'])) {
    $userId = intval($_POST['user_id']);
    
    $fieldsToUpdate = [];
    $params = [];
    $types = "";

    // 1. Kiểm tra Họ tên
    if (isset($_POST['full_name']) && !empty(trim($_POST['full_name']))) {
        $fieldsToUpdate[] = "Username = ?";
        $params[] = trim($_POST['full_name']);
        $types .= "s";
    }

    // 2. Kiểm tra Email
    if (isset($_POST['email']) && !empty(trim($_POST['email']))) {
        $fieldsToUpdate[] = "Email = ?";
        $params[] = trim($_POST['email']);
        $types .= "s";
    }

    // 3. Kiểm tra Mật khẩu
    if (isset($_POST['password']) && !empty(trim($_POST['password']))) {
        
        // 1. Lấy mật khẩu cũ và mới từ POST
        $newPassword = trim($_POST['password']);
        if (!isset($_POST['old_password']) || empty(trim($_POST['old_password']))) {
            echo json_encode(['success' => false, 'error' => 'Vui lòng nhập mật khẩu hiện tại.']);
            exit();
        }
        $oldPassword = trim($_POST['old_password']);

        // 2. Lấy mật khẩu đã mã hóa trong DB
        $stmt_check = $conn->prepare("SELECT Password FROM users WHERE ID = ?");
        $stmt_check->bind_param("i", $userId);
        $stmt_check->execute();
        $result = $stmt_check->get_result()->fetch_assoc();
        
        if (!$result) {
            echo json_encode(['success' => false, 'error' => 'Không tìm thấy người dùng.']);
            exit();
        }
        $hashedPasswordFromDb = $result['Password'];
        
        // 3. Xác thực mật khẩu cũ
        // Giả sử bạn đang dùng MD5 (như code đăng ký)
        if (md5($oldPassword) != $hashedPasswordFromDb) {
            // (Nếu dùng BCRYPT, bạn phải dùng: password_verify($oldPassword, $hashedPasswordFromDb))
            echo json_encode(['success' => false, 'error' => 'Mật khẩu hiện tại không chính xác.']);
            exit();
        }

        // 4. Nếu mật khẩu cũ đúng, mã hóa và thêm vào danh sách cập nhật
        $hashedNewPassword = md5($newPassword);
        $fieldsToUpdate[] = "Password = ?";
        $params[] = $hashedNewPassword;
        $types .= "s";
    }

    // Nếu không có gì để cập nhật, báo lỗi
    if (empty($fieldsToUpdate)) {
        echo json_encode(['success' => false, 'error' => 'Không có thông tin nào để cập nhật.']);
        exit();
    }

    // Thêm user_id vào cuối mảng params
    $params[] = $userId;
    $types .= "i";

    // Xây dựng câu lệnh SQL động
    $sql = "UPDATE users SET " . implode(', ', $fieldsToUpdate) . " WHERE ID = ?";
    
    try {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params); // Dùng ... để "unpack" mảng params

        if ($stmt->execute()) {
            $response = ['success' => true, 'message' => 'Cập nhật thành công!'];
        } else {
            // Xử lý lỗi trùng email (do UNIQUE KEY)
            if ($conn->errno == 1062) {
                 $response = ['success' => false, 'error' => 'Email này đã được sử dụng.'];
            } else {
                 $response = ['success' => false, 'error' => 'Lỗi khi cập nhật: ' . $stmt->error];
            }
        }
        $stmt->close();
    } catch (Exception $e) {
        $response = ['success' => false, 'error' => $e->getMessage()];
    }
} else {
    $response = ['success' => false, 'error' => 'Thiếu User ID.'];
}

$db->dongKetNoi($conn);
echo json_encode($response);
?>