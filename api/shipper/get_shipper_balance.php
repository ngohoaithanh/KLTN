<?php
header('Content-Type: application/json; charset=utf-8');
include_once('../../config/database.php'); // Đảm bảo đường dẫn đúng

$db = new clsKetNoi();
$conn = $db->moKetNoi();
// Khởi tạo response với 3 giá trị mới
$response = ['net_income' => 0, 'fee_in_limit' => 0, 'fee_overdue' => 0];

if (isset($_GET['shipper_id']) && is_numeric($_GET['shipper_id'])) {
    $shipperId = intval($_GET['shipper_id']);

    if ($conn) {
        try {
            // --- Tính Thu Nhập Ròng (Net Income) ---
            // (Phần này giữ nguyên, đã đúng)
            $stmt_income = $conn->prepare("
                SELECT 
                    COALESCE(SUM(CASE WHEN Type IN ('shipping_fee', 'bonus') THEN Amount ELSE 0 END), 0) AS total_earn,
                    COALESCE(SUM(CASE WHEN Type IN ('penalty', 'withdraw') THEN Amount ELSE 0 END), 0) AS total_spent 
                FROM transactions 
                WHERE UserID = ? AND Status = 'completed'
            ");
            $stmt_income->bind_param("i", $shipperId);
            $stmt_income->execute();
            $income_result = $stmt_income->get_result()->fetch_assoc();
            $response['net_income'] = ($income_result['total_earn'] ?? 0) - ($income_result['total_spent'] ?? 0);
            $stmt_income->close();

            // =======================================================
            // ## TÍNH PHÍ COD TRONG HẠN VÀ QUÁ HẠN (ĐÃ NÂNG CẤP) ##
            // =======================================================
            
            // Giả định: Giao dịch 'collect_cod' được tạo khi đơn hàng 'delivered'
            // Chúng ta sẽ tham chiếu đến ngày tạo của giao dịch 'collect_cod'
            
            $stmt_fee = $conn->prepare("
                SELECT 
                    -- Phí COD trong hạn (tính từ 7 ngày gần nhất)
                    COALESCE(SUM(CASE
                        WHEN t_collect.Created_at > DATE_SUB(NOW(), INTERVAL 7 DAY) THEN o.CODFee
                        ELSE 0
                    END), 0) AS fee_in_limit,
                    
                    -- Phí COD quá hạn (tính từ 7 ngày trở về trước)
                    COALESCE(SUM(CASE
                        WHEN t_collect.Created_at <= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN o.CODFee
                        ELSE 0
                    END), 0) AS fee_overdue
                    
                FROM orders o
                -- Join với giao dịch 'collect_cod' để lấy ngày thu tiền
                JOIN transactions t_collect ON o.ID = t_collect.OrderID AND t_collect.Type = 'collect_cod' AND t_collect.UserID = ?
                -- Kiểm tra xem đã nộp tiền chưa
                LEFT JOIN transactions t_deposit ON o.ID = t_deposit.OrderID AND t_deposit.Type = 'deposit_cod' AND t_deposit.UserID = ?
                
                WHERE o.ShipperID = ? 
                  AND o.status = 'delivered' 
                  AND t_deposit.ID IS NULL -- Chỉ lấy các đơn chưa có giao dịch nộp tiền
            ");
            $stmt_fee->bind_param("iii", $shipperId, $shipperId, $shipperId);
            $stmt_fee->execute();
            $fee_result = $stmt_fee->get_result()->fetch_assoc();
            
            $response['fee_in_limit'] = $fee_result['fee_in_limit'];
            $response['fee_overdue'] = $fee_result['fee_overdue'];
            $stmt_fee->close();

        } catch (Exception $e) {
            http_response_code(500);
            $response = ["error" => "Database query failed: " . $e->getMessage()];
        }
    } else {
        http_response_code(500);
        $response = ["error" => "Connection failed"];
    }
} else {
    http_response_code(400);
    $response = ["error" => "Thiếu hoặc sai shipper_id"];
}

$db->dongKetNoi($conn);
echo json_encode($response);
?>