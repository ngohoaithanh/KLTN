<?php
include_once("config/database.php");
include_once("config/callApi.php");

class modelOrder{

    // Hàm dùng chung để xác định BASE URL cho API
    private function getApiBaseUrl() {
        $hostName = $_SERVER['HTTP_HOST'] ?? 'localhost';

        // Môi trường LOCAL: XAMPP / LAN
        if ($hostName === 'localhost' || strpos($hostName, '192.168.') === 0) {
            return "http://localhost/KLTN/api";
        }

        // Môi trường HOSTING
        return "https://dalvin.online/api";
    }

    public function getPaginatedOrders($page = 1, $search = null) {
        $baseUrl = $this->getApiBaseUrl();
        $url = $baseUrl . "/order/get_orders.php"; // Gọi API mới
        
        $data = [
            'page' => $page
        ];
        if ($search !== null) {
            $data['search'] = $search;
        }
        
        // Gọi API với tham số (GET)
        return $this->callApi($url, 'GET', $data);
    }

    public function selectAllOrderForShipper($shipperID) {
        $baseUrl = $this->getApiBaseUrl();
        $url = $baseUrl . "/order/order_for_shipper.php";
        return $this->callApi($url, 'GET', ["shipperID" => $shipperID]);
    }

    // Thêm đơn hàng
    public function addOrder($data) {
        $baseUrl = $this->getApiBaseUrl();
        $url = $baseUrl . "/order/add_order.php";
        $response = $this->callApi($url, 'POST', $data); 
        return $response;
    }
    
    // Cập nhật thông tin đơn hàng
    public function updateOrder($data) {
        $baseUrl = $this->getApiBaseUrl();
        $url = $baseUrl . "/order/update_order.php"; // API cập nhật đơn hàng
        return $this->callApi($url, 'POST', $data);
    }

    // Xóa đơn hàng
    public function deleteOrder($id) {
        $baseUrl = $this->getApiBaseUrl();
        $url = $baseUrl . "/order/delete_order.php"; // API xóa đơn hàng
        return $this->callApi($url, 'POST', ['id' => $id]);
    }

    // Helper function for calling API to avoid repetition
    private function callApi($url, $method, $data = null) {
        return callApi($url, $method, $data);
    }

    public function getOrderById($id) {
        $db = new clsKetNoi();
        $conn = $db->moKetNoi();

        // Cập nhật câu truy vấn SQL để lấy thêm trường 'CustomerEmail' từ bảng users
        $stmt = $conn->prepare("
            SELECT o.*, 
                   u.Username AS FullName, 
                   u.PhoneNumber, 
                   u.Email AS CustomerEmail, 
                   s.Username AS ShipperName
            FROM orders o
            JOIN users u ON o.CustomerID = u.ID
            LEFT JOIN users s ON o.ShipperID = s.ID
            WHERE o.ID = ?
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        $order = null;
        if ($result && $result->num_rows > 0) {
            $order = $result->fetch_assoc();
        }

        $stmt->close();
        $db->dongKetNoi($conn);
        return $order;
    }

    public function searchOrderByID($keyword) {
        $baseUrl = $this->getApiBaseUrl();
        $url = $baseUrl . "/order/search_order.php";
        return $this->callApi($url, 'GET', ["keyword" => $keyword]);
    }

    public function setShipper($shipperID, $orderID){
        // Hàm này thao tác trực tiếp DB, giữ nguyên
        $p = new clsKetNoi();
        $sql = "UPDATE orders SET ShipperID = $shipperID WHERE ID = $orderID";
        $con = $p->moKetNoi();
        $kq = $con->query($sql);
        $p->dongKetNoi($con);
        return $kq;    
    }

    public function setOrderStatus($orderID, $status){
        // Hàm này thao tác trực tiếp DB, giữ nguyên
        $p = new clsKetNoi();
        $sql = "UPDATE orders SET Status = '$status' WHERE ID = $orderID";
        $con = $p->moKetNoi();
        $kq = $con->query($sql);
        $p->dongKetNoi($con);
        return $kq;    
    }
}
?>