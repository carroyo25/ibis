<?php
    class CatalogoModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function buscarItemsPalabra($criterio){
            try {
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
                                                    cm_producto.cdesprod LIKE :criterio
                                                ORDER BY cdesprod ASC");
                $sql->execute(["criterio"=>"%".$criterio."%"]);
                $rc = $sql->rowcount();

                if ($rc > 0){
                    while( $rs = $sql->fetch(PDO::FETCH_ASSOC)) {
                        $productos[] = $rs;
                    }
                }

                return array("productos"=>$productos);

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function buscarItemsCodigo($criterio){
            try {
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
                                                    cm_producto.ccodprod LIKE :criterio
                                                ORDER BY cdesprod ASC");
                $sql->execute(["criterio"=>"%".$criterio."%"]);
                $rc = $sql->rowcount();

                if ($rc > 0){
                    while( $rs = $sql->fetch(PDO::FETCH_ASSOC)) {
                        $productos[] = $rs;
                    }
                }

                return array("productos"=>$productos);

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
                    
                        //combinar celdas
                        $objPHPExcel->getActiveSheet()->mergeCells('A1:F1');
                        
                        //Titulo 
                        $objPHPExcel->getActiveSheet()->setCellValue('A1','CATALOGO DE BIENES');

                        $objPHPExcel->getActiveSheet()
                            ->getStyle('A1:F4')
                            ->getFill()
                            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB('FDE9D9');

                        $objPHPExcel->getActiveSheet()->setCellValue('A4','CODIDO'); // esto cambia
                        $objPHPExcel->getActiveSheet()->setCellValue('B4','DESCRIPCION'); // esto cambia
                        $objPHPExcel->getActiveSheet()->setCellValue('C4','UNIDAD'); // esto cambia
                        $objPHPExcel->getActiveSheet()->setCellValue('D4','GRUPO'); // esto cambia
                        $objPHPExcel->getActiveSheet()->setCellValue('E4','CLASE'); // esto cambia
                        $objPHPExcel->getActiveSheet()->setCellValue('F4','FAMILIA'); // esto cambia

                        $fila = 5;
                        $productos = $this->productos(37);
                        $nreg = count($productos);

                        for ($i=0; $i < $nreg; $i++) { 
                            $objPHPExcel->getActiveSheet()->setCellValue('A'.$fila,$productos[$i]['ccodprod']);
                            $fila++;
                        }

                }

                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');
                $objWriter->save('public/documentos/reportes/catalogo.xlsx');
                exit();

                return array("documento"=>'public/documentos/reportes/catalogo.xlsx');
               
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function listarItemsScroll($pagina,$cantidad){
            try {
                $inicio = ($pagina - 1) * $cantidad;
                $limite = $this->contarItems();

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
                                                ORDER BY cdesprod ASC
                                                LIMIT $inicio,$cantidad");
                $sql->execute();
                $rc = $sql->rowcount();
                $item = 1;

                if ($rc > 0){
                    while( $rs = $sql->fetch()) {
                        $productos[] = $rs;
                    }
                }

                return array("productos"=>$productos,
                            'quedan'=>($inicio + $cantidad) < $limite);

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function contarItems(){
            try {
                $sql = $this->db->connect()->query("SELECT COUNT(*) AS regs FROM cm_producto WHERE flgActivo = 1");
                $sql->execute();
                $filas = $sql->fetch();

                return $filas['regs'];
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function productos($tipo){
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                    cm_producto.ccodprod,
                                                    UPPER( cm_producto.cdesprod ) AS cdesprod,
                                                    tb_parametros.cdescripcion AS tipo,
                                                    tb_unimed.cabrevia,
                                                    UPPER( tb_grupo.cdescrip ) AS GRUPO,
                                                    UPPER( tb_clase.cdescrip ) AS CLASE,
                                                    UPPER( tb_familia.cdescrip ) AS FAMILIA 
                                                FROM
                                                    cm_producto
                                                    INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed
                                                    INNER JOIN tb_parametros ON cm_producto.ntipo = tb_parametros.nidreg
                                                    INNER JOIN tb_grupo ON cm_producto.ngrupo = tb_grupo.ncodgrupo
                                                    INNER JOIN tb_clase ON cm_producto.nclase = tb_clase.ncodclase
                                                    INNER JOIN tb_familia ON cm_producto.nfam = tb_familia.ncodfamilia 
                                                WHERE
                                                    cm_producto.flgActivo = 1 
                                                    AND cm_producto.ntipo =:tipo 
                                                ORDER BY
                                                    cm_producto.cdesprod");
                $sql->execute(["tipo"=>$tipo]);
                $rc = $sql->rowcount();

                if ($rc > 0){
                    while( $rs = $sql->fetch(PDO::FETCH_ASSOC)) {
                        $productos[] = $rs;
                    }
                }

                return $productos;

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }
    }
?>