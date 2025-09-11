<?php
if (!isset($_SESSION["dangnhap"]) || ($_SESSION["role"] != 3 && $_SESSION["role"] != 6 && $_SESSION["role"] !=1 && $_SESSION["role"] !=2)) {
    echo "<script>alert('Bạn không có quyền truy cập!');</script>";
    header("refresh:0; url=index.php");
    exit();
}

include_once('controllers/cOrder.php');
$p = new controlOrder();

// Xử lý tìm kiếm
if (isset($_REQUEST['submit'])) {
    $tblSP = $p->searchOrderById($_REQUEST['search']); // Tìm kiếm
} else {
    $tblSP = $p->getAllOrder(); // Lấy tất cả
}

$orders = [];
if ($tblSP) {
    foreach ($tblSP as $row) {
        $orders[] = [
            'id' => $row['ID'],
            'Username' => $row['UserName'],
            'Delivery_address' => $row['Delivery_address'],
            'Recipient' => $row['Recipient'],
            'RecipientPhone' => $row['RecipientPhone'],
            'Weight' => $row['Weight'],
            'Created_at' => $row['Created_at'],
            'COD_amount' => $row['COD_amount'],
            'Shippername' => $row['ShipperName'],
            'Status' => $row['Status'],
            'Note' => $row['Note'],
            'PhoneNumberCus' => $row['PhoneNumberCus'],
            'Pick_up_address' => $row['Pick_up_address'],
            'Shippingfee' => $row['Shippingfee'] ?? 0
        ];
    }
} else {
    echo "Không có đơn hàng nào";
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Đơn hàng</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
            text-align: center;
            display: inline-block;
            min-width: 100px;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-shipping {
            background-color: #cce5ff;
            color: #004085;
        }
        .status-delivered {
            background-color: #d4edda;
            color: #155724;
        }
        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
<div class="container" id="orders" style="margin-top: 40px;">
    
    <h2 style="margin-bottom: 20px;">Quản Lý Đơn Hàng</h2>

    <div style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
        <form method="POST" action="#">
            <input type="text" id="searchInput" name="search" placeholder="Tìm kiếm đơn hàng ..." class="form-control" style="width: 300px; display: inline-block;">
            <button type="submit" class="btn btn-primary" style="margin-left: 10px;" name="submit">Tìm Kiếm</button>
        </form>
        <a href="?addOrder" class="btn btn-success">+ Tạo Đơn Hàng Mới</a>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Mã đơn</th>
                <th>Bên gửi</th>
                <th>Bên nhận</th> <th>Ngày tạo</th>
                <th>COD</th>
                <th>Phí VC</th>
                <th>Trạng thái</th>
                <?php if ($_SESSION["role"] != 6): ?>
                    <th>Thao tác</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody id="ordersTableBody">
            <?php if (!empty($orders)): ?>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><a href="?order_detail&id=<?= htmlspecialchars($order['id']) ?>"><?= htmlspecialchars($order['id']) ?></a></td>
                        <td>
                            <strong><?= htmlspecialchars($order['Username']) ?: 'Không có tên khách hàng' ?></strong><br>
                            <small class="text-muted"><?= htmlspecialchars($order['PhoneNumberCus'] ?? '') ?></small><br>
                            <small><?= htmlspecialchars($order['Pick_up_address'] ?? '') ?></small>
                        </td>
                        <td>
                            <strong><?= htmlspecialchars($order['Recipient']) ?></strong><br>
                            <small class="text-muted"><?= htmlspecialchars($order['RecipientPhone']) ?></small><br>
                            <small><?= htmlspecialchars($order['Delivery_address']) ?></small>
                        </td>
                        <td><?= htmlspecialchars($order['Created_at']) ?></td>
                        <td><?= number_format($order['COD_amount'], 0, ',', '.') . ' VNĐ' ?></td>
                        <td><?= number_format($order['Shippingfee'], 0, ',', '.') . ' VNĐ' ?></td>
                        <td>
                            <?php 
                            $statusClass = 'status-pending';
                            if (strpos(strtolower($order['Status']), 'giao') !== false) {
                                $statusClass = 'status-delivered';
                            } elseif (strpos(strtolower($order['Status']), 'đang') !== false) {
                                $statusClass = 'status-shipping';
                            } elseif (strpos(strtolower($order['Status']), 'hủy') !== false) {
                                $statusClass = 'status-cancelled';
                            }
                            ?>
                            <span class="status-badge <?= $statusClass ?>"><?= htmlspecialchars($order['Status']) ?></span>
                        </td>
                        <?php if ($_SESSION["role"] != 6): ?>
                            <td style="display: flex; gap: 10px; width: 100%; border: none;">
                                <a href="?deleteOrder&id=<?= htmlspecialchars($order['id']) ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa đơn hàng này?');" class="btn btn-danger">
                                    <i class="fas fa-trash-alt"></i> Xóa
                                </a>
                                <a href="?updateOrder&id=<?= htmlspecialchars($order['id']) ?>" class="btn btn-success">
                                    <i class="fas fa-edit"></i> Sửa
                                </a>
                                <a href="?trackOrder&id=<?= htmlspecialchars($order['id']) ?>" class="btn btn-info">
                                    <i class="fas fa-map-marker-alt"></i> Theo dõi
                                </a>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" style="text-align: center;">Không có dữ liệu đơn hàng nào.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div style="display: flex; justify-content: center; margin-top: 20px;">
        <button class="btn btn-secondary" style="margin: 0 5px;">Trước</button>
        <button class="btn" style="margin: 0 5px; background-color: #2980b9; color: white;">1</button>
        <button class="btn btn-secondary" style="margin: 0 5px;">2</button>
        <button class="btn btn-secondary" style="margin: 0 5px;">3</button>
        <button class="btn btn-secondary" style="margin: 0 5px;">Sau</button>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    const orders = <?php echo json_encode($orders); ?>;
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.getElementById('ordersTableBody');
    const userRole = <?php echo json_encode($_SESSION['role']); ?>; // Get the user's role from the PHP session.

    function getStatusClass(status) {
        status = status.toLowerCase();
        if (status.includes('giao')) return 'status-delivered';
        if (status.includes('đang')) return 'status-shipping';
        if (status.includes('hủy')) return 'status-cancelled';
        return 'status-pending';
    }

    function renderTable(data) {
        tableBody.innerHTML = '';

        if (data.length === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="8">
                        <div class="alert alert-warning text-center" role="alert">
                            Không có đơn hàng nào được tìm thấy.
                        </div>
                    </td>
                </tr>
            `;
            return;
        }

        data.forEach(order => {
            let rowHTML = `
                <tr>
                    <td><a href="?order_detail&id=${order.id}">${order.id}</a></td>
                    <td>
                        <strong>${order.Username || 'Không có tên khách hàng'}</strong><br>
                        <small class="text-muted">${order.PhoneNumberCus || ''}</small><br>
                        <small>${order.Pick_up_address || ''}</small>
                    </td>
                    <td>
                        <strong>${order.Recipient || ''}</strong><br>
                        <small class="text-muted">${order.RecipientPhone || ''}</small><br>
                        <small>${order.Delivery_address || ''}</small>
                    </td>
                    <td>${order.Created_at || ''}</td>
                    <td>${parseInt(order.COD_amount).toLocaleString('vi-VN')} VNĐ</td>
                    <td>${parseInt(order.Shippingfee).toLocaleString('vi-VN')} VNĐ</td>
                    <td><span class="status-badge ${getStatusClass(order.Status)}">${order.Status || ''}</span></td>
                    
            `;

            if (userRole != 6) { //check the role here
                rowHTML += `
                    <td class="d-flex gap-2">
                        <a href="?deleteOrder&id=${order.id}" onclick="return confirm('Bạn có chắc chắn muốn xóa đơn hàng này?');" class="btn btn-danger btn-sm">
                            <i class="fas fa-trash-alt"></i>
                        </a>
                        <a href="?updateOrder&id=${order.id}" class="btn btn-success btn-sm">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="?trackOrder&order_id=${order.id}" class="btn btn-info btn-sm">
                            <i class="fas fa-map"></i>
                        </a>
                    </td>
                `;
            }
            
            rowHTML += `</tr>`;
            tableBody.innerHTML += rowHTML;
        });
    }

    searchInput.addEventListener('input', function () {
        const keyword = this.value.toLowerCase().trim();
        const filtered = orders.filter(order =>
            order.id.toString().includes(keyword) ||
            (order.Username && order.Username.toLowerCase().includes(keyword)) ||
            (order.Delivery_address && order.Delivery_address.toLowerCase().includes(keyword)) ||
            (order.Status && order.Status.toLowerCase().includes(keyword)) ||
            (order.Shippername && order.Shippername.toLowerCase().includes(keyword)) ||
            (order.Recipient && order.Recipient.toLowerCase().includes(keyword)) ||
            (order.RecipientPhone && order.RecipientPhone.toLowerCase().includes(keyword))
        );
        renderTable(filtered);
    });

    renderTable(orders);
</script>
</body>
</html>
