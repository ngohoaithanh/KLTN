<?php
// FILE: views/list_customer/index.php (Đã nâng cấp cho giao diện mới)

include_once('controllers/cUser.php');
$p = new controlNguoiDung();

// Xử lý logic lấy dữ liệu (đã được tối ưu hóa)
$tblSP = (isset($_REQUEST['submit'])) ? $p->searchUser($_REQUEST['search']) : $p->getAllCustomer();

$customers = [];

// Hàm chuẩn hóa dữ liệu an toàn (đã có)
function toArrayOfAssoc($raw) {
    if ($raw === false || $raw === null) return [];
    if ($raw instanceof mysqli_result) {
        $out = [];
        while ($row = $raw->fetch_assoc()) {
            $out[] = $row;
        }
        return $out;
    }
    if (is_array($raw)) {
        if (isset($raw['error'])) return [];
        return array_values(array_filter($raw, 'is_array'));
    }
    return $raw;
}

$normalized = toArrayOfAssoc($tblSP);

if (is_string($normalized)) {
    echo "<div class='alert alert-danger'>Lỗi khi lấy dữ liệu người dùng: " . htmlspecialchars($normalized) . "</div>";
    $normalized = [];
}

foreach ($normalized as $row) {
    $customers[] = [
        'id'             => $row['ID']             ?? '',
        'username'       => $row['Username']       ?? '',
        'phone'          => $row['PhoneNumber']    ?? '',
        'email'          => $row['Email']          ?? '',
        'role'           => $row['RoleName']       ?? '',
        'account_status' => $row['account_status'] ?? 'active',
    ];
}
?>
<div class="container-fluid" id="staff" style="margin-top: 20px;">
<h1 class="h3 mb-4 text-gray-800">Quản Lý Khách Hàng</h1>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Danh sách khách hàng</h6>
        
        <div class="d-flex align-items-center">
            <form method="POST" action="#" class="d-inline-flex mr-3">
                <input type="text" id="searchInput" name="search" placeholder="Tìm kiếm khách hàng..." class="form-control" style="width: 300px;">
                <button type="submit" class="btn btn-primary ml-2" name="submit">Tìm</button>
            </form>
            
            <a href="?addUser&role=7" class="btn btn-success">+ Thêm Khách Hàng</a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="customerTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Mã KH</th>
                        <th>Họ Tên</th>
                        <th>SĐT</th>
                        <th>Email</th>
                        <th>Trạng thái TK</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody id="customerTableBody">
                    </tbody>
            </table>
        </div>
        
        <div style="display: flex; justify-content: center; margin-top: 20px;">
             </div>
    </div>
</div>
</div>

<script>
    // Mảng customer được server đẩy vào
    const customerList = <?php echo json_encode($customers); ?>;

    const searchInput = document.getElementById('searchInput');
    const tableBody = document.getElementById('customerTableBody');

    // === HÀM HELPER TẠO BADGE ===
    function getStatusBadge(status) {
        let badgeClass = 'badge-secondary';
        let statusText = status;
        switch (status) {
            case 'active':
                badgeClass = 'badge-success'; statusText = 'Hoạt động'; break;
            case 'locked':
                badgeClass = 'badge-danger'; statusText = 'Đã khóa'; break;
            case 'pending':
                badgeClass = 'badge-warning'; statusText = 'Chờ duyệt'; break;
        }
        return `<span class="badge ${badgeClass}">${statusText}</span>`;
    }

    // === HÀM RENDER TABLE ===
    function renderTable(data) {
        tableBody.innerHTML = ''; // Xóa sạch bảng
        if (!data || data.length === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center">
                        <div class="alert alert-warning" role="alert">
                            Không có khách hàng nào được tìm thấy.
                        </div>
                    </td>
                </tr>
            `;
            return;
        }
        data.forEach(customer => {
            const statusBadge = getStatusBadge(customer.account_status);

            // Logic cho nút Khóa/Mở khóa
            let toggleButton = '';
            if (customer.account_status == 'active') {
                toggleButton = `
                    <a href="?toggleUserStatus&id=${customer.id}&status=active&return=listCustomer" 
                       onclick="return confirm('Bạn có chắc chắn muốn KHÓA tài khoản này?');" 
                       class="btn btn-warning btn-sm" title="Khóa tài khoản">
                        <i class="fas fa-lock"></i>
                    </a>
                `;
            } else {
                toggleButton = `
                    <a href="?toggleUserStatus&id=${customer.id}&status=${customer.account_status}&return=listCustomer" 
                       onclick="return confirm('Bạn có chắc chắn muốn KÍCH HOẠT tài khoản này?');" 
                       class="btn btn-info btn-sm" title="Kích hoạt tài khoản">
                        <i class="fas fa-lock-open"></i>
                    </a>
                `;
            }

            tableBody.innerHTML += `
                <tr>
                    <td>${customer.id}</td>
                    <td>${customer.username}</td>
                    <td>${customer.phone}</td>
                    <td>${customer.email}</td>
                    <td>${statusBadge}</td>
                    <td>
                        <a href="?updateUser&id=${customer.id}" class="btn btn-success btn-sm" title="Sửa">
                            <i class="fas fa-edit"></i>
                        </a>
                        ${toggleButton}
                        <a href="?deleteUser&id=${customer.id}" onclick="return confirm('Bạn có chắc chắn muốn xóa khách hàng này?');" class="btn btn-danger btn-sm" title="Xóa">
                            <i class="fas fa-trash-alt"></i>
                        </a>
                    </td>
                </tr>
            `;
        });
    }

    // Khi gõ trong input
    searchInput.addEventListener('input', function() {
        const keyword = this.value.toLowerCase().trim();
        const filtered = customerList.filter(customer => 
            customer.username.toLowerCase().includes(keyword) ||
            customer.phone.toLowerCase().includes(keyword) ||
            customer.email.toLowerCase().includes(keyword)
        );
        renderTable(filtered);
    });

    // Ban đầu load toàn bộ danh sách
    renderTable(customerList);
</script>