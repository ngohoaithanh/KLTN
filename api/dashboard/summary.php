<?php
header('Content-Type: application/json');
include_once("../../config/database.php");

$db_class = new clsKetNoi();
$conn = $db_class->moKetNoi();

// Lấy khoảng thời gian từ request (mặc định 7 ngày)
$days = isset($_GET['days']) ? intval($_GET['days']) : 7;

// 1. Lấy các chỉ số KPI cho ngày hôm nay
$kpi_today_sql = "
    SELECT
        (SELECT COUNT(ID) FROM orders WHERE DATE(Created_at) = CURDATE()) as total_orders_today,
        (SELECT SUM(ShippingFee) FROM orders WHERE DATE(Created_at) = CURDATE()) as total_revenue_today,
        (SELECT COUNT(ID) FROM orders WHERE status = 'pending') as pending_orders,
        (SELECT COUNT(shipper_id) FROM shipper_locations WHERE status IN ('online', 'busy') AND updated_at >= NOW() - INTERVAL 5 MINUTE) as active_shippers,
        (SELECT COUNT(ID) FROM users WHERE Role = 6) as total_shippers,
        (SELECT COUNT(ID) FROM users WHERE Role = 7) as total_customers
";
$kpi_result = $conn->query($kpi_today_sql);
$kpi_data = $kpi_result->fetch_assoc();

// 2. Lấy dữ liệu cho biểu đồ đường (doanh thu & số đơn)
$daily_chart_sql = $conn->prepare("
    SELECT
        DATE(Created_at) as date,
        COUNT(ID) as total_orders,
        SUM(ShippingFee) as total_revenue
    FROM orders
    WHERE Created_at >= CURDATE() - INTERVAL ? DAY
    GROUP BY DATE(Created_at)
    ORDER BY date ASC
");
$daily_chart_sql->bind_param("i", $days);
$daily_chart_sql->execute();
$daily_chart_result = $daily_chart_sql->get_result();
$daily_chart_data = [];
while ($row = $daily_chart_result->fetch_assoc()) {
    $daily_chart_data[] = $row;
}

// 3. Lấy dữ liệu cho biểu đồ tròn (trạng thái đơn)
$pie_chart_sql = $conn->prepare("
    SELECT status, COUNT(ID) as count
    FROM orders
    WHERE Created_at >= CURDATE() - INTERVAL ? DAY
    GROUP BY status
");
$pie_chart_sql->bind_param("i", $days);
$pie_chart_sql->execute();
$pie_chart_result = $pie_chart_sql->get_result();
$pie_chart_data = [];
while ($row = $pie_chart_result->fetch_assoc()) {
    $pie_chart_data[] = $row;
}

// 4. Lấy dữ liệu cho bảng xếp hạng Top 5 Shipper
$top_shippers_sql = $conn->prepare("
    SELECT u.Username, COUNT(o.ID) as delivered_count
    FROM orders o
    JOIN users u ON o.ShipperID = u.ID
    WHERE o.status = 'delivered' AND o.Accepted_at >= CURDATE() - INTERVAL ? DAY
    GROUP BY o.ShipperID
    ORDER BY delivered_count DESC
    LIMIT 5
");
$top_shippers_sql->bind_param("i", $days);
$top_shippers_sql->execute();
$top_shippers_result = $top_shippers_sql->get_result();
$top_shippers_data = [];
while ($row = $top_shippers_result->fetch_assoc()) {
    $top_shippers_data[] = $row;
}

// 5. Lấy dữ liệu cho biểu đồ giờ cao điểm
$hourly_sql = $conn->prepare("
    SELECT HOUR(Created_at) as hour, COUNT(ID) as order_count
    FROM orders
    WHERE Created_at >= CURDATE() - INTERVAL ? DAY
    GROUP BY HOUR(Created_at)
    ORDER BY hour ASC
");
$hourly_sql->bind_param("i", $days);
$hourly_sql->execute();
$hourly_result = $hourly_sql->get_result();
$hourly_data = [];
while ($row = $hourly_result->fetch_assoc()) {
    $hourly_data[] = $row;
}

// 6. Lấy dữ liệu cho heatmap
$heatmap_sql = $conn->prepare("
    SELECT Delivery_lat as lat, Delivery_lng as lng
    FROM orders
    WHERE status = 'delivery_failed' 
      AND Delivery_lat IS NOT NULL 
      AND Delivery_lng IS NOT NULL
      AND Created_at >= CURDATE() - INTERVAL ? DAY
");
$heatmap_sql->bind_param("i", $days);
$heatmap_sql->execute();
$heatmap_result = $heatmap_sql->get_result();
$heatmap_data = [];
while ($row = $heatmap_result->fetch_assoc()) {
    $heatmap_data[] = $row;
}

// 7. Lấy dữ liệu Top 5 Khách hàng
$top_customers_sql = $conn->prepare("
    SELECT u.Username, COUNT(o.ID) as order_count
    FROM orders o
    JOIN users u ON o.CustomerID = u.ID
    WHERE o.Created_at >= CURDATE() - INTERVAL ? DAY
    GROUP BY o.CustomerID
    ORDER BY order_count DESC
    LIMIT 5
");
$top_customers_sql->bind_param("i", $days);
$top_customers_sql->execute();
$top_customers_result = $top_customers_sql->get_result();
$top_customers_data = [];
while ($row = $top_customers_result->fetch_assoc()) {
    $top_customers_data[] = $row;
}

// 8. Lấy dữ liệu tăng trưởng người dùng (từ bảng users)
$growth_data_sql = $conn->prepare("
    WITH daily_new_users AS (
        -- Đếm số lượng người dùng mới mỗi ngày theo vai trò
        SELECT
            DATE(created_at) AS join_date,
            SUM(CASE WHEN Role = 7 THEN 1 ELSE 0 END) AS new_customers,
            SUM(CASE WHEN Role = 6 THEN 1 ELSE 0 END) AS new_shippers
        FROM users
        WHERE created_at >= CURDATE() - INTERVAL ? DAY
        GROUP BY DATE(created_at)
    )
    -- Tính tổng tích lũy theo từng ngày
    SELECT 
        join_date,
        SUM(new_customers) OVER (ORDER BY join_date) AS cumulative_customers,
        SUM(new_shippers) OVER (ORDER BY join_date) AS cumulative_shippers
    FROM daily_new_users
    ORDER BY join_date ASC;
");
$growth_data_sql->bind_param("i", $days);
$growth_data_sql->execute();
$growth_result = $growth_data_sql->get_result();
$growth_data = [];
while ($row = $growth_result->fetch_assoc()) {
    $growth_data[] = $row;
}

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

$db_class->dongKetNoi($conn);
?>