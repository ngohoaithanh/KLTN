<?php
// FILE: api/user/update_profile.php (Đã nâng cấp Avatar + Session)
header('Content-Type: application/json; charset=utf-8');
include_once('../../config/database.php');

// Khởi động session để cập nhật dữ liệu phiên làm việc ngay lập tức
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$db = new clsKetNoi();
$conn = $db->moKetNoi();
$response = [];

if (isset($_POST['user_id'])) {
    $userId = intval($_POST['user_id']);
    
    $fieldsToUpdate = [];
    $params = [];
    $types = "";

    // 1. Cập nhật Họ tên
    if (isset($_POST['full_name']) && !empty(trim($_POST['full_name']))) {
        $fieldsToUpdate[] = "Username = ?";
        $params[] = trim($_POST['full_name']);
        $types .= "s";
    }

    // 2. Cập nhật Email
    if (isset($_POST['email']) && !empty(trim($_POST['email']))) {
        $fieldsToUpdate[] = "Email = ?";
        $params[] = trim($_POST['email']);
        $types .= "s";
    }

    // 3. Cập nhật Avatar (MỚI)
    if (isset($_POST['avatar_url']) && !empty(trim($_POST['avatar_url']))) {
        $fieldsToUpdate[] = "Avatar = ?";
        $params[] = trim($_POST['avatar_url']);
        $types .= "s";
    }

    // 4. Cập nhật Mật khẩu (Giữ nguyên logic cũ)
    if (isset($_POST['password']) && !empty(trim($_POST['password']))) {
        $newPassword = trim($_POST['password']);
        if (!isset($_POST['old_password']) || empty(trim($_POST['old_password']))) {
            echo json_encode(['success' => false, 'error' => 'Vui lòng nhập mật khẩu hiện tại.']);
            exit();
        }
        $oldPassword = trim($_POST['old_password']);

        $stmt_check = $conn->prepare("SELECT Password FROM users WHERE ID = ?");
        $stmt_check->bind_param("i", $userId);
        $stmt_check->execute();
        $result = $stmt_check->get_result()->fetch_assoc();
        
        if (!$result) {
            echo json_encode(['success' => false, 'error' => 'Không tìm thấy người dùng.']);
            exit();
        }
        
        if (md5($oldPassword) != $result['Password']) {
            echo json_encode(['success' => false, 'error' => 'Mật khẩu hiện tại không chính xác.']);
            exit();
        }

        $fieldsToUpdate[] = "Password = ?";
        $params[] = md5($newPassword);
        $types .= "s";
    }

    if (empty($fieldsToUpdate)) {
        echo json_encode(['success' => false, 'error' => 'Không có thông tin nào để cập nhật.']);
        exit();
    }

    $params[] = $userId;
    $types .= "i";

    $sql = "UPDATE users SET " . implode(', ', $fieldsToUpdate) . " WHERE ID = ?";
    
    try {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);

        if ($stmt->execute()) {
            // --- CẬP NHẬT SESSION NGAY LẬP TỨC ---
            if (isset($_POST['full_name'])) $_SESSION['user'] = trim($_POST['full_name']); // Cập nhật tên hiển thị
            if (isset($_POST['email'])) $_SESSION['email'] = trim($_POST['email']);
            if (isset($_POST['avatar_url'])) $_SESSION['avatar'] = trim($_POST['avatar_url']); // Cập nhật ảnh hiển thị

            $response = ['success' => true, 'message' => 'Cập nhật thành công!'];
        } else {
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