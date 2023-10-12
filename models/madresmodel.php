<?php
    class MadresModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function importarGuias($cc,$guia){
            try {
                $this->db->connect()->prepare("");
                $salida = "";

                return $salida;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }

        }
    }
?>