<?php
header('Content-Type: application/json');
include_once("../../config/database.php");

// Kiểm tra xem shipper_id có được cung cấp không
if (!isset($_GET['id'])) {
    die(json_encode(['error' => 'Thiếu ID của shipper.']));
}
$shipperId = intval($_GET['id']);

// Lấy khoảng thời gian (mặc định là 7 ngày qua)
$days = isset($_GET['days']) ? intval($_GET['days']) : 7;

$db_class = new clsKetNoi();
$conn = $db_class->moKetNoi();

// 1. Lấy thông tin cơ bản của shipper
$stmt = $conn->prepare("SELECT Username, PhoneNumber, rating FROM users WHERE ID = ?");
$stmt->bind_param("i", $shipperId);
$stmt->execute();
$shipperInfo = $stmt->get_result()->fetch_assoc();

// 2. Lấy các chỉ số KPI và thời gian giao hàng trung bình
// $stmt = $conn->prepare("
//     SELECT
//         COUNT(o.ID) as total_orders,
//         SUM(CASE WHEN o.status = 'delivered' THEN 1 ELSE 0 END) as delivered_orders,
//         SUM(o.ShippingFee) as total_fee,
//         -- Tính thời gian giao hàng trung bình bằng giây
//         AVG(CASE 
//             WHEN o.status = 'delivered' THEN TIMESTAMPDIFF(SECOND, t_pickup.Updated_at, t_delivered.Updated_at) 
//             ELSE NULL 
//         END) as avg_delivery_time_seconds
//     FROM 
//         orders o
//     -- Join để lấy thời gian shipper lấy hàng
//     LEFT JOIN trackings t_pickup ON o.ID = t_pickup.OrderID AND t_pickup.Status LIKE 'Shipper đã lấy hàng thành công.'
//     -- Join để lấy thời gian shipper giao hàng thành công
//     LEFT JOIN trackings t_delivered ON o.ID = t_delivered.OrderID AND t_delivered.Status LIKE 'Giao hàng thành công!'
//     WHERE 
//         o.ShipperID = ? AND o.Accepted_at >= CURDATE() - INTERVAL ? DAY
// ");
// $stmt->bind_param("ii", $shipperId, $days);
// $stmt->execute();
// $kpiStats = $stmt->get_result()->fetch_assoc();

// 2. Lấy các chỉ số KPI
$stmt = $conn->prepare("
    SELECT
        COUNT(ID) as total_orders,
        SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as delivered_orders,
        /* THÊM DÒNG NÀY */
        SUM(CASE WHEN status = 'delivery_failed' THEN 1 ELSE 0 END) as failed_orders, 
        SUM(ShippingFee) as total_fee
    FROM orders
    WHERE ShipperID = ? AND Accepted_at >= CURDATE() - INTERVAL ? DAY
");
$stmt->bind_param("ii", $shipperId, $days);
$stmt->execute();
$kpiStats = $stmt->get_result()->fetch_assoc();


// 3. Lấy dữ liệu cho biểu đồ đơn hàng theo ngày
$stmt = $conn->prepare("
    SELECT
        DATE(Accepted_at) as order_date,
        COUNT(ID) as order_count
    FROM orders
    WHERE ShipperID = ? AND Accepted_at >= CURDATE() - INTERVAL ? DAY
    GROUP BY DATE(Accepted_at)
    ORDER BY order_date ASC
");
$stmt->bind_param("ii", $shipperId, $days);
$stmt->execute();
$chartResult = $stmt->get_result();
$chartData = [];
while ($row = $chartResult->fetch_assoc()) {
    $chartData[] = $row;
}

// 4. Lấy dữ liệu cho biểu đồ tròn
$stmt = $conn->prepare("
    SELECT
        status,
        COUNT(ID) as count
    FROM orders
    WHERE ShipperID = ? AND Accepted_at >= CURDATE() - INTERVAL ? DAY AND status IN ('delivered', 'delivery_failed', 'cancelled')
    GROUP BY status
");
$stmt->bind_param("ii", $shipperId, $days);
$stmt->execute();
$pieResult = $stmt->get_result();
$pieData = [];
while ($row = $pieResult->fetch_assoc()) {
    $pieData[] = $row;
}

// 5. Lấy dữ liệu cho biểu đồ PHÍ COD & PHÍ VC theo ngày
$daily_fees_chart_sql = $conn->prepare("
    SELECT
        DATE(Accepted_at) as order_date,
        SUM(ShippingFee) as total_shipping_fee,
        SUM(CODFee) as total_cod_fee 
    FROM orders
    WHERE ShipperID = ? AND Accepted_at >= CURDATE() - INTERVAL ? DAY
    GROUP BY DATE(Accepted_at)
    ORDER BY order_date ASC
");
$daily_fees_chart_sql->bind_param("ii", $shipperId, $days);
$daily_fees_chart_sql->execute();
$feesChartResult = $daily_fees_chart_sql->get_result();
$feesChartData = [];
while ($row = $feesChartResult->fetch_assoc()) {
    $feesChartData[] = $row;
}
$daily_fees_chart_sql->close(); // <-- Đóng statement nà

// Tập hợp tất cả dữ liệu và trả về dưới dạng JSON
$response = [
    'shipperInfo' => $shipperInfo,
    'kpiStats' => $kpiStats,
    'dailyOrdersChart' => $chartData,
    'dailyFeesChart' => $feesChartData,
    'statusPieChart' => $pieData
];

echo json_encode($response);

$db_class->dongKetNoi($conn);
?>