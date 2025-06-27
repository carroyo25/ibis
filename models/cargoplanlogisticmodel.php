<?php
    class CargoPLanLogisticModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarCargoPlanLogistica($parametros){
            try {

                $salida = "";

                $tipo       = $parametros['tipoSearch']     == -1 ? "%" : $parametros['tipoSearch'];
                $costo      = $parametros['costosSearch']   == -1 ? "%" : $parametros['costosSearch'];
                $descrip    = $parametros['descripSearch']  == "" ? "%" : "%".$parametros['descripSearch']."%";
                $codigo     = $parametros['codigoSearch']   == "" ? "%" : "%".$parametros['codigoSearch']."%";
                $orden      = $parametros['ordenSearch']    == "" ? "%" : $parametros['ordenSearch'];
                $pedido     = $parametros['pedidoSearch']   == "" ? "%" : $parametros['pedidoSearch'];
                $concepto   = $parametros['conceptoSearch'] == "" ? "%" : "%".$parametros['conceptoSearch']."%";
                $estadoItem = $parametros['estado_item']    == "" ? "%" : $parametros['estado_item'];
                $anio       = $parametros['anioSearch']     == "" ? "%" : $parametros['anioSearch'];
                $userID     = $_SESSION['iduser'];
                
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
                                                    tb_pedidodet.estadoItem,
                                                    LPAD( tb_pedidocab.nrodoc, 6, 0 ) AS pedido,
                                                    DATE_FORMAT( tb_pedidocab.emision, '%d/%m/%Y' ) AS crea_pedido,
                                                    DATE_FORMAT( tb_pedidocab.faprueba, '%d/%m/%Y' ) AS aprobacion_pedido,
                                                    tb_pedidocab.anio AS anio_pedido,
                                                    tb_pedidocab.mes AS pedido_mes,
                                                    tb_pedidocab.nivelAten AS atencion,
                                                    tb_pedidocab.idtipomov,
                                                    UPPER( tb_pedidocab.concepto ) AS concepto,
                                                    lg_ordendet.id_orden AS orden,
                                                    lg_ordendet.item AS item_orden,
                                                    cm_producto.ccodprod,
                                                    UPPER( CONCAT_WS( ' ', cm_producto.cdesprod, tb_pedidodet.observaciones ) ) AS descripcion,
                                                    tb_proyectos.ccodproy,
                                                    tb_proyectos.nidreg AS idproyecto,
                                                    UPPER( tb_area.cdesarea ) AS area,
                                                    UPPER( tb_partidas.cdescripcion ) AS partida,
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
                                                    LPAD( lg_ordencab.cnumero, 4, 0 ) AS cnumero,
                                                    UPPER( cm_entidad.crazonsoc ) AS proveedor,
                                                    ( SELECT SUM( lg_ordendet.ncanti ) FROM lg_ordendet WHERE lg_ordendet.niddeta = tb_pedidodet.iditem AND lg_ordendet.id_orden != 0 ) AS cantidad_orden,
                                                    ( SELECT SUM( alm_recepdet.ncantidad ) FROM alm_recepdet WHERE alm_recepdet.niddetaPed = tb_pedidodet.iditem AND alm_recepdet.nflgactivo = 1 ) AS ingreso,
                                                    ( SELECT SUM( alm_despachodet.ndespacho ) FROM alm_despachodet WHERE alm_despachodet.niddetaPed = lg_ordendet.niddeta AND alm_despachodet.nflgactivo = 1 ) AS despachos,
                                                    ( SELECT SUM( alm_existencia.cant_ingr ) FROM alm_existencia WHERE alm_existencia.idpedido = tb_pedidodet.iditem AND alm_existencia.nflgActivo = 1 ) AS ingreso_obra,
                                                    ( SELECT SUM( alm_existencia.cant_ingr ) FROM alm_existencia WHERE alm_existencia.idpedido = tb_pedidodet.iditem AND alm_existencia.nflgActivo = 1 ) AS atencion_almacen,
                                                    UPPER( asignacion.cnameuser ) AS operador,
                                                    DATEDIFF( lg_ordencab.ffechaent, NOW() ) AS dias_atraso,
                                                    transporte.cdescripcion AS transporte,
                                                    transporte.nidreg,
                                                    user_aprueba.cnombres,
                                                    alm_despachocab.cnumguia,
                                                    LPAD( alm_recepcab.nnronota, 6, 0 ) AS nota_ingreso,
                                                    LPAD( alm_cabexist.idreg, 6, 0 ) AS nota_obra,
                                                    DATE_FORMAT( alm_cabexist.ffechadoc, '%d/%m/%Y' ) AS fecha_ingreso_almacen_obra,
                                                    DATE_FORMAT( alm_recepcab.ffecdoc, '%d/%m/%Y' ) AS fecha_recepcion_proveedor,
                                                    tb_equipmtto.cregistro,
                                                    usuarios.cnombres AS usuario,
                                                    DATE_ADD( lg_ordencab.ffechades, INTERVAL lg_ordencab.nplazo DAY ) AS fecha_entrega_final_anterior,
                                                    alm_despachodet.id_regalm,
                                                    DATE_FORMAT(alm_despachocab.ffecenvio, '%d/%m/%Y' ) AS salida_lurin,
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
                                                    alm_transfercab.cnumguia AS guia_transferencia,
                                                    LPAD( alm_transfercab.idreg, 6, 0 ) AS nota_transferencia,
                                                    DATE_FORMAT( alm_transfercab.ftraslado, '%d/%m/%Y' ) AS fecha_traslado,
                                                    UPPER(asignacion.cnameuser) AS asigna,
                                                    sunat.guiasunat,
                                                    embarca.nombreEmbarca,
                                                    embarca.fechaEmbarca,
                                                    alm_madresdet.tracking,
                                                    alm_madresdet.trackinglurin
                                                FROM
                                                    tb_pedidodet
                                                    LEFT JOIN tb_pedidocab ON tb_pedidodet.idpedido = tb_pedidocab.idreg
                                                    LEFT JOIN tb_costusu ON tb_costusu.ncodproy = tb_pedidodet.idcostos
                                                    LEFT JOIN cm_producto ON tb_pedidodet.idprod = cm_producto.id_cprod
                                                    LEFT JOIN lg_ordendet ON lg_ordendet.niddeta = tb_pedidodet.iditem
                                                    LEFT JOIN lg_ordencab ON lg_ordendet.id_orden = lg_ordencab.id_regmov
                                                    LEFT JOIN tb_proyectos ON tb_pedidodet.idcostos = tb_proyectos.nidreg
                                                    LEFT JOIN tb_area ON tb_pedidodet.idarea = tb_area.ncodarea
                                                    LEFT JOIN tb_partidas ON tb_pedidocab.idpartida = tb_partidas.idreg
                                                    LEFT JOIN tb_unimed ON tb_pedidodet.unid = tb_unimed.ncodmed
                                                    LEFT JOIN cm_entidad ON lg_ordencab.id_centi = cm_entidad.id_centi
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
                                                    LEFT JOIN tb_user AS asignacion ON tb_pedidodet.idasigna = asignacion.iduser
                                                    LEFT JOIN lg_guias AS sunat ON sunat.id_regalm = alm_despachocab.id_regalm
                                                    LEFT JOIN alm_madresdet ON alm_madresdet.nropedido = tb_pedidodet.iditem
                                                    LEFT JOIN alm_madrescab ON alm_madrescab.id_regalm = alm_madresdet.id_regalm
                                                    LEFT JOIN lg_guias AS embarca ON alm_madrescab.cnumguia = embarca.cnumguia
                                                WHERE
                                                    tb_pedidodet.nflgActivo 
                                                    AND tb_costusu.nflgactivo = 1 
                                                    AND tb_costusu.id_cuser = :usr 
                                                    AND NOT ISNULL( tb_pedidocab.nrodoc )
                                                    AND tb_pedidocab.nrodoc LIKE :pedido
                                                    AND ISNULL( lg_ordendet.nflgactivo ) 
                                                    AND IFNULL( lg_ordencab.cnumero, '' ) LIKE :orden 
                                                    AND tb_proyectos.nidreg LIKE :costo
                                                    AND tb_pedidocab.idtipomov LIKE :tipo 
                                                    AND cm_producto.ccodprod LIKE :codigo
                                                    AND tb_pedidocab.concepto LIKE :concepto
                                                    AND tb_pedidodet.estadoItem LIKE :estado
                                                    AND CONCAT_WS( ' ', cm_producto.cdesprod, tb_pedidodet.observaciones ) LIKE :descripcion 
                                                    AND tb_pedidocab.anio >= YEAR (NOW()) - 2
                                                    AND IFNULL(lg_ordencab.cper, '') LIKE :anioOrden
                                                    AND tb_pedidocab.anio LIKE :anioPedido
                                                GROUP BY
                                                    tb_pedidodet.iditem 
                                                ORDER BY
                                                    tb_pedidocab.emision DESC");
                                                                                                    
                $sql->execute(["orden"          =>$orden,
                               "pedido"         =>$pedido,
                               "costo"          =>$costo,
                               "codigo"         =>$codigo,
                               "concepto"       =>$concepto,
                               "tipo"           =>$tipo,
                               "estado"         =>$estadoItem,
                               "descripcion"    =>$descrip,
                               "usr"            =>$userID,
                               "anioOrden"      =>$anio,
                               "anioPedido"     =>$anio]);
                
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

                    $counter = 1;

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
                                $aprobado_final = $rs['cantidad_pedido'] - $rs['cantidad_atendida'];
                            } 

                            if ( $aprobado_final < 0){
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
                            }else if( $rs['estadoItem'] == 299 ) {
                                $porcentaje = "95%";
                                $estadofila = "embarque";
                                $estado_item = "embarque";
                                $estado_pedido = "embarque";
                            }else if( $rs['estadoItem'] == 54) {
                                if ($rs['cantidad_pedido'] == $rs['cantidad_atendida']){
                                    $porcentaje = "12%";
                                    $estadofila = "consulta";
                                    $estado_item = "emitido";
                                    $estado_pedido = "emitido";
                                }else{
                                    $porcentaje = "15%";
                                    $estadofila = "item_aprobado";
                                    $estado_item = "emitido";
                                    $estado_pedido = "emitido";
                                }
                            }else if( $rs['estadoItem'] == 52 && round($rs['ingreso_obra'],2) == round($rs['cantidad_pedido'],2) ) {
                                $porcentaje = "100%";
                                $estadofila = "entregado";
                                $estado_item = "atendido";
                                $estado_pedido = "atendido";
                            }else if( $rs['estadoItem'] == 52  && round($rs['ingreso_obra'],2) == round($rs['cantidad_pedido'],2) && $rs['cantidad_aprobada'] > 0) {
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
                            }else if ( round($rs['ingreso_obra'],2) < round($rs['cantidad_pedido'],2 )) {
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
                                        <td class="textoCentro">'.$counter++.'</td>
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
                                        <td class="textoCentro">'.$rs['guiasunat'].'</td>
                                        <td class="textoCentro">'.$rs['salida_lurin'].'</td>
                                        <td class="textoCentro">'.$rs['nota_transferencia'].'</td>
                                        <td class="textoCentro">'.$rs['fecha_traslado'].'</td>
                                        <td class="textoCentro">'.$rs['nota_obra'].'</td>
                                        <td class="textoCentro">'.$rs['fecha_ingreso_almacen_obra'].'</td>
                                        <td class="textoDerecha">'.number_format($rs['ingreso_obra'],2).'</td>
                                        <td class="pl10px">'.$rs['fechaEmbarca'].'</td>
                                        <td class="pl10px">'.$rs['nombreEmbarca'].'</td>
                                        <td class="pl10px">'.$rs['tracking'].'</td>
                                        <td class="pl10px">'.$rs['trackinglurin'].'</td>
                                        <td class="textoCentro">'.$rs['operador'].'</td>
                                        <td class="textoCentro">'.$transporte.'</td>
                                        <td class="pl10px">'.$rs['asigna'].'</td>
                                        <td class="pl10px">'.$rs['fecha_descarga'].'</td>
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
    }
?>