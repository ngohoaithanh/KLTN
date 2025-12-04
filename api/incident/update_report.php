<?php
// FILE: api/incident/update_report.php
header('Content-Type: application/json; charset=utf-8');
include_once("../../config/database.php");
include_once("../../controllers/cNotification.php");
include_once("../../controllers/cLog.php"); // Include Helper Log

$db = new clsKetNoi();
$conn = $db->moKetNoi();

$data = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Sai phương thức']);
    exit;
}

// Nhận dữ liệu từ Admin
$id = isset($data['id']) ? intval($data['id']) : 0;
$status = isset($data['status']) ? trim($data['status']) : ''; // 'resolved', 'rejected'
$resolution = isset($data['resolution']) ? trim($data['resolution']) : '';
$admin_id = isset($data['admin_id']) ? intval($data['admin_id']) : 0; // Lấy từ Session JS gửi lên

if ($id <= 0 || empty($status) || empty($resolution)) {
    echo json_encode(['success' => false, 'error' => 'Vui lòng nhập đầy đủ thông tin xử lý.']);
    exit;
}

try {
    // 1. Cập nhật bảng incident_reports
    $stmt = $conn->prepare("UPDATE incident_reports SET Status = ?, Resolution = ? WHERE ID = ?");
    $stmt->bind_param("ssi", $status, $resolution, $id);
    
    if ($stmt->execute()) {
        
        // 2. Lấy thông tin người báo cáo để gửi thông báo
        $info = $conn->query("SELECT ReporterID, OrderID FROM incident_reports WHERE ID = $id")->fetch_assoc();
        $reporter_id = $info['ReporterID'];
        $order_id = $info['OrderID'];

        // 3. Gửi thông báo (Notification)
        $title = ($status == 'resolved') ? "Báo cáo sự cố đã được xử lý" : "Báo cáo sự cố bị từ chối";
        $msg = "Về đơn hàng #$order_id: $resolution";
        controlNotification::add($reporter_id, $title, $msg, 'system', $order_id);

        // 4. Ghi Log hệ thống (System Log)
        $log_action = "UPDATE_INCIDENT";
        $log_desc = "Đã xử lý báo cáo #$id. Trạng thái: $status. Nội dung: $resolution";
        controlLog::record($admin_id, $log_action, 'incident_reports', $id, $log_desc);

        echo json_encode(['success' => true, 'message' => 'Đã lưu kết quả xử lý.']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Lỗi SQL: ' . $stmt->error]);
    }
    $stmt->close();

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

$db->dongKetNoi($conn);
?>