<?php
include_once("config/database.php");
include_once("config/callApi.php");
class modelCOD{
    public function selectAllCod($status = null, $startDate = null, $endDate = null) {
        $p = new clsKetNoi();
        $con = $p->moKetNoi();
        
        $sql = "SELECT 
                    o.ID AS order_id,
                    o.Created_at AS order_date,
                    u.Username AS customer_name,
                    u.PhoneNumber AS customer_phone,
                    COALESCE(s.Username, 'Chưa phân công') AS shipper_name,
                    COALESCE(s.PhoneNumber, 'N/A') AS shipper_phone,
                    c.Amount AS cod_amount,
                    c.Status AS cod_status,
                    c.Settled_at AS settled_date,
                    o.Delivery_address,
                    o.Status AS order_status,
                    w.Name
                FROM cods c
                JOIN orders o ON c.OrderID = o.ID
                JOIN users u ON o.CustomerID = u.ID
                LEFT JOIN users s ON o.ShipperID = s.ID
                JOIN warehouses w on w.ID =o.WarehouseID
                WHERE 1=1";
        
        // Thêm điều kiện filter
        if ($status && $status != 'all') {
            $sql .= " AND c.Status = '" . $con->real_escape_string($status) . "'";
        }
        
        if ($startDate) {
            $sql .= " AND c.Settled_at >= '" . $con->real_escape_string($startDate) . "'";
        }
        
        if ($endDate) {
            $sql .= " AND c.Settled_at <= '" . $con->real_escape_string($endDate) . " 23:59:59'";
        }
        
        $sql .= " ORDER BY o.Created_at DESC";
        
        $kq = $con->query($sql);
        $p->dongKetNoi($con);
        return $kq;  
    }

    public function sumaryCod(){
        $p = new clsKetNoi();
            $con = $p->moKetNoi();
            $sql = "SELECT 
                        SUM(CASE WHEN c.Status = 'pending' THEN c.Amount ELSE 0 END) AS total_pending,
                        SUM(CASE WHEN c.Status = 'collected' THEN c.Amount ELSE 0 END) AS total_collected,
                        SUM(CASE WHEN c.Status = 'settled' THEN c.Amount ELSE 0 END) AS total_settled,
                        COUNT(CASE WHEN c.Status = 'pending' THEN 1 END) AS count_pending,
                        COUNT(CASE WHEN c.Status = 'collected' THEN 1 END) AS count_collected,
                        COUNT(CASE WHEN c.Status = 'settled' THEN 1 END) AS count_settled
                    FROM cods c
                    JOIN orders o ON c.OrderID = o.ID
                    WHERE o.Status NOT IN ('cancelled', 'returned');";
            $kq = $con->query($sql);
            $p->dongKetNoi($con);
            return $kq;  
    }

    public function chartCod($timeStart, $timeEnd){
        $p = new clsKetNoi();
            $con = $p->moKetNoi();
            $sql = "SELECT 
                        DATE(c.Settled_at) AS date,
                        SUM(c.Amount) AS total_amount,
                        COUNT(*) AS order_count
                    FROM cods c
                    WHERE c.Status = 'settled'
                        AND c.Settled_at BETWEEN $timeStart AND $timeEnd
                    GROUP BY DATE(c.Settled_at)
                    ORDER BY DATE(c.Settled_at);";
            $kq = $con->query($sql);
            $p->dongKetNoi($con);
            return $kq;  
    }

    public function setCodStatus($orderID, $status){
        $p = new clsKetNoi();
        $sql = "UPDATE cods set Status = '$status' where OrderID = $orderID";
        $con = $p->moKetNoi();
        $kq = $con->query($sql);
        $p->dongKetNoi($con);
        return $kq;    
    }
}

?>