<?php
    include_once('controllers/cTracking.php');
    $p = new controlTracking();
    include_once('controllers/cOrder.php');
    $q = new controlOrder();
    $orderInfo = null;
    if (isset($_REQUEST['submit'])) {
        $id = intval($_REQUEST['ID']);
        $orderInfo = $q->getOrderById($id);
        $result = $p->getAllTrackingByOrderID($id);
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
    }
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tra Cứu Đơn Hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Biến màu sắc */
        :root {
            --tracking-primary: #4e73df;
            --tracking-secondary: #858796;
            --tracking-success: #1cc88a;
            --tracking-info: #36b9cc;
            --tracking-warning: #f6c23e;
            --tracking-danger: #e74a3b;
            --tracking-light: #f8f9fc;
            --tracking-dark: #5a5c69;
            --tracking-border: #e3e6f0;
            --tracking-shadow: 0 0.15rem 1.75rem rgba(58, 59, 69, 0.1);
        }

        /* Reset và base styles */
        .tracking__container {
            min-height: 100vh;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        /* Card styles */
        .tracking__card,
        .tracking-card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            max-width: 800px;
            width: 100%;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        /* Header styles */
        .tracking__header,
        .tracking-header {
            background-color: var(--tracking-primary);
            color: white;
            border-bottom: none;
            padding: 1.5rem;
            text-align: center;
        }

        .tracking__title,
        .tracking-title {
            font-weight: 700;
            font-size: 1.75rem;
            margin: 0;
        }

        .tracking__icon,
        .tracking-title i {
            font-size: 1.5rem;
            margin-right: 10px;
        }

        /* Body styles */
        .tracking__body,
        .tracking-body {
            padding: 1.5rem;
        }

        /* Form styles */
        .tracking__input {
            border: 2px solid var(--tracking-border);
            border-radius: 10px 0 0 10px !important;
            padding: 15px 20px;
            font-size: 1.1rem;
        }

        .tracking__input:focus {
            border-color: var(--tracking-primary);
            box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.25);
        }

        .tracking__button {
            border-radius: 0 10px 10px 0 !important;
            padding: 15px 25px;
            font-weight: 600;
            background-color: var(--tracking-primary);
            border: none;
            color: white;
        }

        .tracking__button:hover {
            background-color: #3a5bc7;
        }

        /* Welcome/Empty state styles */
        .tracking__welcome,
        .tracking-empty {
            background-color: var(--tracking-light);
            border-radius: 10px;
            text-align: center;
            padding: 3rem;
            color: var(--tracking-secondary);
        }

        .tracking__welcome-icon,
        .tracking-empty i {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: var(--tracking-secondary);
        }

        .tracking__welcome-title,
        .tracking-empty p {
            color: var(--tracking-dark);
            font-weight: 600;
            margin-bottom: 10px;
            font-size: 1.1rem;
        }

        /* Status and timeline styles */
        .tracking__status,
        .order-info {
            background-color: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: var(--tracking-shadow);
            margin-top: 20px;
        }

        .order-info {
            background-color: #f8f9fa;
            border: 1px solid var(--tracking-border);
            padding: 15px;
        }

        .tracking__status-header {
            border-bottom: 1px solid var(--tracking-border);
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .tracking__order-number {
            font-weight: 700;
            color: var(--tracking-primary);
        }

        /* Timeline styles */
        .tracking__timeline,
        .tracking-timeline {
            position: relative;
            padding-left: 30px;
            margin-top: 30px;
        }

        .tracking__timeline::before,
        .tracking-item::before {
            content: '';
            position: absolute;
            left: 10px;
            top: 0;
            bottom: 0;
            width: 2px;
            background-color: var(--tracking-border);
            z-index: 1;
        }

        .tracking-item::before {
            left: 40px;
        }

        .tracking__step,
        .tracking-item {
            position: relative;
            padding-bottom: 30px;
            display: flex;
            padding: 15px 20px;
        }

        .tracking__step:last-child,
        .tracking-item:last-child {
            padding-bottom: 0;
        }

        .tracking-item.first::before {
            top: 50%;
        }

        .tracking-item:last-child::before {
            bottom: 50%;
        }

        .tracking__step-icon,
        .tracking-icon {
            position: absolute;
            left: -30px;
            top: 0;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--tracking-secondary);
            border: 4px solid white;
            z-index: 2;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .tracking-icon {
            position: relative;
            left: auto;
            margin-right: 15px;
            color: white;
        }

        .tracking__step--active .tracking__step-icon,
        .tracking-item.primary .tracking-icon {
            background-color: var(--tracking-primary);
        }

        .tracking__step--completed .tracking__step-icon,
        .tracking-item.success .tracking-icon {
            background-color: var(--tracking-success);
        }

        .tracking-item.info .tracking-icon {
            background-color: var(--tracking-info);
        }

        .tracking-item.warning .tracking-icon {
            background-color: var(--tracking-warning);
            color: #212529;
        }

        .tracking__step-content,
        .tracking-content {
            flex-grow: 1;
        }

        .tracking__step-date,
        .tracking-time {
            font-size: 0.85rem;
            color: var(--tracking-secondary);
            margin-bottom: 5px;
            font-weight: 600;
        }

        .tracking__step-title,
        .tracking-status {
            font-weight: 600;
            margin-bottom: 5px;
            color: #212529;
        }

        .tracking__step-description,
        .tracking-receiver {
            color: var(--tracking-secondary);
            font-size: 0.95rem;
        }

        .tracking-receiver {
            margin-top: 5px;
        }

        /* Date group styles */
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

        /* Item state styles */
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

        /* Footer styles */
        .tracking__footer,
        .tracking-footer {
            background-color: var(--tracking-light);
            border-top: 1px solid var(--tracking-border);
            text-align: center;
            padding: 1rem 1.5rem;
        }

        .tracking__footer-text {
            color: var(--tracking-dark);
            font-size: 0.9rem;
            margin: 0;
        }

        /* Responsive adjustments */
        @media (max-width: 576px) {
            .tracking__title,
            .tracking-title {
                font-size: 1.25rem;
            }
            
            .tracking__input,
            .tracking__button {
                padding: 12px 15px;
            }
            
            .tracking__button span {
                display: none;
            }
            
            .tracking__button i {
                margin-right: 0 !important;
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
    <div class="tracking__container">
        <div class="tracking__card card shadow-lg">
            <div class="tracking__header card-header text-center py-4">
                <h1 class="tracking__title mb-0">
                    <i class="fas fa-box-open tracking__icon me-2"></i>
                    TRA CỨU ĐƠN HÀNG
                </h1>
            </div>
            
            <div class="tracking__body card-body p-4">
                <div class="tracking__search mb-5">
                    <form id="trackingForm" class="tracking__form" action="#" method="POST">
                        <div class="input-group mb-3">
                            <input type="text" 
                                   class="tracking__input form-control form-control-lg" 
                                   placeholder="Nhập mã đơn hàng của bạn" 
                                   id="trackingNumber"
                                   name="ID"
                                   required>
                            <button class="tracking__button btn btn-primary btn-lg" type="submit" name="submit">
                                <i class="fas fa-search me-2"></i>Tra Cứu
                            </button>
                        </div>
                    </form>
                </div>
                <?php if ($orderInfo && is_array($orderInfo)) : ?>
                    <div class="order-info">
                        <h5>Thông tin đơn hàng</h5>
                        <p><strong>Mã đơn:</strong> <?= htmlspecialchars($orderInfo['ID']) ?></p>
                        <p><strong>Người gửi:</strong> <?= htmlspecialchars($orderInfo['FullName']) ?></p>
                        <p><strong>Người nhận:</strong> <?= htmlspecialchars($orderInfo['Recipient']) ?></p>
                        <p><strong>Địa chỉ giao:</strong> <?= htmlspecialchars($orderInfo['Delivery_address']) ?></p>
                        <p><strong>Trạng thái hiện tại:</strong> <?= htmlspecialchars($orderInfo['Status']) ?></p>
                    </div>

                    <?php if (!empty($trackings)) : ?>
                        <div class="tracking__status">
                            <div class="tracking__status-header">
                                <h5 class="mb-0">Lịch sử vận chuyển</h5>
                            </div>
                            <div class="tracking__timeline">
                                <?php foreach ($trackings as $tracking): ?>
                                    <?php
                                        // Gán màu trạng thái tương ứng
                                        $statusClass = 'primary';
                                        if (stripos($tracking['Status'], 'giao thành công') !== false) {
                                            $statusClass = 'success';
                                        } elseif (stripos($tracking['Status'], 'đang giao') !== false) {
                                            $statusClass = 'info';
                                        } elseif (stripos($tracking['Status'], 'lỗi') !== false || stripos($tracking['Status'], 'thất bại') !== false) {
                                            $statusClass = 'danger';
                                        } elseif (stripos($tracking['Status'], 'chờ') !== false) {
                                            $statusClass = 'warning';
                                        }
                                    ?>
                                    <div class="tracking__step tracking-item <?= $statusClass ?>">
                                        <div class="tracking__step-icon tracking-icon">
                                            <i class="fas fa-truck"></i>
                                        </div>
                                        <div class="tracking__step-content tracking-content">
                                            <div class="tracking__step-date tracking-time"><?= htmlspecialchars($tracking['Updated_at']) ?></div>
                                            <div class="tracking__step-title tracking-status"><?= htmlspecialchars($tracking['Status']) ?></div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="tracking__welcome tracking-empty">
                            <i class="fas fa-info-circle tracking__welcome-icon"></i>
                            <p class="tracking__welcome-title">Không có thông tin vận chuyển cho đơn hàng này.</p>
                        </div>
                    <?php endif; ?>
                <?php elseif (isset($_REQUEST['submit'])): ?>
                    <div class="tracking__welcome tracking-empty">
                        <i class="fas fa-exclamation-circle tracking__welcome-icon"></i>
                        <p class="tracking__welcome-title">Không tìm thấy đơn hàng với mã đã nhập. Vui lòng thử lại.</p>
                    </div>
                <?php else: ?>
                    <div class="tracking__welcome tracking-empty">
                        <i class="fas fa-search-location tracking__welcome-icon"></i>
                        <h3 class="tracking__welcome-title">Chào mừng đến với hệ thống tra cứu đơn hàng</h3>
                        <p class="tracking__welcome-text text-muted">Vui lòng nhập mã đơn hàng để xem trạng thái vận chuyển</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="tracking__footer card-footer text-center py-3">
                <p class="tracking__footer-text mb-0">
                    <i class="fas fa-headset me-2"></i>
                    Hỗ trợ khách hàng: 1900 1234
                </p>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <!-- <script src="script.js"></script> -->
</body>
</html>