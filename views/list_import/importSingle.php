<?php
// session_start();

    include_once('controllers/cWarehouse.php');

    include_once('config/database.php');
    $db = new clsKetNoi();
    $conn = $db->moKetNoi();

    $orderID = $_REQUEST['idOrder'];
    $handled_by = 1;//$_SESSION['user_id']
    $warehouseID = $_REQUEST['warehouseID'];
    date_default_timezone_set('Asia/Ho_Chi_Minh');
    $timestamp = date('Y-m-d H:i:s'); // Đặt thời gian hiện tại

    // 1. Lấy số lượng đơn hiện tại trong kho
    $warehouseCtrl = new controlWarehouse();
    $currentQuantity = $warehouseCtrl->getQuantity($warehouseID)->fetch_assoc()['total_orders'];
    // Sử dụng prepared statement để tránh SQL injection
    $sql = "INSERT INTO order_warehouse_tracking (OrderID, WarehouseID, Handled_by, ActionType, Timestamp) 
            VALUES (?, ?, ?, 'import', ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiis", $orderID, $warehouseID, $_SESSION['user_id'], $timestamp);
    $stmt->execute();
    $db->dongKetNoi($conn);

    include_once('controllers/cTracking.php');
    $trackingController = new controlTracking(); 
    // $trackingStatus = 'Đơn hàng đã được tiếp nhận'; 
    // $trackingResult = $trackingController->addTrackingTimeline($orderID, $trackingStatus, $timestamp);
    // $trackingStatus2 = 'Đơn hàng đã được nhập kho'; 
    // $trackingResult = $trackingController->addTrackingTimeline($orderID, $trackingStatus2, $timestamp);
    // Lần thêm tracking status thứ nhất
    $trackingStatus1 = 'Đơn hàng đã được tiếp nhận';
    $timestamp1_unix = time(); // Lấy timestamp Unix
    $timestamp1_formatted = date('Y-m-d H:i:s', $timestamp1_unix); // Định dạng lại
    $trackingResult1 = $trackingController->addTrackingTimeline($orderID, $trackingStatus1, $timestamp1_formatted);

    // Lần thêm tracking status thứ hai
    $trackingStatus2 = 'Đơn hàng đã được nhập kho';
    $timestamp2_unix = $timestamp1_unix +5; // Thêm 60 giây vào timestamp Unix
    $timestamp2_formatted = date('Y-m-d H:i:s', $timestamp2_unix); // Định dạng lại
    $trackingResult2 = $trackingController->addTrackingTimeline($orderID, $trackingStatus2, $timestamp2_formatted);

    
    $p = new controlWarehouse();
    $result = $p->importSingleOrder($orderID);
    if ($result) {
        // 5. Cập nhật số lượng mới (tăng lên 1)
        $newQuantity = $currentQuantity + 1;
        $updateResult = $warehouseCtrl->setQuantity($warehouseID, $newQuantity);
        // echo "<script>alert('Đơn hàng đã được nhập kho!'); window.location.href='?pendingImports&&id=$warehouseID';</script>";
        if ($updateResult) {
            echo "<script>alert('Đơn hàng đã được nhập kho! Số lượng hiện tại: $newQuantity'); 
                window.location.href='?pendingImports&&id=$warehouseID';</script>";
        } else {
            echo "<script>alert('Đơn hàng đã nhập kho nhưng cập nhật số lượng thất bại!'); 
                window.location.href='?pendingImports&&id=$warehouseID';</script>";
        }
    } else {
        echo "<script>alert('Có lỗi xảy ra!');</script>";
    }
?>