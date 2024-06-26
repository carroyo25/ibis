<?php
    class CargoPlanModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarCargoPlan($parametros){
            try {

                $salida = "No hay registros";

                /*$tipo     = $parametros['tipoSearch'] == -1 ? "%" : $parametros['tipoSearch'];
                $costo      = $parametros['costosSearch'] == -1 ? "%" : $parametros['costosSearch'];
                $codigo     = $parametros['codigoSearch'] == "" ? "%" : "%".$parametros['codigoSearch']."%";
                $orden      = $parametros['ordenSearch'] == "" ? "%" : $parametros['ordenSearch'];
                $pedido     = $parametros['pedidoSearch'] == "" ? "%" : $parametros['pedidoSearch'];
                $concepto   = $parametros['conceptoSearch'] == "" ? "%" : "%".$parametros['conceptoSearch']."%";
                
                $salida = "No hay registros";
                $item = 1;

                $sql = $this->db->connect()->prepare("SELECT
                                                        tb_pedidodet.iditem,
                                                        tb_pedidodet.idpedido,
                                                        tb_pedidodet.idprod,
                                                        tb_pedidodet.cant_pedida,
                                                        tb_pedidodet.cant_aprob,
                                                        tb_pedidodet.estadoItem,
                                                        tb_pedidodet.nflgActivo,
                                                        cm_producto.ccodprod,
                                                        UPPER(
                                                        CONCAT_WS( '', cm_producto.cdesprod, tb_pedidodet.observaciones )) AS producto,
                                                        tb_pedidodet.idtipo,
                                                        tb_pedidocab.idarea,
                                                        tb_pedidocab.idtrans,
                                                        tb_pedidocab.emision,
                                                        tb_pedidocab.nrodoc,
                                                        tb_pedidocab.faprueba,
                                                        tb_pedidocab.concepto,
                                                        tb_unimed.cabrevia,
                                                        lg_ordendet.ncanti,
                                                        lg_ordendet.id_orden,
                                                        r.ncantidad AS ingreso,
                                                        d.ndespacho AS despacho 
                                                    FROM
                                                        tb_pedidodet
                                                        INNER JOIN cm_producto ON tb_pedidodet.idprod = cm_producto.id_cprod
                                                        INNER JOIN tb_pedidocab ON tb_pedidodet.idpedido = tb_pedidocab.idreg
                                                        INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed
                                                        LEFT JOIN lg_ordendet ON tb_pedidodet.iditem = lg_ordendet.niddeta
                                                        LEFT JOIN ( SELECT alm_recepdet.ncantidad, alm_recepdet.niddetaPed, alm_recepdet.pedido FROM alm_recepdet WHERE alm_recepdet.nflgactivo = 1 ) AS r ON tb_pedidodet.iditem = r.niddetaPed 
                                                        AND r.pedido = lg_ordendet.id_orden
                                                        LEFT JOIN ( SELECT alm_despachodet.ndespacho, alm_despachodet.niddetaPed, alm_despachodet.nropedido FROM alm_despachodet WHERE alm_despachodet.nflgactivo = 1 ) AS d ON tb_pedidodet.iditem = d.niddetaPed 
                                                        AND d.nropedido = lg_ordendet.id_orden 
                                                    WHERE
                                                        tb_pedidodet.nflgActivo = 1
                                                        AND tb_pedidodet.estadoItem != 105  
                                                        AND tb_pedidocab.nrodoc LIKE '%' 
                                                        AND cm_producto.ccodprod LIKE '%' 
                                                        AND lg_ordendet.id_orden LIKE '%' 
                                                    ORDER BY
                                                        tb_pedidodet.femitido DESC");
                                                                                                    
                $sql->execute(["orden"=>$orden,
                               "pedido"=>$pedido,
                               "costo"=>$costo,
                                "codigo"=>$codigo,
                                "concepto"=>$concepto,
                                "tipo"=>$tipo]);
                
                $rowCount = $sql->rowCount();

                $estado = "";
                $porcentaje = 0;
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

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()){

                            if ($rs['orden'] ){
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
                            $dias_atraso  =  $saldoRecibir > 0 ? $rs['dias_atraso'] : "-" ;
                            

                            $estado_pedido =  $rs['estadoItem'] >= 54 ? "Atendido":"Pendiente";
                            $estado_item   =  $rs['estadoItem'] >= 54 ? "Atendido":"Pendiente";

                            $transporte = $rs['nidreg'] == 39 ? "TERRESTRE": $rs['transporte'];

                            $atencion = $rs['atencion'] == 47 ? "NORMAL" : "URGENTE"; 

                            if ( $rs['cantidad_orden'] ) {
                                if ( $rs['ingreso_obra'] == $rs['cantidad_orden'] ){
                                    $estadoSemaforo = "semaforoVerde";
                                    $semaforo = "Entregado";
                                }else if ($dias_atraso <= 5) {
                                    $estadoSemaforo = "semaforoVerde";
                                    $semaforo = "Verde";
                                }else if ( $rs['cantidad_orden'] == $rs['ingreso'] ) {
                                    $estadoSemaforo = "semaforoNaranja";
                                    $semaforo = "Naranja";
                                }else if ( $dias_atraso > 5 && ($rs['cantidad_orden'] != $rs['ingreso']) ) {
                                    $estadoSemaforo = "semaforoRojo";
                                    $semaforo = "Rojo";
                                }
                            }
                            
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
                            }else if( $rs['estadoItem'] == 52 ) {
                                $porcentaje = "20%";
                                $estadofila = "stock";
                                $estado_item = "item_stock";
                                $estado_pedido = "stock";
                            }else if (!$rs['orden']) {
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
                            }else if ( $rs['ingreso_obra'] && $rs['ingreso_obra'] < $rs['cantidad_orden']) {
                                $porcentaje = "85%";
                                $estadofila = "item_ingreso_parcial";
                                $estado_item = "atendido";
                                $estado_pedido = "atendido";
                            }else if ( $rs['ingreso_obra'] && $rs['ingreso_obra'] == $rs['cantidad_orden']) {
                                $porcentaje = "100%";
                                $estadofila = "entregado";
                                $estado_item = "atendido";
                                $estado_pedido = "atendido";
                            }
    
                            $salida.='<tr class="pointer" 
                                        data-itempedido="'.$rs['iditem'].'" 
                                        data-pedido="'.$rs['idpedido'].'" 
                                        data-orden="'.$rs['orden'].'"
                                        data-estado="'.$rs['estadoItem'].'"
                                        data-producto="'.$rs['idprod'].'"
                                        data-aprueba="'.$rs['cnombres'].'"
                                        data-porcentaje="'.$rs['porcentaje'].'">
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
                                        <td class="textoDerecha">'.number_format($rs['cantidad_pedido'],2).'</td>
                                        <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                        <td class="textoCentro">'.$rs['unidad'].'</td>
                                        <td class="pl10px">'.$rs['descripcion'].'</td>
                                        <td class="textoCentro '.$clase_operacion.'">'.$tipo_orden.'</td>
                                        <td class="textoCentro">'.$rs['anio_orden'].'</td>
                                        <td class="textoCentro">'.$rs['orden'].'</td>
                                        <td class="textoCentro">'.$rs['fecha_orden'].'</td>
                                        <td class="textoDerecha pr15px" style="background:#e8e8e8;font-weight: bold">'.$rs['cantidad_orden'].'</td>
                                        <td class="pl10px">'.$itemOrden.'</td>
                                        <td class="pl10px">'.$rs['fecha_autorizacion_orden'].'</td>
                                        <td class="pl10px">'.$rs['proveedor'].'</td>
                                        <td class="textoCentro">'.$rs['fecha_entrega'].'</td>
                                        <td class="textoDerecha pr15px">'.$rs['ingreso'].'</td>
                                        <td class="textoCentro">'.$rs['nota_ingreso'].'</td>
                                        <td class="textoDerecha pr15px">'.$saldoRecibir.'</td>
                                        <td class="textoDerecha pr15px">'.$rs['plazo'].'</td>
                                        <td class="textoDerecha pr15px">'.$dias_atraso.'</td>
                                        <td class="textoCentro '.$estadoSemaforo.'">'.$semaforo.'</td>
                                        <td class="textoDerecha">'.$rs['despachos'].'</td>
                                        <td class="textoCentro">'.$rs['cnumguia'].'</td>
                                        <td class="textoCentro">'.$rs['nota_obra'].'</td>
                                        <td class="textoDerecha">'.number_format($rs['ingreso_obra'],2).'</td>
                                        <td class="textoCentro">'.$estado_pedido.'</td>
                                        <td class="textoCentro">'.$estado_item.'</td>
                                        <td class="textoCentro">'.$rs['nroparte'].'</td>
                                        <td class="textoCentro">'.$rs['nregistro'].'</td>
                                        <td class="textoCentro">'.$rs['operador'].'</td>
                                        <td class="textoCentro">'.$transporte.'</td>
                                        <td class="pl10px">'.$rs['concepto'].'</td>
                                </tr>';
                                
                                $nro_orden = $rs['orden'];
                    }
                }else {
                    $salida = "Buscar el pedido";
                }*/
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


                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');
                $objWriter->save('public/documentos/reportes/catalogo.xlsx');
                $objPHPExcel->getActiveSheet()->mergeCells('A1:AO1');
                $objPHPExcel->getActiveSheet()->setCellValue('A1','CARGO PLAN');

                $objPHPExcel->getActiveSheet()->getStyle('A1:AO2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('A1:AO2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(60);

                $objPHPExcel->getActiveSheet()->getColumnDimension("D")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("E")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("K")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("L")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("M")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("O")->setAutoSize(true);
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
                            ->getStyle('AE2:AO2')
                            ->getFill()
                            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB('FFFF00');

                $objPHPExcel->getActiveSheet()
                            ->getStyle('W2:AD2')
                            ->getFill()
                            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB('BFCDDB');

                $objPHPExcel->getActiveSheet()->getStyle('A1:AK2')->getAlignment()->setWrapText(true);

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
                $objPHPExcel->getActiveSheet()->setCellValue('W2','Descripcion del proveedor'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('X2','Fecha Entrega'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('Y2','Cant. Recibida'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('Z2','Nota de Ingreso'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AA2','Saldo por Recibir'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AB2','Dias Entrega'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AC2','Días Atrazo'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AD2','Semáforo'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AE2','Cantidad Despachada'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AF2','Nro. Guia'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AG2','Registro Almacen'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AH2','Cantidad en Obra'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AI2','Estado Pedido'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AJ2','Estado Item'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AK2','N° Parte'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AL2','Codigo Activo'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AM2','Operador Logístico'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AN2','Tipo Transporte'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AO2','Observaciones/Concepto'); // esto cambia
               
                $fila = 3;
                $datos = json_decode($registros);
                $nreg = count($datos);

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
                    $objPHPExcel->getActiveSheet()->setCellValue('J'.$fila,$datos[$i]->crea_pedido);
                    $objPHPExcel->getActiveSheet()->setCellValue('K'.$fila,$datos[$i]->apro_pedido);
                    $objPHPExcel->getActiveSheet()->setCellValue('L'.$fila,$datos[$i]->cantidad);
                    $objPHPExcel->getActiveSheet()->setCellValue('M'.$fila,$datos[$i]->codigo);
                    $objPHPExcel->getActiveSheet()->setCellValue('N'.$fila,$datos[$i]->unidad);
                    $objPHPExcel->getActiveSheet()->setCellValue('O'.$fila,$datos[$i]->descripcion);
                    $objPHPExcel->getActiveSheet()->setCellValue('P'.$fila,$datos[$i]->tipo_orden);
                    $objPHPExcel->getActiveSheet()->setCellValue('Q'.$fila,$datos[$i]->anio_orden);
                    $objPHPExcel->getActiveSheet()->setCellValue('R'.$fila,$datos[$i]->nro_orden);
                    $objPHPExcel->getActiveSheet()->setCellValue('S'.$fila,$datos[$i]->fecha_orden);
                    $objPHPExcel->getActiveSheet()->setCellValue('T'.$fila,$datos[$i]->cantidad_orden);
                    $objPHPExcel->getActiveSheet()->setCellValue('U'.$fila,$datos[$i]->item_orden);
                    $objPHPExcel->getActiveSheet()->setCellValue('V'.$fila,$datos[$i]->autoriza_orden);
                    $objPHPExcel->getActiveSheet()->setCellValue('W'.$fila,$datos[$i]->proveedor);
                    $objPHPExcel->getActiveSheet()->setCellValue('X'.$fila,$datos[$i]->fecha_entrega);
                    $objPHPExcel->getActiveSheet()->setCellValue('Y'.$fila,$datos[$i]->cantidad_recibida);
                    $objPHPExcel->getActiveSheet()->setCellValue('Z'.$fila,$datos[$i]->nota_ingreso);
                    $objPHPExcel->getActiveSheet()->setCellValue('AA'.$fila,$datos[$i]->saldo_recibir);
                    $objPHPExcel->getActiveSheet()->setCellValue('AB'.$fila,$datos[$i]->dias_entrega);
                    $objPHPExcel->getActiveSheet()->setCellValue('AC'.$fila,$datos[$i]->dias_atraso);
                    
                    $objPHPExcel->getActiveSheet()->setCellValue('AD'.$fila,$datos[$i]->semaforo);
                    $objPHPExcel->getActiveSheet()->getStyle('AD'.$fila)->applyFromArray($semaforo);
                    
                    $objPHPExcel->getActiveSheet()->setCellValue('AE'.$fila,$datos[$i]->despacho);
                    $objPHPExcel->getActiveSheet()->setCellValue('AF'.$fila,$datos[$i]->numero_guia);
                    $objPHPExcel->getActiveSheet()->setCellValue('AG'.$fila,$datos[$i]->registro_almacen);
                    $objPHPExcel->getActiveSheet()->setCellValue('AH'.$fila,$datos[$i]->cantidad_obra);
                    $objPHPExcel->getActiveSheet()->setCellValue('AI'.$fila,$datos[$i]->estado_pedido);
                    $objPHPExcel->getActiveSheet()->setCellValue('AJ'.$fila,$datos[$i]->estado_item);
                    $objPHPExcel->getActiveSheet()->setCellValue('AK'.$fila,$datos[$i]->numero_parte);
                    $objPHPExcel->getActiveSheet()->setCellValue('AL'.$fila,$datos[$i]->codigo_activo);
                    $objPHPExcel->getActiveSheet()->setCellValue('AM'.$fila,$datos[$i]->operador);
                    $objPHPExcel->getActiveSheet()->setCellValue('AN'.$fila,$datos[$i]->transporte);
                    $objPHPExcel->getActiveSheet()->setCellValue('AO'.$fila,$datos[$i]->observaciones);

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
    }
?>