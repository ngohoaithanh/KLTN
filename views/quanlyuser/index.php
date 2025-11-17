<?php
// FILE: views/quanlyuser/index.php (Đã nâng cấp cho giao diện mới)

if (!isset($_SESSION["dangnhap"]) || ($_SESSION["role"] != 1 && $_SESSION["role"] != 2)) {
    echo "<script>alert('Bạn không có quyền truy cập!');</script>";
    // Chuyển hướng về index2.php (giao diện mới)
    echo "<script>window.location.href = 'index2.php';</script>"; 
    exit();
}

include_once('controllers/cUser.php');
// Khởi tạo đối tượng và lấy dữ liệu
$p = new controlNguoiDung();
if (isset($_REQUEST['submit'])) {
    $tblSP = $p->searchUser($_REQUEST['search']); // Nếu có tìm kiếm
} else {
    $tblSP = $p->getAllUser(); // Nếu không thì lấy tất cả
}

// Xử lý dữ liệu
$staffs = [];
if (is_array($tblSP)) {
    foreach ($tblSP as $row) {
        $staffs[] = [
            'id' => $row['ID'],
            'username' => $row['Username'],
            'phone' => $row['PhoneNumber'],
            'email' => $row['Email'],
            'role' => $row['RoleName'],
            'account_status' => $row['account_status'],
        ];
    }
} else {
    // Debug lỗi nếu có
    echo "<div class='alert alert-danger'>Lỗi khi lấy dữ liệu người dùng: " . htmlspecialchars($tblSP) . "</div>";
}
?>

<!-- ============================================= -->
<!-- === PHẦN NỘI DUNG (ĐÃ XÓA HTML/HEAD/BODY) === -->
<!-- ============================================= -->

<!-- Tiêu đề trang (sử dụng style của template mới) -->
<div class="container-fluid" id="staff" style="margin-top: 20px;">
<h1 class="h3 mb-4 text-gray-800">Quản Lý Nhân Viên</h1>

<!-- Bảng dữ liệu (sử dụng card-shadow của template mới) -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Danh sách nhân viên</h6>
        
        <div class="d-flex align-items-center">
            <!-- Form tìm kiếm -->
            <form method="POST" action="#" class="d-inline-flex mr-3">
                <input type="text" id="searchInput" name="search" placeholder="Tìm kiếm nhân viên..." class="form-control" style="width: 300px;">
                <button type="submit" class="btn btn-primary ml-2" name="submit">Tìm</button>
            </form>
            
            <a href="?listCustomer" class="btn btn-outline-primary mr-2">DS Khách hàng</a>
            <a href="?addUser" class="btn btn-success">+ Thêm Mới</a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="staffTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Mã NV</th>
                        <th>Họ Tên</th>
                        <th>SĐT</th>
                        <th>Email</th>
                        <th>Chức vụ</th>
                        <th>Tài khoản</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody id="staffTableBody">
                    <!-- JavaScript sẽ render nội dung vào đây -->
                </tbody>
            </table>
            
            <!-- Phần phân trang (giữ nguyên) -->
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
</div>

<!-- ============================================= -->
<!-- === SCRIPT CỦA RIÊNG TRANG NÀY === -->
<!-- (Không cần include jQuery/Bootstrap nữa vì footer2.php đã có) -->
<!-- ============================================= -->
<script>
    // Mảng staff được server đẩy vào (PHP => JavaScript)
    const staffList = <?php echo json_encode($staffs); ?>;

    const searchInput = document.getElementById('searchInput');
    const tableBody = document.getElementById('staffTableBody');

    // Hàm tạo badge Trạng thái Tài khoản
    function getAccountStatusBadge(status) {
        let badgeClass = 'badge-secondary'; // Mặc định
        let statusText = status;

        switch (status) {
            case 'active':
                badgeClass = 'badge-success';
                statusText = 'Hoạt động';
                break;
            case 'locked':
                badgeClass = 'badge-danger';
                statusText = 'Đã khóa';
                break;
            case 'pending':
                badgeClass = 'badge-warning';
                statusText = 'Chờ duyệt';
                break;
        }
        
        return `<span class="badge ${badgeClass}">${statusText}</span>`;
    }

    // Hàm render bảng
    function renderTable(data) {
        tableBody.innerHTML = ''; // Xóa sạch bảng
        if (!data || data.length === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center">
                        <div class="alert alert-warning" role="alert">
                            Không có nhân viên nào được tìm thấy.
                        </div>
                    </td>
                </tr>
            `;
            return;
        }
        data.forEach(staff => {
            const statusBadge = getAccountStatusBadge(staff.account_status);
            
            // Logic cho nút Khóa/Mở khóa
            let toggleButton = '';
            if (staff.account_status == 'active') {
                toggleButton = `<a href="?toggleUserStatus&id=${staff.id}&status=active&return=quanlyuser" onclick="return confirm('Bạn có chắc chắn muốn KHÓA tài khoản này?');" class="btn btn-warning btn-sm" title="Khóa tài khoản"><i class="fas fa-lock"></i></a>`;
            } else {
                toggleButton = `<a href="?toggleUserStatus&id=${staff.id}&status=${staff.account_status}&return=quanlyuser" onclick="return confirm('Bạn có chắc chắn muốn KÍCH HOẠT tài khoản này?');" class="btn btn-info btn-sm" title="Kích hoạt tài khoản"><i class="fas fa-lock-open"></i></a>`;
            }

            tableBody.innerHTML += `
                <tr>
                    <td>${staff.id}</td>
                    <td>${staff.username}</td>
                    <td>${staff.phone}</td>
                    <td>${staff.email}</td>
                    <td>${staff.role}</td>
                    <td>${statusBadge}</td>
                    <td>
                        <a href="?updateUser&id=${staff.id}" class="btn btn-success btn-sm" title="Sửa">
                            <i class="fas fa-edit"></i>
                        </a>
                        ${toggleButton}
                        <a href="?deleteUser&id=${staff.id}" onclick="return confirm('Bạn có chắc chắn muốn xóa nhân viên này?');" class="btn btn-danger btn-sm" title="Xóa">
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
        const filtered = staffList.filter(staff => 
            staff.username.toLowerCase().includes(keyword) ||
            staff.phone.toLowerCase().includes(keyword) ||
            staff.email.toLowerCase().includes(keyword) ||
            staff.role.toLowerCase().includes(keyword)
        );
        renderTable(filtered);
    });

    // Ban đầu load toàn bộ danh sách
    renderTable(staffList);
</script>