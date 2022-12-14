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
                                                            tb_pedidodet.iditem,
                                                            tb_pedidodet.idpedido,
                                                            tb_pedidodet.idorden,
                                                            FORMAT(tb_pedidodet.cant_aprob,2) AS cantidad_aprobada,
                                                            FORMAT(tb_pedidodet.cant_pedida,2) AS cantidad_solicitada,
                                                            tb_pedidodet.cant_atend,
                                                            tb_pedidodet.estadoItem,
                                                            tb_pedidodet.idcostos,
                                                            tb_pedidodet.idtipo,
                                                            tb_pedidocab.nrodoc,
                                                            if (tb_pedidodet.idtipo = 37,'B','S') AS tipo,
                                                            cm_producto.id_cprod,
                                                            cm_producto.ccodprod,
                                                            tb_unimed.cabrevia AS unidad,
                                                            UPPER(
                                                            CONCAT_WS( ' ', cm_producto.cdesprod, tb_pedidodet.observaciones )) AS descripcion,
                                                            DATE_FORMAT( tb_pedidocab.emision, '%d/%m/%Y' ) AS emision_pedido,
                                                            DATE_FORMAT( tb_pedidocab.faprueba, '%d/%m/%Y' ) AS aprobacion_pedido,
                                                            UPPER(tb_pedidocab.concepto) AS concepto,
                                                            YEAR( tb_pedidocab.emision) AS anio_pedido,
                                                            LPAD( tb_pedidocab.idreg, 6, 0 ) AS pedido,
                                                            LPAD( tb_pedidocab.nrodoc, 6, 0 ) AS nropedido,
                                                            IF(tb_pedidocab.nivelAten = 47,'N','U') AS atencion,
                                                            tb_proyectos.ccodproy,
                                                            UPPER(tb_area.cdesarea) AS area,
                                                            UPPER(partidas.cdescripcion) AS partida,
                                                            LPAD(detalles_orden.id_orden,6,0) AS orden,
                                                            FORMAT(detalles_orden.ncanti,2) AS cantidad_orden,
                                                            YEAR(cabecera_orden.ffechadoc) AS anio_orden,
                                                            DATE_FORMAT( cabecera_orden.ffechadoc, '%d/%m/%Y' ) AS emision_orden,
                                                            DATE_FORMAT( cabecera_orden.ffechaent, '%d/%m/%Y' ) AS entrega_proveedor,
                                                            FORMAT(cabecera_orden.nplazo,0) AS nplazo,
                                                            proveedores.crazonsoc,
                                                            UPPER(operadores.cnombres) AS operador,
                                                            tb_parametros.cdescripcion AS estado_pedido,
                                                            transporte.modo_transporte,
                                                            DATEDIFF(NOW(),cabecera_orden.ffechaent) AS dias_atraso  
                                                        FROM
                                                            tb_costusu
                                                            INNER JOIN tb_pedidodet ON tb_costusu.ncodproy = tb_pedidodet.idcostos
                                                            INNER JOIN cm_producto ON tb_pedidodet.idprod = cm_producto.id_cprod
                                                            INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed
                                                            INNER JOIN tb_pedidocab ON tb_pedidodet.idpedido = tb_pedidocab.idreg
                                                            INNER JOIN tb_proyectos ON tb_pedidocab.idcostos = tb_proyectos.nidreg
                                                            INNER JOIN tb_area ON tb_pedidocab.idarea = tb_area.ncodarea
                                                            LEFT JOIN tb_partidas AS partidas ON tb_pedidocab.idpartida = partidas.idreg
                                                            LEFT JOIN ( SELECT id_orden, ncanti, niddeta FROM lg_ordendet WHERE ISNULL(nEstadoReg) ) AS detalles_orden ON tb_pedidodet.iditem = detalles_orden.niddeta
	                                                        LEFT JOIN ( SELECT id_regmov, ffechadoc, ffechaent, id_centi, ncodmon, nplazo, id_cuser, ctiptransp, FechaFin FROM lg_ordencab ) AS cabecera_orden ON detalles_orden.id_orden = cabecera_orden.id_regmov
                                                            LEFT JOIN ( SELECT id_centi, crazonsoc FROM cm_entidad ) AS proveedores ON cabecera_orden.id_centi = proveedores.id_centi
                                                            LEFT JOIN ( SELECT tb_user.cnameuser,tb_user.cnombres,tb_user.iduser FROM tb_user ) AS operadores ON cabecera_orden.id_cuser = operadores.iduser
                                                            LEFT JOIN ( SELECT cdescripcion AS modo_transporte,nidreg FROM tb_parametros ) AS transporte ON transporte.nidreg = cabecera_orden.ctiptransp
                                                            INNER JOIN tb_parametros ON tb_pedidocab.estadodoc = tb_parametros.nidreg 
                                                        WHERE
                                                            tb_costusu.id_cuser = :user 
                                                            AND tb_costusu.nflgactivo = 1 
                                                            AND tb_pedidodet.nflgActivo = 1
                                                            AND tb_pedidodet.idtipo LIKE :tipo
                                                            AND tb_pedidodet.idcostos LIKE :costo
                                                            AND cm_producto.ccodprod LIKE :codigo
                                                            AND tb_pedidodet.idorden LIKE :orden
                                                            AND tb_pedidocab.nrodoc LIKE :pedido
                                                            AND tb_pedidocab.concepto LIKE :concepto
                                                        ORDER BY tb_proyectos.ccodproy");
                
                $sql->execute(["user"       => $_SESSION['iduser'],
                               "tipo"       => $tipo,
                               "costo"      => $costo,
                               "codigo"     => $codigo,
                               "orden"      => $orden,
                               "pedido"     => $pedido,
                               "concepto"   => $concepto]);
                
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()){

                        $tipo  = $rs['tipo'] == 'B' ? 'bienes':'servicios';
                        $orden = $rs['tipo'] == 'B' ? 'C':'S';
                        $estadoItem = $rs['estadoItem'] != 105 ? "ATENDIDO":"ANULADO";
                        $ingresos = $this->cantidadesRecepcion($rs['id_cprod'],$rs['idpedido'],$rs['iditem']);
                        $despachos= $this->cantidadesDespacho($rs['id_cprod'],$rs['idorden'],$rs['iditem']);
                        $ingObra  = $this->cantidadesRecepcionObra($rs['id_cprod'],$rs['idorden'],$rs['iditem']);

                        $saldoRecibir = intval($rs['cantidad_solicitada']) - intval($ingresos[0]['ingresos']);

                        $semaforo = "";
                        $estadoSemaforo ="";

                        if ( $saldoRecibir == 0 ) {
                            $semaforo = "Verde";
                            $estadoSemaforo ="semaforoVerde";
                        }else if ( $ingresos[0]['ingresos'] == 0 && $rs['dias_atraso'] >= 10 && $rs['orden']) {
                            $semaforo = "Rojo";
                            $estadoSemaforo ="semaforoRojo";
                        }else if ( $ingresos[0]['ingresos'] == 0 && $rs['dias_atraso'] <= 10 && $rs['orden']) {
                            $semaforo = "Amarillo";
                            $estadoSemaforo ="semaforoAmarillo";
                        }

                        $estadoFila = "";
                        $porcentaje = "*";

                        if ( $rs['estadoItem'] == 105 ) {
                            $estadoFila = "item_anulado";
                            $porcentaje = "0%";
                        }
                        
                        if( $rs['estadoItem'] == 49 || $rs['estadoItem'] == 54 || $rs['estadoItem'] == 60 ) {
                            $estadoFila = "item_aprobado";
                            $porcentaje = "15%";
                        }
                        if( $rs['orden'] ) {
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

                        if ( $rs['orden'] != "0000") {
                            $salida.='<tr class="pointer" 
                                    data-itempedido="'.$rs['iditem'].'" 
                                    data-pedido="'.$rs['idpedido'].'" 
                                    data-prod="'.$rs['id_cprod'].'"
                                    data-orden="'.$rs['idorden'].'"
                                    data-estado="'.$rs['estadoItem'].'">
                                    <td class="textoCentro">'.str_pad($item++,3,0,STR_PAD_LEFT).'</td>
                                    <td class="textoCentro '.$estadoFila.'">'.$porcentaje.'</td>
                                    <td class="textoDerecha pr15px">'.$rs['ccodproy'].'</td>
                                    <td class="pl20px">'.$rs['area'].'</td>
                                    <td class="pl20px">'.$rs['partida'].'</td>
                                    <td class="textoCentro">'.$rs['atencion'].'</td>
                                    <td class="textoCentro '.$tipo.'">'.$rs['tipo'].'</td>
                                    <td class="textoCentro">'.$rs['anio_pedido'].'</td>
                                    <td class="textoCentro">'.$rs['nropedido'].'</td>
                                    <td class="textoCentro"></td>
                                    <td class="textoCentro">'.$rs['emision_pedido'].'</td>
                                    <td class="textoCentro">'.$rs['aprobacion_pedido'].'</td>
                                    <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                    <td class="pl10px">'.$rs['unidad'].'</td>
                                    <td class="pl10px">'.$rs['descripcion'].'</td>
                                    <td class="textoDerecha pr15px">'.$rs['cantidad_solicitada'].'</td>
                                    <td class="textoCentro '.$tipo.' ">'.$orden.'</td>
                                    <td class="textoCentro">'.$rs['anio_orden'].'</td>
                                    <td class="textoCentro">'.$rs['orden'].'</td>
                                    <td class="textoCentro">'.$rs['emision_orden'].'</td>
                                    <td class="pl10px">'.$rs['crazonsoc'].'</td>
                                    <td class="textoCentro">'.$rs['entrega_proveedor'].'</td>
                                    <td class="textoDerecha pr15px">'.$ingresos[0]['ingresos'].'</td>
                                    <td class="textoDerecha pr15px">'.number_format($saldoRecibir,2).'</td>
                                    <td class="textoDerecha pr15px">'.$rs['nplazo'].'</td>
                                    <td class="textoDerecha pr15px">'.$rs['dias_atraso'].'</td>
                                    <td class="textoCentro '.$estadoSemaforo.'">'.$semaforo.'</td>
                                    <td class="textoCentro ">'.$ingresos[0]['nnronota'].'</td>
                                    <td class="textoCentro">'.$ingresos[0]['guia_proveedor'].'</td>
                                    <td class="textoCentro">'.$ingresos[0]['fecha_ingreso'].'</td>
                                    <td class="textoCentro">'.$despachos[0]['guia_sepcon'].'</td>
                                    <td class="textoCentro">'.$despachos[0]['nota_salida'].'</td>
                                    <td class="textoCentro">'.$despachos[0]['fecha_despacho'].'</td>
                                    <td class="textoDerecha pr15px">'.$ingObra[0]['ingreso_obra'].'</td>
                                    <td class="textoCentro">'.$ingObra[0]['nota_recepcion'].'</td>
                                    <td class="textoCentro">'.$ingObra[0]['fecha_recepcion'].'</td>
                                    <td class="textoCentro">'.$estado.'</td>
                                    <td class="textoCentro">'.$estadoItem.'</td>
                                    <td class=""></td>
                                    <td class=""></td>
                                    <td class="pl20px">'.$rs['operador'].'</td>
                                    <td class="pl10px">'.$rs['modo_transporte'].'</td>
                                    <td class="">'.$rs['concepto'].'</td>
                                    
                                </tr>';
                        }
                        
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
                    
                    /*if( $datos[$i]->estado == "20%")
                        $objPHPExcel->getActiveSheet()->getStyle('B'.$fila)->applyFromArray($veinte);*/
                        
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