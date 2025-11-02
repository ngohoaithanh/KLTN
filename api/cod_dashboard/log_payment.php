<?php
// FILE: api/cod_dashboard/log_payment.php (PHIÊN BẢN NÂNG CẤP THÔNG MINH)
header('Content-Type: application/json; charset=utf-8');
include_once("../../config/database.php");

$db_class = new clsKetNoi();
$conn = $db_class->moKetNoi();

// Nhận dữ liệu JSON được gửi từ JavaScript
$data = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($data['shipper_id']) || !isset($data['amount'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Dữ liệu không hợp lệ.']);
    exit;
}

$shipper_id = intval($data['shipper_id']);
$payment_amount = floatval($data['amount']); // Số tiền kế toán nhận
$note = isset($data['note']) ? trim($data['note']) : 'Kế toán ghi nhận nộp tiền CODFee';

if ($payment_amount <= 0) {
     http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Số tiền phải lớn hơn 0.']);
    exit;
}

// Bắt đầu một TRANSACTION (quan trọng, để đảm bảo tất cả hoặc không gì cả)
$conn->begin_transaction();

try {
    // 1. Tìm tất cả đơn hàng đã giao, có Phí COD > 0, và CHƯA được thanh toán
    // (Sắp xếp theo đơn hàng cũ nhất trước - FIFO)
    $find_unpaid_sql = "
        SELECT o.ID, o.CODFee
        FROM orders o
        WHERE o.ShipperID = ? 
          AND o.status = 'delivered' 
          AND o.CODFee > 0
          AND NOT EXISTS (
              SELECT 1 FROM transactions t
              WHERE t.OrderID = o.ID AND t.Type = 'deposit_cod'
          )
        ORDER BY o.Accepted_at ASC
    ";
    
    $stmt_find = $conn->prepare($find_unpaid_sql);
    $stmt_find->bind_param("i", $shipper_id);
    $stmt_find->execute();
    $unpaid_orders = $stmt_find->get_result();
    $stmt_find->close();

    $payment_remaining = $payment_amount;

    $stmt_insert = $conn->prepare(
        "INSERT INTO transactions (UserID, OrderID, Type, Amount, Status, Note, Created_at) 
         VALUES (?, ?, 'deposit_cod', ?, 'completed', ?, NOW())"
    );

    // 2. Lặp qua các đơn hàng chưa thanh toán và áp dụng thanh toán
    while ($order = $unpaid_orders->fetch_assoc()) {
        if ($payment_remaining <= 0) {
            break; // Đã dùng hết tiền thanh toán
        }

        $order_id = $order['ID'];
        $fee_to_pay = floatval($order['CODFee']);

        // Nếu tiền còn lại đủ trả cho đơn này
        if ($payment_remaining >= $fee_to_pay) {
            $stmt_insert->bind_param("isds", $shipper_id, $order_id, $fee_to_pay, $note);
            $stmt_insert->execute();
            $payment_remaining -= $fee_to_pay;
        } else {
            // Nếu tiền còn lại không đủ trả hết (trả một phần)
            // Ghi nhận một phần thanh toán cho đơn này
            $stmt_insert->bind_param("isds", $shipper_id, $order_id, $payment_remaining, $note . " (trả một phần)");
            $stmt_insert->execute();
            $payment_remaining = 0;
        }
    }
    $stmt_insert->close();

    // 3. Nếu sau khi trả hết các đơn mà shipper vẫn còn "dư" tiền (overpaid)
    if ($payment_remaining > 0) {
        $stmt_overpay = $conn->prepare(
            "INSERT INTO transactions (UserID, OrderID, Type, Amount, Status, Note, Created_at) 
             VALUES (?, NULL, 'overpayment_cod', ?, 'completed', 'Tiền nộp thừa', NOW())"
        );
        $stmt_overpay->bind_param("id", $shipper_id, $payment_remaining);
        $stmt_overpay->execute();
        $stmt_overpay->close();
    }
    
    // 4. Hoàn tất, lưu tất cả thay đổi
    $conn->commit();
    echo json_encode([
        'success' => true, 
        'message' => 'Đã ghi nhận thanh toán thành công.'
    ]);

} catch (Exception $e) {
    // Nếu có lỗi, hủy bỏ mọi thay đổi
    $conn->rollback();
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

// $db->dongKetNoi($conn);
?>