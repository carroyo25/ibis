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
                                        <td colspan="14"></td>
                                        <td><b>Total Orden '.$rs['cnumero'].'</b></td>
                                        <td colspan="2"></td>
                                        <td class="textoDerecha">'.$rs['ntotal'].'</td>
                                        <td></td>
                                        <td class="textoDerecha">'.$rs['total_dolares'].'</td>
                                        <td class="textoDerecha">'.$rs['total_soles'].'</td>
                                        <td colspan="13"></td>
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
                                                        LPAD(lg_ordendet.id_orden, 6, 0) AS orden,
                                                        UPPER(CONCAT_WS('', cm_producto.cdesprod, lg_ordendet.cobserva)) AS descripcion,
                                                        tb_unimed.cabrevia AS unidad,
                                                        cm_producto.ccodprod,
                                                        tb_proyectos.ccodproy,
                                                        UPPER(tb_proyectos.cdesproy) AS proyecto,
                                                        UPPER(tb_area.cdesarea) AS area,
                                                        lg_ordencab.ntcambio,
                                                        UPPER(lg_ordencab.cobservacion) AS observacion,
                                                        lg_ordencab.cper,
                                                        lg_ordencab.ntipmov,
                                                        DATE_FORMAT(lg_ordencab.ffechadoc, '%d/%m/%Y') AS fecha_registro,
                                                        UPPER(cm_entidad.crazonsoc) AS crazonsoc,
                                                        LPAD(tb_pedidocab.nrodoc, 6, 0) AS pedido,
                                                        lg_ordencab.ncodmon,
                                                        DATE_FORMAT(lg_ordencab.FechaFin, '%d/%m/%Y') AS FechaFin,
                                                        CAST(lg_ordencab.nplazo AS SIGNED INTEGER) AS nplazo,
                                                        lg_ordencab.cnumcot,
                                                        tb_pedidodet.nroparte,
                                                        tb_pedidodet.nregistro,
                                                        fpagos.cdescripcion AS tipo_pago,
                                                        cm_entidad.cnumdoc,
                                                        UPPER(tb_grupo.cdescrip) AS grupo,
                                                        UPPER(cm_entidad.cviadireccion) AS cviadireccion,
                                                        lg_ordencab.nfirmaOpe,
                                                        lg_ordencab.nfirmaFin,
                                                        LPAD(lg_ordencab.cnumero, 6, 0) AS cnumero,
                                                        lg_ordencab.nfirmaLog,
                                                        alm_recepdet.ncantidad,
                                                        UPPER(tb_clase.cdescrip) AS clase,
                                                        lg_ordencab.id_regmov,
                                                        tb_pedidocab.anio,
                                                        lg_ordendet.ntotal,
                                                        DATE_FORMAT(lg_ordencab.ffechaent, '%d/%m/%Y') AS fecha_entrega,
                                                        FORMAT(IF(lg_ordencab.ncodmon = 21, lg_ordendet.ntotal, lg_ordendet.ntotal / lg_ordencab.ntcambio), 2) AS dolares,
                                                        FORMAT(IF(lg_ordencab.ncodmon = 20, lg_ordendet.ntotal, lg_ordendet.ntotal * lg_ordencab.ntcambio), 2) AS soles,
                                                        tb_equipmtto.cdescripcion,
                                                        tb_equipmtto.cregistro,
                                                        tb_pedidodet.iditem,
                                                        -- Consolidación de guías y despachos:
                                                        GROUP_CONCAT(DISTINCT alm_despachodet.ndespacho ORDER BY alm_despachodet.ndespacho SEPARATOR ', ') AS ndespacho,
                                                        GROUP_CONCAT(DISTINCT lg_guias.cnumguia ORDER BY lg_guias.cnumguia SEPARATOR ', ') AS cnumguia,
                                                        GROUP_CONCAT(DISTINCT lg_guias.guiasunat ORDER BY lg_guias.guiasunat SEPARATOR ', ') AS guiasunat
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
                                                        LEFT JOIN tb_equipmtto ON tb_pedidodet.nregistro = tb_equipmtto.idreg
                                                        LEFT JOIN alm_despachodet ON alm_despachodet.niddetaPed = tb_pedidodet.iditem
                                                        LEFT JOIN alm_despachocab ON alm_despachocab.id_regalm = alm_despachodet.id_regalm
                                                        LEFT JOIN lg_guias ON lg_guias.id_regalm = alm_despachocab.id_regalm
                                                    WHERE
                                                        lg_ordendet.id_orden = :id
                                                        AND lg_ordendet.nestado = 1
                                                    GROUP BY
                                                        lg_ordendet.id_cprod,
                                                        lg_ordendet.ncanti,
                                                        lg_ordendet.nunitario,
                                                        lg_ordendet.id_orden,
                                                        cm_producto.cdesprod,
                                                        lg_ordendet.cobserva,
                                                        tb_unimed.cabrevia,
                                                        cm_producto.ccodprod,
                                                        tb_proyectos.ccodproy,
                                                        tb_proyectos.cdesproy,
                                                        tb_area.cdesarea,
                                                        lg_ordencab.ntcambio,
                                                        lg_ordencab.cobservacion,
                                                        lg_ordencab.cper,
                                                        lg_ordencab.ntipmov,
                                                        lg_ordencab.ffechadoc,
                                                        cm_entidad.crazonsoc,
                                                        tb_pedidocab.nrodoc,
                                                        lg_ordencab.ncodmon,
                                                        lg_ordencab.FechaFin,
                                                        lg_ordencab.nplazo,
                                                        lg_ordencab.cnumcot,
                                                        tb_pedidodet.nroparte,
                                                        tb_pedidodet.nregistro,
                                                        fpagos.cdescripcion,
                                                        cm_entidad.cnumdoc,
                                                        tb_grupo.cdescrip,
                                                        cm_entidad.cviadireccion,
                                                        lg_ordencab.nfirmaOpe,
                                                        lg_ordencab.nfirmaFin,
                                                        lg_ordencab.cnumero,
                                                        lg_ordencab.nfirmaLog,
                                                        alm_recepdet.ncantidad,
                                                        tb_clase.cdescrip,
                                                        lg_ordencab.id_regmov,
                                                        tb_pedidocab.anio,
                                                        lg_ordendet.ntotal,
                                                        lg_ordencab.ffechaent,
                                                        lg_ordencab.ncodmon,
                                                        tb_equipmtto.cdescripcion,
                                                        tb_equipmtto.cregistro,
                                                        tb_pedidodet.iditem");

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
                                        <td class="pl20px" title="'.$rs['proyecto'].'">'.$rs['proyecto'].'</td>
                                        <td class="pl20px" title="'.$rs['area'].'">'.$rs['area'].'</td>
                                        <td class="textoCentro">'.$rs['fecha_registro'].'</td>
                                        <td class="textoCentro">'.$rs['cper'].'</td>
                                        <td class="textoCentro">'.$tipo.'</td>
                                        <td class="textoCentro">'.$rs['anio'].'</td>
                                        <td class="textoCentro">'.$rs['pedido'].'</td>
                                        <td class="textoCentro">'.$rs['cnumero'].'</td>
                                        <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                        <td class="pl20px" title="'.$rs['descripcion'].'">'.$rs['descripcion'].'</td>
                                        <td class="textoCentro">'.$rs['unidad'].'</td>
                                        <td class="pl20px" title="'.$rs['crazonsoc'].'">'.$rs['crazonsoc'].'</td>
                                        <td class="textoDerecha">'.$rs['ncanti'].'</td>
                                        <td class="textoDerecha">'.$rs['nunitario'].'</td>
                                        <td class="textoDerecha">'.$moneda.'</td>
                                        <td class="textoDerecha">'.$rs['ntotal'].'</td>
                                        <td class="textoDerecha">'.$rs['ntcambio'].'</td>
                                        <td class="textoDerecha">'.$rs['dolares'].'</td>
                                        <td class="textoDerecha">'.$rs['soles'].'</td>
                                        <td class="textoDerecha">'.$rs['FechaFin'].'</td>
                                        <td class="pl20px" title="'.$rs['grupo'].'">'.$rs['grupo'].'</td>
                                        <td class="pl20px" title="'.$rs['clase'].'">'.$rs['clase'].'</td>
                                        <td class="pl20px" title="'.$rs['cviadireccion'].'">'.$rs['cviadireccion'].'</td>
                                        <td class="pl20px">'.$rs['tipo_pago'].'</td>
                                        <td class="textoCentro">'.$rs['fecha_entrega'].'</td>
                                        <td class="textoCentro">'.$rs['nplazo'].'</td>
                                        <td class="textoCentro" >'.$rs['cnumdoc'].'</td>
                                        <td class="pl20px" title="'.$rs['cnumcot'].'">'.$rs['cnumcot'].'</td>
                                        <td class="pl20px">'.$rs['nroparte'].'</td>
                                        <td class="pl20px">'.$rs['cregistro'].'</td>
                                        <td class="textoCentro">'.$estado.'</td>
                                        <td class="pl20px" title="'.$rs['observacion'].'">'.$rs['observacion'].'</td>
                                        <td>'.$rs['guiasunat'].'</td>
                                        <td>'.$rs['cnumguia'].'</td>
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
            require_once 'public/phpSpreadSheet/vendor/autoload.php'; 

            try {
                // Namespace completo para evitar el "Class not found"
                $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                
                $spreadsheet->getProperties()
                    ->setCreator("Sical")
                    ->setLastModifiedBy("Sical")
                    ->setTitle("Reporte Valorizado")
                    ->setSubject("Template excel")
                    ->setDescription("Reporte Valorizado")
                    ->setKeywords("Template excel");

                $spreadsheet->createSheet(1);
                $spreadsheet->setActiveSheetIndex(0);
                $sheet = $spreadsheet->getActiveSheet();
                $sheet->setTitle("Reporte Valorizado");

                // ========== Título ==========
                $sheet->mergeCells('A1:AJ1');
                $sheet->setCellValue('A1', 'REPORTE VALORIZADO');
                $sheet->getStyle('A1:AJ1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '1F4E78']],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                ]);
                $sheet->getRowDimension('1')->setRowHeight(25);

                // ========== Área de encabezados ==========
                $sheet->getStyle('A1:AJ2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A1:AJ2')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getRowDimension('2')->setRowHeight(60);

                foreach (['D','E','L','M','O','T','U','V','AG','AI','AJ'] as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                $sheet->getStyle('A2:P2')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('BFCDDB');
                $sheet->getStyle('Q2:V2')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('00FFFF');
                $sheet->getStyle('W2:AD2')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('BFCDDB');
                $sheet->getStyle('AE2:AJ2')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');

                $sheet->getStyle('A1:AW2')->getAlignment()->setWrapText(true);

                $sheet->getStyle('A2:AJ2')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 9],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        'wrap'       => true,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['rgb' => '808080'],
                        ],
                    ],
                ]);

                $encabezados = [
                    'A2'=>'Items','B2'=>'Codigo Proyecto','C2'=>'Descripción/Proyecto Obra','D2'=>'Area',
                    'E2'=>'Fecha Registro','F2'=>'Año Orden','G2'=>'Tipo','H2'=>'Año Pedido',
                    'I2'=>'N° Pedido','J2'=>'N° Orden','K2'=>'Codigo del Bien/Servicio',
                    'L2'=>'Descripcion del Bien/Servicio','M2'=>'Unidad Medida','N2'=>'Proveedor',
                    'O2'=>'Cantidad','P2'=>'Precio','Q2'=>'Tipo Moneda','R2'=>'Importe Total',
                    'S2'=>'Tipo Cambio','T2'=>'Contable ME Total Dólares','U2'=>'Contable MN Total Soles',
                    'V2'=>'Fecha Aprobación','W2'=>'Clasificacion Grupo','X2'=>'Clasificacion Clase',
                    'Y2'=>'Dirección Proveedor','Z2'=>'Forma Pago','AA2'=>'Fecha Entrega','AB2'=>'N° Días',
                    'AC2'=>'N° R.U.C.','AD2'=>'N° de Cotizacion','AE2'=>'N° Parte','AF2'=>'Código Maq.Equipo',
                    'AG2'=>'Estado','AH2'=>'Observacion','AI2'=>'Guia Sunat','AJ2'=>'Guia Interna',
                ];
                foreach ($encabezados as $cell => $texto) {
                    $sheet->setCellValue($cell, $texto);
                }

                // ========== Datos del cuerpo ==========
                $datos = json_decode($detalles, true);
                if (!is_array($datos)) $datos = [];

                $columnas = [
                    'A'=>'item','B'=>'codigoproyecto','C'=>'descripcionproyecto','D'=>'area','E'=>'fecharegistro',
                    'F'=>'anioorden','G'=>'tipo','H'=>'aniopedido','I'=>'nroorden','J'=>'nropedido',
                    'K'=>'codigo_producto','L'=>'descripcion','M'=>'unidad','N'=>'proveedor','O'=>'cantidad',
                    'P'=>'precio','Q'=>'moneda','R'=>'total','S'=>'cambio','T'=>'dolares','U'=>'soles',
                    'V'=>'aprobacion','W'=>'grupo','X'=>'clase','Y'=>'direccion','Z'=>'pago','AA'=>'entrega',
                    'AB'=>'dias','AC'=>'ruc','AD'=>'cotizacion','AE'=>'parte','AF'=>'equipo','AG'=>'estado',
                    'AH'=>'concepto','AI'=>'sunat','AJ'=>'interna',
                ];

                foreach (['A'=>6,'B'=>16,'C'=>30,'H'=>11,'I'=>11,'J'=>11,'K'=>16,'N'=>28,
                          'W'=>18,'X'=>18,'Y'=>28,'Z'=>16,'AA'=>14,'AB'=>10,'AC'=>14,
                          'AD'=>14,'AE'=>12,'AF'=>14,'AG'=>18,'AH'=>28,'AI'=>14,'AJ'=>16] as $col => $w) {
                    $sheet->getColumnDimension($col)->setWidth($w);
                }

                $fila = 3;
                foreach ($datos as $item) {
                    foreach ($columnas as $letra => $clave) {
                        $sheet->setCellValue($letra . $fila, $item[$clave] ?? '');
                    }
                    $fila++;
                }
                $ultimaFila = $fila - 1;

                // ========== Estilo del cuerpo ==========
                if ($ultimaFila >= 3) {
                    $rango = 'A3:AJ' . $ultimaFila;

                    $sheet->getStyle($rango)->getFont()->setSize(8);
                    $sheet->getStyle($rango)->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                'color' => ['rgb' => 'BFBFBF'],
                            ],
                        ],
                    ]);
                    $sheet->getStyle($rango)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                    foreach (['O','P','R','S','T','U'] as $col) {
                        $sheet->getStyle($col . '3:' . $col . $ultimaFila)
                              ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                    }
                    $sheet->getStyle('P3:U' . $ultimaFila)->getNumberFormat()->setFormatCode('#,##0.00');

                    foreach (['A','F','G','H','I','J','M','Q','S','V','AB'] as $col) {
                        $sheet->getStyle($col . '3:' . $col . $ultimaFila)
                              ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    }

                    for ($f = 3; $f <= $ultimaFila; $f++) {
                        if ($f % 2 == 0) {
                            $sheet->getStyle('A' . $f . ':AJ' . $f)->getFill()
                                  ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('F2F2F2');
                        }
                    }

                    $sheet->setAutoFilter('A2:AJ' . $ultimaFila);
                }

                $sheet->freezePane('B3');

                // ========== Guardar ==========
                $rutaArchivo = 'public/documentos/reportes/valorizado.xlsx';
                $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
                $writer->save($rutaArchivo);

                return ["documento" => $rutaArchivo];

            } catch (\Exception $e) {
                echo $e->getMessage();
                return false;
            }
        }

        public function listarAdjuntosValorizado($orden){
            $ordenes = $this->adjuntosOrden($orden);
            $nroingreso = $this->buscarAdjuntoGuia($orden);
            $guias = $this->adjuntosIngreso($nroingreso);
    

            return array("ordenes"=>$ordenes,
                        "nroingreso"=>$nroingreso,
                        "guiasalmacen"=>$guias );
        }

        private function adjuntosOrden($orden) {
            $docData = array();

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
                                                        LPAD(lg_ordencab.cnumero,6,0) AS cnumero,
                                                        lg_regdocumento.fregsys 
                                                    FROM
                                                        lg_regdocumento
                                                        INNER JOIN lg_ordencab ON lg_regdocumento.nidrefer = lg_ordencab.id_regmov 
                                                    WHERE
                                                        lg_regdocumento.cmodulo = 'ORD' 
                                                        AND lg_regdocumento.nflgactivo = 1 
                                                        AND lg_regdocumento.nidrefer = :orden
                                                    ORDER BY
                                                        lg_regdocumento.nidrefer DESC");
                $sql->execute(["orden"=>$orden]);

                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    
                    while($row=$sql->fetch(PDO::FETCH_ASSOC)){
                        $docData[] = $row;
                    }
                }

                return $docData;

            }catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function adjuntosIngreso($ingreso) {
            $docData = array();

            try{

                if ($ingreso > 0) {
                    
                    $nreg = count($ingreso);

                    for ($i=0; $i < $nreg ; $i++) {
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
                                                            lg_regdocumento.cmodulo = 'GA' 
                                                            AND lg_regdocumento.nflgactivo = 1 
                                                            AND lg_regdocumento.nidrefer = :ingreso
                                                        ORDER BY
                                                                lg_regdocumento.nidrefer DESC");
                        $sql->execute(["ingreso"=>$ingreso[$i]["idregistro"]]);
    
                        $rowCount = $sql->rowCount();
    
                        if ($rowCount > 0) {
                            while($row=$sql->fetch(PDO::FETCH_ASSOC)){
                                $docData[] = $row;
                            }
                        }
                    }
                }

                return $docData;

            }catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function listarAdjuntosCarpeta($parametros){
            try {

                $docData = [];

                $numero = $parametros['numero'] === "" ? "%" : $parametros['numero'];

                $sql = $this->db->connect()->prepare("SELECT
                                                    lg_ordencab.id_regmov,
                                                    lg_ordencab.cper,
                                                    lg_ordencab.cmes,
                                                    lg_ordencab.ntipmov,
                                                    LPAD(lg_ordencab.cnumero,6,0) AS cnumero,
                                                    lg_ordencab.ffechadoc, 
	                                                UPPER(lg_ordencab.cObservacion) AS cObservacion
                                                FROM
                                                    lg_ordencab
                                                WHERE
                                                    lg_ordencab.nflgactivo = 1 
                                                    AND lg_ordencab.cnumero LIKE :numero
                                                    AND lg_ordencab.cper =:anio
                                                    AND lg_ordencab.ncodcos =:cc");
                
                $sql->execute(["anio"=>$parametros['anio'],"cc"=>$parametros['cc'],"numero"=>$numero]);

                if ($sql->rowCount() > 0){
                    while($row=$sql->fetch(PDO::FETCH_ASSOC)){
                        $docData[] = $row;
                    }
                }

                return array("ordenes"=>$docData);
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function buscarAdjuntoGuia($orden){
            try {
                $result = 0;

                $sql = $this->db->connect()->prepare("SELECT
                                                        alm_existencia.idregistro
                                                    FROM
                                                        alm_existencia
                                                        INNER JOIN
                                                        lg_ordendet
                                                        ON alm_existencia.idpedido = lg_ordendet.niddeta
                                                        WHERE lg_ordendet.id_regmov =:orden
                                                        GROUP BY alm_existencia.idregistro");
                $sql->execute(["orden" => $orden]);
                $rowCount = $sql->rowcount();
                
                if ( $rowCount > 0 ) {
                    $result = $sql->fetchAll();
                }
                
                return $result;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }
    }
?>