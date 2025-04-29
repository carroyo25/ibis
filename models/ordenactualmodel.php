<?php
    class OrdenActualModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarOrdenActualScroll($pagina,$cantidad){
            try {
                $inicio = ($pagina - 1) * $cantidad;
                $limite = $this->contarItems();

                $sql = $this->db->connect()->prepare("SELECT
                                                        tb_costusu.ncodcos,
                                                        tb_costusu.ncodproy,
                                                        tb_costusu.id_cuser,
                                                        lg_ordencab.id_regmov,
                                                        LPAD(lg_ordencab.cnumero,6,0) AS cnumero,
                                                        DATE_FORMAT(lg_ordencab.ffechadoc,'%d/%m/%Y') AS emision,
                                                        lg_ordencab.nNivAten,
                                                        lg_ordencab.nEstadoDoc,
                                                        lg_ordencab.ncodpago,
                                                        lg_ordencab.nplazo,
                                                        lg_ordencab.cdocPDF,
                                                        FORMAT(lg_ordencab.ntotal,2) AS ntotal,
                                                        lg_ordencab.ncodmon,
                                                        UPPER( lg_ordencab.cObservacion ) AS concepto,
                                                        UPPER( tb_pedidocab.detalle ) AS detalle,
                                                        UPPER(
                                                        CONCAT_WS( tb_area.ccodarea, tb_area.cdesarea )) AS area,
                                                        UPPER(
                                                        CONCAT_WS( tb_proyectos.ccodproy, tb_proyectos.cdesproy )) AS costos,
                                                        tb_proyectos.ccodproy,
                                                        lg_ordencab.nfirmaLog,
                                                        lg_ordencab.nfirmaFin,
                                                        lg_ordencab.nfirmaOpe,
                                                        tb_parametros.cdescripcion AS atencion,
                                                        UPPER(cm_entidad.crazonsoc) AS proveedor,
                                                        IF(ISNULL(lg_ordencab.nfirmaLog),0,1) AS logistica,
                                                        IF(ISNULL(lg_ordencab.nfirmaFin),0,1) AS finanzas,
                                                        IF(ISNULL(lg_ordencab.nfirmaOpe),0,1) AS operaciones,
                                                        IF(lg_ordencab.nEstadoDoc = 59,'resaltado_firma','-') AS resaltado,
                                                        estados.cdescripcion AS estado,
                                                        UPPER( tb_user.cnameuser ) AS usuario 
                                                    FROM
                                                        tb_costusu
                                                        INNER JOIN lg_ordencab ON tb_costusu.ncodproy = lg_ordencab.ncodpry
                                                        INNER JOIN tb_pedidocab ON lg_ordencab.id_refpedi = tb_pedidocab.idreg
                                                        INNER JOIN tb_area ON lg_ordencab.ncodarea = tb_area.ncodarea
                                                        INNER JOIN tb_proyectos ON lg_ordencab.ncodpry = tb_proyectos.nidreg
                                                        INNER JOIN tb_parametros ON lg_ordencab.nNivAten = tb_parametros.nidreg
                                                        INNER JOIN cm_entidad ON lg_ordencab.id_centi = cm_entidad.id_centi
                                                        INNER JOIN tb_parametros AS estados ON lg_ordencab.nEstadoDoc = estados.nidreg
                                                        LEFT JOIN tb_user ON lg_ordencab.id_cuser = tb_user.iduser 
                                                    WHERE
                                                        tb_costusu.id_cuser = :user 
                                                        AND tb_costusu.nflgactivo = 1
                                                        AND (lg_ordencab.nEstadoDoc = 60 OR lg_ordencab.nEstadoDoc = 62)
                                                        AND lg_ordencab.cper = YEAR(NOW())
                                                    ORDER BY lg_ordencab.id_regmov DESC
                                                    LIMIT $inicio,$cantidad");
                
                $sql->execute(["user"=>$_SESSION['iduser']]);

                $rc = $sql->rowcount();
                $item = 1;

                if ($rc > 0){
                    while( $rs = $sql->fetch()) {
                        $datos[] = $rs;
                    }
                }

                return array("filas"=>$datos,
                            'quedan'=>($inicio + $cantidad) < $limite);

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function contarItems(){
            try {
                $sql = $this->db->connect()->query("SELECT COUNT(id_regmov) AS regs FROM lg_ordencab WHERE nflgactivo = 1");
                $sql->execute();
                $filas = $sql->fetch();

                return $filas['regs'];
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function filtrarActualizacion($parametros){
            try {
                $salida = "";
                $mes  = date("m");

                $orden   = $parametros['ordenSearch'] == "" ? "%" : $parametros['ordenSearch'];
                $costos  = $parametros['costosSearch'] == -1 ? "%" : $parametros['costosSearch'];
                $mes     = $parametros['mesSearch'] == -1 ? "%" :  $parametros['mesSearch'];
                $anio    = $parametros['anioSearch'] == "" ? "%" :  $parametros['anioSearch'];;

                 $salida = "";
                 $sql = $this->db->connect()->prepare("SELECT
                                                        tb_costusu.ncodcos,
                                                        tb_costusu.ncodproy,
                                                        tb_costusu.id_cuser,
                                                        lg_ordencab.id_regmov,
                                                        LPAD(lg_ordencab.cnumero,6,0) AS cnumero,
                                                        lg_ordencab.ffechadoc AS emision,
                                                        lg_ordencab.nNivAten,
                                                        lg_ordencab.nEstadoDoc,
                                                        lg_ordencab.ncodpago,
                                                        lg_ordencab.nplazo,
                                                        lg_ordencab.cdocPDF,
                                                        FORMAT(lg_ordencab.ntotal,2) AS ntotal,
                                                        lg_ordencab.ncodmon,
                                                        UPPER( lg_ordencab.cObservacion ) AS concepto,
                                                        UPPER( tb_pedidocab.detalle ) AS detalle,
                                                        UPPER(
                                                        CONCAT_WS( tb_area.ccodarea, tb_area.cdesarea )) AS area,
                                                        UPPER(
                                                        CONCAT_WS( tb_proyectos.ccodproy, tb_proyectos.cdesproy )) AS costos,
                                                        tb_proyectos.ccodproy,
                                                        lg_ordencab.nfirmaLog,
                                                        lg_ordencab.nfirmaFin,
                                                        lg_ordencab.nfirmaOpe,
                                                        tb_parametros.cdescripcion AS atencion,
                                                        UPPER(cm_entidad.crazonsoc) AS proveedor,
                                                        IF(ISNULL(lg_ordencab.nfirmaLog),0,1) AS logistica,
                                                        IF(ISNULL(lg_ordencab.nfirmaFin),0,1) AS finanzas,
                                                        IF(ISNULL(lg_ordencab.nfirmaOpe),0,1) AS operaciones,
                                                        IF(lg_ordencab.nEstadoDoc = 59,'resaltado_firma','-') AS resaltado,
                                                        estados.cdescripcion AS estado 
                                                    FROM
                                                        tb_costusu
                                                        INNER JOIN lg_ordencab ON tb_costusu.ncodproy = lg_ordencab.ncodpry
                                                        INNER JOIN tb_pedidocab ON lg_ordencab.id_refpedi = tb_pedidocab.idreg
                                                        INNER JOIN tb_area ON lg_ordencab.ncodarea = tb_area.ncodarea
                                                        INNER JOIN tb_proyectos ON lg_ordencab.ncodpry = tb_proyectos.nidreg
                                                        INNER JOIN tb_parametros ON lg_ordencab.nNivAten = tb_parametros.nidreg
                                                        INNER JOIN cm_entidad ON lg_ordencab.id_centi = cm_entidad.id_centi
                                                        INNER JOIN tb_parametros AS estados ON lg_ordencab.nEstadoDoc = estados.nidreg 
                                                    WHERE 
                                                        tb_costusu.id_cuser = :user 
                                                        AND tb_costusu.nflgactivo = 1 
                                                        AND lg_ordencab.cper LIKE :anio
                                                        AND lg_ordencab.cnumero LIKE :orden
                                                        AND tb_costusu.ncodproy LIKE :costos
                                                        AND lg_ordencab.cmes LIKE :mes
                                                        AND (lg_ordencab.nEstadoDoc = 60 OR lg_ordencab.nEstadoDoc = 62)
                                                    ORDER BY lg_ordencab.id_regmov DESC");
                 $sql->execute(["user"=>$_SESSION['iduser'],
                                "orden"=>$orden,
                                "costos"=>$costos,
                                "mes"=>$mes,
                                "anio"=>$anio]);

                 $rowCount = $sql->rowCount();
 
                 if ($rowCount > 0){
                     while ($rs = $sql->fetch()) {
 
                        $flog = is_null($rs['nfirmaLog']) ? 0 : 1;
                        $fope = is_null($rs['nfirmaOpe']) ? 0 : 1;
                        $ffin = is_null($rs['nfirmaFin']) ? 0 : 1;

                        $log = is_null($rs['nfirmaLog']) ? '<i class="far fa-square"></i>' : '<i class="far fa-check-square"></i>';
                        $ope = is_null($rs['nfirmaOpe']) ? '<i class="far fa-square"></i>' : '<i class="far fa-check-square"></i>';
                        $fin = is_null($rs['nfirmaFin']) ? '<i class="far fa-square"></i>' : '<i class="far fa-check-square"></i>';
                        
                        
                        $estado = "Emitido";


                        if($flog && $fope && $ffin){
                            $estado = "Aprobado";
                        }

                        $montoDolares = 0;
                        $montoSoles = 0;

                        if ( $rs['ncodmon'] == 20) {
                            $montoSoles = "S/. ".$rs['ntotal'];
                            $montoDolares = "";
                        }else{
                            $montoSoles = "";
                            $montoDolares =  "$ ".$rs['ntotal'];
                        }

                        if ( $rs['nEstadoDoc'] == 49) {
                            $estado = "procesando";
                        }else if ( $rs['nEstadoDoc'] == 59 ) {
                            $estado = "firmas";
                        }else if ( $rs['nEstadoDoc'] == 60 ) {
                            $estado = "recepcion";
                        }else if ( $rs['nEstadoDoc'] == 62 ) {
                            $estado = "despacho";
                        }else if ( $rs['nEstadoDoc'] == 105 ) {
                            $estado = "anulado";
                            $montoDolares = "";
                            $montoSoles = "";
                        }
 
                         $salida .='<tr class="pointer" data-indice="'.$rs['id_regmov'].'" 
                                                         data-estado="'.$rs['nEstadoDoc'].'"
                                                         data-finanzas="'.$ffin.'"
                                                         data-logistica="'.$flog.'"
                                                         data-operaciones="'.$fope.'">
                                        <td class="textoCentro">'.str_pad($rs['cnumero'],4,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.date("d/m/Y", strtotime($rs['emision'])).'</td>
                                        <td class="pl20px">'.$rs['concepto'].'</td>
                                        <td class="pl20px">'.utf8_decode($rs['costos']).'</td>
                                        <td class="pl20px">'.$rs['area'].'</td>
                                        <td class="pl20px">'.$rs['proveedor'].'</td>
                                        <td class="textoDerecha">'.$montoSoles.'</td>
                                        <td class="textoDerecha">'.$montoDolares.'</td>
                                        <td class="textoCentro '.strtolower($rs['atencion']).'">'.$rs['atencion'].'</td>
                                        <td class="textoCentro '.$estado.'">'.strtoupper($estado).'</td>
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
    }
?>