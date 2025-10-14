<?php
header('Content-Type: application/json');
include_once("../../config/database.php");

$db_class = new clsKetNoi();
$conn = $db_class->moKetNoi();

if ($conn->connect_error) {
    die(json_encode(['error' => "Connection failed: " . $conn->connect_error]));
}

// Tham số để lọc theo trạng thái (all, online, busy, offline)
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';

// Nền tảng câu lệnh SQL
$sql = "SELECT 
            u.ID, u.Username, u.Email, u.PhoneNumber, u.rating,
            sl.lat, sl.lng, sl.status, sl.updated_at,
            v.license_plate, v.model AS vehicle_model
        FROM 
            users u
        LEFT JOIN 
            shipper_locations sl ON u.ID = sl.shipper_id
        LEFT JOIN 
            vehicles v ON u.ID = v.shipper_id AND v.is_active = 1
        WHERE 
            u.Role = 6"; // Role ID = 6 là Shipper

// Thêm điều kiện lọc nếu có
$params = [];
$types = '';
if ($status_filter != 'all') {
    if ($status_filter == 'offline') {
        // Shipper offline là người không có status 'online' hoặc 'busy'
        // hoặc bản ghi vị trí quá 5 phút
        $sql .= " AND (sl.status IS NULL OR sl.status = 'offline' OR sl.updated_at < NOW() - INTERVAL 5 MINUTE)";
    } else {
        $sql .= " AND sl.status = ? AND sl.updated_at >= NOW() - INTERVAL 5 MINUTE";
        $params[] = $status_filter;
        $types .= 's';
    }
}

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$shippers = [];
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Chuẩn hóa trạng thái cho nhất quán
        if (is_null($row['status']) || $row['status'] == 'offline' || (new DateTime($row['updated_at']) < (new DateTime())->modify('-5 minutes'))) {
            $row['status'] = 'offline';
        }
        $shippers[] = $row;
    }
}

echo json_encode($shippers);

$stmt->close();
$db_class->dongKetNoi($conn);
?>