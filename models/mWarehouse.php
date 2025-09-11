<?php
include_once("config/database.php");
include_once("config/callApi.php");
class modelWarehouse{
    public function selectAllWarehouse() {
        $url = "http://localhost/CNMoi/api/warehouse/get_all_warehouses.php";
        return callApi($url, 'GET');
    }

    public function filterWarehouse($operation_status, $search) {
        $url = "http://localhost/CNMoi/api/warehouse/filter_search.php?operation_status=" . urlencode($operation_status) . "&search=" . urlencode($search);
        return callApi($url, 'GET');
    }
    public function getWarehouseDetail($id) {
        $url = "http://localhost/CNMoi/api/warehouse/get_warehouse_detail.php";
        return callApi($url, 'POST', ["id" => $id]);
    }

    public function importSingleOrder($id) {
        $url = "http://localhost/CNMoi/api/warehouse/import_single_order.php";
        return callApi($url, 'POST', ["id" => $id]);
    }

    public function exportSingleOrder($id) {
        $url = "http://localhost/CNMoi/api/warehouse/export_single_order.php";
        return callApi($url, 'POST', ["id" => $id]);
    }

    public function getWarehouseByID($id) {
        $url = "http://localhost/CNMoi/api/warehouse/get_warehouse_by_id.php";
        return callApi($url, 'GET', ["id" => $id]);
    }

    public function addWarehouse($data) {
        $url = "http://localhost/CNMoi/api/warehouse/add_warehouse.php";
        return callApi($url, 'POST', $data);
    }

    public function updateWarehouse($data) {
        $url = "http://localhost/CNMoi/api/warehouse/update_warehouse.php";
        return callApi($url, 'POST', $data);
    }

    public function getQuantity($warehouseID){
        $p = new clsKetNoi();
        $sql = "SELECT COUNT(*) AS total_orders
                FROM orders
                WHERE WarehouseID = $warehouseID and Status='in_warehouse';";
        $con = $p->moKetNoi();
        $kq = $con->query($sql);
        $p->dongKetNoi($con);
        return $kq;    
    }

    public function setQuantity($warehouseID, $newQUantity){
        $p = new clsKetNoi();
        $sql = "UPDATE warehouses SET Quantity = $newQUantity WHERE ID = $warehouseID;";
        $con = $p->moKetNoi();
        $kq = $con->query($sql);
        $p->dongKetNoi($con);
        return $kq;    
    }

    public function checkWarehouseFull($warehouseID){
        $p = new clsKetNoi();
        $sql = "SELECT 
                Quantity, 
                Capacity,
                (Quantity / Capacity) * 100 AS occupancy_rate
            FROM warehouses 
            WHERE ID = $warehouseID";
        $con = $p->moKetNoi();
        $kq = $con->query($sql);
        $p->dongKetNoi($con);
        return $kq;    
    }

    public function updateStatusWarehouse($warehouseID, $status) {
        $p = new clsKetNoi();
        $con = $p->moKetNoi();
        
        $sql = "UPDATE warehouses SET operation_status = ? WHERE ID = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("si", $status, $warehouseID);
        $kq = $stmt->execute();
        
        $stmt->close();
        $p->dongKetNoi($con);
        return $kq;
    }
}
?>