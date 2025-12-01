<?php
// File này dùng để chạy tự động (Cron Job)
include_once('../../config/database.php');

$db = new clsKetNoi();
$conn = $db->moKetNoi();

if ($conn) {
    // Thời gian giới hạn: 5 phút (300 giây)
    // Nếu quá 5 phút không gửi vị trí -> coi như Offline
    $timeLimit = 2; 

    // Câu lệnh SQL: Update trạng thái thành 'offline' cho những ai "mất tích" quá lâu
    $sql = "UPDATE shipper_locations 
            SET status = 'offline' 
            WHERE status = 'online' 
            AND updated_at < DATE_SUB(NOW(), INTERVAL ? MINUTE)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $timeLimit);
    
    if ($stmt->execute()) {
        $affected = $stmt->affected_rows;
        echo "Đã chạy quét tự động. Số shipper bị set Offline: " . $affected;
    } else {
        echo "Lỗi SQL: " . $stmt->error;
    }
    
    $stmt->close();
    $db->dongKetNoi($conn);
}
?>