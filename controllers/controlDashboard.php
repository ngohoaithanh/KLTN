<?php
include_once("models/mDashboard.php");

class controlDashboard {

    public function getSummary() {
        $model = new modelDashboard();
        return $model->getSummary();
    }

}
?>
