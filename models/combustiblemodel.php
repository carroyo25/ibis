<?php
    class CombustibleModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listaConsumosCombustibles($parametros){
            try {

                $docData = [];

                $costo  = $parametros['cc'] != -1 ? $parametros['cc'] : "%";
                $nota   = $parametros['nota'] != "" ? "%".$parametros['nota']."%" : "%";
                $mes    = $parametros['mes'] != "" ? $parametros['mes'] : "%";
                $anio   = $parametros['anio'] != -1 ?$parametros['anio'] : "%";

                $sql = $this->db->connect()->prepare("SELECT
                                                    alm_combustible.idreg,
                                                    DATE_FORMAT(alm_combustible.fregistro,'%d/%m/%Y') AS fregistro,
                                                    alm_combustible.idalm,
                                                    alm_combustible.idtipo,
                                                    alm_combustible.idprod,
                                                    alm_combustible.notaingreso,
                                                    FORMAT(alm_combustible.ncantidad,2) AS ncantidad,
                                                    UPPER(alm_combustible.tobseritem) AS tobseritem,
                                                    UPPER(alm_combustible.cdocumento) AS cdocumento,
                                                    alm_combustible.idusuario,
                                                    alm_combustible.idproyecto,
                                                    alm_combustible.cguia,
                                                    UPPER(alm_combustible.tobserdocum) AS tobserdocum,
                                                    alm_combustible.nidref,
                                                    alm_combustible.idarea,
                                                    IFNULL(UPPER( tb_almacen.cdesalm ),'') AS cdesalm,
                                                    cm_producto.ccodprod,
                                                    cm_producto.cdesprod,
                                                    tb_unimed.cabrevia,
                                                    tb_proyectos.ccodproy,
                                                    UPPER( tb_proyectos.cdesproy ) AS desproy,
                                                    UPPER(tb_equipmtto.cregistro) AS cregistro,
                                                    tb_equipmtto.cdescripcion,
                                                    UPPER(tb_area.cdesarea) AS cdesarea,
                                                    MONTH(alm_combustible.fregistro) AS mes
                                                FROM
                                                    alm_combustible
                                                    LEFT JOIN tb_almacen ON alm_combustible.idalm = tb_almacen.ncodalm
                                                    LEFT JOIN cm_producto ON alm_combustible.idprod = cm_producto.id_cprod
                                                    LEFT JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed
                                                    LEFT JOIN tb_proyectos ON alm_combustible.idproyecto = tb_proyectos.nidreg
                                                    LEFT JOIN tb_equipmtto ON alm_combustible.nidref = tb_equipmtto.idreg
                                                    LEFT JOIN tb_area ON alm_combustible.idarea = tb_area.ncodarea 
                                                WHERE
                                                    alm_combustible.nflgactivo = 1
                                                    AND YEAR(alm_combustible.fregistro) LIKE :anio
                                                    AND MONTH(alm_combustible.fregistro) LIKE  :mes
                                                    AND IFNULL(alm_combustible.notaingreso,'') LIKE :nota
                                                    AND alm_combustible.idproyecto LIKE :costo");

                $sql->execute(["costo" =>$costo,"nota" =>$nota,"anio"=>'%',"mes"=>'%']);
                $rowCount = $sql->rowCount();
                
                if ($rowCount) {
                    $respuesta = true;
                    $i = 0;
                    
                    while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                        $docData[] = $row;
                    }
                }


                return array("datos"=>$docData,"usuarios"=>$this->usuariosAquarius());

            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function consultarCodigo($codigo){
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                            cm_producto.ccodprod,
                                                            cm_producto.id_cprod,
                                                            UPPER(cm_producto.cdesprod) AS cdesprod,
                                                            tb_unimed.cdesmed 
                                                        FROM
                                                            cm_producto
                                                            INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed
                                                        WHERE
                                                            cm_producto.ccodprod =:codigo");
                
                $sql->execute(['codigo' => $codigo]);

                $rowCount = $sql->rowCount();
                
                if ($rowCount) {
                    while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                        $docData[] = $row;
                    }
                }

                return array("datos"=>$docData);
                
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function buscarDocumento($doc) {
            $registrado = false;

            $url = "http://sicalsepcon.net/api/activesapi.php?documento=".$doc;
            
            $api = file_get_contents($url);

            $datos =  json_decode($api);
            $nreg = count($datos);

            $registrado = $nreg > 0 ? true: false;

            return array("datos" => $datos, "registrado"=>$registrado);
        }

        public function registrarCombustible($datos){
            try {
                $sql=$this->db->connect()->prepare("INSERT INTO alm_combustible 
                                                    SET alm_combustible.fregistro=:fecha,
                                                        alm_combustible.idalm=:idalmacen,
                                                        alm_combustible.idtipo=:tipo,
                                                        alm_combustible.idprod=:producto,
                                                        alm_combustible.ncantidad=:cantidad,
                                                        alm_combustible.tobseritem=:obseritem,
                                                        alm_combustible.cdocumento=:nrodoc,
                                                        alm_combustible.idusuario=:usuario,
                                                        alm_combustible.idproyecto=:proyecto,
                                                        alm_combustible.cguia=:guia,
                                                        alm_combustible.tobserdocum=:obserdoc,
                                                        alm_combustible.nidref=:referencia,
                                                        alm_combustible.idarea=:area");
                $sql->execute([
                    "fecha"=>$datos['fechaRegistro'],
                    "idalmacen"=>$datos['almacen'],
                    "tipo"=>$datos['tipo'],
                    "producto"=>$datos['codigo_producto'],
                    "cantidad"=>$datos['cantidad'],
                    "obseritem"=>strtoupper($datos['observacionesItem']),
                    "nrodoc"=>$datos['documento'],
                    "usuario"=>$datos['usuario'],
                    "proyecto"=>$datos['proyecto'],
                    "guia"=>null,
                    "obserdoc"=>strtoupper($datos['observacionesDocumento']),
                    "referencia"=>$datos['referencia'],
                    "area"=>$datos['area']]);

                return array("mensaje"=>'Consumo registrado');

            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function tipoCombustible(){
            try {
                $docData = [];

                $sql=$this->db->connect()->query("SELECT
                                                    cm_producto.ccodprod,
                                                    cm_producto.cdesprod,
                                                    cm_producto.id_cprod 
                                                FROM
                                                    cm_producto 
                                                WHERE
                                                    cm_producto.ngrupo = 8 
                                                    AND cm_producto.nclase = 60 
                                                    AND cm_producto.nfam = 234 
                                                    AND cm_producto.flgActivo = 1");

                while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                    $docData[] = $row;
                }

                return array("datos"=>$docData);

            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function generarReporte($item){
            $stock_inicial = $this->stock_inicial($item);
            $ingreso_mes_actual = $this->ingreso_mes_actual($item);
            $consumo_mes_actual = $this->consumo_mes_actual($item);
            $consolidado_anual = $this->consolidado_anual($item);
            $pivot_ingresos =  $this->pivotingresos($item);
            $pivot_salidas = "";

            return array("stock_inicial"=>$stock_inicial,
                         "ingreso_mes_actual"=>$ingreso_mes_actual,
                         "consumo_mes_actual"=>$consumo_mes_actual,
                         "consolidado_anual"=>$consolidado_anual,
                         "valores_ingreso"=>$pivot_ingresos,
                         "valores_salidas"=>$pivot_salidas);
        }

        public function exportarExcelCombustible($registros){
            try {
                require_once('public/PHPExcel/PHPExcel.php');
                $objPHPExcel = new PHPExcel();
                $objPHPExcel->getProperties()
                    ->setCreator("Sical")
                    ->setLastModifiedBy("Sical")
                    ->setTitle("Control Combustible")
                    ->setSubject("Template excel")
                    ->setDescription("Control Combustible")
                    ->setKeywords("Template excel");

                $objWorkSheet = $objPHPExcel->createSheet(1);

                $objPHPExcel->setActiveSheetIndex(0);
                $objPHPExcel->getActiveSheet()->setTitle("Combustible");

                //combinar celdas
                $objPHPExcel->getActiveSheet()->mergeCells('A1:P1');

                //alineacion
                $objPHPExcel->getActiveSheet()->getStyle('A1:R4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                //Titulo 
                $objPHPExcel->getActiveSheet()->setCellValue('A1','Kardex de Movimientos de Combustible');

                $objPHPExcel->getActiveSheet()
                    ->getStyle('A1:P3')
                    ->getFill()
                    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setRGB('C0DCC0');

                //ancho de columnas
                $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
                $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
                $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
                $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
                $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
                $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
                $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
                $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
                $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(15);
                $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(15);
                $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(15);
                $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(25);


                $objPHPExcel->getActiveSheet()->setCellValue('A3','ITEM'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('B3','FECHA REGISTRO'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('C3','ALMACEN'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('D3','TIPO DE MOVIMIENTO'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('E3','CODIGO'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('F3','DESCRIPCION'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('G3','UNIDAD'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('H3','CANTIDAD'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('I3','TRABAJADOR'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('J3','USUARIO REGISTRA');
                $objPHPExcel->getActiveSheet()->setCellValue('K3','PROYECTO'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('L3','OBSERVACIONES DEL ITEM'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('M3','OBSERVACION DEL DOCUMENTO'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('N3','AREA'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('O3','REFERENCIA ADICIONAL'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('P3','MES'); // esto cambia

                $objPHPExcel->getActiveSheet()->getStyle('A3:T3')->getAlignment()->setWrapText(true);

                $objPHPExcel->getActiveSheet()->getStyle('B')->getNumberFormat()->setFormatCode('dd/mm/yyyy');
                $objPHPExcel->getActiveSheet()->getStyle('H')->getNumberFormat()->setFormatCode('#,##0.00');
                

                $fila = 4;
                $datos = json_decode($registros);
                $nreg = count($datos);

                for ($i=0; $i < $nreg; $i++) { 
                    $objPHPExcel->getActiveSheet()->setCellValue('A'.$fila,$datos[$i]->numero);
                    $objPHPExcel->getActiveSheet()->setCellValue('B'.$fila,PHPExcel_Shared_Date::PHPToExcel($datos[$i]->emision));
                    $objPHPExcel->getActiveSheet()->setCellValue('C'.$fila,$datos[$i]->almacen);
                    $objPHPExcel->getActiveSheet()->setCellValue('D'.$fila,$datos[$i]->tipo);
                    $objPHPExcel->getActiveSheet()->setCellValue('E'.$fila,$datos[$i]->codigo);
                    $objPHPExcel->getActiveSheet()->setCellValue('F'.$fila,$datos[$i]->descripcion);
                    $objPHPExcel->getActiveSheet()->setCellValue('G'.$fila,$datos[$i]->unidad);
                    $objPHPExcel->getActiveSheet()->setCellValue('H'.$fila,$datos[$i]->cantidad);
                    $objPHPExcel->getActiveSheet()->setCellValue('I'.$fila,$datos[$i]->trabajador);
                    $objPHPExcel->getActiveSheet()->setCellValue('J'.$fila,$datos[$i]->usuario);
                    $objPHPExcel->getActiveSheet()->setCellValue('K'.$fila,$datos[$i]->proyecto);
                    $objPHPExcel->getActiveSheet()->setCellValue('L'.$fila,$datos[$i]->observaciones);
                    $objPHPExcel->getActiveSheet()->setCellValue('M'.$fila,$datos[$i]->documento);
                    $objPHPExcel->getActiveSheet()->setCellValue('N'.$fila,$datos[$i]->area);
                    $objPHPExcel->getActiveSheet()->setCellValue('O'.$fila,$datos[$i]->referencia);
                    $objPHPExcel->getActiveSheet()->setCellValue('P'.$fila,$datos[$i]->mes);

                    
                    $fila++;
                }

                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');
                $objWriter->save('public/documentos/reportes/combustible.xlsx');
    
                return array("documento"=>'public/documentos/reportes/combustible.xlsx');
    
                exit();
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function stock_inicial($item){
            try {
                $sql=$this->db->connect()->prepare("SELECT
                                                alm_recepdet.id_cprod,
                                                alm_recepcab.cper,
                                                alm_recepcab.cmes,
                                                alm_recepcab.ncodpry,
                                                SUM(alm_recepdet.ncantidad) AS ingresos_mes_anterior
                                            FROM
                                                alm_recepdet
                                                INNER JOIN alm_recepcab ON alm_recepdet.id_regalm = alm_recepcab.id_regalm 
                                            WHERE
                                                alm_recepdet.nflgactivo = 1 
                                                AND alm_recepdet.id_cprod =:item
                                                AND alm_recepcab.cper = IF ( MONTH(NOW()) = 1,YEAR(NOW())-1,YEAR(NOW()))
                                                AND alm_recepcab.cmes = IF ( MONTH(NOW()) = 1,12,MONTH(NOW())-1)");
                $sql->execute(["item"=>$item]);

                $result = $sql->fetchAll();

                return $result[0]['ingresos_mes_anterior'];


            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function ingreso_mes_actual($item){
            try {
                $sql=$this->db->connect()->prepare("SELECT
                                                alm_recepdet.id_cprod,
                                                alm_recepcab.cper,
                                                alm_recepcab.cmes,
                                                alm_recepcab.ncodpry,
                                                FORMAT(SUM(alm_recepdet.ncantidad),2) AS ingresos_mes_actual
                                            FROM
                                                alm_recepdet
                                                INNER JOIN alm_recepcab ON alm_recepdet.id_regalm = alm_recepcab.id_regalm 
                                            WHERE
                                                alm_recepdet.nflgactivo = 1 
                                                AND alm_recepdet.id_cprod =:item
                                                AND alm_recepcab.cper = YEAR(NOW())
                                                AND alm_recepcab.cmes = MONTH(NOW())");
                $sql->execute(["item"=>$item]);

                $result = $sql->fetchAll();

                return $result[0]['ingresos_mes_actual'];


            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function consumo_mes_actual($item){
            try {
                $sql=$this->db->connect()->prepare("SELECT
                                                    SUM(alm_combustible.ncantidad) AS consumo_mes,
                                                    alm_combustible.idprod 
                                                FROM
                                                    alm_combustible 
                                                WHERE
                                                    alm_combustible.idprod = :item 
                                                    AND alm_combustible.nflgactivo = 1
                                                    AND MONTH(alm_combustible.fregistro) = MONTH(NOW())
                                                    AND YEAR(alm_combustible.fregistro) = YEAR(NOW())");
                $sql->execute(["item"=>$item]);

                $result = $sql->fetchAll();

                return $result[0]['consumo_mes'];


            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function consolidado_anual($item){
            try {
                $sql=$this->db->connect()->prepare("SELECT
                                                    alm_recepdet.id_cprod,
                                                    alm_recepcab.cper,
                                                    alm_recepcab.cmes,
                                                    alm_recepcab.ncodpry,
                                                    SUM( alm_recepdet.ncantidad ) AS consolidado_anual 
                                                FROM
                                                    alm_recepdet
                                                    INNER JOIN alm_recepcab ON alm_recepdet.id_regalm = alm_recepcab.id_regalm 
                                                WHERE
                                                    alm_recepdet.nflgactivo = 1 
                                                    AND alm_recepdet.id_cprod =:item
                                                    AND alm_recepcab.cper = YEAR(NOW())");
                $sql->execute(["item"=>$item]);

                $result = $sql->fetchAll();

                return $result[0]['consolidado_anual'];


            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function pivotIngresos($item){
            try {
                $docData = [];
                //este es la consulta para pivotear tablas
                $sql = $this->db->connect()->prepare("SELECT
                                                    SUM( CASE WHEN MONTH ( alm_recepdet.fregsys ) = 1 THEN alm_recepdet.ncantidad ELSE 0 END ) AS jan,
                                                    SUM( CASE WHEN MONTH ( alm_recepdet.fregsys ) = 2 THEN alm_recepdet.ncantidad ELSE 0 END ) AS feb,
                                                    SUM( CASE WHEN MONTH ( alm_recepdet.fregsys ) = 3 THEN alm_recepdet.ncantidad ELSE 0 END ) AS mar,
                                                    SUM( CASE WHEN MONTH ( alm_recepdet.fregsys ) = 4 THEN alm_recepdet.ncantidad ELSE 0 END ) AS apr,
                                                    SUM( CASE WHEN MONTH ( alm_recepdet.fregsys ) = 5 THEN alm_recepdet.ncantidad ELSE 0 END ) AS may,
                                                    SUM( CASE WHEN MONTH ( alm_recepdet.fregsys ) = 6 THEN alm_recepdet.ncantidad ELSE 0 END ) AS jun,
                                                    SUM( CASE WHEN MONTH ( alm_recepdet.fregsys ) = 7 THEN alm_recepdet.ncantidad ELSE 0 END ) AS jul,
                                                    SUM( CASE WHEN MONTH ( alm_recepdet.fregsys ) = 8 THEN alm_recepdet.ncantidad ELSE 0 END ) AS aug,
                                                    SUM( CASE WHEN MONTH ( alm_recepdet.fregsys ) = 9 THEN alm_recepdet.ncantidad ELSE 0 END ) AS sep,
                                                    SUM( CASE WHEN MONTH ( alm_recepdet.fregsys ) = 10 THEN alm_recepdet.ncantidad ELSE 0 END ) AS oct,
                                                    SUM( CASE WHEN MONTH ( alm_recepdet.fregsys ) = 11 THEN alm_recepdet.ncantidad ELSE 0 END ) AS nov,
                                                    SUM( CASE WHEN MONTH ( alm_recepdet.fregsys ) = 12 THEN alm_recepdet.ncantidad ELSE 0 END ) AS dic 
                                                FROM
                                                    alm_recepdet 
                                                WHERE
                                                    alm_recepdet.nflgactivo = 1 
                                                    AND YEAR ( alm_recepdet.fregsys ) = YEAR (NOW()) 
                                                    AND alm_recepdet.id_cprod = :item");
                $sql->execute(['item' => $item]);

                $rowCount = $sql->rowCount();
                
                if ($rowCount) {
                    while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                        $docData[] = $row;
                    }
                }

                return $docData;

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function pivotSalidas($item){
            try {
                $docData = [];

                //este es la consulta para pivotear tablas
                $sql = $this->db->connect()->prepare("SELECT
                                                    SUM( CASE WHEN MONTH ( alm_recepdet.fregsys ) = 1 THEN alm_recepdet.ncantidad ELSE 0 END ) AS jan,
                                                    SUM( CASE WHEN MONTH ( alm_recepdet.fregsys ) = 2 THEN alm_recepdet.ncantidad ELSE 0 END ) AS feb,
                                                    SUM( CASE WHEN MONTH ( alm_recepdet.fregsys ) = 3 THEN alm_recepdet.ncantidad ELSE 0 END ) AS mar,
                                                    SUM( CASE WHEN MONTH ( alm_recepdet.fregsys ) = 4 THEN alm_recepdet.ncantidad ELSE 0 END ) AS apr,
                                                    SUM( CASE WHEN MONTH ( alm_recepdet.fregsys ) = 5 THEN alm_recepdet.ncantidad ELSE 0 END ) AS may,
                                                    SUM( CASE WHEN MONTH ( alm_recepdet.fregsys ) = 6 THEN alm_recepdet.ncantidad ELSE 0 END ) AS jun,
                                                    SUM( CASE WHEN MONTH ( alm_recepdet.fregsys ) = 7 THEN alm_recepdet.ncantidad ELSE 0 END ) AS jul,
                                                    SUM( CASE WHEN MONTH ( alm_recepdet.fregsys ) = 8 THEN alm_recepdet.ncantidad ELSE 0 END ) AS aug,
                                                    SUM( CASE WHEN MONTH ( alm_recepdet.fregsys ) = 9 THEN alm_recepdet.ncantidad ELSE 0 END ) AS sep,
                                                    SUM( CASE WHEN MONTH ( alm_recepdet.fregsys ) = 10 THEN alm_recepdet.ncantidad ELSE 0 END ) AS oct,
                                                    SUM( CASE WHEN MONTH ( alm_recepdet.fregsys ) = 11 THEN alm_recepdet.ncantidad ELSE 0 END ) AS nov,
                                                    SUM( CASE WHEN MONTH ( alm_recepdet.fregsys ) = 12 THEN alm_recepdet.ncantidad ELSE 0 END ) AS dic 
                                                FROM
                                                    alm_recepdet 
                                                WHERE
                                                    alm_recepdet.nflgactivo = 1 
                                                    AND YEAR ( alm_recepdet.fregsys ) = YEAR (NOW()) 
                                                    AND alm_recepdet.id_cprod = :item");
                $sql->execute(['item' => $item]);

                $rowCount = $sql->rowCount();
                
                if ($rowCount) {
                    while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                        $docData[] = $row;
                    }
                }

                return $docData;


            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }
    }
?>