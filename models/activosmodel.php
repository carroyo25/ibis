<?php
    class ActivosModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }


        public function buscarCodigos($parametros){
            try {
                $codigo = $parametros['codigo'];
                $costos = $parametros['costos'];

                $docData = [];
                $mensaje = "";
                $respuesta = false;

                $sql = $this->db->connect()->prepare("SELECT p.ccodprod,
                                                             p.id_cprod,
                                                             UPPER(p.cdesprod) descripcion,
                                                             u.cabrevia
                                                        FROM cm_producto p
                                                        INNER JOIN tb_unimed u ON u.ncodmed = p.nund
                                                        WHERE p.ccodprod = :codigo");

                $sql->execute(["codigo"=>$codigo]);
                

                while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                    $docData[] = $row;
                }

                 return array("datos"=>$docData);

            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }
    }
?>