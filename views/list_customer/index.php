<?php

include_once('controllers/cUser.php');
// Khởi tạo đối tượng và lấy dữ liệu
$p = new controlNguoiDung();
if (isset($_REQUEST['submit'])) {
    $tblSP = $p->searchUser($_REQUEST['search']); // Nếu có tìm kiếm
} else {
    $tblSP = $p->getAllCustomer(); // Nếu không thì lấy tất cả
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
        <h2 style="margin-bottom: 20px;">Danh sách khách hàng </h2>
        
        <div style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <form method="POST" action="#">
                    <input type="text" id="searchInput" name="search" placeholder="Tìm kiếm khách hàng..." class="form-control" style="width: 300px; display: inline-block;">
                    <button type="submit" class="btn btn-primary" style="margin-left: 10px;" name="submit">Tìm Kiếm</button>
                </form>
            </div>

        </div>
        
        <table>
            <thead>
                <tr>
                    <th>Mã KH</th>
                    <th>Họ Tên</th>
                    <th>SĐT</th>
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
                            <td>
                                <a href="?deleteUser&&id=<?php echo htmlspecialchars($staff['id']); ?>"
                                onclick="return confirm('Bạn có chắc chắn muốn xóa nhân viên này?');"
                                class="btn btn-danger btn-sm">
                                <i class="fas fa-trash-alt"></i> Xóa
                                </a>

                                <a href="edit_staff.php?id=<?php echo htmlspecialchars($staff['id']); ?>"
                                class="btn btn-success btn-sm">
                                <i class="fas fa-edit"></i> Sửa
                                </a>
                            </td>
                            
                            <!-- <td><?php echo htmlspecialchars($staff['note']); ?></td> -->
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" style="text-align: center;">Không có dữ liệu khách hàng nào.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <div class="text-center mb-3">
            <a href="javascript:history.back()" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
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
                            Không có khách hàng nào được tìm thấy.
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
                    <td>
                        <a href="?deleteUser&&id=${staff.id}" onclick="return confirm('Bạn có chắc chắn muốn xóa user này?');" class="btn btn-danger btn-sm" title="Xóa">
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
