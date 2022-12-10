<?php
    class OrdenSegModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarOrdenesSeguimiento($user){
            try {
                 $salida = "";
                 $sql = $this->db->connect()->prepare("SELECT
                                                         tb_costusu.ncodcos,
                                                         tb_costusu.ncodproy,
                                                         tb_costusu.id_cuser,
                                                         lg_ordencab.id_regmov,
                                                         lg_ordencab.cnumero,
                                                         lg_ordencab.ffechadoc,
                                                         lg_ordencab.nNivAten,
                                                         lg_ordencab.nEstadoDoc,
                                                         lg_ordencab.ncodpago,
                                                         lg_ordencab.nplazo,
                                                         lg_ordencab.cdocPDF,
                                                         UPPER( tb_pedidocab.concepto ) AS concepto,
                                                         UPPER( tb_pedidocab.detalle ) AS detalle,
                                                         UPPER(
                                                         CONCAT_WS(' ', tb_area.ccodarea, tb_area.cdesarea )) AS area,
                                                         UPPER(
                                                         CONCAT_WS(' ', tb_proyectos.ccodproy, tb_proyectos.cdesproy )) AS costos,
                                                         lg_ordencab.nfirmaLog,
                                                         lg_ordencab.nfirmaFin,
                                                         lg_ordencab.nfirmaOpe,
                                                         tb_parametros.cdescripcion AS atencion 
                                                     FROM
                                                         tb_costusu
                                                         INNER JOIN lg_ordencab ON tb_costusu.ncodproy = lg_ordencab.ncodpry
                                                         INNER JOIN tb_pedidocab ON lg_ordencab.id_refpedi = tb_pedidocab.idreg
                                                         INNER JOIN tb_area ON lg_ordencab.ncodarea = tb_area.ncodarea
                                                         INNER JOIN tb_proyectos ON lg_ordencab.ncodpry = tb_proyectos.nidreg
                                                         INNER JOIN tb_parametros ON lg_ordencab.nNivAten = tb_parametros.nidreg 
                                                     WHERE
                                                         tb_costusu.id_cuser = :user 
                                                         AND tb_costusu.nflgactivo = 1");
                 $sql->execute(["user"=>$_SESSION['iduser']]);
                 $rowCount = $sql->rowCount();
 
                 if ($rowCount > 0){
                     while ($rs = $sql->fetch()) {
 
                         $flog = is_null($rs['nfirmaLog']) ? 0 : 1;
                         $fope = is_null($rs['nfirmaOpe']) ? 0 : 1;
                         $ffin = is_null($rs['nfirmaFin']) ? 0 : 1;
                         $estado = "Emitido";

                         $resaltado = $rs['nEstadoDoc'] == 59 ? "resaltado_firma" :  "";

                         if($flog && $fope && $ffin){
                            $estado = "Aprobado";
                         }
 
                         $salida .='<tr class="pointer '.$resaltado.'" data-indice="'.$rs['id_regmov'].'" 
                                                         data-estado="'.$rs['nEstadoDoc'].'"
                                                         data-finanzas="'.$ffin.'"
                                                         data-logistica="'.$flog.'"
                                                         data-operaciones="'.$fope.'">
                                     <td class="textoCentro">'.str_pad($rs['cnumero'],4,0,STR_PAD_LEFT).'</td>
                                     <td class="textoCentro">'.date("d/m/Y", strtotime($rs['ffechadoc'])).'</td>
                                     <td class="pl20px">'.$rs['concepto'].'</td>
                                     <td class="pl20px">'.utf8_decode($rs['costos']).'</td>
                                     <td class="pl20px">'.$rs['area'].'</td>
                                     <td class="textoCentro">'.$estado.'</td>
                                     </tr>';
                     }
                 }
 
                 return $salida;                    
            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }

        public function consultarDetalles($id){
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                        lg_ordencab.id_refpedi, 
                                                        lg_ordencab.id_regmov, 
                                                        DATE_FORMAT(lg_ordencab.ffechadoc,'%d/%m/%Y') AS emision,
                                                        DATE_FORMAT(lg_ordencab.ffechaent,'%d/%m/%Y') AS envio,
                                                        lg_ordencab.id_cuser, 
                                                        DATE_FORMAT(lg_ordencab.fechaLog,'%d/%m/%Y') AS fecha_logistica, 
                                                        DATE_FORMAT(lg_ordencab.fechaOpe,'%d/%m/%Y') AS fecha_operaciones, 
                                                        DATE_FORMAT(lg_ordencab.FechaFin,'%d/%m/%Y') AS fecha_finanzas, 
                                                        lg_ordencab.nfirmaLog, 
                                                        lg_ordencab.nfirmaFin, 
                                                        lg_ordencab.nfirmaOpe, 
                                                        tb_user.cnombres,
                                                        UPPER(tb_user.cnameuser) AS cnameuser
                                                    FROM
                                                        lg_ordencab
                                                        INNER JOIN
                                                        tb_user
                                                        ON 
                                                            lg_ordencab.id_cuser = tb_user.iduser
                                                    WHERE
                                                        lg_ordencab.id_regmov =:id");
                $sql->execute(["id"=>$id]);

                $docData = array();
                while($row=$sql->fetch(PDO::FETCH_ASSOC)){
                    $docData[] = $row;
                }

                return array("info"=>$docData,
                             "pedidos"=>$this->pedidosOrden($id),
                            "adjuntos"=>$this->consultarAdjuntos($id));

            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function pedidosOrden($id){
            try {
                $salida = "";
                $sql = $this->db->connect()->prepare("SELECT
                                                        tb_pedidocab.idreg,
                                                        LPAD(tb_pedidocab.nrodoc,6,0) AS nrodoc,
                                                        DATE_FORMAT(tb_pedidocab.emision,'%d/%m/%Y') AS  emision,
                                                        tb_pedidocab.aprueba,
                                                        DATE_FORMAT(tb_pedidocab.faprueba,'%d/%m/%Y') AS faprueba,
                                                        tb_pedidocab.docPdfAprob,
                                                        UPPER(tb_user.cnameuser) AS cnameuser,
                                                        tb_pedidocab.idorden 
                                                    FROM
                                                        tb_pedidocab
                                                        INNER JOIN tb_user ON tb_pedidocab.aprueba = tb_user.iduser 
                                                    WHERE
                                                        tb_pedidocab.idorden = :id");
                $sql->execute(["id"=>$id]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()){
                        $salida .= '<tr class="pointer">
                                        <td class="textoCentro">'.$rs['nrodoc'].'</td>
                                        <td class="textoCentro">'.$rs['emision'].'</td>
                                        <td class="textoCentro">'.$rs['faprueba'].'</td>
                                        <td class="pl20px">'.$rs['cnameuser'].'</td>
                                        <td class="textoCentro"><a href="'.$rs['idreg'].'"><i class="fas fa-file-pdf"></i></a></td>
                                    </tr>';
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function consultarAdjuntos($id){
            try {
                $salida = "";
                $sql = $this->db->connect()->prepare("SELECT
                                            lg_regdocumento.id_regmov, 
                                            lg_regdocumento.nidrefer, 
                                            lg_regdocumento.cmodulo, 
                                            lg_regdocumento.creferencia, 
                                            lg_regdocumento.cdocumento
                                        FROM
                                            lg_regdocumento
                                        WHERE
                                            lg_regdocumento.nidrefer =:id 
                                        AND 
                                            lg_regdocumento.cmodulo = 'ORD'");
                $sql->execute(["id"=>$id]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()){
                        $salida .= '<li><a href="'.$rs['nidrefer'].'"><i class="fas fa-file-pdf"></i><p>'.$rs['cdocumento'].'</p></a></li>';
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }
    }
?>