<?php
    class StocksModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarItems(){
            try {
                $salida = "";
                $sql = $this->db->connect()->prepare("SELECT
                                                    UPPER( tb_proyectos.cdesproy ) AS desproy,
                                                    tb_proyectos.ccodproy,
                                                    alm_existencia.idprod,
                                                    alm_existencia.codprod,
                                                    alm_existencia.serie,
                                                    FORMAT(SUM( alm_existencia.cant_ingr ),2) cantidad_ingreso,
                                                    UPPER( cm_producto.cdesprod ) AS cdesprod,
                                                    cm_producto.ccodprod,
                                                    tb_unimed.cabrevia,
                                                    UPPER( tb_almacen.cdesalm ) AS cdesalm 
                                                FROM
                                                    tb_costusu
                                                    INNER JOIN tb_proyectos ON tb_costusu.ncodproy = tb_proyectos.nidreg
                                                    INNER JOIN alm_cabexist ON tb_costusu.ncodproy = alm_cabexist.idcostos
                                                    INNER JOIN alm_existencia ON alm_cabexist.idreg = alm_existencia.idregistro
                                                    INNER JOIN cm_producto ON alm_existencia.codprod = cm_producto.id_cprod
                                                    INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed
                                                    INNER JOIN tb_almacen ON alm_existencia.idalm = tb_almacen.ncodalm 
                                                WHERE
                                                    tb_costusu.id_cuser = :user 
                                                    AND tb_proyectos.nflgactivo = 1 
                                                GROUP BY
                                                    cm_producto.ccodprod,
                                                    tb_proyectos.ccodproy,
                                                    alm_existencia.idalm");
                $sql->execute(["user"=>$_SESSION['iduser']]);

                $rowCount = $sql->rowCount();
                $item = 1;
                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()){
                        $salida.='<tr class="pointer">
                                        <td class="textoCentro">'.str_pad($item++,4,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                        <td class="pl20px">'.$rs['cdesprod'].'</td>
                                        <td class="textoCentro">'.$rs['cabrevia'].'</td>
                                        <td class="pl20px">'.$rs['cdesalm'].'</td>
                                        <td class="pl20px">'.$rs['desproy'].'</td>
                                        <td class="textoDerecha">'.$rs['cantidad_ingreso'].'</td>
                                        <td class="textoDerecha"></td>
                                        <td class="textoDerecha"></td>
                                  </tr>';
                    }
                }

                return $salida;

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage;
                return false;
            }
        }

        public function nuevoRegistro() {
            try {
                $sql = $this->db->connect()->query("SELECT MAX(idreg) AS numero FROM alm_cabexist");
                $sql->execute();

                $result = $sql->fetchAll();

                return array("numero"=>str_pad($result[0]['numero']+1,6,0,STR_PAD_LEFT));
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage;
                return false;
            }
        }

        public function grabarRegistro($cabecera,$detalles){
            try {
                /*$sql = $this->db->connect()->query("SELECT MAX(idreg) AS numero FROM alm_cabexist");
                $sql->execute();

                $result = $sql->fetchAll();

                return array("numero"=>str_pad($result[0]['numero']+1,6,0,STR_PAD_LEFT));*/
                var_dump($cabecera);
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage;
                return false;
            }
        }

        private function grabarDetalles($detalles,$indice){
            try {
                /*$sql = $this->db->connect()->query("SELECT MAX(idreg) AS numero FROM alm_cabexist");
                $sql->execute();

                $result = $sql->fetchAll();

                return array("numero"=>str_pad($result[0]['numero']+1,6,0,STR_PAD_LEFT));*/
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage;
                return false;
            }
        }
    }
?>