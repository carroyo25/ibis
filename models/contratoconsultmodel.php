<?php
    class ContratoConsultModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarContratosConsulta($user){
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
                                                         FORMAT(lg_ordencab.ntotal,2) AS ntotal,
                                                         tb_proyectos.ccodproy,
                                                         UPPER( lg_ordencab.cObservacion ) AS concepto,
                                                         UPPER( tb_pedidocab.detalle ) AS detalle,
                                                         UPPER(
                                                         CONCAT_WS( tb_area.ccodarea, tb_area.cdesarea )) AS area,
                                                         UPPER(
                                                         CONCAT_WS( tb_proyectos.ccodproy, tb_proyectos.cdesproy )) AS costos,
                                                         lg_ordencab.nfirmaLog,
                                                         lg_ordencab.nfirmaFin,
                                                         lg_ordencab.nfirmaOpe,
                                                         tb_parametros.cdescripcion AS atencion,
                                                         UPPER(cm_entidad.crazonsoc) AS crazonsoc,
                                                         UPPER( tb_user.cnameuser ) AS cnameuser,
                                                         monedas.cabrevia,
                                                         ( SELECT COUNT( lg_ordencomenta.id_regmov ) FROM lg_ordencomenta WHERE lg_ordencomenta.id_regmov = lg_ordencab.id_regmov ) AS comentario 
                                                     FROM
                                                         tb_costusu
                                                         INNER JOIN lg_ordencab ON tb_costusu.ncodproy = lg_ordencab.ncodpry
                                                         INNER JOIN tb_pedidocab ON lg_ordencab.id_refpedi = tb_pedidocab.idreg
                                                         INNER JOIN tb_area ON lg_ordencab.ncodarea = tb_area.ncodarea
                                                         INNER JOIN tb_proyectos ON lg_ordencab.ncodpry = tb_proyectos.nidreg
                                                         INNER JOIN tb_parametros ON lg_ordencab.nNivAten = tb_parametros.nidreg
                                                         INNER JOIN cm_entidad ON lg_ordencab.id_centi = cm_entidad.id_centi
                                                         INNER JOIN tb_user ON lg_ordencab.id_cuser = tb_user.iduser
                                                         INNER JOIN tb_parametros AS monedas ON lg_ordencab.ncodmon = monedas.nidreg  
                                                     WHERE
                                                         tb_costusu.id_cuser = :user
                                                         AND lg_ordencab.ntipdoc = 2
                                                         AND tb_costusu.nflgactivo = 1
                                                         AND lg_ordencab.nEstadoDoc != 49
                                                     ORDER BY  lg_ordencab.id_regmov DESC");
                 $sql->execute(["user"=>$_SESSION['iduser']]);
                 $rowCount = $sql->rowCount();
 
                 if ($rowCount > 0){
                     while ($rs = $sql->fetch()) {
 
                         $log = is_null($rs['nfirmaLog']) ? '<i class="far fa-square"></i>' : '<i class="far fa-check-square"></i>';
                         $ope = is_null($rs['nfirmaOpe']) ? '<i class="far fa-square"></i>' : '<i class="far fa-check-square"></i>';
                         $fin = is_null($rs['nfirmaFin']) ? '<i class="far fa-square"></i>' : '<i class="far fa-check-square"></i>';
 
                         $flog = is_null($rs['nfirmaLog']) ? 0 : 1;
                         $fope = is_null($rs['nfirmaOpe']) ? 0 : 1;
                         $ffin = is_null($rs['nfirmaFin']) ? 0 : 1;
 
                         $resaltado = $rs['nEstadoDoc'] == 59 ? "resaltado_firma" :  "";
                         $observado = "";
                         $obs_alerta = "";
                         $alerta_logistica   = "";  //logistica
                         $alerta_finanzas    = "";  //Finanzas
                         $alerta_operaciones = "";  //operaciones
 
                         if ( $rs['comentario'] > 0 ) {
                             $observado = $rs['comentario'] != 0 ?  $rs['comentario'] :  "";
                             $obs_alerta = $rs['comentario'] % 2 > 0 ?  "semaforoNaranja" :  "semaforoVerde";
 
                             $alerta_logistica   = $this-> buscarUserComentario($rs['id_regmov'],'633ae7e588a52') > 0 && $flog == 0 ? "urgente":"";  //logistica
                             $alerta_finanzas    = $this-> buscarUserComentario($rs['id_regmov'],'6288328f58068') > 0 && $ffin == 0 ? "urgente":"";  //Finanzas
                             $alerta_operaciones = $this-> buscarUserComentario($rs['id_regmov'],'62883306d1cd3') > 0 && $fope == 0 ? "urgente":"";  //operaciones
                         }
 
 
                         $salida .='<tr class="pointer '.$resaltado.'" data-indice="'.$rs['id_regmov'].'" 
                                                         data-estado="'.$rs['nEstadoDoc'].'"
                                                         data-finanzas="'.$ffin.'"
                                                         data-logistica="'.$flog.'"
                                                         data-operaciones="'.$fope.'">
                                     <td class="textoCentro">'.str_pad($rs['cnumero'],6,0,STR_PAD_LEFT).'</td>
                                     <td class="textoCentro">'.date("d/m/Y", strtotime($rs['ffechadoc'])).'</td>
                                     <td class="pl20px">'.$rs['concepto'].'</td>
                                     <td class="pl20px">'.utf8_decode($rs['ccodproy']).'</td>
                                     <td class="pl20px">'.$rs['area'].'</td>
                                     <td class="pl20px">'.$rs['crazonsoc'].'</td>
                                     <td class="pl5px">'.$rs['cnameuser'].'</td>
                                     <td class="textoDerecha">'.$rs['cabrevia'].' '. $rs['ntotal'].'</td>
                                     <td class="textoCentro '.strtolower($rs['atencion']).'">'.$rs['atencion'].'</td>
                                     <td class="textoCentro '.$alerta_logistica.'">'.$log.'</td>
                                     <td class="textoCentro '.$alerta_finanzas.'">'.$fin.'</td>
                                     <td class="textoCentro '.$alerta_operaciones.'">'.$ope.'</td>
                                     <td class="textoCentro '.$obs_alerta.'" >'.$observado.'</td>
                                     </tr>';
                     }
                 }
 
                 return $salida;                    
            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }
    }
?>