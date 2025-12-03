<?php
// api/cron/cron_manage_debt.php
include_once('../../config/database.php');

$db = new clsKetNoi();
$conn = $db->moKetNoi();

if ($conn) {
    // --- PHẦN 1: CẢNH BÁO (5 NGÀY) ---
    // Logic: Tìm đơn delivered > 5 ngày VÀ < 6 ngày (để chỉ báo 1 lần), chưa nộp tiền
    $sqlWarn = "
        SELECT o.ShipperID, o.ID AS OrderID, o.CODFee
        FROM orders o
        LEFT JOIN transactions t ON o.ID = t.OrderID AND t.Type = 'deposit_cod'
        WHERE o.Status = 'delivered' 
          AND o.CODFee > 0
          AND t.ID IS NULL -- Chưa nộp
          AND o.Accepted_at BETWEEN DATE_SUB(NOW(), INTERVAL 6 DAY) AND DATE_SUB(NOW(), INTERVAL 5 DAY)
    ";
    
    $resWarn = $conn->query($sqlWarn);
    $warnCount = 0;
    if ($resWarn) {
        while ($row = $resWarn->fetch_assoc()) {
            $shipperId = $row['ShipperID'];
            $orderId = $row['OrderID'];
            
            $title = "Cảnh báo: Phí COD sắp quá hạn";
            $msg = "Bạn chưa nộp phí COD cho đơn #$orderId. Vui lòng nộp ngay để tránh bị khóa tài khoản.";
            
            // Insert Notification
            $conn->query("INSERT INTO notifications (UserID, Title, Message, Type, ReferenceID) 
                          VALUES ($shipperId, '$title', '$msg', 'warning', $orderId)");
            $warnCount++;
        }
    }

    // --- PHẦN 2: KHÓA TÀI KHOẢN (7 NGÀY) ---
    // Logic: Tìm đơn delivered > 7 ngày, chưa nộp tiền -> Khóa Shipper đó
    $sqlLock = "
        SELECT DISTINCT o.ShipperID
        FROM orders o
        LEFT JOIN transactions t ON o.ID = t.OrderID AND t.Type = 'deposit_cod'
        WHERE o.Status = 'delivered' 
          AND o.CODFee > 0
          AND t.ID IS NULL -- Chưa nộp
          AND o.Accepted_at < DATE_SUB(NOW(), INTERVAL 7 DAY)
    ";
    
    $resLock = $conn->query($sqlLock);
    $lockCount = 0;
    
    if ($resLock) {
        while ($row = $resLock->fetch_assoc()) {
            $shipperId = $row['ShipperID'];
            
            // 1. Khóa tài khoản (Chuyển account_status thành 'locked')
            $conn->query("UPDATE users SET account_status = 'locked' WHERE ID = $shipperId AND account_status = 'active'");
            
            if ($conn->affected_rows > 0) {
                // 2. Gửi thông báo (để khi họ mở app lên hoặc hỏi admin thì biết)
                $title = "Tài khoản đã bị khóa";
                $msg = "Do nợ phí COD quá hạn 7 ngày, tài khoản của bạn đã bị tạm khóa. Vui lòng liên hệ Admin.";
                $conn->query("INSERT INTO notifications (UserID, Title, Message, Type, ReferenceID) 
                              VALUES ($shipperId, '$title', '$msg', 'system', 0)");
                $lockCount++;
            }
        }
    }

    echo "Đã gửi cảnh báo cho $warnCount shipper. Đã khóa $lockCount shipper nợ quá hạn.";
}

$db->dongKetNoi($conn);
?>