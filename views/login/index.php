<style>
    .login-footer-friendly {
        background-color: #f8f9fa; /* Màu nền xám rất nhạt */
        padding: 15px;
        border-radius: 8px;
        margin-top: 20px;
        border: 1px solid #eee;
    }
    .login-footer-friendly p {
        margin: 0;
        font-size: 14px;
        color: #0780f9ff; /* Màu xám đậm hơn */
    }
    .login-footer-friendly i.fa-sun {
        color: var(--warning-color); /* Màu vàng cam */
        margin-right: 5px;
    }
</style>
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION["dangnhap"])) {
    header("refresh: 0; url=index.php");
}

include_once('controllers/cUser.php');
$p = new controlNguoiDung();

if (isset($_POST['submit'])) {
    $data = [
        'phonenumber' => $_POST['phonenumber'] ?? '',
        'password'    => $_POST['password'] ?? ''
    ];
    $result = $p->loginUser($data);

    if ($result['success']) {
        $_SESSION['dangnhap'] = 1;
        $_SESSION['user']     = $result['user']['Username'];
        $_SESSION['user_id']  = $result['user']['ID'];
        $_SESSION['role']     = $result['user']['Role'];
        $_SESSION['email']     = $result['user']['Email'];
        $_SESSION['login_success'] = "Đăng nhập thành công!";
        echo "<script>alert('{$_SESSION['login_success']}');</script>";
        header("Location: index.php");
        exit();
    } else {
        $error = $result['message'];
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập</title>
    <link rel="stylesheet" href="views/css/style.css">
</head>
<body>

<div class="login-wrapper">
    <form class="login-form" method="POST" id="loginForm">
        <h2 class="login-title">Đăng nhập hệ thống</h2>

        <?php if (isset($error)): ?>
            <div class="login-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="login-group">
            <label for="phonenumber">Số điện thoại:</label>
            <input
                type="tel"
                name="phonenumber"
                id="phonenumber"
                required
                inputmode="numeric"
                pattern="0[0-9]{9}"
                placeholder="Nhập số điện thoại (10 số, bắt đầu bằng 0)"
            >
        </div>

        <div class="login-group">
            <label for="password">Mật khẩu:</label>
            <input type="password" name="password" id="password" required placeholder="Nhập mật khẩu">
        </div>

        <div class="login-group">
            <button type="submit" name="submit">Đăng nhập</button>
        </div>
        <div class="login-group text-center login-footer-friendly">
            <p>
                <i class="fas fa-sun"></i> 
                <i>Chúc bạn có một ngày làm việc hiệu quả!</i>
            </p>
        </div>
    </form>
</div>

<script>
    // Cuộn mượt tới form đăng nhập khi trang load
    window.addEventListener('DOMContentLoaded', function () {
        const loginForm = document.getElementById('loginForm');
        if (loginForm) {
            loginForm.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
</script>
</body>
</html>
