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
                                                    FORMAT( lg_ordencab.ntotal, 2 ) AS ntotal,
                                                    UPPER( tb_pedidocab.concepto ) AS concepto,
                                                    lg_ordencab.cdocPDF,
                                                    cm_entidad.crazonsoc,
                                                    tb_proyectos.ccodproy,
                                                    UPPER( CONCAT_WS( ' ', tb_area.ccodarea, tb_area.cdesarea ) ) AS area,
                                                    UPPER( CONCAT_WS( ' ', tb_proyectos.ccodproy, tb_proyectos.cdesproy ) ) AS costos,
                                                    tb_proyectos.nidreg,
                                                    tb_parametros.cdescripcion AS atencion,
                                                    ( lg_ordencab.nfirmaLog + lg_ordencab.nfirmaFin + lg_ordencab.nfirmaOpe ) AS estado_firmas,
                                                    ( SELECT FORMAT( SUM( lg_ordendet.nunitario * lg_ordendet.ncanti ), 2 ) FROM lg_ordendet WHERE lg_ordendet.id_orden = lg_ordencab.id_regmov ) AS total_orden,
                                                    UPPER (tb_user.cnameuser) AS operador  
                                                FROM
                                                    lg_ordencab
                                                    INNER JOIN tb_pedidocab ON lg_ordencab.id_refpedi = tb_pedidocab.idreg
                                                    INNER JOIN tb_area ON lg_ordencab.ncodarea = tb_area.ncodarea
                                                    INNER JOIN tb_proyectos ON lg_ordencab.ncodpry = tb_proyectos.nidreg
                                                    INNER JOIN tb_parametros ON lg_ordencab.nNivAten = tb_parametros.nidreg
                                                    INNER JOIN cm_entidad ON lg_ordencab.id_centi = cm_entidad.id_centi
                                                    INNER JOIN lg_ordendet ON lg_ordencab.id_regmov = lg_ordendet.id_orden
                                                    INNER JOIN tb_user ON lg_ordencab.id_cuser = tb_user.iduser
                                                WHERE
                                                    lg_ordencab.nEstadoDoc = 59 
                                                    AND ( lg_ordencab.nfirmaLog IS NULL OR lg_ordencab.nfirmaOpe IS NULL OR lg_ordencab.nfirmaFin IS NULL ) 
                                                GROUP BY
                                                    lg_ordencab.id_regmov 
                                                ORDER BY
                                                    lg_ordencab.id_regmov DESC");
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

                         $resaltado = "";

                         if ($flog == 1 && $fope == 1 && $ffin == 1) {
                            $resaltado = "resaltado_firma";
                         }else {
                            $resaltado = "";
                         }

                         $alerta_logistica = $this-> buscarUserComentario($rs['id_regmov'],'633ae7e588a52') > 0 && $flog == 0 ? "urgente":" ";  //logistica
                         $alerta_finanzas = $this-> buscarUserComentario($rs['id_regmov'],'6288328f58068')> 0 && $ffin == 0 ? "urgente":" ";  //Finanzas
                         $alerta_operaciones = $this-> buscarUserComentario($rs['id_regmov'],'62883306d1cd3') > 0 && $fope == 0? "urgente":" ";  //operaciones
 
                         $salida .='<tr class="pointer '.$resaltado.'" data-indice="'.$rs['id_regmov'].'" 
                                                         data-estado="'.$rs['nEstadoDoc'].'"
                                                         data-finanzas="'.$ffin.'"
                                                         data-logistica="'.$flog.'"
                                                         data-operaciones="'.$fope.'"
                                                         data-firmas="'.$rs['estado_firmas'].'">
                                        <td class="textoCentro">'.str_pad($rs['cnumero'],4,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.date("d/m/Y", strtotime($rs['ffechadoc'])).'</td>
                                        <td class="pl20px">'.$rs['concepto'].'</td>
                                        <td class="pl20px">'.$rs['costos'].'</td>
                                        <td class="pl20px">'.$rs['crazonsoc'].'</td>
                                        <td class="pl20px">'.$rs['area'].'</td>
                                        <td class="textoCentro '.strtolower($rs['atencion']).'">'.$rs['atencion'].'</td>
                                        <td class="textoDerecha pr10px">'.$rs['total_orden'].'</td>
                                        <td class="textoCentro">'.$rs['operador'].'</td>
                                        <td class="textoCentro '.$alerta_logistica.'">'.$log.'</td>
                                        <td class="textoCentro '.$alerta_finanzas.'">'.$fin.'</td>
                                        <td class="textoCentro '.$alerta_operaciones.'">'.$ope.'</td>
                                        
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
                            "estado"=>true,
                            "listado"=>$this->listarOrdenesFirmas());
            }else {
                return array("mensaje"=>"Ya autorizo la orden",
                            "clase"=>"mensaje_error",
                            "operador"=>$operador,
                            "estado"=>false,
                            "listado"=>$this->listarOrdenesFirmas());
            }
        }

        public function firmarExpress($id) {
            $fecha =  date("Y-m-d");

            try {
                $sql = $this->db->connect()->prepare("UPDATE lg_ordencab SET 
                                                                nfirmaLog=:nLog,
                                                                nfirmaOpe=:nOper,
                                                                nfirmaFin=:nFin,
                                                                codperLog=:usrLog,
                                                                codperOpe=:usrOpe,
                                                                codperFin=:usrFin,
                                                                fechaLog=:fecLog,
                                                                fechaOpe=:fecOpe,
                                                                fechaFin=:fecFin,
                                                                nNivAten=:atencion 
                                                    WHERE id_regmov=:cod");
                
                $sql->execute(["nLog" =>1,
                                "nOper"=>1,
                                "nFin" =>1,
                                "usrLog" => $_SESSION['iduser'],
                                "usrOpe" => $_SESSION['iduser'],
                                "usrFin" => $_SESSION['iduser'],
                                "fecLog" => $fecha,
                                "fecFin"=> $fecha,
                                "fecOpe"=> $fecha,
                                "atencion"=> 46,
                                "cod"=>$id]);

                $rowCount = $sql->rowcount();

                if ($rowCount > 0) {
                    return array("respuesta"=>true,
                                "mensaje"=>"Se autorizo la orden",
                                "clase"=>"mensaje_correcto");
                                
                }else {
                    return array("respuesta"=>true,
                                "mensaje"=>"No se pudo actualizar",
                                "clase"=>"mensaje_error");
                                
                }

            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }

        public function consultarPrecios($codigo){
            try {
                $salida = "";
                $sql = $this->db->connect()->prepare("SELECT
                                                        cm_producto.id_cprod,
                                                        cm_producto.ccodprod,
                                                        UPPER(cm_producto.cdesprod) AS cdesprod,
                                                        lg_ordendet.ncanti,
                                                        lg_ordendet.nunitario,
                                                        lg_ordencab.cnumero AS orden,
                                                        tb_unimed.cabrevia AS unidad,
                                                        tb_parametros.cabrevia AS moneda,
                                                        LPAD(tb_pedidocab.nrodoc,6,0) AS pedido,
                                                        tb_proyectos.ccodproy,
                                                        UPPER(tb_proyectos.cdesproy) AS cdesproy,
                                                        DATE_FORMAT(lg_ordencab.ffechadoc,'%d/%m/%Y') AS fecha,
                                                    IF
                                                        ( lg_ordencab.ncodmon != 20, FORMAT( lg_ordencab.ntcambio, 2 ), 1 ) AS tipo_cambio 
                                                    FROM
                                                        cm_producto
                                                        INNER JOIN lg_ordendet ON cm_producto.id_cprod = lg_ordendet.id_cprod
                                                        INNER JOIN lg_ordencab ON lg_ordendet.id_orden = lg_ordencab.id_regmov
                                                        INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed
                                                        INNER JOIN tb_parametros ON lg_ordencab.ncodmon = tb_parametros.nidreg
                                                        INNER JOIN tb_pedidocab ON lg_ordencab.id_refpedi = tb_pedidocab.idreg
                                                        INNER JOIN tb_proyectos ON lg_ordencab.ncodpry = tb_proyectos.nidreg
                                                        INNER JOIN cm_entidad ON lg_ordencab.id_centi = cm_entidad.id_centi
                                                    WHERE
                                                        cm_producto.id_cprod = :codigo
                                                    ORDER BY
                                                        lg_ordencab.ffechadoc ASC");
                $sql->execute(["codigo"=>$codigo]);
                $rowCount = $sql->rowcount();

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()) {

                        $tipo_cambio = $rs['tipo_cambio'] > 1 ? $rs['tipo_cambio'] : ''; 
 
                        $salida .='<tr>
                                        <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                        <td class="pl10px">'.$rs['cdesprod'].'</td>
                                        <td class="pl10px">'.$rs['cdesproy'].'</td>
                                        <td class="textoCentro">'.$rs['moneda'].'</td>
                                        <td class="textoDerecha pr20px">'.$rs['nunitario'].'</td>
                                        <td class="textoDerecha pr20px">'.$rs['ncanti'].'</td>
                                        <td class="textoCentro">'.$rs['pedido'].'</td>
                                        <td class="textoCentro">'.$rs['orden'].'</td>
                                        <td class="textoDerecha pr20px">'.$tipo_cambio.'</td>
                                        <td class="textoCentro">'.$rs['fecha'].'</td>
                                    </tr>';
                    }

                }

                return $salida;

            } catch (PDOException $th) {
                    echo "Error: " . $th->getMessage();
                    return false;
                }
        }

        public function filtrarOrdenesFirmas($parametros){
            try {
                //$mes  = date("m");

                $tipo   = $parametros['tipoSearch'] == -1 ? "%" : $parametros['tipoSearch'];
                $costos = $parametros['costosSearch'] == -1 ? "%" : $parametros['costosSearch'];
                $mes    = $parametros['mesSearch'] == -1 ? "%" :  $parametros['mesSearch'];
                $anio   = $parametros['anioSearch'];


                $salida = "";
                 $sql = $this->db->connect()->prepare("SELECT
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
                                                            FORMAT(lg_ordencab.ntotal,2) as ntotal,
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
                                                            tb_proyectos.ccodproy,
                                                            tb_parametros.cdescripcion AS atencion,
                                                            (
                                                                lg_ordencab.nfirmaLog + lg_ordencab.nfirmaFin + lg_ordencab.nfirmaOpe
                                                            ) AS estado_firmas,
                                                            UPPER(cm_entidad.crazonsoc) AS crazonsoc,
                                                            UPPER (tb_user.cnameuser) AS operador
                                                            FROM
                                                            lg_ordencab
                                                            INNER JOIN tb_pedidocab ON lg_ordencab.id_refpedi = tb_pedidocab.idreg
                                                            INNER JOIN tb_area ON lg_ordencab.ncodarea = tb_area.ncodarea
                                                            INNER JOIN tb_proyectos ON lg_ordencab.ncodpry = tb_proyectos.nidreg
                                                            INNER JOIN tb_parametros ON lg_ordencab.nNivAten = tb_parametros.nidreg
                                                            INNER JOIN cm_entidad ON lg_ordencab.id_centi = cm_entidad.id_centi
                                                            INNER JOIN tb_user ON lg_ordencab.id_cuser = tb_user.iduser
                                                            WHERE
                                                                lg_ordencab.nEstadoDoc = 59 
                                                                AND lg_ordencab.ncodpry LIKE :costos 
                                                                AND lg_ordencab.ntipmov LIKE :tipomov 
                                                                AND MONTH ( lg_ordencab.ffechadoc ) LIKE :mes
                                                                AND YEAR ( lg_ordencab.ffechadoc ) LIKE :anio
                                                                AND (lg_ordencab.nfirmaLog IS NULL OR lg_ordencab.nfirmaOpe IS NULL  OR lg_ordencab.nfirmaFin IS NULL )");
                                                                
                 $sql->execute(["tipomov"=>$tipo,
                                "costos"=>$costos,
                                "mes"=>$mes,
                                "anio"=>$anio]);

                 $rowCount = $sql->rowCount();
 
                 if ($rowCount > 0){
                     while ($rs = $sql->fetch()) {
 
                         $log = is_null($rs['nfirmaLog']) ? '<i class="far fa-square"></i>' : '<i class="far fa-check-square"></i>';
                         $ope = is_null($rs['nfirmaOpe']) ? '<i class="far fa-square"></i>' : '<i class="far fa-check-square"></i>';
                         $fin = is_null($rs['nfirmaFin']) ? '<i class="far fa-square"></i>' : '<i class="far fa-check-square"></i>';
 
                         $flog = is_null($rs['nfirmaLog']) ? 0 : 1;
                         $fope = is_null($rs['nfirmaOpe']) ? 0 : 1;
                         $ffin = is_null($rs['nfirmaFin']) ? 0 : 1;

                         $resaltado = "";

                         if ($flog == 1 && $fope == 1 && $ffin == 1) {
                            $resaltado = "resaltado_firma";
                         }else {
                            $resaltado = "";
                         }
 
                         $salida .='<tr class="pointer '.$resaltado.'" data-indice="'.$rs['id_regmov'].'" 
                                                         data-estado="'.$rs['nEstadoDoc'].'"
                                                         data-finanzas="'.$ffin.'"
                                                         data-logistica="'.$flog.'"
                                                         data-operaciones="'.$fope.'"
                                                         data-firmas="'.$rs['estado_firmas'].'">
                                     <td class="textoCentro">'.str_pad($rs['cnumero'],4,0,STR_PAD_LEFT).'</td>
                                     <td class="textoCentro">'.date("d/m/Y", strtotime($rs['ffechadoc'])).'</td>
                                     <td class="pl20px">'.$rs['concepto'].'</td>
                                     <td class="pl20px">'.utf8_decode($rs['ccodproy']).'</td>
                                     <td class="pl20px">'.$rs['crazonsoc'].'</td>
                                     <td class="pl20px">'.$rs['area'].'</td>
                                     <td class="textoCentro '.strtolower($rs['atencion']).'">'.$rs['atencion'].'</td>
                                     <td class="textoDerecha pr10px">'.$rs['ntotal'].'</td>
                                     <td class="textoCentro">'.$rs['operador'].'</td>
                                     <td class="textoCentro">'.$log.'</td>
                                     <td class="textoCentro">'.$fin.'</td>
                                     <td class="textoCentro">'.$ope.'</td>
                                     </tr>';
                     }
                 }
 
                 return $salida; 


            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
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