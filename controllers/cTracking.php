<?php
include_once("models/mTracking.php");
class controlTracking{
    public function getAllTrackingByOrderID($id) {
        $p = new modelTracking();
        return $p->selectTrackingByOrderID($id);
    }

    public function addTrackingTimeline($orderId, $status, $time) {
        $p = new modelTracking();
        $kq = $p->addTrackingTimeline($orderId, $status, $time);
        return $kq;
    }

}
?>