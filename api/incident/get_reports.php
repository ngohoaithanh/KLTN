<?php
// FILE: api/incident/get_reports.php
header('Content-Type: application/json; charset=utf-8');
include_once("../../config/database.php");

$db = new clsKetNoi();
$conn = $db->moKetNoi();

// 1. Lấy tham số phân trang & tìm kiếm
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 20;
$offset = ($page - 1) * $limit;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status = isset($_GET['status']) ? trim($_GET['status']) : 'all';

// 2. Xây dựng câu truy vấn
$where = "WHERE 1=1";
$params = [];
$types = "";

if ($search) {
    $where .= " AND (r.OrderID LIKE ? OR u.Username LIKE ? OR r.Type LIKE ?)";
    $term = "%$search%";
    $params = [$term, $term, $term];
    $types .= "sss";
}

if ($status !== 'all') {
    $where .= " AND r.Status = ?";
    $params[] = $status;
    $types .= "s";
}

// 3. Đếm tổng số (cho phân trang)
$count_sql = "
    SELECT COUNT(r.ID) as total 
    FROM incident_reports r 
    JOIN users u ON r.ReporterID = u.ID 
    $where
";
$stmt_count = $conn->prepare($count_sql);
if (!empty($params)) $stmt_count->bind_param($types, ...$params);
$stmt_count->execute();
$total = $stmt_count->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total / $limit);
$stmt_count->close();

// 4. Lấy dữ liệu chi tiết
// JOIN để lấy tên người báo cáo và vai trò của họ
$sql = "
    SELECT 
        r.*, 
        u.Username AS ReporterName, 
        u.PhoneNumber AS ReporterPhone,
        ro.Name AS ReporterRole
    FROM incident_reports r
    JOIN users u ON r.ReporterID = u.ID
    JOIN roles ro ON u.Role = ro.ID
    $where
    ORDER BY r.Created_at DESC
    LIMIT ? OFFSET ?
";

$params[] = $limit;
$params[] = $offset;
$types .= "ii";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode([
    'data' => $data,
    'pagination' => [
        'current_page' => $page,
        'total_pages' => $total_pages,
        'total_records' => $total
    ]
]);

$db->dongKetNoi($conn);
?>