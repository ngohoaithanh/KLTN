<?php
if (!isset($_SESSION["dangnhap"]) || ($_SESSION["role"] !=5 && $_SESSION["role"] !=1 && $_SESSION["role"] !=2)) {
    echo "<script>alert('Bạn không có quyền truy cập!');</script>";
    header("refresh:0; url=index.php");
    exit();
}
include_once("controllers/controlDashboard.php");

$dashboard = new controlDashboard();
$data = $dashboard->getSummary();

// Dữ liệu giả lập đơn hàng theo thời gian
$order_trend = [
    'labels' => ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5'],
    'values' => [2, 5, 3, 7, 8] // Bạn có thể lấy dữ liệu thật từ DB nếu có
];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thống kê hệ thống</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f3f6f9;
            color: #364a63;
        }

        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px;
        }

        .dashboard-header {
            text-align: center;
            color: #4d70fa;
            margin-bottom: 40px;
        }

        .overview-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .overview-card {
            background-color: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            border-left: 5px solid #4d70fa;
        }

        .overview-card h5 {
            font-size: 1.1rem;
            color: #6c757d;
            margin-bottom: 8px;
        }

        .overview-card .value {
            font-size: 1.75rem;
            font-weight: bold;
            color: #364a63;
        }

        .chart-section {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }

        .chart-section h3 {
            color: #4d70fa;
            margin-bottom: 20px;
            font-size: 1.5rem;
        }

        .chart-row {
            display: flex;
            gap: 20px;
            overflow-x: auto;
        }

        .chart-container {
            flex: 1;
            min-width: 300px;
            max-width: 500px;
            height: 300px;
        }

        .role-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 20px;
        }

        .role-badge {
            background-color: #e9ecef;
            color: #495057;
            padding: 10px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
        }

        .role-badge strong {
            font-weight: bold;
            color: #4d70fa;
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="dashboard-container">
        <h2 class="dashboard-header"><i class="bi bi-speedometer2 me-2"></i> Báo cáo & Thống kê</h2>

        <div class="overview-grid">
            <div class="overview-card">
                <h5><i class="bi bi-cart-fill me-2"></i> Tổng đơn hàng</h5>
                <p class="value"><?= $data['total_orders'] ?></p>
            </div>
            <div class="overview-card">
                <h5><i class="bi bi-cash-coin me-2"></i> Tổng thu (COD)</h5>
                <p class="value"><?= number_format($data['total_thu']) ?> VND</p>
            </div>
            <div class="overview-card">
                <h5><i class="bi bi-truck me-2"></i> Tổng chi (vận chuyển)</h5>
                <p class="value"><?= number_format($data['total_chi']) ?> VND</p>
            </div>
            <div class="overview-card">
                <h5><i class="bi bi-people-fill me-2"></i> Tổng người dùng</h5>
                <p class="value"><?= $data['total_users'] ?></p>
            </div>
        </div>

        <?php if (!empty($data['role_counts'])): ?>
            <div class="role-badges">
                <?php foreach ($data['role_counts'] as $role): ?>
                    <span class="role-badge">
                        <i class="bi bi-person-badge-fill me-1"></i>
                        <strong><?= htmlspecialchars($role['role_name']) ?>:</strong> <?= $role['total_users'] ?>
                    </span>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="chart-section">
            <h3><i class="bi bi-bar-chart-line me-2"></i> Phân tích đơn hàng</h3>
            <div class="chart-row">
                <div class="chart-container">
                    <canvas id="statusChart"></canvas>
                </div>
                <div class="chart-container">
                    <canvas id="financeChart"></canvas>
                </div>
            </div>
        </div>

        <div class="chart-section">
            <h3><i class="bi bi-graph-up me-2"></i> Xu hướng và thống kê khác</h3>
            <div class="chart-row">
                <div class="chart-container">
                    <canvas id="orderTrendChart"></canvas>
                </div>
                <div class="chart-container">
                    <canvas id="codStatusChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Dữ liệu trạng thái đơn hàng
        const statusData = {
            labels: [
                'Chờ xử lý', 'Đã tiếp nhận', 'Trong kho', 'Xuất kho', 'Đang giao',
                'Giao thành công', 'Giao thất bại', 'Bị hoàn trả', 'Đã hủy'
            ],
            datasets: [{
                label: 'Số lượng',
                data: [
                    <?= $data['pending'] ?? 0 ?>,
                    <?= $data['received'] ?? 0 ?>,
                    <?= $data['in_warehouse'] ?? 0 ?>,
                    <?= $data['out_of_warehouse'] ?? 0 ?>,
                    <?= $data['in_transit'] ?? 0 ?>,
                    <?= $data['delivered'] ?? 0 ?>,
                    <?= $data['delivery_failed'] ?? 0 ?>,
                    <?= $data['returned'] ?? 0 ?>,
                    <?= $data['cancelled'] ?? 0 ?>
                ],
                backgroundColor: [
                    '#636efa', '#67c8ff', '#a78bfa', '#56ca85', '#ffc107',
                    '#05b18a', '#fa5252', '#adb5bd', '#343a40'
                ]
            }]
        };

        new Chart(document.getElementById('statusChart'), {
            type: 'pie',
            data: statusData,
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Trạng thái đơn hàng',
                        font: {
                            size: 16
                        }
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Biểu đồ thu/chi
        const financeData = {
            labels: ['Tổng thu (COD)', 'Tổng chi (Phí vận chuyển)'],
            datasets: [{
                label: 'VNĐ',
                data: [<?= $data['total_thu'] ?>, <?= $data['total_chi'] ?>],
                backgroundColor: ['#28a745', '#dc3545']
            }]
        };

        new Chart(document.getElementById('financeChart'), {
            type: 'bar',
            data: financeData,
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'So sánh thu/chi',
                        font: {
                            size: 16
                        }
                    },
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: value => value.toLocaleString('vi-VN') + ' VND'
                        }
                    }
                }
            }
        });

        // Biểu đồ đơn hàng theo thời gian (giả lập)
        const orderTrendData = {
            labels: <?= json_encode($order_trend['labels']) ?>,
            datasets: [{
                label: 'Số đơn hàng',
                data: <?= json_encode($order_trend['values']) ?>,
                fill: false,
                borderColor: '#4d70fa',
                backgroundColor: '#4d70fa',
                tension: 0.3
            }]
        };

        new Chart(document.getElementById('orderTrendChart'), {
            type: 'line',
            data: orderTrendData,
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Đơn hàng theo thời gian',
                        font: {
                            size: 16
                        }
                    },
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Biểu đồ COD theo Status
        const codStatusLabels = <?= json_encode(array_column($data['cod_by_status'], 'Status')) ?>;
        const codStatusData = <?= json_encode(array_map('intval', array_column($data['cod_by_status'], 'total_cod'))) ?>;

        new Chart(document.getElementById('codStatusChart'), {
            type: 'bar',
            data: {
                labels: codStatusLabels,
                datasets: [{
                    label: 'Tổng COD (VND)',
                    data: codStatusData,
                    backgroundColor: ['#28a745', '#ffc107', '#dc3545', '#17a2b8']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'COD theo trạng thái',
                        font: {
                            size: 16
                        }
                    },
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: value => value.toLocaleString('vi-VN') + ' VND'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Trạng thái'
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>