<?php
header('Content-Type: application/json; charset=utf-8');
include_once('../../config/database.php'); // Đảm bảo đường dẫn này đúng

$db = new clsKetNoi();
$conn = $db->moKetNoi();
// Khởi tạo response với giá trị mặc định
$response = ['net_income' => 0, 'service_fee_held' => 0];

if (isset($_GET['shipper_id']) && is_numeric($_GET['shipper_id'])) {
    $shipperId = intval($_GET['shipper_id']);

    if ($conn) {
        try {
            // --- Tính Thu Nhập Ròng (Net Income) ---
            $stmt_income = $conn->prepare("
                SELECT 
                    -- Tổng thu nhập từ phí ship và bonus
                    COALESCE(SUM(CASE WHEN Type IN ('shipping_fee', 'bonus') THEN Amount ELSE 0 END), 0) AS total_earn,
                    -- Tổng các khoản chi (phạt, rút tiền)
                    COALESCE(SUM(CASE WHEN Type IN ('penalty', 'withdraw') THEN Amount ELSE 0 END), 0) AS total_spent 
                FROM transactions 
                WHERE UserID = ? AND Status = 'completed'
            ");
            $stmt_income->bind_param("i", $shipperId);
            $stmt_income->execute();
            $income_result = $stmt_income->get_result()->fetch_assoc();
            $netIncome = $income_result['total_earn'] - $income_result['total_spent'];
            $response['net_income'] = $netIncome;
            $stmt_income->close();

            // --- Tính Phí Dịch Vụ COD (CODFee) đang giữ ---
            // Cách 1: Tính dựa trên các giao dịch (chính xác hơn nếu 'deposit_cod' ghi đúng số tiền nộp)
            /*
            $stmt_fee = $conn->prepare("
                SELECT 
                    COALESCE(SUM(CASE WHEN Type = 'collect_cod' THEN CODFee_Component ELSE 0 END), 0) AS total_cod_fee_collected,
                    COALESCE(SUM(CASE WHEN Type = 'deposit_cod' THEN CODFee_Component_Deposited ELSE 0 END), 0) AS total_cod_fee_deposited
                FROM transactions 
                WHERE UserID = ? AND Type IN ('collect_cod', 'deposit_cod') AND Status = 'completed' 
                -- Giả sử bạn có cách lưu hoặc tính CODFee riêng trong bảng transactions
            ");
            // ... thực thi và tính toán ...
            */

            // Cách 2: Truy vấn CODFee trực tiếp từ các đơn hàng 'delivered' mà shipper chưa nộp tiền
            // Cách này đơn giản hơn nếu bảng transactions chưa tách CODFee
            $stmt_fee = $conn->prepare("
                SELECT COALESCE(SUM(o.CODFee), 0) AS total_fee_held
                FROM orders o
                LEFT JOIN transactions t_deposit ON o.ID = t_deposit.OrderID AND t_deposit.Type = 'deposit_cod' AND t_deposit.UserID = ?
                WHERE o.ShipperID = ? 
                  AND o.status = 'delivered' 
                  AND o.COD_amount > 0 
                  AND t_deposit.ID IS NULL -- Chỉ lấy các đơn chưa có giao dịch nộp tiền COD
            ");
            $stmt_fee->bind_param("ii", $shipperId, $shipperId);
            $stmt_fee->execute();
            $fee_result = $stmt_fee->get_result()->fetch_assoc();
            $response['service_fee_held'] = $fee_result['total_fee_held'];
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