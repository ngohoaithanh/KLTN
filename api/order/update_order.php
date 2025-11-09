<?php
header('Content-Type: application/json; charset=utf-8');
include_once('../../config/database.php');

$db = new clsKetNoi();
$conn = $db->moKetNoi();

if (
    isset($_POST['id'], $_POST['CustomerID'], $_POST['FullName'], $_POST['PhoneNumber'],
        $_POST['Pick_up_address'], $_POST['Delivery_address'], $_POST['Recipient'],
        $_POST['RecipientPhone'], $_POST['Weight'], $_POST['Status'],
        $_POST['COD_amount'])
    && $_POST['Weight'] !== '' && $_POST['COD_amount'] !== '' && $_POST['Status'] !== ''
) {
    // Lấy dữ liệu từ form
    $id = intval($_POST['id']);
    $customerID = intval($_POST['CustomerID']);
    $fullName = trim($_POST['FullName']);
    $phoneNumber = trim($_POST['PhoneNumber']);
    $pickup = trim($_POST['Pick_up_address']);
    $delivery = trim($_POST['Delivery_address']);
    $recipient = trim($_POST['Recipient']);
    $recipientPhone = trim($_POST['RecipientPhone']);
    
    // Kiểm tra giá trị trọng lượng (Weight)
    if (!isset($_POST['Weight']) || empty($_POST['Weight'])) {
        echo json_encode(['success' => false, 'error' => 'Thiếu trường: Weight']);
        exit;
    }
    $weight = floatval($_POST['Weight']); // Lấy trọng lượng từ POST
    
    if ($weight <= 0) {
        echo json_encode(['success' => false, 'error' => 'Trọng lượng phải lớn hơn 0']);
        exit;
    }

    $status = trim($_POST['Status']);
    $cod = floatval($_POST['COD_amount']);
    $note = isset($_POST['Note']) ? trim($_POST['Note']) : '';

    // Tính toán ShippingFee dựa trên trọng lượng
    if ($weight <= 1) {
        $shippingFee = 15000;
    } elseif ($weight <= 2) {
        $shippingFee = 18000;
    } else {
        $extraWeight = $weight - 2;
        $extraFee = ceil($extraWeight * 2) * 2500;
        $shippingFee = 18000 + $extraFee;
    }

    // Kiểm tra các trạng thái hợp lệ
    $allowed_statuses = ['pending','accepted','picked_up','in_transit','delivered','delivery_failed','cancelled'];
    if (!in_array($status, $allowed_statuses)) {
        echo json_encode(['success' => false, 'error' => 'Trạng thái không hợp lệ']);
        exit;
    }

    // Thực hiện cập nhật dữ liệu
    $conn->begin_transaction();

    try {
        // Cập nhật đơn hàng
        $sql = "UPDATE orders SET 
                    Pick_up_address = ?, 
                    Delivery_address = ?, 
                    Recipient = ?, 
                    RecipientPhone = ?, 
                    Weight = ?, 
                    ShippingFee = ?, 
                    Status = ?, 
                    COD_amount = ?, 
                    Note = ? 
                WHERE ID = ?";
        $stmt = $conn->prepare($sql);
       $stmt->bind_param(
    'ssssddsdsi', // Kiểu dữ liệu
    $pickup,      // s
    $delivery,    // s
    $recipient,   // s
    $recipientPhone, // s
    $weight,      // d
    $shippingFee, // d
    $status,      // s
    $cod,         // d
    $note,        // s
    $id           // i
);


        if (!$stmt->execute()) {
            throw new Exception("Lỗi khi cập nhật đơn hàng: " . $stmt->error);
        }
        $stmt->close();

        // Cập nhật thông tin người dùng
        $sql_user = "UPDATE users SET Username = ?, PhoneNumber = ? WHERE ID = ?";
        $stmt_user = $conn->prepare($sql_user);
        $stmt_user->bind_param("ssi", $fullName, $phoneNumber, $customerID);

        if (!$stmt_user->execute()) {
            throw new Exception("Lỗi khi cập nhật thông tin khách hàng: " . $stmt_user->error);
        }

        $stmt_user->close();
        $conn->commit();

        echo json_encode(['success' => true]);

    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }

} else {
    // Kiểm tra các trường bắt buộc
    $missing = [];
    $required = ['id', 'CustomerID', 'FullName', 'PhoneNumber', 'Pick_up_address', 'Delivery_address', 'Recipient',
                 'RecipientPhone', 'Weight', 'Status', 'COD_amount'];

    foreach ($required as $field) {
        if (!isset($_POST[$field]) || trim($_POST[$field]) === '') {
            $missing[] = $field;
        }
    }

    echo json_encode([
        'success' => false,
        'error' => 'Thiếu trường: ' . implode(', ', $missing)
    ]);
}

$db->dongKetNoi($conn);
?>
