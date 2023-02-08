<?php
    class CargoPlannerModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarCargoPlan($parametros){
            try {
                $tipo       = $parametros['tipoSearch'] == -1 ? "%" : $parametros['tipoSearch'];
                $costo      = $parametros['costosSearch'] == -1 ? "%" : $parametros['costosSearch'];
                $codigo     = $parametros['codigoSearch'] == "" ? "%" : "%".$parametros['codigoSearch']."%";
                $orden      = $parametros['ordenSearch'] == "" ? "%" : $parametros['ordenSearch'];
                $pedido     = $parametros['pedidoSearch'] == "" ? "%" : $parametros['pedidoSearch'];
                $concepto   = $parametros['conceptoSearch'] == "" ? "%" : "%".$parametros['conceptoSearch']."%";
                
                $salida = "No hay registros";
                $item = 1;

                $sql = $this->db->connect()->prepare("SELECT
                                                        lg_ordendet.nitemord,
                                                        lg_ordendet.id_orden,
                                                        lg_ordendet.id_cprod,
                                                        cm_producto.ccodprod,
                                                        UPPER( CONCAT( cm_producto.cdesprod ,' ', tb_pedidodet.observaciones) ) AS descripcion,
                                                        tb_proyectos.ccodproy,
                                                        tb_proyectos.cdesproy,
                                                        cm_entidad.crazonsoc,
                                                        tb_pedidodet.cant_pedida AS cantidad_pedido,
                                                        lg_ordendet.ncanti AS cantidad_orden,
                                                        lg_ordendet.item,
                                                        lg_ordencab.cper AS anio_orden,
                                                        lg_ordencab.id_regmov AS orden,
                                                        tb_pedidocab.nrodoc AS pedido,
                                                        DATE_FORMAT( tb_pedidocab.emision, '%d/%m/%Y' ) AS crea_pedido,
                                                        DATE_FORMAT( tb_pedidocab.faprueba, '%d/%m/%Y' ) AS aprobacion_pedido,
                                                        lg_ordencab.ffechadoc AS emision_orden,
                                                        tb_pedidocab.anio AS anio_pedido,
                                                        tb_partidas.cdescripcion AS partida,
                                                        UPPER(cm_entidad.crazonsoc) AS proveedor,
                                                        lg_ordencab.FechaFin AS fecha_autorizacion,
                                                        lg_ordencab.ffechaent AS fecha_entrega,
                                                        lg_ordencab.nplazo AS plazo,
                                                        lg_ordencab.ntipmov,
                                                        DATE_FORMAT( lg_ordencab.ffechadoc, '%d/%m/%Y' ) AS fecha_orden,
                                                        DATE_FORMAT(lg_ordencab.ffechaent, '%d/%m/%Y' ) AS fecha_entrega,
                                                        DATE_FORMAT(lg_ordencab.fechafin, '%d/%m/%Y' ) AS fecha_autorizacion,
                                                        tb_pedidodet.estadoItem,
                                                        tb_pedidodet.nregistro,
                                                        tb_pedidodet.nroparte,
                                                        UPPER( tb_area.cdesarea ) AS area,
                                                        tb_parametros.cdescripcion AS atencion,
                                                        tb_unimed.cabrevia AS unidad,
                                                        lg_ordencab.nEstadoDoc 
                                                    FROM
                                                        lg_ordendet
                                                        INNER JOIN cm_producto ON lg_ordendet.id_cprod = cm_producto.id_cprod
                                                        INNER JOIN lg_ordencab ON lg_ordendet.id_orden = lg_ordencab.id_regmov
                                                        INNER JOIN tb_proyectos ON lg_ordencab.ncodpry = tb_proyectos.nidreg
                                                        INNER JOIN cm_entidad ON lg_ordencab.id_centi = cm_entidad.id_centi
                                                        INNER JOIN tb_pedidodet ON lg_ordendet.niddeta = tb_pedidodet.iditem
                                                        INNER JOIN tb_pedidocab ON tb_pedidodet.idpedido = tb_pedidocab.idreg
                                                        LEFT JOIN tb_partidas ON tb_pedidocab.idpartida = tb_partidas.idreg
                                                        INNER JOIN tb_area ON tb_pedidocab.idarea = tb_area.ncodarea
                                                        INNER JOIN tb_parametros ON lg_ordencab.nNivAten = tb_parametros.nidreg
                                                        INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed 
                                                    WHERE
                                                        lg_ordendet.id_regmov <> 0 
                                                        AND lg_ordendet.id_orden LIKE :orden 
                                                    ORDER BY
                                                        lg_ordencab.id_regmov DESC");
                
                $sql->execute(["orden"=>$orden]);
                
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()){
                            
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

                            $tipo_orden = $rs['ntipmov'] == 37 ? 'BIENES' : 'SERVICIO';
                            $clase_operacion = $rs['ntipmov'] == 37 ? 'bienes' : 'servicios';

                            /*$dias_atraso = $rs['dias_atraso'] >= 0 && $rs['cantidad_orden'] - $rs['ingreso_obra'] ? $rs['dias_atraso'] : "" ;
                            $saldoRecibir = $rs['cantidad_orden'] - $rs['ingreso']; 

                            $saldo_obra =  $rs['cantidad_orden'] - $rs['ingreso_obra'];
                            $estado_pedido =  $rs['estado_pedido'] >= 54 ? "Atendido":"Pendiente";
                            $estado_item   =  $rs['estado_pedido'] >= 54 ? "Atendido":"Pendiente";

                            $transporte = $rs['ctiptransp'] == 39 ? "TERRESTRE": $rs['transporte'];

                            if ( $rs['cantidad_orden'] >  0 ) {
                                if ( $saldo_obra == 0 ){
                                    $estadoSemaforo = "semaforoVerde";
                                    $semaforo = "Entregado";
                                }else if ( $dias_atraso >= 5 && ($rs['cantidad_orden'] != $rs['ingreso']) ) {
                                    $estadoSemaforo = "semaforoRojo";
                                    $semaforo = "Rojo";
                                }else if ( $rs['cantidad_orden'] == $rs['ingreso'] ) {
                                    $estadoSemaforo = "semaforoNaranja";
                                    $semaforo = "Naranja";
                                }else if ( $dias_atraso == "" ) {
                                    $estadoSemaforo = "semaforoVerde";
                                    $semaforo = "Verde";
                                }
                            }*/
                            

                            /*if ( $rs['estadoItem'] == 105 ) {
                                $porcentaje = "0%";
                                $estadofila = "anulado";
                                $estado_item = "anulado";
                            }else if ( $rs['estadoItem'] == 49 ) {
                                $porcentaje = "10%";
                                $estadofila = "pedidoCreado";
                            }else if ( $rs['estadoItem'] == 54 ) {
                                $porcentaje = "15%";
                                $estadofila = "item_aprobado";
                            }else if ( $rs['estadoItem'] == 52 ) {
                                $porcentaje = "20%";
                                $estadofila = "stock";
                            }else if ( $rs['nro_orden']  && !$rs['ingreso'] ) {
                                $porcentaje = "25%";
                                $estadofila = "item_orden";
                            }else if ($rs['ingreso'] > 0 && $rs['cantidad_orden'] > $rs['ingreso']) {
                                $porcentaje = "40%";
                                $estadofila = "item_parcial";
                            }else if ( $rs['cantidad_orden'] == $rs['ingreso']  && !$rs['guia_sepcon']) {
                                $porcentaje = "50%";
                                $estadofila = "item_ingreso_total";
                            }else if ( $rs['estadoItem'] == 86 ) {
                                $porcentaje = "60%";
                                $estadofila = "compra_local";
                            }else if ( $rs['estadoItem'] == 87 ) {
                                $porcentaje = "70%";
                                $estadofila = "item_ingreso_total";
                            }else if ( $rs['guia_sepcon'] && !$rs['ingreso_obra'] ) {
                                $porcentaje = "75%";
                                $estadofila = "item_transito";
                            }else if ( $rs['ingreso_obra'] > 0 ) {
                                $porcentaje = "100%";
                                $estadofila = "item_obra";
                            }*/
    
                            $salida.='<tr class="pointer" 
                                        data-itempedido="" 
                                        data-pedido="" 
                                        data-prod=""
                                        data-orden=""
                                        data-estado=""
                                        data-producto="">
                                        <td class="textoCentro">'.str_pad($item++,3,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro '.$estadofila.'">'.$porcentaje.'</td>
                                        <td class="textoDerecha pr15px">'.$rs['ccodproy'].'</td>
                                        <td class="pl20px">'.$rs['area'].'</td>
                                        <td class="pl20px">'.$rs['partida'].'</td>
                                        <td class="textoCentro ">'.$rs['atencion'].'</td>
                                        <td class="textoCentro '.$clase_operacion.'">'.$tipo_orden.'</td>
                                        <td class="textoCentro">'.$rs['anio_pedido'].'</td>
                                        <td class="textoCentro">'.$rs['pedido'].'</td>
                                        <td class="textoCentro">'.$rs['crea_pedido'].'</td>
                                        <td class="textoCentro">'.$rs['aprobacion_pedido'].'</td>
                                        <td class="textoDerecha">'.$rs['cantidad_pedido'].'</td>
                                        <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                        <td class="textoCentro">'.$rs['unidad'].'</td>
                                        <td class="pl10px">'.$rs['descripcion'].'</td>
                                        <td class="textoCentro '.$clase_operacion.'">'.$tipo_orden.'</td>
                                        <td class="textoCentro">'.$rs['anio_orden'].'</td>
                                        <td class="textoCentro">'.$rs['orden'].'</td>
                                        <td class="textoCentro">'.$rs['fecha_orden'].'</td>
                                        <td class="textoDerecha pr15px" style="background:#e8e8e8;font-weight: bold">'.$rs['cantidad_orden'].'</td>
                                        <td class="pl10px">'.$rs['item'].'</td>
                                        <td class="pl10px">'.$rs['fecha_autorizacion'].'</td>
                                        <td class="pl10px">'.$rs['proveedor'].'</td>
                                        <td class="textoCentro">'.$rs['fecha_entrega'].'</td>
                                        <td class="textoDerecha pr15px"></td>
                                        <td class="textoDerecha pr15px '.$estadoSemaforo.'">'.$saldoRecibir.'</td>
                                        <td class="textoDerecha pr15px">'.$rs['plazo'].'</td>
                                        <td class="textoDerecha pr15px">'.$dias_atraso.'</td>
                                        <td class="textoCentro '.$estadoSemaforo.'">'.$semaforo.'</td>
                                        <td class="textoCentro"></td>
                                        <td class="textoCentro"></td>
                                        <td class="textoCentro"></td>
                                        <td class="textoCentro"></td>
                                        <td class="textoCentro"></td>
                                        <td class="textoCentro"></td>
                                        <td class="textoDerecha pr15px"></td>
                                        <td class="textoCentro"></td>
                                        <td class="textoCentro"></td>
                                        <td class="textoCentro '.$estado_pedido.'">'.$estado_pedido.'</td>
                                        <td class="textoCentro">'.$estado_item.'</td>
                                        <td class="textoCentro"></td>
                                        <td class="textoCentro"></td>
                                        <td class="textoCentro"></td>
                                        <td class="textoCentro">'.$transporte.'</td>
                                        <td class="pl10px"></td>
                                </tr>';
                    }
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


                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');
                $objWriter->save('public/documentos/reportes/catalogo.xlsx');
                $objPHPExcel->getActiveSheet()->mergeCells('A1:AQ1');
                $objPHPExcel->getActiveSheet()->setCellValue('A1','CARGO PLAN');

                $objPHPExcel->getActiveSheet()->getStyle('A1:AQ2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('A1:AQ2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

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
                $objPHPExcel->getActiveSheet()->getColumnDimension("AG")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("AJ")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("AK")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("AL")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("AO")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("AP")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("AQ")->setAutoSize(true);

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
                            ->getStyle('AE2:AJ2')
                            ->getFill()
                            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB('FFFF00');

                $objPHPExcel->getActiveSheet()
                            ->getStyle('AK2:AN2')
                            ->getFill()
                            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB('BFCDDB');
                
                
                $objPHPExcel->getActiveSheet()
                            ->getStyle('AO2:AQ2')
                            ->getFill()
                            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB('C0DCC0');

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
                $objPHPExcel->getActiveSheet()->setCellValue('K2','Aprobación Pedido'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('L2','Cantidad Pedido'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('M2','Codigo del Bien/Servicio'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('N2','Unidad Medida'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('O2','Descripcion del Bien/Servicio'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('P2','Cantidad Solicitada'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('Q2','Tipo Orden'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('R2','Año Orden'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('S2','Nro Orden'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('T2','Fecha Orden'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('U2','Cantidad Orden'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('V2','Descripcion del proveedor'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('W2','Fecha Entrega'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('X2','Saldo por Recibir'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('Y2','Dias Entrega'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('Z2','Días Atrazo'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AA2','Semáforo'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AB2','Nota Ingreso'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AC2','Guia Ingreso'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AD2','Fecha Ingreso'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AE2','Nota Salida'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AF2','Guia Remision'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AG2','Fecha Guia Remisión'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AH2','Cantidad Recibida Obra'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AI2','Nota Ingreso Obra'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AJ2','Fecha Recep Obra'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AK2','Estado Pedido'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AL2','Estado Item'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AM2','N° Parte'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AN2','Codigo Activo'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AO2','Operador Logístico'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AP2','Tipo Transporte'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AQ2','Observaciones/Concepto'); // esto cambia
               
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
                    $objPHPExcel->getActiveSheet()->setCellValue('P'.$fila,$datos[$i]->cantidad);
                    $objPHPExcel->getActiveSheet()->setCellValue('Q'.$fila,$datos[$i]->tipo_orden);
                    $objPHPExcel->getActiveSheet()->setCellValue('R'.$fila,$datos[$i]->anio_orden);
                    $objPHPExcel->getActiveSheet()->setCellValue('S'.$fila,$datos[$i]->nro_orden);
                    $objPHPExcel->getActiveSheet()->setCellValue('T'.$fila,$datos[$i]->fecha_orden);
                    $objPHPExcel->getActiveSheet()->setCellValue('U'.$fila,$datos[$i]->cantidad_orden);
                    $objPHPExcel->getActiveSheet()->setCellValue('V'.$fila,$datos[$i]->proveedor);
                    $objPHPExcel->getActiveSheet()->setCellValue('W'.$fila,$datos[$i]->fecha_entrega);
                    $objPHPExcel->getActiveSheet()->setCellValue('X'.$fila,$datos[$i]->saldo_recibir);
                    $objPHPExcel->getActiveSheet()->setCellValue('Y'.$fila,$datos[$i]->dias_entrega);
                    $objPHPExcel->getActiveSheet()->setCellValue('Z'.$fila,$datos[$i]->dias_atraso);
                    
                    $objPHPExcel->getActiveSheet()->setCellValue('AA'.$fila,$datos[$i]->semaforo);
                    $objPHPExcel->getActiveSheet()->getStyle('AA'.$fila)->applyFromArray($semaforo);
                    
                    $objPHPExcel->getActiveSheet()->setCellValue('AB'.$fila,$datos[$i]->nota_ingreso);
                    $objPHPExcel->getActiveSheet()->setCellValue('AC'.$fila,$datos[$i]->guia_ingreso);
                    $objPHPExcel->getActiveSheet()->setCellValue('AD'.$fila,$datos[$i]->fecha_ingreso);
                    $objPHPExcel->getActiveSheet()->setCellValue('AE'.$fila,$datos[$i]->nota_salida);
                    $objPHPExcel->getActiveSheet()->setCellValue('AF'.$fila,$datos[$i]->guia_remision);
                    $objPHPExcel->getActiveSheet()->setCellValue('AG'.$fila,$datos[$i]->fecha_guiaremision);
                    $objPHPExcel->getActiveSheet()->setCellValue('AH'.$fila,$datos[$i]->cantidad_obra);
                    $objPHPExcel->getActiveSheet()->setCellValue('AI'.$fila,$datos[$i]->nota_ingresoobra);
                    $objPHPExcel->getActiveSheet()->setCellValue('AJ'.$fila,$datos[$i]->fecha_recepobra);
                    $objPHPExcel->getActiveSheet()->setCellValue('AK'.$fila,$datos[$i]->estado_pedido);
                    $objPHPExcel->getActiveSheet()->setCellValue('AL'.$fila,$datos[$i]->estado_item);
                    $objPHPExcel->getActiveSheet()->setCellValue('AM'.$fila,$datos[$i]->numero_parte);
                    $objPHPExcel->getActiveSheet()->setCellValue('AN'.$fila,$datos[$i]->codigo_activo);
                    $objPHPExcel->getActiveSheet()->setCellValue('AO'.$fila,$datos[$i]->operador);
                    $objPHPExcel->getActiveSheet()->setCellValue('AP'.$fila,$datos[$i]->transporte);
                    $objPHPExcel->getActiveSheet()->setCellValue('AQ'.$fila,$datos[$i]->observaciones);

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
    }
?>