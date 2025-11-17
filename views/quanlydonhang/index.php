<?php
// FILE: views/quanlydonhang/index.php (Đã nâng cấp cho giao diện mới)

if (!isset($_SESSION["dangnhap"]) || ($_SESSION["role"] != 3 && $_SESSION["role"] != 6 && $_SESSION["role"] !=1 && $_SESSION["role"] !=2)) {
    echo "<script>alert('Bạn không có quyền truy cập!');</script>";
    echo "<script>window.location.href = 'index2.php';</script>"; // Chuyển về index2.php
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
            'CODFee' => $row['CODFee'],
            'PhoneNumberCus' => $row['PhoneNumberCus'],
            'Pick_up_address' => $row['Pick_up_address'],
            'Shippingfee' => $row['Shippingfee'] ?? 0
        ];
    }
}
?>
<!-- <div class="container-fluid"> -->
<div class="container-fluid" id="staff" style="margin-top: 20px;">
<h1 class="h3 mb-4 text-gray-800">Quản Lý Đơn Hàng</h1>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Danh sách đơn hàng</h6>
        
        <div class="d-flex align-items-center">
            <form method="POST" action="#" class="d-inline-flex mr-3">
                <input type="text" id="searchInput" name="search" placeholder="Tìm kiếm đơn hàng..." class="form-control" style="width: 300px;">
                <button type="submit" class="btn btn-primary ml-2" name="submit">Tìm</button>
            </form>
            
            <?php if ($_SESSION["role"] != 6): // Shipper không thể tự tạo đơn ?>
                <a href="?addOrder" class="btn btn-success">+ Thêm Đơn Hàng</a>
            <?php endif; ?>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="ordersTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Mã đơn</th>
                        <th>Bên gửi</th>
                        <th>Bên nhận</th>
                        <th>Ngày tạo</th>
                        <th>COD</th>
                        <th>Phí VC</th>
                        <th>Phí COD</th>
                        <th>Trạng thái</th>
                        <?php if ($_SESSION["role"] != 6): ?>
                            <th>Thao tác</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody id="ordersTableBody">
                    </tbody>
            </table>
        </div>
        
        <div style="display: flex; justify-content: center; margin-top: 20px;">
            <button class="btn btn-secondary" style="margin: 0 5px;">Trước</button>
            <button class="btn btn-primary" style="margin: 0 5px;">1</button>
            <button class="btn btn-secondary" style="margin: 0 5px;">2</button>
            <button class="btn btn-secondary" style="margin: 0 5px;">3</button>
            <button class="btn btn-secondary" style="margin: 0 5px;">Sau</button>
        </div>
    </div>
</div>
</div>

<script>
    // Mảng orders được server đẩy vào
    const ordersList = <?php echo json_encode($orders); ?>;
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.getElementById('ordersTableBody');
    const userRole = <?php echo json_encode($_SESSION['role']); ?>;

    // Hàm helper để tạo badge trạng thái (Dùng class của Bootstrap)
    function getStatusBadge(status) {
        let badgeClass = 'badge-secondary'; // Mặc định
        let statusText = status;

        if (!status) return `<span class="badge badge-light">N/A</span>`;
        status = status.toLowerCase();

        if (status.includes('delivered')) {
            badgeClass = 'badge-success'; statusText = 'Đã giao';
        } else if (status.includes('in_transit') || status.includes('picked_up')) {
            badgeClass = 'badge-info'; statusText = 'Đang giao';
        } else if (status.includes('accepted')) {
            badgeClass = 'badge-primary'; statusText = 'Đã nhận';
        } else if (status.includes('pending')) {
            badgeClass = 'badge-warning'; statusText = 'Chờ xử lý';
        } else if (status.includes('delivery_failed') || status.includes('cancelled') || status.includes('returned')) {
            badgeClass = 'badge-danger'; 
            if(status.includes('failed')) statusText = 'Giao thất bại';
            if(status.includes('cancelled')) statusText = 'Đã hủy';
            if(status.includes('returned')) statusText = 'Hoàn trả';
        }
        
        return `<span class="badge ${badgeClass}">${statusText}</span>`;
    }
    
    // Hàm định dạng tiền tệ
    function formatCurrency(number) {
        return parseInt(number || 0).toLocaleString('vi-VN') + ' VNĐ';
    }

    // Hàm render bảng
    function renderTable(data) {
        tableBody.innerHTML = ''; // Xóa sạch bảng
        
        // Tính colspan dựa trên vai trò
        const colspan = (userRole != 6) ? 9 : 8; 

        if (!data || data.length === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="${colspan}" class="text-center">
                        <div class="alert alert-warning" role="alert">
                            Không có đơn hàng nào được tìm thấy.
                        </div>
                    </td>
                </tr>
            `;
            return;
        }

        data.forEach(order => {
            const statusBadge = getStatusBadge(order.Status);

            let rowHTML = `
                <tr>
                    <td><a href="?order_detail&id=${order.id}">${order.id}</a></td>
                    <td>
                        <strong>${order.Username || 'Không có'}</strong><br>
                        <small class="text-muted">${order.PhoneNumberCus || ''}</small><br>
                        <small>${order.Pick_up_address || ''}</small>
                    </td>
                    <td>
                        <strong>${order.Recipient || ''}</strong><br>
                        <small class="text-muted">${order.RecipientPhone || ''}</small><br>
                        <small>${order.Delivery_address || ''}</small>
                    </td>
                    <td>${order.Created_at || ''}</td>
                    <td>${formatCurrency(order.COD_amount)}</td>
                    <td>${formatCurrency(order.Shippingfee)}</td>
                    <td>${formatCurrency(order.CODFee)}</td>
                    <td>${statusBadge}</td>
            `;

            if (userRole != 6) { //check the role here
                rowHTML += `
                    <td>
                        <div class="d-flex">
                            <a href="?deleteOrder&id=${order.id}" onclick="return confirm('Bạn có chắc chắn muốn xóa đơn hàng này?');" class="btn btn-danger btn-sm mr-1" title="Xóa">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                            <a href="?updateOrder&id=${order.id}" class="btn btn-success btn-sm mr-1" title="Sửa">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="?trackOrder&order_id=${order.id}" class="btn btn-info btn-sm" title="Theo dõi">
                                <i class="fas fa-map-marker-alt"></i> </a>
                        </div>
                    </td>
                `;
            }
            
            rowHTML += `</tr>`;
            tableBody.innerHTML += rowHTML;
        });
    }

    // Khi gõ trong input (tìm kiếm)
    searchInput.addEventListener('input', function () {
        const keyword = this.value.toLowerCase().trim();
        const filtered = ordersList.filter(order =>
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

    // Ban đầu load toàn bộ danh sách
    renderTable(ordersList);
</script>