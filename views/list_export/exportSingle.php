<?php
// session_start();

    include_once('controllers/cWarehouse.php');

    include_once('config/database.php');
    $db = new clsKetNoi();
    $conn = $db->moKetNoi();

    $orderID = $_REQUEST['idOrder'];
    $handled_by = $_SESSION['user_id'];//$_SESSION['user_id']
    $warehouseID = $_REQUEST['warehouseID'];
    date_default_timezone_set('Asia/Ho_Chi_Minh');
    $timestamp = date('Y-m-d H:i:s'); // Đặt thời gian hiện tại
    
    // 1. Lấy số lượng đơn hiện tại trong kho
    $warehouseCtrl = new controlWarehouse();
    $currentQuantity = $warehouseCtrl->getQuantity($warehouseID)->fetch_assoc()['total_orders'];

    // Sử dụng prepared statement để tránh SQL injection
    $sql = "INSERT INTO order_warehouse_tracking (OrderID, WarehouseID, Handled_by, ActionType, Timestamp) 
            VALUES (?, ?, ?, 'export', ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiis", $orderID, $warehouseID, $_SESSION['user_id'], $timestamp);
    $stmt->execute();
    $db->dongKetNoi($conn);

    include_once('controllers/cTracking.php');
    $trackingController = new controlTracking(); 
    $trackingStatus = 'Đơn hàng đã được xuất kho và đang được sắp xếp shipper giao hàng'; 
    $timestamp1_unix = time(); // Lấy timestamp Unix
    $timestamp1_formatted = date('Y-m-d H:i:s', $timestamp1_unix); // Định dạng lại
    $trackingResult = $trackingController->addTrackingTimeline($orderID, $trackingStatus, $timestamp1_formatted);


    include_once('controllers/cUser.php');
    $tblData = new controlNguoiDung(); 
    $shipperID = $tblData->getRandomShipperByWarehouseID($warehouseID);

    if ($shipperID) {
        include_once('controllers/cOrder.php');
        $sp = new controlOrder(); 
        $spResult = $sp->setShipper($shipperID, $orderID);

        include_once('controllers/cTracking.php');
        $trackingStatus2 = 'Đơn hàng đang trên đường giao, vui lòng chú ý điện thoại'; 
        $timestamp2_unix = $timestamp1_unix +60; // Thêm 60 giây vào timestamp Unix
        $timestamp2_formatted = date('Y-m-d H:i:s', $timestamp2_unix); // Định dạng lại
        $trackingResult2 = $trackingController->addTrackingTimeline($orderID, $trackingStatus2, $timestamp2_formatted);
    } else {
        echo "Không có shipper nào trong kho này";
    }

        
    $p = new controlWarehouse();
    $result = $p->exportSingleOrder($orderID);
    if ($result) {
        // 6. Cập nhật số lượng mới (giảm đi 1)
        $newQuantity = $currentQuantity - 1;
        $updateResult = $warehouseCtrl->setQuantity($warehouseID, $newQuantity);
        
        if ($updateResult) {
            echo "<script>alert('Đơn hàng đã được xuất kho! Số lượng hiện tại: $newQuantity'); 
                window.location.href='?detailWarehouse&&id=$warehouseID';</script>";
        } else {
            echo "<script>alert('Đơn hàng đã xuất kho nhưng cập nhật số lượng thất bại!'); 
                window.location.href='?detailWarehouse&&id=$warehouseID';</script>";
        }
        // echo "<script>alert('Đơn hàng đã được xuất kho!'); window.location.href='?detailWarehouse&&id=$warehouseID';</script>";
    } else {
        echo "<script>alert('Có lỗi xảy ra!');</script>";
    }
?>