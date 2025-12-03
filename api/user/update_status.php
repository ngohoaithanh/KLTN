<?php
// FILE: api/user/update_status.php
// PHIÊN BẢN NÂNG CẤP: Chấp nhận dữ liệu từ Form (x-www-form-urlencoded)

header('Content-Type: application/json; charset=utf-8');
include_once("../../controllers/cNotification.php");
include_once('../../config/database.php');
$db = new clsKetNoi();
$conn = $db->moKetNoi();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Phương thức request không hợp lệ.']);
    exit;
}

// *** THAY ĐỔI QUAN TRỌNG: Đọc dữ liệu từ $_POST ***
if (!isset($_POST['id']) || !isset($_POST['new_status'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Thiếu ID hoặc trạng thái mới.']);
    exit;
}

$id = intval($_POST['id']);
$new_status = $_POST['new_status']; // 'active', 'locked'

// Kiểm tra giá trị new_status hợp lệ
if (!in_array($new_status, ['active', 'locked', 'pending'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Trạng thái mới không hợp lệ.']);
    exit;
}

try {
    // Sử dụng Prepared Statements để bảo mật
    $stmt = $conn->prepare("UPDATE users SET account_status = ? WHERE ID = ?");
    $stmt->bind_param("si", $new_status, $id);

    if ($stmt->execute()) {
        $title = ($new_status == 'active') ? "Tài khoản đã được kích hoạt" : "Tài khoản đã bị khóa";
        $msg   = ($new_status == 'active') 
                ? "Chúc mừng! Tài khoản của bạn đã được kích hoạt. Bạn có thể bắt đầu sử dụng dịch vụ." 
                : "Tài khoản của bạn đã bị khóa bởi quản trị viên. Vui lòng liên hệ để biết thêm chi tiết.";

        controlNotification::add($id, $title, $msg, "system");
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Cập nhật trạng thái thành công']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Lỗi CSDL: ' . $stmt->error]);
    }
    $stmt->close();

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Lỗi máy chủ: ' . $e->getMessage()]);
}

$db->dongKetNoi($conn);
?>