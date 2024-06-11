<?php
    class CombustibleModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarConsumos(){
            
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
                $sql=$this->db->connect()->prepare("INSERT INTO alm_combustible 
                                                    SET alm_combustible.fregistro=:fecha,
                                                        alm_combustible.idalm=:idalmacen,
                                                        alm_combustible.idtipo=:tipo,
                                                        alm_combustible.idprod=:producto,
                                                        alm_combustible.ncantidad=:cantidad,
                                                        alm_combustible.tobseritem=:obseritem,
                                                        alm_combustible.cdocumento=:nrodoc,
                                                        alm_combustible.idusuario=:usuario,
                                                        alm_combustible.idproyecto=:proyecto,
                                                        alm_combustible.cguia=:guia,
                                                        alm_combustible.tobserdocum=:obserdoc,
                                                        alm_combustible.nidref=:referencia,
                                                        alm_combustible.idarea=:area");
                $sql->execute([
                    "fecha"=>$datos['fechaRegistro'],
                    "idalmacen"=>$datos['almacen'],
                    "tipo"=>$datos['tipo'],
                    "producto"=>$datos['codigo_producto'],
                    "cantidad"=>$datos['cantidad'],
                    "obseritem"=>strtoupper($datos['observacionesItem']),
                    "nrodoc"=>$datos['documento'],
                    "usuario"=>$datos['usuario'],
                    "proyecto"=>$datos['proyecto'],
                    "guia"=>$datos['guia'],
                    "obserdoc"=>strtoupper($datos['observacionesDocumento']),
                    "referencia"=>$datos['referencia'],
                    "area"=>$datos['area']]);

                return array("mensaje"=>'Consumo registrado');

            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }
    }
?>