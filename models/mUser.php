<?php
include_once("config/database.php");
include_once("config/callApi.php");
class modelNguoiDung{
    public function selectAllUser() {
        $url = "http://localhost/KLTN/api/user/user.php";
        return callApi($url, 'GET');
    }

    public function selectAllCustomer() {
        $url = "http://localhost/KLTN/api/user/customer.php";
        return callApi($url, 'GET');
    }

    public function searchUserByName($keyword) {
        $url = "http://localhost/KLTN/api/user/search_user.php";
        $response = callApi($url, 'GET', ["keyword" => $keyword]);
        if (!is_array($response)) {
            return [];
        }

        if (isset($response['error'])) {
            return []; 
        }

        return $response;
    }
    
    public function addUser($data) {
        $url = "http://localhost/KLTN/api/user/add_user.php";
        return callApi($url, 'POST', $data);
    }

    public function deleteUser($id) {
        $url = "http://localhost/KLTN/api/user/delete_user.php";
        return callApi($url, "POST", ["id" => $id]);
    }

    public function getUserById($id) {
        include_once("config/database.php");
        $db = new clsKetNoi();
        $conn = $db->moKetNoi();

        $stmt = $conn->prepare("SELECT * FROM users WHERE ID = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        $user = null;
        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();
        }

        $stmt->close();
        $db->dongKetNoi($conn);
        return $user;
    }

    public function updateUser($data) {
        $url = "http://localhost/KLTN/api/user/update_user.php";
        return callApi($url, 'POST', $data);
    }

    public function loginUser($data) {
        $url = "http://localhost/KLTN/api/user/login_user.php";
        return callApi($url, 'POST', $data);
    }
    
    public function getUserByRole($role) {
        $url = "http://localhost/KLTN/api/user/get_user_by_role.php";
        return callApi($url, 'GET', ["role" => $role]);
    }

    public function getShipperByWarehouseID($warehouseID){
        $p = new clsKetNoi();
        $sql = "SELECT * FROM users WHERE Role = 6 AND warehouse_id=$warehouseID";
        $con = $p->moKetNoi();
        $kq = $con->query($sql);
        $p->dongKetNoi($con);
        return $kq;  
    }
}

?>