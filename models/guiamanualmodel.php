<?php
    class GuiaManualModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarGuiasManuales(){
            
        }

        public function nuevonumeroguia(){
            $guiaAutomatica = $this->numeroGuia();
            $mensaje = "numero de guia creado";

            return array("mensaje"=>$mensaje,"guia"=>$guiaAutomatica); 
        }
    }
?>