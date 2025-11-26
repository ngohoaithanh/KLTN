<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION = array();

    session_destroy();

    echo "<script>
        alert('Đăng xuất thành công!');
        window.location.href = 'index.php?login';
    </script>";
exit();
?>