<?php
// FILE: api/system_log/get_logs.php
header('Content-Type: application/json; charset=utf-8');
include_once("../../config/database.php");

// Chỉ Admin mới được xem log (Bảo mật thêm ở API)
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 1) {
    echo json_encode(['data' => []]); // Trả về rỗng nếu không phải Admin
    exit;
}

$db = new clsKetNoi();
$conn = $db->moKetNoi();

// Lấy tham số phân trang
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 50; // 50 dòng mỗi trang
$offset = ($page - 1) * $limit;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Xây dựng truy vấn
$where = "WHERE 1=1";
$params = [];
$types = "";

if ($search) {
    $where .= " AND (u.Username LIKE ? OR l.Action LIKE ? OR l.Description LIKE ?)";
    $term = "%$search%";
    $params = [$term, $term, $term];
    $types = "sss";
}

// 1. Đếm tổng số
$count_sql = "SELECT COUNT(*) as total FROM system_logs l LEFT JOIN users u ON l.UserID = u.ID $where";
$stmt_count = $conn->prepare($count_sql);
if ($search) $stmt_count->bind_param($types, ...$params);
$stmt_count->execute();
$total = $stmt_count->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total / $limit);
$stmt_count->close();

// 2. Lấy dữ liệu
$sql = "SELECT l.*, u.Username 
        FROM system_logs l 
        LEFT JOIN users u ON l.UserID = u.ID 
        $where 
        ORDER BY l.Created_at DESC 
        LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);
// Thêm tham số limit/offset vào bind_param
$params[] = $limit;
$params[] = $offset;
$types .= "ii";

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