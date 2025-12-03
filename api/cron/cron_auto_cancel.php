<?php
// api/cron/cron_auto_cancel.php
header('Content-Type: text/html; charset=utf-8'); // Để hiển thị kết quả trên trình duyệt/log
include_once('../../config/database.php');

$db = new clsKetNoi();
$conn = $db->moKetNoi();

if ($conn) {
    // Thời gian giới hạn: 30 phút
    $minutes = 30;
    
    // 1. Tìm các đơn hàng cần hủy
    $sqlSelect = "SELECT ID, CustomerID FROM orders 
                  WHERE Status = 'pending' 
                  AND Created_at < DATE_SUB(NOW(), INTERVAL ? MINUTE)";
                  
    $stmt = $conn->prepare($sqlSelect);
    $stmt->bind_param("i", $minutes);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $count = 0;
    
    // 2. Duyệt qua từng đơn để Hủy và Báo khách
    while ($row = $result->fetch_assoc()) {
        $orderId = $row['ID'];
        $custId = $row['CustomerID'];
        
        // A. Cập nhật trạng thái -> Cancelled
        $sqlUpdate = "UPDATE orders SET Status = 'cancelled' WHERE ID = $orderId";
        $conn->query($sqlUpdate);
        
        // B. Ghi lịch sử Tracking
        $msgTrack = "Hệ thống tự động hủy đơn do không tìm thấy tài xế sau 30 phút.";
        $sqlTrack = "INSERT INTO trackings (OrderID, Status) VALUES ($orderId, '$msgTrack')";
        $conn->query($sqlTrack);
        
        // C. Gửi Thông báo cho Khách hàng
        $title = "Đơn hàng đã bị hủy";
        $msgNoti = "Rất tiếc, không tìm thấy tài xế nào quanh khu vực của bạn. Đơn hàng #$orderId đã tự động hủy.";
        // Dùng query trực tiếp cho nhanh vì đây là script chạy ngầm
        $sqlNoti = "INSERT INTO notifications (UserID, Title, Message, Type, ReferenceID) 
                    VALUES ($custId, '$title', '$msgNoti', 'system', $orderId)";
        $conn->query($sqlNoti);
        
        $count++;
    }
    
    echo "Đã quét và hủy $count đơn hàng treo quá 30 phút.";
    $stmt->close();
}

$db->dongKetNoi($conn);
?>