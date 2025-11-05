<?php
// FILE: api/shipper/getShipperDetailStats.php (Đã nâng cấp)
header('Content-Type: application/json');
include_once("../../config/database.php");

// Kiểm tra xem shipper_id có được cung cấp không
if (!isset($_GET['id'])) {
    die(json_encode(['error' => 'Thiếu ID của shipper.']));
}
$shipperId = intval($_GET['id']);

$db_class = new clsKetNoi();
$conn = $db_class->moKetNoi();

// --- BƯỚC NÂNG CẤP: CHUẨN BỊ BỘ LỌC THỜI GIAN ---
$sql_filter_accepted = ""; // Bộ lọc cho các bảng dựa trên ngày chấp nhận đơn
$param_type = "";
$param_value = null;

if (isset($_GET['date']) && !empty($_GET['date'])) {
    // Nếu người dùng chọn 1 ngày cụ thể
    $sql_filter_accepted = "AND DATE(Accepted_at) = ?";
    $param_type = "s"; // 's' for string
    $param_value = $_GET['date'];
} else {
    // Mặc định, hoặc khi người dùng chọn khoảng ngày
    $days = isset($_GET['days']) ? intval($_GET['days']) : 7;
    $sql_filter_accepted = "AND Accepted_at >= CURDATE() - INTERVAL ? DAY";
    $param_type = "i"; // 'i' for integer
    $param_value = $days;
}
// --- KẾT THÚC BƯỚC NÂNG CẤP ---


// 1. Lấy thông tin cơ bản của shipper (Không đổi)
$stmt_info = $conn->prepare("SELECT Username, PhoneNumber, rating FROM users WHERE ID = ?");
$stmt_info->bind_param("i", $shipperId);
$stmt_info->execute();
$shipperInfo = $stmt_info->get_result()->fetch_assoc();
$stmt_info->close();

// 2. Lấy các chỉ số KPI
// Sửa lại câu truy vấn để dùng bộ lọc mới
$stmt_kpi = $conn->prepare("
    SELECT
        COUNT(ID) as total_orders,
        SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as delivered_orders,
        SUM(ShippingFee) as total_fee,
        SUM(CASE WHEN status = 'delivery_failed' THEN 1 ELSE 0 END) as failed_orders
    FROM orders
    WHERE ShipperID = ? $sql_filter_accepted 
");
$stmt_kpi->bind_param("i{$param_type}", $shipperId, $param_value); // Thêm tham số lọc
$stmt_kpi->execute();
$kpiStats = $stmt_kpi->get_result()->fetch_assoc();
$stmt_kpi->close();

// 3. Lấy dữ liệu cho biểu đồ SỐ LƯỢNG đơn hàng theo ngày
$daily_orders_chart_sql = $conn->prepare("
    SELECT
        DATE(Accepted_at) as order_date,
        COUNT(ID) as order_count 
    FROM orders
    WHERE ShipperID = ? $sql_filter_accepted
    GROUP BY DATE(Accepted_at)
    ORDER BY order_date ASC
");
$daily_orders_chart_sql->bind_param("i{$param_type}", $shipperId, $param_value);
$daily_orders_chart_sql->execute();
$ordersChartResult = $daily_orders_chart_sql->get_result();
$ordersChartData = [];
while ($row = $ordersChartResult->fetch_assoc()) {
    $ordersChartData[] = $row;
}
$daily_orders_chart_sql->close();

// 4. Lấy dữ liệu cho biểu đồ PHÍ COD & PHÍ VC theo ngày
$daily_fees_chart_sql = $conn->prepare("
    SELECT
        DATE(Accepted_at) as order_date,
        SUM(ShippingFee) as total_shipping_fee,
        SUM(CODFee) as total_cod_fee 
    FROM orders
    WHERE ShipperID = ? $sql_filter_accepted
    GROUP BY DATE(Accepted_at)
    ORDER BY order_date ASC
");
$daily_fees_chart_sql->bind_param("i{$param_type}", $shipperId, $param_value);
$daily_fees_chart_sql->execute();
$feesChartResult = $daily_fees_chart_sql->get_result();
$feesChartData = [];
while ($row = $feesChartResult->fetch_assoc()) {
    $feesChartData[] = $row;
}
$daily_fees_chart_sql->close();

// 5. Lấy dữ liệu cho biểu đồ tròn
$stmt_pie = $conn->prepare("
    SELECT status, COUNT(ID) as count
    FROM orders
    WHERE ShipperID = ? $sql_filter_accepted AND status IN ('delivered', 'delivery_failed', 'cancelled')
    GROUP BY status
");
$stmt_pie->bind_param("i{$param_type}", $shipperId, $param_value);
$stmt_pie->execute();
$pieResult = $stmt_pie->get_result();
$pieData = [];
while ($row = $pieResult->fetch_assoc()) {
    $pieData[] = $row;
}
$stmt_pie->close();

// Tập hợp tất cả dữ liệu và trả về
$response = [
    'shipperInfo' => $shipperInfo,
    'kpiStats' => $kpiStats,
    'dailyOrdersChart' => $ordersChartData,
    'dailyFeesChart' => $feesChartData,
    'statusPieChart' => $pieData
];

echo json_encode($response);
$db_class->dongKetNoi($conn);
?>