<?php
// FILE: api/cod_dashboard/get_receivables.php (ĐÃ NÂNG CẤP "NỢ QUÁ HẠN")
header('Content-Type: application/json; charset=utf-8');
include_once("../../config/database.php");

$db_class = new clsKetNoi();
$conn = $db_class->moKetNoi();

$response = [
    'kpi' => [],
    'shipper_balances' => [],
    'recent_transactions' => []
];

// --- 1. Lấy dữ liệu cho Bảng Công Nợ Shipper (Nâng cấp) ---
$sql_table = "
    SELECT
        u.ID AS shipper_id,
        u.Username,
        u.PhoneNumber,
        
        -- (A) Tổng phí COD shipper đã thu (từ các đơn đã giao)
        (SELECT COALESCE(SUM(o.CODFee), 0)
         FROM orders o
         WHERE o.ShipperID = u.ID AND o.status = 'delivered') AS TotalFeeCollected,
        
        -- (B) Tổng tiền shipper đã nộp
        (SELECT COALESCE(SUM(t.Amount), 0)
         FROM transactions t
         WHERE t.UserID = u.ID AND t.Type = 'deposit_cod') AS TotalFeePaid,
         
        -- (C) TÍNH TOÁN MỚI: Tổng phí quá hạn (đã giao > 7 ngày & chưa nộp)

         (SELECT COALESCE(SUM(o.CODFee), 0)
         FROM orders o
         -- Join với bảng trackings để tìm ngày giao hàng thực tế
         LEFT JOIN trackings t_delivered 
             ON o.ID = t_delivered.OrderID 
             AND t_delivered.Status = 'Giao hàng thành công!'
         WHERE o.ShipperID = u.ID 
           AND o.status = 'delivered'
           -- Điều kiện lọc mới: Ngày giao hàng thực tế < 7 ngày trước
           AND t_delivered.Updated_at < NOW() - INTERVAL 7 DAY 
           AND NOT EXISTS (
               SELECT 1 FROM transactions t
               WHERE t.OrderID = o.ID AND t.Type = 'deposit_cod'
           )
        ) AS TotalOverdueFee
            
    FROM
        users u
    WHERE
        u.Role = 6 -- Chỉ lấy Shipper
    GROUP BY
        u.ID, u.Username, u.PhoneNumber
    HAVING 
        TotalFeeCollected != TotalFeePaid -- Chỉ hiện shipper có nợ
";

$result_table = $conn->query($sql_table);
$shipper_balances = [];
$total_fee_owed = 0; 

while ($row = $result_table->fetch_assoc()) {
    $balance = $row['TotalFeeCollected'] - $row['TotalFeePaid'];
    $row['Balance'] = $balance;
    $total_fee_owed += $balance;
    $shipper_balances[] = $row;
}
usort($shipper_balances, function($a, $b) {
    return $b['Balance'] <=> $a['Balance'];
});
$response['shipper_balances'] = $shipper_balances;


// --- 2. Lấy dữ liệu cho các Thẻ KPI (Giữ nguyên) ---
$sql_kpi = "
    SELECT
        (SELECT COALESCE(SUM(Amount), 0) FROM transactions WHERE Type = 'deposit_cod' AND DATE(Created_at) = CURDATE()) AS SettledToday,
        (SELECT COALESCE(SUM(CODFee), 0) FROM orders WHERE status IN ('in_transit', 'picked_up')) AS FeeInProgress,
        (SELECT COALESCE(SUM(CODFee), 0) FROM orders WHERE MONTH(Created_at) = MONTH(CURDATE()) AND YEAR(Created_at) = YEAR(CURDATE())) AS FeeThisMonth
";
$kpi_data = $conn->query($sql_kpi)->fetch_assoc();
$kpi_data['TotalFeeOwed'] = $total_fee_owed; 
$response['kpi'] = $kpi_data;

// --- 3. LẤY LỊCH SỬ GIAO DỊCH (Giữ nguyên) ---
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-7 days'));
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');
$end_date_sql = date('Y-m-d', strtotime($end_date . ' +1 day'));

$sql_history = $conn->prepare("
    SELECT 
        t.Created_at, u.Username, t.Amount, t.OrderID, t.Note, t.Type, 
        t.ProofImage  /* <-- THÊM CỘT NÀY */
    FROM transactions t
    JOIN users u ON t.UserID = u.ID
    WHERE t.Type IN ('deposit_cod', 'overpayment_cod') 
      AND t.Created_at >= ? AND t.Created_at < ? 
    ORDER BY t.Created_at DESC
");
$sql_history->bind_param("ss", $start_date, $end_date_sql);
$sql_history->execute();
$history_result = $sql_history->get_result();
$recent_transactions = [];
while ($row = $history_result->fetch_assoc()) {
    $recent_transactions[] = $row;
}
$response['recent_transactions'] = $recent_transactions;
$sql_history->close();

echo json_encode($response);
// $db->dongKetNoi($conn);
?>