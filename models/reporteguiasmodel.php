<?php
    class ReporteGuiasModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarGuias($datos) {
            try {
                // Validate required fields
                if (!isset($datos['anio']) || empty($datos['anio'])) {
                    throw new Exception("El campo 'anio' es obligatorio.");
                }
        
                // Sanitize inputs
                $anio  = (int) $datos['anio'];
                $guia  = !empty($datos['guia']) ? '%' . $datos['guia'] . '%' : '%';
                $sunat = !empty($datos['sunat']) ? '%' . $datos['sunat'] . '%' : '%';
        
                $sql = $this->db->connect()->prepare("
                    SELECT
                        lg_guias.cnumguia,
                        lg_guias.corigen,
                        lg_guias.cdestino,
                        DATE_FORMAT(lg_guias.ftraslado,'%d/%m/%Y') ftraslado,
                        DATE_FORMAT(lg_guias.freg,'%d/%m/%Y') freg,
                        lg_guias.guiasunat,
                        YEAR(lg_guias.freg) AS anio,
                        lg_guias.cenvio,
                        lg_guias.cobserva
                    FROM
                        lg_guias 
                    WHERE
                        lg_guias.nflgActivo = 1 
                        AND IFNULL(lg_guias.guiasunat,'') LIKE :sunat
                        AND lg_guias.cnumguia LIKE :guia
                        AND YEAR(lg_guias.freg) = :anio
                    ORDER BY
                        lg_guias.freg DESC");
        
                $sql->execute([
                    "sunat" => $sunat,
                    "guia"  => $guia,
                    "anio"  => $anio
                ]);
        
                $docData = $sql->fetchAll(PDO::FETCH_ASSOC);
        
                if (empty($docData)) {
                    return [
                        "success" => false,
                        "message" => "No se encontraron registros."
                    ];
                }
        
                return [
                    "success" => true,
                    "datos"   => $docData
                ];
        
            } catch (PDOException $e) {
                error_log("Database Error: " . $e->getMessage());
                return [
                    "success" => false,
                    "message" => "Error en la base de datos."
                ];
            } catch (Exception $e) {
                error_log("General Error: " . $e->getMessage());
                return [
                    "success" => false,
                    "message" => $e->getMessage()
                ];
            }
        }

        public function exportarExcelGuias($detalles){
            require_once('public/PHPExcel/PHPExcel.php');
            try {
                $objPHPExcel = new PHPExcel();
                $objPHPExcel->getProperties()
                    ->setCreator("Sical")
                    ->setLastModifiedBy("Sical")
                    ->setTitle("Cargo Plan")
                    ->setSubject("Template excel")
                    ->setDescription("Reporte de Guias de Remision")
                    ->setKeywords("Template excel");

                $cuerpo = array(
                    'font'  => array(
                    'bold'  => false,
                    'size'  => 7,
                ));

                $objWorkSheet = $objPHPExcel->createSheet(1);

                $objPHPExcel->setActiveSheetIndex(0);
                $objPHPExcel->getActiveSheet()->setTitle("Reporte Guias de Remisión");

                $objPHPExcel->getActiveSheet()->mergeCells('A1:C3');
                $objPHPExcel->getActiveSheet()->mergeCells('D1:D3');
                $objPHPExcel->getActiveSheet()->mergeCells('E1:F3');

                $objPHPExcel->getActiveSheet()->setCellValue('D1','REPORTE GUIAS');
                $objPHPExcel->getActiveSheet()->setCellValue('E1','PSPC-420-X-IN-002-FR-004');

                $objPHPExcel->getActiveSheet()->getStyle('A1:F2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('A1:F2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $objPHPExcel->getActiveSheet()->getRowDimension('4')->setRowHeight(60);
                $objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(40);

                $objPHPExcel->getActiveSheet()->getStyle('A4:F4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('A4:F4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                // Aquí tenías comentado el setAutoSize
                $objPHPExcel->getActiveSheet()->getColumnDimension("A")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("B")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("C")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("E")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("F")->setAutoSize(true);

                $objPHPExcel->getActiveSheet()
                            ->getStyle('A4:F4')
                            ->getFill()
                            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB('BFCDDB');

                $objPHPExcel->getActiveSheet()->getStyle('A1:F2')->getAlignment()->setWrapText(true);

                $objPHPExcel->getActiveSheet()->setCellValue('A4','GUIA INTERNA');
                $objPHPExcel->getActiveSheet()->setCellValue('B4','FECHA ENVIO');
                $objPHPExcel->getActiveSheet()->setCellValue('C4','AÑO');
                $objPHPExcel->getActiveSheet()->setCellValue('D4','OBSERVACIONES');
                $objPHPExcel->getActiveSheet()->setCellValue('E4','GUIA SUNAT');
                $objPHPExcel->getActiveSheet()->setCellValue('F4','TRANSPORTE');


                $fila = 5;
                $datos = json_decode($detalles);
                $nreg = count($datos);

                for ($i=0; $i < $nreg ; $i++) {
                    $objPHPExcel->getActiveSheet()->setCellValue('A'.$fila,$datos[$i]->guia);
                    $objPHPExcel->getActiveSheet()->setCellValue('B'.$fila,$datos[$i]->envio);
                    $objPHPExcel->getActiveSheet()->setCellValue('C'.$fila,$datos[$i]->anio);
                    $objPHPExcel->getActiveSheet()->setCellValue('D'.$fila,$datos[$i]->observaciones);
                    $objPHPExcel->getActiveSheet()->setCellValue('E'.$fila,$datos[$i]->sunat);
                    $objPHPExcel->getActiveSheet()->setCellValue('F'.$fila,$datos[$i]->transporte);

                    $fila++;
                }

                // ======================================================
                // LO ÚNICO QUE SE AGREGA VA AQUÍ ABAJO, ANTES DE GUARDAR:
                // ======================================================

                // 1. Ancho de la columna D para que no se desborde
                $objPHPExcel->getActiveSheet()->getColumnDimension("D")->setWidth(80);

                // 2. Imagen PNG anclada en A1:C4 (Ajusta la ruta si es necesario)
                $rutaLogo = 'public/img/logo.png';
                if (file_exists($rutaLogo)) {
                    $objDrawing = new PHPExcel_Worksheet_Drawing();
                    $objDrawing->setName('Logo');
                    $objDrawing->setDescription('Logo');
                    $objDrawing->setPath($rutaLogo);
                    $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
                    $objDrawing->setCoordinates('A1');
                    $objDrawing->setOffsetX(5);
                    $objDrawing->setOffsetY(5);
                    $objDrawing->setResizeProportional(true);
                    $objDrawing->setWidth(180);
                }

                // ======================================================
                // GUARDAR
                // ======================================================
                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');
                $objWriter->save('public/documentos/reportes/reporteguias.xlsx');

                return array("documento"=>'public/documentos/reportes/reporteguias.xlsx');

                exit();

            }  catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }
    }
?>