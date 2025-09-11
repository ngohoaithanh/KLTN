<?php
include_once("config/database.php");
include_once("config/callApi.php");
class modelTracking{
    public function selectTrackingByOrderID($id) {
        $url = "http://localhost/KLTN/api/tracking/get_tracking_timeline.php";
        return callApi($url, 'GET', ["order_id" => $id]);
    }

    public function addTrackingTimeline($orderId, $status, $time){
        $p = new clsKetNoi();
        $sql = "insert into trackings (OrderID, Status, Updated_at) values ($orderId, '$status', '$time')";
        $con = $p->moKetNoi();
        $kq = $con->query($sql);
        $p->dongKetNoi($con);
        return $kq;
        
    }
    

}

?>