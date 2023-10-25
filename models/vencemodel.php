<?php
    class VenceModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }


        public function listarVencimientos($costo,$codigo,$descripcion) {
            $cc = $costo == "" ? "%" : "%".$costo."%";
            $cod = $codigo == "" ? "%" : "%".$codigo."%";
            $descrip = $descripcion == "" ? "%" : "%".$descripcion."%";

            try {
                $sql = $this->db->connect()->prepare("SELECT
                alm_existencia.idreg,
                alm_existencia.codprod,
                alm_existencia.freg,
                DATE_FORMAT( alm_existencia.vence, '%d/%m/%Y' ) AS vence,
                cm_producto.ccodprod,
                UPPER( cm_producto.cdesprod ) AS producto,
                DATEDIFF( NOW(), alm_existencia.vence ) AS pasados,
                alm_existencia.nguia,
                alm_cabexist.idcostos,
                tb_proyectos.ccodproy,
                tb_unimed.cabrevia,
            IF
                ( alm_existencia.nropedido != 0, alm_existencia.nropedido, '-' ) AS orden,
            IF
                ( alm_existencia.tipo = 1, 'COMPRA', 'INVENTARIO' ) AS origen,
                alm_existencia.nroorden AS pedido,
                alm_existencia.cant_ingr,
                alm_existencia.cant_ord,
                s.consumo
            FROM
                alm_existencia
                LEFT JOIN cm_producto ON alm_existencia.codprod = cm_producto.id_cprod
                LEFT JOIN alm_cabexist ON alm_existencia.idregistro = alm_cabexist.idreg
                INNER JOIN tb_proyectos ON alm_cabexist.idcostos = tb_proyectos.nidreg
                INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed
                LEFT JOIN ( 
                    SELECT SUM( alm_consumo.cantsalida ) AS consumo, 
                                alm_consumo.idprod,alm_consumo.ncostos FROM alm_consumo 
                                WHERE alm_consumo.flgactivo = 1 AND alm_consumo.ncostos = 42
                                GROUP BY alm_consumo.idprod) AS s ON s.idprod = alm_existencia.codprod 
            WHERE
                alm_existencia.vence <> '' 
                AND DATEDIFF( NOW(), alm_existencia.vence ) > 1 
                AND alm_existencia.nflgActivo = 1 
                AND tb_proyectos.nidreg LIKE :cc
                AND cm_producto.cdesprod LIKE :descripcion 
                AND cm_producto.ccodprod LIKE :codigo 
            ORDER BY
                cm_producto.cdesprod ASC");
                 $sql->execute(["cc" => $cc,"codigo"=>$cod,"descripcion"=>$descrip]);

                 $rowcount = $sql->rowcount();
                 $item = 1;
                 $salida = "";
                 $estado = "";

                 if ($rowcount > 0) {
                     while ($rs = $sql->fetch()) {

                         $estado = intval($rs['pasados']);

                         if ($estado > 7) {
                             $alerta ="semaforoRojo";
                         }elseif ($estado == 7) {
                             $alerta ="semaforNaranja";
                         }elseif($estado < 7) {
                             $alerta ="semaforoVerde";
                         }

                         $salida .='<tr class="pointer" data-idexiste  ="'.$rs['idreg'].'" 
                                                        data-idproducto="'.$rs['codprod'].'"
                                                        data-idcostos  ="'.$rs['idcostos'].'">
                                         <td class="textoCentro">'.str_pad($item++,3,0,STR_PAD_LEFT).'</td>
                                         <td class="textoCentro">'.$rs['ccodproy'].'</td>
                                         <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                         <td class="pl20px">'.$rs['producto'].'</td>
                                         <td class="textoCentro">'.$rs['cabrevia'].'</td>
                                         <td class="textoCentro">'.$rs['orden'].'</td>
                                         <td class="textoCentro">'.$rs['origen'].'</td>
                                         <td class="textoCentro '.$alerta.'" style="color:#fff">'.$rs['vence'].'</td>
                                         <td class="textoDerecha">'.$rs['pasados'].'</td>
                                         <td class="textoDerecha">'.$rs['cant_ingr'].'</td>
                                         <td class="textoDerecha">'.$rs['consumo'].'</td>
                                         <td class="textoDerecha"></td>
                                     </tr>';
                     }
                 }

                 return $salida;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }
        
        public function detallarItem($producto,$costos){
            $salida = "";

            try {
                $sql=$this->db->connect()->prepare("SELECT
                                                            DATE_FORMAT( alm_existencia.vence, '%d/%m/%Y' ) AS fecha_vencimiento,
                                                            alm_cabexist.idcostos,
                                                            alm_cabexist.idreg,
                                                            tb_pedidodet.observaciones,
                                                            tb_pedidodet.idorden,
                                                            FORMAT( tb_pedidodet.cant_orden, 2 ) AS cant_orden,
                                                            DATE_FORMAT( alm_existencia.freg, '%d/%m/%Y' ) AS fecha_ingreso 
                                                        FROM
                                                            alm_existencia
                                                            INNER JOIN alm_cabexist ON alm_existencia.idregistro = alm_cabexist.idreg
                                                            INNER JOIN tb_proyectos ON alm_cabexist.idcostos = tb_proyectos.nidreg
                                                            INNER JOIN tb_pedidodet ON alm_existencia.idpedido = tb_pedidodet.iditem 
                                                        WHERE
                                                            alm_existencia.codprod = :id 
                                                            AND alm_existencia.vence != ''
                                                            AND alm_cabexist.idcostos = :cc");
                $sql->execute(["id"=>$producto,"cc"=>$costos]);

                $rowcount = $sql->rowCount();

                if ($rowcount>0) {
                    while ($rs = $sql->fetch()) {
                        $salida .='<tr class="pointer"> 
                                        <td class="pl20px">'.$rs['observaciones'].'</td>
                                        <td class="textoCentro">'.$rs['fecha_ingreso'].'</td>
                                        <td class="textoCentro">'.$rs['idorden'].'</td>
                                        <td class="textoCentro">'.$rs['fecha_vencimiento'].'</td>
                                        <td class="textoDerecha">'.$rs['cant_orden'].'</td>
                                    </tr>';
                    }
                }

                
                return $salida; 
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function exportExcel($detalles){
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
                    $objPHPExcel->getActiveSheet()->setTitle("Cargo Plan");
    
    
                    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');
                    $objPHPExcel->getActiveSheet()->mergeCells('A1:AP1');
                    $objPHPExcel->getActiveSheet()->setCellValue('A1','CARGO PLAN');
    
                    $objPHPExcel->getActiveSheet()->getStyle('A1:AP2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $objPHPExcel->getActiveSheet()->getStyle('A1:AP2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    
                    $objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(60);

                    $objPHPExcel->getActiveSheet()->getColumnDimension("A")->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension("B")->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension("C")->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension("D")->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension("E")->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension("F")->setAutoSize(true);

                    $objPHPExcel->getActiveSheet()->setCellValue('A2','Items'); // esto cambia
                    $objPHPExcel->getActiveSheet()->setCellValue('B2','Centro de Costos'); // esto cambia
                    $objPHPExcel->getActiveSheet()->setCellValue('C2','Codigo'); // esto cambia
                    $objPHPExcel->getActiveSheet()->setCellValue('D2','Descripción'); // esto cambia
                    $objPHPExcel->getActiveSheet()->setCellValue('E2','Unidad'); // esto cambia
                    $objPHPExcel->getActiveSheet()->setCellValue('F2','Fecha Vencimiento'); // esto cambia
                    $objPHPExcel->getActiveSheet()->setCellValue('G2','Dias'); // esto cambia

                    $objPHPExcel->getActiveSheet()
                            ->getStyle('A2:G2')
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
                        $objPHPExcel->getActiveSheet()->setCellValue('F'.$fila,$datos[$i]->vence);
                        $objPHPExcel->getActiveSheet()->setCellValue('G'.$fila,$datos[$i]->dias);

                        $fila++;
                    }


                    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');
                    $objWriter->save('public/documentos/reportes/vencimiento.xlsx');
    
                    return array("documento"=>'public/documentos/reportes/vencimiento.xlsx');
    
                    exit();
    
                    return $salida;


            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }
    }
?>