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