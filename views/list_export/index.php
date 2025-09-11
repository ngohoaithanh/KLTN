<?php
include_once('controllers/cOrder.php');
$p = new controlOrder();

$warehouseID = $_GET['id'] ?? null;
if (!$warehouseID) {
    echo '<div class="alert alert-danger">Thiếu mã kho hàng (warehouseID).</div>';
    return;
}
$data = $p->getExportOrders($warehouseID);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh sách hàng chờ nhập kho</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS & Font Awesome -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>
        .custom-pending-imports-container {
            margin-top: 30px;
        }
        .custom-pending-imports-title {
            margin-bottom: 20px;
            font-weight: bold;
            color: #2c3e50;
        }
        .custom-pending-imports-table th,
        .custom-pending-imports-table td {
            vertical-align: middle;
        }
        .custom-pending-imports-empty {
            font-style: italic;
            color: #888;
        }
    </style>


</head>
<body>
    <div class="container custom-pending-imports-container">
        <h2 class="custom-pending-imports-title">Danh sách đơn đã hàng xuất kho</h2>
        <?php if (is_array($data) && isset($data[0])): ?>
            
        <div class="table-responsive">
            <!-- <a href="?importList&id=<?= $warehouseID ?>" class="btn btn-lg btn-success shadow-sm custom-import-all-btn">
                <i class="fas fa-boxes"></i> Nhập kho tất cả đơn hàng
            </a> -->


            <table class="table table-bordered table-hover table-striped custom-pending-imports-table">
                <thead >
                    <tr>
                        <th>ID</th>
                        <th>Người gửi</th>
                        <th>Người nhận</th>
                        <th>Địa chỉ giao</th>
                        <th>Ngày tạo đơn</th>
                        <th>Trạng thái</th>
                        <th>Shipper</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data as $order): ?>
                        <tr>
                            <td><?= htmlspecialchars($order['ID']) ?></td>
                            <td><?= htmlspecialchars($order['CustomerName']) ?></td>
                            <td><?= htmlspecialchars($order['Recipient']) ?></td>
                            <td><?= htmlspecialchars($order['Delivery_address']) ?></td>
                            <td><?= htmlspecialchars($order['Created_at']) ?></td>
                            <td><span class="badge badge-warning">Đã xuất kho</span></td>
                            <td><?= htmlspecialchars($order['ShipperName']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
            <p class="custom-pending-imports-empty">Không có đơn hàng được xuất kho.</p>
        <?php endif; ?>
        <div class="text-center mb-3">
            <a href="javascript:history.back()" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>
</body>
</html>
