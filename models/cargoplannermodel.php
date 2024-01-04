<?php
    class CargoPlannerModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarCargoPlan($parametros){
            try {

                $salida = "Buscar el pedido";

                $tipo       = $parametros['tipoSearch'] == -1 ? "%" : $parametros['tipoSearch'];
                $costo      = $parametros['costosSearch'] == -1 ? "%" : $parametros['costosSearch'];
                $descrip    = $parametros['descripSearch'] == "" ? "%" : "%".$parametros['descripSearch']."%";
                $codigo     = $parametros['codigoSearch'] == "" ? "%" : "%".$parametros['codigoSearch']."%";
                $orden      = $parametros['ordenSearch'] == "" ? "%" : $parametros['ordenSearch'];
                $pedido     = $parametros['pedidoSearch'] == "" ? "%" : $parametros['pedidoSearch'];
                $concepto   = $parametros['conceptoSearch'] == "" ? "%" : "%".$parametros['conceptoSearch']."%";
                $estadoItem = $parametros['estado_item'] == "" ? "%" : $parametros['estado_item'];
                $anio       = $parametros['anioSearch'] == "" ? "%" : $parametros['anioSearch'];
                
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
                                                        DATE_ADD( lg_ordencab.ffechades, INTERVAL lg_ordencab.nplazo DAY ) AS fecha_entrega_final
                                                    FROM
                                                        tb_pedidodet
                                                        INNER JOIN tb_pedidocab ON tb_pedidodet.idpedido = tb_pedidocab.idreg
                                                        LEFT JOIN lg_ordendet ON lg_ordendet.niddeta = tb_pedidodet.iditem
                                                        INNER JOIN cm_producto ON tb_pedidodet.idprod = cm_producto.id_cprod
                                                        INNER JOIN tb_proyectos ON tb_pedidodet.idcostos = tb_proyectos.nidreg
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
                                            WHERE
                                                tb_pedidodet.nflgActivo
                                                AND ISNULL( lg_ordendet.nflgactivo ) 
                                                AND tb_pedidocab.nrodoc LIKE :pedido 
                                                AND IFNULL( lg_ordencab.cnumero, '' ) LIKE :orden
                                                AND tb_proyectos.nidreg LIKE :costo
                                                AND tb_pedidocab.idtipomov LIKE :tipo
                                                AND tb_pedidocab.anio LIKE :anio
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
                               "descripcion"    =>$descrip,
                               "anio"           =>$anio]);
                
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
                            }else if( $rs['estadoItem'] == 52  && $rs['ingreso_obra'] == $rs['cantidad_pedido'] ) {
                                $porcentaje = "100%";
                                $estadofila = "entregado";
                                $estado_item = "atendido";
                                $estado_pedido = "atendido";
                            }else if( $rs['estadoItem'] == 52  && $rs['ingreso_obra'] == $rs['cantidad_aprobada']) {
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
                            }else  if( $rs['ingreso'] && $rs['ingreso'] < $rs['cantidad_orden']) {
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

                            $cantidad = $rs['cantidad_aprobada'] == 0 ? $rs['cantidad_pedido'] : $rs['cantidad_aprobada'];

                            $fecha_entrega = "";
                            $fecha_descarga = "";
                            $dias_plazo = intVal( $rs['plazo'] )+1 .' days';

                            if ( $rs['fecha_autorizacion_orden'] !== null && $rs['estadoItem'] !== 105 ) { 
                                //$fecha_entrega = date("d/m/Y",strtotime($rs['FechaFin'].$dias_plazo));
                                $fecha_descarga = date("d/m/Y",strtotime($rs['FechaFin'].' 1 days'));
                                $fecha_entrega = $rs['fecha_entrega'];
                            }

                            if ( $rs['estadoItem'] !== 105 ) {
                                

                                if  ($fecha_entrega !== ''){
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
                                        data-porcentaje="'.$porcentaje.'">
                                        <td class="textoCentro">'.str_pad($item++,3,0,STR_PAD_LEFT).'</td>
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
                                        <td class="textoDerecha">'.number_format($cantidad,2).'</td>
                                        <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                        <td class="textoCentro">'.$rs['unidad'].'</td>
                                        <td class="pl10px">'.$rs['descripcion'].'</td>
                                        <td class="textoCentro '.$clase_operacion.'">'.$tipo_orden.'</td>
                                        <td class="textoCentro">'.$rs['anio_orden'].'</td>
                                        <td class="textoCentro">'.$rs['cnumero'].'</td>
                                        <td class="textoCentro">'.$rs['fecha_orden'].'</td>
                                        <td class="textoDerecha pr15px" style="background:#e8e8e8;font-weight: bold">'.$rs['cantidad_orden'].'</td>
                                        <td class="pl10px">'.$rs['item_orden'].'</td>
                                        <td class="pl10px">'.$rs['fecha_autorizacion_orden'].'</td>
                                        <td class="textoDerecha pr15px">'.number_format($rs['cantidad_atendida'],2).'</td>
                                        <td class="pl10px">'.$rs['proveedor'].'</td>
                                        <td class="textoCentro">'.$fecha_descarga.'</td>
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

                $objPHPExcel->getActiveSheet()->mergeCells('A1:AP1');
                $objPHPExcel->getActiveSheet()->setCellValue('A1','CARGO PLAN');

                $objPHPExcel->getActiveSheet()->getStyle('A1:AP2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('A1:AP2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(60);

                $objPHPExcel->getActiveSheet()->getColumnDimension("D")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("E")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("K")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("L")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("M")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("O")->setWidth(90);
                $objPHPExcel->getActiveSheet()->getColumnDimension("T")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("U")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("V")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("W")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("Ab")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("AG")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("AJ")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("AK")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("AL")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("AM")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("AN")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("AO")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("AP")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("AQ")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("AR")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("AS")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("AT")->setAutoSize(true);

                $objPHPExcel->getActiveSheet()
                            ->getStyle('A2:P2')
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
                            ->getStyle('AE2:AT2')
                            ->getFill()
                            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB('FFFF00');

                

                $objPHPExcel->getActiveSheet()->getStyle('A1:AT2')->getAlignment()->setWrapText(true);

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
                $objPHPExcel->getActiveSheet()->setCellValue('M2','Codigo del Bien/Servicio'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('N2','Unidad Medida'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('O2','Descripcion del Bien/Servicio'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('P2','Tipo Orden'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('Q2','Año Orden'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('R2','Nro Orden'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('S2','Fecha Orden'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('T2','Cantidad Orden'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('U2','Item Orden'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('V2','Fecha Autorizacion'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('W2','Atencion Almacen'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('X2','Descripcion del proveedor'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('Y2','Fecha Envio Proveedor'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('Z2','Fecha Entrega Proveedor'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AA2','Cant. Recibida'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AB2','Nota de Ingreso'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AC2','Fecha Recepcion Proveedor'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AD2','Saldo por Recibir'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AE2','Dias Entrega'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AF2','Días Atrazo'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AG2','Semáforo'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AH2','Cantidad Despachada'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AI2','Nro. Guia'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AJ2','Registro Almacen'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AK2','Fecha Ingreso Almacen'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AL2','Cantidad en Obra'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AM2','Estado Pedido'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AN2','Estado Item'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AO2','N° Parte'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AP2','Codigo Activo'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AQ2','Operador Logístico'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AR2','Tipo Transporte'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AS2','Observaciones/Concepto'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AT2','Solicitante'); // esto cambia
               
                $fila = 3;
                $datos = json_decode($registros);
                $nreg = count($datos);

                $objPHPExcel->getActiveSheet()->getStyle('S')->getNumberFormat()->setFormatCode('dd/mm/yyyy');
                $objPHPExcel->getActiveSheet()->getStyle('J')->getNumberFormat()->setFormatCode('dd/mm/yyyy');
                $objPHPExcel->getActiveSheet()->getStyle('K')->getNumberFormat()->setFormatCode('dd/mm/yyyy');
                $objPHPExcel->getActiveSheet()->getStyle('V')->getNumberFormat()->setFormatCode('dd/mm/yyyy');
                $objPHPExcel->getActiveSheet()->getStyle('Y')->getNumberFormat()->setFormatCode('dd/mm/yyyy');
                $objPHPExcel->getActiveSheet()->getStyle('Z')->getNumberFormat()->setFormatCode('dd/mm/yyyy');
                $objPHPExcel->getActiveSheet()->getStyle('AC')->getNumberFormat()->setFormatCode('dd/mm/yyyy');
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

                    $objPHPExcel->getActiveSheet()->setCellValue('L'.$fila,$datos[$i]->cantidad);
                    $objPHPExcel->getActiveSheet()->setCellValue('M'.$fila,$datos[$i]->codigo);
                    $objPHPExcel->getActiveSheet()->setCellValue('N'.$fila,$datos[$i]->unidad);
                    $objPHPExcel->getActiveSheet()->setCellValue('O'.$fila,$datos[$i]->descripcion);
                    $objPHPExcel->getActiveSheet()->setCellValue('P'.$fila,$datos[$i]->tipo_orden);
                    $objPHPExcel->getActiveSheet()->setCellValue('Q'.$fila,$datos[$i]->anio_orden);
                    $objPHPExcel->getActiveSheet()->setCellValue('R'.$fila,$datos[$i]->nro_orden);
                   
                    if  ($datos[$i]->fecha_orden !== "")
                        $objPHPExcel->getActiveSheet()->setCellValue('S'.$fila,PHPExcel_Shared_Date::PHPToExcel($datos[$i]->fecha_orden));
                        

                    $objPHPExcel->getActiveSheet()->setCellValue('T'.$fila,$datos[$i]->cantidad_orden);
                    $objPHPExcel->getActiveSheet()->setCellValue('U'.$fila,$datos[$i]->item_orden);

                    if  ($datos[$i]->autoriza_orden !== "")
                        $objPHPExcel->getActiveSheet()->setCellValue('V'.$fila,PHPExcel_Shared_Date::PHPToExcel($datos[$i]->autoriza_orden));
                    
                    $objPHPExcel->getActiveSheet()->setCellValue('X'.$fila,$datos[$i]->proveedor);

                    if  ( $datos[$i]->fecha_envio !== "" )
                        $objPHPExcel->getActiveSheet()->setCellValue('Y'.$fila,PHPExcel_Shared_Date::PHPToExcel($datos[$i]->fecha_envio));

                    if  ( $datos[$i]->fecha_entrega !== "" )
                        $objPHPExcel->getActiveSheet()->setCellValue('Z'.$fila,PHPExcel_Shared_Date::PHPToExcel($datos[$i]->fecha_entrega));

                    $objPHPExcel->getActiveSheet()->setCellValue('AA'.$fila,$datos[$i]->cantidad_recibida);
                    $objPHPExcel->getActiveSheet()->setCellValue('AB'.$fila,$datos[$i]->nota_ingreso);

                    if  ($datos[$i]->fecha_recepcion !== "")
                        $objPHPExcel->getActiveSheet()->setCellValue('AC'.$fila,PHPExcel_Shared_Date::PHPToExcel($datos[$i]->fecha_recepcion));
                    
                    $objPHPExcel->getActiveSheet()->setCellValue('AD'.$fila,$datos[$i]->saldo_recibir);
                    $objPHPExcel->getActiveSheet()->setCellValue('AE'.$fila,$datos[$i]->dias_entrega);
                    $objPHPExcel->getActiveSheet()->setCellValue('AF'.$fila,$datos[$i]->dias_atraso);
                    
                    $objPHPExcel->getActiveSheet()->setCellValue('AG'.$fila,$datos[$i]->semaforo);
                    $objPHPExcel->getActiveSheet()->getStyle('AG'.$fila)->applyFromArray($semaforo);
                    
                    $objPHPExcel->getActiveSheet()->setCellValue('AH'.$fila,$datos[$i]->despacho);
                    $objPHPExcel->getActiveSheet()->setCellValue('AI'.$fila,$datos[$i]->numero_guia);
                    $objPHPExcel->getActiveSheet()->setCellValue('AJ'.$fila,$datos[$i]->registro_almacen);

                    if  ($datos[$i]->fecha_registro_obra !== "")
                        $objPHPExcel->getActiveSheet()->setCellValue('AK'.$fila,PHPExcel_Shared_Date::PHPToExcel($datos[$i]->fecha_registro_obra));

                    $objPHPExcel->getActiveSheet()->setCellValue('AL'.$fila,$datos[$i]->cantidad_obra);

                    $objPHPExcel->getActiveSheet()->setCellValue('AM'.$fila,$datos[$i]->estado_pedido);
                    $objPHPExcel->getActiveSheet()->setCellValue('AN'.$fila,$datos[$i]->estado_item);
                    $objPHPExcel->getActiveSheet()->setCellValue('AO'.$fila,$datos[$i]->numero_parte);
                    $objPHPExcel->getActiveSheet()->setCellValue('AP'.$fila,$datos[$i]->codigo_activo);
                    $objPHPExcel->getActiveSheet()->setCellValue('AQ'.$fila,$datos[$i]->operador);
                    $objPHPExcel->getActiveSheet()->setCellValue('AR'.$fila,$datos[$i]->transporte);
                    $objPHPExcel->getActiveSheet()->setCellValue('AS'.$fila,$datos[$i]->observaciones);
                    $objPHPExcel->getActiveSheet()->setCellValue('AT'.$fila,$datos[$i]->solicitante);

                    $fila++;               
                }

                $objPHPExcel->getActiveSheet()->getStyle('A:C')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('A:C')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                
                $objPHPExcel->getActiveSheet()->getStyle('F:N')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('F:N')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $objPHPExcel->getActiveSheet()->getStyle('Q:T')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('Q:T')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $objPHPExcel->getActiveSheet()->getStyle('AA:AG')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('AA:AG')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $objPHPExcel->getActiveSheet()->getStyle('AI:AL')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('AI:AL')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $objPHPExcel->getActiveSheet()->getStyle('AP')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('AP')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

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
                        "despachos"=>$this->despachos($refpedido));
        }

        private function ordenes($orden) {
            try {
                $salida = "";
                $sql = $this->db->connect()->prepare("SELECT
                                                            lg_ordencab.id_regmov,
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
                                        <td class="textoCentro">'.$rs['id_regmov'].'</td>
                                        <td class="textoCentro">'.$rs['ffechadoc'].'</td>
                                        <td class="pl20px">'.$rs['crazonsoc'].'</td>
                                        <td class="pl20px">'.$rs['ccodproy'].'</td>
                                        <td class="textoCentro"><a href="'.$rs['id_regmov'].'"><i class="far fa-file-pdf"></i></a></td>
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
                                                        DATE_FORMAT( alm_recepcab.ffecdoc, '%d/%m,/%Y' ) AS ffecdoc,
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
                                        <td class="textoCentro"><a href="'.$rs['nnronota'].'"><i class="far fa-file-pdf"></i></a></td>
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
                                                        alm_despachocab.nnronota, 
                                                        alm_despachocab.ffecdoc, 
                                                        alm_despachocab.cnumguia, 
                                                        alm_despachocab.ffecenvio, 
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
                                        <td class="textoCentro"><a href="'.$rs['nnronota'].'"><i class="far fa-file-pdf"></i></a></td>
                                    </tr>';
                    }
                }

                return $salida;

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function exportarTotal(){
            require_once('public/PHPExcel/PHPExcel.php');
            try {
               $salida = "";

               /*$_SESSION['progreso'] = 0;
               session_write_close();*/

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
                                                FORMAT( lg_ordencab.nplazo, 0 ) AS plazo,
                                                DATE_FORMAT( lg_ordencab.ffechadoc, '%d/%m/%Y' ) AS fecha_orden,
                                                DATE_FORMAT( lg_ordencab.ffechaent, '%d/%m/%Y' ) AS fecha_entrega,
                                                DATE_FORMAT( lg_ordencab.fechafin, '%d/%m/%Y' ) AS fecha_autorizacion_orden,
                                                UPPER( cm_entidad.crazonsoc ) AS proveedor,
                                                UPPER( tb_user.cnameuser ) AS operador,
                                                UPPER( tb_pedidocab.concepto ) AS concepto,
                                                DATEDIFF(  lg_ordencab.ffechaent, NOW() ) AS dias_atraso,
                                                DATE_ADD( lg_ordencab.ffechades, INTERVAL lg_ordencab.nplazo DAY ) AS fecha_entrega_final,
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
                                                DATE_FORMAT( alm_cabexist.ffechadoc, '%d/%m/%Y' ) AS fecha_registro_almacen
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
                                                LEFT JOIN ( SELECT SUM( lg_ordendet.ncanti ) AS cantidad_orden, lg_ordendet.niddeta FROM lg_ordendet WHERE lg_ordendet.id_orden != 0 GROUP BY lg_ordendet.niddeta ) AS o ON   o.niddeta = tb_pedidodet.iditem
                                                LEFT JOIN ( SELECT SUM( alm_recepdet.ncantidad ) AS ingreso, alm_recepdet.niddetaPed FROM alm_recepdet WHERE alm_recepdet.nflgactivo = 1 GROUP BY alm_recepdet.niddetaPed ) AS i ON i.niddetaPed = tb_pedidodet.iditem
                                                LEFT JOIN ( SELECT SUM( alm_despachodet.ndespacho ) AS despachos, alm_despachodet.niddetaPed FROM alm_despachodet WHERE alm_despachodet.nflgactivo = 1 GROUP BY alm_despachodet.niddetaPed ) AS d ON  d.niddetaPed = tb_pedidodet.iditem
                                                LEFT JOIN ( SELECT SUM( alm_existencia.cant_ingr ) AS ingreso_obra, alm_existencia.idpedido FROM alm_existencia WHERE alm_existencia.nflgActivo = 1 GROUP BY alm_existencia.idpedido ) AS a ON a.idpedido= tb_pedidodet.iditem
                                                LEFT JOIN tb_user AS usuarios ON tb_pedidocab.usuario = usuarios.iduser 
                                            WHERE
                                                tb_pedidodet.nflgActivo 
                                                AND ISNULL( lg_ordendet.nflgactivo )
                                            GROUP BY
                                                tb_pedidodet.iditem");
                $sql->execute();
                $rowCount = $sql->rowCount();

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

                //$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');
                
                $objPHPExcel->getActiveSheet()->mergeCells('A1:AP1');
                $objPHPExcel->getActiveSheet()->setCellValue('A1','CARGO PLAN');

                $objPHPExcel->getActiveSheet()->getStyle('A1:AT2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('A1:AT2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(60);

                $objPHPExcel->getActiveSheet()->getColumnDimension("D")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("E")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("J")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("K")->setWidth(10);
                $objPHPExcel->getActiveSheet()->getColumnDimension("L")->setWidth(10);
                $objPHPExcel->getActiveSheet()->getColumnDimension("M")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("O")->setWidth(90);
                $objPHPExcel->getActiveSheet()->getColumnDimension("S")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("T")->setWidth(10);
                $objPHPExcel->getActiveSheet()->getColumnDimension("U")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("V")->setWidth(15);
                $objPHPExcel->getActiveSheet()->getColumnDimension("W")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("X")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("Y")->setWidth(9);
                $objPHPExcel->getActiveSheet()->getColumnDimension("Z")->setWidth(12);
                $objPHPExcel->getActiveSheet()->getColumnDimension("Y")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("AA")->setWidth(10);
                $objPHPExcel->getActiveSheet()->getColumnDimension("AB")->setWidth(10);
                $objPHPExcel->getActiveSheet()->getColumnDimension("AC")->setWidth(13);
                $objPHPExcel->getActiveSheet()->getColumnDimension("AE")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("AG")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("AH")->setWidth(12);
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


                $objPHPExcel->getActiveSheet()
                            ->getStyle('A2:P2')
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
                            ->getStyle('AE2:AT2')
                            ->getFill()
                            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB('FFFF00');

               

                $objPHPExcel->getActiveSheet()->getStyle('A1:AT2')->getAlignment()->setWrapText(true);

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
                $objPHPExcel->getActiveSheet()->setCellValue('M2','Codigo del Bien/Servicio'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('N2','Unidad Medida'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('O2','Descripcion del Bien/Servicio'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('P2','Tipo Orden'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('Q2','Año Orden'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('R2','Nro Orden'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('S2','Fecha Orden'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('T2','Cantidad Orden'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('U2','Item Orden'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('V2','Fecha Autorizacion'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('W2','Atencion Almacen'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('X2','Descripcion del proveedor'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('Y2','Fecha Envio Proveedor'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('Z2','Fecha Entrega Proveedor'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AA2','Cant. Recibida'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AB2','Nota de Ingreso'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AC2','Fecha Recepcion Proveedor'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AD2','Saldo por Recibir'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AE2','Dias Entrega'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AF2','Días Atrazo'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AG2','Semáforo'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AH2','Cantidad Despachada'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AI2','Nro. Guia'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AJ2','Registro Almacen'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AK2','Fecha Ingreso Almacen'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AL2','Cantidad en Obra'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AM2','Estado Pedido'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AN2','Estado Item'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AO2','N° Parte'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AP2','Codigo Activo'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AQ2','Operador Logístico'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AR2','Tipo Transporte'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AS2','Observaciones/Concepto'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AT2','Solicitante'); // esto cambia


                $objPHPExcel->getActiveSheet()->getStyle('B:C')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('B:C')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                
                $objPHPExcel->getActiveSheet()->getStyle('F:K')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('F:K')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $objPHPExcel->getActiveSheet()->getStyle('M:N')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('M:N')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $objPHPExcel->getActiveSheet()->getStyle('P:S')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('P:S')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $objPHPExcel->getActiveSheet()->getStyle('V')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('V')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $objPHPExcel->getActiveSheet()->getStyle('Q:S')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('Q:S')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $objPHPExcel->getActiveSheet()->getStyle('Y')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('Y')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $objPHPExcel->getActiveSheet()->getStyle('AA')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('AA')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $objPHPExcel->getActiveSheet()->getStyle('AE')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('AE')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $objPHPExcel->getActiveSheet()->getStyle('AE')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('AE')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $objPHPExcel->getActiveSheet()->getStyle('AG:AH')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('AG:AH')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $objPHPExcel->getActiveSheet()->getStyle('AJ:AO')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('AJ:AO')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $objPHPExcel->getActiveSheet()->getStyle('J')->getNumberFormat()->setFormatCode('dd/mm/yyyy');
                $objPHPExcel->getActiveSheet()->getStyle('L')->getNumberFormat()->setFormatCode('#,##0.00');
                $objPHPExcel->getActiveSheet()->getStyle('S')->getNumberFormat()->setFormatCode('dd/mm/yyyy');
                $objPHPExcel->getActiveSheet()->getStyle('T')->getNumberFormat()->setFormatCode('#,##0.00');
                $objPHPExcel->getActiveSheet()->getStyle('V')->getNumberFormat()->setFormatCode('dd/mm/yyyy');
                $objPHPExcel->getActiveSheet()->getStyle('W')->getNumberFormat()->setFormatCode('#,##0.00');
                $objPHPExcel->getActiveSheet()->getStyle('Y')->getNumberFormat()->setFormatCode('dd/mm/yyyy');
                $objPHPExcel->getActiveSheet()->getStyle('Z')->getNumberFormat()->setFormatCode('dd/mm/yyyy');

                $objPHPExcel->getActiveSheet()->getStyle('AA')->getNumberFormat()->setFormatCode('#,##0.00');
                $objPHPExcel->getActiveSheet()->getStyle('AC')->getNumberFormat()->setFormatCode('dd/mm/yyyy');
                $objPHPExcel->getActiveSheet()->getStyle('AD')->getNumberFormat()->setFormatCode('#,##0.00');
                $objPHPExcel->getActiveSheet()->getStyle('AH')->getNumberFormat()->setFormatCode('#,##0.00');
                $objPHPExcel->getActiveSheet()->getStyle('AK')->getNumberFormat()->setFormatCode('dd/mm/yyyy');
                $objPHPExcel->getActiveSheet()->getStyle('AL')->getNumberFormat()->setFormatCode('#,##0.00');
               
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
                
                if($rowCount > 0) {
                    while($rs = $sql->fetch()){

                        /*$_SESSION['progreso']+=1;
                        session_write_close();*/

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
                        $dias_atraso  =  $saldoRecibir > 0 && $rs['dias_atraso'] < 1 ? $rs['dias_atraso'] : "-" ;
                        $suma_atendido = number_format($rs['cantidad_orden'] + $rs['cantidad_atendida'],2);

                        $cantidad = $rs['cantidad_aprobada'] == 0 ? $rs['cantidad_pedido'] : $rs['cantidad_aprobada'];

                        $estado_pedido =  $rs['estadoItem'] >= 54 ? "Atendido":"Pendiente";
                        $estado_item   =  $rs['estadoItem'] >= 54 ? "Atendido":"Pendiente";

                        $transporte = $rs['nidreg'] == 39 ? "TERRESTRE": $rs['transporte'];
                        $atencion = $rs['atencion'] == 47 ? "NORMAL" : "URGENTE"; 

                        $color_mostrar  = 'FFFFFF';
                        $color_semaforo = 'FFFFFF';
                        $porcentaje = '';

                        $fecha_entrega = "";
                        $fecha_descarga = "";
                        $dias_plazo = intVal( $rs['plazo'] )+1 .' days';

                        if ( $rs['fecha_autorizacion_orden'] !== null && $rs['estadoItem'] !== 105 ) { 
                            //$fecha_entrega = date("d/m/Y",strtotime($rs['FechaFin'].$dias_plazo));
                            $fecha_descarga = date("d/m/Y",strtotime($rs['FechaFin'].' 1 days'));
                            $fecha_entrega = $rs['fecha_entrega'];
                        }


                        if ( $rs['estadoItem'] !== 105 ) {

                            if  ($fecha_entrega !== ''){
                                $dias_atraso  =  $rs['dias_atraso'];

                                if ( $rs['ingreso_obra'] == $rs['cantidad_orden'] ){
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
                                    $dias_atraso  =  $rs['dias_atraso']*-1;
                                } 
                            }else {
                                $dias_atraso  =  "";
                                $semaforoEstado = "Procesando";
                                $color_semaforo = "FFFF00";

                                if ( $rs['ingreso_obra'] > 0 && $rs['ingreso_obra'] === $rs['cantidad_atendida'] ){
                                    $semaforoEstado = "Entregado";
                                    $color_semaforo = '90EE90';
                                    $dias_atraso  = "";
                                }else if ( $rs['cantidad_atendida'] > 0) {
                                    $semaforoEstado = "Stock";
                                    $color_semaforo = "9068B0";
                                    $dias_atraso  = "";
                                }
                            }
                        }else {
                            $color_semaforo = 'CDCDCD';
                            $semaforoEstado = "Anulado";
                        }

                        
                        if ( $rs['estadoItem'] === 105 ) {
                            $porcentaje = "0%";
                            $estadofila = "anulado";
                            $estado_item = "anulado";
                            $estado_pedido = "anulado";
                            $color_mostrar = 'C8C8C8';
                        }else if( $rs['estadoItem'] === 49 ) {
                            $porcentaje = "10%";
                            $estadofila = "procesando";
                            $estado_item = "procesando";
                            $estado_pedido = "procesando";
                            $color_mostrar = 'F8CAAD';
                        }else if( $rs['estadoItem'] === 53 ) {
                            $porcentaje = "10%";
                            $estadofila = "emitido";
                            $estado_item = "Emitido";
                            $estado_pedido = "Pedido Emitido";
                            $color_mostrar = 'FF0000';
                        }else if( $rs['estadoItem'] === 54 ) {
                            $porcentaje = "15%";
                            $estadofila = "aprobado";
                            $estado_item = "aprobado";
                            $estado_pedido = "Pedido aprobado";
                            $color_mostrar = 'FF0000';
                        }else if( $rs['estadoItem'] === 230 ) {
                            $porcentaje = "100%";
                            $estadofila = "comprado";
                            $estado_item = "Compra Local";
                            $estado_pedido = "Compra Local";
                            $color_mostrar = 'FF0000';
                        }else if( $rs['estadoItem'] == 52  && $rs['ingreso_obra'] == $rs['cantidad_pedido'] ) {
                            $porcentaje = "100%";
                            $estadofila = "entregado";
                            $estado_item = "atendido";
                            $estado_pedido = "atendido";
                            $color_mostrar = '00FF00';
                        }else if( $rs['estadoItem'] === 52  && $rs['ingreso_obra'] == $rs['cantidad_aprobada']) {
                            $porcentaje = "100%";
                            $estadofila = "entregado";
                            $estado_item = "atendido";
                            $estado_pedido = "atendido";
                            $color_mostrar = '00FF00';
                        }else if( $rs['estadoItem'] === 52 ) {
                            $porcentaje = "20%";
                            $estadofila = "stock";
                            $estado_item = "item_stock";
                            $estado_pedido = "stock";
                            $color_mostrar = 'B3C5E6';
                        }else if ( $rs['orden'] && !$rs['proveedor']) {
                            $porcentaje = "25%";
                            $estadofila = "item_orden";
                            $estado_item = "aprobado";
                            $estado_pedido = "aprobado";
                            $color_mostrar = 'FFFF00';   
                        }else if ( $rs['proveedor'] && !$rs['ingreso'] ) {
                            $porcentaje = "30%";
                            $estadofila = "item_enviado";
                            $estado_item = "atendido";
                            $estado_pedido = "atendido";
                            $color_mostrar = 'C0DCC0';
                        }else  if( $rs['ingreso'] && $rs['ingreso'] < $rs['cantidad_orden']) {
                            $porcentaje = "40%";
                            $estadofila = "item_ingreso_parcial";
                            $estado_item = "atendido";
                            $estado_pedido = "atendido";
                            $color_mostrar = 'FFFFE1';
                        }else  if( !$rs['despachos'] && $rs['ingreso'] && $rs['ingreso'] == $rs['cantidad_orden'] ) {
                            $porcentaje = "50%";
                            $estadofila = "item_ingreso_total";
                            $estado_item = "atendido";
                            $estado_pedido = "atendido";
                            $color_mostrar = 'A9D08F';
                        }else if ( $rs['despachos'] && !$rs['ingreso_obra'] ) {
                            $porcentaje = "75%";
                            $estadofila = "item_transito";
                            $estado_item = "atendido";
                            $estado_pedido = "atendido";
                            $color_mostrar = '00FFFF';
                        }else if ( $rs['ingreso_obra'] && $rs['ingreso_obra'] < $rs['cantidad_orden']) {
                            $porcentaje = "85%";
                            $estadofila = "item_ingreso_parcial";
                            $estado_item = "Entrega Parcial";
                            $estado_pedido = "Entrega Parcial";
                            $color_mostrar = 'FFFFE1';
                        }else if ( $rs['ingreso_obra'] && $suma_atendido == $rs['cantidad_aprobada']) {
                            $porcentaje = "100%";
                            $estadofila = "entregado";
                            $estado_item = "atendido";
                            $estado_pedido = "atendido";
                            $semaforo = "Entregado";
                            $color_mostrar = '00FF00';
                        }else if ( $rs['ingreso_obra'] && $rs['ingreso_obra'] == $rs['cantidad_orden'] ) {
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

                        $objPHPExcel->getActiveSheet()->setCellValue('A'.$fila,$item++);
                        $objPHPExcel->getActiveSheet()->setCellValue('B'.$fila,$porcentaje);
                        $objPHPExcel->getActiveSheet()->getStyle('B'.$fila)->applyFromArray($color);

                        $objPHPExcel->getActiveSheet()->setCellValue('C'.$fila,$rs['ccodproy']);
                        $objPHPExcel->getActiveSheet()->setCellValue('D'.$fila,$rs['area']);
                        $objPHPExcel->getActiveSheet()->setCellValue('E'.$fila,$rs['partida']);
                        $objPHPExcel->getActiveSheet()->setCellValue('F'.$fila,$atencion);
                        $objPHPExcel->getActiveSheet()->setCellValue('G'.$fila,$tipo_orden);
                        $objPHPExcel->getActiveSheet()->setCellValue('H'.$fila,$rs['anio_pedido']);
                        $objPHPExcel->getActiveSheet()->setCellValue('I'.$fila,$rs['pedido']);

                        if  ($rs['crea_pedido'] !== "")
                            $objPHPExcel->getActiveSheet()->setCellValue('J'.$fila,PHPExcel_Shared_Date::PHPToExcel($rs['crea_pedido']));
                           
                        if  ( $rs['aprobacion_pedido'] !== null )
                            $objPHPExcel->getActiveSheet()->setCellValue('K'.$fila,$rs['aprobacion_pedido']);
                        else
                            $objPHPExcel->getActiveSheet()->setCellValue('K'.$fila,'');

                        $objPHPExcel->getActiveSheet()->setCellValue('L'.$fila,$cantidad);
                        

                        $objPHPExcel->getActiveSheet()->setCellValue('M'.$fila,$rs['ccodprod']);
                        $objPHPExcel->getActiveSheet()->setCellValue('N'.$fila,$rs['unidad']);
                        $objPHPExcel->getActiveSheet()->setCellValue('O'.$fila,$rs['descripcion']);
                        $objPHPExcel->getActiveSheet()->setCellValue('P'.$fila,$tipo_orden);
                        $objPHPExcel->getActiveSheet()->setCellValue('Q'.$fila,$rs['anio_orden']);
                        $objPHPExcel->getActiveSheet()->setCellValue('R'.$fila,$rs['orden']);

                        if  ($rs['fecha_orden'] !== null)
                            $objPHPExcel->getActiveSheet()->setCellValue('S'.$fila,PHPExcel_Shared_Date::PHPToExcel($rs['fecha_orden']));

                        $objPHPExcel->getActiveSheet()->setCellValue('T'.$fila,$rs['cantidad_orden']);
                       

                        $objPHPExcel->getActiveSheet()->setCellValue('U'.$fila,$rs['item_orden']);

                        if  ($rs['fecha_autorizacion_orden'] !== null)
                            $objPHPExcel->getActiveSheet()->setCellValue('V'.$fila,PHPExcel_Shared_Date::PHPToExcel($rs['fecha_autorizacion_orden']));
                            
                        
                        $objPHPExcel->getActiveSheet()->setCellValue('W'.$fila,$rs['cantidad_atendida']);
                       

                        $objPHPExcel->getActiveSheet()->setCellValue('X'.$fila,$rs['proveedor']);

                        if ( $fecha_descarga != "" )
                            $objPHPExcel->getActiveSheet()->setCellValue('Y'.$fila,PHPExcel_Shared_Date::PHPToExcel($fecha_descarga));   
                        
                        if ( $fecha_entrega != "" )
                        $objPHPExcel->getActiveSheet()->setCellValue('Z'.$fila,PHPExcel_Shared_Date::PHPToExcel($fecha_entrega));   

                        $objPHPExcel->getActiveSheet()->setCellValue('AA'.$fila,$rs['ingreso']);
                        $objPHPExcel->getActiveSheet()->setCellValue('AB'.$fila,$rs['nota_ingreso']);
                        $objPHPExcel->getActiveSheet()->setCellValue('AC'.$fila,$rs['fecha_recepcion_proveedor']);
                        $objPHPExcel->getActiveSheet()->setCellValue('AD'.$fila,$saldoRecibir);
                       
                        
                        $objPHPExcel->getActiveSheet()->setCellValue('AE'.$fila,$rs['plazo']);
                        $objPHPExcel->getActiveSheet()->setCellValue('AF'.$fila,$dias_atraso);
                        
                        $objPHPExcel->getActiveSheet()->setCellValue('AG'.$fila,strtoupper($semaforoEstado));
                        $objPHPExcel->getActiveSheet()->getStyle('AG'.$fila)->applyFromArray($semaforo);
                        
                        $objPHPExcel->getActiveSheet()->setCellValue('AH'.$fila,$rs['despachos']);
                        $objPHPExcel->getActiveSheet()->setCellValue('AI'.$fila,$rs['cnumguia']);
                        
                        $objPHPExcel->getActiveSheet()->setCellValue('AJ'.$fila,$rs['nota_obra']);
                        $objPHPExcel->getActiveSheet()->setCellValue('AK'.$fila,$rs['fecha_registro_almacen']);
                        $objPHPExcel->getActiveSheet()->setCellValue('AL'.$fila,$rs['ingreso_obra']);

                        $objPHPExcel->getActiveSheet()->setCellValue('AM'.$fila,$estado_pedido);
                        $objPHPExcel->getActiveSheet()->setCellValue('AN'.$fila,$estado_item);
                        $objPHPExcel->getActiveSheet()->setCellValue('AO'.$fila,$rs['nroparte']);
                        $objPHPExcel->getActiveSheet()->setCellValue('AP'.$fila,$rs['cregistro']);
                        $objPHPExcel->getActiveSheet()->setCellValue('AQ'.$fila,$rs['operador']);
                        $objPHPExcel->getActiveSheet()->setCellValue('AR'.$fila,$transporte);
                        $objPHPExcel->getActiveSheet()->setCellValue('AS'.$fila,$rs['concepto']);

                        $objPHPExcel->getActiveSheet()->setCellValue('AT'.$fila,$rs['nombre_elabora']);
                        $fila++;
                    }
                }
            
                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');
                $objWriter->save('public/documentos/reportes/cargoplan.xlsx');

                return array("documento"=>'public/documentos/reportes/cargoplan.xlsx');

                exit();

                return $salida;

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
    }
?>