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

        function grabarGuiaManual($guia,$form,$detalles,$operacion){
            $mensaje = "error de creacion";
            $guiaAutomatica = "";

            try {
                if ( $operacion == 'n' ){
                    $guiaAutomatica = $this->numeroGuia();
                    $mensaje = "Se grabo la guia de remision";

                }else if( $operacion == 'u' ){
                    $mensaje = "Se actualizo la guia de remision";
                }

                return array("mensaje"=>$mensaje,"guia"=>$guiaAutomatica);

            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }
    }
?>