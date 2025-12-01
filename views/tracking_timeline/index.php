<?php
    include_once('controllers/cTracking.php');
    $p = new controlTracking();
    include_once('controllers/cOrder.php');
    $q = new controlOrder();

    $orderInfo = null;
    if (isset($_GET['order_id'])) {
        $id = intval($_GET['order_id']);
        $result = $p->getAllTrackingByOrderID($id);
        $orderInfo = $q->getOrderById($id);
    }
    $trackings = [];
    if (is_array($result) && isset($result['data']) && is_array($result['data'])) {
        foreach ($result['data'] as $row) {
            $trackings[] = [
                'id' => $row['ID'],
                'OrderID' => $row['OrderID'],
                'Status' => $row['Status'],
                'Updated_at' => $row['Updated_at'],
            ];
        }
    }

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Theo dõi đơn hàng #<?php echo htmlspecialchars($_GET['order_id'] ?? ''); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .tracking-container {
            background-color: #f8f9fa;
            min-height: 100vh;
            padding: 2rem 0;
        }

        .tracking-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .tracking-header {
            background-color: #3498db;
            color: white;
            padding: 1.5rem;
            border-bottom: none;
        }

        .tracking-title {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
        }

        .tracking-title i {
            margin-right: 10px;
        }

        .tracking-body {
            padding: 0;
        }

        .tracking-timeline {
            position: relative;
            padding: 20px 0;
        }

        .tracking-date-group {
            margin-bottom: 1.5rem;
        }

        .tracking-date {
            background-color: #e9ecef;
            color: #495057;
            padding: 5px 15px;
            border-radius: 20px;
            display: inline-block;
            margin: 0 20px 10px;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .tracking-item {
            display: flex;
            padding: 15px 20px;
            position: relative;
        }

        .tracking-item::before {
            content: '';
            position: absolute;
            left: 40px;
            top: 0;
            bottom: 0;
            width: 2px;
            background-color: #dee2e6;
            z-index: 1;
        }

        .tracking-item.first::before {
            top: 50%;
        }

        .tracking-item:last-child::before {
            bottom: 50%;
        }

        .tracking-item.success {
            background-color: rgba(40, 167, 69, 0.05);
        }

        .tracking-item.info {
            background-color: rgba(23, 162, 184, 0.05);
        }

        .tracking-item.warning {
            background-color: rgba(255, 193, 7, 0.05);
        }

        .tracking-item.primary {
            background-color: rgba(13, 110, 253, 0.05);
        }

        .tracking-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            position: relative;
            z-index: 2;
            flex-shrink: 0;
        }

        .tracking-item.success .tracking-icon {
            background-color: #28a745;
            color: white;
        }

        .tracking-item.info .tracking-icon {
            background-color: #17a2b8;
            color: white;
        }

        .tracking-item.warning .tracking-icon {
            background-color: #ffc107;
            color: #212529;
        }

        .tracking-item.primary .tracking-icon {
            background-color: #0d6efd;
            color: white;
        }

        .tracking-content {
            flex-grow: 1;
        }

        .tracking-time {
            font-weight: 600;
            color: #495057;
            margin-bottom: 5px;
        }

        .tracking-status {
            color: #212529;
            margin-bottom: 5px;
        }

        .tracking-receiver {
            font-size: 0.9rem;
            color: #6c757d;
            margin-top: 5px;
        }

        .tracking-empty {
            text-align: center;
            padding: 3rem;
            color: #6c757d;
        }

        .tracking-empty i {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #adb5bd;
        }

        .tracking-empty p {
            font-size: 1.1rem;
            margin: 0;
        }

        .order-info {
            padding: 15px;
            margin-top: 20px;
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 5px;
        }

        .order-info h6 {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .order-info p {
            margin-bottom: 8px;
        }

        .order-info i {
            margin-right: 5px;
        }

        .tracking-footer {
            background-color: #f8f9fa;
            border-top: 1px solid #e9ecef;
            text-align: center;
            padding: 1.5rem;
        }

        /* Responsive adjustments */
        @media (max-width: 576px) {
            .tracking-title {
                font-size: 1.25rem;
            }

            .tracking-item {
                padding: 12px 15px;
            }

            .tracking-icon {
                width: 32px;
                height: 32px;
                font-size: 0.9rem;
            }
        }
     </style>
</head>
<body>
    <div class="tracking-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card tracking-card">
                        <div class="card-header tracking-header">
                            <h2 class="tracking-title">
                                <i class="fas fa-truck"></i> Theo dõi đơn hàng #<?php echo htmlspecialchars($_GET['order_id'] ?? ''); ?>
                            </h2>
                        </div>
                        <div class="card-body tracking-body">
                            <?php if ($orderInfo): ?>
                                <div class="order-info">
                                    <!-- <h6><i class="fas fa-info-circle"></i> Thông tin người nhận</h6> -->
                                    <p><i class="fas fa-user"></i> <strong>Người nhận:</strong> <?php echo htmlspecialchars($orderInfo['Recipient']); ?></p>
                                    <p><i class="fas fa-phone"></i> <strong>Điện thoại:</strong> <?php echo htmlspecialchars($orderInfo['RecipientPhone']); ?></p>
                                    <p><i class="fas fa-map-marker-alt"></i> <strong>Địa chỉ giao hàng:</strong> <?php echo htmlspecialchars($orderInfo['Delivery_address']); ?></p>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($trackings)): ?>
                                <div class="tracking-timeline">
                                    <?php
                                    // Nhóm các trạng thái theo ngày
                                    $groupedTrackings = [];
                                    foreach ($trackings as $tracking) {
                                        $date = date('d-m-Y', strtotime($tracking['Updated_at']));
                                        $groupedTrackings[$date][] = $tracking;
                                    }

                                    $firstIteration = true;
                                    foreach ($groupedTrackings as $date => $dailyTrackings):
                                    ?>
                                        <div class="tracking-date-group">
                                            <div class="tracking-date">
                                                <?php echo $date; ?>
                                            </div>

                                            <?php foreach ($dailyTrackings as $index => $tracking):
                                                $isLast = $index === count($dailyTrackings) - 1;
                                                $isFirst = $index === 0;
                                                $statusClass = '';

                                                // Xác định class trạng thái
                                                if (strpos($tracking['Status'], 'thành công') !== false) {
                                                    $statusClass = 'success';
                                                    $icon = 'fas fa-check-circle';
                                                } elseif (strpos($tracking['Status'], 'Đang vận chuyển') !== false) {
                                                    $statusClass = 'info';
                                                    $icon = 'fas fa-truck';
                                                } elseif (strpos($tracking['Status'], 'chuẩn bị') !== false) {
                                                    $statusClass = 'warning';
                                                    $icon = 'fas fa-box-open';
                                                } elseif (strpos($tracking['Status'], 'Đặt hàng') !== false) {
                                                    $statusClass = 'primary';
                                                    $icon = 'fas fa-shopping-cart';
                                                } else {
                                                    $statusClass = 'secondary';
                                                    $icon = 'fas fa-info-circle';
                                                }
                                            ?>
                                                <div class="tracking-item <?php echo $statusClass; ?> <?php echo $firstIteration && $isFirst ? 'first' : ''; ?>">
                                                    <div class="tracking-icon">
                                                        <i class="<?php echo $icon; ?>"></i>
                                                    </div>
                                                    <div class="tracking-content">
                                                        <div class="tracking-time">
                                                            <?php echo date('H:i - d/m/Y', strtotime($tracking['Updated_at'])); ?>
                                                        </div>
                                                        <div class="tracking-status">
                                                            <?php echo htmlspecialchars($tracking['Status']); ?>
                                                        </div>
                                                        <?php if (strpos($tracking['Status'], 'Người nhận hàng') !== false): ?>
                                                            <div class="tracking-receiver">
                                                                <?php
                                                                    // Extract receiver name from status
                                                                    $receiverInfo = explode('Người nhận hàng:', $tracking['Status']);
                                                                    if (count($receiverInfo) > 1) {
                                                                        echo '<strong>Người nhận:</strong> ' . htmlspecialchars(trim(explode('--', $receiverInfo[1])[0]));
                                                                    }
                                                                ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                <?php
                                                    $firstIteration = false;
                                                    endforeach;
                                                ?>
                                            </div>
                                        <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="tracking-empty">
                                    <i class="fas fa-box-open"></i>
                                    <p>Không tìm thấy thông tin theo dõi cho đơn hàng này</p>
                                </div>
                            <?php endif; ?>

                            
                        </div>
                        <div class="card-footer tracking-footer">
                            <a href="?quanlydonhang" class="btn btn-primary">
                                <i class="fas fa-home"></i> Quay về trang chủ
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>