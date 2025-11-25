<?php
include_once("config/callApi.php");

class modelDashboard {

    public function getSummary() {
        $url = "https://dalvin.online/api/dashboard/summary.php";
        return callApi($url, 'GET');
    }

}
?>
