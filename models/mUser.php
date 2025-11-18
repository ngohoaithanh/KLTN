<?php
include_once("config/database.php");
include_once("config/callApi.php");
class modelNguoiDung{
    public function getUsers($page = 1, $search = null) {
        $url = "http://localhost/KLTN/api/user/user.php";
        
        $data = [
            'page' => $page
        ];
        if ($search !== null) {
            $data['search'] = $search;
        }
        
        // Gọi API với tham số (GET)
        return callApi($url, 'GET', $data);
    }

    public function selectAllCustomer() {
        $url = "http://localhost/KLTN/api/user/customer.php";
        return callApi($url, 'GET');
    }

    // public function searchUserByName($keyword) {
    //     $url = "http://localhost/KLTN/api/user/search_user.php";
    //     $response = callApi($url, 'GET', ["keyword" => $keyword]);
    //     if (!is_array($response)) {
    //         return [];
    //     }

    //     if (isset($response['error'])) {
    //         return []; 
    //     }

    //     return $response;
    // }
    
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

        // $stmt = $conn->prepare("SELECT * FROM users WHERE ID = ?");
        $stmt = $conn->prepare("
            SELECT 
                u.*, 
                v.license_plate, 
                v.model AS vehicle_model, 
                v.type AS vehicle_type
            FROM 
                users u
            LEFT JOIN 
                vehicles v ON u.ID = v.shipper_id AND v.is_active = 1
            WHERE 
                u.ID = ?
        ");
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

    public function updateShipperVehicle($shipper_id, $license_plate, $vehicle_model) {
        $db = new clsKetNoi();
        $conn = $db->moKetNoi();

        // 1. Kiểm tra xem shipper đã có xe trong bảng chưa
        $stmt_check = $conn->prepare("SELECT id FROM vehicles WHERE shipper_id = ?");
        $stmt_check->bind_param("i", $shipper_id);
        $stmt_check->execute();
        $result = $stmt_check->get_result();

        if ($result->num_rows > 0) {
            // Nếu đã có -> CẬP NHẬT
            $stmt_update = $conn->prepare("UPDATE vehicles SET license_plate = ?, model = ? WHERE shipper_id = ?");
            $stmt_update->bind_param("ssi", $license_plate, $vehicle_model, $shipper_id);
            $success = $stmt_update->execute();
        } else {
            // Nếu chưa có -> THÊM MỚI
            $stmt_insert = $conn->prepare("INSERT INTO vehicles (shipper_id, license_plate, model) VALUES (?, ?, ?)");
            $stmt_insert->bind_param("iss", $shipper_id, $license_plate, $vehicle_model);
            $success = $stmt_insert->execute();
        }
        
        $db->dongKetNoi($conn);
        return $success;
    }

    public function addShipperVehicle($shipper_id, $license_plate, $vehicle_model) {
        $db = new clsKetNoi();
        $conn = $db->moKetNoi();

        $stmt_insert = $conn->prepare(
            "INSERT INTO vehicles (shipper_id, license_plate, model, is_active) VALUES (?, ?, ?, 1)"
        );
        $stmt_insert->bind_param("iss", $shipper_id, $license_plate, $vehicle_model);
        $success = $stmt_insert->execute();
        $stmt_insert->close();
        
        $db->dongKetNoi($conn);
        return $success;
    }

    public function updateUserStatus($id, $new_status) {
        $url = "http://localhost/KLTN/api/user/update_status.php";
        $data = ["id" => $id, "new_status" => $new_status];
        return callApi($url, 'POST', $data); 
    }
}

?>