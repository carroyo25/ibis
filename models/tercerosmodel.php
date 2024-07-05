<?php
    class TercerosModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function buscarDatosTerceros($doc,$cc) {
            $registrado = false;
            $url = "http://sicalsepcon.net/api/tercerosapi.php?doc=".$doc;
            
            $api = file_get_contents($url);

            $datos = json_decode($api);

            return array("datos" => $datos,
                        "anteriores"=>$this->kardexAnterior($doc,$cc));
        }

        private function kardexAnterior($d,$c){
            try {
                $salida = "";
                $sql = $this->db->connect()->prepare("SELECT
                                                        alm_consumo.idreg,
                                                        alm_consumo.reguser,
                                                        alm_consumo.idprod,
                                                        alm_consumo.cantsalida,
                                                        DATE_FORMAT(alm_consumo.fechasalida,'%d/%m/%Y') AS fechasalida,
                                                        DATE_FORMAT(alm_consumo.fechadevolucion,'%d/%m/%Y') AS fechadevolucion,
                                                        alm_consumo.nhoja,
                                                        alm_consumo.cisometrico,
                                                        alm_consumo.cobserentrega,
                                                        alm_consumo.cobserdevuelto,
                                                        alm_consumo.cestado,
                                                        alm_consumo.cserie,
                                                        alm_consumo.flgdevolver,
                                                        alm_consumo.cfirma,
                                                        cm_producto.ccodprod,
                                                        alm_consumo.nkardex,
                                                        alm_consumo.calmacen,
                                                        UPPER(cm_producto.cdesprod) AS cdesprod,
                                                        tb_unimed.cabrevia,COUNT(*) 
                                                    FROM
                                                        alm_consumo
                                                        LEFT JOIN cm_producto ON alm_consumo.idprod = cm_producto.id_cprod
                                                        LEFT JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed 
                                                    WHERE
                                                            nrodoc = :documento 
                                                        AND ncostos = :cc
                                                    GROUP BY
                                                            alm_consumo.idprod,
                                                            alm_consumo.fechasalida,
                                                            cm_producto.ccodprod,
                                                            alm_consumo.cantsalida,
                                                            alm_consumo.nhoja
                                                    HAVING COUNT(*) >= 1
                                                    ORDER BY alm_consumo.freg DESC" );
                $sql->execute(["documento"=>$d,"cc"=>$c]);
                $rowCount = $sql->rowCount();
                $item = 1;
                $salida ="No hay registros";
                $numero_item = $this->cantidadItems($d,$c);


                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()){

                        $marcado = $rs['flgdevolver'] == 1 ? "checked" : "";
                        $firma = "public/documentos/firmas/".$rs['cfirma'].".png";

                        $salida .= '<tr class="pointer" data-grabado="1" 
                                                        data-registrado="1" 
                                                        data-kardex = "'.$rs['nkardex'].'"
                                                        data-firma = "'.$rs['cfirma'].'"
                                                        data-devolucion = "'.$rs['fechadevolucion'].'"
                                                        data-firmadevolucion ="'.$rs['calmacen'].'"
                                                        data-registro="'.$rs['idreg'].'">
                                        <td class="textoDerecha">'.$rowCount--.'</td>
                                        <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                        <td class="pl5px">'.$rs['cdesprod'].'</td>
                                        <td class="textoCentro">'.$rs['cabrevia'].'</td>
                                        <td class="textoDerecha"><input value="'.$rs['cantsalida'].'" readonly></td>
                                        <td class="textoCentro"><input value="'.$rs['fechasalida'].'" readonly></td>
                                        <td class="textoCentro"><input value="'.$rs['nhoja'].'" readonly></td>
                                        <td class="pl5px"><input value="'.$rs['cisometrico'].'" readonly></td>
                                        <td class="pl5px"><input value="'.$rs['cobserentrega'].'" readonly></td>
                                        <td class="pl5px"><input value="'.$rs['cserie'].'" readonly></td>
                                        <td class="textoCentro"><input type="checkbox" '.$marcado.'></td>
                                        <td class="pl5px"><input value="'.$rs['cestado'].'" readonly></td>
                                        <td class="textoCentro">
                                            <div style ="width:110px !important; text-align:center">
                                                <img src = '.$firma.' style ="width:100% !important">
                                            </div>
                                        </td>
                                        <td class="textoCentro"><a href="'.$rs['idreg'].'"><i class="far fa-trash-alt"></i></a></td>
                                    </tr>';
                    }
                }

                return $salida;

            }catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }  
        }

        public function buscarProductosTerceros($codigo){
            try {
                $salida = "";
                $sql = $this->db->connect()->prepare("SELECT
                                                        cm_producto.id_cprod,
                                                        cm_producto.ccodprod,
                                                        UPPER(cm_producto.cdesprod) AS cdesprod,
                                                        tb_unimed.cabrevia,
                                                        NOW() AS fecha
                                                    FROM
                                                        cm_producto
                                                        INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed 
                                                    WHERE
                                                        cm_producto.flgActivo = 1 
                                                        AND cm_producto.ccodprod = :codigo 
                                                        AND cm_producto.ntipo = 37");
                $sql->execute(["codigo"=>$codigo]);

                $rowCount = $sql->rowCount();
                $result = $sql->fetchAll();

                if ($rowCount > 0) {
                    $respuesta = array("descripcion"=>$result[0]['cdesprod'],
                                        "codigo"=>$result[0]['ccodprod'],
                                        "unidad"=>$result[0]['cabrevia'],
                                        "idprod"=>$result[0]['id_cprod'],
                                        "fecha"=>$result[0]['fecha'],
                                        "registrado"=>true);
                }else{
                    $respuesta = array("registrado"=>false); 
                }

                return $respuesta;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function createExcelReport($nombre,$documento,$empresa,$detalles){
            try {
                require_once "public/PHPExcel/PHPExcel/IOFactory.php";

                require_once('public/PHPExcel/PHPExcel.php');
                $objPHPExcel = new PHPExcel();
                $objPHPExcel->getProperties()
                    ->setCreator("Sical")
                    ->setLastModifiedBy("Sical")
                    ->setTitle("Control Almacen")
                    ->setSubject("Template excel")
                    ->setDescription("Control Almacen")
                    ->setKeywords("Template excel");

                $objWorkSheet = $objPHPExcel->createSheet(1);

                $objPHPExcel->setActiveSheetIndex(0);
                $objPHPExcel->getActiveSheet()->setTitle("Kardex Terceros");

                //combinar celdas
                $objPHPExcel->getActiveSheet()->mergeCells('A1:K1');
                $objPHPExcel->getActiveSheet()->getStyle('A7:K7')->getAlignment()->setWrapText(true);
                $objPHPExcel->getActiveSheet()->getStyle('A1:K1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('A1:K1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $objPHPExcel->getActiveSheet()->getStyle('A7:K7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('A7:K7')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $objPHPExcel->getActiveSheet()->getStyle('C4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

                $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
                $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
                $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
                $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
                $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
                $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
                $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
                $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(25);

                //Titulo 
                $objPHPExcel->getActiveSheet()->setCellValue('A1','KARDEX TERCEROS');

                $objPHPExcel->getActiveSheet()->getStyle('B3:B5')->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle('A7:K7')->getFont()->setBold(true); 

                $objPHPExcel->getActiveSheet()->setCellValue('B3','NOMBRES');
                $objPHPExcel->getActiveSheet()->setCellValue('B4','N° DOCUMENTO');
                $objPHPExcel->getActiveSheet()->setCellValue('B5','EMPRESA');

                $objPHPExcel->getActiveSheet()->setCellValue('C3',$nombre);
                $objPHPExcel->getActiveSheet()->setCellValue('C4',$documento);
                $objPHPExcel->getActiveSheet()->setCellValue('C5',$empresa);

                $objPHPExcel->getActiveSheet()->setCellValue('A7','ITEM');
                $objPHPExcel->getActiveSheet()->setCellValue('B7','CODIGO');
                $objPHPExcel->getActiveSheet()->setCellValue('C7','DESCRIPCION');
                $objPHPExcel->getActiveSheet()->setCellValue('D7','UND.');
                $objPHPExcel->getActiveSheet()->setCellValue('E7','CANT.');
                $objPHPExcel->getActiveSheet()->setCellValue('F7','FECHA ENTREGA');
                $objPHPExcel->getActiveSheet()->setCellValue('G7','N° HOJA');
                $objPHPExcel->getActiveSheet()->setCellValue('H7','ISOMETRICOS');
                $objPHPExcel->getActiveSheet()->setCellValue('I7','OBSERVACIONES');
                $objPHPExcel->getActiveSheet()->setCellValue('J7','SERIE');
                $objPHPExcel->getActiveSheet()->setCellValue('K7','ESTADO');

                $objPHPExcel->getActiveSheet()
                    ->getStyle('A7:K7')
                    ->getFill()
                    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setRGB('0F73D6');

                $fila = 8;
                $datos = json_decode($detalles);
                $nreg = count($datos);
                $item = 1;
                $fecha = "";

                for ($i=0; $i < $nreg; $i++) { 
                    $objPHPExcel->getActiveSheet()->setCellValue('A'.$fila,$item++);
                    $objPHPExcel->getActiveSheet()->setCellValue('B'.$fila,$datos[$i]->codigo);
                    $objPHPExcel->getActiveSheet()->setCellValue('C'.$fila,$datos[$i]->descripcion);
                    $objPHPExcel->getActiveSheet()->setCellValue('D'.$fila,$datos[$i]->unidad);
                    $objPHPExcel->getActiveSheet()->setCellValue('E'.$fila,$datos[$i]->cantidad);
                    $objPHPExcel->getActiveSheet()->setCellValue('F'.$fila,$datos[$i]->fecha);
                    $objPHPExcel->getActiveSheet()->setCellValue('G'.$fila,$datos[$i]->hoja);
                    $objPHPExcel->getActiveSheet()->setCellValue('H'.$fila,$datos[$i]->isometrico);
                    $objPHPExcel->getActiveSheet()->setCellValue('I'.$fila,$datos[$i]->observac);
                    $objPHPExcel->getActiveSheet()->setCellValue('J'.$fila,$datos[$i]->serie);
                    $objPHPExcel->getActiveSheet()->setCellValue('K'.$fila,$datos[$i]->estado);
                    
                    $fila++;
                }

                $fileName = $nombre.'_'.$empresa.'.xlsx';

                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');
                $objWriter->save('public/documentos/temp/'.$fileName);
   
                return array("documento"=>'public/documentos/temp/'.$fileName);

            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }
    }
?>