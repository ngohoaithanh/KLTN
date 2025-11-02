<?php
// FILE: api/dashboard/summary.php (PHIÊN BẢN SỬA LỖI HOÀN CHỈNH)
header('Content-Type: application/json; charset=utf-8');

include_once("../../config/database.php");
$db = new clsKetNoi();
$conn = $db->moKetNoi();

// 1. LẤY KPI CHO HÔM NAY (Không thay đổi)
$kpi_today_sql = "
    SELECT
        (SELECT COUNT(ID) FROM orders WHERE DATE(Created_at) = CURDATE()) as total_orders_today,
        (SELECT SUM(ShippingFee) FROM orders WHERE DATE(Created_at) = CURDATE()) as total_revenue_today,
        (SELECT COUNT(ID) FROM orders WHERE status = 'pending') as pending_orders,
        (SELECT COUNT(shipper_id) FROM shipper_locations WHERE status IN ('online', 'busy') AND updated_at >= NOW() - INTERVAL 5 MINUTE) as active_shippers,
        (SELECT COUNT(ID) FROM users WHERE Role = 6) as total_shippers,
        (SELECT COUNT(ID) FROM users WHERE Role = 7) as total_customers,
        (SELECT SUM(CODFee) FROM orders WHERE DATE(Created_at) = CURDATE() AND COD_amount > 0) as total_cod_fee_today
";
$kpi_result = $conn->query($kpi_today_sql);
$kpi_data = $kpi_result->fetch_assoc();

// 2. CHUẨN BỊ BỘ LỌC THỜI GIAN (ĐÃ SỬA LỖI)
// Luôn sử dụng bí danh 'o.' để đảm bảo tính nhất quán
$sql_filter_created = "";
$sql_filter_accepted = "";
$param_type = "";
$param_value = null;

if (isset($_GET['date']) && !empty($_GET['date'])) {
    // LỌC THEO NGÀY CỤ THỂ
    $sql_filter_created = "WHERE DATE(o.Created_at) = ?";
    $sql_filter_accepted = "WHERE DATE(o.Accepted_at) = ?";
    $param_type = "s"; 
    $param_value = $_GET['date'];
} else {
    // LỌC THEO KHOẢNG NGÀY (MẶC ĐỊNH)
    $days = isset($_GET['days']) ? intval($_GET['days']) : 7;
    $sql_filter_created = "WHERE o.Created_at >= CURDATE() - INTERVAL ? DAY";
    $sql_filter_accepted = "WHERE o.Accepted_at >= CURDATE() - INTERVAL ? DAY";
    $param_type = "i";
    $param_value = $days;
}

// 3. Lấy dữ liệu cho biểu đồ đường (doanh thu & số đơn)
// Thêm bí danh 'o'
$daily_chart_sql = $conn->prepare("
    SELECT DATE(o.Created_at) as date, COUNT(o.ID) as total_orders, SUM(o.ShippingFee) as total_revenue
    FROM orders o 
    $sql_filter_created 
    GROUP BY DATE(o.Created_at)
    ORDER BY date ASC
");
$daily_chart_sql->bind_param($param_type, $param_value);
$daily_chart_sql->execute();
$daily_chart_result = $daily_chart_sql->get_result();
$daily_chart_data = [];
while ($row = $daily_chart_result->fetch_assoc()) { $daily_chart_data[] = $row; }
$daily_chart_sql->close();

// 4. Lấy dữ liệu cho biểu đồ tròn (trạng thái đơn)
// Thêm bí danh 'o'
$pie_chart_sql = $conn->prepare("
    SELECT o.status, COUNT(o.ID) as count
    FROM orders o
    $sql_filter_created 
    GROUP BY o.status
");
$pie_chart_sql->bind_param($param_type, $param_value);
$pie_chart_sql->execute();
$pie_chart_result = $pie_chart_sql->get_result();
$pie_chart_data = [];
while ($row = $pie_chart_result->fetch_assoc()) { $pie_chart_data[] = $row; }
$pie_chart_sql->close();

// 5. Lấy dữ liệu cho bảng xếp hạng Top 5 Shipper
// Đã có 'o.' và 'u.'
$top_shippers_sql = $conn->prepare("
    SELECT u.Username, COUNT(o.ID) as delivered_count
    FROM orders o JOIN users u ON o.ShipperID = u.ID
    $sql_filter_accepted AND o.status = 'delivered'
    GROUP BY o.ShipperID
    ORDER BY delivered_count DESC LIMIT 5
");
$top_shippers_sql->bind_param($param_type, $param_value);
$top_shippers_sql->execute();
$top_shippers_result = $top_shippers_sql->get_result();
$top_shippers_data = [];
while ($row = $top_shippers_result->fetch_assoc()) { $top_shippers_data[] = $row; }
$top_shippers_sql->close();

// 6. Lấy dữ liệu cho biểu đồ giờ cao điểm
// Thêm bí danh 'o'
$hourly_sql = $conn->prepare("
    SELECT HOUR(o.Created_at) as hour, COUNT(o.ID) as order_count
    FROM orders o
    $sql_filter_created
    GROUP BY HOUR(o.Created_at)
    ORDER BY hour ASC
");
$hourly_sql->bind_param($param_type, $param_value);
$hourly_sql->execute();
$hourly_result = $hourly_sql->get_result();
$hourly_data = [];
while ($row = $hourly_result->fetch_assoc()) { $hourly_data[] = $row; }
$hourly_sql->close();

// 7. Lấy dữ liệu cho heatmap
// Thêm bí danh 'o'
$heatmap_sql = $conn->prepare("
    SELECT o.Delivery_lat as lat, o.Delivery_lng as lng
    FROM orders o
    $sql_filter_created AND o.status = 'delivery_failed' AND o.Delivery_lat IS NOT NULL AND o.Delivery_lng IS NOT NULL
");
$heatmap_sql->bind_param($param_type, $param_value);
$heatmap_sql->execute();
$heatmap_result = $heatmap_sql->get_result();
$heatmap_data = [];
while ($row = $heatmap_result->fetch_assoc()) { $heatmap_data[] = $row; }
$heatmap_sql->close();

// 8. Lấy dữ liệu Top 5 Khách hàng
// Đã có 'o.' và 'u.'
$top_customers_sql = $conn->prepare("
    SELECT u.Username, COUNT(o.ID) as order_count
    FROM orders o JOIN users u ON o.CustomerID = u.ID
    $sql_filter_created
    GROUP BY o.CustomerID
    ORDER BY order_count DESC LIMIT 5
");
$top_customers_sql->bind_param($param_type, $param_value);
$top_customers_sql->execute();
$top_customers_result = $top_customers_sql->get_result();
$top_customers_data = [];
while ($row = $top_customers_result->fetch_assoc()) { $top_customers_data[] = $row; }
$top_customers_sql->close();

// 9. Lấy dữ liệu tăng trưởng người dùng (Không thay đổi, vì truy vấn bảng 'users')
$growth_days_filter = 30; 
if (isset($_GET['days']) && !isset($_GET['date'])) {
    $growth_days_filter = intval($_GET['days']);
}
$growth_data_sql = $conn->prepare("
    WITH daily_new_users AS (
        SELECT DATE(created_at) AS join_date,
               SUM(CASE WHEN Role = 7 THEN 1 ELSE 0 END) AS new_customers,
               SUM(CASE WHEN Role = 6 THEN 1 ELSE 0 END) AS new_shippers
        FROM users
        WHERE created_at >= CURDATE() - INTERVAL ? DAY
        GROUP BY DATE(created_at)
    )
    SELECT join_date,
           SUM(new_customers) OVER (ORDER BY join_date) AS cumulative_customers,
           SUM(new_shippers) OVER (ORDER BY join_date) AS cumulative_shippers
    FROM daily_new_users ORDER BY join_date ASC;
");
$growth_data_sql->bind_param("i", $growth_days_filter);
$growth_data_sql->execute();
$growth_result = $growth_data_sql->get_result();
$growth_data = [];
while ($row = $growth_result->fetch_assoc()) { $growth_data[] = $row; }
$growth_data_sql->close();

// Tập hợp tất cả dữ liệu và trả về
$response = [
    'kpi' => $kpi_data,
    'dailyChart' => $daily_chart_data,
    'statusPieChart' => $pie_chart_data,
    'topShippers' => $top_shippers_data,
    'hourlyStats' => $hourly_data,
    'failedHeatmap' => $heatmap_data,
    'topCustomers' => $top_customers_data,
    'growthChart' => $growth_data
];

echo json_encode($response);

$db->dongKetNoi($conn);
?>