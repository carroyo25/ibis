<?php
    class DetalleCsModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarReporteConsumos($costo,$codigo,$descripcion) {
            
            $cc = $costo == "-1" ? "%" : "%".$costo."%";
            $cod = $codigo == "" ? "%" : "%".$codigo."%";
            $descrip = $descripcion == "" ? "%" : "%".$descripcion."%";

            $salida = "";

            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                        ibis.cm_producto.ccodprod,
                                                        UPPER( ibis.cm_producto.cdesprod ) AS producto,
                                                        ibis.tb_proyectos.ccodproy,
                                                        ibis.tb_proyectos.nidreg,
                                                        ibis.alm_consumo.cantsalida AS salida,
                                                        DATE_FORMAT( ibis.alm_consumo.fechasalida, '%d/%m/%Y' ) AS fechasalida,
                                                        ibis.alm_consumo.nrodoc,
                                                        CONCAT_WS( ' ', a.apellidos, a.nombres ) AS usuario,
                                                        ibis.tb_unimed.cabrevia,
                                                        ibis.alm_consumo.idreg,
                                                        ibis.cm_producto.ngrupo,
                                                        ibis.cm_producto.nclase,
                                                        ibis.cm_producto.nfam,
                                                        ibis.alm_consumo.cserie,
                                                        DATE_FORMAT( ibis.alm_consumo.fechadevolucion, '%d/%m/%Y' ) AS fechadevolucion
                                                    FROM
                                                        ibis.alm_consumo
                                                        LEFT JOIN ibis.cm_producto ON alm_consumo.idprod = cm_producto.id_cprod
                                                        LEFT JOIN ibis.tb_proyectos ON alm_consumo.ncostos = tb_proyectos.nidreg
                                                        LEFT JOIN ( SELECT DISTINCT rrhh.tabla_aquarius.dni, rrhh.tabla_aquarius.apellidos, rrhh.tabla_aquarius.nombres FROM rrhh.tabla_aquarius ) AS a ON ibis.alm_consumo.nrodoc = a.dni
                                                        LEFT JOIN ibis.tb_unimed ON ibis.cm_producto.nund = ibis.tb_unimed.ncodmed 
                                                    WHERE
                                                        alm_consumo.flgactivo = 1 
                                                        AND cm_producto.cdesprod LIKE :descripcion  
                                                        AND cm_producto.ccodprod LIKE :codigo 
                                                        AND alm_consumo.ncostos LIKE :cc 
                                                    ORDER BY
                                                        ibis.tb_proyectos.ccodproy ASC");

                $sql->execute(["cc" => $cc,"codigo"=>$cod,"descripcion"=>$descrip]);

                $rowcount = $sql->rowcount();
                $item = 1;

                if ($rowcount > 0) {
                     while ($rs = $sql->fetch()) {
                        $salida .='<tr class="pointer" data-idconsumo  ="'.$rs['idreg'].'" 
                                    data-idproducto="'.$rs['ccodprod'].'"
                                    data-idcostos  ="'.$rs['nidreg'].'">
                                        <td class="textoCentro">'.str_pad($item++,3,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.$rs['ccodproy'].'</td>
                                        <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                        <td class="pl20px">'.$rs['producto'].'</td>
                                        <td class="textoCentro">'.$rs['cabrevia'].'</td>
                                        <td class="textoDerecha">'.$rs['nrodoc'].'</td>
                                        <td class="pl20px">'.$rs['usuario'].'</td>
                                        <td class="textoCentro">'.$rs['fechasalida'].'</td>
                                        <td class="textoCentro">'.$rs['fechadevolucion'].'</td>
                                        <td class="textoCentro">'.$rs['cserie'].'</td>
                                        <td class="textoDerecha">'.number_format($rs['salida'],2,'.','').'</td>
                                    </tr>';
                     }
                }

                return $salida;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function exportExcelDetalleConsumo($detalles){
            require_once('public/PHPExcel/PHPExcel.php');
            try {
                $objPHPExcel = new PHPExcel();
                $objPHPExcel->getProperties()
                    ->setCreator("Sical")
                    ->setLastModifiedBy("Sical")
                    ->setTitle("Cargo Plan")
                    ->setSubject("Template excel")
                    ->setDescription("Reporte Vencimientos")
                    ->setKeywords("Template excel");

                    $objWorkSheet = $objPHPExcel->createSheet(1);

                    $objPHPExcel->setActiveSheetIndex(0);
                    $objPHPExcel->getActiveSheet()->setTitle("Listado Detallado de Cosumos");
    
    
                    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');
                    $objPHPExcel->getActiveSheet()->mergeCells('A1:AP1');
                    $objPHPExcel->getActiveSheet()->setCellValue('A1','RESGISTRO DE CONSUMOS DETALLADOS');
    
                    $objPHPExcel->getActiveSheet()->getStyle('A1:AP2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $objPHPExcel->getActiveSheet()->getStyle('A1:AP2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    
                    $objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(60);

                    $objPHPExcel->getActiveSheet()->getColumnDimension("A")->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension("B")->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension("C")->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension("D")->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension("E")->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension("F")->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension("G")->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension("H")->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension("I")->setAutoSize(true);

                    $objPHPExcel->getActiveSheet()->setCellValue('A2','Items'); // esto cambia
                    $objPHPExcel->getActiveSheet()->setCellValue('B2','Centro de Costos'); // esto cambia
                    $objPHPExcel->getActiveSheet()->setCellValue('C2','Codigo'); // esto cambia
                    $objPHPExcel->getActiveSheet()->setCellValue('D2','Descripcion'); // esto cambia
                    $objPHPExcel->getActiveSheet()->setCellValue('E2','Unidad'); // esto cambia
                    $objPHPExcel->getActiveSheet()->setCellValue('F2','N° Documento'); // esto cambia
                    $objPHPExcel->getActiveSheet()->setCellValue('G2','Nombre'); // esto cambia
                    $objPHPExcel->getActiveSheet()->setCellValue('H2','Fecha Salida'); // esto cambia
                    $objPHPExcel->getActiveSheet()->setCellValue('I2','Total Consumo'); // esto cambia

                    $objPHPExcel->getActiveSheet()
                            ->getStyle('A2:I2')
                            ->getFill()
                            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB('BFCDDB');

                    $fila = 3;
                    $datos = json_decode($detalles);
                    $nreg = count($datos);
    
                    for ($i=0; $i < $nreg ; $i++) {
                        $objPHPExcel->getActiveSheet()->setCellValue('A'.$fila,$datos[$i]->item++);
                        $objPHPExcel->getActiveSheet()->setCellValue('B'.$fila,$datos[$i]->costos);
                        $objPHPExcel->getActiveSheet()->setCellValue('D'.$fila,$datos[$i]->codigo);
                        $objPHPExcel->getActiveSheet()->setCellValue('C'.$fila,$datos[$i]->descripcion);
                        $objPHPExcel->getActiveSheet()->setCellValue('E'.$fila,$datos[$i]->unidad);
                        $objPHPExcel->getActiveSheet()->setCellValue('F'.$fila,$datos[$i]->documento);
                        $objPHPExcel->getActiveSheet()->setCellValue('G'.$fila,$datos[$i]->nombre);
                        $objPHPExcel->getActiveSheet()->setCellValue('H'.$fila,$datos[$i]->fecha);
                        $objPHPExcel->getActiveSheet()->setCellValue('I'.$fila,$datos[$i]->total);

                        $fila++;
                    }


                    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');
                    $objWriter->save('public/documentos/reportes/consumos.xlsx');
    
                    return array("documento"=>'public/documentos/reportes/consumos.xlsx');
    
                    exit();
    
                    return $salida;


            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }
    }
?>