<?php
// FILE: controllers/cLog.php
include_once(__DIR__ . '/../config/database.php');

class controlLog {
    
    // Hàm tĩnh (Static) để gọi nhanh: controlLog::record(...)
    public static function record($userId, $action, $targetTable = null, $targetId = null, $description = '') {
        // $userId = ($userId > 0) ? $userId : 1;
        $db = new clsKetNoi();
        $conn = $db->moKetNoi();

        try {
            // Lấy IP và thông tin trình duyệt
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'UNKNOWN';

            $stmt = $conn->prepare("
                INSERT INTO system_logs (UserID, Action, TargetTable, TargetID, Description, IPAddress, UserAgent, Created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            
            $stmt->bind_param("ississs", $userId, $action, $targetTable, $targetId, $description, $ip, $userAgent);
            $stmt->execute();
            $stmt->close();
            
        } catch (Exception $e) {
            // Log thất bại thì ghi vào error_log của server, không làm sập web
            error_log("System Log Error: " . $e->getMessage());
        } finally {
            $db->dongKetNoi($conn);
        }
    }
}
?>