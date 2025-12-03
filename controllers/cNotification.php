<?php
// FILE: controllers/cNotification.php
include_once(__DIR__ . '/../config/database.php'); // Đường dẫn tương đối an toàn

class controlNotification {
    
    // Hàm tĩnh (Static) để gọi nhanh từ bất cứ đâu mà không cần new class
    public static function add($userId, $title, $message, $type = 'system', $referenceId = null) {
        $db = new clsKetNoi();
        $conn = $db->moKetNoi();

        try {
            $stmt = $conn->prepare("INSERT INTO notifications (UserID, Title, Message, Type, ReferenceID, Created_at) VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param("isssi", $userId, $title, $message, $type, $referenceId);
            $stmt->execute();
            $stmt->close();
            // Sau này: Có thể thêm code gửi FCM (Firebase) ở đây để rung điện thoại
            return true;
        } catch (Exception $e) {
            // Ghi log lỗi nếu cần, nhưng không làm sập trang web
            error_log("Lỗi gửi thông báo: " . $e->getMessage());
            return false;
        } finally {
            $db->dongKetNoi($conn);
        }
    }

    // Hàm lấy thông báo cho một user
    public function getNotificationsByUser($userId, $limit = 20) {
        $db = new clsKetNoi();
        $conn = $db->moKetNoi();
        
        $sql = "SELECT * FROM notifications WHERE UserID = ? ORDER BY Created_at DESC LIMIT ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $userId, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        $stmt->close();
        $db->dongKetNoi($conn);
        return $data;
    }
}
?>