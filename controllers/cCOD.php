<?php
include_once("models/mCOD.php");
class controlCOD{
    public function selectAllCod($status = null, $startDate = null, $endDate = null) {
        $p = new modelCOD();
        return $p->selectAllCod($status, $startDate, $endDate);
    }

    public function sumaryCod(){
        $p = new modelCOD();
        return $p->sumaryCod(); 
    }

    public function chartCod($timeStart, $timeEnd){
        $p = new modelCOD();
        return $p->chartCod($timeStart, $timeEnd);
    }

    public function setCodStatus($orderID, $status) {
        $p = new modelCOD();
        $kq = $p->setCodStatus($orderID, $status);
        return $kq;
    }
}
?>