<?php
include_once("models/mUser.php");
class controlNguoiDung{
    public function getAllUser() {
        $p = new modelNguoiDung();
        return $p->selectAllUser();
    }

    public function getAllCustomer() {
        $p = new modelNguoiDung();
        return $p->selectAllCustomer();
    }

    public function searchUser($keyword) {
        $p = new modelNguoiDung();
        return $p->searchUserByName($keyword);
    }

    public function addUser($data) {
        $p = new modelNguoiDung();
        return $p->addUser($data);
    }
    
    public function deleteUser($id) {
        $p = new modelNguoiDung();
        return $p->deleteUser($id);
        // return $result;
    }

    public function getUserById($id) {
        $model = new modelNguoiDung();
        return $model->getUserById($id);
    }

    public function updateUser($data) {
        $p = new modelNguoiDung();
        return $p->updateUser($data);
    }

    public function loginUser($data) {
        $p = new modelNguoiDung();
        return $p->loginUser($data);
    }
    
    public function getUserByRole($role) {
        $model = new modelNguoiDung();
        return $model->getUserByRole($role);
    }

    public function getShipperByWarehouseID($warehouseID) {
        $p = new modelNguoiDung();
        $kq = $p->getShipperByWarehouseID($warehouseID);
        return $kq;
    }

    public function getRandomShipperByWarehouseID($warehouseID) {
        $p = new modelNguoiDung();
        $kq = $p->getShipperByWarehouseID($warehouseID);
        
        $shippers = array();
        if ($kq && $kq->num_rows > 0) {
            while($row = $kq->fetch_assoc()) {
                $shippers[] = $row['ID'];
            }
            return $shippers[array_rand($shippers)];
        }
        return null;
    }
}
?>