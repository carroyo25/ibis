<?php
    class SalidaModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarNotasIngreso(){
            try {
                //code...
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function importarIngresos(){
            try {
                $salida = "";
                $sql = $this->db->connect()->prepare("SELECT
                                                    ibis.alm_recepcab.id_regalm,
                                                    ibis.tb_costusu.ncodproy,
                                                    ibis.alm_recepcab.nnronota,
                                                    ibis.alm_recepcab.cper,
                                                    ibis.alm_recepcab.cmes,
                                                    ibis.alm_recepcab.ncodalm1,
                                                    ibis.alm_recepcab.ffecdoc,
                                                    ibis.alm_recepcab.cnumguia,
                                                    ibis.alm_recepcab.ncodpry,
                                                    ibis.alm_recepcab.ncodarea,
                                                    ibis.alm_recepcab.idref_pedi,
                                                    ibis.alm_recepcab.idref_abas,
                                                    ibis.alm_recepcab.nEstadoDoc,
                                                    UPPER(
                                                    CONCAT_WS( ' ', ibis.tb_proyectos.ccodproy, ibis.tb_proyectos.cdesproy )) AS proyecto,
                                                    UPPER(
                                                    CONCAT_WS( ' ', ibis.tb_area.ccodarea, ibis.tb_area.cdesarea )) AS area 
                                                FROM
                                                    ibis.tb_costusu
                                                    INNER JOIN ibis.alm_recepcab ON ibis.tb_costusu.ncodproy = ibis.alm_recepcab.ncodpry
                                                    INNER JOIN ibis.tb_proyectos ON ibis.alm_recepcab.ncodpry = ibis.tb_proyectos.nidreg
                                                    INNER JOIN ibis.tb_area ON ibis.alm_recepcab.ncodarea = ibis.tb_area.ncodarea 
                                                WHERE
                                                    ibis.tb_costusu.nflgactivo = 1 
                                                    AND ibis.tb_costusu.id_cuser = :usr 
                                                    AND ibis.alm_recepcab.nEstadoDoc = 62");
                $sql->execute(["usr"=>$_SESSION['iduser']]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0){
                    while ($rs = $sql->fetch()){
                        $salida .='<tr class="pointer" data-idnit="'.$rs['id_regalm'].'">
                                        <td class="textoCentro">'.$rs['nnronota'].'</td>
                                        <td class="textoCentro">'.date("d/m/Y", strtotime($rs['ffecdoc'])).'</td>
                                        <td class="pl20px">'.$rs['area'].'</td>
                                        <td class="pl20px">'.$rs['proyecto'].'</td>
                                    </tr>';
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function llamarNotaIngresoId($id){
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                            ibis.alm_recepcab.id_regalm,
                                            ibis.alm_recepcab.nnronota,
                                            ibis.alm_recepcab.cper,
                                            ibis.alm_recepcab.cmes,
                                            ibis.alm_recepcab.ffecdoc,
                                            ibis.alm_recepcab.id_userAprob AS aprueba,
                                            ibis.alm_recepcab.nEstadoDoc,
                                            UPPER(
                                            CONCAT_WS( ' ', ibis.tb_proyectos.ccodproy, ibis.tb_proyectos.cdesproy )) AS proyecto,
                                            UPPER(
                                            CONCAT_WS( ' ', ibis.tb_area.ccodarea, ibis.tb_area.cdesarea )) AS area,
                                            ibis.tb_user.cnombres,
                                            LPAD( ibis.tb_pedidocab.nrodoc, 6, 0 ) AS pedido,
                                            CONCAT_WS( '', rrhh.tabla_aquarius.nombres, rrhh.tabla_aquarius.apellidos ) AS solicita,
                                            UPPER( ibis.tb_almacen.cdesalm ) AS almacen,
                                            UPPER( ibis.tb_pedidocab.concepto ) AS concepto,
                                            ibis.tb_parametros.cdescripcion,
                                            ibis.tb_parametros.cabrevia,
                                            ibis.lg_ordencab.cnumero AS orden,
                                            ibis.lg_ordencab.ffechadoc,
                                            ibis.tb_pedidocab.emision,
                                            ibis.alm_recepcab.ncodpry,
                                            ibis.alm_recepcab.ncodarea,
                                            ibis.alm_recepcab.ncodalm1,
                                            ibis.alm_recepcab.idref_pedi,
                                            ibis.alm_recepcab.idref_abas,
                                            ibis.alm_recepcab.cnumguia 
                                        FROM
                                            ibis.alm_recepcab
                                            INNER JOIN ibis.tb_proyectos ON ibis.alm_recepcab.ncodpry = ibis.tb_proyectos.nidreg
                                            INNER JOIN ibis.tb_area ON ibis.alm_recepcab.ncodarea = ibis.tb_area.ncodarea
                                            INNER JOIN ibis.tb_user ON ibis.alm_recepcab.id_userAprob = ibis.tb_user.iduser
                                            INNER JOIN ibis.tb_pedidocab ON ibis.alm_recepcab.idref_pedi = ibis.tb_pedidocab.idreg
                                            INNER JOIN rrhh.tabla_aquarius ON ibis.tb_pedidocab.idsolicita = rrhh.tabla_aquarius.internal
                                            INNER JOIN ibis.tb_almacen ON ibis.alm_recepcab.ncodalm1 = ibis.tb_almacen.ncodalm
                                            INNER JOIN ibis.tb_parametros ON ibis.alm_recepcab.nEstadoDoc = ibis.tb_parametros.nidreg
                                            INNER JOIN ibis.lg_ordencab ON ibis.alm_recepcab.idref_abas = ibis.lg_ordencab.id_regmov 
                                        WHERE
                                            ibis.alm_recepcab.id_regalm = :id 
                                            AND ibis.alm_recepcab.nEstadoDoc = 62");
                                        $sql->execute(["id"=>$id]);
            
            $docData = array();

            while($row=$sql->fetch(PDO::FETCH_ASSOC)){
                $docData[] = $row;
            }

            $query = "SELECT COUNT( alm_despachocab.id_regalm ) AS numero FROM alm_despachocab WHERE ncodalm1 =:cod";

            return array("cabecera"=>$docData,
                        "detalles"=>$this->detallesNotaIngreso($id),
                        "numero"=>$this->generarNumero($docData[0]["ncodalm1"],$query));
        } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function detallesNotaIngreso($id){
            try {
                $salida = "";
                $sql = $this->db->connect()->prepare("SELECT
                                                    alm_recepdet.niddeta, 
                                                    alm_recepdet.id_regalm, 
                                                    alm_recepdet.ncodalm1, 
                                                    alm_recepdet.id_cprod, 
                                                    FORMAT(alm_recepdet.ncantidad,2) AS cantidad, 
                                                    alm_recepdet.niddetaPed, 
                                                    alm_recepdet.niddetaOrd, 
                                                    alm_recepdet.nestadoreg, 
                                                    cm_producto.ccodprod, 
                                                    cm_producto.cdesprod, 
                                                    tb_unimed.cabrevia, 
	                                                alm_recepdet.cobserva,
	                                                alm_recepdet.fvence 
                                                FROM
                                                    alm_recepdet
                                                    INNER JOIN
                                                    cm_producto
                                                    ON 
                                                        alm_recepdet.id_cprod = cm_producto.id_cprod
                                                    INNER JOIN
                                                    tb_unimed
                                                    ON 
                                                        cm_producto.nund = tb_unimed.ncodmed
                                                WHERE
                                                    alm_recepdet.id_regalm = :id");
                $sql->execute(["id"=>$id]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    $item = 1;
                    while ($rs = $sql->fetch()){
                        $salida.='<tr data-itemorden="'.$rs['niddetaOrd'].'" data-itempedido="'.$rs['niddetaPed'].'" data-itemingreso="'.$rs['niddeta'].'">
                                        <td>...</td>
                                        <td class="textoCentro">'.str_pad($item,3,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                        <td class="pl20px">'.$rs['cdesprod'].'</td>
                                        <td class="textoCentro">'.$rs['cabrevia'].'</td>
                                        <td class="textoDerecha pr20px">'.$rs['cantidad'].'</td>
                                        <td class="pl20px">'.$rs['cobserva'].'</td>
                                        <td class="textoCentro">serie</td>
                                        <td class="textoCentro">'.$rs['fvence'].'</td>
                                        <td class="textoCentro">'.$rs['nestadoreg'].'</td>
                                    </tr>';
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }
    }
?>