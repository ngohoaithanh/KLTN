<?php
// FILE: api/order/get_orders.php
header('Content-Type: application/json; charset=utf-8');
include_once('../../config/database.php');

$db = new clsKetNoi();
$conn = $db->moKetNoi();

// 1. Lấy tham số
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 20; // 20 đơn hàng mỗi trang
$offset = ($page - 1) * $limit;
$search = isset($_GET['search']) ? trim($_GET['search']) : null;

// 2. Xây dựng câu truy vấn WHERE
$where_clause = "WHERE 1=1"; // Bắt đầu với mệnh đề true
$params = [];
$types = "";

if ($search !== null) {
    $search_term = "%" . $search . "%";
    // Tìm kiếm ở nhiều cột
    $where_clause .= " AND (
        o.ID LIKE ? OR 
        u_customer.Username LIKE ? OR 
        o.Delivery_address LIKE ? OR 
        o.status LIKE ? OR 
        u_shipper.Username LIKE ? OR 
        o.Recipient LIKE ? OR 
        o.RecipientPhone LIKE ?
    )";
    // 7 tham số 's' (string)
    $types .= "sssssss";
    for ($i = 0; $i < 7; $i++) {
        $params[] = $search_term;
    }
}

// 3. Truy vấn ĐẾM TỔNG SỐ (COUNT) - để tính toán phân trang
$count_sql = "
    SELECT COUNT(o.ID) as total
    FROM orders o
    LEFT JOIN users u_customer ON o.CustomerID = u_customer.ID
    LEFT JOIN users u_shipper ON o.ShipperID = u_shipper.ID
    $where_clause
";

$stmt_count = $conn->prepare($count_sql);
if (!empty($params)) {
    $stmt_count->bind_param($types, ...$params);
}
$stmt_count->execute();
$total_records = $stmt_count->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_records / $limit);
$stmt_count->close();

// 4. Truy vấn LẤY DỮ LIỆU (SELECT) - với LIMIT và OFFSET
$sql = "
    SELECT 
        o.ID,
        u_customer.Username as UserName,
        o.Delivery_address,
        o.Recipient,
        o.RecipientPhone,
        o.Created_at,
        o.COD_amount,
        o.CODFee,
        o.ShippingFee AS Shippingfee,
        u_shipper.Username as ShipperName,
        o.status as Status,
        o.Note,
        u_customer.PhoneNumber as PhoneNumberCus,
        o.Pick_up_address
    FROM orders o
    LEFT JOIN users u_customer ON o.CustomerID = u_customer.ID
    LEFT JOIN users u_shipper ON o.ShipperID = u_shipper.ID
    $where_clause
    ORDER BY o.ID DESC
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

// 5. Trả về JSON hoàn chỉnh
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