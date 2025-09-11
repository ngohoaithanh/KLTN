<?php
    class clsKetNoi{
        public function moKetNoi(){
            return mysqli_connect("localhost","root", "", "qlgh");
        }

        public function dongKetNoi($con){
            mysqli_close($con);
        }
    }

?>