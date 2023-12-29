<?php
    class ValorizadoModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarOrdenes($parametros){
            try {

                $tipo   = $parametros['tipoSearch'] == -1 ? "%" : "%".$parametros['tipoSearch']."%";
                $costos = $parametros['costosSearch'] == -1 ? "%" : $parametros['costosSearch'];
                $mes    = $parametros['mesSearch'] == -1 ? "%" :  $parametros['mesSearch'];
                $anio   = $parametros['anioSearch'] == "" ? "%" : $parametros['anioSearch'];

                $salida = "";

                $sql = $this->db->connect()->prepare("SELECT
                                                    lg_ordencab.id_regmov,
                                                    lg_ordencab.cper,
                                                    lg_ordencab.cnumero,
                                                    FORMAT(lg_ordencab.ntotal,2) AS ntotal,
                                                    lg_ordencab.ntipmov,
                                                    lg_ordencab.ncodmon,
                                                    FORMAT( IF ( lg_ordencab.ncodmon = 21, lg_ordencab.ntotal, lg_ordencab.ntotal / lg_ordencab.ntcambio ), 2 ) AS total_dolares,
                                                    FORMAT( IF ( lg_ordencab.ncodmon = 20, lg_ordencab.ntotal, lg_ordencab.ntotal * lg_ordencab.ntcambio ), 2 ) AS total_soles 
                                                FROM
                                                    lg_ordencab 
                                                WHERE
                                                    lg_ordencab.nEstadoDoc != 105 
                                                    AND lg_ordencab.ncodcos LIKE :costo
                                                    AND YEAR(lg_ordencab.ffechadoc) LIKE :anio");
                $sql->execute(["costo" => $costos,
                                "anio" => $anio]);
                
                $rowCount = $sql->rowcount();

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()) {
                        $salida .= $this->detallesOrden($rs['id_regmov']);
                        $salida .='<tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td><b>Total Orden '.$rs['id_regmov'].'</b></td>
                                        <td></td>
                                        <td></td>
                                        <td class="textoDerecha">'.$rs['ntotal'].'</td>
                                        <td></td>
                                        <td class="textoDerecha">'.$rs['total_dolares'].'</td>
                                        <td class="textoDerecha">'.$rs['total_soles'].'</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>';
                    }
                }

                return $salida;

            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function detallesOrden($id){
            try {
                $salida = "";
                $sql = $this->db->connect()->prepare("SELECT
                                                    lg_ordendet.id_cprod,
                                                    lg_ordendet.ncanti,
                                                    lg_ordendet.nunitario,
                                                    LPAD(lg_ordendet.id_orden,6,0) AS orden,
                                                    UPPER(
                                                    CONCAT_WS( '', cm_producto.cdesprod, tb_pedidodet.observaciones )) AS descripcion,
                                                    tb_unimed.cabrevia AS unidad,
                                                    cm_producto.ccodprod,
                                                    tb_proyectos.ccodproy,
                                                    UPPER( tb_proyectos.cdesproy ) AS proyecto,
                                                    UPPER( tb_area.cdesarea ) AS area,
                                                    lg_ordencab.ntcambio,
                                                    UPPER(lg_ordencab.cobservacion) AS observacion,
                                                    lg_ordencab.cper,
                                                    lg_ordencab.ntipmov,
                                                    DATE_FORMAT(lg_ordencab.ffechadoc,'%d/%m/%Y') AS fecha_registro,
                                                    cm_entidad.crazonsoc,
                                                    LPAD( tb_pedidocab.nrodoc, 6, 0 ) AS pedido,
                                                    lg_ordencab.ncodmon,
                                                    DATE_FORMAT(lg_ordencab.FechaFin,'%d/%m/%Y') AS FechaFin,
                                                    lg_ordencab.nplazo,
                                                    lg_ordencab.cnumcot,
                                                    tb_pedidodet.nroparte,
                                                    tb_pedidodet.nregistro,
                                                    fpagos.cdescripcion AS tipo_pago,
                                                    cm_entidad.cnumdoc,
                                                    tb_grupo.cdescrip AS grupo,
                                                    UPPER(cm_entidad.cviadireccion) as cviadireccion,
                                                    lg_ordencab.nfirmaOpe,
                                                    lg_ordencab.nfirmaFin,
                                                    lg_ordencab.nfirmaLog,
                                                    alm_recepdet.ncantidad,
                                                    tb_clase.cdescrip AS clase,
                                                    lg_ordencab.id_regmov,
                                                    tb_pedidocab.anio,
                                                    lg_ordendet.ntotal,
                                                    DATE_FORMAT( lg_ordencab.ffechaent, '%d/%m/%Y' ) AS fecha_entrega,
                                                    FORMAT( IF ( lg_ordencab.ncodmon = 21, lg_ordendet.ntotal, lg_ordendet.ntotal / lg_ordencab.ntcambio ), 2 ) AS dolares,
	                                                FORMAT( IF ( lg_ordencab.ncodmon = 20, lg_ordendet.ntotal, lg_ordendet.ntotal * lg_ordencab.ntcambio ), 2 ) AS soles
                                                FROM
                                                    lg_ordendet
                                                    INNER JOIN cm_producto ON lg_ordendet.id_cprod = cm_producto.id_cprod
                                                    INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed
                                                    INNER JOIN lg_ordencab ON lg_ordendet.id_orden = lg_ordencab.id_regmov
                                                    INNER JOIN tb_pedidodet ON lg_ordendet.niddeta = tb_pedidodet.iditem
                                                    INNER JOIN tb_proyectos ON lg_ordendet.ncodcos = tb_proyectos.nidreg
                                                    INNER JOIN tb_area ON tb_pedidodet.idarea = tb_area.ncodarea
                                                    INNER JOIN cm_entidad ON lg_ordencab.id_centi = cm_entidad.id_centi
                                                    INNER JOIN tb_pedidocab ON tb_pedidodet.idpedido = tb_pedidocab.idreg
                                                    INNER JOIN tb_parametros AS fpagos ON lg_ordencab.ncodpago = fpagos.nidreg
                                                    INNER JOIN tb_grupo ON cm_producto.ngrupo = tb_grupo.ncodgrupo
                                                    LEFT JOIN alm_recepdet ON lg_ordendet.niddeta = alm_recepdet.niddeta
                                                    INNER JOIN tb_clase ON cm_producto.nclase = tb_clase.ncodclase
                                                WHERE lg_ordendet.id_orden = :id
                                                AND lg_ordendet.nestado = 1");

                $sql->execute(["id" => $id]);

                $rowCount = $sql->rowcount();

                $item = 1;

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()) {
                        $tipo = $rs['ntipmov'] == 37 ? 'B':'S';
                        $moneda = $rs['ncodmon'] == 20 ? "S/.":"$";
                        $total = $rs['nunitario'] * $rs['ncanti'];
                        $estado = "";

                        if ( $rs['ncantidad'] == 0 ) {
                            $estado = "RECEPCIONADA";
                        }elseif ( $rs['nfirmaOpe'] && $rs['nfirmaFin'] && $rs['nfirmaLog']) {
                            $estado = "APROBADA";
                        }


                        $salida .='<tr class="pointer">
                                        <td class="textoCentro">'.str_pad($item++,4,0,STR_PAD_LEFT).'</td>
                                        <td class="pl20px">'.$rs['ccodproy'].'</td>
                                        <td class="pl20px">'.$rs['proyecto'].'</td>
                                        <td class="pl20px">'.$rs['area'].'</td>
                                        <td class="textoCentro">'.$rs['fecha_registro'].'</td>
                                        <td class="textoCentro">'.$rs['cper'].'</td>
                                        <td class="textoCentro">'.$tipo.'</td>
                                        <td class="textoCentro">'.$rs['anio'].'</td>
                                        <td class="textoCentro">'.$rs['pedido'].'</td>
                                        <td class="textoCentro">'.$rs['orden'].'</td>
                                        <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                        <td class="pl20px">'.$rs['descripcion'].'</td>
                                        <td class="textoCentro">'.$rs['unidad'].'</td>
                                        <td class="pl20px">'.$rs['crazonsoc'].'</td>
                                        <td class="textoDerecha">'.$rs['ncanti'].'</td>
                                        <td class="textoDerecha">'.$rs['nunitario'].'</td>
                                        <td class="textoDerecha">'.$moneda.'</td>
                                        <td class="textoDerecha">'.$rs['ntotal'].'</td>
                                        <td class="textoDerecha">'.$rs['ntcambio'].'</td>
                                        <td class="textoDerecha">'.$rs['dolares'].'</td>
                                        <td class="textoDerecha">'.$rs['soles'].'</td>
                                        <td class="textoDerecha">'.$rs['FechaFin'].'</td>
                                        <td class="pl20px">'.$rs['grupo'].'</td>
                                        <td class="pl20px">'.$rs['clase'].'</td>
                                        <td class="pl20px">'.$rs['cviadireccion'].'</td>
                                        <td class="pl20px">'.$rs['tipo_pago'].'</td>
                                        <td class="textoCentro">'.$rs['fecha_entrega'].'</td>
                                        <td class="textoCentro">'.$rs['nplazo'].'</td>
                                        <td class="textoCentro">'.$rs['cnumdoc'].'</td>
                                        <td class="pl20px">'.$rs['cnumcot'].'</td>
                                        <td class="pl20px">'.$rs['nroparte'].'</td>
                                        <td class="pl20px">'.$rs['nregistro'].'</td>
                                        <td class="textoCentro">'.$estado.'</td>
                                        <td class="pl20px">'.$rs['observacion'].'</td>
                                    </tr>';
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function exportarValorizado($detalles){
            require_once('public/PHPExcel/PHPExcel.php');
            try {
                $objPHPExcel = new PHPExcel();
                $objPHPExcel->getProperties()
                    ->setCreator("Sical")
                    ->setLastModifiedBy("Sical")
                    ->setTitle("Cargo Plan")
                    ->setSubject("Template excel")
                    ->setDescription("Reporte Valorizado")
                    ->setKeywords("Template excel");

                $cuerpo = array(
                    'font'  => array(
                    'bold'  => false,
                    'size'  => 7,
                ));

                $objWorkSheet = $objPHPExcel->createSheet(1);

                $objPHPExcel->setActiveSheetIndex(0);
                $objPHPExcel->getActiveSheet()->setTitle("Reporte Valorizado");

                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');
                $objWriter->save('public/documentos/reportes/catalogo.xlsx');
                $objPHPExcel->getActiveSheet()->mergeCells('A1:AQ1');
                $objPHPExcel->getActiveSheet()->setCellValue('A1','REPORTE VALORIZADO');

                $objPHPExcel->getActiveSheet()->getStyle('A1:AQ2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('A1:AQ2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(60);

                $objPHPExcel->getActiveSheet()->getColumnDimension("D")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("E")->setAutoSize(true);
                //$objPHPExcel->getActiveSheet()->getColumnDimension("K")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("L")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("M")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("O")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("T")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("U")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("V")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("AG")->setAutoSize(true);
                

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
                            ->getStyle('AE2:AH2')
                            ->getFill()
                            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB('FFFF00');


                $objPHPExcel->getActiveSheet()->getStyle('A1:AW2')->getAlignment()->setWrapText(true);

                $objPHPExcel->getActiveSheet()->setCellValue('A2','Items'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('B2','Codigo Proyecto'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('C2','Descripción/Proyecto Obra'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('D2','Area'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('E2','Fecha Registro'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('F2','Año Orden'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('G2','Tipo'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('H2','Año Pedido'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('I2','N° Pedido'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('J2','N° Orden'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('K2','Codigo del Bien/Servicio'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('L2','Descripcion del Bien/Servicio'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('M2','Unidad Medida'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('N2','Proveedor'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('O2','Cantidad'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('P2','Precio'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('Q2','Tipo Moneda'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('R2','Importe Total'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('S2','Tipo Cambio'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('T2','Contable ME Total Dólares'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('U2','Contable MN Total Soles'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('V2','Fecha Aprobación'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('W2','Clasificacion Grupo'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('X2','Clasificacion Clase'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('Y2','Dirección Proveedor'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('Z2','Forma Pago'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AA2','Fecha Entrega'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AB2','N° Días'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AC2','N° R.U.C.'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AD2','N° de Cotizacion'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AE2','N° Parte'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AF2','Código Maq.Equipo'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AG2','Estado'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('AH2','Observacion'); // esto cambia
               
                $fila = 3;
                $datos = json_decode($detalles);
                $nreg = count($datos);

                for ($i=0; $i < $nreg ; $i++) {
                    $objPHPExcel->getActiveSheet()->setCellValue('A'.$fila,$datos[$i]->item);
                    $objPHPExcel->getActiveSheet()->setCellValue('B'.$fila,$datos[$i]->codigoproyecto);
                    $objPHPExcel->getActiveSheet()->setCellValue('C'.$fila,$datos[$i]->descripcionproyecto);
                    $objPHPExcel->getActiveSheet()->setCellValue('D'.$fila,$datos[$i]->area);
                    $objPHPExcel->getActiveSheet()->setCellValue('E'.$fila,$datos[$i]->fecharegistro);
                    $objPHPExcel->getActiveSheet()->setCellValue('F'.$fila,$datos[$i]->anioorden);
                    $objPHPExcel->getActiveSheet()->setCellValue('G'.$fila,$datos[$i]->tipo);
                    $objPHPExcel->getActiveSheet()->setCellValue('H'.$fila,$datos[$i]->aniopedido);
                    $objPHPExcel->getActiveSheet()->setCellValue('I'.$fila,$datos[$i]->nroorden);
                    $objPHPExcel->getActiveSheet()->setCellValue('J'.$fila,$datos[$i]->nropedido);
                    $objPHPExcel->getActiveSheet()->setCellValue('K'.$fila,$datos[$i]->codigo_producto);
                    $objPHPExcel->getActiveSheet()->setCellValue('L'.$fila,$datos[$i]->descripcion);
                    $objPHPExcel->getActiveSheet()->setCellValue('M'.$fila,$datos[$i]->unidad);
                    $objPHPExcel->getActiveSheet()->setCellValue('N'.$fila,$datos[$i]->proveedor);
                    $objPHPExcel->getActiveSheet()->setCellValue('O'.$fila,$datos[$i]->cantidad);
                    $objPHPExcel->getActiveSheet()->setCellValue('P'.$fila,$datos[$i]->precio);
                    $objPHPExcel->getActiveSheet()->setCellValue('Q'.$fila,$datos[$i]->moneda);
                    $objPHPExcel->getActiveSheet()->setCellValue('R'.$fila,$datos[$i]->total);
                    $objPHPExcel->getActiveSheet()->setCellValue('S'.$fila,$datos[$i]->cambio);
                    $objPHPExcel->getActiveSheet()->setCellValue('T'.$fila,$datos[$i]->dolares);
                    $objPHPExcel->getActiveSheet()->setCellValue('U'.$fila,$datos[$i]->soles);
                    $objPHPExcel->getActiveSheet()->setCellValue('V'.$fila,$datos[$i]->aprobacion);
                    $objPHPExcel->getActiveSheet()->setCellValue('W'.$fila,$datos[$i]->grupo);
                    $objPHPExcel->getActiveSheet()->setCellValue('X'.$fila,$datos[$i]->clase);
                    $objPHPExcel->getActiveSheet()->setCellValue('Y'.$fila,$datos[$i]->direccion);
                    $objPHPExcel->getActiveSheet()->setCellValue('Z'.$fila,$datos[$i]->pago);
                    $objPHPExcel->getActiveSheet()->setCellValue('AA'.$fila,$datos[$i]->entrega);
                    $objPHPExcel->getActiveSheet()->setCellValue('AB'.$fila,$datos[$i]->dias);
                    $objPHPExcel->getActiveSheet()->setCellValue('AC'.$fila,$datos[$i]->ruc);
                    $objPHPExcel->getActiveSheet()->setCellValue('AD'.$fila,$datos[$i]->cotizacion);
                    $objPHPExcel->getActiveSheet()->setCellValue('AE'.$fila,$datos[$i]->parte);
                    $objPHPExcel->getActiveSheet()->setCellValue('AF'.$fila,$datos[$i]->equipo);
                    $objPHPExcel->getActiveSheet()->setCellValue('AG'.$fila,$datos[$i]->estado);
                    $objPHPExcel->getActiveSheet()->setCellValue('AH'.$fila,$datos[$i]->concepto);
                   

                    $fila++;
                }


                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');
                $objWriter->save('public/documentos/reportes/valorizado.xlsx');

                return array("documento"=>'public/documentos/reportes/valorizado.xlsx');

                exit();

            }  catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function listarAdjuntos($cc) {
            try{
                $sql = $this->db->connect()->prepare("SELECT
                                                        lg_regdocumento.creferencia,
                                                        lg_regdocumento.cmodulo,
                                                        UPPER(
                                                        SUBSTR( lg_regdocumento.cdocumento FROM 1 FOR 30 )) AS documento,
                                                        UPPER( lg_regdocumento.cdocumento ) AS mensaje,
                                                        LPAD( lg_regdocumento.nidrefer, 6, 0 ) AS orden,
                                                        lg_regdocumento.id_regmov,
                                                        lg_ordencab.ncodcos,
                                                        lg_regdocumento.fregsys 
                                                    FROM
                                                        lg_regdocumento
                                                        INNER JOIN lg_ordencab ON lg_regdocumento.nidrefer = lg_ordencab.id_regmov 
                                                    WHERE
                                                        lg_regdocumento.cmodulo = 'ORD' 
                                                        AND lg_regdocumento.nflgactivo = 1 
                                                        AND lg_ordencab.ncodcos = :cc
                                                    ORDER BY
                                                        lg_regdocumento.nidrefer DESC");
                $sql->execute(["cc"=>$cc]);

                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    $docData = array();
                    while($row=$sql->fetch(PDO::FETCH_ASSOC)){
                            $docData[] = $row;
                    }
                }

                return array("adjuntos"=>$docData);

            }catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }
    }
?>