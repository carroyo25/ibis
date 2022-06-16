<?php
    class EvaluacionModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarOrdenes(){
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
                                                            CONCAT_WS( tb_area.ccodarea, tb_area.cdesarea )) AS area,
                                                            UPPER(
                                                            CONCAT_WS( tb_proyectos.ccodproy, tb_proyectos.cdesproy )) AS costos,
                                                            lg_ordencab.id_centi,
                                                            cm_entidad.cnumdoc,
                                                            UPPER(cm_entidad.crazonsoc) AS proveedor 
                                                        FROM
                                                            tb_costusu
                                                            INNER JOIN lg_ordencab ON tb_costusu.ncodproy = lg_ordencab.ncodpry
                                                            INNER JOIN tb_pedidocab ON lg_ordencab.id_refpedi = tb_pedidocab.idreg
                                                            INNER JOIN tb_area ON lg_ordencab.ncodarea = tb_area.ncodarea
                                                            INNER JOIN tb_proyectos ON lg_ordencab.ncodpry = tb_proyectos.nidreg
                                                            INNER JOIN tb_parametros ON lg_ordencab.nNivAten = tb_parametros.nidreg
                                                            INNER JOIN cm_entidad ON lg_ordencab.id_centi = cm_entidad.id_centi 
                                                        WHERE
                                                            tb_costusu.id_cuser = :user 
                                                            AND tb_costusu.nflgactivo = 1 
                                                            AND lg_ordencab.nEstadoDoc = 67");
                                                            $sql->execute(["user"=>$_SESSION['iduser']]);
                 $rowCount = $sql->rowCount();
 
                 if ($rowCount > 0){
                     while ($rs = $sql->fetch()) {
 
                         $salida .='<tr class="pointer" data-indice="'.$rs['id_regmov'].'" >
                                     <td class="textoCentro">'.str_pad($rs['cnumero'],4,0,STR_PAD_LEFT).'</td>
                                     <td class="textoCentro">'.date("d/m/Y", strtotime($rs['ffechadoc'])).'</td>
                                     <td class="pl20px">'.$rs['detalle'].'</td>
                                     <td class="pl20px">'.utf8_decode($rs['costos']).'</td>
                                     <td class="pl20px">'.$rs['area'].'</td>
                                     <td class="pl20px">'.$rs['proveedor'].'</td>
                                    </tr>';
                     }
                 }
 
                 return $salida;                    
            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }

        public function evaluar($rol,$tipo){
            try {
                $salida = "";
                $sql = $this->db->connect()->prepare("SELECT
                                                        tb_evaluaciones.idreg,
                                                        tb_evaluaciones.descripcion,
                                                        tb_evaluaciones.ayuda,
                                                        tb_evaluaciones.puntaje,
                                                        tb_evaluaciones.peso 
                                                    FROM
                                                        tb_evaluaciones 
                                                    WHERE
                                                        tb_evaluaciones.nrol = :rol 
                                                        AND tb_evaluaciones.tipo = :tipo");
                $sql->execute(["rol"=>$rol,"tipo"=>$tipo]);
                $rowCount = $sql->rowCount();
                if ($rowCount > 0){
                    while ($rs = $sql->fetch()){
                        $salida.='<tr data-reg="'.$rs['idreg'].'">
                                    <td class="pl20px">'.$rs['descripcion'].'</td>
                                    <td class="pl20px">'.$rs['ayuda'].'</td>
                                    <td><input type="number" value="'.$rs['puntaje'].'" max="5" min="1"></td>
                                  </tr>';
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
            }
        }

        public function grabarEvaluacion($detalles,$orden,$entidad){
            try {
                //code...
            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
            }
        }
        
    }
?>