<?php
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="cargoplan.xlxs"');
    header('Cache-Control: max-age=0');
    
    use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
    use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
    use Box\Spout\Common\Entity\Style\CellAlignment;
    use Box\Spout\Common\Entity\Style\Color;
	use Box\Spout\Common\Type;

    use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

    class CargoPlannerModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarCargoPlan($parametros){
            try {

                $salida = "";

                $tipo       = $parametros['tipoSearch'] == -1 ? "%" : $parametros['tipoSearch'];
                $costo      = $parametros['costosSearch'] == -1 ? "%" : $parametros['costosSearch'];
                $descrip    = $parametros['descripSearch'] == "" ? "%" : "%".$parametros['descripSearch']."%";
                $codigo     = $parametros['codigoSearch'] == "" ? "%" : "%".$parametros['codigoSearch']."%";
                $orden      = $parametros['ordenSearch'] == "" ? "%" : $parametros['ordenSearch'];
                $pedido     = $parametros['pedidoSearch'] == "" ? "%" : $parametros['pedidoSearch'];
                $concepto   = $parametros['conceptoSearch'] == "" ? "%" : "%".$parametros['conceptoSearch']."%";
                $estadoItem = $parametros['estado_item'] == "" ? "%" : $parametros['estado_item'];
                
                $salida = "No hay registros";
                $item = 1;

                $sql = $this->db->connect()->prepare("SELECT
                                                        tb_pedidodet.iditem,
                                                        tb_pedidodet.idpedido,
                                                        tb_pedidodet.idprod,
                                                        tb_pedidodet.nroparte,
                                                        tb_pedidodet.nregistro,
                                                        tb_pedidodet.cant_pedida AS cantidad_pedido,
                                                        tb_pedidodet.cant_atend AS cantidad_atendida,
                                                        tb_pedidodet.cant_aprob AS cantidad_aprobada,
                                                        LPAD(tb_pedidocab.nrodoc,6,0) AS pedido,
                                                        lg_ordendet.id_orden AS orden,
                                                        lg_ordendet.item AS item_orden,
                                                        cm_producto.ccodprod,
                                                        UPPER( CONCAT_WS( ' ', cm_producto.cdesprod, tb_pedidodet.observaciones ) ) AS descripcion,
                                                        tb_pedidodet.estadoItem,
                                                        tb_proyectos.ccodproy,
                                                        tb_proyectos.nidreg AS idproyecto,
                                                        UPPER( tb_area.cdesarea ) AS area,
                                                        UPPER( tb_partidas.cdescripcion ) AS partida,
                                                        DATE_FORMAT( tb_pedidocab.emision, '%d/%m/%Y' ) AS crea_pedido,
                                                        DATE_FORMAT( tb_pedidocab.faprueba, '%d/%m/%Y' ) AS aprobacion_pedido,
                                                        tb_pedidocab.anio AS anio_pedido,
                                                        tb_pedidocab.mes AS pedido_mes,
                                                        tb_pedidocab.nivelAten AS atencion,
                                                        tb_pedidocab.idtipomov,
                                                        tb_unimed.cabrevia AS unidad,
                                                        lg_ordencab.cper AS anio_orden,
                                                        lg_ordencab.ntipmov,
                                                        FORMAT( lg_ordencab.nplazo, 0 ) AS plazo,
                                                        DATE_FORMAT( lg_ordencab.ffechadoc, '%d/%m/%Y' ) AS fecha_orden,
                                                        DATE_FORMAT( lg_ordencab.ffechaent, '%d/%m/%Y' ) AS fecha_entrega,
                                                        DATE_FORMAT( lg_ordencab.ffechades, '%d/%m/%Y' ) AS fecha_descarga,
                                                        DATE_FORMAT( lg_ordencab.fechafin, '%d/%m/%Y' ) AS fecha_autorizacion_orden,
                                                        lg_ordencab.ffechades,
                                                        lg_ordencab.fechaLog,
                                                        lg_ordencab.fechaOpe,
                                                        lg_ordencab.FechaFin,
                                                        lg_ordencab.ffechaent,
                                                        lg_ordencab.nEstadoDoc,
                                                        lg_ordencab.nNivAten,
                                                        LPAD(lg_ordencab.cnumero,4,0) AS cnumero,
                                                        UPPER( cm_entidad.crazonsoc ) AS proveedor,
                                                        ( SELECT SUM(lg_ordendet.ncanti) FROM lg_ordendet WHERE lg_ordendet.niddeta = tb_pedidodet.iditem AND lg_ordendet.id_orden != 0 ) AS cantidad_orden,
                                                        ( SELECT SUM( alm_recepdet.ncantidad ) FROM alm_recepdet WHERE alm_recepdet.niddetaPed = tb_pedidodet.iditem AND alm_recepdet.nflgactivo = 1 ) AS ingreso,
                                                        ( SELECT SUM( alm_despachodet.ndespacho ) FROM alm_despachodet WHERE alm_despachodet.niddetaPed = lg_ordendet.niddeta AND alm_despachodet.nflgactivo = 1 ) AS despachos,
                                                        ( SELECT SUM( alm_existencia.cant_ingr ) FROM alm_existencia WHERE alm_existencia.idpedido = tb_pedidodet.iditem AND alm_existencia.nflgActivo = 1) AS ingreso_obra,
                                                        ( SELECT SUM( alm_existencia.cant_ingr ) FROM alm_existencia WHERE alm_existencia.idpedido = tb_pedidodet.iditem AND alm_existencia.nflgActivo = 1 ) AS atencion_almacen,
                                                        UPPER( tb_user.cnameuser ) AS operador,
                                                        UPPER( tb_pedidocab.concepto ) AS concepto,
                                                        DATEDIFF(  lg_ordencab.ffechaent, NOW() ) AS dias_atraso,
                                                        transporte.cdescripcion AS transporte,
                                                        transporte.nidreg,
                                                        user_aprueba.cnombres,
                                                        alm_despachocab.cnumguia,
                                                        LPAD(alm_recepcab.nnronota,6,0) AS nota_ingreso,
                                                        LPAD(alm_cabexist.idreg,6,0) AS nota_obra,
                                                        DATE_FORMAT( alm_cabexist.ffechadoc, '%d/%m/%Y' ) AS fecha_ingreso_almacen_obra,
                                                        DATE_FORMAT( alm_recepcab.ffecdoc, '%d/%m/%Y' ) AS fecha_recepcion_proveedor,
                                                        tb_equipmtto.cregistro,
	                                                    usuarios.cnombres AS usuario,
                                                        DATE_ADD( lg_ordencab.ffechades, INTERVAL lg_ordencab.nplazo DAY ) AS fecha_entrega_final_anterior,
                                                        alm_despachodet.id_regalm,
                                                        DATE_FORMAT( GREATEST( COALESCE ( lg_ordencab.fechaLog, '' ), COALESCE ( lg_ordencab.fechaOpe, '' ), COALESCE ( lg_ordencab.FechaFin, '' ) ),'%d/%m/%Y') AS fecha_autorizacion,
                                                        DATE_FORMAT(DATE_ADD(GREATEST( COALESCE ( lg_ordencab.fechaLog, '' ), COALESCE ( lg_ordencab.fechaOpe, '' ), COALESCE ( lg_ordencab.FechaFin, '' ) ), INTERVAL lg_ordencab.nplazo DAY),'%d/%m/%Y') AS fecha_entrega_final,
                                                        alm_transfercab.cnumguia AS guia_transferencia,
                                                        LPAD(alm_transfercab.idreg,6,0) AS nota_transferencia,
                                                        DATE_FORMAT(alm_transfercab.ftraslado,'%d/%m/%Y') AS fecha_traslado
                                                    FROM
                                                        tb_pedidodet
                                                        LEFT JOIN tb_pedidocab ON tb_pedidodet.idpedido = tb_pedidocab.idreg
                                                        LEFT JOIN lg_ordendet ON lg_ordendet.niddeta = tb_pedidodet.iditem
                                                        LEFT JOIN cm_producto ON tb_pedidodet.idprod = cm_producto.id_cprod
                                                        LEFT JOIN tb_proyectos ON tb_pedidodet.idcostos = tb_proyectos.nidreg
                                                        LEFT JOIN tb_area ON tb_pedidodet.idarea = tb_area.ncodarea
                                                        LEFT JOIN tb_partidas ON tb_pedidocab.idpartida = tb_partidas.idreg
                                                        LEFT JOIN tb_unimed ON tb_pedidodet.unid = tb_unimed.ncodmed
                                                        LEFT JOIN lg_ordencab ON lg_ordendet.id_orden = lg_ordencab.id_regmov
                                                        LEFT JOIN cm_entidad ON lg_ordencab.id_centi = cm_entidad.id_centi
                                                        LEFT JOIN tb_user ON lg_ordencab.id_cuser = tb_user.iduser
                                                        LEFT JOIN tb_parametros AS transporte ON lg_ordencab.ctiptransp = transporte.nidreg
                                                        LEFT JOIN tb_user AS user_aprueba ON tb_pedidocab.aprueba = user_aprueba.iduser
                                                        LEFT JOIN alm_despachodet ON tb_pedidodet.iditem = alm_despachodet.niddetaPed
                                                        LEFT JOIN alm_despachocab ON alm_despachodet.id_regalm = alm_despachocab.id_regalm
                                                        LEFT JOIN alm_recepdet ON tb_pedidodet.iditem = alm_recepdet.niddetaPed
                                                        LEFT JOIN alm_recepcab ON alm_recepdet.id_regalm = alm_recepcab.id_regalm
                                                        LEFT JOIN alm_existencia ON tb_pedidodet.iditem = alm_existencia.idpedido
                                                        LEFT JOIN alm_cabexist ON alm_existencia.idregistro = alm_cabexist.idreg
                                                        LEFT JOIN tb_equipmtto ON tb_pedidodet.nregistro = tb_equipmtto.idreg
                                                        LEFT JOIN tb_user AS usuarios ON tb_pedidocab.usuario = usuarios.iduser
                                                        LEFT JOIN alm_transferdet ON alm_transferdet.iddetped = tb_pedidodet.iditem
	                                                    LEFT JOIN alm_transfercab ON alm_transfercab.idreg = alm_transferdet.idtransfer	 
                                            WHERE
                                                tb_pedidodet.nflgActivo
                                                AND ISNULL( lg_ordendet.nflgactivo ) 
                                                AND tb_pedidocab.nrodoc LIKE :pedido 
                                                AND IFNULL( lg_ordencab.cnumero, '' ) LIKE :orden
                                                AND tb_proyectos.nidreg LIKE :costo
                                                AND tb_pedidocab.idtipomov LIKE :tipo
                                                AND cm_producto.ccodprod LIKE :codigo
                                                AND tb_pedidocab.concepto LIKE :concepto
                                                AND tb_pedidodet.estadoItem LIKE :estado
                                                AND CONCAT_WS( ' ', cm_producto.cdesprod, tb_pedidodet.observaciones ) LIKE :descripcion
                                            GROUP BY
                                                tb_pedidodet.iditem");
                                                                                                    
                $sql->execute(["orden"          =>$orden,
                               "pedido"         =>$pedido,
                               "costo"          =>$costo,
                               "codigo"         =>$codigo,
                               "concepto"       =>$concepto,
                               "tipo"           =>$tipo,
                               "estado"         =>$estadoItem,
                               "descripcion"    =>$descrip]);
                
                $rowCount = $sql->rowCount();

                $estado = "";
                $porcentaje = 0;
                $estadofila = 0;
                
                $saldoRecibir = "";
                $saldo = "";
                $dias_atraso = "";
                $estado_pedido = "pendiente";
                $clase_operacion = "";
                $tipo_pedido = "";
                $estado_item = "";
                $transporte = "";
                $itemOrden = 1;
                $nro_orden = 0;

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()){

                        $porcentaje = "100%";

                            if ( $rs['orden'] ){
                                if ( $nro_orden == $rs['orden'] ) {
                                    $itemOrden++;
                                }else{
                                    $itemOrden = 1;
                                }
                            }else {
                                $itemOrden = "";
                            }
                            
                            $tipo_orden = $rs['idtipomov'] == 37 ? 'BIENES' : 'SERVICIO';
                            $clase_operacion = $rs['idtipomov'] == 37 ? 'bienes' : 'servicios';
                            
                            $saldoRecibir = $rs['cantidad_orden'] - $rs['ingreso'] > 0 ? $rs['cantidad_orden'] - $rs['ingreso'] : "-";

                            $dias_atraso  =  "";
                            
                            $estadoSemaforo = "";
                            $semaforo = "";

                            $suma_atendido = number_format($rs['cantidad_orden'] + $rs['atencion_almacen'],2);

                            $estado_pedido =  $rs['estadoItem'] >= 54 ? "Atendido":"Pendiente";
                            $estado_item   =  $rs['estadoItem'] >= 54 ? "Atendido":"Pendiente";

                            $transporte = $rs['nidreg'] == 39 ? "TERRESTRE": $rs['transporte'];

                            $atencion = $rs['atencion'] == 47 ? "NORMAL" : "URGENTE"; 

                            $aprobado=0;

                            $aprobado = $rs['cantidad_aprobada'] == 0 ? $rs['cantidad_pedido']:$rs['cantidad_aprobada'];


                            $aprobado_final = $rs['cantidad_pedido'] - $rs['cantidad_atendida'];

                            if ( $aprobado_final != $rs['cantidad_aprobada'] ) {
                                $aprobado_final = $rs['cantidad_aprobada'];
                            } 
                            

                            $equal = round($suma_atendido,2) === round($aprobado,2) ? true : false;
                           
                            if ( $rs['estadoItem'] == 105 ) {
                                $porcentaje = "0%";
                                $estadofila = "anulado";
                                $estado_item = "anulado";
                                $estado_pedido = "anulado";
                            }else if( $rs['estadoItem'] == 49 ) {
                                $porcentaje = "10%";
                                $estadofila = "stock";
                                $estado_item = "item_stock";
                                $estado_pedido = "stock";
                            }else if( $rs['estadoItem'] == 51 ) {
                                $porcentaje = "12%";
                                $estadofila = "emitido";
                                $estado_item = "Emitido";
                                $estado_pedido = "Pedido Emitido";
                            }else if( $rs['estadoItem'] == 53 ) {
                                $porcentaje = "10%";
                                $estadofila = "emitido";
                                $estado_item = "Emitido";
                                $estado_pedido = "Pedido Emitido";
                            }else if( $rs['estadoItem'] == 230 ) {
                                $porcentaje = "100%";
                                $estadofila = "comprado";
                                $estado_item = "Compra Local";
                                $estado_pedido = "Compra Local";
                            }else if( $rs['estadoItem'] == 54) {
                                $porcentaje = "15%";
                                $estadofila = "item_aprobado";
                                $estado_item = "emitido";
                                $estado_pedido = "emitido";
                            }else if( $rs['estadoItem'] == 52  && $rs['ingreso_obra'] == $rs['cantidad_pedido'] ) {
                                $porcentaje = "100%";
                                $estadofila = "entregado";
                                $estado_item = "atendido";
                                $estado_pedido = "atendido";
                            }else if( $rs['estadoItem'] == 52  && $rs['ingreso_obra'] == $rs['cantidad_aprobada'] && $rs['cantidad_aprobada'] > 0) {
                                $porcentaje = "100%";
                                $estadofila = "entregado";
                                $estado_item = "atendido";
                                $estado_pedido = "atendido";
                            }else if( $rs['estadoItem'] == 52 ) {
                                $porcentaje = "20%";
                                $estadofila = "stock";
                                $estado_item = "item_stock";
                                $estado_pedido = "stock";
                            }else if (!$rs['orden'] ) {
                                $porcentaje = "15%";
                                $estadofila = "item_aprobado";
                                $estado_item = "aprobado";
                                $estado_pedido = "aprobado";   
                            }else if ( $rs['orden'] && !$rs['proveedor']) {
                                $porcentaje = "25%";
                                $estadofila = "item_orden";
                                $estado_item = "aprobado";
                                $estado_pedido = "aprobado";   
                            }else if ( $rs['proveedor'] && !$rs['ingreso'] ) {
                                $porcentaje = "30%";
                                $estadofila = "item_enviado";
                                $estado_item = "atendido";
                                $estado_pedido = "atendido";
                            }else  if( $rs['ingreso'] && $rs['ingreso'] < $rs['cantidad_orden'] ) {
                                $porcentaje = "40%";
                                $estadofila = "item_ingreso_parcial";
                                $estado_item = "atendido";
                                $estado_pedido = "atendido";
                            }else  if( !$rs['despachos'] && $rs['ingreso'] && $rs['ingreso'] == $rs['cantidad_orden'] ) {
                                $porcentaje = "50%";
                                $estadofila = "item_ingreso_total";
                                $estado_item = "atendido";
                                $estado_pedido = "atendido";
                            }else if ( $rs['despachos'] && !$rs['ingreso_obra'] ) {
                                $porcentaje = "75%";
                                $estadofila = "item_transito";
                                $estado_item = "atendido";
                                $estado_pedido = "atendido";
                            }else if ( round($rs['ingreso_obra'],2) < round($rs['cantidad_orden'],2 )) {
                                $porcentaje = "85%";
                                $estadofila = "item_ingreso_parcial";
                                $estado_item = "atendido";
                                $estado_pedido = "atendido";
                            }else if ( $rs['ingreso_obra'] && round($suma_atendido,2) === round($aprobado,2)) {
                                $porcentaje = "100%";
                                $estadofila = "entregado";
                                $estado_item = "atendido";
                                $estado_pedido = "atendido";
                                $semaforo = "Entregado";
                            }else if ( $rs['ingreso_obra'] && round($rs['ingreso_obra'],2) === round($rs['cantidad_orden'],2)) {
                                $porcentaje = "100%";
                                $estadofila = "entregado";
                                $estado_item = "atendido";
                                $estado_pedido = "atendido";
                            }

                            $cantidad = $rs['cantidad_pedido'];


                            $fecha_entrega = "";
                            $fecha_descarga = "";
                            $dias_plazo = intVal( $rs['plazo'] )+1 .' days';

                            $fecha_autoriza = "-";
                            $fecha_entrega = "-";

                            if( $rs['fechaLog'] !== "" && $rs['fechaOpe'] !== "" && $rs['FechaFin'] !== "") {
                                $fecha_autoriza = $rs['fecha_autorizacion'];
                                $fecha_entrega = $rs['fecha_entrega_final'];
                            }

                            if ( $rs['estadoItem'] !== 105 ) {
                                

                                if  ($fecha_entrega !== null){
                                    $dias_atraso  =  $rs['dias_atraso'];

                                    if ( $rs['ingreso_obra'] == $rs['cantidad_orden'] ){
                                        $estadoSemaforo = "semaforoVerde";
                                        $semaforo = "Entregado";
                                        $dias_atraso  = "";
                                    }else if ( $dias_atraso > 7 ) {
                                        $estadoSemaforo = "semaforoVerde";
                                        $semaforo = "Verde";
                                        $dias_atraso  = "";
                                    }else if ( $dias_atraso >= 0 && $dias_atraso <= 7){
                                        $estadoSemaforo = "semaforoNaranja";
                                        $semaforo = "Naranja";
                                        $dias_atraso  = "";
                                    }else if ($dias_atraso < 0) {
                                        $estadoSemaforo = "semaforoRojo";
                                        $semaforo = "Rojo";
                                        $dias_atraso  =  $rs['dias_atraso']*-1;
                                    } 
                                }else {
                                    $dias_atraso  =  "";
                                    $estadoSemaforo = "semaforoAmarillo";
                                    $semaforo = "Procesando";

                                    if ( $rs['ingreso_obra'] > 0 && $rs['ingreso_obra'] === $rs['cantidad_atendida'] ){
                                        $estadoSemaforo = "semaforoVerde";
                                        $semaforo = "Entregado";
                                        $dias_atraso  = "";
                                    }else if ( $rs['cantidad_atendida'] > 0) {
                                        $estadoSemaforo = "semaforoVerde";
                                        $semaforo = "Stock";
                                        $dias_atraso  = "";
                                    }
                                }
                            }else {
                                $estadoSemaforo = "anulado";
                                $semaforo = "Anulado";
                            }

                            $salida.='<tr class="pointer" 
                                        data-itempedido="'.$rs['iditem'].'" 
                                        data-pedido="'.$rs['idpedido'].'" 
                                        data-orden="'.$rs['orden'].'"
                                        data-estado="'.$rs['estadoItem'].'"
                                        data-producto="'.$rs['idprod'].'"
                                        data-aprueba="'.$rs['cnombres'].'"
                                        data-despacho="'.$rs['id_regalm'].'"
                                        data-porcentaje="'.$rs['ingreso_obra'].'">
                                        <td class="textoCentro">'.$equal.'</td>
                                        <td class="textoCentro '.$estadofila.'">'.$porcentaje.'</td>
                                        <td class="textoDerecha pr15px">'.$rs['ccodproy'].'</td>
                                        <td class="pl20px">'.$rs['area'].'</td>
                                        <td class="pl20px">'.$rs['partida'].'</td>
                                        <td class="textoCentro ">'.$atencion.'</td>
                                        <td class="textoCentro '.$clase_operacion.'">'.$tipo_orden.'</td>
                                        <td class="textoCentro">'.$rs['anio_pedido'].'</td>
                                        <td class="textoCentro">'.$rs['pedido'].'</td>
                                        <td class="textoCentro">'.$rs['crea_pedido'].'</td>
                                        <td class="textoCentro">'.$rs['aprobacion_pedido'].'</td>
                                        <td class="textoDerecha">'.number_format($cantidad,2,'.', '').'</td>
                                        <td class="textoDerecha">'.number_format($aprobado,2,'.', '').'</td>
                                        <td class="textoCentro">'.number_format($aprobado_final,2,'.', '').'</td>
                                        <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                        <td class="textoCentro">'.$rs['unidad'].'</td>
                                        <td class="pl10px">'.$rs['descripcion'].'</td>
                                        <td class="textoCentro '.$clase_operacion.'">'.$tipo_orden.'</td>
                                        <td class="textoCentro">'.$rs['anio_orden'].'</td>
                                        <td class="textoCentro">'.$rs['cnumero'].'</td>
                                        <td class="textoCentro">'.$rs['fecha_orden'].'</td>
                                        <td class="textoDerecha pr15px" style="background:#e8e8e8;font-weight: bold">'.$rs['cantidad_orden'].'</td>
                                        <td class="pl10px">'.$rs['item_orden'].'</td>
                                        <td class="pl10px">'.$fecha_autoriza.'</td>
                                        <td class="textoDerecha pr15px">'.number_format($rs['cantidad_atendida'],2).'</td>
                                        <td class="pl10px">'.$rs['proveedor'].'</td>
                                        <td class="textoCentro">'.$fecha_entrega.'</td>

                                        <td class="textoDerecha pr15px">'.$rs['ingreso'].'</td>
                                        <td class="textoCentro">'.$rs['nota_ingreso'].'</td>
                                        <td class="textoCentro">'.$rs['fecha_recepcion_proveedor'].'</td>

                                        <td class="textoDerecha pr15px">'.$saldoRecibir.'</td>
                                        <td class="textoDerecha pr15px">'.$rs['plazo'].'</td>
                                        <td class="textoDerecha pr15px">'.$dias_atraso.'</td>
                                        <td class="textoCentro '.$estadoSemaforo.'">'.$semaforo.'</td>
                                        <td class="textoDerecha">'.$rs['despachos'].'</td>
                                        <td class="textoCentro">'.$rs['cnumguia'].'</td>
                                        <td class="textoCentro">'.$rs['nota_transferencia'].'</td>
                                        <td class="textoCentro">'.$rs['fecha_traslado'].'</td>
                                        <td class="textoCentro">'.$rs['nota_obra'].'</td>
                                        <td class="textoCentro">'.$rs['fecha_ingreso_almacen_obra'].'</td>
                                        <td class="textoDerecha">'.number_format($rs['ingreso_obra'],2).'</td>
                                        <td class="textoCentro">'.$estado_pedido.'</td>
                                        <td class="textoCentro">'.$estado_item.'</td>
                                        <td class="textoCentro">'.$rs['nroparte'].'</td>
                                        <td class="textoCentro">'.$rs['cregistro'].'</td>
                                        <td class="textoCentro">'.$rs['operador'].'</td>
                                        <td class="textoCentro">'.$transporte.'</td>
                                        <td class="pl10px">'.$rs['concepto'].'</td>
                                        <td class="pl10px">'.$rs['usuario'].'</td>
                                </tr>';
                                
                                $nro_orden = $rs['orden'];
                    }
                }else {
                    $salida = "Buscar el pedido";
                }
                return $salida;
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function cantidadesRecepcion($idprod,$pedido,$item) {
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                FORMAT(SUM( alm_recepdet.ncantidad ),2) AS ingresos,
                                                LPAD(alm_recepdet.id_regalm,6,0) AS nota_ingreso,
                                                alm_recepcab.nnronota,
                                                alm_recepcab.cnumguia AS guia_proveedor,
                                                DATE_FORMAT(alm_recepcab.ffecdoc,'%d/%m/%Y') AS fecha_ingreso
                                            FROM
                                                alm_recepdet
                                                INNER JOIN alm_recepcab ON alm_recepdet.id_regalm = alm_recepcab.id_regalm  
                                            WHERE
                                                alm_recepdet.id_cprod = :producto
                                            AND alm_recepdet.orden = :pedido
                                            AND alm_recepdet.niddetaPed = :itempedido");
                $sql->execute(["pedido"=>$pedido, "producto"=>$idprod,"itempedido"=>$item]);
                $result = $sql->fetchAll();

                return $result;

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function cantidadesDespacho($idprod,$orden,$item) {
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                            FORMAT( SUM( alm_despachodet.ndespacho ), 2 ) AS despacho,
                                                            LPAD( alm_despachocab.nnronota, 6, 0 ) AS nota_salida,
                                                            alm_despachocab.cnumguia AS guia_sepcon,
                                                            DATE_FORMAT( alm_despachocab.ffecdoc, '%d/%m/%Y' ) AS fecha_despacho 
                                                        FROM
                                                            alm_despachodet
                                                            INNER JOIN alm_despachocab ON alm_despachodet.id_regalm = alm_despachocab.id_regalm 
                                                        WHERE
                                                            alm_despachodet.nropedido = :orden 
                                                            AND alm_despachodet.id_cprod = :producto 
                                                            AND alm_despachodet.niddetaPed = :itempedido");
                $sql->execute(["orden"=>$orden, "producto"=>$idprod,"itempedido"=>$item]);
                $result = $sql->fetchAll();

                return $result;

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function cantidadesRecepcionObra($idprod,$orden,$item) {
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                    FORMAT( SUM( alm_existencia.cant_ingr ), 2 ) AS ingreso_obra,
                                                    DATE_FORMAT( alm_cabexist.ffechadoc, '%d/%m/%Y' ) AS fecha_recepcion,
                                                    LPAD( alm_cabexist.idreg, 6, 0 ) AS nota_recepcion,
                                                    LPAD( alm_existencia.nguia, 6, 0 ) AS guia_recepcion 
                                                FROM
                                                    alm_existencia
                                                    INNER JOIN alm_cabexist ON alm_existencia.idalm = alm_cabexist.idreg 
                                                WHERE
                                                    alm_existencia.idpedido = :itempedido 
                                                    AND alm_existencia.nropedido = :orden 
                                                    AND alm_existencia.codprod = :producto");
                $sql->execute(["orden"=>$orden, "producto"=>$idprod,"itempedido"=>$item]);
                $result = $sql->fetchAll();

                return $result;

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function exportExcel($registros){
            require_once('public/PHPExcel/PHPExcel.php');
            try {
                $objPHPExcel = new PHPExcel();
                
                $objPHPExcel->getProperties()
                    ->setCreator("Sical")
                    ->setLastModifiedBy("Sical")
                    ->setTitle("Cargo Plan")
                    ->setSubject("Template excel")
                    ->setDescription("Cargo Plan")
                    ->setKeywords("Template excel");

                $cuerpo = array(
                    'font'  => array(
                    'bold'  => false,
                    'size'  => 7,
                ));

                $objWorkSheet = $objPHPExcel->createSheet(1);

                $objPHPExcel->setActiveSheetIndex(0);
                $objPHPExcel->getActiveSheet()->setTitle("Cargo Plan");

                $objPHPExcel->getActiveSheet()->mergeCells('A1:AW1');
                $objPHPExcel->getActiveSheet()->setCellValue('A1','CARGO PLAN');

                $objPHPExcel->getActiveSheet()->getStyle('A1:AW2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('A1:AW2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(60);

                $objPHPExcel->getActiveSheet()->getColumnDimension("D")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("E")->setWidth(50);
                $objPHPExcel->getActiveSheet()->getColumnDimension("J")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("K")->setWidth(10);
                $objPHPExcel->getActiveSheet()->getColumnDimension("L")->setWidth(15);
                $objPHPExcel->getActiveSheet()->getColumnDimension("M")->setWidth(15);
                $objPHPExcel->getActiveSheet()->getColumnDimension("N")->setWidth(15);
                $objPHPExcel->getActiveSheet()->getColumnDimension("O")->setWidth(24);
                $objPHPExcel->getActiveSheet()->getColumnDimension("Q")->setWidth(100);
                $objPHPExcel->getActiveSheet()->getColumnDimension("S")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("T")->setWidth(10);
                $objPHPExcel->getActiveSheet()->getColumnDimension("U")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("V")->setWidth(15);
                $objPHPExcel->getActiveSheet()->getColumnDimension("W")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("X")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("Y")->setWidth(9);
                $objPHPExcel->getActiveSheet()->getColumnDimension("Z")->setWidth(100);
                $objPHPExcel->getActiveSheet()->getColumnDimension("Y")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("AA")->setWidth(10);
                $objPHPExcel->getActiveSheet()->getColumnDimension("AB")->setWidth(10);
                $objPHPExcel->getActiveSheet()->getColumnDimension("AC")->setWidth(13);
                $objPHPExcel->getActiveSheet()->getColumnDimension("AD")->setWidth(15);
                $objPHPExcel->getActiveSheet()->getColumnDimension("AE")->setWidth(12);
                $objPHPExcel->getActiveSheet()->getColumnDimension("AF")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("AG")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("AH")->setWidth(12);
                $objPHPExcel->getActiveSheet()->getColumnDimension("AI")->setWidth(12);
                $objPHPExcel->getActiveSheet()->getColumnDimension("AJ")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("AK")->setWidth(14);
                $objPHPExcel->getActiveSheet()->getColumnDimension("AL")->setWidth(12);
                $objPHPExcel->getActiveSheet()->getColumnDimension("AM")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("AN")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("AO")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("AP")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("AQ")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("AR")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("AS")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("AT")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("AU")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("AV")->setWidth(50);
                $objPHPExcel->getActiveSheet()->getColumnDimension("AW")->setAutoSize(true);

                $objPHPExcel->getActiveSheet()
                            ->getStyle('A2:K2')
                            ->getFill()
                            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB('BFCDDB');
                
                $objPHPExcel->getActiveSheet()
                            ->getStyle('L2:N2')
                            ->getFill()
                            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB('FC4236');

                $objPHPExcel->getActiveSheet()
                            ->getStyle('O2:P2')
                            ->getFill()
                            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB('BFCDDB');

                $objPHPExcel->getActiveSheet()
                            ->getStyle('Q2:V2')
                            ->getFill()
                            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB('00FFFF');
                
                $objPHPExcel->getActiveSheet()
                            ->getStyle('W2:AD2')
                            ->getFill()
                            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB('BFCDDB');

                $objPHPExcel->getActiveSheet()
                            ->getStyle('AE2:AM2')
                            ->getFill()
                            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB('FFFF00');

                $objPHPExcel->getActiveSheet()
                            ->getStyle('AN2:AW2')
                            ->getFill()
                            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB('127BDD');

                $objPHPExcel->getActiveSheet()->getStyle('A1:AW2')->getAlignment()->setWrapText(true);


                $objPHPExcel->getActiveSheet()->setCellValue('A2','Items'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('B2','Estado Actual'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('C2','Codigo Proyecto'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('D2','Area'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('E2','Partida'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('F2','Atención'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('G2','Tipo'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('H2','Año Pedido'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('I2','N° Pedido'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('J2','Creación Pedido'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('K2','Aprobación del Pedido'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('L2','Cantidad Pedida'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('M2','Cantidad Aprobada'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('N2','Cantidad Compra'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('O2','Codigo del Bien/Servicio'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('P2','Unidad Medida'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('Q2','Descripcion del Bien/Servicio'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('R2','Tipo Orden'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('S2','Año Orden'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('T2','Nro Orden'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('U2','Fecha Orden'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('V2','Cantidad Orden'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('W2','Item Orden'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('X2','Fecha Autorizacion'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('Y2','Atencion Almacen'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('Z2','Descripcion del proveedor'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AA2','Fecha Entrega Proveedor'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AB2','Cant. Recibida'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AC2','Nota de Ingreso'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AD2','Fecha Recepcion Proveedor'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AE2','Saldo por Recibir'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AF2','Dias Entrega'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AG2','Días Atrazo'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AH2','Semáforo'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AI2','Cantidad Despachada'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AJ2','Nro. Guia'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AK2','Fecha Traslado'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AL2','Nro. Nota Transferencia'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AM2','Registro Almacen'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AN2','Fecha Ingreso Almacen'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AO2','Cantidad en Obra'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AP2','Estado Pedido'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AQ2','Estado Item'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AR2','N° Parte'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AS2','Codigo Activo'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AT2','Operador Logístico'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AU2','Tipo Transporte'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AV2','Observaciones/Concepto'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AW2','Solicitante'); // esto cambia
               
                $fila = 3;
                $datos = json_decode($registros);
                $nreg = count($datos);

                $objPHPExcel->getActiveSheet()->getStyle('J')->getNumberFormat()->setFormatCode('dd/mm/yyyy');
                $objPHPExcel->getActiveSheet()->getStyle('K')->getNumberFormat()->setFormatCode('dd/mm/yyyy');
                $objPHPExcel->getActiveSheet()->getStyle('L')->getNumberFormat()->setFormatCode('#,##0.00');
                $objPHPExcel->getActiveSheet()->getStyle('M')->getNumberFormat()->setFormatCode('#,##0.00');
                $objPHPExcel->getActiveSheet()->getStyle('N')->getNumberFormat()->setFormatCode('#,##0.00');

                $objPHPExcel->getActiveSheet()->getStyle('O')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

                $objPHPExcel->getActiveSheet()->getStyle('U')->getNumberFormat()->setFormatCode('dd/mm/yyyy');
                $objPHPExcel->getActiveSheet()->getStyle('V')->getNumberFormat()->setFormatCode('#,##0.00');
                $objPHPExcel->getActiveSheet()->getStyle('X')->getNumberFormat()->setFormatCode('dd/mm/yyyy');

                $objPHPExcel->getActiveSheet()->getStyle('Y')->getNumberFormat()->setFormatCode('#,##0.00');
                $objPHPExcel->getActiveSheet()->getStyle('AA')->getNumberFormat()->setFormatCode('dd/mm/yyyy');
                $objPHPExcel->getActiveSheet()->getStyle('AB')->getNumberFormat()->setFormatCode('#,##0.00');
                $objPHPExcel->getActiveSheet()->getStyle('AI')->getNumberFormat()->setFormatCode('#,##0.00');
                $objPHPExcel->getActiveSheet()->getStyle('AK')->getNumberFormat()->setFormatCode('dd/mm/yyyy');


                for ($i=0; $i < $nreg ; $i++) {

                    $color_mostrar  = 'FFFFFF';
                    $color_semaforo = 'FFFFFF';

                    //color estado Item
                    if ( $datos[$i]->estado == "0%"){
                        $color_mostrar = 'C8C8C8';
                    }else if ( $datos[$i]->estado == "10%") {
                        $color_mostrar = 'F8CAAD';
                    }else if ( $datos[$i]->estado == "15%") {
                        $color_mostrar = 'FF0000';
                    }else if ( $datos[$i]->estado == "20%") {
                        $color_mostrar = 'B3C5E6';
                    }else if ( $datos[$i]->estado == "25%") {
                        $color_mostrar = 'FFFF00';
                    }else if ( $datos[$i]->estado == "30%") {
                        $color_mostrar = 'C0DCC0';
                    }else if ( $datos[$i]->estado == "40%") {
                        $color_mostrar = 'FFFFE1';
                    }else if ( $datos[$i]->estado == "50%") {
                        $color_mostrar = 'A9D08F';
                    }else if ( $datos[$i]->estado == "60%") {
                        $color_mostrar = 'FF00FF';
                    }else if ( $datos[$i]->estado == "70%") {
                        $color_mostrar = 'FFC000';
                    }else if ( $datos[$i]->estado == "75%") {
                        $color_mostrar = '00FFFF';
                    }else if ( $datos[$i]->estado == "100%") {
                        $color_mostrar = '00FF00';
                    }

                    //color semaforo
                    if($datos[$i]->semaforo == "Verde" ) {
                        $color_semaforo = '90EE90';
                    }else if($datos[$i]->semaforo == "Entregado" ) {
                        $color_semaforo = '90EE90';
                    }else if($datos[$i]->semaforo == "Naranja" ) {
                        $color_semaforo = 'FFD700';
                    }else if($datos[$i]->semaforo == "Rojo" ) {
                        $color_semaforo = 'FF0000';
                    }
                    
                    $color = array(
                        'fill' => array(
                            'type' => PHPExcel_Style_Fill::FILL_SOLID,
                            'startcolor' => array(
                                'argb' => $color_mostrar,
                            ),
                            'endcolor' => array(
                                'argb' =>  $color_mostrar,
                            ),
                        ),
                    );

                    $semaforo = array(
                        'fill' => array(
                            'type' => PHPExcel_Style_Fill::FILL_SOLID,
                            'startcolor' => array(
                                'argb' => $color_semaforo,
                            ),
                            'endcolor' => array(
                                'argb' =>  $color_semaforo,
                            ),
                        ),
                    );

                    $objPHPExcel->getActiveSheet()->setCellValue('A'.$fila,$datos[$i]->item);
                    $objPHPExcel->getActiveSheet()->setCellValue('B'.$fila,$datos[$i]->estado);
                    $objPHPExcel->getActiveSheet()->getStyle('B'.$fila)->applyFromArray($color);
                        
                    $objPHPExcel->getActiveSheet()->setCellValue('C'.$fila,$datos[$i]->proyecto);
                    $objPHPExcel->getActiveSheet()->setCellValue('D'.$fila,$datos[$i]->area);
                    $objPHPExcel->getActiveSheet()->setCellValue('E'.$fila,$datos[$i]->partida);
                    $objPHPExcel->getActiveSheet()->setCellValue('F'.$fila,$datos[$i]->atencion);
                    $objPHPExcel->getActiveSheet()->setCellValue('G'.$fila,$datos[$i]->tipo);
                    $objPHPExcel->getActiveSheet()->setCellValue('H'.$fila,$datos[$i]->anio_pedido);
                    $objPHPExcel->getActiveSheet()->setCellValue('I'.$fila,$datos[$i]->num_pedido);

                    if  ($datos[$i]->crea_pedido !== "")
                        $objPHPExcel->getActiveSheet()->setCellValue('J'.$fila,PHPExcel_Shared_Date::PHPToExcel($datos[$i]->crea_pedido));

                    if  ($datos[$i]->apro_pedido !== "")
                        $objPHPExcel->getActiveSheet()->setCellValue('K'.$fila,PHPExcel_Shared_Date::PHPToExcel($datos[$i]->apro_pedido));

                    $aprobado = $datos[$i]->aprobado == 0 ? $datos[$i]->cantidad : $datos[$i]->aprobado;
                    $aprobado_final =  floatval($aprobado)-floatval($datos[$i]->cantidad) < 0 ? 0 : $aprobado-$datos[$i]->cantidad;

                    $objPHPExcel->getActiveSheet()->setCellValue('L'.$fila,$datos[$i]->cantidad);
                    $objPHPExcel->getActiveSheet()->setCellValue('M'.$fila,$aprobado_final);
                    $objPHPExcel->getActiveSheet()->setCellValue('N'.$fila,$datos[$i]->compra);
                    $objPHPExcel->getActiveSheet()->setCellValue('O'.$fila,$datos[$i]->codigo);
                    
                    $objPHPExcel->getActiveSheet()->setCellValue('P'.$fila,$datos[$i]->unidad);
                    $objPHPExcel->getActiveSheet()->setCellValue('Q'.$fila,$datos[$i]->descripcion);
                    $objPHPExcel->getActiveSheet()->setCellValue('R'.$fila,$datos[$i]->tipo_orden);
                    $objPHPExcel->getActiveSheet()->setCellValue('S'.$fila,$datos[$i]->anio_orden);
                    $objPHPExcel->getActiveSheet()->setCellValue('T'.$fila,$datos[$i]->nro_orden);

                    if  ($datos[$i]->fecha_orden !== "")
                        $objPHPExcel->getActiveSheet()->setCellValue('U'.$fila,PHPExcel_Shared_Date::PHPToExcel($datos[$i]->fecha_orden));
                        

                    $objPHPExcel->getActiveSheet()->setCellValue('V'.$fila,$datos[$i]->cantidad_orden);
                    $objPHPExcel->getActiveSheet()->setCellValue('W'.$fila,$datos[$i]->item_orden);

                    if  ($datos[$i]->autoriza_orden !== "")
                        $objPHPExcel->getActiveSheet()->setCellValue('X'.$fila,PHPExcel_Shared_Date::PHPToExcel($datos[$i]->autoriza_orden));

                    $objPHPExcel->getActiveSheet()->setCellValue('Y'.$fila,$datos[$i]->cantidad_almacen);
                    
                    $objPHPExcel->getActiveSheet()->setCellValue('Z'.$fila,$datos[$i]->proveedor);

                    if  ( $datos[$i]->fecha_entrega !== "" )
                        $objPHPExcel->getActiveSheet()->setCellValue('AA'.$fila,PHPExcel_Shared_Date::PHPToExcel($datos[$i]->fecha_entrega));

                    $objPHPExcel->getActiveSheet()->setCellValue('AB'.$fila,$datos[$i]->cantidad_recibida);
                    $objPHPExcel->getActiveSheet()->setCellValue('AC'.$fila,$datos[$i]->nota_ingreso);

                    if  ($datos[$i]->fecha_recepcion !== "")
                        $objPHPExcel->getActiveSheet()->setCellValue('AD'.$fila,PHPExcel_Shared_Date::PHPToExcel($datos[$i]->fecha_recepcion));
                    
                    $objPHPExcel->getActiveSheet()->setCellValue('AE'.$fila,$datos[$i]->saldo_recibir);
                    $objPHPExcel->getActiveSheet()->setCellValue('AF'.$fila,$datos[$i]->dias_entrega);
                    $objPHPExcel->getActiveSheet()->setCellValue('AG'.$fila,$datos[$i]->dias_atraso);
                    
                    $objPHPExcel->getActiveSheet()->setCellValue('AH'.$fila,$datos[$i]->semaforo);
                    $objPHPExcel->getActiveSheet()->getStyle('AH'.$fila)->applyFromArray($semaforo);
                    
                    $objPHPExcel->getActiveSheet()->setCellValue('AI'.$fila,$datos[$i]->despacho);
                    $objPHPExcel->getActiveSheet()->setCellValue('AJ'.$fila,$datos[$i]->numero_guia);

                    $objPHPExcel->getActiveSheet()->setCellValue('AK'.$fila,$datos[$i]->fecha_traslado);
                    $objPHPExcel->getActiveSheet()->setCellValue('AL'.$fila,$datos[$i]->guia_transfer);
                    

                    $objPHPExcel->getActiveSheet()->setCellValue('AM'.$fila,$datos[$i]->registro_almacen);

                    if  ($datos[$i]->fecha_registro_obra !== "")
                        $objPHPExcel->getActiveSheet()->setCellValue('AN'.$fila,PHPExcel_Shared_Date::PHPToExcel($datos[$i]->fecha_registro_obra));

                    $objPHPExcel->getActiveSheet()->setCellValue('AO'.$fila,$datos[$i]->cantidad_obra);

                    $objPHPExcel->getActiveSheet()->setCellValue('AP'.$fila,$datos[$i]->estado_pedido);
                    $objPHPExcel->getActiveSheet()->setCellValue('AQ'.$fila,$datos[$i]->estado_item);
                    $objPHPExcel->getActiveSheet()->setCellValue('AR'.$fila,$datos[$i]->numero_parte);
                    $objPHPExcel->getActiveSheet()->setCellValue('AS'.$fila,$datos[$i]->codigo_activo);
                    $objPHPExcel->getActiveSheet()->setCellValue('AT'.$fila,$datos[$i]->operador);
                    $objPHPExcel->getActiveSheet()->setCellValue('AU'.$fila,$datos[$i]->transporte);
                    $objPHPExcel->getActiveSheet()->setCellValue('AV'.$fila,$datos[$i]->observaciones);
                    $objPHPExcel->getActiveSheet()->setCellValue('AW'.$fila,$datos[$i]->solicitante);

                    $fila++;               
                }

                $objPHPExcel->getActiveSheet()->getStyle('B:C')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('B:C')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                
                $objPHPExcel->getActiveSheet()->getStyle('F:K')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('F:K')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $objPHPExcel->getActiveSheet()->getStyle('O:P')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('O:P')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $objPHPExcel->getActiveSheet()->getStyle('R:S')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('R:S')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $objPHPExcel->getActiveSheet()->getStyle('Y')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('Y')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $objPHPExcel->getActiveSheet()->getStyle('AA')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('AA')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $objPHPExcel->getActiveSheet()->getStyle('AC')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('AC')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $objPHPExcel->getActiveSheet()->getStyle('AE')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('AE')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $objPHPExcel->getActiveSheet()->getStyle('AH')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('AH')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $objPHPExcel->getActiveSheet()->getStyle('AJ:AO')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('AJ:AO')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');
                $objWriter->save('public/documentos/reportes/cargoplan.xlsx');

                return array("documento"=>'public/documentos/reportes/cargoplan.xlsx');

                exit();

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function consultaResumen($orden,$refpedido) {
            return array("orden"=>$this->ordenes($orden),
                        "ingresos"=>$this->ingresos($refpedido),
                        "despachos"=>$this->despachos($refpedido),
                        "registros"=>$this->registros($refpedido));
        }

        private function ordenes($orden) {
            try {
                $salida = "";
                $sql = $this->db->connect()->prepare("SELECT
                                                            lg_ordencab.id_regmov,
                                                            LPAD(lg_ordencab.cnumero,6,0) AS cnumero,
                                                            DATE_FORMAT( lg_ordencab.ffechadoc, '%d/%m/%Y' ) AS ffechadoc,
                                                            tb_proyectos.ccodproy,
                                                            cm_entidad.crazonsoc 
                                                        FROM
                                                            lg_ordencab
                                                            INNER JOIN cm_entidad ON lg_ordencab.id_centi = cm_entidad.id_centi
                                                            INNER JOIN tb_proyectos ON lg_ordencab.ncodpry = tb_proyectos.nidreg 
                                                        WHERE
                                                            lg_ordencab.id_regmov = :orden");
                $sql->execute(["orden"=>$orden]);
                $rowCount = $sql->rowCount();
                
                if($rowCount > 0) {
                    while($rs = $sql->fetch()){
                        $salida .= '<tr>
                                        <td class="textoCentro">'.$rs['cnumero'].'</td>
                                        <td class="textoCentro">'.$rs['ffechadoc'].'</td>
                                        <td class="pl20px">'.$rs['crazonsoc'].'</td>
                                        <td class="pl20px">'.$rs['ccodproy'].'</td>
                                        <td class="textoDerecha"><a href="'.$rs['id_regmov'].'"><i class="far fa-file-pdf"></i></a></td>
                                    </tr>';
                    }
                }

                return $salida;

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function ingresos($refpedi) {
            try {
                $salida = "";
                $sql = $this->db->connect()->prepare("SELECT
                                                        alm_recepdet.niddeta,
                                                        alm_recepdet.niddetaPed,
                                                        alm_recepdet.niddetaOrd,
                                                        alm_recepcab.nnronota,
                                                        alm_recepcab.id_regalm,
                                                        DATE_FORMAT( alm_recepcab.ffecdoc, '%d/%m/%Y' ) AS ffecdoc,
                                                        alm_recepcab.cnumguia 
                                                    FROM
                                                        alm_recepdet
                                                        INNER JOIN alm_recepcab ON alm_recepdet.id_regalm = alm_recepcab.id_regalm 
                                                    WHERE
                                                        alm_recepdet.niddetaPed = :ref_pedi 
                                                        AND alm_recepdet.nflgactivo = 1");
                $sql->execute(["ref_pedi"=>$refpedi]);
                $rowCount = $sql->rowCount();
                
                if($rowCount > 0) {
                    while($rs = $sql->fetch()){
                        $salida .= '<tr>
                                        <td class="textoCentro">'.$rs['nnronota'].'</td>
                                        <td class="textoCentro">'.$rs['ffecdoc'].'</td>
                                        <td class="textoCentro">'.$rs['cnumguia'].'</td>
                                        <td class="textoDerecha"><a href="'.$rs['id_regalm'].'"><i class="far fa-file-pdf"></i></a></td>
                                    </tr>';
                    }
                }

                return $salida;

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function despachos($refpedi) {
            try {
                $salida = "";
                $sql = $this->db->connect()->prepare("SELECT
                                                        alm_despachodet.niddeta,
                                                        alm_despachodet.id_regalm,
                                                        LPAD(alm_despachocab.nnronota,6,0) AS nnronota, 
                                                        DATE_FORMAT(alm_despachocab.ffecdoc,'%d/%m/%Y') AS ffecdoc, 
                                                        alm_despachocab.cnumguia, 
                                                        DATE_FORMAT(alm_despachocab.ffecenvio,'%d/%m/%Y') AS ffecenvio, 
                                                        alm_despachocab.nReferido
                                                    FROM
                                                        alm_despachodet
                                                        INNER JOIN
                                                        alm_despachocab
                                                        ON 
                                                            alm_despachodet.id_regalm = alm_despachocab.id_regalm
                                                    WHERE
                                                        alm_despachodet.niddetaPed = :ref_pedi
                                                        AND alm_despachodet.nflgactivo = 1");
                $sql->execute(["ref_pedi"=>$refpedi]);
                $rowCount = $sql->rowCount();
                
                if($rowCount > 0) {
                    while($rs = $sql->fetch()){
                        $salida .= '<tr>
                                        <td class="textoCentro">'.$rs['nnronota'].'</td>
                                        <td class="textoCentro">'.$rs['ffecdoc'].'</td>
                                        <td class="textoCentro">'.$rs['cnumguia'].'</td>
                                        <td class="textoCentro">'.$rs['nReferido'].'</td>
                                        <td class="textoDerecha"><a href="'.$rs['id_regalm'].'"><i class="far fa-file-pdf"></i></a></td>
                                    </tr>';
                    }
                }

                return $salida;

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function registros($refpedi) {
            try {
                $salida = "";
                $sql = $this->db->connect()->prepare("SELECT
                                                        alm_existencia.idregistro,
                                                        DATE_FORMAT( alm_cabexist.ffechadoc, '%d/%m/%Y' ) AS ffechadoc,
                                                        alm_existencia.idreg 
                                                    FROM
                                                        alm_existencia
                                                        INNER JOIN alm_cabexist ON alm_existencia.idregistro = alm_cabexist.idreg 
                                                    WHERE
                                                        alm_existencia.idpedido = :ref_pedi 
                                                        AND alm_existencia.nflgActivo = 1");
                $sql->execute(["ref_pedi"=>$refpedi]);
                $rowCount = $sql->rowCount();
                
                if($rowCount > 0) {
                    while($rs = $sql->fetch()){
                        $salida .= '<tr>
                                        <td class="textoCentro">'.$rs['idregistro'].'</td>
                                        <td class="textoCentro">'.$rs['ffechadoc'].'</td>
                                        <td class="textoDerecha"><a href="'.$rs['idregistro'].'"><i class="far fa-file-pdf"></i></a></td>
                                    </tr>';
                    }
                }

                return $salida;

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        //OPCIONES PARA EXPORTAR TOTAL
        public function exportarTotal($estado){
            try {
                $salida = "";
                $docData = [];

                /*if (file_exists('public/documentos/reportes/cargoplan.xlsx')) {
                    $archivo = 'public/documentos/reportes/cargoplan.xlsx';

                    $fileCreationTime = filectime($archivo);

                    // Obtener la fecha y hora actual
                    $currentTime = time();

                    // Calcular la diferencia en segundos
                    $differenceInSeconds = $currentTime - $fileCreationTime;

                    // Convertir la diferencia en días
                    $differenceInDays = floor($differenceInSeconds / (60 * 60 * 24));

                    // Convertir la diferencia en horas
                    $differenceInHours = floor($differenceInSeconds / (60 * 60));

                    if ( $differenceInHours < 4 )
                        return array("documento"=>$archivo);
                }*/

                $sql = $this->db->connect()->query("SELECT
                                                    tb_pedidodet.iditem,
                                                    tb_pedidodet.idpedido,
                                                    tb_pedidodet.idprod,
                                                    tb_pedidodet.nroparte,
                                                    tb_pedidodet.nregistro,
                                                    tb_pedidodet.cant_pedida AS cantidad_pedido,
                                                    tb_pedidodet.cant_aprob AS cantidad_aprobada,
                                                    tb_pedidodet.cant_atend AS cantidad_atendida,
                                                    LPAD( tb_pedidocab.nrodoc, 6, 0 ) AS pedido,
                                                    lg_ordendet.id_orden AS orden,
                                                    lg_ordendet.item AS item_orden,
                                                    cm_producto.ccodprod,
                                                    UPPER( CONCAT_WS( ' ', cm_producto.cdesprod, tb_pedidodet.observaciones ) ) AS descripcion,
                                                    tb_pedidodet.estadoItem,
                                                    tb_proyectos.ccodproy,
                                                    tb_proyectos.nidreg AS idproyecto,
                                                    UPPER( tb_area.cdesarea ) AS area,
                                                    UPPER( tb_partidas.cdescripcion ) AS partida,
                                                    DATE_FORMAT( tb_pedidocab.emision, '%d/%m/%Y' ) AS crea_pedido,
                                                    DATE_FORMAT( tb_pedidocab.faprueba, '%d/%m/%Y' ) AS aprobacion_pedido,
                                                    DATE_FORMAT( lg_ordencab.ffechades, '%d/%m/%Y' ) AS fecha_descarga,
                                                    tb_pedidocab.anio AS anio_pedido,
                                                    tb_pedidocab.mes AS pedido_mes,
                                                    tb_pedidocab.nivelAten AS atencion,
                                                    tb_pedidocab.idtipomov,
                                                    tb_unimed.cabrevia AS unidad,
                                                    lg_ordencab.cper AS anio_orden,
                                                    lg_ordencab.ntipmov,
                                                    lg_ordencab.FechaFin,
                                                    LPAD(lg_ordencab.cnumero,6,0) AS cnumero,
                                                    lg_ordencab.fechaLog,
                                                    lg_ordencab.fechaOpe,
                                                    lg_ordencab.nNivAten,
                                                    DATE_FORMAT( lg_ordencab.ffechadoc, '%d/%m/%Y' ) AS fecha_orden,
                                                    UPPER( cm_entidad.crazonsoc ) AS proveedor,
                                                    UPPER( tb_user.cnameuser ) AS operador,
                                                    UPPER( tb_pedidocab.concepto ) AS concepto,
                                                    DATEDIFF( lg_ordencab.ffechaent, NOW() ) AS dias_atraso,
                                                    transporte.cdescripcion AS transporte,
                                                    transporte.nidreg,
                                                    user_aprueba.cnombres,
                                                    alm_despachocab.cnumguia,
                                                    LPAD( alm_recepcab.nnronota, 6, 0 ) AS nota_ingreso,
                                                    LPAD( alm_cabexist.idreg, 6, 0 ) AS nota_obra,
                                                    tb_equipmtto.cregistro,
                                                    o.cantidad_orden,
                                                    i.ingreso,
                                                    d.despachos,
                                                    a.ingreso_obra,
                                                    usuarios.cnombres AS nombre_elabora,
                                                    DATE_FORMAT( alm_recepcab.ffecdoc, '%d/%m/%Y' ) AS fecha_recepcion_proveedor,
                                                    DATE_FORMAT( alm_cabexist.ffechadoc, '%d/%m/%Y' ) AS fecha_registro_almacen,
                                                    alm_transfercab.cnumguia AS guia_transferencia,
                                                    LPAD(alm_transfercab.idreg,6,0) AS nota_transferencia,
                                                    DATE_FORMAT( alm_transfercab.ftraslado, '%d/%m/%Y' ) AS fecha_traslado,
                                                    DATE_FORMAT(
                                                        GREATEST( COALESCE ( lg_ordencab.fechaLog, '' ), COALESCE ( lg_ordencab.fechaOpe, '' ), COALESCE ( lg_ordencab.FechaFin, '' ) ),
                                                        '%d/%m/%Y' 
                                                    ) AS fecha_autorizacion,
                                                    DATE_FORMAT(
                                                        DATE_ADD(
                                                            GREATEST( COALESCE ( lg_ordencab.fechaLog, '' ), COALESCE ( lg_ordencab.fechaOpe, '' ), COALESCE ( lg_ordencab.FechaFin, '' ) ),
                                                            INTERVAL lg_ordencab.nplazo DAY 
                                                        ),
                                                        '%d/%m/%Y' 
                                                    ) AS fecha_entrega_final,
                                                    FORMAT( lg_ordencab.nplazo, 0 ) AS plazo
                                                FROM
                                                    tb_pedidodet
                                                    LEFT JOIN tb_pedidocab ON tb_pedidodet.idpedido = tb_pedidocab.idreg
                                                    LEFT JOIN lg_ordendet ON lg_ordendet.niddeta = tb_pedidodet.iditem
                                                    LEFT JOIN cm_producto ON tb_pedidodet.idprod = cm_producto.id_cprod
                                                    LEFT JOIN tb_proyectos ON tb_pedidodet.idcostos = tb_proyectos.nidreg
                                                    LEFT JOIN tb_area ON tb_pedidodet.idarea = tb_area.ncodarea
                                                    LEFT JOIN tb_partidas ON tb_pedidocab.idpartida = tb_partidas.idreg
                                                    LEFT JOIN tb_unimed ON tb_pedidodet.unid = tb_unimed.ncodmed
                                                    LEFT JOIN lg_ordencab ON lg_ordendet.id_orden = lg_ordencab.id_regmov
                                                    LEFT JOIN cm_entidad ON lg_ordencab.id_centi = cm_entidad.id_centi
                                                    LEFT JOIN tb_user ON lg_ordencab.id_cuser = tb_user.iduser
                                                    LEFT JOIN tb_parametros AS transporte ON lg_ordencab.ctiptransp = transporte.nidreg
                                                    LEFT JOIN tb_user AS user_aprueba ON tb_pedidocab.aprueba = user_aprueba.iduser
                                                    LEFT JOIN alm_despachodet ON tb_pedidodet.iditem = alm_despachodet.niddetaPed
                                                    LEFT JOIN alm_despachocab ON alm_despachodet.id_regalm = alm_despachocab.id_regalm
                                                    LEFT JOIN alm_recepdet ON tb_pedidodet.iditem = alm_recepdet.niddetaPed
                                                    LEFT JOIN alm_recepcab ON alm_recepdet.id_regalm = alm_recepcab.id_regalm
                                                    LEFT JOIN alm_existencia ON tb_pedidodet.iditem = alm_existencia.idpedido
                                                    LEFT JOIN alm_cabexist ON alm_existencia.idregistro = alm_cabexist.idreg
                                                    LEFT JOIN tb_equipmtto ON tb_pedidodet.nregistro = tb_equipmtto.idreg
                                                    LEFT JOIN ( SELECT SUM( lg_ordendet.ncanti ) AS cantidad_orden, lg_ordendet.niddeta FROM lg_ordendet WHERE lg_ordendet.id_orden != 0 GROUP BY lg_ordendet.niddeta ) AS o ON o.niddeta = tb_pedidodet.iditem
                                                    LEFT JOIN ( SELECT SUM( alm_recepdet.ncantidad ) AS ingreso, alm_recepdet.niddetaPed FROM alm_recepdet WHERE alm_recepdet.nflgactivo = 1 GROUP BY alm_recepdet.niddetaPed ) AS i ON i.niddetaPed = tb_pedidodet.iditem
                                                    LEFT JOIN ( SELECT SUM( alm_despachodet.ndespacho ) AS despachos, alm_despachodet.niddetaPed FROM alm_despachodet WHERE alm_despachodet.nflgactivo = 1 GROUP BY alm_despachodet.niddetaPed ) AS d ON d.niddetaPed = tb_pedidodet.iditem
                                                    LEFT JOIN ( SELECT SUM( alm_existencia.cant_ingr ) AS ingreso_obra, alm_existencia.idpedido FROM alm_existencia WHERE alm_existencia.nflgActivo = 1 GROUP BY alm_existencia.idpedido ) AS a ON a.idpedido = tb_pedidodet.iditem
                                                    LEFT JOIN tb_user AS usuarios ON tb_pedidocab.usuario = usuarios.iduser
                                                    LEFT JOIN alm_transferdet ON alm_transferdet.iddetped = tb_pedidodet.iditem
                                                    LEFT JOIN alm_transfercab ON alm_transfercab.idreg = alm_transferdet.idtransfer 
                                                WHERE
                                                    tb_pedidodet.nflgActivo 
                                                    AND ISNULL( lg_ordendet.nflgactivo )
                                                    AND tb_proyectos.nflgactivo = 1
                                                GROUP BY
                                                    tb_pedidodet.iditem
                                                ORDER BY 
                                                    tb_pedidocab.anio DESC
                                                LIMIT 500");
                $sql->execute();
                $rowCount = $sql->rowCount();

                if ($rowCount) {
                    $respuesta = true;
                    
                    while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                        $docData[] = $row;
                    }

                    if ($estado == 1){
                        $this->crearExcel($docData);
                        $archivo = 'public/documentos/reportes/cargoplan.xlsx';
                    }
                    else if ($estado == 2){
                        $this->crearCSV($docData);
                        $archivo = 'public/documentos/temp/cargoplan.csv';
                    }
                    else if ($estado == 3){
                        $this->crearSpout($docData);
                        $archivo = 'public/documentos/temp/cargoplan.xlsx';
                    }
                    else if ($estado == 4){
                        $this->crearSpreadSheet($docData);
                        $archivo = 'public/documentos/temp/cargoplanSpreedSheet.xlsx';
                    }      
                }

                return array("documento"=>$archivo);

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function contarItemsCargoPlan(){
            try{
                $_SESSION['progreso'] = 0;

                $sql = $this->db->connect()->query("SELECT COUNT(*) AS items FROM tb_pedidodet WHERE tb_pedidodet.nflgActivo = 1");
                $sql->execute();

                $result = $sql->fetchAll();

                return $result[0]['items'];

            }catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function calcularDiasCargoPlan($fechaInicial){
            $fechaActual = date('Y-m-d'); // la fecha del ordenador

            $diff = abs(strtotime($fechaActual) - strtotime($fechaInicial));
            $years = floor($diff / (365*60*60*24));

            return floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
        }

        public function gererarNotaIngreso($id) {
            require_once("public/formatos/notaingreso.php");

            $cabecera = $this->cabeceraNotaIngreso($id);
            $detalles = $this->detallesIngreso($id);
            $nreg = count($detalles);
            $fecha = explode("-",$cabecera[0]['ffecdoc']);

            $dia = $fecha[2];
            $mes = $fecha[1];
            $anio = $fecha[0];

            $pdf = new PDF($cabecera[0]['nnronota'],0,$dia,$mes,$anio,
                            $cabecera[0]['proyecto'],$cabecera[0]['almacen'],$cabecera[0]['cdescripcion'],$cabecera[0]['orden'],
                            $cabecera[0]['pedido'],$cabecera[0]['cnumguia'],$cabecera[0]['cnombres'],NULL,'I',$cabecera[0]['cdescripcion'],NULL);

            $pdf->AliasNbPages();
            $pdf->AddPage();
            $pdf->SetWidths(array(7,20,55,8,12,15,45,13,15));
            $pdf->SetFont('Arial','',6);
            $lc = 0;
            $rc = 0;

            
            $item = 1;

            foreach ($detalles as $detalle) {
                $pdf->SetAligns(array("C","L","L","L","R","L","L","L","L"));

                $pdf->Row(array(str_pad($item++,3,"0",STR_PAD_LEFT),
                                        $detalle['codigo'],
                                        utf8_decode($detalle['nombre']),
                                        $detalle['unidad'],
                                        $detalle['cantidad'],
                                        '',
                                        $cabecera[0]['crazonsoc'],
                                        '',
                                        ''));
                $lc++;
                $rc++;
                
                if ($pdf->getY() >= 190) {
                    $pdf->AddPage();
                    $lc = 0;
                }
            }
            

            $file = uniqid("NI").".pdf";
            $filename = "public/documentos/temp/".$file;

            $pdf->Output($filename,'F');
            
            return $filename;
        }

        private function cabeceraNotaIngreso($id) { 
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                        ibis.alm_recepcab.id_regalm,
                                                        ibis.alm_recepcab.ctipmov,
                                                        ibis.alm_recepcab.ncodmov,
                                                        ibis.alm_recepcab.nnronota,
                                                        ibis.alm_recepcab.cper,
                                                        ibis.alm_recepcab.cmes,
                                                        ibis.alm_recepcab.ncodalm1,
                                                        ibis.alm_recepcab.ffecdoc,
                                                        ibis.alm_recepcab.cnumguia,
                                                        ibis.alm_recepcab.ncodpry,
                                                        ibis.alm_recepcab.ncodarea,
                                                        ibis.alm_recepcab.ncodcos,
                                                        ibis.alm_recepcab.idref_pedi,
                                                        ibis.alm_recepcab.idref_abas,
                                                        ibis.alm_recepcab.id_userAprob,
                                                        ibis.alm_recepcab.nEstadoDoc,
                                                        ibis.tb_proyectos.ccodproy,
                                                        UPPER( ibis.tb_proyectos.cdesproy ) AS proyecto,
                                                        ibis.tb_area.ccodarea,
                                                        UPPER ( ibis.tb_area.cdesarea ) AS area,
                                                        ibis.tb_user.cnombres,
                                                        ibis.tb_pedidocab.idsolicita,
                                                        ibis.tb_almacen.ccodalm,
                                                        UPPER( ibis.tb_almacen.cdesalm ) AS almacen,
                                                        ibis.alm_recepcab.nnromov,
                                                        ibis.tb_parametros.cdescripcion,
                                                        ibis.cm_entidad.crazonsoc,
                                                        LPAD( ibis.tb_pedidocab.nrodoc, 6, 0 ) AS pedido,
                                                        LPAD( ibis.lg_ordencab.cnumero, 6, 0 ) AS orden,
                                                        UPPER( ibis.tb_pedidocab.concepto ) AS concepto,
                                                        UPPER( ibis.tb_pedidocab.detalle ) AS detalle,
                                                        estados.cabrevia AS estado,
                                                        ibis.alm_recepcab.nflgCalidad 
                                                    FROM
                                                        ibis.alm_recepcab
                                                        INNER JOIN ibis.tb_proyectos ON alm_recepcab.ncodpry = tb_proyectos.nidreg
                                                        INNER JOIN ibis.tb_area ON alm_recepcab.ncodarea = tb_area.ncodarea
                                                        INNER JOIN ibis.tb_user ON alm_recepcab.id_userAprob = tb_user.iduser
                                                        INNER JOIN ibis.tb_pedidocab ON ibis.alm_recepcab.idref_pedi = ibis.tb_pedidocab.idreg
                                                        INNER JOIN ibis.tb_almacen ON ibis.alm_recepcab.ncodalm1 = ibis.tb_almacen.ncodalm
                                                        INNER JOIN ibis.tb_parametros ON ibis.alm_recepcab.ncodmov = ibis.tb_parametros.nidreg
                                                        INNER JOIN ibis.cm_entidad ON ibis.alm_recepcab.id_centi = ibis.cm_entidad.id_centi
                                                        INNER JOIN ibis.lg_ordencab ON ibis.alm_recepcab.idref_abas = ibis.lg_ordencab.id_regmov
                                                        INNER JOIN ibis.tb_parametros AS estados ON ibis.alm_recepcab.nEstadoDoc = estados.nidreg 
                                                    WHERE alm_recepcab.id_regalm = :id
                                                    LIMIT 1");
                $sql->execute(["id"=>$id]);

                $rowCount = $sql->rowCount();
                
                if ($rowCount > 0) {
                    $docData = array();
                    while($row=$sql->fetch(PDO::FETCH_ASSOC)){
                        $docData[] = $row;
                    }
                }

                return $docData;
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function detallesIngreso($id){
            $detalles=[];

            $sql=$this->db->connect()->prepare("SELECT
                                                    alm_recepdet.niddeta,
                                                    alm_recepdet.id_regalm,
                                                    alm_recepdet.ncodalm1,
                                                    alm_recepdet.id_cprod,
                                                    alm_recepdet.ncantidad AS ncantidad,
                                                    alm_recepdet.niddetaPed,
                                                    alm_recepdet.niddetaOrd,
                                                    alm_recepdet.nestadoreg,
                                                    cm_producto.ccodprod,
                                                    UPPER(
                                                        CONCAT_WS(
                                                            ' ',
                                                            cm_producto.cdesprod,
                                                            tb_pedidodet.observaciones,
                                                            tb_pedidodet.docEspec
                                                        )
                                                    ) AS cdesprod,
                                                    lg_ordendet.ncanti AS cantidad_orden,
                                                    tb_unimed.cabrevia
                                                FROM
                                                    alm_recepdet
                                                INNER JOIN tb_pedidodet ON alm_recepdet.niddetaPed = tb_pedidodet.iditem
                                                INNER JOIN cm_producto ON alm_recepdet.id_cprod = cm_producto.id_cprod
                                                INNER JOIN lg_ordendet ON alm_recepdet.niddetaOrd = lg_ordendet.nitemord
                                                INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed
                                                WHERE
                                                    alm_recepdet.id_regalm = :id
                                                AND alm_recepdet.nflgactivo = 1");
                $sql->execute(['id'=>$id]);
                $rowCount = $sql->rowCount();
                
                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()) {
                        $item['codigo'] = $rs['ccodprod'];
                        $item['nombre'] = $rs['cdesprod'];
                        $item['cantidad'] = $rs['ncantidad'];
                        $item['unidad'] = $rs['cabrevia'];

                        array_push($detalles,$item);
                    }
                }

                return $detalles;
        }

        public function cabeceraSalida($id) {
            try {
                //code...
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function detalleSalida($id) { 
        }

        public function generarGuiaRemision($id) {
            try {
                require_once("public/formatos/guiaremision.php");

                $cabecera = $this->cabeceraGuiaRemision($id);
                $detalles = $this->detallesGuiaRemision($id);
                $nreg = count($detalles);

                $pdf = new PDF($cabecera[0]['cnumguia'],
                                    $cabecera[0]['ffecdoc'],
                                    "20504898173",
                                    "SERVICIOS PETROLEROS Y CONSTRUCCIONES SEPCON S.A.C",
                                    "AV. SAN BORJA NORTE N° 445 - SAN BORJA-LIMA-PERU.",
                                    $cabecera[0]['centi'],
                                    $cabecera[0]['centiruc'],
                                    $cabecera[0]['centidir'],
                                    $cabecera[0]['cdirorigen'],
                                    null,
                                    null,
                                    null,
                                    $cabecera[0]['ffecenvio'],
                                    $cabecera[0]['cenvio'],
                                    $cabecera[0]['cdestino'],
                                    null,
                                    null,
                                    null,
                                    $cabecera[0]['cmarca'],
                                    $cabecera[0]['cplaca'],
                                    $cabecera[0]['cnombre'],
                                    $cabecera[0]['clicencia'],
                                    $cabecera[0]['cenvio'],
                                    $cabecera[0]['nReferido'],
                                    $cabecera[0]['cdesproy'],
                                    $cabecera[0]['cper'],
                                    $cabecera[0]["cobserva"],
                                    $cabecera[0]["cdestinatario"],
                                    1,
                                    'A4');

                $pdf->AliasNbPages();
                $pdf->AddPage();
                $pdf->SetWidths(array(10,15,15,147));
                $pdf->SetFillColor(255,255,255);
                $pdf->SetTextColor(0,0,0);

                $pdf->SetFont('Arial','',7);
                $lc = 0;
                $rc = 0;

                //aca podria sumar la orden

                for($i=1;$i<=$nreg;$i++){

                    $pdf->SetX(13);
                    $pdf->SetCellHeight(3);
                    //$pdf->SetFont('Arial','',3);

                    $pdf->SetAligns(array("R","R","C","L"));
                    $pdf->Row(array(str_pad($i,3,"0",STR_PAD_LEFT),
                                    $detalles[$rc]['ncantidad'],
                                    $detalles[$rc]['cabrevia'],
                                    utf8_decode($detalles[$rc]['ccodprod'] .' '. $detalles[$rc]['cdesprod']  .' '.' P : '.$detalles[$rc]['pedido'].' O : '.$detalles[$rc]['orden'])));
                    $lc++;
                    $rc++;

                    if ($lc == 26) {
                        $pdf->AddPage();
                        $lc = 0;
                    }
                }

                $file = uniqid("GR").".pdf";
                $filename = "public/documentos/temp/".$file;

                $pdf->Output($filename,'F');
                
                return $filename;
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function cabeceraGuiaRemision($id) {
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                        lg_guias.idreg,
                                                        lg_guias.id_regalm,
                                                        lg_guias.cnumguia,
                                                        lg_guias.corigen,
                                                        lg_guias.cdirorigen,
                                                        lg_guias.cdestino,
                                                        lg_guias.cdirdest,
                                                        lg_guias.centi,
                                                        lg_guias.centidir,
                                                        lg_guias.centiruc,
                                                        lg_guias.ctraslado,
                                                        lg_guias.cenvio,
                                                        lg_guias.cautoriza,
                                                        lg_guias.cmarca,
                                                        lg_guias.cplaca,
                                                        lg_guias.cnumadre,
                                                        lg_guias.cnombre,
                                                        lg_guias.flgmadre,
                                                        lg_guias.clicencia,
                                                        lg_guias.ftraslado,
                                                        lg_guias.fguia,
                                                        lg_guias.cobserva,
                                                        lg_guias.cdestinatario,
                                                        alm_despachocab.ffecenvio,
                                                        alm_despachocab.ffecdoc,
                                                        alm_despachocab.nReferido,
                                                        alm_despachocab.cper,
                                                        tb_proyectos.ccodproy,
                                                        tb_proyectos.cdesproy 
                                                    FROM
                                                        lg_guias
                                                        LEFT JOIN alm_despachocab ON lg_guias.id_regalm = alm_despachocab.id_regalm
                                                        LEFT JOIN tb_proyectos ON alm_despachocab.ncodpry = tb_proyectos.nidreg 
                                                    WHERE
                                                        lg_guias.id_regalm = :id 
                                                        AND lg_guias.nflgActivo");
                
                $sql->execute(["id"=>$id]);

                if ($sql->rowCount() > 0) {
                    $docData = array();
                    while($row=$sql->fetch(PDO::FETCH_ASSOC)){
                        $docData[] = $row;
                    }
                }

                return $docData;

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function detallesGuiaRemision($id) {
            try {
                $sql=$this->db->connect()->prepare("SELECT
                                            alm_despachodet.ncantidad AS cantidad,
                                            alm_despachodet.niddetaPed,
                                            LPAD( alm_despachodet.nropedido, 6, 0 ) AS orden,
                                            LPAD( alm_despachodet.nroorden, 6, 0 ) AS pedido,
                                            cm_producto.ccodprod,
                                            REPLACE ( FORMAT( alm_despachodet.ndespacho, 2 ), ',', '' ) AS ncantidad,
                                            UPPER( CONCAT_WS( ' ', cm_producto.cdesprod, tb_pedidodet.observaciones ) ) AS cdesprod,
                                            tb_unimed.cabrevia
                                        FROM
                                            alm_despachodet
                                            LEFT JOIN cm_producto ON alm_despachodet.id_cprod = cm_producto.id_cprod
                                            LEFT JOIN tb_pedidodet ON alm_despachodet.niddetaPed = tb_pedidodet.iditem
                                            LEFT JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed
                                            LEFT JOIN tb_pedidocab ON alm_despachodet.nropedido = tb_pedidocab.idreg
                                            LEFT JOIN lg_ordencab ON lg_ordencab.id_regmov = alm_despachodet.nropedido 
                                        WHERE
                                            alm_despachodet.id_regalm = :id 
                                            AND alm_despachodet.nflgactivo = 1");
                $sql->execute(["id"=>$id]);

                $rowCount = $sql->rowCount();
                
                if ($rowCount > 0) {
                    $docData = array();
                    while($row=$sql->fetch(PDO::FETCH_ASSOC)){
                        $docData[] = $row;
                    }
                }

                return $docData;
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function listarArchivos($id){
            try {
                $salida = "";

                $sql = $this->db->connect()->prepare("SELECT lg_regdocumento.nidrefer, lg_regdocumento.creferencia, lg_regdocumento.cdocumento 
                                                      FROM lg_regdocumento
                                                      WHERE lg_regdocumento.nflgactivo = 1 
                                                      AND lg_regdocumento.nidrefer =:id");
                $sql->execute(["id"=>$id]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0){
                    while ($rs = $sql->fetch()) {
                        $salida.='<li><a href="'.$rs['creferencia'].'">'.$rs['cdocumento'].'</a></li>';
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function listarProyectosFiltro(){
            try {
                $salida = "";

                $sql = $this->db->connect()->prepare("SELECT
                                                    tb_costusu.ncodproy,
                                                    UPPER(
                                                    CONCAT_WS( ' ', tb_proyectos.ccodproy, tb_proyectos.cdesproy )) AS nombre 
                                                FROM
                                                    tb_costusu
                                                    INNER JOIN tb_proyectos ON tb_costusu.ncodproy = tb_proyectos.nidreg 
                                                WHERE
                                                    tb_costusu.id_cuser = :user 
                                                    AND tb_costusu.nflgactivo = 1 
                                                    AND tb_proyectos.nflgactivo = 1 
                                                ORDER BY
                                                    tb_proyectos.ccodproy");
                $sql->execute(["user"=>$_SESSION["iduser"]]);
                $rowCount = $sql->rowCount();

                if ( $rowCount > 0 ) {
                    while ($rs = $sql->fetch()) {
                        $salida .= '<li>
                                        <input type="checkbox" name="'.$rs['ncodproy'].'" id="'.$rs['ncodproy'].'">
                                        <label for="'.$rs['ncodproy'].'">'.$rs['nombre'].'</label>
                                    </li>';
                    }
                }

                return $salida;
                            
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function exportarCsv($datos){
            try {
                $arreglo = [];
                $titulo = array('Items','Estado Actual','Codigo Proyecto','Area','Partida','Atención','Tipo','Año Pedido','N° Pedido', 'Creación Pedido',
                                    'Aprobación del Pedido','Cantidad Pedida', 'Cantidad Aprobada', 'Cantidad Compra','Codigo del Bien/Servicio','Unidad Medida',
                                    'Descripcion del Bien/Servicio','Tipo Orden','Año Orden', 'Nro Orden', 'Fecha Orden', 'Cantidad Orden', 'Item Orden', 'Fecha Autorizacion',
                                    'Atencion Almacen', 'Descripcion del proveedor','Fecha Entrega Proveedor','Cant. Recibida','Nota de Ingreso', 'Fecha Recepcion Proveedor',
                                    'Saldo por Recibir','Dias Entrega','Días Atrazo','Semáforo', 'Cantidad Despachada','Nro. Guia','Fecha Traslado','Nro. Guia Transferencia',
                                    'Registro Almacen','Fecha Ingreso Almacen', 'Cantidad en Obra', 'Estado Pedido', 'Estado Item', 'N° Parte', 'Codigo Activo', 'Operador Logístico', 
                                    'Tipo Transporte','Observaciones/Concepto','Solicitante');


                array_push($arreglo,$titulo);

                $ruta ="public/documentos/temp/cargoplan.csv";

                $this->generarCSV($arreglo, $ruta, $delimitador = ';', $encapsulador = '"');

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function crearCSV($arreglo, $ruta, $delimitador, $encapsulador){
            $file_handle = fopen($ruta, 'w');
            
            foreach ($arreglo as $linea) {
              fputcsv($file_handle, $linea, $delimitador, $encapsulador);
            }

            rewind($file_handle);
            fclose($file_handle);
        }

        public function filtrarExportarTotal($parametros){
            try {
                $costos = json_decode($parametros['costos']);
                $cc = array();
                $docdata = [];

                foreach ($costos as $costo){
                    array_push($cc,$costo);
                }

                $string_from_array = implode(',', $cc);

                $sql = $this->db->connect()->prepare("SELECT
                                                        tb_pedidodet.iditem,
                                                        tb_pedidodet.idpedido,
                                                        tb_pedidodet.idprod,
                                                        tb_pedidodet.nroparte,
                                                        tb_pedidodet.nregistro,
                                                        tb_pedidodet.cant_pedida AS cantidad_pedido,
                                                        tb_pedidodet.cant_aprob AS cantidad_aprobada,
                                                        tb_pedidodet.cant_atend AS cantidad_atendida,
                                                        LPAD( tb_pedidocab.nrodoc, 6, 0 ) AS pedido,
                                                        lg_ordendet.id_orden AS orden,
                                                        lg_ordendet.item AS item_orden,
                                                        cm_producto.ccodprod,
                                                        UPPER( CONCAT_WS( ' ', cm_producto.cdesprod, tb_pedidodet.observaciones ) ) AS descripcion,
                                                        tb_pedidodet.estadoItem,
                                                        tb_proyectos.ccodproy,
                                                        tb_proyectos.nidreg AS idproyecto,
                                                        UPPER( tb_area.cdesarea ) AS area,
                                                        UPPER( tb_partidas.cdescripcion ) AS partida,
                                                        DATE_FORMAT( tb_pedidocab.emision, '%d/%m/%Y' ) AS crea_pedido,
                                                        DATE_FORMAT( tb_pedidocab.faprueba, '%d/%m/%Y' ) AS aprobacion_pedido,
                                                        DATE_FORMAT( lg_ordencab.ffechades, '%d/%m/%Y' ) AS fecha_descarga,
                                                        tb_pedidocab.anio AS anio_pedido,
                                                        tb_pedidocab.mes AS pedido_mes,
                                                        tb_pedidocab.nivelAten AS atencion,
                                                        tb_pedidocab.idtipomov,
                                                        tb_unimed.cabrevia AS unidad,
                                                        lg_ordencab.cper AS anio_orden,
                                                        lg_ordencab.ntipmov,
                                                        lg_ordencab.FechaFin,
                                                        lg_ordencab.cnumero,
                                                        lg_ordencab.ffechades,
                                                        lg_ordencab.fechaLog,
                                                        lg_ordencab.fechaOpe,
                                                        lg_ordencab.ffechaent,
                                                        lg_ordencab.nEstadoDoc,
                                                        lg_ordencab.nNivAten,
                                                        DATE_FORMAT( lg_ordencab.ffechadoc, '%d/%m/%Y' ) AS fecha_orden,
                                                        UPPER( cm_entidad.crazonsoc ) AS proveedor,
                                                        UPPER( tb_user.cnameuser ) AS operador,
                                                        UPPER( tb_pedidocab.concepto ) AS concepto,
                                                        DATEDIFF( lg_ordencab.ffechaent, NOW() ) AS dias_atraso,
                                                        transporte.cdescripcion AS transporte,
                                                        transporte.nidreg,
                                                        user_aprueba.cnombres,
                                                        alm_despachocab.cnumguia,
                                                        LPAD( alm_recepcab.nnronota, 6, 0 ) AS nota_ingreso,
                                                        LPAD( alm_cabexist.idreg, 6, 0 ) AS nota_obra,
                                                        tb_equipmtto.cregistro,
                                                        o.cantidad_orden,
                                                        i.ingreso,
                                                        d.despachos,
                                                        a.ingreso_obra,
                                                        usuarios.cnombres AS nombre_elabora,
                                                        DATE_FORMAT( alm_recepcab.ffecdoc, '%d/%m/%Y' ) AS fecha_recepcion_proveedor,
                                                        DATE_FORMAT( alm_cabexist.ffechadoc, '%d/%m/%Y' ) AS fecha_registro_almacen,
                                                        alm_transfercab.cnumguia AS guia_transferencia,
                                                        LPAD(alm_transfercab.idreg,6,0) AS nota_transferencia,
                                                        DATE_FORMAT( alm_transfercab.ftraslado, '%d/%m/%Y' ) AS fecha_traslado,
                                                        DATE_FORMAT(
                                                            GREATEST( COALESCE ( lg_ordencab.fechaLog, '' ), COALESCE ( lg_ordencab.fechaOpe, '' ), COALESCE ( lg_ordencab.FechaFin, '' ) ),
                                                            '%d/%m/%Y' 
                                                        ) AS fecha_autorizacion,
                                                        DATE_FORMAT(
                                                            DATE_ADD(
                                                                GREATEST( COALESCE ( lg_ordencab.fechaLog, '' ), COALESCE ( lg_ordencab.fechaOpe, '' ), COALESCE ( lg_ordencab.FechaFin, '' ) ),
                                                                INTERVAL lg_ordencab.nplazo DAY 
                                                            ),
                                                            '%d/%m/%Y' 
                                                        ) AS fecha_entrega_final,
                                                        FORMAT( lg_ordencab.nplazo, 0 ) AS plazo
                                                    FROM
                                                        tb_pedidodet
                                                        LEFT JOIN tb_pedidocab ON tb_pedidodet.idpedido = tb_pedidocab.idreg
                                                        LEFT JOIN lg_ordendet ON lg_ordendet.niddeta = tb_pedidodet.iditem
                                                        LEFT JOIN cm_producto ON tb_pedidodet.idprod = cm_producto.id_cprod
                                                        LEFT JOIN tb_proyectos ON tb_pedidodet.idcostos = tb_proyectos.nidreg
                                                        LEFT JOIN tb_area ON tb_pedidodet.idarea = tb_area.ncodarea
                                                        LEFT JOIN tb_partidas ON tb_pedidocab.idpartida = tb_partidas.idreg
                                                        LEFT JOIN tb_unimed ON tb_pedidodet.unid = tb_unimed.ncodmed
                                                        LEFT JOIN lg_ordencab ON lg_ordendet.id_orden = lg_ordencab.id_regmov
                                                        LEFT JOIN cm_entidad ON lg_ordencab.id_centi = cm_entidad.id_centi
                                                        LEFT JOIN tb_user ON lg_ordencab.id_cuser = tb_user.iduser
                                                        LEFT JOIN tb_parametros AS transporte ON lg_ordencab.ctiptransp = transporte.nidreg
                                                        LEFT JOIN tb_user AS user_aprueba ON tb_pedidocab.aprueba = user_aprueba.iduser
                                                        LEFT JOIN alm_despachodet ON tb_pedidodet.iditem = alm_despachodet.niddetaPed
                                                        LEFT JOIN alm_despachocab ON alm_despachodet.id_regalm = alm_despachocab.id_regalm
                                                        LEFT JOIN alm_recepdet ON tb_pedidodet.iditem = alm_recepdet.niddetaPed
                                                        LEFT JOIN alm_recepcab ON alm_recepdet.id_regalm = alm_recepcab.id_regalm
                                                        LEFT JOIN alm_existencia ON tb_pedidodet.iditem = alm_existencia.idpedido
                                                        LEFT JOIN alm_cabexist ON alm_existencia.idregistro = alm_cabexist.idreg
                                                        LEFT JOIN tb_equipmtto ON tb_pedidodet.nregistro = tb_equipmtto.idreg
                                                        LEFT JOIN ( SELECT SUM( lg_ordendet.ncanti ) AS cantidad_orden, lg_ordendet.niddeta FROM lg_ordendet WHERE lg_ordendet.id_orden != 0 GROUP BY lg_ordendet.niddeta ) AS o ON o.niddeta = tb_pedidodet.iditem
                                                        LEFT JOIN ( SELECT SUM( alm_recepdet.ncantidad ) AS ingreso, alm_recepdet.niddetaPed FROM alm_recepdet WHERE alm_recepdet.nflgactivo = 1 GROUP BY alm_recepdet.niddetaPed ) AS i ON i.niddetaPed = tb_pedidodet.iditem
                                                        LEFT JOIN ( SELECT SUM( alm_despachodet.ndespacho ) AS despachos, alm_despachodet.niddetaPed FROM alm_despachodet WHERE alm_despachodet.nflgactivo = 1 GROUP BY alm_despachodet.niddetaPed ) AS d ON d.niddetaPed = tb_pedidodet.iditem
                                                        LEFT JOIN ( SELECT SUM( alm_existencia.cant_ingr ) AS ingreso_obra, alm_existencia.idpedido FROM alm_existencia WHERE alm_existencia.nflgActivo = 1 GROUP BY alm_existencia.idpedido ) AS a ON a.idpedido = tb_pedidodet.iditem
                                                        LEFT JOIN tb_user AS usuarios ON tb_pedidocab.usuario = usuarios.iduser
                                                        LEFT JOIN alm_transferdet ON alm_transferdet.iddetped = tb_pedidodet.iditem
                                                        LEFT JOIN alm_transfercab ON alm_transfercab.idreg = alm_transferdet.idtransfer 
                                                    WHERE
                                                        tb_pedidodet.nflgActivo 
                                                        AND ISNULL( lg_ordendet.nflgactivo )
                                                        AND tb_pedidocab.emision BETWEEN :fecha_inicio AND :fecha_final
                                                        AND tb_proyectos.nidreg IN ($string_from_array)
                                                    GROUP BY
                                                        tb_pedidodet.iditem
                                                    ORDER BY 
                                                        tb_proyectos.nidreg");

                $sql->execute(["fecha_inicio"=>$parametros['fechaInicio'],"fecha_final"=>$parametros['fechaFinal']]);
                
                $rowCount = $sql->rowCount();
                
                if ($rowCount) {
                    $respuesta = true;
                    
                    while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                        $docData[] = $row;
                    }

                    $this->crearExcel($docData);
                }
                
                return array("documento"=>'public/documentos/reportes/cargoplan.xlsx',"rango"=>$string_from_array);

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function crearSpreadSheet($datos){
            try {
                require_once('public/phpSpreadSheet/vendor/autoload.php');

                ob_end_clean();

                $spread = new Spreadsheet();
                $spread
                    ->getProperties()
                    ->setCreator("Sical")
                    ->setLastModifiedBy('Sical Sepcon')
                    ->setTitle('Cargo Plan')
                    ->setSubject('Cargo Plan')
                    ->setDescription('Cargo Plan SpreadSheet Documentation')
                    ->setKeywords('PHPSpreadsheet')
                    ->setCategory('Categoría Excel');

                $sheet = $spread->getActiveSheet();

                $sheet->getColumnDimension("D")->setAutoSize(true);
                $sheet->getColumnDimension("E")->setWidth(50);
                $sheet->getColumnDimension("J")->setAutoSize(true);
                $sheet->getColumnDimension("K")->setWidth(10);
                $sheet->getColumnDimension("L")->setWidth(15);
                $sheet->getColumnDimension("M")->setWidth(15);
                $sheet->getColumnDimension("N")->setWidth(15);
                $sheet->getColumnDimension("O")->setWidth(24);
                $sheet->getColumnDimension("Q")->setWidth(100);
                $sheet->getColumnDimension("S")->setAutoSize(true);
                $sheet->getColumnDimension("T")->setWidth(10);
                $sheet->getColumnDimension("U")->setAutoSize(true);
                $sheet->getColumnDimension("V")->setWidth(15);
                $sheet->getColumnDimension("W")->setAutoSize(true);
                $sheet->getColumnDimension("X")->setAutoSize(true);
                $sheet->getColumnDimension("Y")->setWidth(9);
                $sheet->getColumnDimension("Z")->setWidth(100);
                $sheet->getColumnDimension("Y")->setAutoSize(true);
                $sheet->getColumnDimension("AA")->setWidth(10);
                $sheet->getColumnDimension("AB")->setWidth(10);
                $sheet->getColumnDimension("AC")->setWidth(13);
                $sheet->getColumnDimension("AD")->setWidth(15);
                $sheet->getColumnDimension("AE")->setWidth(12);
                $sheet->getColumnDimension("AF")->setAutoSize(true);
                $sheet->getColumnDimension("AG")->setAutoSize(true);
                $sheet->getColumnDimension("AH")->setWidth(12);
                $sheet->getColumnDimension("AI")->setWidth(12);
                $sheet->getColumnDimension("AJ")->setAutoSize(true);
                $sheet->getColumnDimension("AK")->setWidth(14);
                $sheet->getColumnDimension("AL")->setWidth(12);
                $sheet->getColumnDimension("AM")->setAutoSize(true);
                $sheet->getColumnDimension("AN")->setAutoSize(true);
                $sheet->getColumnDimension("AO")->setAutoSize(true);
                $sheet->getColumnDimension("AP")->setAutoSize(true);
                $sheet->getColumnDimension("AQ")->setAutoSize(true);
                $sheet->getColumnDimension("AR")->setAutoSize(true);
                $sheet->getColumnDimension("AS")->setAutoSize(true);
                $sheet->getColumnDimension("AT")->setAutoSize(true);
                $sheet->getColumnDimension("AU")->setAutoSize(true);
                $sheet->getColumnDimension("AV")->setWidth(50);
                $sheet->getColumnDimension("AW")->setAutoSize(true);

                $sheet->mergeCells('A1:AW1');

                $sheet->getStyle('A1:AW2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A1:AW2')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);

                $sheet->getRowDimension('2')->setRowHeight(60);

                $sheet->getStyle('A1:AW2')->getAlignment()->setWrapText(true);

                $sheet
                    ->setTitle("Cargo Plan")
                    ->setCellValue('A1','CARGO PLAN')
                    ->setCellValue('A2','Items')
                    ->setCellValue('B2','Estado Actual')
                    ->setCellValue('C2','Codigo Proyecto')
                    ->setCellValue('D2','Area')
                    ->setCellValue('E2','Partida')
                    ->setCellValue('F2','Atención')
                    ->setCellValue('G2','Tipo')
                    ->setCellValue('H2','Año Pedido')
                    ->setCellValue('I2','N° Pedido')
                    ->setCellValue('J2','Creación Pedido')
                    ->setCellValue('K2','Aprobación del Pedido')
                    ->setCellValue('L2','Cantidad Pedida')
                    ->setCellValue('M2','Cantidad Aprobada')
                    ->setCellValue('N2','Cantidad Compra')
                    ->setCellValue('O2','Codigo del Bien/Servicio')
                    ->setCellValue('P2','Unidad Medida')
                    ->setCellValue('Q2','Descripcion del Bien/Servicio')
                    ->setCellValue('R2','Tipo Orden')
                    ->setCellValue('S2','Año Orden')
                    ->setCellValue('T2','Nro Orden')
                    ->setCellValue('U2','Fecha Orden')
                    ->setCellValue('V2','Cantidad Orden')
                    ->setCellValue('W2','Item Orden')
                    ->setCellValue('X2','Fecha Autorizacion')
                    ->setCellValue('Y2','Atencion Almacen')
                    ->setCellValue('Z2','Descripcion del proveedor')
                    ->setCellValue('AA2','Fecha Entrega Proveedor')
                    ->setCellValue('AB2','Cant. Recibida')
                    ->setCellValue('AC2','Nota de Ingreso')
                    ->setCellValue('AD2','Fecha Recepcion Proveedor')
                    ->setCellValue('AE2','Saldo por Recibir')
                    ->setCellValue('AF2','Dias Entrega')
                    ->setCellValue('AG2','Días Atrazo')
                    ->setCellValue('AH2','Semáforo')
                    ->setCellValue('AI2','Cantidad Despachada')
                    ->setCellValue('AJ2','Nro. Guia')
                    ->setCellValue('AK2','Nro. Guia Transferencia')
                    ->setCellValue('AL2','Fecha Traslado')
                    ->setCellValue('AM2','Registro Almacen')
                    ->setCellValue('AN2','Fecha Ingreso Almacen')
                    ->setCellValue('AO2','Cantidad en Obra')
                    ->setCellValue('AP2','Estado Pedido')
                    ->setCellValue('AQ2','Estado Item')
                    ->setCellValue('AR2','N° Parte')
                    ->setCellValue('AS2','Codigo Activo')
                    ->setCellValue('AT2','Operador Logístico')
                    ->setCellValue('AU2','Tipo Transporte')
                    ->setCellValue('AV2','Observaciones/Concepto')
                    ->setCellValue('AW2','Solicitante');

                $fila = 3;
                $estado = "";
                $porcentaje = "100%";
                $estadofila = 0;
                $estadoSemaforo = "";
                $semaforo = "";
                $saldoRecibir = "";
                $saldo = "";
                $dias_atraso = "";
                $estado_pedido = "pendiente";
                $clase_operacion = "";
                $tipo_pedido = "";
                $estado_item = "";
                $transporte = "";
                $itemOrden = 1;
                $nro_orden = 0;
                $item = 1;

                forEach($datos AS $dato){
                    $tipo_orden = $dato['idtipomov'] == 37 ? 'BIENES' : 'SERVICIO';
                    $clase_operacion = $dato['idtipomov'] == 37 ? 'bienes' : 'servicios';
                    $saldoRecibir = $dato['cantidad_orden'] - $dato['ingreso'] > 0 ? $dato['cantidad_orden'] - $dato['ingreso'] : "-";
                    $dias_atraso  =  $saldoRecibir > 0 && $dato['dias_atraso'] < 1 ? $dato['dias_atraso'] : "-" ;
                    $suma_atendido = number_format($dato['cantidad_orden'] + $dato['cantidad_atendida'],2);

                    $cantidad = $dato['cantidad_pedido'];

                    $estado_pedido =  $dato['estadoItem'] >= 54 ? "Atendido":"Pendiente";
                    $estado_item   =  $dato['estadoItem'] >= 54 ? "Atendido":"Pendiente";

                    $transporte = $dato['nidreg'] == 39 ? "TERRESTRE": $dato['transporte'];
                    $atencion = $dato['atencion'] == 47 ? "NORMAL" : "URGENTE"; 

                    $color_mostrar  = 'FFFFFF';
                    $color_semaforo = 'FFFFFF';
                    $porcentaje = '';

                    $fecha_entrega = null;
                    $fecha_autoriza = null;

                    $dias_plazo = intVal( $dato['plazo'] )+1 .' days';

                    if( $dato['fechaLog'] !== null && $dato['fechaOpe'] !== null && $dato['FechaFin'] !== null ) {
                        $fecha_autoriza = $dato['fecha_autorizacion'];
                        $fecha_entrega = $dato['fecha_entrega_final'];
                    }

                    //formatos
                    //porcentajes
                    if ( $dato['estadoItem'] == 105 ) {
                        $porcentaje = "0%";
                        $estadofila = "anulado";
                        $estado_item = "anulado";
                        $estado_pedido = "anulado";
                        $color_mostrar = 'C8C8C8';
                    }else if( $dato['estadoItem'] == 49 ) {
                        $porcentaje = "10%";
                        $estadofila = "Procesando";
                        $estado_item = "item_stock";
                        $estado_pedido = "Procesando";
                        $color_mostrar = 'F8CAAD';
                    }else if( $dato['estadoItem'] == 53 ) {
                        $porcentaje = "10%";
                        $estadofila = "emitido";
                        $estado_item = "Emitido";
                        $estado_pedido = "Pedido Emitido";
                    }else if( $dato['estadoItem'] == 230 ) {
                        $porcentaje = "100%";
                        $estadofila = "comprado";
                        $estado_item = "Compra Local";
                        $estado_pedido = "Compra Local";
                        $color_mostrar = 'FF0000';
                    }else if( $dato['estadoItem'] == 54) {
                        $porcentaje = "15%";
                        $estadofila = "aprobado";
                        $estado_item = "aprobado";
                        $estado_pedido = "aprobado";
                        $color_mostrar = 'FC4236';
                    }else if( $dato['estadoItem'] == 52  && $dato['ingreso_obra'] == $dato['cantidad_pedido'] ) {
                        $porcentaje = "100%";
                        $estadofila = "entregado";
                        $estado_item = "atendido";
                        $estado_pedido = "atendido";
                        $color_mostrar = 'B3C5E6';
                    }else if( $dato['estadoItem'] == 52  && $dato['ingreso_obra'] == $dato['cantidad_aprobada'] && $dato['cantidad_aprobada'] > 0) {
                        $porcentaje = "100%";
                        $estadofila = "entregado";
                        $estado_item = "atendido";
                        $estado_pedido = "atendido";
                        $color_mostrar = 'B3C5E6';
                    }else if( $dato['estadoItem'] == 52 ) {
                        $porcentaje = "20%";
                        $estadofila = "stock";
                        $estado_item = "item_stock";
                        $estado_pedido = "stock";
                        $color_mostrar = 'B3C5E6';
                    }else if (!$dato['orden'] ) {
                        $porcentaje = "15%";
                        $estadofila = "item_aprobado";
                        $estado_item = "aprobado";
                        $estado_pedido = "aprobado";
                        $color_mostrar = 'FC4236';   
                    }else if ( $dato['orden'] && !$dato['proveedor']) {
                        $porcentaje = "25%";
                        $estadofila = "item_orden";
                        $estado_item = "aprobado";
                        $estado_pedido = "aprobado";   
                    }else if ( $dato['proveedor'] && !$dato['ingreso'] ) {
                        $porcentaje = "30%";
                        $estadofila = "item_enviado";
                        $estado_item = "atendido";
                        $estado_pedido = "atendido";
                        $color_mostrar = 'C0DCC0';
                    }else if( $dato['ingreso'] && $dato['ingreso'] < $dato['cantidad_orden'] ) {
                        $porcentaje = "40%";
                        $estadofila = "item_ingreso_parcial";
                        $estado_item = "atendido";
                        $estado_pedido = "atendido";
                        $color_mostrar = 'C0DCC0';
                    }else  if( !$dato['despachos'] && $dato['ingreso'] && $dato['ingreso'] == $dato['cantidad_orden'] ) {
                        $porcentaje = "50%";
                        $estadofila = "item_ingreso_total";
                        $estado_item = "atendido";
                        $estado_pedido = "atendido";
                        $color_mostrar = 'A9D08F';
                    }else if ( $dato['despachos'] && !$dato['ingreso_obra'] ) {
                        $porcentaje = "75%";
                        $estadofila = "item_transito";
                        $estado_item = "atendido";
                        $estado_pedido = "atendido";
                        $color_mostrar = '00FFFF';
                    }else if ( round($dato['ingreso_obra'],2) < round($dato['cantidad_orden'],2 )) {
                        $porcentaje = "85%";
                        $estadofila = "item_ingreso_parcial";
                        $estado_item = "atendido";
                        $estado_pedido = "atendido";
                        $color_mostrar = 'FFFFE1';
                    }else if ( $dato['ingreso_obra'] && round($suma_atendido,2) === round($dato['cantidad_aprobada'],2)) {
                        $porcentaje = "100%";
                        $estadofila = "entregado";
                        $estado_item = "atendido";
                        $estado_pedido = "atendido";
                        $semaforo = "Entregado";
                        $color_mostrar = '00FF00';
                    }else if ( $dato['ingreso_obra'] && round($dato['ingreso_obra'],2) === round($dato['cantidad_orden'],2)) {
                        $porcentaje = "100%";
                        $estadofila = "entregado";
                        $estado_item = "atendido";
                        $estado_pedido = "atendido";
                        $color_mostrar = '00FF00';
                    }

                    /*datos para el semaforo */
                    if ( $dato['estadoItem'] !== 105 ) {

                        if  ($fecha_entrega !== null){
                            $dias_atraso  =  $dato['dias_atraso'];

                            if ( $dato['ingreso_obra'] == $dato['cantidad_orden'] ){
                                $semaforoEstado = "Entregado";
                                $color_semaforo = '90EE90';
                                $dias_atraso  = "";
                            }else if ( $dias_atraso > 7 ) {
                                $semaforoEstado = "Verde";
                                $color_semaforo = '90EE90';
                                $dias_atraso  = "";
                            }else if ( $dias_atraso >= 0 && $dias_atraso <= 7){
                                $semaforoEstado = "Naranja";
                                $color_semaforo = 'FFD700';
                                $dias_atraso  = "";
                            }
                            else if ($dias_atraso < 0) {
                                $semaforoEstado = "Rojo";
                                $color_semaforo = 'FF0000';
                                $dias_atraso  =  $dato['dias_atraso']*-1;  //para que no salga negativo
                            } 
                        }else {
                            $dias_atraso  =  "";
                            $semaforoEstado = "Procesando";
                            $color_semaforo = "FFFF00";

                            if ( $dato['ingreso_obra'] > 0 && $dato['ingreso_obra'] === $dato['cantidad_atendida'] ){
                                $semaforoEstado = "Entregado";
                                $color_semaforo = '90EE90';
                                $dias_atraso  = "";
                            }else if ( $dato['cantidad_atendida'] > 0) {
                                $semaforoEstado = "Stock";
                                $color_semaforo = '90EE90';
                                $dias_atraso  = "";
                            }
                        }
                    }else {
                        $color_semaforo = 'CDCDCD';
                        $semaforoEstado = "Anulado";
                    }
                    //

                    $cantidad_compra = $dato['cantidad_pedido'] - $dato['cantidad_atendida'];

                    if ( $cantidad_compra != $dato['cantidad_aprobada'] ) {
                        $cantidad_compra = $dato['cantidad_aprobada'];
                    } 

                    $sheet->setCellValue('A'.$fila,$item++);
                    $sheet->setCellValue('B'.$fila,$porcentaje);

                    $sheet->setCellValue('C'.$fila,$dato['ccodproy']);
                    $sheet->setCellValue('D'.$fila,$dato['area']);
                    $sheet->setCellValue('E'.$fila,$dato['partida']);
                    $sheet->setCellValue('F'.$fila,$atencion);
                    $sheet->setCellValue('G'.$fila,$tipo_orden);
                    $sheet->setCellValue('H'.$fila,$dato['anio_pedido']);
                    $sheet->setCellValue('I'.$fila,$dato['pedido']);

                    $sheet->setCellValue('L'.$fila,$cantidad);
                    $sheet->setCellValue('M'.$fila,$dato['cantidad_aprobada']);
                    $sheet->setCellValue('N'.$fila,$cantidad_compra);

                    $sheet->setCellValue('O'.$fila,$dato['ccodprod']);
                    $sheet->setCellValue('P'.$fila,$dato['unidad']);
                    $sheet->setCellValue('Q'.$fila,$dato['descripcion']);
                    $sheet->setCellValue('R'.$fila,$tipo_orden);
                    $sheet->setCellValue('S'.$fila,$dato['anio_orden']);
                    $sheet->setCellValue('T'.$fila,$dato['cnumero']);

                    $sheet->setCellValue('AB'.$fila,$dato['ingreso']);
                    $sheet->setCellValue('AC'.$fila,$dato['nota_ingreso']);
                    $sheet->setCellValue('AD'.$fila,$dato['fecha_recepcion_proveedor']);
                    $sheet->setCellValue('AE'.$fila,$saldoRecibir);
                        
                    $sheet->setCellValue('AF'.$fila,$dato['plazo']);
                    $sheet->setCellValue('AG'.$fila,$dias_atraso);
                        
                    $sheet->setCellValue('AH'.$fila,strtoupper($semaforoEstado));
                        
                    $sheet->setCellValue('AI'.$fila,$dato['despachos']);
                    $sheet->setCellValue('AJ'.$fila,$dato['cnumguia']);

                    $sheet->setCellValue('AK'.$fila,$dato['fecha_traslado']);
                    $sheet->setCellValue('AL'.$fila,$dato['nota_transferencia']);
                        
                    $sheet->setCellValue('AM'.$fila,$dato['nota_obra']);
                    $sheet->setCellValue('AN'.$fila,$dato['fecha_registro_almacen']);
                    $sheet->setCellValue('AO'.$fila,$dato['ingreso_obra']);

                    $sheet->setCellValue('AP'.$fila,$estado_pedido);
                    $sheet->setCellValue('AQ'.$fila,$estado_item);
                    $sheet->setCellValue('AR'.$fila,$dato['nroparte']);
                    $sheet->setCellValue('AS'.$fila,$dato['cregistro']);
                    $sheet->setCellValue('AT'.$fila,$dato['operador']);
                    $sheet->setCellValue('AU'.$fila,$transporte);
                    $sheet->setCellValue('AV'.$fila,$dato['concepto']);

                    $sheet->setCellValue('AW'.$fila,$dato['nombre_elabora']);

                    $fila++;
                }
                
                $writer = new Xlsx($spread);
                $writer->save('public/documentos/temp/cargoplanSpreedSheet.xlsx');


            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function crearExcel($datos){
            require_once('public/PHPExcel/PHPExcel.php');

            $objPHPExcel = new PHPExcel();
            $objPHPExcel->getProperties()
                    ->setCreator("Sical")
                    ->setLastModifiedBy("Sical")
                    ->setTitle("Cargo Plan")
                    ->setSubject("Template excel")
                    ->setDescription("Cargo Plan")
                    ->setKeywords("Template excel");

                $cuerpo = array(
                    'font'  => array(
                    'bold'  => false,
                    'size'  => 7,
                ));

                $objWorkSheet = $objPHPExcel->createSheet(1);

                $objPHPExcel->setActiveSheetIndex(0);
                $objPHPExcel->getActiveSheet()->setTitle("Cargo Plan");

                $objPHPExcel->getActiveSheet()->mergeCells('A1:AW1');
                $objPHPExcel->getActiveSheet()->setCellValue('A1','CARGO PLAN');

                $objPHPExcel->getActiveSheet()->getStyle('A1:AW2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('A1:AW2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(60);

                $objPHPExcel->getActiveSheet()->getColumnDimension("D")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("E")->setWidth(50);
                $objPHPExcel->getActiveSheet()->getColumnDimension("J")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("K")->setWidth(10);
                $objPHPExcel->getActiveSheet()->getColumnDimension("L")->setWidth(15);
                $objPHPExcel->getActiveSheet()->getColumnDimension("M")->setWidth(15);
                $objPHPExcel->getActiveSheet()->getColumnDimension("N")->setWidth(15);
                $objPHPExcel->getActiveSheet()->getColumnDimension("O")->setWidth(24);
                $objPHPExcel->getActiveSheet()->getColumnDimension("Q")->setWidth(100);
                $objPHPExcel->getActiveSheet()->getColumnDimension("S")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("T")->setWidth(10);
                $objPHPExcel->getActiveSheet()->getColumnDimension("U")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("V")->setWidth(15);
                $objPHPExcel->getActiveSheet()->getColumnDimension("W")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("X")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("Y")->setWidth(9);
                $objPHPExcel->getActiveSheet()->getColumnDimension("Z")->setWidth(100);
                $objPHPExcel->getActiveSheet()->getColumnDimension("Y")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("AA")->setWidth(10);
                $objPHPExcel->getActiveSheet()->getColumnDimension("AB")->setWidth(10);
                $objPHPExcel->getActiveSheet()->getColumnDimension("AC")->setWidth(13);
                $objPHPExcel->getActiveSheet()->getColumnDimension("AD")->setWidth(15);
                $objPHPExcel->getActiveSheet()->getColumnDimension("AE")->setWidth(12);
                $objPHPExcel->getActiveSheet()->getColumnDimension("AF")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("AG")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("AH")->setWidth(12);
                $objPHPExcel->getActiveSheet()->getColumnDimension("AI")->setWidth(12);
                $objPHPExcel->getActiveSheet()->getColumnDimension("AJ")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("AK")->setWidth(14);
                $objPHPExcel->getActiveSheet()->getColumnDimension("AL")->setWidth(12);
                $objPHPExcel->getActiveSheet()->getColumnDimension("AM")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("AN")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("AO")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("AP")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("AQ")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("AR")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("AS")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("AT")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("AU")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("AV")->setWidth(50);
                $objPHPExcel->getActiveSheet()->getColumnDimension("AW")->setAutoSize(true);

                $objPHPExcel->getActiveSheet()
                            ->getStyle('A2:K2')
                            ->getFill()
                            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB('BFCDDB');
                
                $objPHPExcel->getActiveSheet()
                            ->getStyle('L2:N2')
                            ->getFill()
                            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB('FC4236');

                $objPHPExcel->getActiveSheet()
                            ->getStyle('O2:P2')
                            ->getFill()
                            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB('BFCDDB');

                $objPHPExcel->getActiveSheet()
                            ->getStyle('Q2:V2')
                            ->getFill()
                            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB('00FFFF');
                
                $objPHPExcel->getActiveSheet()
                            ->getStyle('W2:AD2')
                            ->getFill()
                            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB('BFCDDB');

                $objPHPExcel->getActiveSheet()
                            ->getStyle('AE2:AM2')
                            ->getFill()
                            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB('FFFF00');

                $objPHPExcel->getActiveSheet()
                            ->getStyle('AN2:AW2')
                            ->getFill()
                            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB('127BDD');

                $objPHPExcel->getActiveSheet()->getStyle('A1:AW2')->getAlignment()->setWrapText(true);

                $objPHPExcel->getActiveSheet()->setCellValue('A2','Items'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('B2','Estado Actual'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('C2','Codigo Proyecto'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('D2','Area'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('E2','Partida'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('F2','Atención'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('G2','Tipo'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('H2','Año Pedido'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('I2','N° Pedido'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('J2','Creación Pedido'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('K2','Aprobación del Pedido'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('L2','Cantidad Pedida'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('M2','Cantidad Aprobada'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('N2','Cantidad Compra'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('O2','Codigo del Bien/Servicio'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('P2','Unidad Medida'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('Q2','Descripcion del Bien/Servicio'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('R2','Tipo Orden'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('S2','Año Orden'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('T2','Nro Orden'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('U2','Fecha Orden'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('V2','Cantidad Orden'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('W2','Item Orden'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('X2','Fecha Autorizacion'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('Y2','Atencion Almacen'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('Z2','Descripcion del proveedor'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AA2','Fecha Entrega Proveedor'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AB2','Cant. Recibida'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AC2','Nota de Ingreso'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AD2','Fecha Recepcion Proveedor'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AE2','Saldo por Recibir'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AF2','Dias Entrega'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AG2','Días Atrazo'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AH2','Semáforo'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AI2','Cantidad Despachada'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AJ2','Nro. Guia'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AK2','Nro. Guia Transferencia'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AL2','Fecha Traslado'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AM2','Registro Almacen'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AN2','Fecha Ingreso Almacen'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AO2','Cantidad en Obra'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AP2','Estado Pedido'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AQ2','Estado Item'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AR2','N° Parte'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AS2','Codigo Activo'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AT2','Operador Logístico'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AU2','Tipo Transporte'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AV2','Observaciones/Concepto'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AW2','Solicitante'); // esto cambia


                $objPHPExcel->getActiveSheet()->getStyle('B:C')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('B:C')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                
                $objPHPExcel->getActiveSheet()->getStyle('F:K')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('F:K')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $objPHPExcel->getActiveSheet()->getStyle('O:P')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('O:P')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $objPHPExcel->getActiveSheet()->getStyle('R:S')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('R:S')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $objPHPExcel->getActiveSheet()->getStyle('Y')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('Y')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $objPHPExcel->getActiveSheet()->getStyle('AA')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('AA')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $objPHPExcel->getActiveSheet()->getStyle('AC')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('AC')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $objPHPExcel->getActiveSheet()->getStyle('AE')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('AE')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $objPHPExcel->getActiveSheet()->getStyle('AH')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('AH')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $objPHPExcel->getActiveSheet()->getStyle('AJ:AO')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('AJ:AO')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $objPHPExcel->getActiveSheet()->getStyle('J')->getNumberFormat()->setFormatCode('dd/mm/yyyy');
                $objPHPExcel->getActiveSheet()->getStyle('L')->getNumberFormat()->setFormatCode('#,##0.00');
                $objPHPExcel->getActiveSheet()->getStyle('M')->getNumberFormat()->setFormatCode('#,##0.00');
                $objPHPExcel->getActiveSheet()->getStyle('N')->getNumberFormat()->setFormatCode('#,##0.00');

                $objPHPExcel->getActiveSheet()->getStyle('O')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
    
                $objPHPExcel->getActiveSheet()->getStyle('U')->getNumberFormat()->setFormatCode('dd/mm/yyyy');
                $objPHPExcel->getActiveSheet()->getStyle('V')->getNumberFormat()->setFormatCode('#,##0.00');
                $objPHPExcel->getActiveSheet()->getStyle('X')->getNumberFormat()->setFormatCode('dd/mm/yyyy');

                $objPHPExcel->getActiveSheet()->getStyle('Y')->getNumberFormat()->setFormatCode('#,##0.00');
                $objPHPExcel->getActiveSheet()->getStyle('AA')->getNumberFormat()->setFormatCode('dd/mm/yyyy');
                $objPHPExcel->getActiveSheet()->getStyle('AB')->getNumberFormat()->setFormatCode('#,##0.00');
                $objPHPExcel->getActiveSheet()->getStyle('AI')->getNumberFormat()->setFormatCode('#,##0.00');
                $objPHPExcel->getActiveSheet()->getStyle('AK')->getNumberFormat()->setFormatCode('dd/mm/yyyy');
                $objPHPExcel->getActiveSheet()->getStyle('AO')->getNumberFormat()->setFormatCode('#,##0.00');
               
                $fila = 3;
                $estado = "";
                $porcentaje = "100%";
                $estadofila = 0;
                $estadoSemaforo = "";
                $semaforo = "";
                $saldoRecibir = "";
                $saldo = "";
                $dias_atraso = "";
                $estado_pedido = "pendiente";
                $clase_operacion = "";
                $tipo_pedido = "";
                $estado_item = "";
                $transporte = "";
                $itemOrden = 1;
                $nro_orden = 0;
                $item = 1;

                forEach($datos AS $dato){

                    $tipo_orden = $dato['idtipomov'] == 37 ? 'BIENES' : 'SERVICIO';
                    $clase_operacion = $dato['idtipomov'] == 37 ? 'bienes' : 'servicios';
                    $saldoRecibir = $dato['cantidad_orden'] - $dato['ingreso'] > 0 ? $dato['cantidad_orden'] - $dato['ingreso'] : "-";
                    $dias_atraso  =  $saldoRecibir > 0 && $dato['dias_atraso'] < 1 ? $dato['dias_atraso'] : "-" ;
                    $suma_atendido = number_format($dato['cantidad_orden'] + $dato['cantidad_atendida'],2);

                    $cantidad = $dato['cantidad_pedido'];

                    $estado_pedido =  $dato['estadoItem'] >= 54 ? "Atendido":"Pendiente";
                    $estado_item   =  $dato['estadoItem'] >= 54 ? "Atendido":"Pendiente";

                    $transporte = $dato['nidreg'] == 39 ? "TERRESTRE": $dato['transporte'];
                    $atencion = $dato['atencion'] == 47 ? "NORMAL" : "URGENTE"; 

                    $color_mostrar  = 'FFFFFF';
                    $color_semaforo = 'FFFFFF';
                    $porcentaje = '';

                    $fecha_entrega = null;
                    $fecha_autoriza = null;

                    $dias_plazo = intVal( $dato['plazo'] )+1 .' days';

                    if( $dato['fechaLog'] !== null && $dato['fechaOpe'] !== null && $dato['FechaFin'] !== null ) {
                        $fecha_autoriza = $dato['fecha_autorizacion'];
                        $fecha_entrega = $dato['fecha_entrega_final'];
                    }

                    /*datos para el semaforo */
                    if ( $dato['estadoItem'] !== 105 ) {

                        if  ($fecha_entrega !== null){
                            $dias_atraso  =  $dato['dias_atraso'];

                            if ( $dato['ingreso_obra'] == $dato['cantidad_orden'] ){
                                $semaforoEstado = "Entregado";
                                $color_semaforo = '90EE90';
                                $dias_atraso  = "";
                            }else if ( $dias_atraso > 7 ) {
                                $semaforoEstado = "Verde";
                                $color_semaforo = '90EE90';
                                $dias_atraso  = "";
                            }else if ( $dias_atraso >= 0 && $dias_atraso <= 7){
                                $semaforoEstado = "Naranja";
                                $color_semaforo = 'FFD700';
                                $dias_atraso  = "";
                            }
                            else if ($dias_atraso < 0) {
                                $semaforoEstado = "Rojo";
                                $color_semaforo = 'FF0000';
                                $dias_atraso  =  $dato['dias_atraso']*-1;  //para que no salga negativo
                            } 
                        }else {
                            $dias_atraso  =  "";
                            $semaforoEstado = "Procesando";
                            $color_semaforo = "FFFF00";

                            if ( $dato['ingreso_obra'] > 0 && $dato['ingreso_obra'] === $dato['cantidad_atendida'] ){
                                $semaforoEstado = "Entregado";
                                $color_semaforo = '90EE90';
                                $dias_atraso  = "";
                            }else if ( $dato['cantidad_atendida'] > 0) {
                                $semaforoEstado = "Stock";
                                $color_semaforo = '90EE90';
                                $dias_atraso  = "";
                            }
                        }
                    }else {
                        $color_semaforo = 'CDCDCD';
                        $semaforoEstado = "Anulado";
                    }


                    if ( $dato['estadoItem'] == 105 ) {
                        $porcentaje = "0%";
                        $estadofila = "anulado";
                        $estado_item = "anulado";
                        $estado_pedido = "anulado";
                        $color_mostrar = 'C8C8C8';
                    }else if( $dato['estadoItem'] == 49 ) {
                        $porcentaje = "10%";
                        $estadofila = "Procesando";
                        $estado_item = "item_stock";
                        $estado_pedido = "Procesando";
                        $color_mostrar = 'F8CAAD';
                    }else if( $dato['estadoItem'] == 53 ) {
                        $porcentaje = "10%";
                        $estadofila = "emitido";
                        $estado_item = "Emitido";
                        $estado_pedido = "Pedido Emitido";
                    }else if( $dato['estadoItem'] == 230 ) {
                        $porcentaje = "100%";
                        $estadofila = "comprado";
                        $estado_item = "Compra Local";
                        $estado_pedido = "Compra Local";
                        $color_mostrar = 'FF0000';
                    }else if( $dato['estadoItem'] == 54) {
                        $porcentaje = "15%";
                        $estadofila = "aprobado";
                        $estado_item = "aprobado";
                        $estado_pedido = "aprobado";
                        $color_mostrar = 'FC4236';
                    }else if( $dato['estadoItem'] == 52  && $dato['ingreso_obra'] == $dato['cantidad_pedido'] ) {
                        $porcentaje = "100%";
                        $estadofila = "entregado";
                        $estado_item = "atendido";
                        $estado_pedido = "atendido";
                        $color_mostrar = 'B3C5E6';
                    }else if( $dato['estadoItem'] == 52  && $dato['ingreso_obra'] == $dato['cantidad_aprobada'] && $dato['cantidad_aprobada'] > 0) {
                        $porcentaje = "100%";
                        $estadofila = "entregado";
                        $estado_item = "atendido";
                        $estado_pedido = "atendido";
                        $color_mostrar = 'B3C5E6';
                    }else if( $dato['estadoItem'] == 52 ) {
                        $porcentaje = "20%";
                        $estadofila = "stock";
                        $estado_item = "item_stock";
                        $estado_pedido = "stock";
                        $color_mostrar = 'B3C5E6';
                    }else if (!$dato['orden'] ) {
                        $porcentaje = "15%";
                        $estadofila = "item_aprobado";
                        $estado_item = "aprobado";
                        $estado_pedido = "aprobado";
                        $color_mostrar = 'FC4236';   
                    }else if ( $dato['orden'] && !$dato['proveedor']) {
                        $porcentaje = "25%";
                        $estadofila = "item_orden";
                        $estado_item = "aprobado";
                        $estado_pedido = "aprobado";   
                    }else if ( $dato['proveedor'] && !$dato['ingreso'] ) {
                        $porcentaje = "30%";
                        $estadofila = "item_enviado";
                        $estado_item = "atendido";
                        $estado_pedido = "atendido";
                        $color_mostrar = 'C0DCC0';
                    }else if( $dato['ingreso'] && $dato['ingreso'] < $dato['cantidad_orden'] ) {
                        $porcentaje = "40%";
                        $estadofila = "item_ingreso_parcial";
                        $estado_item = "atendido";
                        $estado_pedido = "atendido";
                        $color_mostrar = 'C0DCC0';
                    }else  if( !$dato['despachos'] && $dato['ingreso'] && $dato['ingreso'] == $dato['cantidad_orden'] ) {
                        $porcentaje = "50%";
                        $estadofila = "item_ingreso_total";
                        $estado_item = "atendido";
                        $estado_pedido = "atendido";
                        $color_mostrar = 'A9D08F';
                    }else if ( $dato['despachos'] && !$dato['ingreso_obra'] ) {
                        $porcentaje = "75%";
                        $estadofila = "item_transito";
                        $estado_item = "atendido";
                        $estado_pedido = "atendido";
                        $color_mostrar = '00FFFF';
                    }else if ( round($dato['ingreso_obra'],2) < round($dato['cantidad_orden'],2 )) {
                        $porcentaje = "85%";
                        $estadofila = "item_ingreso_parcial";
                        $estado_item = "atendido";
                        $estado_pedido = "atendido";
                        $color_mostrar = 'FFFFE1';
                    }else if ( $dato['ingreso_obra'] && round($suma_atendido,2) === round($dato['cantidad_aprobada'],2)) {
                        $porcentaje = "100%";
                        $estadofila = "entregado";
                        $estado_item = "atendido";
                        $estado_pedido = "atendido";
                        $semaforo = "Entregado";
                        $color_mostrar = '00FF00';
                    }else if ( $dato['ingreso_obra'] && round($dato['ingreso_obra'],2) === round($dato['cantidad_orden'],2)) {
                        $porcentaje = "100%";
                        $estadofila = "entregado";
                        $estado_item = "atendido";
                        $estado_pedido = "atendido";
                        $color_mostrar = '00FF00';
                    }

                    $color = array(
                        'fill' => array(
                            'type' => PHPExcel_Style_Fill::FILL_SOLID,
                            'startcolor' => array(
                                'argb' => $color_mostrar,
                            ),
                            'endcolor' => array(
                                'argb' =>  $color_mostrar,
                            ),
                        ),
                    );

                    $semaforo = array(
                        'fill' => array(
                            'type' => PHPExcel_Style_Fill::FILL_SOLID,
                            'startcolor' => array(
                                'argb' => $color_semaforo,
                            ),
                            'endcolor' => array(
                                'argb' => $color_semaforo,
                            ),
                        ),
                    );

                    $cantidad_compra = $dato['cantidad_pedido'] - $dato['cantidad_atendida'];

                    if ( $cantidad_compra != $dato['cantidad_aprobada'] ) {
                        $cantidad_compra = $dato['cantidad_aprobada'];
                    } 

                    $objPHPExcel->getActiveSheet()->setCellValue('A'.$fila,$item++);
                    $objPHPExcel->getActiveSheet()->setCellValue('B'.$fila,$porcentaje);
                    $objPHPExcel->getActiveSheet()->getStyle('B'.$fila)->applyFromArray($color);

                    $objPHPExcel->getActiveSheet()->setCellValue('C'.$fila,$dato['ccodproy']);
                    $objPHPExcel->getActiveSheet()->setCellValue('D'.$fila,$dato['area']);
                    $objPHPExcel->getActiveSheet()->setCellValue('E'.$fila,$dato['partida']);
                    $objPHPExcel->getActiveSheet()->setCellValue('F'.$fila,$atencion);
                    $objPHPExcel->getActiveSheet()->setCellValue('G'.$fila,$tipo_orden);
                    $objPHPExcel->getActiveSheet()->setCellValue('H'.$fila,$dato['anio_pedido']);
                    $objPHPExcel->getActiveSheet()->setCellValue('I'.$fila,$dato['pedido']);

                    if  ( $dato['crea_pedido'] !== "" && $dato['crea_pedido'] !== null )
                            $objPHPExcel->getActiveSheet()->setCellValue('J'.$fila,PHPExcel_Shared_Date::PHPToExcel($dato['crea_pedido']));
                           
                    if  ( $dato['aprobacion_pedido'] !== null )
                        $objPHPExcel->getActiveSheet()->setCellValue('K'.$fila,$dato['aprobacion_pedido']);
                    else
                        $objPHPExcel->getActiveSheet()->setCellValue('K'.$fila,'');

                    $objPHPExcel->getActiveSheet()->setCellValue('L'.$fila,$cantidad);
                    $objPHPExcel->getActiveSheet()->setCellValue('M'.$fila,$dato['cantidad_aprobada']);
                    $objPHPExcel->getActiveSheet()->setCellValue('N'.$fila,$cantidad_compra);

                    $objPHPExcel->getActiveSheet()->setCellValue('O'.$fila,$dato['ccodprod']);
                    $objPHPExcel->getActiveSheet()->setCellValue('P'.$fila,$dato['unidad']);
                    $objPHPExcel->getActiveSheet()->setCellValue('Q'.$fila,$dato['descripcion']);
                    $objPHPExcel->getActiveSheet()->setCellValue('R'.$fila,$tipo_orden);
                    $objPHPExcel->getActiveSheet()->setCellValue('S'.$fila,$dato['anio_orden']);
                    $objPHPExcel->getActiveSheet()->setCellValue('T'.$fila,$dato['cnumero']);

                    if  ($dato['fecha_orden'] !== null)
                        $objPHPExcel->getActiveSheet()->setCellValue('U'.$fila,PHPExcel_Shared_Date::PHPToExcel($dato['fecha_orden']));

                    $objPHPExcel->getActiveSheet()->setCellValue('V'.$fila,$dato['cantidad_orden']);

                    $objPHPExcel->getActiveSheet()->setCellValue('W'.$fila,$dato['item_orden']);

                    if  ( $fecha_autoriza !== null )
                        $objPHPExcel->getActiveSheet()->setCellValue('X'.$fila,PHPExcel_Shared_Date::PHPToExcel($fecha_autoriza));
                        
                    $objPHPExcel->getActiveSheet()->setCellValue('Y'.$fila,$dato['cantidad_atendida']);
                    $objPHPExcel->getActiveSheet()->setCellValue('Z'.$fila,$dato['proveedor']); 
                        
                    if ( $fecha_entrega !== null )
                        $objPHPExcel->getActiveSheet()->setCellValue('AA'.$fila,PHPExcel_Shared_Date::PHPToExcel($fecha_entrega));
                    
                    $objPHPExcel->getActiveSheet()->setCellValue('AB'.$fila,$dato['ingreso']);
                    $objPHPExcel->getActiveSheet()->setCellValue('AC'.$fila,$dato['nota_ingreso']);
                    $objPHPExcel->getActiveSheet()->setCellValue('AD'.$fila,$dato['fecha_recepcion_proveedor']);
                    $objPHPExcel->getActiveSheet()->setCellValue('AE'.$fila,$saldoRecibir);
                        
                    $objPHPExcel->getActiveSheet()->setCellValue('AF'.$fila,$dato['plazo']);
                    $objPHPExcel->getActiveSheet()->setCellValue('AG'.$fila,$dias_atraso);
                        
                    $objPHPExcel->getActiveSheet()->setCellValue('AH'.$fila,strtoupper($semaforoEstado));
                    $objPHPExcel->getActiveSheet()->getStyle('AH'.$fila)->applyFromArray($semaforo);
                        
                    $objPHPExcel->getActiveSheet()->setCellValue('AI'.$fila,$dato['despachos']);
                    $objPHPExcel->getActiveSheet()->setCellValue('AJ'.$fila,$dato['cnumguia']);

                    $objPHPExcel->getActiveSheet()->setCellValue('AK'.$fila,$dato['fecha_traslado']);
                    $objPHPExcel->getActiveSheet()->setCellValue('AL'.$fila,$dato['nota_transferencia']);
                        
                    $objPHPExcel->getActiveSheet()->setCellValue('AM'.$fila,$dato['nota_obra']);
                    $objPHPExcel->getActiveSheet()->setCellValue('AN'.$fila,$dato['fecha_registro_almacen']);
                    $objPHPExcel->getActiveSheet()->setCellValue('AO'.$fila,$dato['ingreso_obra']);

                    $objPHPExcel->getActiveSheet()->setCellValue('AP'.$fila,$estado_pedido);
                    $objPHPExcel->getActiveSheet()->setCellValue('AQ'.$fila,$estado_item);
                    $objPHPExcel->getActiveSheet()->setCellValue('AR'.$fila,$dato['nroparte']);
                    $objPHPExcel->getActiveSheet()->setCellValue('AS'.$fila,$dato['cregistro']);
                    $objPHPExcel->getActiveSheet()->setCellValue('AT'.$fila,$dato['operador']);
                    $objPHPExcel->getActiveSheet()->setCellValue('AU'.$fila,$transporte);
                    $objPHPExcel->getActiveSheet()->setCellValue('AV'.$fila,$dato['concepto']);

                    $objPHPExcel->getActiveSheet()->setCellValue('AW'.$fila,$dato['nombre_elabora']);

                    $fila++;
                }

                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');
                $objWriter->save('public/documentos/reportes/cargoplan.xlsx');
        }

        private function crearSpout($datos){
            require_once("public/Box/Spout/Autoloader/autoload.php");

            $fila = [];
            $i = 1;

            $writer = WriterEntityFactory::createXLSXWriter();
            $writer->setTempFolder('public/documentos/temp/');
            //$writer->setColumnsWidth(100);

            $writer->openToFile('public/documentos/temp/cargoplan.xlsx');

            /** Create a style with the StyleBuilder */
            $header = (new StyleBuilder())
                    ->setFontBold()
                    ->setFontSize(10)
                    ->setFontColor(Color::WHITE)
                    ->setShouldWrapText()
                    ->setCellAlignment(CellAlignment::CENTER)
                    ->setBackgroundColor(Color::BLUE)
                    ->setFormat(200)
                    ->build();

            /** Shortcut: add a row from an array of values */
            $titulo = array('Items','Estado Actual','Codigo Proyecto','Area','Partida','Atención','Tipo','Año Pedido','N° Pedido', 'Creación Pedido',
                                    'Aprobación del Pedido','Cantidad Pedida', 'Cantidad Aprobada', 'Cantidad Compra','Codigo del Bien/Servicio','Unidad Medida',
                                    'Descripcion del Bien/Servicio','Tipo Orden','Año Orden', 'Nro Orden', 'Fecha Orden', 'Cantidad Orden', 'Item Orden', 'Fecha Autorizacion',
                                    'Atencion Almacen', 'Descripcion del proveedor','Fecha Entrega Proveedor','Cant. Recibida','Nota de Ingreso', 'Fecha Recepcion Proveedor',
                                    'Saldo por Recibir','Dias Entrega','Días Atrazo','Semáforo', 'Cantidad Despachada','Nro. Guia','Fecha Traslado','Nro. Guia Transferencia',
                                    'Registro Almacen','Fecha Ingreso Almacen', 'Cantidad en Obra', 'Estado Pedido', 'Estado Item', 'N° Parte', 'Codigo Activo', 'Operador Logístico', 
                                    'Tipo Transporte','Observaciones/Concepto','Solicitante');

            $rowFromValues = WriterEntityFactory::createRowFromArray($titulo,$header);
            $writer->addRow($rowFromValues);

            foreach($datos as $dato){

                $tipo_orden = $dato['idtipomov'] == 37 ? 'B' : 'S';
                $clase_operacion = $dato['idtipomov'] == 37 ? 'bienes' : 'servicios';
                
                $saldoRecibir = $dato['cantidad_orden'] - $dato['ingreso'] > 0 ? $dato['cantidad_orden'] - $dato['ingreso'] : "-";

                $dias_atraso  =  "";
                
                $estadoSemaforo = "";
                $semaforo = "";
                $porcentaje = "100%";

                $transporte = $dato['nidreg'] == 39 ? "TERRESTRE": $dato['transporte'];
                $atencion = $dato['atencion'] == 47 ? "NORMAL" : "URGENTE"; 

                $suma_atendido = number_format($dato['cantidad_orden'] + $dato['cantidad_atendida'],2);

                $estado_pedido =  $dato['estadoItem'] >= 54 ? "Atendido":"Pendiente";
                $estado_item   =  $dato['estadoItem'] >= 54 ? "Atendido":"Pendiente";

                $cantidad = $dato['cantidad_pedido'];

                if ( $dato['estadoItem'] == 105 ) {
                    $porcentaje = "0%";
                    $estadofila = "anulado";
                    $estado_item = "anulado";
                    $estado_pedido = "anulado";
                    $color_mostrar = 'C8C8C8';
                }else if( $dato['estadoItem'] == 49 ) {
                    $porcentaje = "10%";
                    $estadofila = "Procesando";
                    $estado_item = "item_stock";
                    $estado_pedido = "Procesando";
                    $color_mostrar = 'F8CAAD';
                }else if( $dato['estadoItem'] == 53 ) {
                    $porcentaje = "10%";
                    $estadofila = "emitido";
                    $estado_item = "Emitido";
                    $estado_pedido = "Pedido Emitido";
                }else if( $dato['estadoItem'] == 230 ) {
                    $porcentaje = "100%";
                    $estadofila = "comprado";
                    $estado_item = "Compra Local";
                    $estado_pedido = "Compra Local";
                    $color_mostrar = 'FF0000';
                }else if( $dato['estadoItem'] == 54) {
                    $porcentaje = "15%";
                    $estadofila = "aprobado";
                    $estado_item = "aprobado";
                    $estado_pedido = "aprobado";
                    $color_mostrar = 'FC4236';
                }else if( $dato['estadoItem'] == 52  && $dato['ingreso_obra'] == $dato['cantidad_pedido'] ) {
                    $porcentaje = "100%";
                    $estadofila = "entregado";
                    $estado_item = "atendido";
                    $estado_pedido = "atendido";
                    $color_mostrar = 'B3C5E6';
                }else if( $dato['estadoItem'] == 52  && $dato['ingreso_obra'] == $dato['cantidad_aprobada'] && $dato['cantidad_aprobada'] > 0) {
                    $porcentaje = "100%";
                    $estadofila = "entregado";
                    $estado_item = "atendido";
                    $estado_pedido = "atendido";
                    $color_mostrar = 'B3C5E6';
                }else if( $dato['estadoItem'] == 52 ) {
                    $porcentaje = "20%";
                    $estadofila = "stock";
                    $estado_item = "item_stock";
                    $estado_pedido = "stock";
                    $color_mostrar = 'B3C5E6';
                }else if (!$dato['orden'] ) {
                    $porcentaje = "15%";
                    $estadofila = "item_aprobado";
                    $estado_item = "aprobado";
                    $estado_pedido = "aprobado";
                    $color_mostrar = 'FC4236';   
                }else if ( $dato['orden'] && !$dato['proveedor']) {
                    $porcentaje = "25%";
                    $estadofila = "item_orden";
                    $estado_item = "aprobado";
                    $estado_pedido = "aprobado";   
                }else if ( $dato['proveedor'] && !$dato['ingreso'] ) {
                    $porcentaje = "30%";
                    $estadofila = "item_enviado";
                    $estado_item = "atendido";
                    $estado_pedido = "atendido";
                    $color_mostrar = 'C0DCC0';
                }else if( $dato['ingreso'] && $dato['ingreso'] < $dato['cantidad_orden'] ) {
                    $porcentaje = "40%";
                    $estadofila = "item_ingreso_parcial";
                    $estado_item = "atendido";
                    $estado_pedido = "atendido";
                    $color_mostrar = 'C0DCC0';
                }else  if( !$dato['despachos'] && $dato['ingreso'] && $dato['ingreso'] == $dato['cantidad_orden'] ) {
                    $porcentaje = "50%";
                    $estadofila = "item_ingreso_total";
                    $estado_item = "atendido";
                    $estado_pedido = "atendido";
                    $color_mostrar = 'A9D08F';
                }else if ( $dato['despachos'] && !$dato['ingreso_obra'] ) {
                    $porcentaje = "75%";
                    $estadofila = "item_transito";
                    $estado_item = "atendido";
                    $estado_pedido = "atendido";
                    $color_mostrar = '00FFFF';
                }else if ( round($dato['ingreso_obra'],2) < round($dato['cantidad_orden'],2 )) {
                    $porcentaje = "85%";
                    $estadofila = "item_ingreso_parcial";
                    $estado_item = "atendido";
                    $estado_pedido = "atendido";
                    $color_mostrar = 'FFFFE1';
                }else if ( $dato['ingreso_obra'] && round($suma_atendido,2) === round($dato['cantidad_aprobada'],2)) {
                    $porcentaje = "100%";
                    $estadofila = "entregado";
                    $estado_item = "atendido";
                    $estado_pedido = "atendido";
                    $semaforo = "Entregado";
                    $color_mostrar = '00FF00';
                }else if ( $dato['ingreso_obra'] && round($dato['ingreso_obra'],2) === round($dato['cantidad_orden'],2)) {
                    $porcentaje = "100%";
                    $estadofila = "entregado";
                    $estado_item = "atendido";
                    $estado_pedido = "atendido";
                    $color_mostrar = '00FF00';
                }

                $creacion_pedido = $dato['crea_pedido'] !== null ? $dato['crea_pedido'] : "";
                $aprueba_pedido =  $dato['aprobacion_pedido'] !== null ? $dato['aprobacion_pedido'] : "";
                $cantidad_aprobado = $dato['cantidad_aprobada'];
                   
                $fila = array($i++,$porcentaje,$dato['ccodproy'],$dato['area'],$dato['partida'],$atencion,$tipo_orden,$dato['anio_pedido'],$dato['pedido'],
                                $creacion_pedido,$aprueba_pedido,$cantidad,$dato['cantidad_aprobada']);
                $rowFromValues = WriterEntityFactory::createRowFromArray($fila);
                $writer->addRow($rowFromValues);
            }

            $writer->close();
        }
    }
?>