<?php

if (!isset($_SESSION["dangnhap"]) || ($_SESSION["role"] != 1 && $_SESSION["role"] != 2)) {
    echo "<script>alert('Bạn không có quyền truy cập!');</script>";
    header("refresh:0; url=index.php");
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
            'note' => $row['Note'] ?? '',
            'warehouse_name' => $row['warehouse_name'] ?? '',
        ];
    }
} else {
    // Debug lỗi nếu có
    echo "<div class='alert alert-danger'>Lỗi khi lấy dữ liệu người dùng: " . htmlspecialchars($tblSP) . "</div>";
}

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Nhân Viên</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <div class="container" id="staff" style="margin-top: 40px;">
        <h2 style="margin-bottom: 20px;">Quản Lý Nhân Viên </h2>
        
        <div style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <form method="POST" action="#">
                    <input type="text" id="searchInput" name="search" placeholder="Tìm kiếm nhân viên..." class="form-control" style="width: 300px; display: inline-block;">
                    <button type="submit" class="btn btn-primary" style="margin-left: 10px;" name="submit">Tìm Kiếm</button>
                </form>
            </div>

            <a href="?listCustomer" class="btn btn-outline-primary">Danh sách khách hàng</a>
            <a href="?addUser" class="btn btn-success">+ Thêm Nhân Viên Mới</a>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>Mã NV</th>
                    <th>Họ Tên</th>
                    <th>SĐT</th>
                    <th>Email</th>
                    <th>Chức vụ</th>
                    <th>Nơi làm việc</th>
                    <th>Thao tác</th>
                    <!-- <th>Note</th> -->
                </tr>
            </thead>
            <tbody id="staffTableBody">
                <?php if (!empty($staffs)): ?>
                    <?php foreach ($staffs as $staff): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($staff['id']); ?></td>
                            <td><?php echo htmlspecialchars($staff['username']); ?></td>
                            <td><?php echo htmlspecialchars($staff['phone']); ?></td>
                            <td><?php echo htmlspecialchars($staff['email']); ?></td>
                            <td><?php echo htmlspecialchars($staff['role']); ?></td>
                            <td><?php echo htmlspecialchars($staff['warehouse_name']); ?></td>
                            <td>
                                <a href="?deleteUser&&id=<?php echo htmlspecialchars($staff['id']); ?>" 
                                onclick="return confirm('Bạn có chắc chắn muốn xóa nhân viên này?');" 
                                class="btn btn-danger btn-sm" title="Xóa">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                                <a href="edit_staff.php?id=<?php echo htmlspecialchars($staff['id']); ?>" 
                                class="btn btn-success btn-sm" title="Sửa">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" style="text-align: center;">Không có dữ liệu shipper nào.</td>
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

    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<script>
    // Mảng staff được server đẩy vào (PHP => JavaScript)
    const staffList = <?php echo json_encode($staffs); ?>;

    const searchInput = document.getElementById('searchInput');
    const tableBody = document.getElementById('staffTableBody');

    // Hàm render table
    function renderTable(data) {
        tableBody.innerHTML = ''; // Xóa sạch bảng
        if (data.length === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="6">
                        <div class="alert alert-warning text-center" role="alert">
                            Không có nhân viên nào được tìm thấy.
                        </div>
                    </td>
                </tr>
            `;
            return;
        }
        data.forEach(staff => {
            tableBody.innerHTML += `
                <tr>
                    <td>${staff.id}</td>
                    <td>${staff.username}</td>
                    <td>${staff.phone}</td>
                    <td>${staff.email}</td>
                    <td>${staff.role}</td>
                    <td>${staff.warehouse_name}</td>
                    <td>
                        <a href="?deleteUser&&id=${staff.id}" onclick="return confirm('Bạn có chắc chắn muốn xóa nhân viên này?');" class="btn btn-danger btn-sm" title="Xóa">
                            <i class="fas fa-trash-alt"></i>
                        </a>
                        <a href="?updateUser&&id=${staff.id}" class="btn btn-success btn-sm" title="Sửa">
                            <i class="fas fa-edit"></i>
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
