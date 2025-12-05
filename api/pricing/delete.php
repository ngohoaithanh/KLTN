<?php
// FILE: api/pricing/delete.php (Phiên bản An toàn: Chặn xóa khi đang Active)
header('Content-Type: application/json; charset=utf-8');

// 1. Khởi động Session để lấy ID Admin
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$current_user_id = $_SESSION['user_id'] ?? 0;

include_once("../../config/database.php");
include_once("../../controllers/cLog.php");

$db = new clsKetNoi(); 
$conn = $db->moKetNoi();

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['id'])) {
    $id = intval($data['id']);

    // 2. Kiểm tra thông tin bảng giá trước khi xóa
    $check_query = $conn->query("SELECT Name, IsActive FROM pricing_rules WHERE ID = $id");
    
    if ($check_query && $check_query->num_rows > 0) {
        $rule = $check_query->fetch_assoc();
        
        // === CHỐT CHẶN QUAN TRỌNG ===
        if ($rule['IsActive'] == 1) {
            echo json_encode([
                'success' => false, 
                'error' => 'Vui lòng bỏ kích hoạt bảng giá này trước khi xóa'
            ]);
            $db->dongKetNoi($conn);
            exit; // Dừng ngay lập tức
        }
        // ==============================
        
        $rule_name = $rule['Name'];

        // 3. Thực hiện Xóa mềm (Soft Delete)
        // Lúc này đã chắc chắn là IsActive = 0 rồi
        $stmt = $conn->prepare("UPDATE pricing_rules SET IsDeleted = 1 WHERE ID = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            
            // 4. Ghi Log Hệ thống
            if ($current_user_id > 0) {
                $log_desc = "Đã xóa bảng giá: $rule_name";
                controlLog::record(
                    $current_user_id,   // Người xóa
                    'DELETE_PRICING',   // Hành động
                    'pricing_rules',    // Bảng bị tác động
                    $id,                // ID dòng bị xóa
                    $log_desc           // Chi tiết
                );
            }
            
            echo json_encode(['success' => true, 'message' => 'Đã xóa bảng giá thành công.']);
        } else {
            echo json_encode(['success' => false, 'error' => $stmt->error]);
        }
        $stmt->close();

    } else {
        echo json_encode(['success' => false, 'error' => 'Bảng giá không tồn tại.']);
    }

} else {
    echo json_encode(['success' => false, 'error' => 'Thiếu ID bảng giá.']);
}

$db->dongKetNoi($conn);
?>