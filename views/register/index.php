<?php
if(isset($_SESSION["dangnhap"])){
    header("refresh: 0; url=index.php");
}

include_once('controllers/cUser.php');
$p = new controlNguoiDung();

if (isset($_POST['submit'])) {
    $username = trim($_POST['username']);
    $phone    = trim($_POST['phone']);
    $email    = trim($_POST['email']);
    $role     = 6;
    $password = trim($_POST['password']);

    

    $errors = [];

    if (empty($username)) $errors[] = "Họ tên không được để trống.";
    if (empty($phone)) $errors[] = "Số điện thoại không được để trống.";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email không hợp lệ.";
    if (empty($password)) $errors[] = "Mật khẩu không được để trống.";

    if (count($errors) > 0) {
        foreach ($errors as $error) {
            echo "<script>alert('$error');</script>";
        }
    } else {
        $data = [
            'username' => $username,
            'phone'    => $phone,
            'email'    => $email,
            'role'     => $role,
            'password' => $password
        ];
        $result = $p->addUser($data);

        if ($result && isset($result['success']) && $result['success']) {
            echo "<script>alert('Đăng kí tài khoản thành công!'); window.location.href='?login';</script>";
            exit();
        } else {
            $errorMessage = isset($result['error']) ? $result['error'] : 'Không rõ nguyên nhân';
            echo "<script>alert('Đăng ký tài khoản thất bại: $errorMessage');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng ký</title>
    <link rel="stylesheet" href="../views/css/style.css">
</head>
<body>

<div class="login-wrapper">
    <form class="login-form" method="POST" id="registerForm" onsubmit="return validatePasswords()">
        <h2 class="login-title">Đăng ký tài khoản</h2>

        <?php if (isset($error)): ?>
            <div class="login-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="login-group">
            <label for="username">Tên người dùng:</label>
            <input type="text" name="username" id="username" required placeholder="Nhập tên người dùng">
        </div>

        <div class="login-group">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required placeholder="Nhập email">
        </div>

        <div class="login-group">
            <label for="password">Mật khẩu:</label>
            <input type="password" name="password" id="password" required placeholder="Nhập mật khẩu">
        </div>

        <div class="login-group">
            <label for="confirm_password">Xác nhận mật khẩu:</label>
            <input type="password" name="confirm_password" id="confirm_password" required placeholder="Nhập lại mật khẩu">
        </div>

        <div class="login-group">
            <label for="phonenumber">Số điện thoại:</label>
            <input type="tel" name="phone" id="phone" required placeholder="Nhập số điện thoại">
        </div>

        <div class="login-group">
            <button type="submit" name="submit">Đăng ký</button>
        </div>
    </form>
</div>

<script>
function validatePasswords() {
    const pass = document.getElementById("password").value;
    const confirm = document.getElementById("confirm_password").value;

    if (pass !== confirm) {
        alert("Mật khẩu và xác nhận mật khẩu không khớp.");
        return false;
    }
    return true;
}

// Kiểm tra mật khẩu trùng khớp trong khi gõ
document.addEventListener('DOMContentLoaded', () => {
    const passInput = document.getElementById("password");
    const confirmInput = document.getElementById("confirm_password");

    const errorDiv = document.createElement("div");
    errorDiv.id = "passwordMatchMessage";
    errorDiv.style.color = "red";
    errorDiv.style.fontSize = "14px";
    errorDiv.style.marginTop = "6px";
    confirmInput.parentNode.appendChild(errorDiv);

    function checkMatch() {
        const pass = passInput.value;
        const confirm = confirmInput.value;

        if (!confirm) {
            errorDiv.textContent = "";
            return;
        }

        if (pass !== confirm) {
            errorDiv.textContent = "❌ Mật khẩu không khớp";
        } else {
            errorDiv.textContent = "✅ Mật khẩu khớp";
            errorDiv.style.color = "green";
        }
    }

    passInput.addEventListener("input", checkMatch);
    confirmInput.addEventListener("input", checkMatch);

    // Cuộn đến form
    document.getElementById('registerForm').scrollIntoView({ behavior: 'smooth', block: 'center' });
});
</script>

</body>
</html>
