<?php
header('Content-Type: application/json; charset=utf-8');
include_once("../../config/database.php");
$db = new clsKetNoi(); $conn = $db->moKetNoi();

$data = json_decode(file_get_contents('php://input'), true);
$response = ['success' => false];

try {
    $name = $data['Name'];
    $type = $data['VehicleType'];
    $baseDist = floatval($data['BaseDistance']);
    $basePrice = floatval($data['BasePrice']);
    $priceKm = floatval($data['PricePerKm']);
    $freeWeight = floatval($data['FreeWeight']);
    $priceKg = floatval($data['PricePerKg']);
    $active = intval($data['IsActive']);

    if (isset($data['ID']) && $data['ID'] > 0) {
        // UPDATE
        $stmt = $conn->prepare("UPDATE pricing_rules SET Name=?, VehicleType=?, BaseDistance=?, BasePrice=?, PricePerKm=?, FreeWeight=?, PricePerKg=?, IsActive=? WHERE ID=?");
        $stmt->bind_param("ssdddddii", $name, $type, $baseDist, $basePrice, $priceKm, $freeWeight, $priceKg, $active, $data['ID']);
    } else {
        // INSERT
        $stmt = $conn->prepare("INSERT INTO pricing_rules (Name, VehicleType, BaseDistance, BasePrice, PricePerKm, FreeWeight, PricePerKg, IsActive) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdddddi", $name, $type, $baseDist, $basePrice, $priceKm, $freeWeight, $priceKg, $active);
    }

    if ($stmt->execute()) {
        // === GHI LOG ===
        if (session_status() === PHP_SESSION_NONE) session_start();
        $admin_id = $_SESSION['user_id'] ?? 0;
        
        include_once("../../controllers/cLog.php");
        
        // Xác định là Thêm mới hay Cập nhật
        $is_update = (isset($data['ID']) && $data['ID'] > 0);
        $action = $is_update ? 'UPDATE_PRICING' : 'CREATE_PRICING';
        $target_id = $is_update ? $data['ID'] : $conn->insert_id;
        
        $desc = "$action: $name (Giá cơ bản: $basePrice)";
        controlLog::record($admin_id, $action, 'pricing_rules', $target_id, $desc);
        // ==============
        
        $response['success'] = true;
    } else {
        $response['error'] = $stmt->error;
    }
    $stmt->close();
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

echo json_encode($response);
$db->dongKetNoi($conn);
?>