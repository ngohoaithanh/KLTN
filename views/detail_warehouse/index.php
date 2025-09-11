<?php
$id = $_GET['id'] ?? null;
include_once('controllers/cWarehouse.php');
$p = new controlWarehouse();
if (!$id) {
    echo '<div class="alert alert-danger">Kho không hợp lệ.</div>';
    return;
}
$tblSP = $p->getWarehouseDetail($id); 
$wdt = [];

if (is_array($tblSP) && isset($tblSP[0])) {
    foreach ($tblSP as $row) {
        $wdt[] = [
            'id' => $row['ID'],
            'OrderID' => $row['OrderID'],
            'Handled_by' => $row['user_name'],
            'Timestamp' => $row['Timestamp'],
            'Note' => $row['Note'],
            'name' => $row['warehouse_name'],
            'Address' => $row['Address'],
            'operation_status' => $row['operation_status'],
            'action' => $row['ActionType'],
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

</head>
<body>   
    <div class="container custom-detail-warehouse-container mt-4">
        <?php if (!empty($wdt)): ?>
            <?php $info = $wdt[0]; ?>
            <h2>Thông tin kho: <strong><?= htmlspecialchars($info['name']) ?></strong></h2>

            <div class="row mt-3">
                <div class="col-md-6">
                    <p><strong>Địa chỉ:</strong> <?= htmlspecialchars($info['Address']) ?></p>
                    <p><strong>Trạng thái hoạt động:</strong> <?= htmlspecialchars($info['operation_status']) ?></p>
                </div>
            </div>
        <?php endif; ?>

        <!-- Luôn hiển thị nút thao tác, miễn là $id có giá trị hợp lệ -->
        <?php if ($id): ?>
            <div class="mt-3 mb-4">
                <a href="index.php?pendingImports&id=<?= $id ?>" class="btn btn-outline-info mr-2">
                    📦 Hàng chờ nhập kho
                </a>
                <a href="?exportedGoods&id=<?= $id ?>" class="btn btn-outline-success">
                    🚚 Hàng đã xuất kho
                </a>
            </div>
        <?php endif; ?>

        <h4 class="mt-4">Lịch sử nhập / xuất kho</h4>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover custom-detail-warehouse-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Mã đơn hàng</th>
                        <th>Người thực hiện</th>
                        <th>Thời gian</th>
                        <th>Ghi chú</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($wdt)): ?>
                        <?php foreach ($wdt as $w): ?>
                            <tr class="custom-table-row">
                                <td><?= $w['id'] ?></td>
                                <td><?= htmlspecialchars($w['OrderID']) ?></td>
                                <td><?= htmlspecialchars($w['Handled_by']) ?></td>
                                <td><?= htmlspecialchars($w['Timestamp']) ?></td>
                                <td><?= htmlspecialchars($w['action']) ?></td>
                                <td>
                                    <!-- Thêm tùy chỉnh quản lý kho, ví dụ như tên người quản lý -->
                                    <?php if (htmlspecialchars($w['action']) === "import"): ?>
                                        <a href="?exportSingleOrder&idOrder=<?= $w['OrderID'] ?>&warehouseID=<?= $id ?>" 
                                        class="btn btn-sm btn-primary custom-table-button me-2">Xuất kho</a>  
                                        <!-- <a href="index.php?controller=warehouse&action=deleteWarehouse&id=<?= $w['OrderID'] ?>"
                                        class="btn btn-sm btn-danger custom-table-button"
                                        onclick="return confirm('Bạn có chắc chắn muốn xóa kho này không?')">Xóa</a> -->
                                    <?php else: ?>
                                        <span class="text-success">Đã xuất kho</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">Chưa có dữ liệu nhập xuất cho kho này.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="text-center mb-3">
            <a href="javascript:history.back()" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>

</body>
</html>
