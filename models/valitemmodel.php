<?php
    class ValItemModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function consultarItems($parametros) {
            try {
                $costos     = $parametros['costosSearch'] == -1 ? "%" : $parametros['costosSearch'];
                $codigo     = $parametros['codigoBusqueda'] == "" ? "%" : "%".$parametros['codigoBusqueda']."%";
                $concepto   = $parametros['descripcionSearch'] == "" ? "%" : "%".$parametros['descripcionSearch']."%";

                $salida = "";

                $sql = $this->db->connect()->prepare("SELECT
                                                        lg_ordendet.nunitario,
                                                        lg_ordendet.ncanti,
                                                        lg_ordencab.ffechadoc,
                                                        LPAD( lg_ordencab.cnumero, 6, 0 ) AS orden,
                                                        lg_ordencab.ntcambio,
                                                        cm_producto.ccodprod,
                                                        UPPER( CONCAT_WS( ' ', cm_producto.cdesprod, tb_pedidodet.observaciones ) ) AS descripcion,
                                                        tb_unimed.cabrevia AS unidad,
                                                        tb_parametros.cabrevia AS moneda,
                                                        lg_ordendet.ncodcos,
                                                        lg_ordencab.ncodmon 
                                                    FROM
                                                        lg_ordendet
                                                        INNER JOIN lg_ordencab ON lg_ordendet.id_regmov = lg_ordencab.id_regmov
                                                        INNER JOIN cm_producto ON lg_ordendet.id_cprod = cm_producto.id_cprod
                                                        INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed
                                                        INNER JOIN tb_pedidodet ON lg_ordendet.niddeta = tb_pedidodet.iditem
                                                        INNER JOIN tb_parametros ON lg_ordencab.ncodmon = tb_parametros.nidreg 
                                                    WHERE
                                                        lg_ordencab.ntipmov = 37 
                                                        AND lg_ordendet.ncodcos LIKE :costos 
                                                        AND cm_producto.ccodprod LIKE :codigo
                                                        AND lg_ordendet.nestado = 1
                                                    ORDER BY
                                                        lg_ordencab.id_regmov ASC");
                $sql->execute(["costos"=>$costos,"codigo"=>$codigo]);
                $rowCount = $sql->rowcount();
                $item = 1;


                $total_soles = 0;
                $total_dolares = 0;

                if ($rowCount > 0){
                    while($rs = $sql->fetch()){

                        if ($rs['ncodmon'] == 20) {
                            $precio_soles =  $rs['nunitario'] ;
                            $precio_dolares = $rs['nunitario'] / $rs['ntcambio'];
                        }else {
                            $precio_soles =   $rs['nunitario'] * $rs['ntcambio'];
                            $precio_dolares =  $rs['nunitario'] ;
                        }

                        $salida .='<tr class="pointer">
                                        <td class="textoCentro">'.str_pad($item++,3,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                        <td class="pl20px">'.$rs['descripcion'].'</td>
                                        <td class="textoCentro">'.$rs['unidad'].'</td>
                                        <td class="textoCentro">'.$rs['moneda'].'</td>
                                        <td class="textoDerecha">'.$rs['ntcambio'].'</td>
                                        <td class="textoCentro">'.$rs['ffechadoc'].'</td>
                                        <td class="textoCentro">'.$rs['orden'].'</td>
                                        <td class="textoDerecha">'.$rs['ncanti'].'</td>
                                        <td class="textoDerecha">'.number_format($precio_soles,2).'</td>
                                        <td class="textoDerecha">'.number_format($precio_dolares,2).'</td>
                                    </tr>';

                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function exportarValorizadoItem($detalles){
            require_once('public/PHPExcel/PHPExcel.php');
            try {
                $objPHPExcel = new PHPExcel();
                $objPHPExcel->getProperties()
                    ->setCreator("Sical")
                    ->setLastModifiedBy("Sical")
                    ->setTitle("Cargo Plan")
                    ->setSubject("Template excel")
                    ->setDescription("Reporte Valorizado Items")
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
                $objPHPExcel->getActiveSheet()->mergeCells('A1:K1');
                $objPHPExcel->getActiveSheet()->setCellValue('A1','REPORTE VALORIZADO');

                $objPHPExcel->getActiveSheet()->getStyle('A1:K2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('A1:K2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(60);

                $objPHPExcel->getActiveSheet()->getColumnDimension("D")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("E")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("K")->setAutoSize(true);

                $objPHPExcel->getActiveSheet()
                            ->getStyle('A2:K2')
                            ->getFill()
                            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB('BFCDDB');

                $objPHPExcel->getActiveSheet()->getStyle('A1:AK2')->getAlignment()->setWrapText(true);

                $objPHPExcel->getActiveSheet()->setCellValue('A2','Items'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('B2','Codigo'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('C2','Descripción'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('D2','UND'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('E2','Moneda'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('F2','Tipo de Cambio'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('G2','Fecha Orden'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('H2','N° Orden'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('I2','Cantidad'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('J2','Precio Soles'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('K2','Precio Dólares'); // esto cambia
            
               
                $fila = 3;
                $datos = json_decode($detalles);
                $nreg = count($datos);

                for ($i=0; $i < $nreg ; $i++) {
                    $objPHPExcel->getActiveSheet()->setCellValue('A'.$fila,$datos[$i]->item);
                    $objPHPExcel->getActiveSheet()->setCellValue('B'.$fila,$datos[$i]->codigo);
                    $objPHPExcel->getActiveSheet()->setCellValue('C'.$fila,$datos[$i]->descripcion);
                    $objPHPExcel->getActiveSheet()->setCellValue('D'.$fila,$datos[$i]->unidad);
                    $objPHPExcel->getActiveSheet()->setCellValue('E'.$fila,$datos[$i]->moneda);
                    $objPHPExcel->getActiveSheet()->setCellValue('F'.$fila,$datos[$i]->cambio);
                    $objPHPExcel->getActiveSheet()->setCellValue('G'.$fila,$datos[$i]->fecha);
                    $objPHPExcel->getActiveSheet()->setCellValue('H'.$fila,$datos[$i]->orden);
                    $objPHPExcel->getActiveSheet()->setCellValue('I'.$fila,$datos[$i]->cantidad);
                    $objPHPExcel->getActiveSheet()->setCellValue('J'.$fila,$datos[$i]->soles);
                    $objPHPExcel->getActiveSheet()->setCellValue('K'.$fila,$datos[$i]->dolares);

                    $fila++;
                }


                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');
                $objWriter->save('public/documentos/reportes/valorizadoitems.xlsx');

                return array("documento"=>'public/documentos/reportes/valorizadoitems.xlsx');

                exit();

            }  catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        
    }
?>