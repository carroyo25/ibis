<?php
    class CombustibleModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function consultarCodigo($codigo){
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                            cm_producto.ccodprod,
                                                            cm_producto.id_cprod,
                                                            UPPER(cm_producto.cdesprod) AS cdesprod,
                                                            tb_unimed.cdesmed 
                                                        FROM
                                                            cm_producto
                                                            INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed
                                                        WHERE
                                                            cm_producto.ccodprod =:codigo");
                
                $sql->execute(['codigo' => $codigo]);

                $rowCount = $sql->rowCount();
                
                if ($rowCount) {
                    while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                        $docData[] = $row;
                    }
                }

                return array("datos"=>$docData);
                
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function buscarDocumento($doc) {
            $registrado = false;

            $url = "http://sicalsepcon.net/api/activesapi.php?documento=".$doc;
            
            $api = file_get_contents($url);

            $datos =  json_decode($api);
            $nreg = count($datos);

            $registrado = $nreg > 0 ? true: false;

            return array("datos" => $datos, "registrado"=>$registrado);
        }

        public function registrarCombustible($datos){
            try {
                //code...
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }
    }
?>