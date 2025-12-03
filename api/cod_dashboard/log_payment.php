<?php
// FILE: api/cod_dashboard/log_payment.php (PHIÊN BẢN HOÀN CHỈNH VỚI BẢNG RECEIPTS)

// 1. BẮT ĐẦU GOM OUTPUT (Để tránh lỗi JSON do warning/notice)
ob_start();
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json; charset=utf-8');
include_once("../../config/database.php");
include_once('../../controllers/cNotification.php');

$db = new clsKetNoi();
$conn = $db->moKetNoi();

// Đọc dữ liệu JSON
$input_data = file_get_contents('php://input');
$data = json_decode($input_data, true);

$response = [];
$http_code = 200;

try {
    // Validate dữ liệu đầu vào
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($data['shipper_id']) || !isset($data['amount'])) {
        throw new Exception('Dữ liệu không hợp lệ hoặc thiếu tham số.', 400);
    }

    $shipper_id = intval($data['shipper_id']);
    $payment_amount = floatval($data['amount']);
    $note = isset($data['note']) ? trim($data['note']) : '';
    $proof_image = isset($data['proof_image']) ? trim($data['proof_image']) : null;

    if ($payment_amount <= 0) {
        throw new Exception('Số tiền phải lớn hơn 0.', 400);
    }

    // Bắt đầu Transaction
    $conn->begin_transaction();

    // --- BƯỚC 1: TẠO PHIẾU THU (RECEIPT) ---
    // Mã phiếu thu: PT + YmdHis + ShipperID (Ví dụ: PT20231027103001_141)
    $receipt_code = "PT" . date("YmdHis") . "_" . $shipper_id;
    
    // Lưu ProofImage vào bảng Receipts
    $sql_receipt = "INSERT INTO receipts (Code, ShipperID, TotalAmount, ProofImage, Note, Created_at) 
                    VALUES (?, ?, ?, ?, ?, NOW())";
    
    $stmt_receipt = $conn->prepare($sql_receipt);
    if (!$stmt_receipt) throw new Exception("Lỗi tạo Receipt: " . $conn->error);
    
    $stmt_receipt->bind_param("sidss", $receipt_code, $shipper_id, $payment_amount, $proof_image, $note);
    
    if (!$stmt_receipt->execute()) throw new Exception("Lỗi thực thi Receipt: " . $stmt_receipt->error);
    
    // Lấy ID của phiếu thu vừa tạo
    $receipt_id = $stmt_receipt->insert_id;
    $stmt_receipt->close();


    // --- BƯỚC 2: TÌM ĐƠN HÀNG NỢ (FIFO) ---
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


    // --- BƯỚC 3: TẠO GIAO DỊCH CHI TIẾT (TRANSACTIONS) ---
    $payment_remaining = $payment_amount;

    // Cập nhật câu INSERT: Thêm ReceiptID, bỏ ProofImage (vì đã lưu ở Receipt)
    $sql_insert = "INSERT INTO transactions (UserID, OrderID, Type, Amount, Status, Note, ReceiptID, Created_at) 
                   VALUES (?, ?, 'deposit_cod', ?, 'completed', ?, ?, NOW())";
    $stmt_insert = $conn->prepare($sql_insert);
    if (!$stmt_insert) throw new Exception("Lỗi chuẩn bị Insert Transaction: " . $conn->error);

    while ($order = $unpaid_orders->fetch_assoc()) {
        if ($payment_remaining <= 0) break;

        $order_id = $order['ID'];
        $fee_to_pay = floatval($order['CODFee']);
        $current_note = "Thanh toán theo phiếu thu #" . $receipt_code; // Ghi chú tham chiếu
        $current_amount = 0;

        if ($payment_remaining >= $fee_to_pay) {
            $current_amount = $fee_to_pay;
            $payment_remaining -= $fee_to_pay;
        } else {
            $current_amount = $payment_remaining;
            $current_note .= " (trả một phần)";
            $payment_remaining = 0;
        }

        $stmt_insert->bind_param("isdsi", $shipper_id, $order_id, $current_amount, $current_note, $receipt_id);
        if (!$stmt_insert->execute()) throw new Exception("Lỗi thực thi Transaction: " . $stmt_insert->error);
    }
    $stmt_insert->close();

    // --- BƯỚC 4: XỬ LÝ NỘP THỪA (OVERPAYMENT) ---
    if ($payment_remaining > 0) {
        $sql_overpay = "INSERT INTO transactions (UserID, OrderID, Type, Amount, Status, Note, ReceiptID, Created_at) 
                        VALUES (?, NULL, 'overpayment_cod', ?, 'completed', 'Tiền nộp thừa', ?, NOW())";
        $stmt_overpay = $conn->prepare($sql_overpay);
        $stmt_overpay->bind_param("idi", $shipper_id, $payment_remaining, $receipt_id);
        if (!$stmt_overpay->execute()) throw new Exception("Lỗi thực thi Overpay: " . $stmt_overpay->error);
        $stmt_overpay->close();
    }

    $msg = "Kế toán đã xác nhận khoản nộp " . number_format($payment_amount) . "đ. Ghi chú: $note";
    controlNotification::add($shipper_id, "Thanh toán công nợ thành công", $msg, "system", $receipt_id);

    $conn->commit();
    $response = ['success' => true, 'message' => 'Đã ghi nhận phiếu thu thành công.'];

} catch (Exception $e) {
    $conn->rollback();
    $http_code = $e->getCode() ? $e->getCode() : 500;
    if ($http_code < 100 || $http_code > 599) $http_code = 500;
    $response = ['success' => false, 'error' => $e->getMessage()];
}

$db->dongKetNoi($conn);

// XÓA SẠCH BỘ ĐỆM TRƯỚC KHI TRẢ VỀ JSON
ob_end_clean();

http_response_code($http_code);
header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
?>