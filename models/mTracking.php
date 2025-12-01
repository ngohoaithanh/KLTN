<?php
include_once("config/database.php");
include_once("config/callApi.php");

class modelTracking{
    private function getApiBaseUrl() {
        $hostName = $_SERVER['HTTP_HOST'] ?? 'localhost';
        if ($hostName === 'localhost' || strpos($hostName, '192.168.') === 0) {
            return "http://localhost/KLTN/api";
        }
        return "https://dalvin.online/api";
    }

    public function selectTrackingByOrderID($id) {
        $baseUrl = $this->getApiBaseUrl();
        $url = $baseUrl . "/tracking/get_tracking_timeline.php";

        return callApi($url, 'GET', ["order_id" => $id]);
    }

    public function addTrackingTimeline($orderId, $status, $time){
        $p = new clsKetNoi();
        $sql = "INSERT INTO trackings (OrderID, Status, Updated_at) 
                VALUES ($orderId, '$status', '$time')";
        $con = $p->moKetNoi();
        $kq = $con->query($sql);
        $p->dongKetNoi($con);
        return $kq;
    }
}
?>