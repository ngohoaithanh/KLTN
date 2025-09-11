<?php
if (!isset($_SESSION["dangnhap"]) || ($_SESSION["role"] != 4 && $_SESSION["role"] !=1 && $_SESSION["role"] !=2)) {
    echo "<script>alert('Bạn không có quyền truy cập!');</script>";
    header("refresh:0; url=index.php");
    exit();
}
include_once('controllers/cWarehouse.php');
// Khởi tạo đối tượng và lấy dữ liệu
$p = new controlWarehouse();
if (isset($_REQUEST['submit'])) {
    $operation_status = $_REQUEST['operation_status'] ?? '';
    $search = $_REQUEST['search'] ?? '';
    $tblSP = $p->getFilteredWarehouse($operation_status, $search);
} else {
    $tblSP = $p->getAllWarehouse(); // Nếu không thì lấy tất cả
}

$warehouses = [];
if ($tblSP) {
    foreach ($tblSP as $row) {
        $statusInfo = $p->getWarehouseStatus($row['ID']);
        $warehouses[] = [
            'id' => $row['ID']?? '',
            'name' => $row['Name']?? '',
            'address' => $row['Address']?? '',
            'quantity' => $row['Quantity']?? '',
            'capacity' => $row['Capacity']?? '',
            'operation_status' => $row['operation_status']?? '',
            'created_at' => $row['created_at']?? '',
            'manager_username' => $row['manager_username']?? '',
            'status_text' => $statusInfo['status_text'] ?? 'N/A',
            'status_class' => $statusInfo['badge_class'] ?? 'bg-secondary'
        ];
    }
}

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Kho Lưu Hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .badge.bg-warning {
            color: #000;
        }
        .d-block {
            display: block;
        }
    </style>
</head>
<body>
<div class="container mt-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary"> Quản lý Kho Lưu Hàng</h2>
        <a href="?add_warehouse" class="btn btn-success">+ Thêm kho mới</a>
    </div>

    <!-- Form lọc -->
    <form method="POST" action="#" id="filterForm" class="row g-3 mb-4">
        <input type="hidden" name="controller" value="warehouse">
        <input type="hidden" name="action" value="index">

        <div class="col-md-4">
            <label for="search" class="form-label">Tìm kiếm kho</label>

            <input type="text" name="search" id="search" class="form-control"
                placeholder="Nhập tên hoặc địa chỉ kho"
                value="<?= htmlspecialchars($_POST['search'] ?? '') ?>">
        </div>

        <div class="col-md-4">
            <label for="operation_status" class="form-label">Trạng thái hoạt động kho</label>
            <select name="operation_status" id="operation_status" class="form-select">
                <option value="">-- Tất cả --</option>
                <option value="active" <?= ($_POST['operation_status'] ?? '') == 'active' ? 'selected' : '' ?>>Đang hoạt động</option>
                <option value="paused" <?= ($_POST['operation_status'] ?? '') == 'paused' ? 'selected' : '' ?>>Tạm ngưng</option>
                <option value="full" <?= ($_POST['operation_status'] ?? '') == 'full' ? 'selected' : '' ?>>Đầy kho</option>
            </select>
        </div>

        <div class="col-md-4 d-flex align-items-end">
            <button type="submit" name="submit" class="btn btn-primary w-100">🔍 Lọc</button>
        </div>
    </form>

    <!-- Bảng danh sách kho -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Tên kho</th>
                    <th>Địa chỉ</th>
                    <th>Tình trạng kho</th> <!-- Cột mới thêm -->
                    <th>Trạng thái kho</th>
                    <th>Quản lý</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($warehouses)): ?>
                    <?php foreach ($warehouses as $w): ?>
                        <tr>
                            <td><?= $w['id'] ?></td>
                            <td><a href="?detailWarehouse&id=<?= $w['id'] ?>" style="text-decoration: none"><?= htmlspecialchars($w['name']) ?></a></td>
                            <td><?= htmlspecialchars($w['address']) ?></td>
                            <td>
                                <span class="badge <?= $w['status_class'] ?>">
                                    <?= $w['status_text'] ?>
                                </span>
                                <small class="text-muted d-block">
                                    <?= $w['quantity'] ?>/<?= $w['capacity'] ?>
                                </small>
                            </td>
                            <td>
                                <span class="badge bg-<?= $w['operation_status'] === 'active' ? 'success' : 'secondary' ?>">
                                    <?= htmlspecialchars($w['operation_status']) ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($w['manager_username']) ?></td>
                            <td>
                                <a href="update_warehouse&id=<?= $w['id'] ?>" class="btn btn-sm btn-primary me-2">Cập nhật</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted">Không có kho nào phù hợp với bộ lọc.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</body>
<script>
    // Danh sách kho từ PHP đẩy vào JavaScript
    const warehouseList = <?= json_encode($warehouses); ?>;

    const searchInput = document.getElementById('search');
    const statusSelect = document.getElementById('operation_status');
    const tableBody = document.querySelector('tbody');

    // Hàm render bảng kho
    function renderTable(data) {
        tableBody.innerHTML = '';
        if (data.length === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center text-muted">Không có kho nào phù hợp với bộ lọc.</td>
                </tr>
            `;
            return;
        }

        data.forEach(w => {
            const statusBadge = w.operation_status === 'active' ? 'success' : 'secondary';

            tableBody.innerHTML += `
                <tr>
                    <td>${w.id}</td>
                    <td><a href="?detailWarehouse&id=${w.id}" style="text-decoration: none">${w.name}</a></td>
                    <td>${w.address}</td>
                    <td>
                        <span class="badge ${w.status_class}">${w.status_text}</span>
                        <small class="text-muted d-block">${w.quantity}/${w.capacity}</small>
                    </td>
                    <td><span class="badge bg-${statusBadge}">${w.operation_status}</span></td>
                    <td>${w.manager_username}</td>
                    <td>
                        <a href="?update_warehouse&id=${w.id}" class="btn btn-sm btn-primary me-2">Cập nhật</a>
                    </td>
                </tr>
            `;
        });
    }

    // Hàm lọc dữ liệu theo từ khóa và trạng thái
    function filterWarehouses() {
        const keyword = searchInput.value.toLowerCase().trim();
        const status = statusSelect.value;

        const filtered = warehouseList.filter(w => {
            const matchesKeyword = w.name.toLowerCase().includes(keyword) ||
                                   w.address.toLowerCase().includes(keyword);
            const matchesStatus = status === '' || w.operation_status === status;
            return matchesKeyword && matchesStatus;
        });

        renderTable(filtered);
    }

    // Gọi lại khi người dùng gõ từ khóa hoặc chọn trạng thái
    searchInput.addEventListener('input', filterWarehouses);
    statusSelect.addEventListener('change', filterWarehouses);

    // Load danh sách ban đầu
    renderTable(warehouseList);
</script>




</html>
