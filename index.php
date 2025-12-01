<?php
// error_reporting(0);
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="views/img/favicon.png" type="image/png">
    <title>DALVIN</title>
<body>
    
</body>
</html>
<?php
    include_once("views/giaodien/header.php");
    if(isset($_GET["quanlyuser"])){
        include_once("views/quanlyuser/index.php");
    }elseif(isset($_GET["quanlydonhang"])){
        if($_SESSION['role'] == 6){
            include_once("views/order_of_shipper/index.php");
        }else{
            include_once("views/quanlydonhang/index.php");
        }    
    }elseif(isset($_GET["order_detail"])){
        include_once("views/order_detail/index.php");
    }elseif(isset($_GET["dashboard"])){
        include_once("views/dashboard/index.php");
    }elseif(isset($_GET["addUser"])){
        include_once("views/add_user/index.php");
    }elseif(isset($_GET["deleteUser"])){
        include_once("views/delete_user/index.php");
    }elseif(isset($_GET["updateUser"])){
        include_once("views/update_user/index.php");
    }elseif(isset($_GET["addOrder"])){
        include_once("views/add_order/index.php");
    }elseif(isset($_GET["deleteOrder"])){
        include_once("views/Delete_Order/index.php");
    }elseif(isset($_GET["updateOrder"])){
        include_once("views/update_Oder/index.php");
    }elseif(isset($_GET["login"])){
        include_once("views/login/index.php");
    }elseif(isset($_GET["logout"])){
        include_once("views/logout/index.php");
    }elseif(isset($_GET["listCustomer"])){
        include_once("views/list_customer/index.php");
    }elseif(isset($_GET["trackOrder"])){
        include_once("views/tracking_timeline/index.php");
    }elseif(isset($_GET["tracking_info"])){
        include_once("views/tracking_info/index.php");
    }elseif(isset($_GET["cod_dashboard"])){
        include_once("views/cod_dashboard/index.php");
    }elseif(isset($_GET["quanlyshipper"])){
        include_once("views/quanlyshipper/index.php");
    }elseif(isset($_GET["shipper_stats"])){
        include_once("views/shipper_stats/index.php");
    }elseif(isset($_GET["download"])){
        include_once("views/download/index.php");
    }elseif(isset($_GET["toggleUserStatus"])){  
        include_once("views/lock_user/toggle_user_status.php");   
    }elseif(isset($_GET["profile"])){
        include_once("views/profile/index.php");
    }else{
        if (!isset($_SESSION['dangnhap']) || $_SESSION['dangnhap'] != 1) {
            include_once("views/giaodien/intro.php"); 
            
        } elseif ($_SESSION['role'] == 6 || $_SESSION['role'] == 7) {
            include_once("views/giaodien/intro_app_redirect.php"); 

        } else {
            include_once("views/giaodien/intro_dashboard.php"); 
        }
    }
    // include_once("views/giaodien/footer.php");
    // include_once("views/giaodien/back_to_top.php");
    if (!isset($_GET["download"])) {
        include_once("views/giaodien/footer.php");
        include_once("views/giaodien/back_to_top.php");
    }

?>