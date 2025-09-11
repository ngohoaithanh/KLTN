<?php

include_once('controllers/cOrder.php');
include_once('config/database.php');

$p = new controlOrder();
$db = new clsKetNoi();
$conn = $db->moKetNoi();


$sql_warehouses = "SELECT ID, Name FROM warehouses WHERE operation_status ='active'";
$result_warehouses = $conn->query($sql_warehouses);
$role = $_SESSION['Role'] ?? 'customer'; // 'employee' hoặc 'customer'

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm Đơn Hàng</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="add-staff-form" style="margin-top: 50px;">
    <h2 class="add-staff-title">Thêm Đơn Hàng Mới</h2>
    <form method="POST" action= 'api/order/add_order.php' id="orderForm">
        <!-- Các input ẩn bắt buộc -->
       
    <!-- <form method="POST"> -->
    <!-- Các input ẩn bắt buộc -->
    <input type="hidden" name="CustomerID" value="0">
    <input type="hidden" name="ShipperID" value="1">
    <input type="hidden" name="Status" value="pending">

    <div class="form-group-add">
        <label>Tên Khách Hàng</label>
        <input type="text" name="CustomerName" class="form-control-add" required>
    </div>

    <div class="form-group-add">
        <label>Số Điện Thoại (Phone Number)</label>
        <input type="text" name="PhoneNumber" class="form-control-add" required pattern="[0-9]{10,11}" title="Số điện thoại phải có 10 hoặc 11 chữ số">
    </div>

    <div class="form-group-add">
        <label>Địa Chỉ Lấy Hàng (Pick Up Address)</label>
        <input type="text" name="Pick_up_address" class="form-control-add" required>
    </div>

    <div class="form-group-add">
        <label>Địa Chỉ Giao Hàng (Delivery Address)</label>
        <input type="text" name="Delivery_address" class="form-control-add" required>
    </div>

    <div class="form-group-add">
        <label>Người Nhận Hàng (Recipient)</label>
        <input type="text" name="Recipient" class="form-control-add" required>
    </div>

    <div class="form-group-add">
        <label>Số Điện Thoại Người Nhận (Recipient Phone)</label>
        <input type="text" name="RecipientPhone" class="form-control-add" required pattern="[0-9]{10,11}" title="Số điện thoại phải có 10 chữ số">
    </div>

    <div class="form-group-add">
        <label>Trọng Lượng (Weight)</label>
        <input type="number" step="0.01" name="Weight" class="form-control-add" required>
    </div>

    <div class="form-group-add">
        <label>Tiền COD (COD_amount)</label>
        <input type="number" step="1000" name="COD_amount" class="form-control-add" required>
    </div>

 
    <!-- <div class="form-group-add">
        <label>Kho (Warehouse)</label>
        <select name="WarehouseID" class="form-control-add" required>
            <option value="">-- Chọn kho --</option>
            <?php
            if ($result_warehouses && $result_warehouses->num_rows > 0) {
                while ($row = $result_warehouses->fetch_assoc()) {
                    echo "<option value='" . $row['ID'] . "'>" . $row['Name'] . "</option>";
                }
            } else {
                echo "<option disabled>Không có kho nào</option>";
            }
            ?>
        </select>
    </div> -->

    <?php date_default_timezone_set('Asia/Ho_Chi_Minh'); ?>
    <div class="form-group-add">
        <label>Ngày Tạo (Create_at)</label>
        <input type="text" name="Create_at" class="form-control-add" value="<?php echo date('Y-m-d H:i:s'); ?>" readonly>
    </div>

    <div class="form-group-add">
        <label>Ghi Chú</label>
        <textarea name="Note" rows="4" class="form-control-add" placeholder="Nhập ghi chú nếu có..."></textarea>
    </div>

    <div class="button-group-add">
        <button type="submit" name="btn-them" class="btn-add btn btn-primary">Thêm Đơn Hàng</button>
        <a href="?quanlydonhang" class="btn-back btn btn-secondary">Quay lại</a>
    </div>
</form>
</div>
</body>
</html>

<!-- Gộp validate và gửi form -->
<script>
document.getElementById('orderForm').addEventListener('submit', async function (e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);
    
    // Validate số điện thoại
    const phoneRegex = /^[0-9]{10,11}$/;
    const senderPhone = formData.get("PhoneNumber")?.trim();
    const recipientPhone = formData.get("RecipientPhone")?.trim();

    if (!phoneRegex.test(senderPhone)) {
        alert("SĐT người gửi không hợp lệ (10-11 số).");
        return;
    }
    if (!phoneRegex.test(recipientPhone)) {
        alert("SĐT người nhận không hợp lệ (10-11 số).");
        return;
    }

    // Gửi dữ liệu
    try {
        const response = await fetch('api/order/add_order.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.text();

        if (response.ok) {
            alert("✅ Thêm đơn hàng thành công!\n" );
            form.reset();
            window.location.href = '?quanlydonhang';
        } else {
            alert("❌ Lỗi từ server: " + result);
        }
    } catch (error) {
        alert("❌ Kết nối thất bại: " + error.message);
    }
});
</script>


