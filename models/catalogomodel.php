<?php
    class CatalogoModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarItems(){
            try {
                $salida = "";
                $sql = $this->db->connect()->query("SELECT
                                                    cm_producto.id_cprod,
                                                    cm_producto.ccodprod,
                                                    UPPER(cm_producto.cdesprod) AS cdesprod,
                                                    cm_producto.flgActivo,
                                                    tb_parametros.cdescripcion AS tipo,
                                                    tb_unimed.cabrevia 
                                                FROM
                                                    cm_producto
                                                    INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed
                                                    INNER JOIN tb_parametros ON cm_producto.ntipo = tb_parametros.nidreg 
                                                WHERE
                                                    cm_producto.flgActivo = 1
                                                ORDER BY cdesprod ASC");
                $sql->execute();
                $rc = $sql->rowcount();
                $item = 1;

                if ($rc > 0){
                    while( $rs = $sql->fetch()) {
                        $salida .='<tr data-id="'.$rs['id_cprod'].'" class="pointer">
                                        <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                        <td class="textoCentro '.strtolower($rs['tipo']).'">'.$rs['tipo'].'</td>
                                        <td class="pl20px">'.$rs['cdesprod'].'</td>
                                        <td class="textoCentro">'.$rs['cabrevia'].'</td>
                                    </tr>';
                        $item++;
                    }
                }

                return $salida;

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function buscarItemsPalabra($criterio){
            try {
                $salida = "";
                $sql = $this->db->connect()->prepare("SELECT
                                                    cm_producto.id_cprod,
                                                    cm_producto.ccodprod,
                                                    UPPER(cm_producto.cdesprod) AS cdesprod,
                                                    cm_producto.flgActivo,
                                                    tb_parametros.cdescripcion AS tipo,
                                                    tb_unimed.cabrevia 
                                                FROM
                                                    cm_producto
                                                    INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed
                                                    INNER JOIN tb_parametros ON cm_producto.ntipo = tb_parametros.nidreg 
                                                WHERE
                                                    cm_producto.flgActivo = 1 AND
                                                    cm_producto.cdesprod LIKE :criterio");
                $sql->execute(["criterio"=>"%".$criterio."%"]);
                $rc = $sql->rowcount();
                $item = 1;

                if ($rc > 0){
                    while( $rs = $sql->fetch()) {
                        $salida .='<tr data-id="'.$rs['id_cprod'].'" class="pointer">
                                        <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                        <td class="textoCentro '.strtolower($rs['tipo']).'">'.$rs['tipo'].'</td>
                                        <td class="pl20px">'.$rs['cdesprod'].'</td>
                                        <td class="textoCentro">'.$rs['cabrevia'].'</td>
                                        <td class="textoCentro"><a href="'.$rs['id_cprod'].'"><i class="fas fa-trash-alt"></i></a></td>
                                    </tr>';
                        $item++;
                    }
                }

                return $salida;

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function buscarItemsCodigo($criterio){
            try {
                $salida = "";
                $sql = $this->db->connect()->prepare("SELECT
                                                    cm_producto.id_cprod,
                                                    cm_producto.ccodprod,
                                                    UPPER(cm_producto.cdesprod) AS cdesprod,
                                                    cm_producto.flgActivo,
                                                    tb_parametros.cdescripcion AS tipo,
                                                    tb_unimed.cabrevia 
                                                FROM
                                                    cm_producto
                                                    INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed
                                                    INNER JOIN tb_parametros ON cm_producto.ntipo = tb_parametros.nidreg 
                                                WHERE
                                                    cm_producto.flgActivo = 1 AND
                                                    cm_producto.ccodprod LIKE :criterio");
                $sql->execute(["criterio"=>"%".$criterio."%"]);
                $rc = $sql->rowcount();
                $item = 1;

                if ($rc > 0){
                    while( $rs = $sql->fetch()) {
                        $salida .='<tr data-id="'.$rs['id_cprod'].'" class="pointer">
                                        <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                        <td class="textoCentro '.strtolower($rs['tipo']).'">'.$rs['tipo'].'</td>
                                        <td class="pl20px">'.$rs['cdesprod'].'</td>
                                        <td class="textoCentro">'.$rs['cabrevia'].'</td>
                                        <td class="textoCentro"><a href="'.$rs['id_cprod'].'"><i class="fas fa-trash-alt"></i></a></td>
                                    </tr>';
                        $item++;
                    }
                }

                return $salida;

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function exportarCatalogo(){
            try {
                require_once('public/PHPExcel/PHPExcel.php');
                $objPHPExcel = new PHPExcel();
                $objPHPExcel->getProperties()
                ->setCreator("Karen Montalvo")
                ->setLastModifiedBy("Karen Montalvo")
                ->setTitle("Matriz de identificacion de requisitos legales")
                ->setSubject("Template excel")
                ->setDescription("Matriz de identificación de requisitos legales")
                ->setKeywords("Template excel");

                $cuerpo = array(
                    'font'  => array(
                    'bold'  => false,
                    'size'  => 7,
                ));

                $hojas = ["Bienes","Servicios"];

                $objWorkSheet = $objPHPExcel->createSheet(1);

                $objPHPExcel->setActiveSheetIndex(0);
                $objPHPExcel->getActiveSheet()->setTitle("Bienes");


                $objPHPExcel->setActiveSheetIndex(1);
                $objPHPExcel->getActiveSheet()->setTitle("Servicios");

                for ($ap=0; $ap <= 1 ; $ap++) { 
                    $objPHPExcel->setActiveSheetIndex($ap);

                    //alineacion
                        $objPHPExcel->getActiveSheet()->getStyle('C1:I5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $objPHPExcel->getActiveSheet()->getStyle('C1:I5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                        $objPHPExcel->getActiveSheet()->getStyle('A1:AJ5')->getAlignment()->setWrapText(true);

                        $objPHPExcel->getActiveSheet()->getStyle('A8:AJ9')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $objPHPExcel->getActiveSheet()->getStyle('A8:AJ9')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                        $objPHPExcel->getActiveSheet()->getStyle('A6:J6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $objPHPExcel->getActiveSheet()->getStyle('A6:J6')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                        //estilo de fuentes
                        $objPHPExcel->getActiveSheet()->getStyle('A6:J1000')->applyFromArray($cuerpo);

                        //ancho de columnas
                        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
                        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
                        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(27);
                        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(27);
                        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
                        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
                        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
                        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
                        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
                        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
                        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(40);

                        //combinar celdas
                        $objPHPExcel->getActiveSheet()->mergeCells('A1:C1');
                        

                        //Titulo 
                        $objPHPExcel->getActiveSheet()->setCellValue('C1','CATALOGO DE BIENES');

                        $objPHPExcel->getActiveSheet()
                            ->getStyle('A8:J9')
                            ->getFill()
                            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB('FDE9D9');

                        $objPHPExcel->getActiveSheet()->setCellValue('A4','CODIDO'); // esto cambia
                        $objPHPExcel->getActiveSheet()->setCellValue('B4','DESCRIPCION'); // esto cambia
                        $objPHPExcel->getActiveSheet()->setCellValue('C4','UNIDAD'); // esto cambia

                }

                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');
                $objWriter->save('public/documentos/reportes/catalogo.xlsx');
                exit();

                return 'public/documentos/reportes/catalogo.xlsx';

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }
    }
?>