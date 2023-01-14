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
                
                $salida="";
                $item = 1;
                $sql = $this->db->connect()->prepare("SELECT
                                                        LPAD( tb_pedidocab.nrodoc, 6, 0 ) AS numero_pedido,
                                                        DATE_FORMAT( tb_pedidocab.emision, '%d/%m/%Y' ) AS fecha_pedido,
                                                        DATE_FORMAT( tb_pedidocab.faprueba, '%d/%m/%Y' ) AS aprobacion_pedido,
                                                        tb_pedidocab.anio AS anio_pedido,
                                                        UPPER(tb_pedidocab.concepto) AS concepto,
                                                        tb_pedidodet.iditem,
                                                        tb_pedidodet.idpedido,
                                                        tb_pedidodet.idtipo AS tipo_pedido,
                                                        LPAD( tb_pedidodet.idorden, 6, 0 ) AS numero_orden,
                                                        tb_pedidodet.idprod,
                                                        tb_pedidodet.idcostos,
                                                        REPLACE(FORMAT(tb_pedidodet.cant_pedida,2),',','') AS cantidad_pedida,
                                                        cm_producto.ccodprod,
                                                        tb_pedidodet.estadoItem,
                                                        tb_proyectos.ccodproy,
                                                        UPPER( CONCAT_WS( ' ', cm_producto.cdesprod, tb_pedidodet.observaciones ) ) AS cdesprod,
                                                        tb_pedidodet.cant_orden,
                                                        (SELECT SUM( alm_recepdet.ncantidad ) FROM alm_recepdet WHERE alm_recepdet.niddetaPed = tb_pedidodet.iditem )AS ingresos,
	                                                    (SELECT SUM( alm_despachodet.ndespacho) FROM alm_despachodet WHERE alm_despachodet.niddetaPed = tb_pedidodet.iditem ) AS despachos,
                                                        FORMAT(SUM( alm_existencia.cant_ingr ),2) AS ingreso_obra,
                                                        lg_ordencab.cper AS anio_orden,
                                                        lg_ordencab.ntipmov AS tipo_orden,
                                                        DATEDIFF(NOW(),lg_ordencab.ffechaent) AS dias_atraso,
                                                        FORMAT(lg_ordencab.nplazo,0) AS nplazo,
                                                        DATE_FORMAT( lg_ordencab.ffechadoc, '%d/%m/%Y' ) AS fecha_orden,
                                                        DATE_FORMAT(lg_ordencab.ffechaent, '%d/%m/%Y' ) AS fecha_entrega,
                                                        tb_partidas.cdescripcion AS partidas,
                                                        UPPER( tb_area.cdesarea ) AS cdesarea,
                                                        UPPER(cm_entidad.crazonsoc) AS proveedor,
                                                        UPPER( tb_user.cnameuser ) AS operador,
                                                        alm_recepcab.nnronota AS nota_ingreso,
                                                        alm_recepcab.cnumguia AS guia_proveedor,
                                                        LPAD(alm_despachocab.nnronota,6,0) AS nota_despacho,
                                                        DATE_FORMAT(alm_recepcab.ffecdoc, '%d/%m/%Y' ) AS fecha_ingreso,
                                                        DATE_FORMAT(alm_despachocab.ffecdoc, '%d/%m/%Y' ) AS fecha_despacho,
                                                        alm_despachocab.cnumguia AS guia_sepcon,
                                                        alm_despachocab.ffecenvio AS fecha_envio_despacho,
                                                        DATE_FORMAT( alm_cabexist.ffechadoc , '%d/%m/%Y' )AS fecha_obra,
                                                        LPAD(alm_cabexist.idreg,6,0) AS nota_obra,
                                                        tb_parametros.cdescripcion AS transporte,
                                                        atencion.cdescripcion AS atencion,
                                                        tb_unimed.cabrevia AS unidad,
                                                        tb_pedidodet.nroparte,
	                                                    tb_equipmtto.cregistro,
                                                        lg_ordendet.ncanti, 
	                                                    lg_ordencab.ctiptransp 
                                                    FROM
                                                        tb_pedidodet
                                                        INNER JOIN cm_producto ON tb_pedidodet.idprod = cm_producto.id_cprod
                                                        LEFT JOIN alm_recepdet ON tb_pedidodet.iditem = alm_recepdet.niddetaPed
                                                        LEFT JOIN alm_despachodet ON tb_pedidodet.iditem = alm_despachodet.niddetaPed
                                                        LEFT JOIN alm_existencia ON tb_pedidodet.iditem = alm_existencia.idpedido
                                                        INNER JOIN tb_proyectos ON tb_pedidodet.idcostos = tb_proyectos.nidreg
                                                        INNER JOIN tb_pedidocab ON tb_pedidodet.idpedido = tb_pedidocab.idreg
                                                        LEFT JOIN lg_ordencab ON tb_pedidodet.idorden = lg_ordencab.id_regmov
                                                        LEFT JOIN tb_partidas ON tb_pedidocab.idpartida = tb_partidas.idreg
                                                        INNER JOIN tb_area ON tb_pedidocab.idarea = tb_area.ncodarea
                                                        LEFT JOIN cm_entidad ON lg_ordencab.id_centi = cm_entidad.id_centi
                                                        LEFT JOIN tb_user ON lg_ordencab.id_cuser = tb_user.iduser
                                                        LEFT JOIN alm_recepcab ON alm_recepdet.id_regalm = alm_recepcab.id_regalm
                                                        LEFT JOIN alm_despachocab ON alm_despachodet.id_regalm = alm_despachocab.id_regalm
                                                        LEFT JOIN alm_cabexist ON alm_existencia.idregistro = alm_cabexist.idreg
                                                        LEFT JOIN tb_parametros ON lg_ordencab.ctiptransp = tb_parametros.nidreg
                                                        INNER JOIN tb_parametros AS atencion ON tb_pedidodet.tipoAten = atencion.nidreg
                                                        INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed
                                                        LEFT JOIN Lg_ordendet ON tb_pedidodet.iditem = lg_ordendet.niddeta
                                                        LEFT JOIN tb_equipmtto ON tb_pedidodet.nregistro = tb_equipmtto.idreg 
                                                        WHERE
                                                            tb_pedidodet.nflgActivo 
                                                            AND tb_pedidodet.idpedido 
                                                            AND tb_pedidodet.idtipo LIKE :tipo
                                                            AND tb_pedidodet.idcostos LIKE :costo
                                                            AND cm_producto.ccodprod LIKE :codigo
                                                            AND tb_pedidocab.nrodoc LIKE :pedido
                                                            AND tb_pedidocab.concepto LIKE :concepto
                                                            AND tb_pedidodet.idorden LIKE :orden
                                                        GROUP BY tb_pedidodet.iditem
                                                        ORDER BY tb_pedidocab.anio DESC");
                
                $sql->execute(["tipo"       => $tipo,
                               "costo"      => $costo,
                               "codigo"     => $codigo,
                               "orden"      => $orden,
                               "pedido"     => $pedido,
                               "concepto"   => $concepto]);
                
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()){
                            
                            $estado = "";
                            $porcentaje = 0;
                            $estadoFila = 0;
                            $estadoSemaforo = "";
                            $semaforo = "";
                            $saldoRecibir = 0;
                            $saldo = 0;

                            $tipo_pedido = $rs['tipo_pedido'] == 37 ? 'B' : 'S';
                            $tipo_orden = $rs['tipo_pedido'] == 37 ? 'C' : 'S';
                            $clase_operacion = $rs['tipo_pedido'] == 37 ? 'bienes' : 'servicios';

                            if ( $rs['estadoItem'] == 105 ) {
                                $estadoFila = "item_anulado";
                                $porcentaje = "0%";
                            }

                            if ( $rs['tipo_pedido'] == 37 ){
                                $saldo = intval($rs['cantidad_pedida']) - intval($rs['ingresos']);
                                $saldoRecibir = number_format($saldo,2);
                            }

                            $dias_atraso = $rs['tipo_pedido'] == 37 ? $rs['dias_atraso'] : " ";
                            $dias_atraso = $rs['dias_atraso'] < 0 ? " " : $dias_atraso;

                            $dias_atraso = $saldo == 0 ? " " : $dias_atraso;
                            $transporte = $rs['ctiptransp'] == 39 ? "TERRESTRE": $rs['transporte'];

                            if ( $saldoRecibir == $rs['ingreso_obra']) {
                                $semaforo = "entregado";
                                $estadoSemaforo ="semaforoNaranja";
                            }else if ( $rs['ingresos'] == $rs['cantidad_pedida'] && $dias_atraso <= 7){
                                $semaforo = "verde";
                                $estadoSemaforo ="semaforoVerde";
                            }else if ( $rs['ingresos'] == $rs['cantidad_pedida'] && $dias_atraso > 7){
                                $semaforo = "Rojo";
                                $estadoSemaforo ="semaforoRojo";
                            }
                            
                            /*if( $rs['estadoItem'] == 49 || $rs['estadoItem'] == 54 || $rs['estadoItem'] == 60 ) {
                                $estadoFila = "item_aprobado";
                                $porcentaje = "15%";
                            }


                                /*if( $rs['orden'] ) {
                                    $estadoFila = "item_orden";
                                    $porcentaje = "20%";
                                    $estado = "Orden";
                                }
                                
                                if( !$rs['orden'] && $ingresos[0]['ingresos'] > 0)  {
                                    $estadoFila = "item_parcial";
                                    $porcentaje = "25%";
                                }
                                
                                if( $rs['orden'] && $ingresos[0]['ingresos'] != $rs['cantidad_solicitada'] && $ingresos[0]['ingresos'])  {
                                    $estadoFila = "item_ingreso_parcial";
                                    $porcentaje = "40%";
                                }

                                if( $rs['orden'] && $ingresos[0]['ingresos'] == $rs['cantidad_solicitada'])  {
                                    $estadoFila = "item_ingreso_total";
                                    $porcentaje = "50%";
                                }

                                if( $despachos[0]['nota_salida'] )  {
                                    $estadoFila = "item_registro_salida";
                                    $porcentaje = "60%";
                                    $estado = "Despacho";
                                }

                                if( $despachos[0]['guia_sepcon'] )  {
                                    $estadoFila = "item_transito";
                                    $estado = "Transito";
                                }

                                if( $ingObra[0]['nota_recepcion'] )  {
                                    $estadoFila = "item_obra";
                                    $porcentaje = "100%";
                                    $estado = "Atendido";
                                }
                                    */

                            $salida.='<tr class="pointer" 
                                    data-itempedido="" 
                                    data-pedido="'.$rs['idpedido'].'" 
                                    data-prod=""
                                    data-orden=""
                                    data-estado=""
                                    data-producto="'.$rs['idprod'].'">
                                    <td class="textoCentro">'.str_pad($item++,3,0,STR_PAD_LEFT).'</td>
                                    <td class="textoCentro '.$estadoFila.'">'.$porcentaje.'</td>
                                    <td class="textoDerecha pr15px">'.$rs['ccodproy'].'</td>
                                    <td class="pl20px">'.$rs['cdesarea'].'</td>
                                    <td class="pl20px">'.$rs['partidas'].'</td>
                                    <td class="textoCentro '.strtolower($rs['atencion']).'">'.$rs['atencion'].'</td>
                                    <td class="textoCentro '.$clase_operacion.'">'.$tipo_pedido.'</td>
                                    <td class="textoCentro">'.$rs['anio_pedido'].'</td>
                                    <td class="textoCentro">'.$rs['numero_pedido'].'</td>
                                    <td class="textoCentro">'.$rs['fecha_pedido'].'</td>
                                    <td class="textoCentro">'.$rs['aprobacion_pedido'].'</td>
                                    <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                    <td class="textoCentro">'.$rs['unidad'].'</td>
                                    <td class="pl10px">'.$rs['cdesprod'].'</td>
                                    <td class="textoDerecha pr10px">'.$rs['cantidad_pedida'].'</td>
                                    <td class="textoCentro '.$clase_operacion.'">'.$tipo_orden.'</td>
                                    <td class="textoCentro">'.$rs['anio_orden'].'</td>
                                    <td class="textoCentro">'.$rs['numero_orden'].'</td>
                                    <td class="textoCentro">'.$rs['fecha_orden'].'</td>
                                    <td class="textoDerecha pr15px" style="background:#e8e8e8;font-weight: bold">'.$rs['ncanti'].'</td>
                                    <td class="pl10px">'.$rs['proveedor'].'</td>
                                    <td class="textoCentro">'.$rs['fecha_entrega'].'</td>
                                    <td class="textoDerecha pr15px">'.$rs['ingresos'].'</td>
                                    <td class="textoDerecha pr15px '.$estadoSemaforo.'">'.$saldoRecibir.'</td>
                                    <td class="textoDerecha pr15px">'.$rs['nplazo'].'</td>
                                    <td class="textoDerecha pr15px">'.$dias_atraso.'</td>
                                    <td class="textoCentro '.$estadoSemaforo.'">'.$semaforo.'</td>
                                    <td class="textoCentro">'.$rs['nota_ingreso'].'</td>
                                    <td class="textoCentro">'.$rs['guia_proveedor'].'</td>
                                    <td class="textoCentro">'.$rs['fecha_ingreso'].'</td>
                                    <td class="textoCentro">'.$rs['nota_despacho'].'</td>
                                    <td class="textoCentro">'.$rs['guia_sepcon'].'</td>
                                    <td class="textoCentro">'.$rs['fecha_despacho'].'</td>
                                    <td class="textoDerecha pr15px">'.$rs['ingreso_obra'].'</td>
                                    <td class="textoCentro">'.$rs['nota_obra'].'</td>
                                    <td class="textoCentro">'.$rs['fecha_obra'].'</td>
                                    <td class="textoCentro"></td>
                                    <td class="textoCentro"></td>
                                    <td class="">'.$rs['nroparte'].'</td>
                                    <td class="">'.$rs['cregistro'].'</td>
                                    <td class="pl20px">'.$rs['operador'].'</td>
                                    <td class="pl10px">'.$transporte.'</td>
                                    <td class="">'.$rs['concepto'].'</td>
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

                $veinte = array(
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'startcolor' => array(
                            'argb' => '4CCAE2',
                        ),
                        'endcolor' => array(
                            'argb' => '4CCAE2',
                        ),
                    ),
                );

                
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
                $objPHPExcel->getActiveSheet()->setCellValue('J2','N° Mtto'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('K2','Creación Pedido'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('L2','Aprobación Pedido'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('M2','Codigo del Bien/Servicio'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('N2','Unidad Medida'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('O2','Descripcion del Bien/Servicio'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('P2','Cantidad Solicitada'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('Q2','Tipo Orden'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('R2','Año Orden'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('S2','Fecha Orden'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('T2','Nro. Orden'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('U2','Descripcion Proveedor'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('V2','Fecha Envio Proveedor'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('W2','Cantidad Recibida'); // esto cambia
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
                    $objPHPExcel->getActiveSheet()->setCellValue('A'.$fila,$datos[$i]->item);

                    $objPHPExcel->getActiveSheet()->setCellValue('B'.$fila,$datos[$i]->estado);
                    
                    if( $datos[$i]->estado == "20%")
                        $objPHPExcel->getActiveSheet()->getStyle('B'.$fila)->applyFromArray($veinte);
                        
                    $objPHPExcel->getActiveSheet()->setCellValue('C'.$fila,$datos[$i]->proyecto);
                    $objPHPExcel->getActiveSheet()->setCellValue('D'.$fila,$datos[$i]->area);
                    $objPHPExcel->getActiveSheet()->setCellValue('E'.$fila,$datos[$i]->partida);
                    $objPHPExcel->getActiveSheet()->setCellValue('F'.$fila,$datos[$i]->atencion);
                    $objPHPExcel->getActiveSheet()->setCellValue('G'.$fila,$datos[$i]->tipo);
                    $objPHPExcel->getActiveSheet()->setCellValue('H'.$fila,$datos[$i]->anio_pedido);
                    $objPHPExcel->getActiveSheet()->setCellValue('I'.$fila,$datos[$i]->num_pedido);
                    $objPHPExcel->getActiveSheet()->setCellValue('J'.$fila,$datos[$i]->num_mmto);
                    $objPHPExcel->getActiveSheet()->setCellValue('K'.$fila,$datos[$i]->crea_pedido);
                    $objPHPExcel->getActiveSheet()->setCellValue('L'.$fila,$datos[$i]->apro_pedido);
                    $objPHPExcel->getActiveSheet()->setCellValue('M'.$fila,$datos[$i]->codigo);
                    $objPHPExcel->getActiveSheet()->setCellValue('N'.$fila,$datos[$i]->unidad);
                    $objPHPExcel->getActiveSheet()->setCellValue('O'.$fila,$datos[$i]->descripcion);
                    $objPHPExcel->getActiveSheet()->setCellValue('P'.$fila,$datos[$i]->cantidad);
                    $objPHPExcel->getActiveSheet()->setCellValue('Q'.$fila,$datos[$i]->tipo_orden);
                    $objPHPExcel->getActiveSheet()->setCellValue('R'.$fila,$datos[$i]->anio_orden);
                    $objPHPExcel->getActiveSheet()->setCellValue('T'.$fila,$datos[$i]->fecha_orden);
                    $objPHPExcel->getActiveSheet()->setCellValue('S'.$fila,$datos[$i]->nro_orden);
                    $objPHPExcel->getActiveSheet()->setCellValue('U'.$fila,$datos[$i]->proveedor);
                    $objPHPExcel->getActiveSheet()->setCellValue('V'.$fila,$datos[$i]->envio_proveedor);
                    $objPHPExcel->getActiveSheet()->setCellValue('W'.$fila,$datos[$i]->cantidad_recibida);
                    $objPHPExcel->getActiveSheet()->setCellValue('X'.$fila,$datos[$i]->saldo_recibir);
                    $objPHPExcel->getActiveSheet()->setCellValue('Y'.$fila,$datos[$i]->dias_entrega);
                    $objPHPExcel->getActiveSheet()->setCellValue('Z'.$fila,$datos[$i]->dias_atraso);
                    $objPHPExcel->getActiveSheet()->setCellValue('AA'.$fila,$datos[$i]->semaforo);
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

                $objPHPExcel->getActiveSheet()->getStyle('V')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('V')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

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