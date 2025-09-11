<?php
header('Content-Type: application/json; charset=utf-8');
include_once('../../config/database.php');

$db = new clsKetNoi();
$conn = $db->moKetNoi();

if (isset($_GET['order_id']) && is_numeric($_GET['order_id'])) {
    $id = intval($_GET['order_id']);
    
    // Sử dụng prepared statement để tránh SQL injection
    $query = "SELECT * FROM trackings WHERE OrderID = ? ORDER BY Updated_at DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $trackings = [];
        while ($row = $result->fetch_assoc()) {
            $trackings[] = $row;
        }
        echo json_encode([
            'success' => true,
            'data' => $trackings
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Không tìm thấy lịch sử tracking']);
    }
    
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Thiếu ID hợp lệ']);
}

$db->dongKetNoi($conn);
?>