<?php
include_once('controllers/cUser.php');
$p = new controlNguoiDung();

if (isset($_REQUEST['submit'])) {
    $tblSP = $p->searchUser($_REQUEST['search']); 
} else {
    $tblSP = $p->getAllCustomer(); 
}

$customers = [];

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
        'id'             => $row['ID']          ?? '',
        'username'       => $row['Username']    ?? '',
        'phone'          => $row['PhoneNumber'] ?? '',
        'email'          => $row['Email']       ?? '',
        'role'           => $row['RoleName']    ?? '',
        'account_status' => $row['account_status'] ?? 'active',
    ];
}
?>


<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Khách Hàng</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <div class="container" id="staff" style="margin-top: 40px;">
        <h2 style="margin-bottom: 20px;">Danh sách khách hàng </h2>
        
        <div style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <form method="POST" action="#">
                    <input type="text" id="searchInput" name="search" placeholder="Tìm kiếm khách hàng..." class="form-control" style="width: 300px; display: inline-block;">
                    <button type="submit" class="btn btn-primary" style="margin-left: 10px;" name="submit">Tìm Kiếm</button>
                </form>
            </div>
            <a href="?addUser&role=7" class="btn btn-success">+ Thêm Khách Hàng</a>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>Mã KH</th>
                    <th>Họ Tên</th>
                    <th>SĐT</th>
                    <th>Email</th>
                    <th>Trạng thái TK</th> <th>Thao tác</th>
                </tr>
            </thead>
            <tbody id="customerTableBody"> </tbody>
        </table>
        
        <div style="display: flex; justify-content: center; margin-top: 20px;">
            </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<script>
    // Mảng customer được server đẩy vào
    const customerList = <?php echo json_encode($customers); ?>;

    const searchInput = document.getElementById('searchInput');
    const tableBody = document.getElementById('customerTableBody');

    // === HÀM HELPER TẠO BADGE (Giống hệt trang quanlyuser) ===
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

    // === HÀM RENDER TABLE ĐÃ NÂNG CẤP ===
    function renderTable(data) {
        tableBody.innerHTML = ''; // Xóa sạch bảng
        if (data.length === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="6"> <div class="alert alert-warning text-center" role="alert">
                            Không có khách hàng nào được tìm thấy.
                        </div>
                    </td>
                </tr>
            `;
            return;
        }
        data.forEach(customer => {
            // Lấy badge trạng thái
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
                    <td>${statusBadge}</td> <td>
                        <a href="?deleteUser&&id=${customer.id}" onclick="return confirm('Bạn có chắc chắn muốn xóa khách hàng này?');" class="btn btn-danger btn-sm" title="Xóa">
                            <i class="fas fa-trash-alt"></i>
                        </a>
                        <a href="?updateUser&&id=${customer.id}" class="btn btn-success btn-sm" title="Sửa">
                            <i class="fas fa-edit"></i>
                        </a>
                        ${toggleButton} </td>
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