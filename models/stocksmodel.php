<?php
    class StocksModel extends Model{

        public function __construct(){
            parent::__construct();
        }

        public function listarItems($parametros){
            try {
                $salida = '';
                $cc = $parametros['costosSearch'];
                $cp = $parametros['codigoBusqueda'] == "" ? "%" : $parametros['codigoBusqueda'];
                $de = $parametros['descripcionSearch'] == "" ? "%" : "%".$parametros['descripcionSearch']."%";

                /*
                SELECT
	items.id_cprod,
	items.ccodprod,
	UPPER( items.cdesprod ) AS cdesprod,
	r.numero_pedidos,
	r.cantidad_pedido,
	o.numero_ordenes,
	o.cantidad_orden,
	i.numero_ingresos,
	i.cantidad_ingresos,
	d.numero_despachos,
	d.cantidad_despachos,
	io.ingresos_obra,
	io.existencia_obra,
	iv.numero_inventarios,
	iv.cantidad_inventarios,
	c.numero_consumo,
	c.cantidad_consumo,
	t.numero_transferencias,
	t.cantidad_transferencias
FROM
	cm_producto AS items 
	/*pedidos
	LEFT JOIN (
        SELECT
            COUNT( tb_pedidodet.cant_aprob ) AS numero_pedidos,
            SUM( tb_pedidodet.cant_aprob ) AS cantidad_pedido,
            tb_pedidodet.idprod 
        FROM
            tb_pedidodet 
        WHERE
            tb_pedidodet.nflgActivo = 1 
            AND tb_pedidodet.idcostos = 34 
            AND tb_pedidodet.idprod = 5537 
        ) AS r ON r.idprod = items.id_cprod 
        /*ordenes
        LEFT JOIN (
        SELECT
            COUNT( lg_ordendet.ncanti ) AS numero_ordenes,
            SUM( lg_ordendet.ncanti ) AS cantidad_orden,
            lg_ordendet.id_cprod 
        FROM
            lg_ordendet 
        WHERE
            lg_ordendet.id_cprod = 5537 
            AND lg_ordendet.ncodcos = 34 
            AND lg_ordendet.nEstadoReg != 105 
        ) AS o ON o.id_cprod = items.id_cprod 
        /*ingresos guias
        LEFT JOIN (
        SELECT
            SUM( alm_recepdet.ncantidad ) AS cantidad_ingresos,
            COUNT( alm_recepdet.ncantidad ) AS numero_ingresos,
            alm_recepdet.id_cprod 
        FROM
            alm_recepdet
            LEFT JOIN alm_recepcab ON alm_recepcab.id_regalm = alm_recepdet.id_regalm 
        WHERE
            alm_recepdet.id_cprod = 5537 
            AND alm_recepdet.nflgactivo = 1 
            AND alm_recepcab.ncodpry = 34 
        ) AS i ON i.id_cprod = items.id_cprod 
        /*despachos
        LEFT JOIN (
        SELECT
            COUNT( alm_despachodet.ncantidad ) AS numero_despachos,
            SUM( alm_despachodet.ncantidad ) AS cantidad_despachos,
            alm_despachodet.id_cprod 
        FROM
            alm_despachodet
            LEFT JOIN alm_despachocab ON alm_despachocab.id_regalm = alm_despachodet.id_regalm 
        WHERE
            id_cprod = 5537 
            AND alm_despachodet.nflgactivo = 1 
            AND alm_despachocab.ncodpry = 34 
        ) AS d ON d.id_cprod = items.id_cprod 
        /*ingreso obra
        LEFT JOIN (
        SELECT
            COUNT( alm_existencia.cant_ingr ) AS ingresos_obra,
            SUM( alm_existencia.cant_ingr ) AS existencia_obra,
            alm_existencia.codprod 
        FROM
            alm_existencia
            LEFT JOIN alm_cabexist ON alm_cabexist.idreg = alm_existencia.idregistro 
        WHERE
            alm_existencia.codprod = 5537 
            AND alm_existencia.nflgActivo = 1 
            AND alm_cabexist.idcostos = 34 
        ) AS io ON io.codprod = items.id_cprod 
        /*inventarios
        LEFT JOIN (
        SELECT
            COUNT( alm_inventariodet.cant_ingr ) AS numero_inventarios,
            SUM( alm_inventariodet.cant_ingr ) AS cantidad_inventarios,
            alm_inventariocab.idcostos,
            alm_inventariodet.codprod 
        FROM
            alm_inventariodet
            INNER JOIN alm_inventariocab ON alm_inventariodet.idregistro = alm_inventariocab.idreg 
        WHERE
            alm_inventariodet.codprod = 5537 
            AND alm_inventariodet.nflgActivo = 1 
            AND alm_inventariocab.idcostos = 34 
        ) AS iv ON iv.codprod = items.id_cprod
        /*consumos
    LEFT JOIN(
        SELECT COUNT(alm_consumo.cantsalida) AS numero_consumo,
                    SUM(alm_consumo.cantsalida) AS cantidad_consumo,
                    alm_consumo.idprod
        FROM alm_consumo 
        WHERE alm_consumo.flgactivo = 1 
                    AND alm_consumo.idprod = 5537
                    AND alm_consumo.ncostos = 34
    )	AS c ON c.idprod = items.id_cprod
    /*transferencias
    LEFT JOIN(
        SELECT COUNT(alm_transferdet.ncanti) AS  numero_transferencias,
                    SUM(alm_transferdet.ncanti) AS cantidad_transferencias,
                    alm_transferdet.idcprod
        FROM alm_transferdet 
        WHERE alm_transferdet.nflgactivo = 1 
            AND alm_transferdet.idcprod = 5537
            AND alm_transferdet.idcostos = 34
    ) AS t ON t.idcprod = items.id_cprod
    WHERE
        items.flgActivo = 1 
        AND items.ntipo = 37 
        AND items.id_cprod = 5537 
    ORDER BY
        items.cdesprod ASC*/


                $sql = $this->db->connect()->prepare("SELECT DISTINCT
                                                        cm_producto.id_cprod,
                                                        cm_producto.ccodprod,
                                                        UPPER( cm_producto.cdesprod ) AS cdesprod,
                                                        tb_unimed.cabrevia,
                                                        r.ingresos,
                                                        i.inventarios,
                                                        i.condicion,
                                                        c.salidas,
                                                        d.devuelto,
                                                        t.transferencias,
                                                        m.minimo 
                                                    FROM
                                                        cm_producto
                                                        LEFT JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed
                                                        LEFT JOIN (
                                                                SELECT
                                                                    alm_existencia.codprod,
                                                                    SUM( alm_existencia.cant_ingr ) AS ingresos,
                                                                    alm_cabexist.idcostos 
                                                                FROM
                                                                    alm_existencia
                                                                    LEFT JOIN alm_cabexist ON alm_existencia.idregistro = alm_cabexist.idreg 
                                                                WHERE
                                                                    alm_cabexist.idcostos = :cingreso 
                                                                GROUP BY
                                                                    alm_existencia.codprod 
                                                                ) AS r ON r.codprod = cm_producto.id_cprod
                                                    LEFT JOIN (
                                                        SELECT DISTINCTROW
                                                            SUM( alm_inventariodet.cant_ingr ) AS inventarios,
                                                            alm_inventariodet.codprod,
                                                            alm_inventariodet.condicion 
                                                        FROM
                                                            alm_inventariodet
                                                            INNER JOIN alm_inventariocab ON alm_inventariodet.idregistro = alm_inventariocab.idreg 
                                                        WHERE
                                                            alm_inventariocab.idcostos = :cinventario 
                                                            AND alm_inventariodet.nflgActivo = 1 
                                                        GROUP BY
                                                            alm_inventariodet.codprod 
                                                        ) AS i ON i.codprod = cm_producto.id_cprod
                                                        LEFT JOIN (
                                                        SELECT
                                                            alm_consumo.idprod,
                                                            SUM( alm_consumo.cantsalida ) AS salidas 
                                                        FROM
                                                            alm_consumo 
                                                        WHERE
                                                            alm_consumo.ncostos = :csalida 
                                                            AND alm_consumo.flgactivo = 1 
                                                        GROUP BY
                                                            alm_consumo.idprod 
                                                        ) AS c ON c.idprod = cm_producto.id_cprod
                                                        LEFT JOIN (
                                                        SELECT
                                                            alm_consumo.idprod,
                                                            SUM( alm_consumo.cantdevolucion ) AS devuelto 
                                                        FROM
                                                            alm_consumo 
                                                        WHERE
                                                            alm_consumo.ncostos = :cdevolucion 
                                                            AND alm_consumo.flgactivo = 1 
                                                        GROUP BY
                                                            alm_consumo.idprod 
                                                        ) AS d ON d.idprod = cm_producto.id_cprod
                                                        LEFT JOIN (
                                                        SELECT
                                                            alm_transferdet.idcprod,
                                                            SUM( alm_transferdet.ncanti ) AS transferencias,
                                                            alm_transferdet.idcostos 
                                                        FROM
                                                            alm_transferdet 
                                                        WHERE
                                                            alm_transferdet.idcostos = :ctransferencia 
                                                            AND alm_transferdet.nflgactivo = 1 
                                                        GROUP BY
                                                            alm_transferdet.idcprod 
                                                        ) AS t ON t.idcprod = cm_producto.id_cprod
                                                        LEFT JOIN ( 
                                                            SELECT alm_minimo.dfecha, 
                                                                            alm_minimo.idprod, 
                                                                            alm_minimo.ncantidad AS minimo
                                                            FROM 
                                                            alm_minimo 
                                                            WHERE 
                                                            alm_minimo.ncostos = :cminimo 
                                                        ) AS m ON m.idprod = cm_producto.id_cprod 
                                                    WHERE
                                                        cm_producto.flgActivo = 1 
                                                        AND cm_producto.ntipo = 37 
                                                        AND (NOT ISNULL( r.ingresos ) OR NOT ISNULL( i.inventarios ) OR NOT ISNULL( c.salidas ) OR NOT ISNULL( t.transferencias	) )
                                                        AND cm_producto.ccodprod LIKE :codigo  
                                                    AND cm_producto.cdesprod LIKE :descripcion
                                                    ORDER BY
                                                        cm_producto.cdesprod ASC");
                $sql->execute(["cingreso" =>$cc,
                                "cinventario" =>$cc,
                                "csalida" =>$cc,
                                "cdevolucion" =>$cc,
                                "ctransferencia" =>$cc,
                                "cminimo" =>$cc,
                                "codigo" =>$cp,
                                "descripcion" =>$de]);
                $rowCount = $sql->rowCount();
                
                $item = 1;
                $salida = '<tr><td colspan="9">No hay registros para mostrar</td></tr>';

                if ($rowCount > 0) {
                    $salida="";
                    while ($rs = $sql->fetch()){
                        $saldo = ( $rs['ingresos']+$rs['inventarios']+$rs['devuelto'] )-$rs['salidas'];
                        $saldo = $saldo > -1 ? $saldo : $saldo;
                        $estado = $saldo > -1 ? "semaforoVerde":"semaforoRojo";

                        $alerta_minimo = ( $rs['minimo']*.7 ) > $saldo ? "semaforoRojo":"";

                        $c1 = ($rs['condicion'] == '1A' || $rs['condicion'] == '1.A.' || $rs['condicion'] == '1.A') ? number_format($rs['inventarios']) : "";
                        $c2 = ($rs['condicion'] == '1B' || $rs['condicion'] == '1.B.' || $rs['condicion'] == '1.B') ? number_format($rs['inventarios']) : "";
                        $c3 = ($rs['condicion'] == '2A' || $rs['condicion'] == '2.A.' || $rs['condicion'] == '2.A') ? number_format($rs['inventarios']) : "";
                        $c4 = ($rs['condicion'] == '2B' || $rs['condicion'] == '2.B.' || $rs['condicion'] == '2.B') ? number_format($rs['inventarios']) : "";
                        $c5 = ($rs['condicion'] == '3A' || $rs['condicion'] == '3.A.' || $rs['condicion'] == '3.A') ? number_format($rs['inventarios']) : "";
                        $c6 = ($rs['condicion'] == '3B' || $rs['condicion'] == '3.B.' || $rs['condicion'] == '3.B') ? number_format($rs['inventarios']) : "";
                        $c7 = ($rs['condicion'] == '3C' || $rs['condicion'] == '3.C.' || $rs['condicion'] == '3.C') ? number_format($rs['inventarios']) : "";

                        //if ( $saldo ){
                            $salida.='<tr class="pointer" data-idprod="'.$rs['id_cprod'].'" data-costos="'.$rs['ingresos'].'">
                                            <td class="textoCentro">'.str_pad($item++,4,0,STR_PAD_LEFT).'</td>
                                            <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                            <td class="pl20px">'.$rs['cdesprod'].'</td>
                                            <td class="textoCentro">'.$rs['cabrevia'].'</td>
                                            <td class="textoDerecha">'.number_format($rs['ingresos'],2).'</td>
                                            <td class="textoDerecha">'.number_format($rs['inventarios'],2).'</td>
                                            <td class="textoDerecha">'.number_format($rs['salidas'],2).'</td>
                                            <td class="textoDerecha">'.number_format($rs['devuelto'],2).'</td>
                                            <td class="textoDerecha">'.number_format($rs['transferencias'],2).'</td>
                                            <td class="textoDerecha '.$alerta_minimo.'">'.number_format($rs['minimo'],2).'</td>
                                            <td class="textoDerecha '.$estado.'"><div>'.number_format($saldo,2).'</div></td>
                                            <td class="textoCentro">'.$c1.'</td>
                                            <td class="textoCentro">'.$c2.'</td>
                                            <td class="textoCentro">'.$c3.'</td>
                                            <td class="textoCentro">'.$c4.'</td>
                                            <td class="textoCentro">'.$c5.'</td>
                                            <td class="textoCentro">'.$c6.'</td>
                                            <td class="textoCentro">'.$c7.'</td>
                                    </tr>';
                        //}
                    }
                }else {
                    $salida = '<tr colspan="8">No hay registros</tr>';
                }

                return $salida;

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function obtenerResumen($codigo){
            return  array("pedidos"=>$this->numeroPedidos($codigo),
                          "ordenes"=>$this->numeroOrdenes($codigo),
                          "inventario"=>$this->inventarios($codigo),
                          "ingresos"=>$this->verIngresos($codigo),
                          "pendientes"=>$this->pendientesOC($codigo),
                          "precios"=>$this->listaPrecios($codigo),
                          "existencias"=>$this->listaExistencias($codigo));
        }

        private function numeroPedidos($codigo){
            try {
                $sql=$this->db->connect()->prepare("SELECT
                                                        COUNT( tb_pedidodet.idprod ) AS numero_pedidos 
                                                    FROM
                                                        tb_pedidodet 
                                                    WHERE
                                                        tb_pedidodet.idprod = :codigo 
                                                        AND tb_pedidodet.nflgActivo = 1 
                                                        AND tb_pedidodet.idpedido != 0");
                $sql->execute(["codigo"=>$codigo]);
                $result = $sql->fetchAll();

                return $result[0]['numero_pedidos'];

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function numeroOrdenes($codigo){
            try {
                $sql=$this->db->connect()->prepare("SELECT
                                                        COUNT( lg_ordendet.id_cprod ) AS numero_orden 
                                                    FROM
                                                        lg_ordendet 
                                                    WHERE
                                                        lg_ordendet.id_cprod = :codigo
                                                    AND lg_ordendet.id_orden != 0");
                $sql->execute(["codigo"=>$codigo]);
                $result = $sql->fetchAll();

                if ( empty($result[0]['numero_orden'] ) ) 
                    return 0;
                else
                    return $result[0]['numero_orden'];
                
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function verIngresos($codigo){
            try {
                $sql=$this->db->connect()->prepare("SELECT
                                                    SUM( alm_existencia.cant_ingr ) AS ingresos 
                                                FROM
                                                    alm_existencia 
                                                WHERE
                                                    alm_existencia.codprod = :codigo");
                $sql->execute(["codigo"=>$codigo]);
                $result = $sql->fetchAll();

                return isset($result[0]['ingresos']) ? $result[0]['ingresos'] : 0;
                
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function pendientesOC($codigo){
            try {
                $sql=$this->db->connect()->prepare("SELECT
                                                    SUM( lg_ordendet.ncanti ) AS cantidad_pendiente 
                                                FROM
                                                    lg_ordendet 
                                                WHERE
                                                    lg_ordendet.id_cprod = :codigo 
                                                    AND lg_ordendet.nEstadoReg = 60");
                $sql->execute(["codigo"=>$codigo]);
                $result = $sql->fetchAll();

                return isset($result[0]['cantidad_pendiente']) ? $result[0]['cantidad_pendiente'] : 0;
                
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function listaPrecios($codigo){
            try {
                $salida = "";
                $sql=$this->db->connect()->prepare("SELECT
                                                        lg_ordendet.nunitario,
                                                        DATE_FORMAT( lg_ordencab.ffechadoc, '%d/%m/%Y' ) fecha,
                                                        tb_parametros.cabrevia,
                                                        lg_ordencab.ntcambio 
                                                    FROM
                                                        lg_ordendet
                                                        INNER JOIN lg_ordencab ON lg_ordendet.id_regmov = lg_ordencab.id_regmov
                                                        INNER JOIN tb_parametros ON lg_ordencab.ncodmon = tb_parametros.nidreg 
                                                    WHERE
                                                        lg_ordendet.id_cprod = :codigo 
                                                        AND lg_ordendet.id_orden IS NOT NULL
                                                    GROUP BY lg_ordendet.nunitario,lg_ordencab.ffechadoc,lg_ordencab.ntcambio");
                $sql->execute(["codigo"=>$codigo]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()){
                        $salida .='<tr class="pointer">
                                        <td class="textoCentro">'.$rs['fecha'].'</td>
                                        <td class="textoCentro">'.$rs['cabrevia'].'</td>
                                        <td class="textoDerecha">'.$rs['ntcambio'].'</td>
                                        <td class="textoDerecha">'.$rs['nunitario'].'</td>
                                    </tr>';
                    }
                }else {
                    $salida = '<tr class="textoCentro"><td colspan="4">Sin registros anteriores</td></tr>';
                }

                return $salida;

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function listaExistencias($codigo){
            try {
                $salida = "";
                $sql=$this->db->connect()->prepare("SELECT
                                                        FORMAT(alm_existencia.cant_ingr,2) AS cant_ingr,
                                                        UPPER( tb_almacen.cdesalm ) AS almacen,
                                                        tb_proyectos.ccodproy,
                                                        tb_unimed.cabrevia 
                                                    FROM
                                                        alm_existencia
                                                        INNER JOIN alm_cabexist ON alm_existencia.idregistro = alm_cabexist.idreg
                                                        INNER JOIN tb_almacen ON alm_existencia.idalm = tb_almacen.ncodalm
                                                        INNER JOIN tb_proyectos ON alm_cabexist.idcostos = tb_proyectos.nidreg
                                                        INNER JOIN cm_producto ON alm_existencia.codprod = cm_producto.id_cprod
                                                        INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed 
                                                    WHERE
                                                        alm_existencia.codprod = :codigo");
                $sql->execute(["codigo"=>$codigo]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()){
                        $salida .='<tr class="pointer">
                                        <td class="pl20px">'.$rs['ccodproy'].'</td>
                                        <td class="textoCentro">'.$rs['cabrevia'].'</td>
                                        <td class="textoDerecha">'.$rs['cant_ingr'].'</td>
                                        <td class="textoDerecha"></td>
                                        <td class="textoDerecha">'.$rs['cant_ingr'].'</td>
                                        <td class="textoDerecha">'.$rs['almacen'].'</td>
                                    </tr>';
                    }
                }else {
                    $salida = '<tr class="textoCentro"><td colspan="4">Sin registros anteriores</td></tr>';
                }

                return $salida;

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }
    
        private function inventarios($codigo){
            try {
                $sql=$this->db->connect()->prepare("SELECT
                                                        SUM( alm_inventariodet.cant_ingr ) AS inventario 
                                                    FROM
                                                        alm_inventariodet 
                                                    WHERE
                                                        alm_inventariodet.codprod = :codigo");
                $sql->execute(["codigo"=>$codigo]);
                $result = $sql->fetchAll();

                
                return isset( $result[0]['inventario'] ) ? $result[0]['inventario'] : 0;

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function exportarExcel($registros) {
            try {
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
                $objPHPExcel->getActiveSheet()->setTitle("Inventario");

                //combinar celdas
                $objPHPExcel->getActiveSheet()->mergeCells('A1:H1');

                //alineacion
                $objPHPExcel->getActiveSheet()->getStyle('A1:H4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('A1:H4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $objPHPExcel->getActiveSheet()->getStyle('A1:H5')->getAlignment()->setWrapText(true);

                //ancho de columnas
                $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
                $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
                $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(27);
                $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
                $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
                $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
                $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
                        
                //Titulo 
                $objPHPExcel->getActiveSheet()->setCellValue('A1','Control de Almacén');

                $objPHPExcel->getActiveSheet()
                    ->getStyle('A1:H4')
                    ->getFill()
                    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setRGB('FDE9D9');

                $objPHPExcel->getActiveSheet()->setCellValue('A4','ITEM'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('B4','CODIGO'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('C4','DESCRIPCION'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('D4','UNIDAD'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('E4','CANTIDAD GUIAS'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('F4','INGRESO INVENTARIO'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('G4','CANTIDAD SALIDAS'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('H4','CANTIDAD DEVUELTO'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('I4','TRANSFERENCIAS'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('J4','SALDO'); // esto cambia
       
                $fila = 5;
                $datos = json_decode($registros);
                $nreg = count($datos);

                for ($i=0; $i < $nreg; $i++) { 
                    $objPHPExcel->getActiveSheet()->setCellValue('A'.$fila,$datos[$i]->item);
                    $objPHPExcel->getActiveSheet()->setCellValue('B'.$fila,$datos[$i]->codigo);
                    $objPHPExcel->getActiveSheet()->setCellValue('C'.$fila,$datos[$i]->descripcion);
                    $objPHPExcel->getActiveSheet()->setCellValue('D'.$fila,$datos[$i]->unidad);
                    $objPHPExcel->getActiveSheet()->setCellValue('E'.$fila,$datos[$i]->ingreso);
                    $objPHPExcel->getActiveSheet()->setCellValue('F'.$fila,$datos[$i]->inventario);
                    $objPHPExcel->getActiveSheet()->setCellValue('G'.$fila,$datos[$i]->salida);
                    $objPHPExcel->getActiveSheet()->setCellValue('H'.$fila,$datos[$i]->devuelto);
                    $objPHPExcel->getActiveSheet()->setCellValue('I'.$fila,$datos[$i]->transferencias);
                    $objPHPExcel->getActiveSheet()->setCellValue('J'.$fila,$datos[$i]->saldo);
                    
                    $fila++;
                }

                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');
                $objWriter->save('public/documentos/reportes/control.xlsx');

                return array("documento"=>'public/documentos/reportes/control.xlsx');

                exit();
               
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function registrarMinimo($parametros){
            try {
                $mensaje = "Error en el ingreso";
                $sw = false;

                $sql = $this->db->connect()->prepare("INSERT INTO alm_minimo 
                                                            SET iduser=:user,
                                                                idprod=:producto,
                                                                ncostos=:costos,
                                                                ncantidad=:cantidad");
                $sql->execute(["costos"=>$parametros["cc"],
                        "producto"=>$parametros["prod"],
                        "user"=>$_SESSION['iduser'],
                        "cantidad"=>$parametros["cantidad"]]);

                $rowCount = $sql->rowCount();

                if ($rowCount > 0){
                    $mensaje = "Se agrego el registro..";
                    $sw = true;
                }

                return array("mensaje"=>$mensaje,
                            "sw"=>$sw);

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }
    }
?>