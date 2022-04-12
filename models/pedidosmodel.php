<?php
    class PedidosModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function subirAdjuntos($codigo,$adjuntos){
            $countfiles = count( $adjuntos['name'] );

            for($i=0;$i<$countfiles;$i++){
                try {
                    $ext = explode('.',$adjuntos['name'][$i]);
                    $filename = uniqid().".".end($ext);
                    // Upload file
                    if (move_uploaded_file($adjuntos['tmp_name'][$i],'public/documentos/pedidos/adjuntos/'.$filename)){
                        $sql= $this->db->connect()->prepare("INSERT INTO lg_regdocumento 
                                                                    SET nidrefer=:cod,cmodulo=:mod,cdocumento=:doc,
                                                                        creferencia=:ref,nflgactivo=:est");
                        $sql->execute(["cod"=>$codigo,
                                        "mod"=>"PED",
                                        "ref"=>$filename,
                                        "doc"=>$adjuntos['name'][$i],
                                        "est"=>1]);
                    }
                    

                } catch (PDOException $th) {
                    echo "Error: ".$th->getMessage();
                    return false;
                }
            }

        }

        public function generarVistaPrevia($datos,$detalles){
            require_once('public/formatos/pedidos.php');
            
            var_dump($datos,$detalles);

        }
    }

    
?>