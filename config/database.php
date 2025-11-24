<?php
    class clsKetNoi {
        private $host;
        private $user;
        private $pass;
        private $db;

        public function __construct() {
            $hostName = $_SERVER['HTTP_HOST'];

            if ($hostName === 'localhost' || strpos($hostName, '192.168') === 0) {
                $this->host = 'localhost';
                $this->user = 'root';
                $this->pass = '';
                $this->db   = 'kltn';
            } else {
                $this->host = 'localhost'; 
                $this->user = 'ayyabtzn_dalvin';   
                $this->pass = 'dalvin12345@';   
                $this->db   = 'ayyabtzn_dalvin';     
            }
        }

        public function moKetNoi() {
            $con = @mysqli_connect($this->host, $this->user, $this->pass, $this->db);
            
            if (!$con) {
                echo json_encode(['success' => false, 'message' => 'Lỗi kết nối Database: ' . mysqli_connect_error()]);
                exit(); 
            }

            mysqli_set_charset($con, 'utf8mb4');
            return $con;
        }

        public function dongKetNoi($con){
            if ($con) {
                mysqli_close($con);
            }
        }
    }
