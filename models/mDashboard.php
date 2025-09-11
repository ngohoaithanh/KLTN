<?php
include_once("config/callApi.php");

class modelDashboard {

    public function getSummary() {
        $url = "http://localhost/CNMoi/api/dashboard/summary.php";
        return callApi($url, 'GET');
    }

}
?>
