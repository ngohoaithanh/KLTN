<?php
include_once("models/mOrder.php");
class controlOrder {
    public function getPaginatedOrders($page = 1, $search = null) {
        $p = new modelOrder();
        return $p->getPaginatedOrders($page, $search);
    }

    public function getAllOrderForShipper($shipperID) {
        $p = new modelOrder();
        return $p->selectAllOrderForShipper($shipperID);
    }

    public function searchOrderById($keyword) {
        $p = new modelOrder();
        return $p->searchOrderByID($keyword);
    }

    public function addOrder($data) {
        $p = new modelOrder();
        return $p->addOrder($data);
    }

    public function deleteOrder($id) {
        $p = new modelOrder();
        return $p->deleteOrder($id);
    }

    public function getOrderById($id) {
        $p = new modelOrder();
        return $p->getOrderById($id);
    }

    public function updateOrder($data) {
        $p = new modelOrder();
        return $p->updateOrder($data);
    }

    public function setShipper($shipperID, $orderID) {
        $p = new modelOrder();
        $kq = $p->setShipper($shipperID, $orderID);
        return $kq;
    }

    public function setOrderStatus($orderID, $status) {
        $p = new modelOrder();
        $kq = $p->setOrderStatus($orderID, $status);
        return $kq;
    }

}
