
<?php
    class ConsumoModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function buscarDatos($doc) {
            $registrado = false;
            $url = "http://sicalsepcon.net/api/activesapi.php?documento=".$doc;
            $api = file_get_contents($url);
            
            $datos =  json_decode($api);
            $nreg = count($datos);

            $registrado = $nreg > 0 ? true: false;

            return array("datos" => $datos,
                        "registrado"=>$registrado);
        }

        public function buscarProductos($codigo){
            try {
                $salida = "";
                $sql = $this->db->connect()->prepare("SELECT
                                                        cm_producto.id_cprod,
                                                        cm_producto.ccodprod,
                                                        cm_producto.cdesprod,
                                                        tb_unimed.cabrevia,
                                                        NOW() AS fecha
                                                    FROM
                                                        cm_producto
                                                        INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed 
                                                    WHERE
                                                        cm_producto.flgActivo = 1 
                                                        AND cm_producto.ccodprod = :codigo 
                                                        AND cm_producto.ntipo = 37");
                $sql->execute(["codigo"=>$codigo]);

                $rowCount = $sql->rowCount();
                $result = $sql->fetchAll();

                if ($rowCount > 0) {
                    $respuesta = array("descripcion"=>$result[0]['cdesprod'],
                                        "codigo"=>$result[0]['ccodprod'],
                                        "unidad"=>$result[0]['cabrevia'],
                                        "idprod"=>$result[0]['id_cprod'],
                                        "fecha"=>$result[0]['fecha'],
                                        "registrado"=>true);
                }else{
                    $respuesta = array("registrado"=>false); 
                }

                return $respuesta;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }
    }
?>