<?php
    class OrdenConsultModel extends Model{

        public function __construct(){
            parent::__construct();
        }

        public function listarOrdenes($user){
           try {
                $salida = "";
                $sql = $this->db->connect()->prepare("SELECT
                                                        tb_costusu.ncodcos,
                                                        tb_costusu.ncodproy,
                                                        tb_costusu.id_cuser,
                                                        lg_ordencab.id_regmov,
                                                        lg_ordencab.cnumero,
                                                        lg_ordencab.ffechadoc,
                                                        lg_ordencab.nNivAten,
                                                        lg_ordencab.nEstadoDoc,
                                                        lg_ordencab.ncodpago,
                                                        lg_ordencab.nplazo,
                                                        lg_ordencab.cdocPDF,
                                                        lg_ordencab.ntotal,
                                                        lg_ordencab.ncodmon,
                                                        UPPER( lg_ordencab.cObservacion ) AS concepto,
                                                        UPPER( tb_pedidocab.detalle ) AS detalle,
                                                        UPPER(
                                                        CONCAT_WS( tb_area.ccodarea, tb_area.cdesarea )) AS area,
                                                        UPPER(
                                                        CONCAT_WS( tb_proyectos.ccodproy, tb_proyectos.cdesproy )) AS costos,
                                                        tb_proyectos.ccodproy,
                                                        lg_ordencab.nfirmaLog,
                                                        lg_ordencab.nfirmaFin,
                                                        lg_ordencab.nfirmaOpe,
                                                        tb_parametros.cdescripcion AS atencion,
                                                        UPPER(cm_entidad.crazonsoc) AS proveedor 
                                                    FROM
                                                        tb_costusu
                                                        INNER JOIN lg_ordencab ON tb_costusu.ncodproy = lg_ordencab.ncodpry
                                                        INNER JOIN tb_pedidocab ON lg_ordencab.id_refpedi = tb_pedidocab.idreg
                                                        INNER JOIN tb_area ON lg_ordencab.ncodarea = tb_area.ncodarea
                                                        INNER JOIN tb_proyectos ON lg_ordencab.ncodpry = tb_proyectos.nidreg
                                                        INNER JOIN tb_parametros ON lg_ordencab.nNivAten = tb_parametros.nidreg
                                                        INNER JOIN cm_entidad ON lg_ordencab.id_centi = cm_entidad.id_centi
                                                        INNER JOIN tb_parametros AS estados ON lg_ordencab.nEstadoDoc = estados.nidreg 
                                                    WHERE
                                                        tb_costusu.id_cuser = :user 
                                                        AND tb_costusu.nflgactivo = 1
                                                        AND YEAR(lg_ordencab.ffechadoc) = YEAR(NOW())
                                                        ORDER BY id_regmov DESC");
                $sql->execute(["user"=>$_SESSION['iduser']]);
                $rowCount = $sql->rowCount();

                

                if ($rowCount > 0){
                    while ($rs = $sql->fetch()) {

                        $montoDolares = 0;
                        $montoSoles = 0;
                        $estado = '';

                        $log = is_null($rs['nfirmaLog']) ? '<i class="far fa-square"></i>' : '<i class="far fa-check-square"></i>';
                        $ope = is_null($rs['nfirmaOpe']) ? '<i class="far fa-square"></i>' : '<i class="far fa-check-square"></i>';
                        $fin = is_null($rs['nfirmaFin']) ? '<i class="far fa-square"></i>' : '<i class="far fa-check-square"></i>';

                        $flog = is_null($rs['nfirmaLog']) ? 0 : 1;
                        $fope = is_null($rs['nfirmaOpe']) ? 0 : 1;
                        $ffin = is_null($rs['nfirmaFin']) ? 0 : 1;


                        if ( $rs['ncodmon'] == 20) {
                            $montoSoles = "S/. ".number_format($rs['ntotal'],2);
                            $montoDolares = "";
                        }else{
                            $montoSoles = "";
                            $montoDolares =  "$ ".number_format($rs['ntotal'],2);
                        }

                        if ( $rs['nEstadoDoc'] == 49) {
                            $estado = "procesando";
                        }else if ( $rs['nEstadoDoc'] == 59 ) {
                            $estado = "firmas";
                        }else if ( $rs['nEstadoDoc'] == 60 ) {
                            $estado = "recepcion";
                        }else if ( $rs['nEstadoDoc'] == 62 ) {
                            $estado = "despacho";
                        }else if ( $rs['nEstadoDoc'] == 105 ) {
                            $estado = "anulado";
                            $montoDolares = "";
                            $montoSoles = "";
                        }


                        $salida .='<tr class="pointer " data-indice="'.$rs['id_regmov'].'" 
                                                        data-estado="'.$rs['nEstadoDoc'].'"
                                                        data-finanzas="'.$ffin.'"
                                                        data-logistica="'.$flog.'"
                                                        data-operaciones="'.$fope.'">
                                    <td class="textoCentro">'.str_pad($rs['cnumero'],4,0,STR_PAD_LEFT).'</td>
                                    <td class="textoCentro">'.date("d/m/Y", strtotime($rs['ffechadoc'])).'</td>
                                    <td class="pl20px">'.$rs['concepto'].'</td>
                                    <td class="pl20px">'.utf8_decode($rs['ccodproy']).'</td>
                                    <td class="pl20px">'.$rs['area'].'</td>
                                    <td class="pl20px">'.$rs['proveedor'].'</td>
                                    <td class="textoDerecha">'.$montoSoles.'</td>
                                    <td class="textoDerecha">'.$montoDolares.'</td>
                                    <td class="textoCentro '.strtolower($rs['atencion']).'">'.$rs['atencion'].'</td>
                                    <td class="textoCentro '.$estado.'">'.strtoupper($estado).'</td>
                                    <td class="textoCentro">'.$log.'</td>
                                    <td class="textoCentro">'.$fin.'</td>
                                    <td class="textoCentro">'.$ope.'</td>
                                    </tr>';
                    }
                }

                return $salida;                    
           } catch (PDOException $th) {
               echo "Error: " . $th->getMessage();
               return false;
           }
        }

        public function verDatosCabecera($pedido){
            $datosPedido = $this->datosPedido($pedido);
            $sql = "SELECT COUNT(lg_ordencab.id_regmov) AS numero FROM lg_ordencab WHERE lg_ordencab.ncodcos =:cod";
            $api = file_get_contents('https://api.apis.net.pe/v1/tipo-cambio-sunat');
            $cambio = json_decode($api);

            $numero = $this->generarNumero($datosPedido[0]["idcostos"],$sql);

            $salida = array("pedido"=>$datosPedido,
                            "orden"=>$numero,
                            "cambio"=>$cambio->compra);

            return $salida;
        }

        private function datosPedido($pedido){
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                        ibis.tb_pedidocab.idreg,
                                                        ibis.tb_pedidocab.idcostos,
                                                        ibis.tb_pedidocab.idarea,
                                                        ibis.tb_pedidocab.idtrans,
                                                        ibis.tb_pedidocab.idsolicita,
                                                        ibis.tb_pedidocab.idtipomov,
                                                        ibis.tb_pedidocab.emision,
                                                        ibis.tb_pedidocab.vence,
                                                        ibis.tb_pedidocab.estadodoc,
                                                        ibis.tb_pedidocab.nrodoc,
                                                        ibis.tb_pedidocab.usuario,
                                                        UPPER(ibis.tb_pedidocab.concepto) AS concepto,
                                                        UPPER(ibis.tb_pedidocab.detalle) AS detalle,
                                                        ibis.tb_pedidocab.nivelAten,
                                                        ibis.tb_pedidocab.docPdfAprob,
                                                        ibis.tb_pedidocab.verificacion,
                                                        UPPER(
                                                        CONCAT( ibis.tb_proyectos.ccodproy, ' ', ibis.tb_proyectos.cdesproy )) AS proyecto,
                                                        UPPER(
                                                        CONCAT( ibis.tb_area.ccodarea, ' ', ibis.tb_area.cdesarea )) AS area,
                                                        UPPER(
                                                        CONCAT( ibis.tb_parametros.nidreg, ' ', ibis.tb_parametros.cdescripcion )) AS transporte,
                                                        estados.cdescripcion AS estado,
                                                        estados.cabrevia,
                                                        UPPER(
                                                        CONCAT_WS( ' ', tipos.nidreg, tipos.cdescripcion )) AS tipo,
                                                        ibis.tb_proyectos.veralm 
                                                    FROM
                                                        ibis.tb_pedidocab
                                                        INNER JOIN ibis.tb_proyectos ON ibis.tb_pedidocab.idcostos = ibis.tb_proyectos.nidreg
                                                        INNER JOIN ibis.tb_area ON ibis.tb_pedidocab.idarea = ibis.tb_area.ncodarea
                                                        INNER JOIN ibis.tb_parametros ON ibis.tb_pedidocab.idtrans = ibis.tb_parametros.nidreg
                                                        INNER JOIN ibis.tb_parametros AS transportes ON ibis.tb_pedidocab.idtrans = transportes.nidreg
                                                        INNER JOIN ibis.tb_parametros AS estados ON ibis.tb_pedidocab.estadodoc = estados.nidreg
                                                        INNER JOIN ibis.tb_parametros AS tipos ON ibis.tb_pedidocab.idtipomov = tipos.nidreg 
                                                    WHERE
                                                        tb_pedidocab.idreg = :pedido ");
                $sql->execute(["pedido"=>$pedido]);
                
                $rowCount = $sql->rowCount();
                
                if ($rowCount > 0) {
                    $docData = array();
                    while($row=$sql->fetch(PDO::FETCH_ASSOC)){
                        $docData[] = $row;
                    }
                }

                return $docData;

            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }

        public function datosEntidad($entidad){
            try {
                $sql=$this->db->connect()->prepare("SELECT
                                                    cm_entidad.cnumdoc,
                                                    cm_entidad.crazonsoc,
                                                    UPPER(cm_entidadcon.cnombres) AS contacto,
                                                    cm_entidadcon.cemail AS correo_contacto,
                                                    cm_entidadcon.ctelefono1 AS telefono_contacto,
                                                    cm_entidad.id_centi,
                                                    cm_entidad.cemail AS correo_entidad,
                                                    cm_entidad.cviadireccion,
                                                    cm_entidad.ctelefono,
                                                    cm_entidad.nagenret
                                                FROM
                                                    cm_entidadcon
                                                INNER JOIN cm_entidad ON cm_entidadcon.id_centi = cm_entidad.id_centi
                                                WHERE
                                                    cm_entidad.cnumdoc = :entidad
                                                LIMIT 1");
                $sql->execute(["entidad"=>$entidad]);

                $rowCount = $sql->rowCount();
                
                if ($rowCount > 0) {
                    $docData = array();
                    while($row=$sql->fetch(PDO::FETCH_ASSOC)){
                        $docData[] = $row;
                    }
                }

                return $docData;
            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }

        private function bancosProveedor($entidad){
            try {
                $bancos = [];
                $item = array();

                $sql = $this->db->connect()->prepare("SELECT
                                                    bancos.cdescripcion AS banco,
                                                    cm_entidadbco.cnrocta AS cuenta,
                                                    monedas.cdescripcion AS moneda
                                                FROM
                                                    cm_entidadbco
                                                    INNER JOIN tb_parametros AS bancos ON cm_entidadbco.ncodbco = bancos.nidreg
                                                    INNER JOIN tb_parametros AS monedas ON cm_entidadbco.cmoneda = monedas.nidreg 
                                                WHERE
                                                    cm_entidadbco.nflgactivo = 7 
                                                    AND cm_entidadbco.id_centi = :entidad");
                $sql->execute(["entidad"=>$entidad]);
                $rowCount = $sql->rowCount();

                if($rowCount > 0){
                    while ($rs = $sql->fetch()) {
                        $item['banco'] = $rs['banco'];
                        $item['moneda'] = $rs['moneda'];
                        $item['cuenta'] = $rs['cuenta'];
                        
                        array_push($bancos,$item);
                    }
                }

                return $bancos;

            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }

        public function ordenfiltrar($parametros){
            
            $orden  = isset($parametros['ordenSearch'])  && $parametros['ordenSearch'] != "" ? $parametros['ordenSearch'] : "%";
            $costos = $parametros['costosSearch'] == -1 ?  "%" : $parametros['costosSearch'];
            $mes    = $parametros['mesSearch'] != '-1' ? $parametros['mesSearch'] : "%";
            $anio   = isset($parametros['anioSearch']) && $parametros['anioSearch'] != "" ? $parametros['anioSearch'] : "%";

            try {
                $salida = "No hay registros";
                $sql = $this->db->connect()->prepare("SELECT
                                                        tb_costusu.ncodcos,
                                                        tb_costusu.ncodproy,
                                                        tb_costusu.id_cuser,
                                                        lg_ordencab.id_regmov,
                                                        lg_ordencab.cnumero,
                                                        lg_ordencab.ffechadoc,
                                                        lg_ordencab.nNivAten,
                                                        lg_ordencab.nEstadoDoc,
                                                        lg_ordencab.ncodpago,
                                                        lg_ordencab.nplazo,
                                                        lg_ordencab.cdocPDF,
                                                        lg_ordencab.ntotal,
                                                        lg_ordencab.ncodmon,
                                                        UPPER( tb_pedidocab.concepto ) AS concepto,
                                                        UPPER( tb_pedidocab.detalle ) AS detalle,
                                                        UPPER(
                                                        CONCAT_WS( tb_area.ccodarea, tb_area.cdesarea )) AS area,
                                                        UPPER(
                                                        CONCAT_WS( tb_proyectos.ccodproy, tb_proyectos.cdesproy )) AS costos,
                                                        tb_proyectos.ccodproy,
                                                        lg_ordencab.nfirmaLog,
                                                        lg_ordencab.nfirmaFin,
                                                        lg_ordencab.nfirmaOpe,
                                                        tb_parametros.cdescripcion AS atencion,
                                                        UPPER( cm_entidad.crazonsoc ) AS proveedor 
                                                    FROM
                                                        tb_costusu
                                                        INNER JOIN lg_ordencab ON tb_costusu.ncodproy = lg_ordencab.ncodpry
                                                        INNER JOIN tb_pedidocab ON lg_ordencab.id_refpedi = tb_pedidocab.idreg
                                                        INNER JOIN tb_area ON lg_ordencab.ncodarea = tb_area.ncodarea
                                                        INNER JOIN tb_proyectos ON lg_ordencab.ncodpry = tb_proyectos.nidreg
                                                        INNER JOIN tb_parametros ON lg_ordencab.nNivAten = tb_parametros.nidreg
                                                        INNER JOIN cm_entidad ON lg_ordencab.id_centi = cm_entidad.id_centi 
                                                    WHERE
                                                        tb_costusu.id_cuser = :user 
                                                        AND tb_costusu.nflgactivo = 1 
                                                        AND lg_ordencab.cper LIKE :anio
                                                        AND lg_ordencab.id_regmov LIKE :orden
                                                        AND tb_costusu.ncodproy LIKE :costos
                                                        AND lg_ordencab.cmes LIKE :mes
                                                    ORDER BY
                                                        id_regmov DESC");
                $sql->execute(["user"=>$_SESSION['iduser'],
                                "anio"=>$anio,
                                "orden"=>$orden,
                                "costos"=>$costos,
                                "mes"=>$mes]);
                
                $rowCount = $sql->rowCount();

                if ($rowCount > 0){
                    while ($rs = $sql->fetch()) {

                        $montoDolares = 0;
                        $montoSoles = 0;

                        $log = is_null($rs['nfirmaLog']) ? '<i class="far fa-square"></i>' : '<i class="far fa-check-square"></i>';
                        $ope = is_null($rs['nfirmaOpe']) ? '<i class="far fa-square"></i>' : '<i class="far fa-check-square"></i>';
                        $fin = is_null($rs['nfirmaFin']) ? '<i class="far fa-square"></i>' : '<i class="far fa-check-square"></i>';

                        $flog = is_null($rs['nfirmaLog']) ? 0 : 1;
                        $fope = is_null($rs['nfirmaOpe']) ? 0 : 1;
                        $ffin = is_null($rs['nfirmaFin']) ? 0 : 1;

                        $resaltado = $rs['nEstadoDoc'] == 59 ? "resaltado_firma" :  "";


                        if ( $rs['ncodmon'] == 20) {
                            $montoSoles = "S/. ".number_format($rs['ntotal'],2);
                            $montoDolares = "";
                        }else{
                            $montoSoles = "";
                            $montoDolares =  "$ ".number_format($rs['ntotal'],2);
                        }

                        if ( $rs['nEstadoDoc'] == 49) {
                            $estado = "procesando";
                        }else if ( $rs['nEstadoDoc'] == 59 ) {
                            $estado = "firmas";
                        }else if ( $rs['nEstadoDoc'] == 60 ) {
                            $estado = "recepcion";
                        }else if ( $rs['nEstadoDoc'] == 62 ) {
                            $estado = "despacho";
                        }else if ( $rs['nEstadoDoc'] == 105 ) {
                            $estado = "anulado";
                            $montoDolares = "";
                            $montoSoles = "";
                        }

                        $resaltado = $rs['nEstadoDoc'] == 59 ? "resaltado_firma" :  "";


                        $salida .='<tr class="pointer '.$resaltado.'" data-indice="'.$rs['id_regmov'].'" 
                                                        data-estado="'.$rs['nEstadoDoc'].'"
                                                        data-finanzas="'.$ffin.'"
                                                        data-logistica="'.$flog.'"
                                                        data-operaciones="'.$fope.'">
                                    <td class="textoCentro">'.str_pad($rs['cnumero'],4,0,STR_PAD_LEFT).'</td>
                                    <td class="textoCentro">'.date("d/m/Y", strtotime($rs['ffechadoc'])).'</td>
                                    <td class="pl20px">'.$rs['concepto'].'</td>
                                    <td class="pl20px">'.utf8_decode($rs['ccodproy']).'</td>
                                    <td class="pl20px">'.$rs['area'].'</td>
                                    <td class="pl20px">'.$rs['proveedor'].'</td>
                                    <td class="textoDerecha">'.$montoSoles.'</td>
                                    <td class="textoDerecha">'.$montoDolares.'</td>
                                    <td class="textoCentro '.strtolower($rs['atencion']).'">'.$rs['atencion'].'</td>
                                    <td class="textoCentro '.$estado.'">'.strtoupper($estado).'</td>
                                    <td class="textoCentro">'.$log.'</td>
                                    <td class="textoCentro">'.$fin.'</td>
                                    <td class="textoCentro">'.$ope.'</td>
                                    </tr>';
                    }
                }

                return $salida;                    
           } catch (PDOException $th) {
               echo "Error: " . $th->getMessage();
               return false;
           }

            return $orden;
        }

        public function exportar($detalles){
            require_once('public/PHPExcel/PHPExcel.php');
            try {
                $objPHPExcel = new PHPExcel();
                $objPHPExcel->getProperties()
                    ->setCreator("Sical")
                    ->setLastModifiedBy("Sical")
                    ->setTitle("Cargo Plan")
                    ->setSubject("Template excel")
                    ->setDescription("Reporte Ordenes")
                    ->setKeywords("Template excel");

                $cuerpo = array(
                    'font'  => array(
                    'bold'  => false,
                    'size'  => 7,
                ));

                $objWorkSheet = $objPHPExcel->createSheet(1);

                $objPHPExcel->setActiveSheetIndex(0);
                $objPHPExcel->getActiveSheet()->setTitle("Reporte Ordenes");

                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');
                $objWriter->save('public/documentos/reportes/catalogo.xlsx');
                $objPHPExcel->getActiveSheet()->mergeCells('A1:K1');
                $objPHPExcel->getActiveSheet()->setCellValue('A1','REPORTE ORDENES');

                $objPHPExcel->getActiveSheet()->getStyle('A1:K2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('A1:K2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $objPHPExcel->getActiveSheet()->getStyle('I')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('I')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('J')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('J')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('K')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('K')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(60);

                $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(70);
                $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(70);
                $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(70);

                $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
                $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
                

                $objPHPExcel->getActiveSheet()
                            ->getStyle('A2:K2')
                            ->getFill()
                            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB('BFCDDB');

                $objPHPExcel->getActiveSheet()->getStyle('A1:k2')->getAlignment()->setWrapText(true);

                $objPHPExcel->getActiveSheet()->setCellValue('A2','Número'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('B2','Emision'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('C2','Descripción'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('D2','Centro de Costos'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('E2','Area'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('F2','Proveedor'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('G2','Precio Soles'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('H2','Precio Dólares'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('I2','Logística'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('J2','Operaciones'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('K2','Finanzas'); // esto cambia

                $fila = 3;
                $datos = json_decode($detalles);
                $nreg = count($datos);

                for ($i=0; $i < $nreg ; $i++) {

                    $logistica = $datos[$i]->logistica == 1 ? "X" : "";
                    $operaciones = $datos[$i]->logistica == 1 ? "X" : "";
                    $finanzas = $datos[$i]->logistica == 1 ? "X" : "";

                    $soles = explode(" ",$datos[$i]->soles);
                    $dolares = explode(" ",$datos[$i]->dolares);

                    $s = str_replace(',','',(end($soles)));
                    $d = str_replace(',','',(end($dolares)));
                    
                    $objPHPExcel->getActiveSheet()->setCellValue('A'.$fila,$datos[$i]->item);
                    $objPHPExcel->getActiveSheet()->setCellValue('B'.$fila,$datos[$i]->emision);
                    $objPHPExcel->getActiveSheet()->setCellValue('C'.$fila,$datos[$i]->descripcion);
                    $objPHPExcel->getActiveSheet()->setCellValue('D'.$fila,$datos[$i]->costo);
                    $objPHPExcel->getActiveSheet()->setCellValue('E'.$fila,$datos[$i]->area);
                    $objPHPExcel->getActiveSheet()->setCellValue('F'.$fila,$datos[$i]->proveedor);
                    $objPHPExcel->getActiveSheet()->setCellValue('G'.$fila,$s);
                    $objPHPExcel->getActiveSheet()->getStyle('G'.$fila)->getNumberFormat()->setFormatCode('"S/." #,##0.00');
                    $objPHPExcel->getActiveSheet()->setCellValue('H'.$fila,$d);
                    $objPHPExcel->getActiveSheet()->getStyle('H'.$fila)->getNumberFormat()->setFormatCode('"$" #,##0.00');
                    $objPHPExcel->getActiveSheet()->setCellValue('I'.$fila,$logistica);
                    $objPHPExcel->getActiveSheet()->setCellValue('J'.$fila,$operaciones);
                    $objPHPExcel->getActiveSheet()->setCellValue('K'.$fila,$finanzas);

                    $fila++;
                }

                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');
                $objWriter->save('public/documentos/reportes/reporte.xlsx');

                return array("documento"=>'public/documentos/reportes/reporte.xlsx');

                exit();
            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }

        public function listarOrdenConsultScroll($pagina,$cantidad){
            try {
                $inicio = ($pagina - 1) * $cantidad;
                $limite = $this->contarItems();

                $sql = $this->db->connect()->prepare("SELECT
                                                        tb_costusu.ncodcos,
                                                        tb_costusu.ncodproy,
                                                        tb_costusu.id_cuser,
                                                        lg_ordencab.id_regmov,
                                                        LPAD(lg_ordencab.cnumero,6,0) AS cnumero,
                                                        DATE_FORMAT(lg_ordencab.ffechadoc,'%d/%m/%Y') AS emision,
                                                        lg_ordencab.nNivAten,
                                                        lg_ordencab.nEstadoDoc,
                                                        lg_ordencab.ncodpago,
                                                        lg_ordencab.nplazo,
                                                        lg_ordencab.cdocPDF,
                                                        FORMAT(lg_ordencab.ntotal,2) AS ntotal,
                                                        lg_ordencab.ncodmon,
                                                        UPPER( lg_ordencab.cObservacion ) AS concepto,
                                                        UPPER( tb_pedidocab.detalle ) AS detalle,
                                                        UPPER(
                                                        CONCAT_WS( tb_area.ccodarea, tb_area.cdesarea )) AS area,
                                                        UPPER(
                                                        CONCAT_WS( tb_proyectos.ccodproy, tb_proyectos.cdesproy )) AS costos,
                                                        tb_proyectos.ccodproy,
                                                        lg_ordencab.nfirmaLog,
                                                        lg_ordencab.nfirmaFin,
                                                        lg_ordencab.nfirmaOpe,
                                                        tb_parametros.cdescripcion AS atencion,
                                                        UPPER(cm_entidad.crazonsoc) AS proveedor,
                                                        IF(ISNULL(lg_ordencab.nfirmaLog),0,1) AS logistica,
                                                        IF(ISNULL(lg_ordencab.nfirmaFin),0,1) AS finanzas,
                                                        IF(ISNULL(lg_ordencab.nfirmaOpe),0,1) AS operaciones,
                                                        IF(lg_ordencab.nEstadoDoc = 59,'resaltado_firma','-') AS resaltado,
                                                        estados.cdescripcion AS estado 
                                                    FROM
                                                        tb_costusu
                                                        INNER JOIN lg_ordencab ON tb_costusu.ncodproy = lg_ordencab.ncodpry
                                                        INNER JOIN tb_pedidocab ON lg_ordencab.id_refpedi = tb_pedidocab.idreg
                                                        INNER JOIN tb_area ON lg_ordencab.ncodarea = tb_area.ncodarea
                                                        INNER JOIN tb_proyectos ON lg_ordencab.ncodpry = tb_proyectos.nidreg
                                                        INNER JOIN tb_parametros ON lg_ordencab.nNivAten = tb_parametros.nidreg
                                                        INNER JOIN cm_entidad ON lg_ordencab.id_centi = cm_entidad.id_centi
                                                        INNER JOIN tb_parametros AS estados ON lg_ordencab.nEstadoDoc = estados.nidreg 
                                                    WHERE
                                                        tb_costusu.id_cuser = :user 
                                                        AND tb_costusu.nflgactivo = 1
                                                    ORDER BY lg_ordencab.id_regmov DESC
                                                    LIMIT $inicio,$cantidad");
                
                $sql->execute(["user"=>$_SESSION['iduser']]);

                $rc = $sql->rowcount();
                $item = 1;

                if ($rc > 0){
                    while( $rs = $sql->fetch()) {
                        $datos[] = $rs;
                    }
                }

                return array("filas"=>$datos,
                            'quedan'=>($inicio + $cantidad) < $limite);

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function contarItems(){
            try {
                $sql = $this->db->connect()->query("SELECT COUNT(id_regmov) AS regs FROM lg_ordencab WHERE nflgactivo = 1");
                $sql->execute();
                $filas = $sql->fetch();

                return $filas['regs'];
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

    }
?>