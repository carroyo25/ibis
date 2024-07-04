<?php
    class CargoPlanModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarCargoPlanPrecio($parametros){
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
                                                        tb_pedidodet.cant_pedida AS cantidad_pedido,
                                                        tb_pedidodet.cant_atend AS cantidad_atendida,
                                                        tb_pedidodet.cant_aprob AS cantidad_aprobada,
                                                        LPAD( tb_pedidocab.nrodoc, 6, 0 ) AS pedido,
                                                        lg_ordendet.id_orden AS orden,
                                                        cm_producto.ccodprod,
                                                        UPPER( CONCAT_WS( ' ', cm_producto.cdesprod, tb_pedidodet.observaciones ) ) AS descripcion,
                                                        tb_pedidodet.estadoItem,
                                                        tb_proyectos.ccodproy,
                                                        tb_proyectos.nidreg AS idproyecto,
                                                        UPPER( tb_area.cdesarea ) AS area,
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
                                                        lg_ordencab.ncodcos,
                                                        LPAD( lg_ordencab.cnumero, 4, 0 ) AS cnumero,
                                                        UPPER( cm_entidad.crazonsoc ) AS proveedor,
                                                        ( SELECT SUM( lg_ordendet.ncanti ) FROM lg_ordendet WHERE lg_ordendet.niddeta = tb_pedidodet.iditem AND lg_ordendet.id_orden != 0 ) AS cantidad_orden,
                                                        UPPER( tb_user.cnameuser ) AS operador,
                                                        UPPER( tb_pedidocab.concepto ) AS concepto,
                                                        DATEDIFF( lg_ordencab.ffechaent, NOW() ) AS dias_atraso,
                                                        usuarios.cnombres AS usuario,
                                                        DATE_ADD( lg_ordencab.ffechades, INTERVAL lg_ordencab.nplazo DAY ) AS fecha_entrega_final_anterior,
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
                                                        monedas.cabrevia,
                                                        lg_ordencab.ntcambio,
                                                    FORMAT(IF
                                                        ( lg_ordencab.ncodmon = 20, lg_ordendet.nunitario, NULL ),4) AS precio_soles,
                                                    IF
                                                        ( lg_ordencab.ncodmon = 21, lg_ordendet.nunitario, NULL ) AS precio_dolares,
                                                        lg_ordencab.cReferencia,
                                                        pagos.cdescripcion AS tipo_pago,
                                                        UPPER( familias.cdescrip ) AS familia,
                                                        LPAD(alm_recepcab.nnronota,6,0) AS nota_ingreso,
                                                        DATE_FORMAT( alm_recepcab.ffecdoc, '%d/%m/%Y' ) AS fecha_recepcion_proveedor,
                                                        ( SELECT SUM( alm_recepdet.ncantidad ) FROM alm_recepdet WHERE alm_recepdet.niddetaPed = tb_pedidodet.iditem AND alm_recepdet.nflgactivo = 1 ) AS ingreso,
                                                        ( SELECT SUM( alm_despachodet.ndespacho ) FROM alm_despachodet WHERE alm_despachodet.niddetaPed = lg_ordendet.niddeta AND alm_despachodet.nflgactivo = 1 ) AS despachos,
                                                        ( SELECT SUM( alm_existencia.cant_ingr ) FROM alm_existencia WHERE alm_existencia.idpedido = tb_pedidodet.iditem AND alm_existencia.nflgActivo = 1 ) AS ingreso_obra,
                                                        ( SELECT SUM( alm_existencia.cant_ingr ) FROM alm_existencia WHERE alm_existencia.idpedido = tb_pedidodet.iditem AND alm_existencia.nflgActivo = 1 ) AS atencion_almacen,
                                                        lg_ordendet.ntotal
                                                    FROM
                                                        tb_pedidodet
                                                        LEFT JOIN tb_pedidocab ON tb_pedidodet.idpedido = tb_pedidocab.idreg
                                                        LEFT JOIN lg_ordendet ON lg_ordendet.niddeta = tb_pedidodet.iditem
                                                        LEFT JOIN cm_producto ON tb_pedidodet.idprod = cm_producto.id_cprod
                                                        LEFT JOIN tb_proyectos ON tb_pedidodet.idcostos = tb_proyectos.nidreg
                                                        LEFT JOIN tb_area ON tb_pedidodet.idarea = tb_area.ncodarea
                                                        LEFT JOIN tb_unimed ON tb_pedidodet.unid = tb_unimed.ncodmed
                                                        LEFT JOIN lg_ordencab ON lg_ordendet.id_orden = lg_ordencab.id_regmov
                                                        LEFT JOIN cm_entidad ON lg_ordencab.id_centi = cm_entidad.id_centi
                                                        LEFT JOIN tb_user ON lg_ordencab.id_cuser = tb_user.iduser
                                                        LEFT JOIN tb_user AS usuarios ON tb_pedidocab.usuario = usuarios.iduser
                                                        LEFT JOIN tb_parametros AS monedas ON lg_ordencab.ncodmon = monedas.nidreg
                                                        LEFT JOIN tb_parametros AS pagos ON lg_ordencab.ncodpago = pagos.nidreg
                                                        LEFT JOIN tb_familia AS familias ON familias.ncodfamilia = cm_producto.nfam
                                                        LEFT JOIN alm_despachodet ON tb_pedidodet.iditem = alm_despachodet.niddetaPed
                                                        LEFT JOIN alm_recepdet ON tb_pedidodet.iditem = alm_recepdet.niddetaPed
                                                        LEFT JOIN alm_existencia ON tb_pedidodet.iditem = alm_existencia.idpedido
                                                        LEFT JOIN alm_recepcab ON alm_recepdet.id_regalm = alm_recepcab.id_regalm 
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


                            $atencion = $rs['atencion'] == 47 ? "NORMAL" : "URGENTE"; 
                           
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
                            }else if ( $rs['ingreso_obra'] && round($suma_atendido,2) === round($rs['cantidad_aprobada'],2)) {
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

                            $aprobado=0;

                            $aprobado = $rs['cantidad_aprobada'] == 0 ? $rs['cantidad_pedido']:$rs['cantidad_aprobada'];
                            $aprobado_final = $aprobado-$rs['cantidad_atendida'] < 0 ? 0 : $aprobado-$rs['cantidad_atendida'];

                            $salida.='<tr class="pointer" 
                                            data-itempedido="'.$rs['iditem'].'" 
                                            data-pedido="'.$rs['idpedido'].'" 
                                            data-orden="'.$rs['orden'].'"
                                            data-estado="'.$rs['estadoItem'].'"
                                            data-producto="'.$rs['idprod'].'"
                                            data-porcentaje="'.$porcentaje.'">
                                        <td class="textoCentro">'.str_pad($item++,3,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro '.$estadofila.'">'.$porcentaje.'</td>
                                        <td class="textoDerecha pr15px">'.$rs['ccodproy'].'</td>
                                        <td class="pl20px">'.$rs['area'].'</td>
                                        <td class="textoCentro '.$clase_operacion.'">'.$tipo_orden.'</td>
                                        <td class="textoDerecha">'.number_format($cantidad,2).'</td>
                                        <td class="textoDerecha">'.number_format($aprobado,2).'</td>
                                        <td class="textoCentro">'.number_format($aprobado_final,2).'</td>
                                        <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                        <td class="textoCentro">'.$rs['unidad'].'</td>
                                        <td class="pl10px">'.$rs['descripcion'].'</td>
                                        <td class="textoCentro '.$clase_operacion.'">'.$tipo_orden.'</td>
                                        <td class="textoCentro">'.$rs['anio_orden'].'</td>
                                        <td class="textoCentro">'.$rs['cnumero'].'</td>
                                        <td class="textoCentro">'.$rs['fecha_orden'].'</td>
                                        <td class="textoDerecha pr15px" style="background:#e8e8e8;font-weight: bold">'.$rs['cantidad_orden'].'</td>
                                        <td class="pl10px">'.$rs['proveedor'].'</td>
                                        <td class="textoCentro">'.$fecha_entrega.'</td>
                                        <td class="textoDerecha pr15px">'.$rs['ingreso'].'</td>
                                        <td class="textoCentro">'.$rs['nota_ingreso'].'</td>
                                        <td class="textoCentro">'.$rs['fecha_recepcion_proveedor'].'</td>

                                        <td class="textoDerecha pr15px">'.$saldoRecibir.'</td>
                                        <td class="textoDerecha pr15px">'.$rs['plazo'].'</td>
                                        <td class="textoDerecha pr15px">'.$dias_atraso.'</td>
                                        <td class="textoCentro '.$estadoSemaforo.'">'.$semaforo.'</td>
                                        <td class="textoCentro">'.$rs['operador'].'</td>
                                        <td class="pl10px">'.$rs['concepto'].'</td>

                                        <td class="pl10px">'.$rs['cabrevia'].'</td>
                                        <td class="textoDerecha">'.$rs['ntcambio'].'</td>
                                        <td class="textoDerecha">'.$rs['precio_dolares'].'</td>
                                        <td class="textoDerecha">'.$rs['precio_soles'].'</td>
                                        <td class="textoDerecha">'.$rs['ntotal'].'</td>
                                        <td class="pl10px">'.$rs['tipo_pago'].'</td>
                                        <td class="pl10px">'.$rs['cReferencia'].'</td>
                                        <td class="pl10px">'.$rs['familia'].'</td>

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

        public function crearExcelPrecio(){
            try {
                $salida = "";
                $docData = [];

                $sql = $this->db->connect()->query("SELECT
                                                        tb_pedidodet.iditem,
                                                        tb_pedidodet.idpedido,
                                                        tb_pedidodet.idprod,
                                                        tb_pedidodet.cant_pedida AS cantidad_pedido,
                                                        tb_pedidodet.cant_atend AS cantidad_atendida,
                                                        tb_pedidodet.cant_aprob AS cantidad_aprobada,
                                                        LPAD( tb_pedidocab.nrodoc, 6, 0 ) AS pedido,
                                                        lg_ordendet.id_orden AS orden,
                                                        cm_producto.ccodprod,
                                                        UPPER( CONCAT_WS( ' ', cm_producto.cdesprod, tb_pedidodet.observaciones ) ) AS descripcion,
                                                        tb_pedidodet.estadoItem,
                                                        tb_proyectos.ccodproy,
                                                        tb_proyectos.nidreg AS idproyecto,
                                                        UPPER( tb_area.cdesarea ) AS area,
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
                                                        lg_ordencab.ncodcos,
                                                        LPAD( lg_ordencab.cnumero, 4, 0 ) AS cnumero,
                                                        UPPER( cm_entidad.crazonsoc ) AS proveedor,
                                                        ( SELECT SUM( lg_ordendet.ncanti ) FROM lg_ordendet WHERE lg_ordendet.niddeta = tb_pedidodet.iditem AND lg_ordendet.id_orden != 0 ) AS cantidad_orden,
                                                        UPPER( tb_user.cnameuser ) AS operador,
                                                        UPPER( tb_pedidocab.concepto ) AS concepto,
                                                        DATEDIFF( lg_ordencab.ffechaent, NOW() ) AS dias_atraso,
                                                        usuarios.cnombres AS usuario,
                                                        DATE_ADD( lg_ordencab.ffechades, INTERVAL lg_ordencab.nplazo DAY ) AS fecha_entrega_final_anterior,
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
                                                        monedas.cabrevia,
                                                        lg_ordencab.ntcambio,
                                                    FORMAT(IF
                                                        ( lg_ordencab.ncodmon = 20, lg_ordendet.nunitario, NULL ),4) AS precio_soles,
                                                    IF
                                                        ( lg_ordencab.ncodmon = 21, lg_ordendet.nunitario, NULL ) AS precio_dolares,
                                                        lg_ordencab.cReferencia,
                                                        pagos.cdescripcion AS tipo_pago,
                                                        UPPER( familias.cdescrip ) AS familia,
                                                        LPAD(alm_recepcab.nnronota,6,0) AS nota_ingreso,
                                                        DATE_FORMAT( alm_recepcab.ffecdoc, '%d/%m/%Y' ) AS fecha_recepcion_proveedor,
                                                        ( SELECT SUM( alm_recepdet.ncantidad ) FROM alm_recepdet WHERE alm_recepdet.niddetaPed = tb_pedidodet.iditem AND alm_recepdet.nflgactivo = 1 ) AS ingreso,
                                                        ( SELECT SUM( alm_despachodet.ndespacho ) FROM alm_despachodet WHERE alm_despachodet.niddetaPed = lg_ordendet.niddeta AND alm_despachodet.nflgactivo = 1 ) AS despachos,
                                                        ( SELECT SUM( alm_existencia.cant_ingr ) FROM alm_existencia WHERE alm_existencia.idpedido = tb_pedidodet.iditem AND alm_existencia.nflgActivo = 1 ) AS ingreso_obra,
                                                        ( SELECT SUM( alm_existencia.cant_ingr ) FROM alm_existencia WHERE alm_existencia.idpedido = tb_pedidodet.iditem AND alm_existencia.nflgActivo = 1 ) AS atencion_almacen,
                                                        lg_ordendet.ntotal
                                                    FROM
                                                        tb_pedidodet
                                                        LEFT JOIN tb_pedidocab ON tb_pedidodet.idpedido = tb_pedidocab.idreg
                                                        LEFT JOIN lg_ordendet ON lg_ordendet.niddeta = tb_pedidodet.iditem
                                                        LEFT JOIN cm_producto ON tb_pedidodet.idprod = cm_producto.id_cprod
                                                        LEFT JOIN tb_proyectos ON tb_pedidodet.idcostos = tb_proyectos.nidreg
                                                        LEFT JOIN tb_area ON tb_pedidodet.idarea = tb_area.ncodarea
                                                        LEFT JOIN tb_unimed ON tb_pedidodet.unid = tb_unimed.ncodmed
                                                        LEFT JOIN lg_ordencab ON lg_ordendet.id_orden = lg_ordencab.id_regmov
                                                        LEFT JOIN cm_entidad ON lg_ordencab.id_centi = cm_entidad.id_centi
                                                        LEFT JOIN tb_user ON lg_ordencab.id_cuser = tb_user.iduser
                                                        LEFT JOIN tb_user AS usuarios ON tb_pedidocab.usuario = usuarios.iduser
                                                        LEFT JOIN tb_parametros AS monedas ON lg_ordencab.ncodmon = monedas.nidreg
                                                        LEFT JOIN tb_parametros AS pagos ON lg_ordencab.ncodpago = pagos.nidreg
                                                        LEFT JOIN tb_familia AS familias ON familias.ncodfamilia = cm_producto.nfam
                                                        LEFT JOIN alm_despachodet ON tb_pedidodet.iditem = alm_despachodet.niddetaPed
                                                        LEFT JOIN alm_recepdet ON tb_pedidodet.iditem = alm_recepdet.niddetaPed
                                                        LEFT JOIN alm_existencia ON tb_pedidodet.iditem = alm_existencia.idpedido
                                                        LEFT JOIN alm_recepcab ON alm_recepdet.id_regalm = alm_recepcab.id_regalm 
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
                                                        tb_pedidodet.iditem 
                                                    ORDER BY
                                                        tb_pedidocab.emision DESC");
                $sql->execute();
                $rowCount = $sql->rowCount();

                while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                    $docData[] = $row;
                }

                $this->crearExcelExport($docData);
                $archivo = 'public/documentos/reportes/cargoplanprecio.xlsx';

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function crearExcelExport($datos){
            require_once('public/PHPExcel/PHPExcel.php');

            $objPHPExcel = new PHPExcel();
                $objPHPExcel->getProperties()
                    ->setCreator("Sical")
                    ->setLastModifiedBy("Sical")
                    ->setTitle("Cargo Plan")
                    ->setSubject("Template excel")
                    ->setDescription("Cargo Plan Valorizado")
                    ->setKeywords("Template excel");

                $cuerpo = array(
                    'font'  => array(
                    'bold'  => false,
                    'size'  => 7,
                ));

                $objWorkSheet = $objPHPExcel->createSheet(1);

                $objPHPExcel->setActiveSheetIndex(0);
                $objPHPExcel->getActiveSheet()->setTitle("Cargo Plan Valorizado");

                $objPHPExcel->getActiveSheet()->mergeCells('A1:AW1');
                $objPHPExcel->getActiveSheet()->setCellValue('A1','CARGO PLAN VALORIZADO');

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
                $objPHPExcel->getActiveSheet()->setCellValue('E2','Tipo'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('F2','Cantidad Pedida'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('G2','Cantidad Aprobada'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('H2','Cantidad Compra'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('I2','Codigo del Bien/Servicio'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('J2','Unidad Medida'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('K2','Descripcion del Bien/Servicio'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('L2','Tipo Orden'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('N2','Año Orden'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('M2','Nro Orden'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('O2','Fecha Orden'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('P2','Cantidad Orden'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('Q2','Descripcion del proveedor'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('R2','Fecha Entrega Proveedor'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('S2','Cant. Recibida'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('T2','Nota de Ingreso'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('U2','Fecha Recepcion Proveedor'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('V2','Saldo por Recibir'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('W2','Dias Entrega'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('X2','Días Atrazo'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('Y2','Semáforo'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('Z2','Operador Logístico'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AA2','Observaciones/Concepto'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AB2','Tipo Moneda'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AC2','Tipo Cambio'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AE2','Precio Dolares'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AF2','Precio Soles'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AG2','Importe Total'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AH2','Forma Pago'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AI2','Referencia Pago'); // esto cambia


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

                /*forEach($datos AS $dato){

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

                    $cantidad_compra = $dato['cantidad_aprobada'] - $dato['cantidad_atendida'] < 0 ? 0 : $dato['cantidad_aprobada'] - $dato['cantidad_atendida'];
                    $cantidad_aprobada = $dato['cantidad_aprobada'] == 0 ? $cantidad : $dato['cantidad_aprobada'];

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
                    $objPHPExcel->getActiveSheet()->setCellValue('M'.$fila,$cantidad_aprobada);
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

                    $objPHPExcel->getActiveSheet()->setCellValue('AK'.$fila,$dato['guia_transferencia']);
                    $objPHPExcel->getActiveSheet()->setCellValue('AL'.$fila,$dato['fecha_traslado']);
                        
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
                }*/

                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');
                $objWriter->save('public/documentos/reportes/cargoplan.xlsx');
        }
    }
?>