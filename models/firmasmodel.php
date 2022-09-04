<?php
    class FirmasModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarOrdenesFirmas(){
            try {
                 $salida = "";
                 $sql = $this->db->connect()->query("SELECT
                                                            lg_ordencab.id_regmov,
                                                            lg_ordencab.cnumero,
                                                            lg_ordencab.ffechadoc,
                                                            lg_ordencab.nNivAten,
                                                            lg_ordencab.nEstadoDoc,
                                                            lg_ordencab.ncodpago,
                                                            lg_ordencab.nplazo,
                                                            lg_ordencab.nfirmaLog,
                                                            lg_ordencab.nfirmaFin,
                                                            lg_ordencab.nfirmaOpe,
                                                            UPPER(tb_pedidocab.concepto) AS concepto,
                                                            lg_ordencab.cdocPDF,
                                                            UPPER(
                                                                    CONCAT_WS(
                                                                        ' ',
                                                                        tb_area.ccodarea,
                                                                        tb_area.cdesarea
                                                                    )
                                                                ) AS area,
                                                            UPPER(
                                                                    CONCAT_WS(
                                                                        ' ',
                                                                        tb_proyectos.ccodproy,
                                                                        tb_proyectos.cdesproy
                                                                    )
                                                                ) AS costos,
                                                            tb_proyectos.nidreg,
                                                            tb_parametros.cdescripcion AS atencion
                                                            FROM
                                                            lg_ordencab
                                                            INNER JOIN tb_pedidocab ON lg_ordencab.id_refpedi = tb_pedidocab.idreg
                                                            INNER JOIN tb_area ON lg_ordencab.ncodarea = tb_area.ncodarea
                                                            INNER JOIN tb_proyectos ON lg_ordencab.ncodpry = tb_proyectos.nidreg
                                                            INNER JOIN tb_parametros ON lg_ordencab.nNivAten = tb_parametros.nidreg
                                                            WHERE
                                                                lg_ordencab.nEstadoDoc = 59
                                                            ");
                 $sql->execute();
                 $rowCount = $sql->rowCount();
 
                 if ($rowCount > 0){
                     while ($rs = $sql->fetch()) {
 
                         $log = is_null($rs['nfirmaLog']) ? '<i class="far fa-square"></i>' : '<i class="far fa-check-square"></i>';
                         $ope = is_null($rs['nfirmaOpe']) ? '<i class="far fa-square"></i>' : '<i class="far fa-check-square"></i>';
                         $fin = is_null($rs['nfirmaFin']) ? '<i class="far fa-square"></i>' : '<i class="far fa-check-square"></i>';
 
                         $flog = is_null($rs['nfirmaLog']) ? 0 : 1;
                         $fope = is_null($rs['nfirmaOpe']) ? 0 : 1;
                         $ffin = is_null($rs['nfirmaFin']) ? 0 : 1;
 
 
                         $salida .='<tr class="pointer" data-indice="'.$rs['id_regmov'].'" 
                                                         data-estado="'.$rs['nEstadoDoc'].'"
                                                         data-finanzas="'.$ffin.'"
                                                         data-logistica="'.$flog.'"
                                                         data-operaciones="'.$fope.'">
                                     <td class="textoCentro">'.str_pad($rs['cnumero'],4,0,STR_PAD_LEFT).'</td>
                                     <td class="textoCentro">'.date("d/m/Y", strtotime($rs['ffechadoc'])).'</td>
                                     <td class="pl20px">'.$rs['concepto'].'</td>
                                     <td class="pl20px">'.utf8_decode($rs['costos']).'</td>
                                     <td class="pl20px">'.$rs['area'].'</td>
                                     <td class="textoCentro '.strtolower($rs['atencion']).'">'.$rs['atencion'].'</td>
                                     <td class="textoCentro">'.$log.'</td>
                                     <td class="textoCentro">'.$ope.'</td>
                                     <td class="textoCentro">'.$fin.'</td>
                                     </tr>';
                     }
                 }
 
                 return $salida;                    
            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }

        public function firmar($id){
            $operador = $this->obtenerOperador();
            $fecha =  date("Y-m-d");

            if ( $operador == "L" ) {
                $sql = $this->db->connect()->prepare("UPDATE lg_ordencab SET nfirmaLog=:fir,codperLog=:usr,fechaLog=:fecha WHERE id_regmov=:cod");
            }else if ($operador == "O") {
                $sql = $this->db->connect()->prepare("UPDATE lg_ordencab SET nfirmaOpe=:fir,codperOpe=:usr,fechaOpe=:fecha WHERE id_regmov=:cod");
            }else if ($operador == "F") {
                $sql = $this->db->connect()->prepare("UPDATE lg_ordencab SET nfirmaFin=:fir,codperFin=:usr,fechaFin=:fecha WHERE id_regmov=:cod");
            }

            $sql->execute(["cod"=>$id,
                            "usr"=>$_SESSION['iduser'],
                            "fir"=>1,
                            "fecha"=>$fecha]);
            
            $rowCount = $sql->rowCount();
            
            if ($rowCount > 0){
                return array("mensaje"=>"Se autorizo la orden",
                            "clase"=>"mensaje_correcto",
                            "esatdo"=>true);
            }else {
                return array("mensaje"=>"Ya autorizo la orden",
                            "clase"=>"mensaje_error",
                            "operador"=>$operador,
                            "estado"=>false);
            }
        }

        private function obtenerOperador(){
            try {
                
                $sql = $this->db->connect()->prepare("SELECT rol FROM tb_user WHERE iduser=:usr");
                $sql->execute(["usr"=>$_SESSION["iduser"]]);
                $result = $sql->fetchAll();

                return $result[0]['rol'];

            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }
    }
?>