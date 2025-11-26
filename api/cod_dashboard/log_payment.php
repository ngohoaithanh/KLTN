<?php
// FILE: api/cod_dashboard/log_payment.php (Đã thêm ProofImage)
header('Content-Type: application/json; charset=utf-8');
include_once("../../config/database.php");

$db = new clsKetNoi();
$conn = $db->moKetNoi();

$data = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($data['shipper_id']) || !isset($data['amount'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Dữ liệu không hợp lệ.']);
    exit;
}

$shipper_id = intval($data['shipper_id']);
$payment_amount = floatval($data['amount']);
$note = isset($data['note']) ? trim($data['note']) : '';
// Nhận URL ảnh (nếu có)
$proof_image = isset($data['proof_image']) ? trim($data['proof_image']) : null;

if ($payment_amount <= 0) {
     http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Số tiền phải lớn hơn 0.']);
    exit;
}

$conn->begin_transaction();

try {
    // 1. Tìm đơn chưa thanh toán (Giữ nguyên logic cũ)
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

    // Cập nhật câu INSERT để thêm ProofImage
    $stmt_insert = $conn->prepare(
        "INSERT INTO transactions (UserID, OrderID, Type, Amount, Status, Note, ProofImage, Created_at) 
         VALUES (?, ?, 'deposit_cod', ?, 'completed', ?, ?, NOW())"
    );

    while ($order = $unpaid_orders->fetch_assoc()) {
        if ($payment_remaining <= 0) break;

        $order_id = $order['ID'];
        $fee_to_pay = floatval($order['CODFee']);

        if ($payment_remaining >= $fee_to_pay) {
            $stmt_insert->bind_param("isdss", $shipper_id, $order_id, $fee_to_pay, $note, $proof_image);
            $stmt_insert->execute();
            $payment_remaining -= $fee_to_pay;
        } else {
            $note_partial = $note . " (trả một phần)";
            $stmt_insert->bind_param("isdss", $shipper_id, $order_id, $payment_remaining, $note_partial, $proof_image);
            $stmt_insert->execute();
            $payment_remaining = 0;
        }
    }
    $stmt_insert->close();

    // Xử lý nộp thừa (Overpayment)
    if ($payment_remaining > 0) {
        $stmt_overpay = $conn->prepare(
            "INSERT INTO transactions (UserID, OrderID, Type, Amount, Status, Note, ProofImage, Created_at) 
             VALUES (?, NULL, 'overpayment_cod', ?, 'completed', 'Tiền nộp thừa', ?, NOW())"
        );
        $stmt_overpay->bind_param("ids", $shipper_id, $payment_remaining, $proof_image);
        $stmt_overpay->execute();
        $stmt_overpay->close();
    }
    
    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Đã ghi nhận thanh toán thành công.']);

} catch (Exception $e) {
    $conn->rollback();
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

$db->dongKetNoi($conn);
?>