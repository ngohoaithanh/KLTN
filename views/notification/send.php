<?php
    if (!isset($_SESSION["dangnhap"]) || ($_SESSION["role"] !=1 && $_SESSION["role"] !=2)) {
        echo "<script>alert('Bạn không có quyền truy cập!');</script>";
        echo "<script>window.location.href = 'index.php';</script>";
        exit();
    }
?>
<div class="container-fluid" style="margin-top: 20px;">
    <h1 class="h3 mb-4 text-gray-800 text-center">Gửi Thông Báo Hệ Thống</h1>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Soạn thông báo mới</h6>
                </div>
                <div class="card-body">
                    <form id="send-notif-form">
                        
                        <div class="form-group">
                            <label>Tiêu đề thông báo</label>
                            <input type="text" id="notif-title" class="form-control" placeholder="VD: Bảo trì hệ thống..." required>
                        </div>

                        <div class="form-group">
                            <label>Nội dung chi tiết</label>
                            <textarea id="notif-message" class="form-control" rows="5" placeholder="Nhập nội dung thông báo..." required></textarea>
                        </div>

                        <div class="form-group">
                            <label>Gửi đến ai?</label>
                            <select id="notif-target" class="form-control">
                                <option value="individual">Một người dùng cụ thể (Theo ID)</option>
                                <option value="all_shippers">Toàn bộ Shipper</option>
                                <option value="all_customers">Toàn bộ Khách hàng</option>
                            </select>
                        </div>

                        <div class="form-group" id="user-id-group">
                            <label>ID Người nhận</label>
                            <input type="number" id="notif-user-id" class="form-control" placeholder="Nhập ID User...">
                            <small class="text-muted">Bạn có thể tìm ID trong trang Quản lý User.</small>
                        </div>

                        <hr>
                        <button type="button" class="btn btn-primary btn-icon-split" id="btn-send">
                            <span class="icon text-white-50"><i class="fas fa-paper-plane"></i></span>
                            <span class="text">Gửi Thông Báo Ngay</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Lịch sử gửi gần đây</h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <?php
                        include_once('config/database.php'); // Đảm bảo include đúng
                        $db_hist = new clsKetNoi();
                        $conn_hist = $db_hist->moKetNoi();
                        $sql_hist = "SELECT Title, Created_at FROM notifications WHERE Type = 'system' ORDER BY ID DESC LIMIT 5";
                        $result_hist = $conn_hist->query($sql_hist);
                        
                        if ($result_hist && $result_hist->num_rows > 0) {
                            while ($row = $result_hist->fetch_assoc()) {
                                $time = date('H:i d/m', strtotime($row['Created_at']));
                                echo '
                                <div class="list-group-item px-0">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1 font-weight-bold text-gray-800" style="font-size: 0.9rem;">' . htmlspecialchars($row['Title']) . '</h6>
                                        <small class="text-muted">' . $time . '</small>
                                    </div>
                                    <small class="text-gray-500"><i class="fas fa-check-double text-success mr-1"></i> Đã gửi</small>
                                </div>';
                            }
                        } else {
                            echo '<div class="text-center text-gray-500 py-3">Chưa có thông báo nào.</div>';
                        }
                        $db_hist->dongKetNoi($conn_hist);
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const targetSelect = document.getElementById('notif-target');
        const userIdGroup = document.getElementById('user-id-group');
        const btnSend = document.getElementById('btn-send'); // Nút Gửi
        const form = document.getElementById('send-notif-form');

        // Xử lý ẩn/hiện ô nhập ID
        targetSelect.addEventListener('change', function() {
            if (this.value === 'individual') {
                userIdGroup.style.display = 'block';
            } else {
                userIdGroup.style.display = 'none';
            }
        });

        // === SỬA LỖI: Bắt sự kiện CLICK vào nút thay vì SUBMIT form ===
        btnSend.addEventListener('click', async function() {
            
            // 1. Validate dữ liệu thủ công
            const title = document.getElementById('notif-title').value.trim();
            const message = document.getElementById('notif-message').value.trim();
            const userId = document.getElementById('notif-user-id').value.trim();

            if (!title) { alert('Vui lòng nhập tiêu đề!'); return; }
            if (!message) { alert('Vui lòng nhập nội dung!'); return; }
            
            if (targetSelect.value === 'individual' && !userId) {
                alert('Vui lòng nhập ID người nhận!');
                return;
            }

            if(!confirm('Bạn có chắc chắn muốn gửi thông báo này không?')) return;

            // 2. Xử lý gửi
            const originalText = btnSend.innerHTML;
            btnSend.disabled = true;
            btnSend.innerHTML = '<span class="text">Đang gửi...</span>';

            try {
                const payload = {
                    title: title,
                    message: message,
                    target_type: targetSelect.value,
                    user_id: userId
                };

                const response = await fetch('api/notification/send.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify(payload)
                });

                const result = await response.json();

                if (result.success) {
                    alert(result.message);
                    form.reset(); 
                    targetSelect.value = 'individual';
                    userIdGroup.style.display = 'block';
                    // Tải lại trang sau 1s để cập nhật lịch sử
                    setTimeout(() => location.reload(), 1000);
                } else {
                    alert('Lỗi: ' + result.error);
                }

            } catch (error) {
                console.error(error);
                alert('Có lỗi xảy ra khi gửi. Vui lòng kiểm tra Console.');
            } finally {
                btnSend.disabled = false;
                btnSend.innerHTML = originalText;
            }
        });
    });
</script>