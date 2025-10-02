<?php
include_once('controllers/cOrder.php');
include_once('controllers/cTracking.php');
$trackingController = new controlTracking(); 
$p = new controlOrder();
date_default_timezone_set('Asia/Ho_Chi_Minh');
$orderDetail = null;
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];
    $orderDetail = $p->getOrderById($id); // Phương thức này bạn nên có sẵn trong controller
} else {
    echo "ID không hợp lệ!";
}
// var_dump($orderDetail);
// Xử lý logic khi nút được nhấn (bạn cần triển khai các hàm này trong controller)
if (isset($_POST['delivered'])) {
    $delivered = $p->setOrderStatus($_GET['id'], 'delivered');
    $trackingStatus = 'Đơn hàng đã giao thành công'; 
    $timestamp1_unix = time(); // Lấy timestamp Unix
    $timestamp1_formatted = date('Y-m-d H:i:s', $timestamp1_unix); // Định dạng lại
    $trackingResult = $trackingController->addTrackingTimeline($_GET['id'], $trackingStatus, $timestamp1_formatted);

    // create transaction
    if($orderDetail['COD_amount'] > 0){
        include_once('../../config/database.php');
        $db = new clsKetNoi();
        $conn = $db->moKetNoi();
        $typeTrans = 'collect_cod';
        $statusTrans = 'completed';
        $createdAt = null;

        $sqlInsertTrans = "INSERT INTO transactions (OrderID, UserID, Type, Amount, Status, Created_at) VALUES (?, ?, ?, ?, ?, ?)";
        $stmtTrans = $conn->prepare($sqlInsertTrans);
        $stmtTrans->bind_param("iisdss", $_GET['id'], $orderDetail['ShipperID'], $typeTrans, $orderDetail['COD_amount'], $statusTrans,$createdAt);
        if (!$stmtTrans->execute()) {
            echo json_encode(['success' => false, 'error' => 'Lỗi thêm Transaction: ' . $stmtTrans->error]);
            exit();
        }
        $stmtTrans->close();
        // $db->dongKetNoi($conn);
    }

    // update status for COD:
    $updateCODSql = "UPDATE cods SET Status = 'collected', Settled_at = ? WHERE OrderID = ?";
    $stmtUpdateCOD = $conn->prepare($updateCODSql);

    $settledAt = $timestamp1_formatted; // thời gian hiện tại giống với tracking timeline
    $stmtUpdateCOD->bind_param("si", $settledAt, $_GET['id']);

    if (!$stmtUpdateCOD->execute()) {
        echo json_encode(['success' => false, 'error' => 'Lỗi cập nhật trạng thái COD: ' . $stmtUpdateCOD->error]);
        exit();
    }

    $stmtUpdateCOD->close();   
    $db->dongKetNoi($conn);
    echo "<script>
    alert('Giao hàng thành công!');
    window.location.href = window.location.href;
</script>";
    
}elseif (isset($_POST['failed'])) {
    $delivery_failed = $p->setOrderStatus($_GET['id'], 'delivery_failed');
    $trackingStatus = 'Giao hàng thất bại, shipper sẽ liên lạc lại cho bạn'; 
    $timestamp1_unix = time(); // Lấy timestamp Unix
    $timestamp1_formatted = date('Y-m-d H:i:s', $timestamp1_unix); // Định dạng lại
    $trackingResult = $trackingController->addTrackingTimeline($_GET['id'], $trackingStatus, $timestamp1_formatted);
    echo "<script>
    alert('Giao hàng thất bại!');
    window.location.href = window.location.href;
</script>";
    
}elseif (isset($_POST['returned'])) {
    $returned = $p->setOrderStatus($_GET['id'], 'returned');
    $trackingStatus = 'Đơn hàng được hoàn trả'; 
    $timestamp1_unix = time(); // Lấy timestamp Unix
    $timestamp1_formatted = date('Y-m-d H:i:s', $timestamp1_unix); // Định dạng lại
    $trackingResult = $trackingController->addTrackingTimeline($_GET['id'], $trackingStatus, $timestamp1_formatted);
    echo "<script>
    alert('Hoàn trả đơn hàng!');
    window.location.href = window.location.href;
</script>";
    
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi Tiết Đơn Hàng</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f6f9;
        }
        .container {
            margin-top: 50px;
        }
        .title {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 30px;
            color: #333;
            text-align: center; /* Căn giữa tiêu đề */
        }
        .box {
            padding: 30px;
            border-radius: 12px;
            background-color: #ffffff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        .label {
            font-weight: bold;
            color: #555;
        }
        .row p {
            font-size: 16px;
            margin-bottom: 12px;
        }
        .badge-info {
            background-color: #17a2b8;
        }
        .button-group {
            margin-top: 20px;
            text-align: center; /* Căn giữa các nút */
        }
        .button-group button {
            margin: 0 10px;
        }
        .btn-success {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .btn-danger {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .btn-warning {
            background-color: #ffc107;
            color: #212529;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .back-btn {
            display: block;
            padding: 10px 20px;
            background-color:rgb(0, 136, 255);
            color: white;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s ease;
            width: fit-content;
            margin: 20px auto 0; /* Căn giữa và tạo khoảng cách phía trên */
        }
        .back-btn:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
<div class="container">

    <h3 class="title">Chi Tiết Đơn Hàng #<?= htmlspecialchars($_GET['id']) ?></h3>

    <?php if (!empty($orderDetail)): ?>
        <div class="box">
            <div class="row">
                <div class="col-md-6">
                    <p><span class="label">Người gửi:</span> <?= htmlspecialchars($orderDetail['FullName']) ?></p>
                    <p><span class="label">SĐT người gửi:</span> <?= htmlspecialchars($orderDetail['PhoneNumber'] ?? 'N/A') ?></p>
                    <p><span class="label">Địa chỉ lấy hàng:</span> <?= htmlspecialchars($orderDetail['Pick_up_address']) ?></p>
                    <p><span class="label">Ngày tạo:</span> <?= htmlspecialchars($orderDetail['Created_at']) ?></p>
                    <p><span class="label">Ghi chú:</span> <?= htmlspecialchars($orderDetail['Note'] ?? 'Không có ghi chú') ?></p>
                </div>
                <div class="col-md-6">
                    <p><span class="label">Người nhận:</span> <?= htmlspecialchars($orderDetail['Recipient']) ?></p>
                    <p><span class="label">SĐT người nhận:</span> <?= htmlspecialchars($orderDetail['RecipientPhone']) ?></p>
                    <p><span class="label">Địa chỉ giao hàng:</span> <?= htmlspecialchars($orderDetail['Delivery_address']) ?></p>
                    <p><span class="label">Shipper:</span> <?= htmlspecialchars($orderDetail['ShipperName'] ?? 'Chưa phân công') ?></p>
                </div>
            </div>
            <hr>
            <div class="row mt-3">
                <div class="col-md-4">
                    <p><span class="label">Trạng thái:</span>
                        <span class="badge badge-info"><?= htmlspecialchars($orderDetail['Status']?? 'N/A') ?></span>
                    </p>
                </div>
                <div class="col-md-4">
                    <p><span class="label">Khối lượng:</span> <?= htmlspecialchars($orderDetail['Weight']) ?> kg</p>
                </div>
                <div class="col-md-4">
                    <p><span class="label">COD:</span> <?= number_format($orderDetail['COD_amount'], 0, ',', '.') ?> VNĐ</p>
                    <p><span class="label">Phí vận chuyển:</span> <?= number_format($orderDetail['ShippingFee'], 0, ',', '.') ?> VNĐ</p>
                    <p><span class="label">Tổng:</span> <?= number_format($orderDetail['ShippingFee'] + $orderDetail['COD_amount'], 0, ',', '.') ?> VNĐ</p>
                </div>
            </div>
        </div>
        
        <?php if ($_SESSION['role'] == 6): ?>
        <div class="button-group">
            <form method="post">
                <button type="submit" class="btn btn-success" name="delivered">Giao hàng thành công</button>
                <button type="submit" class="btn btn-danger" name="failed">Giao hàng thất bại</button>
                <button type="submit" class="btn btn-warning" name="returned">Hoàn trả hàng</button>
            </form>
        </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="alert alert-danger">
            Không tìm thấy thông tin đơn hàng!
        </div>
    <?php endif; ?>

    <a href="javascript:history.back()" class="back-btn">← Quay lại</a>
</div>
</body>
</html>