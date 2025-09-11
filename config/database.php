<?php
    class clsKetNoi{
        public function moKetNoi(){
            return mysqli_connect("localhost","root", "", "kltn");
        }

        public function dongKetNoi($con){
            mysqli_close($con);
        }
    }

?>