<?php
// FILE: views/order_detail/index.php (ĐÃ SỬA LỖI XUNG ĐỘT CSS)

include_once('controllers/cOrder.php');
include_once('controllers/cTracking.php');
$trackingController = new controlTracking(); 
$p = new controlOrder();
date_default_timezone_set('Asia/Ho_Chi_Minh');
function getStatusBadge($status) {
    $badgeClass = 'badge-secondary'; // Mặc định
    $statusText = ucfirst($status);  // Tự động viết hoa chữ cái đầu

    switch ($status) {
        case 'delivered':
            $badgeClass = 'badge-success'; $statusText = 'Đã giao'; break;
        case 'picked_up':
            $badgeClass = 'badge-info'; $statusText = 'Đã lấy hàng'; break;
        case 'in_transit':
            $badgeClass = 'badge-info'; $statusText = 'Đang giao'; break;
        case 'accepted':
            $badgeClass = 'badge-primary'; $statusText = 'Đã chấp nhận'; break;
        case 'pending':
            $badgeClass = 'badge-warning'; $statusText = 'Chờ xử lý'; break;
        case 'delivery_failed':
        case 'cancelled':
            $badgeClass = 'badge-danger'; $statusText = 'Đã hủy/Thất bại'; break;
    }
    return "<span class='badge {$badgeClass}'>{$statusText}</span>";
}
$orderDetail = null;
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];
    $orderDetail = $p->getOrderById($id); 
} else {
    echo "ID không hợp lệ!";
}

// Xử lý logic khi nút được nhấn
if (isset($_POST['delivered'])) {
    $delivered = $p->setOrderStatus($_GET['id'], 'delivered');
    $trackingStatus = 'Đơn hàng đã giao thành công'; 
    $timestamp1_unix = time();
    $timestamp1_formatted = date('Y-m-d H:i:s', $timestamp1_unix);
    $trackingResult = $trackingController->addTrackingTimeline($_GET['id'], $trackingStatus, $timestamp1_formatted);

    // create transaction
    if($orderDetail['COD_amount'] > 0){
        include_once('config/database.php'); // Đổi đường dẫn cho đúng
        $db = new clsKetNoi();
        $conn = $db->moKetNoi();
        $typeTrans = 'collect_cod';
        $statusTrans = 'completed';
        $createdAt =  $timestamp1_formatted; // Sửa lỗi: Gán thời gian hiện tại

        $sqlInsertTrans = "INSERT INTO transactions (OrderID, UserID, Type, Amount, Status, Created_at) VALUES (?, ?, ?, ?, ?, ?)";
        $stmtTrans = $conn->prepare($sqlInsertTrans);
        $stmtTrans->bind_param("iisdss", $_GET['id'], $orderDetail['ShipperID'], $typeTrans, $orderDetail['COD_amount'], $statusTrans,$createdAt);
        if (!$stmtTrans->execute()) {
            echo "Lỗi thêm Transaction: " . $stmtTrans->error;
            // Không nên exit() ở đây, hãy để code chạy tiếp
        }
        $stmtTrans->close();
        $db->dongKetNoi($conn);
    }

    echo "<script>
    alert('Giao hàng thành công!');
    window.location.href = window.location.href;
    </script>";
    exit(); // Thêm exit() để đảm bảo dừng thực thi
    
} elseif (isset($_POST['failed'])) {
    $delivery_failed = $p->setOrderStatus($_GET['id'], 'delivery_failed');
    $trackingStatus = 'Giao hàng thất bại, shipper sẽ liên lạc lại cho bạn'; 
    $timestamp1_unix = time(); 
    $timestamp1_formatted = date('Y-m-d H:i:s', $timestamp1_unix);
    $trackingResult = $trackingController->addTrackingTimeline($_GET['id'], $trackingStatus, $timestamp1_formatted);
    echo "<script>
    alert('Giao hàng thất bại!');
    window.location.href = window.location.href;
    </script>";
    exit();
    
} elseif (isset($_POST['returned'])) {
    $returned = $p->setOrderStatus($_GET['id'], 'returned');
    $trackingStatus = 'Đơn hàng được hoàn trả'; 
    $timestamp1_unix = time();
    $timestamp1_formatted = date('Y-m-d H:i:s', $timestamp1_unix);
    $trackingResult = $trackingController->addTrackingTimeline($_GET['id'], $trackingStatus, $timestamp1_formatted);
    echo "<script>
    alert('Hoàn trả đơn hàng!');
    window.location.href = window.location.href;
    </script>";
    exit();
}
?>

<style>
    /* * Bằng cách thêm #order-detail-wrapper vào trước mỗi selector,
     * chúng ta ép các style này CHỈ được áp dụng cho các phần tử
     * nằm bên trong div có ID là "order-detail-wrapper".
     * Chúng sẽ không còn ảnh hưởng đến các phần khác của website.
     */
    
    #order-detail-wrapper {
        font-family: 'Arial', sans-serif;
        background-color: #f4f6f9;
        margin-top: 50px;
        padding-bottom: 30px; /* Thêm padding dưới */
    }
    
    #order-detail-wrapper .title {
        font-size: 28px;
        font-weight: bold;
        margin-bottom: 30px;
        color: #333;
        text-align: center;
    }
    
    #order-detail-wrapper .box {
        padding: 30px;
        border-radius: 12px;
        background-color: #ffffff;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        margin-bottom: 30px;
    }
    
    #order-detail-wrapper .label {
        font-weight: bold;
        color: #555;
    }
    
    #order-detail-wrapper .row p {
        font-size: 16px;
        margin-bottom: 12px;
    }
    
    #order-detail-wrapper .button-group {
        margin-top: 20px;
        text-align: center;
    }
    
    #order-detail-wrapper .button-group button {
        margin: 5px 10px; /* Thêm margin-top 5px */
    }
    
    /* Đặt tên class cụ thể để tránh xung đột với .btn-success của Bootstrap */
    #order-detail-wrapper .btn-action-success {
        background-color: #28a745;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
    }
    
    #order-detail-wrapper .btn-action-danger {
        background-color: #dc3545;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
    }
    
    #order-detail-wrapper .btn-action-warning {
        background-color: #ffc107;
        color: #212529;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
    }
    
    #order-detail-wrapper .back-btn {
        display: block;
        padding: 10px 20px;
        background-color: rgb(0, 136, 255);
        color: white;
        border-radius: 5px;
        text-decoration: none;
        transition: background-color 0.3s ease;
        width: fit-content;
        margin: 20px auto 0;
    }
    
    #order-detail-wrapper .back-btn:hover {
        background-color: #5a6268;
        color: white;
        text-decoration: none;
    }

    #order-detail-wrapper .proof-image {
        width: 100%;           /* Ảnh sẽ lấp đầy theo chiều rộng của cột */
        max-height: 300px;     /* === THAY ĐỔI CHÍNH: Giới hạn chiều cao tối đa === */
        object-fit: cover;   /* Đảm bảo ảnh lấp đầy khung mà không bị méo */
        border-radius: 8px;
        border: 1px solid #ddd;
        margin-top: 10px;
        cursor: pointer;       /* Thêm con trỏ để báo hiệu có thể click */
    }
</style>

<div id="order-detail-wrapper">

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
                            <?php echo getStatusBadge($orderDetail['status'] ?? 'N/A');?>
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


        <?php if (!empty($orderDetail['PickUp_Photo_Path']) || !empty($orderDetail['Delivery_Photo_Path'])): ?>
            <hr>
            <h5 class="label mb-3">Bằng chứng Giao/Lấy hàng</h5>
            <div class="row">
                <?php if (!empty($orderDetail['PickUp_Photo_Path'])): ?>
                    <div class="col-md-6">
                        <p class="label">Ảnh lấy hàng:</p>
                        <a href="<?= htmlspecialchars($orderDetail['PickUp_Photo_Path']) ?>" target="_blank">
                            <img src="<?= htmlspecialchars($orderDetail['PickUp_Photo_Path']) ?>" alt="Ảnh lấy hàng" class="proof-image">
                        </a>
                    </div>
                <?php endif; ?>

                <?php if (!empty($orderDetail['Delivery_Photo_Path'])): ?>
                    <div class="col-md-6">
                        <p class="label">Ảnh giao hàng:</p>
                        <a href="<?= htmlspecialchars($orderDetail['Delivery_Photo_Path']) ?>" target="_blank">
                            <img src="<?= htmlspecialchars($orderDetail['Delivery_Photo_Path']) ?>" alt="Ảnh giao hàng" class="proof-image">
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        </div> <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 6): ?>
        <div class="button-group">
            </div>
    <?php endif; ?>
            
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 6): ?>
            <div class="button-group">
                <form method="post">
                    <button type="submit" class="btn-action-success" name="delivered">Giao hàng thành công</button>
                    <button type="submit" class="btn-action-danger" name="failed">Giao hàng thất bại</button>
                    <button type="submit" class="btn-action-warning" name="returned">Hoàn trả hàng</button>
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

</div>