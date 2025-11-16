<?php
header('Content-Type: application/json; charset=utf-8');
include_once('../../config/database.php');
include_once('../../config/auth_check.php');
$db = new clsKetNoi();
$conn = $db->moKetNoi();
$response = [];

// Các trường bắt buộc
// Các trường bắt buộc
$requiredFields = [
    'CustomerName', 'Pick_up_address', 'Delivery_address', 
    'Recipient', 'RecipientPhone', 'Status', 
    'COD_amount', 'Weight', 'PhoneNumber',
    'Shippingfee'
];

// Kiểm tra các trường dữ liệu yêu cầu
$missingFields = [];
foreach ($requiredFields as $field) {
    if (!isset($_REQUEST[$field]) || trim($_REQUEST[$field]) === '') {
        $missingFields[] = $field;
    }
}

if (!empty($missingFields)) {
    echo json_encode([
        'success' => false,
        'error' => 'Thiếu dữ liệu: ' . implode(', ', $missingFields),
        'debug_request' => $_REQUEST
    ]);
    exit();
}
    
// Lấy thêm lat/lng từ request (nếu có)
$PickUpLat = isset($_REQUEST['Pick_up_lat']) ? floatval($_REQUEST['Pick_up_lat']) : null;
$PickUpLng = isset($_REQUEST['Pick_up_lng']) ? floatval($_REQUEST['Pick_up_lng']) : null;
$DeliveryLat = isset($_REQUEST['Delivery_lat']) ? floatval($_REQUEST['Delivery_lat']) : null;
$DeliveryLng = isset($_REQUEST['Delivery_lng']) ? floatval($_REQUEST['Delivery_lng']) : null;
// Lấy dữ liệu từ form
$CustomerName = trim($_REQUEST['CustomerName']);
$PickUpAddress = trim($_REQUEST['Pick_up_address']);
$DeliveryAddress = trim($_REQUEST['Delivery_address']);
$Recipient = trim($_REQUEST['Recipient']);
$RecipientPhone = trim($_REQUEST['RecipientPhone']);
$Status = trim($_REQUEST['Status']);
$CODAmount = floatval($_REQUEST['COD_amount']);
$Note = isset($_REQUEST['Note']) ? trim($_REQUEST['Note']) : '';
$Weight = floatval($_REQUEST['Weight']);
$ShippingFee = floatval($_REQUEST['Shippingfee']);
date_default_timezone_set('Asia/Ho_Chi_Minh');
$Create_at = date('Y-m-d H:i:s');
$CustomerID = 0;
$feePayer = isset($_REQUEST['fee_payer']) ? trim($_REQUEST['fee_payer']) : 'sender';

$PhoneNumber = isset($_REQUEST['PhoneNumber']) ? trim($_REQUEST['PhoneNumber']) : '';  // Lấy 'PhoneNumber' từ form


// Kiểm tra khách hàng
$sqlCheck = "SELECT ID FROM users WHERE Username = ? LIMIT 1";
$stmtCheck = $conn->prepare($sqlCheck);
$stmtCheck->bind_param("s", $CustomerName);
$stmtCheck->execute();
$stmtCheck->store_result();

if ($stmtCheck->num_rows > 0) {
    $stmtCheck->bind_result($CustomerID);
    $stmtCheck->fetch();
    $stmtCheck->close();
} else {
    $stmtCheck->close();
    $FakeEmail = 'guest_' . time() . '@fake.local';
    $FakePassword = password_hash('123456', PASSWORD_BCRYPT);
    $Role = 7;

    // Thêm customer mới với số điện thoại
    $stmtInsertCustomer = $conn->prepare("INSERT INTO users (Username, Email, Password, PhoneNumber, Role, Note) VALUES (?, ?, ?, ?, ?, '')");
    $stmtInsertCustomer->bind_param("ssssi", $CustomerName, $FakeEmail, $FakePassword, $PhoneNumber, $Role);

    if (!$stmtInsertCustomer->execute()) {
        echo json_encode(['success' => false, 'error' => 'Lỗi thêm khách hàng: ' . $stmtInsertCustomer->error]);
        exit();
    }
    $CustomerID = $stmtInsertCustomer->insert_id;
    $stmtInsertCustomer->close();
}

// Xác minh tồn tại CustomerID
$sqlVerify = "SELECT ID FROM users WHERE ID = ?";
$stmtVerify = $conn->prepare($sqlVerify);
$stmtVerify->bind_param("i", $CustomerID);
$stmtVerify->execute();
$stmtVerify->store_result();

if ($stmtVerify->num_rows == 0) {
    echo json_encode(['success' => false, 'error' => 'CustomerID không tồn tại sau khi xử lý']);
    exit();
}
$stmtVerify->close();

// Tính phí vận chuyển dựa trên khối lượng
// if ($Weight < 1) {
//     $ShippingFee = 15000;
// } elseif ($Weight <= 2) {
//     $ShippingFee = 18000;
// } else {
//     $extraWeight = $Weight - 2;
//     $extraFee = ceil($extraWeight * 2) * 2500;
//     $ShippingFee = 18000 + $extraFee;
// }

// ===== Tính phí COD 2% với min/max, nhưng nếu CODAmount = 0 thì bằng 0 =====
if ($CODAmount <= 0) {
    $CODFee = 0.0;
} else {
    $CODFee = $CODAmount * 0.01;
    if ($CODFee < 5000) $CODFee = 5000;
    if ($CODFee > 15000) $CODFee = 15000;
}

// $ID = (int) (date("md") . mt_rand(1000, 9999));

$ID = 0;
$maxTries = 5;
do {
    // Tạo ID ngẫu nhiên theo cách của bạn
    $potentialID = (int) (date("md") . mt_rand(1000, 9999));
    
    // Kiểm tra xem ID này đã tồn tại trong database chưa
    $stmtCheckID = $conn->prepare("SELECT ID FROM orders WHERE ID = ?");
    $stmtCheckID->bind_param("i", $potentialID);
    $stmtCheckID->execute();
    $stmtCheckID->store_result();
    
    if ($stmtCheckID->num_rows == 0) {
        // Nếu ID chưa tồn tại, chúng ta có thể sử dụng nó
        $ID = $potentialID;
    }
    $stmtCheckID->close();
    $maxTries--;
} while ($ID == 0 && $maxTries > 0);

if ($ID == 0) {
    // Nếu sau 5 lần vẫn không tạo được ID duy nhất (rất hiếm), báo lỗi
    echo json_encode(['success' => false, 'error' => 'Không thể tạo mã đơn hàng, vui lòng thử lại.']);
    exit();
}

$sqlInsertOrder = "INSERT INTO orders 
(ID, CustomerID, Pick_up_address, Pick_up_lat, Pick_up_lng, 
 Delivery_address, Delivery_lat, Delivery_lng, 
 Recipient, RecipientPhone, Status, COD_amount, Created_at, Note, ShippingFee, Weight, CODFee, fee_payer)
VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?, ?)";

$stmtOrder = $conn->prepare($sqlInsertOrder);
$stmtOrder->bind_param(
    "iisddsddsssdssddds",  
    $ID,
    $CustomerID,
    $PickUpAddress,
    $PickUpLat,
    $PickUpLng,
    $DeliveryAddress,
    $DeliveryLat,
    $DeliveryLng,
    $Recipient,
    $RecipientPhone,
    $Status,
    $CODAmount,
    $Create_at,
    $Note,
    $ShippingFee,
    $Weight,
    $CODFee,
    $feePayer
);


if (!$stmtOrder->execute()) {
    echo json_encode(['success' => false, 'error' => 'Lỗi thêm đơn hàng: ' . $stmtOrder->error]);
    exit();
}

// Lấy ID của đơn hàng vừa chèn
// $orderID = $stmtOrder->insert_id;

// // Cập nhật lại trường Created_at
// $sqlUpdateDate = "UPDATE orders SET Created_at = ? WHERE ID = ?";
// $stmtUpdateDate = $conn->prepare($sqlUpdateDate);
// $stmtUpdateDate->bind_param("si", $Create_at, $orderID);
// if (!$stmtUpdateDate->execute()) {
//     echo json_encode(['success' => false, 'error' => 'Lỗi cập nhật Created_at: ' . $stmtUpdateDate->error]);
//     exit();
// }
// $stmtUpdateDate->close();

//create tracking
$trackingStatus = 'Đơn hàng đã được tạo.';
$sqlInsertTracking = "INSERT INTO trackings (OrderID, Status, Updated_at) VALUES (?, ?, ?)";
$stmtTracking = $conn->prepare($sqlInsertTracking);
$stmtTracking->bind_param("iss", $ID, $trackingStatus, $Create_at); // Sử dụng $ID thay vì $orderID
if (!$stmtTracking->execute()) {
    // Nếu cần, bạn có thể ghi log lỗi ở đây
}
$stmtTracking->close();

echo json_encode([
    'success' => true,
    'message' => 'Thêm đơn hàng thành công'
]);

$stmtOrder->close();

$db->dongKetNoi($conn);
?>
