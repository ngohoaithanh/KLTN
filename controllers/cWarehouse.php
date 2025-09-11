<?php
include_once("models/mWarehouse.php");
class controlWarehouse{
    public function getAllWarehouse() {
        $p = new modelWarehouse();
        return $p->selectAllWarehouse();
    }

    public function getFilteredWarehouse($operation_status, $search) {
        $p = new modelWarehouse();
        return $p->filterWarehouse($operation_status, $search);
    }

    public function getWarehouseDetail($id) {
        $p = new modelWarehouse();
        return $p->getWarehouseDetail($id);
    }
    
    public function importSingleOrder($id) {
        $p = new modelWarehouse();
        return $p->importSingleOrder($id);
    }

    public function exportSingleOrder($id) {
        $p = new modelWarehouse();
        return $p->exportSingleOrder($id);
    }

    public function getWarehouseByID($id) {
        $p = new modelWarehouse();
        return $p->getWarehouseByID($id);
    }

    public function addWarehouse($data) {
        $p = new modelWarehouse();
        return $p->addWarehouse($data);
    }

    public function updateWarehouse($data) {
        $p = new modelWarehouse();
        return $p->updateWarehouse($data);
    }

    public function getQuantity($warehouseID) {
        $p = new modelWarehouse();
        $kq = $p->getQuantity($warehouseID);
        return $kq;
    }

    public function setQuantity($warehouseID, $newQUantity) {
        $p = new modelWarehouse();
        $kq = $p->setQuantity($warehouseID, $newQUantity);
        return $kq;
    }

    // public function getWarehouseStatus($warehouseID) {
    //     $p = new modelWarehouse();
    //     $result = $p->checkWarehouseFull($warehouseID)->fetch_assoc();
        
    //     if (!$result) return null;
        
    //     $occupancyRate = $result['occupancy_rate'] ?? 0;
        
    //     if ($result['Quantity'] >= $result['Capacity']) {
    //         $p->updateStatusWarehouse($warehouseID, 'full');
    //         $status = 'Đầy 100%';
    //         $badgeClass = 'bg-danger';
    //     } elseif ($occupancyRate >= 80) {
    //         $p->updateStatusWarehouse($warehouseID, 'active');
    //         $status = 'Gần đầy ('.round($occupancyRate, 2).'%)';
    //         $badgeClass = 'bg-warning';
    //     } else {
    //         $p->updateStatusWarehouse($warehouseID, 'active');
    //         $status = 'Bình thường ('.round($occupancyRate, 2).'%)';
    //         $badgeClass = 'bg-success';
    //     }
        
    //     return [
    //         'status_text' => $status,
    //         'badge_class' => $badgeClass,
    //         'quantity' => $result['Quantity'],
    //         'capacity' => $result['Capacity']
    //     ];
    // }

    public function getWarehouseStatus($warehouseID) {
    $p = new modelWarehouse();
    $result = $p->checkWarehouseFull($warehouseID)->fetch_assoc();
    
    // Di chuyển kiểm tra $result lên đầu để tránh lỗi khi $result null
    if (!$result) return null;
    
    $occupancyRate = $result['occupancy_rate'] ?? 0;
    $currentStatus = $result['operation_status'] ?? 'active';
    
    // Xác định trạng thái mới
    if ($result['Quantity'] >= $result['Capacity']) {
        $newStatus = 'full';
        $statusText = 'Đầy 100%';
        $badgeClass = 'bg-danger';
    } elseif ($occupancyRate >= 80) {
        $newStatus = 'active';
        $statusText = 'Gần đầy ('.round($occupancyRate, 2).'%)';
        $badgeClass = 'bg-warning';
    } else {
        $newStatus = 'active';
        $statusText = 'Bình thường ('.round($occupancyRate, 2).'%)';
        $badgeClass = 'bg-success';
    }
    
    // Chỉ update nếu trạng thái thay đổi
    if ($currentStatus !== $newStatus) {
        $p->updateStatusWarehouse($warehouseID, $newStatus);
    }
    
    return [
        'status_text' => $statusText,
        'badge_class' => $badgeClass,
        'quantity' => $result['Quantity'],
        'capacity' => $result['Capacity'],
        'operation_status' => $newStatus
    ];
}

}
?>