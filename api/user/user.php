<?php
// FILE: api/user/user.php (ĐÃ NÂNG CẤP PHÂN TRANG)
header('Content-Type: application/json; charset=utf-8');
include_once('../../config/database.php');

$db = new clsKetNoi();
$conn = $db->moKetNoi();

// --- 1. Lấy tham số ---
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 20; // Số lượng bản ghi mỗi trang
$offset = ($page - 1) * $limit;
$search = isset($_GET['search']) ? trim($_GET['search']) : null;

// --- 2. Xây dựng câu truy vấn WHERE ---
$where_clause = "WHERE u.Role NOT IN (1, 7) AND hidden = 1"; // Lấy tất cả user TRỪ Shipper và Khách hàng
$params = [];
$types = "";

if ($search !== null) {
    $search_term = "%" . $search . "%";
    // Tìm kiếm ở nhiều cột
    $where_clause .= " AND (u.Username LIKE ? OR u.PhoneNumber LIKE ? OR u.Email LIKE ? OR r.Name LIKE ?)";
    // 4 tham số 's' (string)
    $types .= "ssss";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
}

// --- 3. Truy vấn ĐẾM TỔNG SỐ (COUNT) ---
// Câu lệnh này dùng để tính toán có bao nhiêu trang
$count_sql = "SELECT COUNT(u.ID) as total 
              FROM users u 
              LEFT JOIN roles r ON u.Role = r.ID 
              $where_clause";
              
$stmt_count = $conn->prepare($count_sql);
if (!empty($params)) {
    $stmt_count->bind_param($types, ...$params);
}
$stmt_count->execute();
$total_records = $stmt_count->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_records / $limit);
$stmt_count->close();

// --- 4. Truy vấn LẤY DỮ LIỆU (SELECT) ---
$sql = "
    SELECT 
        u.ID, u.Username, u.Email, u.PhoneNumber, u.account_status,
        r.Name as RoleName
    FROM users u
    LEFT JOIN roles r ON u.Role = r.ID
    $where_clause
    ORDER BY u.ID DESC
    LIMIT ? OFFSET ?
";

// Thêm 2 tham số 'i' (integer) cho LIMIT và OFFSET
$types .= "ii";
$params[] = $limit;
$params[] = $offset;

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}
$stmt->close();
$db->dongKetNoi($conn);

// --- 5. Trả về JSON hoàn chỉnh ---
echo json_encode([
    'data' => $data,
    'pagination' => [
        'current_page' => $page,
        'total_pages' => $total_pages,
        'total_records' => $total_records,
        'limit' => $limit
    ]
]);
?>