<?php
if (!isset($_SESSION["dangnhap"]) || ($_SESSION["role"] != 5 && $_SESSION["role"] !=1 && $_SESSION["role"] !=2)) {
    echo "<script>alert('Bạn không có quyền truy cập!');</script>";
    header("refresh:0; url=index.php");
    exit();
}

include_once('controllers/cCOD.php');
// include_once('controllers/cOrder.php');
$codController = new controlCOD();
// $orderController = new controlOrder();
// Lấy dữ liệu tổng quan
$summaryData = $codController->sumaryCod()->fetch_assoc();

// Lấy dữ liệu đơn hàng COD
// Xử lý filter
$statusFilter = $_GET['status'] ?? null;
$startDateFilter = $_GET['start'] ?? null;
$endDateFilter = $_GET['end'] ?? null;

// Lấy dữ liệu đơn hàng COD với filter
$ordersData = $codController->selectAllCod($statusFilter, $startDateFilter, $endDateFilter);

// Lấy dữ liệu biểu đồ (7 ngày gần nhất)
$endDate = date('Y-m-d');
$startDate = date('Y-m-d', strtotime('-7 days'));
$chartData = $codController->chartCod($startDate, $endDate);

// Chuẩn bị dữ liệu cho biểu đồ
$chartLabels = [];
$chartValues = [];
while ($row = $chartData->fetch_assoc()) {
    $chartLabels[] = $row['date'];
    $chartValues[] = $row['total_amount'];
}

if (isset($_POST['btn_pay'])) {
    $orderId = $_POST['order_id'];
    $settledUp = $codController->setCodStatus($orderId, 'settled');
    // var_dump($orderId);
    // var_dump($_POST['cod_amount']);
    // var_dump( $_SESSION['user_id']);
    // creat trans
    include_once('../../config/database.php');
        $db = new clsKetNoi();
        $conn = $db->moKetNoi();
        $typeTrans = 'pay_cod';
        $statusTrans = 'completed';
        $createdAt = null;

        $sqlInsertTrans = "INSERT INTO transactions (OrderID, UserID, Type, Amount, Status, Created_at) VALUES (?, ?, ?, ?, ?, ?)";
        $stmtTrans = $conn->prepare($sqlInsertTrans);
        $stmtTrans->bind_param("iisdss", $orderId, $_SESSION['user_id'], $typeTrans, $_POST['cod_amount'], $statusTrans,$createdAt);
        if (!$stmtTrans->execute()) {
            echo json_encode(['success' => false, 'error' => 'Lỗi thêm Transaction: ' . $stmtTrans->error]);
            exit();
        }
        $stmtTrans->close();
     if ($settledUp) {
        echo "<script>alert('Đã thanh toán COD đơn hàng #$orderId thành công!');</script>";
        echo "<script>window.location.href='?cod_dashboard';</script>";
    } else {
        echo "<script>alert('Cập nhật thất bại. Vui lòng thử lại!');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý COD | Admin</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .cod-admin-container {
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        .cod-admin-header {
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .cod-summary-cards .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s;
        }
        .cod-summary-cards .card:hover {
            transform: translateY(-5px);
        }
        .cod-card-pending {
            background-color: #fff8e1;
        }
        .cod-card-collected {
            background-color: #e8f5e9;
        }
        .cod-card-settled {
            background-color: #e3f2fd;
        }
        .cod-card-value {
            font-size: 1.8rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        .cod-card-count {
            color: #6c757d;
            font-size: 0.9rem;
        }
        .badge.bg-warning {
            background-color: #ffc107 !important;
            color: #000;
        }
    </style>
</head>
<body>
    <div class="cod-admin-container">
        <!-- Header -->
        <header class="cod-admin-header p-3" style="background-color: #ffffff; color: #007bff;">
    <div class="container-fluid d-flex justify-content-center align-items-center">
        <h1 class="mb-0"><i class="fas fa-money-bill-wave me-2" style='text-align:center;'></i>Quản Lý Thu Hộ (COD)</h1>
    </div>
</header>

        <!-- Main Content -->
        <div class="cod-admin-main container-fluid mt-4">
            <!-- Summary Cards -->
            <div class="row cod-summary-cards">
                <div class="col-md-4 mb-4">
                    <div class="card cod-card-pending h-100">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-clock me-2"></i>Chờ Thu</h5>
                            <p class="cod-card-value"><?= number_format($summaryData['total_pending']) ?> ₫</p>
                            <p class="cod-card-count"><?= $summaryData['count_pending'] ?> đơn</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card cod-card-collected h-100">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-wallet me-2"></i>Đã Thu</h5>
                            <p class="cod-card-value"><?= number_format($summaryData['total_collected']) ?> ₫</p>
                            <p class="cod-card-count"><?= $summaryData['count_collected'] ?> đơn</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card cod-card-settled h-100">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-check-circle me-2"></i>Đã Thanh Toán</h5>
                            <p class="cod-card-value"><?= number_format($summaryData['total_settled']) ?> ₫</p>
                            <p class="cod-card-count"><?= $summaryData['count_settled'] ?> đơn</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters & Search -->
            <div class="cod-filter-section mb-4 p-3 bg-light rounded">
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label">Trạng Thái</label>
                        <select class="form-select cod-filter-status">
                            <option value="all">Tất Cả</option>
                            <option value="pending">Chờ Thu</option>
                            <option value="collected">Đã Thu</option>
                            <option value="settled">Đã Thanh Toán</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Từ Ngày</label>
                        <input type="date" class="form-control cod-filter-date" value="<?= date('Y-m-d', strtotime('-7 days')) ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Đến Ngày</label>
                        <input type="date" class="form-control cod-filter-date" value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button class="btn btn-primary cod-filter-btn">
                            <i class="fas fa-search me-2"></i>Tìm Kiếm
                        </button>
                    </div>
                </div>
            </div>

            <!-- COD Orders Table -->
            <div class="cod-table-section">
                <div class="card">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Danh Sách Đơn Hàng COD</h5>
                        <button class="btn btn-success cod-export-btn">
                            <i class="fas fa-file-excel me-2"></i>Xuất Excel
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover cod-orders-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>Mã Đơn</th>
                                        <th>Kho Hàng</th>
                                        <th>Khách Hàng</th>
                                        <th>Shipper</th>
                                        <th>Số Tiền</th>
                                        <th>Trạng Thái</th>
                                        <th>Ngày Tạo</th>
                                        <th>Hành Động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($order = $ordersData->fetch_assoc()): ?>
                                    <tr>
                                        <td>#<?= $order['order_id'] ?></td>
                                        <td><?= $order['Name'] ?></td>
                                        <td><?= $order['customer_name'] ?></td>
                                        <td><?= $order['shipper_name'] ?: 'Chưa phân công' ?></td>
                                        <td><?= number_format($order['cod_amount']) ?> ₫</td>
                                        <td>
                                            <?php 
                                            $badgeClass = [
                                                'pending' => 'bg-warning',
                                                'collected' => 'bg-info',
                                                'settled' => 'bg-success'
                                            ][$order['cod_status']] ?? 'bg-secondary';
                                            ?>
                                            <span class="badge <?= $badgeClass ?>">
                                                <?= [
                                                    'pending' => 'Chờ Thu',
                                                    'collected' => 'Đã Thu',
                                                    'settled' => 'Đã Thanh Toán'
                                                ][$order['cod_status']] ?>
                                            </span>
                                        </td>
                                        <td><?= date('d/m/Y', strtotime($order['order_date'])) ?></td>
                                        <td>
                                            <form method="post">
                                                <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                                                <input type="hidden" name="cod_amount" value="<?= $order['cod_amount'] ?>">
                                            <?php
                                                if($order['cod_status'] === 'collected'){
                                                    echo '<button class="btn btn-sm btn-outline-primary cod-action-btn" name="btn_pay">
                                                <i class="fas fa-eye"></i>Thanh toán
                                            </button>';
                                                }
                                            ?>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="cod-chart-section mt-4">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Thống Kê COD 7 Ngày Gần Nhất</h5>
                    </div>
                    <div class="card-body">
                        <div class="cod-chart-container" style="height: 300px;">
                            <canvas id="codChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap & JS Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        // Biểu đồ COD
        const ctx = document.getElementById('codChart').getContext('2d');
        const codChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($chartLabels) ?>,
                datasets: [{
                    label: 'Tổng COD (VND)',
                    data: <?= json_encode($chartValues) ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString('vi-VN') + ' ₫';
                            }
                        }
                    }
                }
            }
        });

        // Xử lý filter
        const statusSelect = document.querySelector('.cod-filter-status');
        const startDateInput = document.querySelectorAll('.cod-filter-date')[0];
        const endDateInput = document.querySelectorAll('.cod-filter-date')[1];

        // Hàm xử lý khi có thay đổi
        const filterBtn = document.querySelector('.cod-filter-btn');
        filterBtn.addEventListener('click', function () {
            const status = statusSelect.value;
            const start = startDateInput.value;
            const end = endDateInput.value;

            const queryParams = new URLSearchParams();

            if (status && status !== 'all') queryParams.append('status', status);
            if (start) queryParams.append('start', start);
            if (end) queryParams.append('end', end);

            // Reload trang với query string mới
            window.location.href = '?' + queryParams.toString();
        });

        // Set lại selected value cho dropdown filter nếu có giá trị từ PHP
        <?php if ($statusFilter): ?>
            statusSelect.value = "<?= $statusFilter ?>";
        <?php endif; ?>
        <?php if ($startDateFilter): ?>
            startDateInput.value = "<?= $startDateFilter ?>";
        <?php endif; ?>
        <?php if ($endDateFilter): ?>
            endDateInput.value = "<?= $endDateFilter ?>";
        <?php endif; ?>
        function updateFilters() {
            const status = statusSelect.value;
            const startDate = startDateInput.value;
            const endDate = endDateInput.value;
            window.location.href = `?cod_dashboard&status=${status}&start=${startDate}&end=${endDate}`;
            // window.location.href = '?cod_dashboard&status=collected&start=2025-05-10&end=2025-05-17';
        }
        

        // Lắng nghe sự thay đổi
        statusSelect.addEventListener('change', updateFilters);
        startDateInput.addEventListener('change', updateFilters);
        endDateInput.addEventListener('change', updateFilters);
    </script>
</body>
</html>