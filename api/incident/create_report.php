<?php
header('Content-Type: application/json; charset=utf-8');
include_once('../../config/database.php');
include_once('../../config/auth_check.php'); // Bắt buộc đăng nhập

$db = new clsKetNoi();
$conn = $db->moKetNoi();
$response = [];

$reporterId = $_SESSION['user_id']; // Lấy ID người báo cáo từ session

if (isset($_POST['order_id']) && isset($_POST['type']) && isset($_POST['description'])) {
    $orderId = intval($_POST['order_id']);
    $type = trim($_POST['type']);
    $description = trim($_POST['description']);
    $proofImage = isset($_POST['proof_image']) ? trim($_POST['proof_image']) : null;

    if ($conn) {
        $stmt = $conn->prepare("INSERT INTO incident_reports (OrderID, ReporterID, Type, Description, ProofImage) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iisss", $orderId, $reporterId, $type, $description, $proofImage);
        
        if ($stmt->execute()) {
            $incident_id = $conn->insert_id;
            // === BƯỚC QUAN TRỌNG: GỬI THÔNG BÁO CHO ADMIN ===
            include_once("../../controllers/cNotification.php");
            
            // 1. Lấy danh sách ID của tất cả Admin (Role = 1) và Quản lý (Role = 2)
            // Để đảm bảo ai quản lý cũng nhận được tin
            $sql_admins = "SELECT ID FROM users WHERE Role IN (1, 2)";
            $result_admins = $conn->query($sql_admins);

            $notif_title = "Sự cố mới từ Shipper";
            $notif_message = "Shipper vừa báo cáo sự cố cho đơn hàng #$orderId. Vui lòng kiểm tra.";

            // 2. Lặp qua từng Admin và gửi thông báo cho họ
            while ($row = $result_admins->fetch_assoc()) {
                $admin_id = $row['ID'];
                // Gửi vào "hòm thư" của ông Admin này
                controlNotification::add($admin_id, $notif_title, $notif_message, 'system', $incident_id);
            }
            // =================================================
            $response['success'] = true;
            $response['message'] = "Báo cáo đã được gửi thành công. Chúng tôi sẽ xem xét sớm nhất.";
        } else {
            $response['success'] = false;
            $response['error'] = "Lỗi: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $response['success'] = false;
        $response['error'] = "Lỗi kết nối database.";
    }
} else {
    $response['success'] = false;
    $response['error'] = "Thiếu thông tin báo cáo.";
}

$db->dongKetNoi($conn);
echo json_encode($response);
?>