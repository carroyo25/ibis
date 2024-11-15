<?php
    class StocksModel extends Model{

        public function __construct(){
            parent::__construct();
        }

        public function listarItems($parametros){
            try {
                $salida = '';
                $cc = $parametros['costosSearch'];
                $cp = $parametros['codigoBusqueda'] == "" ? "%" : "%".$parametros['codigoBusqueda']."%";
                $de = $parametros['descripcionSearch'] == "" ? "%" : "%".$parametros['descripcionSearch']."%";

            
                $sql = $this->db->connect()->prepare("SELECT
                                            cm_producto.id_cprod,
                                            cm_producto.ccodprod,
                                            UPPER( cm_producto.cdesprod ) AS cdesprod,
                                            recepcion.cantidad_obra AS ingresos,
                                            recepcion.idreg,
                                            inventarios.condicion,
                                            inventarios.inventarios_cantidad AS inventarios,
                                            SUM( consumo.cantsalida ) AS consumos,
                                            SUM( consumo.cantdevolucion ) AS devoluciones,
                                            sal_trans.salida_transferencia AS salidas_transferencia,
                                            sal_trans.iditem,
                                            ing_trans.ingreso_transferencia AS ingresos_transferencias,
                                            minimo.cantidad_minima AS minimo,
                                            tb_unimed.cabrevia,
                                            ajustes.ajustes_cantidad AS ajustes,
                                            UPPER( tb_grupo.cdescrip ) AS grupo,
                                            UPPER( tb_clase.cdescrip ) AS clase,
                                            UPPER( tb_familia.cdescrip ) AS familia 
                                        FROM
                                            cm_producto
                                            LEFT JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed
                                            LEFT JOIN (
                                            SELECT
                                                COUNT( alm_existencia.cant_ingr ) AS ingresos_obra,
                                                SUM( alm_existencia.cant_ingr ) AS cantidad_obra,
                                                alm_existencia.codprod,
                                                alm_existencia.idreg 
                                            FROM
                                                alm_existencia
                                                LEFT JOIN alm_cabexist ON alm_cabexist.idreg = alm_existencia.idregistro 
                                            WHERE
                                                alm_existencia.nflgActivo = 1 
                                                AND alm_cabexist.idcostos = :cingreso  
                                            GROUP BY
                                                alm_existencia.codprod 
                                            ) AS recepcion ON recepcion.codprod = cm_producto.id_cprod
                                            LEFT JOIN (
                                            SELECT
                                                COUNT( alm_inventariodet.cant_ingr ) AS inventarios_registros,
                                                SUM( alm_inventariodet.cant_ingr ) AS inventarios_cantidad,
                                                alm_inventariocab.idcostos,
                                                alm_inventariodet.codprod,
                                                alm_inventariodet.condicion 
                                            FROM
                                                alm_inventariodet
                                                INNER JOIN alm_inventariocab ON alm_inventariodet.idregistro = alm_inventariocab.idreg 
                                            WHERE
                                                alm_inventariodet.nflgActivo = 1 
                                                AND alm_inventariocab.idcostos = :cinventario 
                                            GROUP BY
                                                alm_inventariodet.codprod 
                                            ) AS inventarios ON inventarios.codprod = cm_producto.id_cprod
                                            LEFT JOIN (
                                            SELECT
                                                SUM( alm_consumo.cantsalida ) AS cantsalida,
                                                alm_consumo.cantdevolucion,
                                                alm_consumo.idprod 
                                            FROM
                                                alm_consumo 
                                            WHERE
                                                alm_consumo.ncostos = :csalida
                                                AND alm_consumo.flgactivo = 1 
                                            GROUP BY
                                                alm_consumo.fechasalida,
                                                alm_consumo.nrodoc,
                                                alm_consumo.idprod 
                                            ) AS consumo ON consumo.idprod = cm_producto.id_cprod
                                            LEFT JOIN (
                                            SELECT
                                                SUM( alm_transferdet.ncanti ) AS salida_transferencia,
                                                alm_transferdet.idcprod,
                                                alm_transferdet.iditem 
                                            FROM
                                                alm_transferdet
                                                LEFT JOIN alm_transfercab ON alm_transferdet.idtransfer = alm_transfercab.idreg 
                                            WHERE
                                                alm_transferdet.nflgactivo = 1 
                                                AND alm_transfercab.idcc = :ctransfsalida 
                                            GROUP BY
                                                alm_transferdet.idcprod 
                                            ) AS sal_trans ON sal_trans.idcprod = cm_producto.id_cprod
                                            LEFT JOIN (
                                            SELECT
                                                SUM( alm_transferdet.ncanti ) AS ingreso_transferencia,
                                                alm_transferdet.idcprod 
                                            FROM
                                                alm_transferdet
                                                LEFT JOIN alm_transfercab ON alm_transferdet.idtransfer = alm_transfercab.idreg 
                                            WHERE
                                                alm_transferdet.nflgactivo = 1 
                                                AND alm_transfercab.idcd = :ctransfingreso 
                                            GROUP BY
                                                alm_transferdet.idcprod 
                                            ) AS ing_trans ON ing_trans.idcprod = cm_producto.id_cprod
                                            LEFT JOIN (
                                            SELECT
                                                alm_minimo.dfecha,
                                                alm_minimo.idprod,
                                                alm_minimo.ncantidad AS cantidad_minima 
                                            FROM
                                                alm_minimo 
                                            WHERE
                                                alm_minimo.ncostos = :cminimo  
                                            GROUP BY
                                                alm_minimo.idprod,
                                                alm_minimo.dfecha 
                                            ) AS minimo ON minimo.idprod = cm_producto.id_cprod
                                            LEFT JOIN (
                                            SELECT
                                                COUNT( alm_ajustedet.cant_ingr ) AS ajustes_registros,
                                                SUM( alm_ajustedet.cant_ingr ) AS ajustes_cantidad,
                                                alm_ajustecab.idcostos,
                                                alm_ajustedet.codprod,
                                                alm_ajustedet.condicion 
                                            FROM
                                                alm_ajustedet
                                                INNER JOIN alm_ajustecab ON alm_ajustedet.idregistro = alm_ajustecab.idreg 
                                            WHERE
                                                alm_ajustedet.nflgActivo = 1 
                                                AND alm_ajustecab.idcostos = :cajuste 
                                                AND NOT ISNULL( alm_ajustecab.idrecepciona ) 
                                            GROUP BY
                                                alm_ajustedet.codprod 
                                            ) AS ajustes ON ajustes.codprod = cm_producto.id_cprod
                                            LEFT JOIN tb_grupo ON cm_producto.ngrupo = tb_grupo.ncodgrupo
                                            LEFT JOIN tb_clase ON cm_producto.nclase = tb_clase.ncodclase
                                            LEFT JOIN tb_familia ON cm_producto.nfam = tb_familia.ncodfamilia 
                                        WHERE
                                            cm_producto.flgActivo = 1 
                                            AND cm_producto.ntipo = 37 
                                            AND cm_producto.ccodprod LIKE :codigo 
                                            AND cm_producto.cdesprod LIKE :descripcion 
                                            AND ( NOT ISNULL( recepcion.cantidad_obra ) OR NOT ISNULL( inventarios.inventarios_cantidad ) OR NOT ISNULL( sal_trans.salida_transferencia ) ) 
                                        GROUP BY
                                            cm_producto.id_cprod 
                                        ORDER BY
                                            cm_producto.cdesprod ASC");
                $sql->execute(["cingreso" =>$cc,
                                "cinventario" =>$cc,
                                "csalida" =>$cc,
                                "ctransfsalida" =>$cc,
                                "ctransfingreso" =>$cc,
                                "cajuste" =>$cc,
                                "cminimo" =>$cc,
                                "codigo" =>$cp,
                                "descripcion" =>$de]);
                $rowCount = $sql->rowCount();
                
                $item = 1;
                $salida = '<tr><td colspan="9">No hay registros para mostrar</td></tr>';

                $_SESSION['progreso'] = 0;
                session_write_close();

                if ($rowCount > 0) {
                    $salida="";
                    while ($rs = $sql->fetch()){

                        $_SESSION['progreso']+=1;
                        session_write_close();
                        //sleep(1);

                        $saldo = ( $rs['ingresos']+$rs['inventarios']+$rs['devoluciones'] )-($rs['consumos']+$rs['salidas_transferencia'] ) + $rs['ajustes'];
                        
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
                            $salida.='<tr class="pointer" data-idprod="'.$rs['id_cprod'].'" 
                                                        data-costos="'.$rs['ingresos'].'" 
                                                        data-existencia="'.$rs['idreg'].'"
                                                        data-transferencia="'.$rs['iditem'].'"
                                                        data-grupo="'.$rs['grupo'].'"
                                                        data-clase="'.$rs['clase'].'"
                                                        data-familia="'.$rs['familia'].'">
                                            <td class="textoCentro"><a href="'.$rs['id_cprod'].'">'.str_pad($item++,4,0,STR_PAD_LEFT).'</a></td>
                                            <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                            <td class="pl20px">'.$rs['cdesprod'].'</td>
                                            <td class="textoCentro">'.$rs['cabrevia'].'</td>
                                            <td class="textoDerecha">'.number_format($rs['ingresos'],2).'</td>
                                            <td class="textoDerecha">'.number_format($rs['inventarios'],2).'</td>
                                            <td class="textoDerecha">'.number_format($rs['consumos'],2).'</td>
                                            <td class="textoDerecha">'.number_format($rs['devoluciones'],2).'</td>
                                            <td class="textoDerecha">'.number_format($rs['salidas_transferencia'],2).'</td>
                                            <td class="textoDerecha">'.number_format($rs['ajustes'],2).'</td>
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

                $_SESSION['progreso'] = 0;
                //session_write_close();

                return $salida;

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function obtenerResumen($codigo,$cc){
            return  array("pedidos"=>$this->numeroPedidos($codigo,$cc),
                          "ordenes"=>$this->numeroOrdenes($codigo,$cc),
                          "recepcion"=>$this->numeroRecepcion($codigo,$cc),
                          "despacho"=>$this->numeroDespacho($codigo,$cc),
                          "existencias"=>$this->numeroIngresosObra($codigo,$cc),
                          "inventarios"=>$this->numeroInventarios($codigo,$cc),
                          "transferencias"=>$this->numeroTransferencias($codigo,$cc),
                          "consumos"=>$this->numeroConsumos($codigo,$cc),
                          "devoluciones"=>$this->numeroDevolucion($codigo,$cc),
                          "minimos"=>$this->registrosMinimos($codigo,$cc),
                          "precios"=>$this->preciosProductos($codigo));
        }

        private function numeroPedidos($codigo,$cc){
            try {
                $sql=$this->db->connect()->prepare("SELECT
                                                        FORMAT(COUNT( tb_pedidodet.idprod ),2) AS numero,
                                                        FORMAT(SUM(tb_pedidodet.cant_aprob),2) AS cantidad
                                                    FROM
                                                        tb_pedidodet 
                                                    WHERE
                                                    tb_pedidodet.idprod = :codigo AND
                                                    tb_pedidodet.nflgActivo = 1 AND
                                                    tb_pedidodet.idcostos = :cc");
                $sql->execute(["codigo"=>$codigo,"cc"=>$cc]);
                $result = $sql->fetchAll();

                $numeros = 0;
                $cantidad = 0;

                if ($numeros >= 0){
                    $numeros = $result[0]['numero'];
                    $cantidad = $result[0]['cantidad'];
                }
               
                return array("numeros"=>$numeros,
                            "cantidad"=>$cantidad);

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function numeroOrdenes($codigo,$cc){
            try {
                $sql=$this->db->connect()->prepare("SELECT
                                                    FORMAT(SUM(lg_ordendet.ncanti),2) AS cantidad,
                                                    FORMAT(COUNT(lg_ordendet.ncanti),2) AS numero
                                                    FROM
                                                        lg_ordendet 
                                                    WHERE
                                                        lg_ordendet.ncodcos = :cc
                                                        AND lg_ordendet.id_cprod = :codigo
                                                        AND lg_ordendet.nEstadoReg != 105");
                $sql->execute(["codigo"=>$codigo,"cc"=>$cc]);
                $result = $sql->fetchAll();

                $numOrd = 0;
                $cantOrd = 0;

                if ($numOrd >= 0){
                    $numOrd = $result[0]['numero'];
                    $cantOrd = $result[0]['cantidad'];
                }
               
                return array("numeros"=>$numOrd,
                            "cantidad"=>$cantOrd);
                
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function numeroRecepcion($codigo,$cc){
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                FORMAT(SUM( alm_recepdet.ncantidad ),2) AS cantidad,
                                FORMAT(COUNT( alm_recepdet.ncantidad ),2) AS numero 
                            FROM
                                alm_recepdet
                                INNER JOIN alm_recepcab ON alm_recepdet.id_regalm = alm_recepcab.id_regalm 
                            WHERE
                                alm_recepdet.id_cprod = :codigo 
                                AND alm_recepcab.ncodpry = :cc");

                $sql->execute(["codigo"=>$codigo,"cc"=>$cc]);
                $result = $sql->fetchAll();

                $numeros = 0;
                $cantidad = 0;

                if ($numeros >= 0){
                    $numeros = $result[0]['numero'];
                    $cantidad = $result[0]['cantidad'];
                }

                return array("numeros"=>$numeros,
                            "cantidad"=>$cantidad);
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function numeroDespacho($codigo,$cc){
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                        FORMAT(SUM(alm_despachodet.ncantidad),2) AS cantidad,
                                                        FORMAT(COUNT(alm_despachodet.id_cprod),2) AS numero 
                                                    FROM
                                                        alm_despachodet
                                                        INNER JOIN alm_despachocab ON alm_despachodet.id_regalm = alm_despachocab.id_regalm 
                                                    WHERE
                                                        alm_despachodet.nflgactivo = 1 
                                                        AND alm_despachocab.ncodpry = :cc
                                                        AND alm_despachodet.id_cprod = :codigo");

                $sql->execute(["codigo"=>$codigo,"cc"=>$cc]);
                $result = $sql->fetchAll();

                $numeros = 0;
                $cantidad = 0;

                if ($numeros >= 0){
                    $numeros = $result[0]['numero'];
                    $cantidad = $result[0]['cantidad'];
                }

                return array("numeros"=>$numeros,
                            "cantidad"=>$cantidad);
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function numeroIngresosObra($codigo,$cc){
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                        FORMAT(SUM( alm_existencia.cant_ingr ),2) AS cantidad,
                                                        FORMAT(COUNT( alm_existencia.cant_ingr ),2) AS numero 
                                                    FROM
                                                        alm_existencia
                                                        LEFT JOIN alm_cabexist ON alm_existencia.idregistro = alm_cabexist.idreg 
                                                    WHERE
                                                        alm_existencia.nflgActivo = 1 
                                                        AND alm_cabexist.idcostos = :cc 
                                                        AND alm_existencia.codprod = :codigo");

                $sql->execute(["codigo"=>$codigo,"cc"=>$cc]);
                $result = $sql->fetchAll();

                $numeros = 0;
                $cantidad = 0;

                if ($numeros >= 0){
                    $numeros = $result[0]['numero'];
                    $cantidad = $result[0]['cantidad'];
                }

                return array("numeros"=>$numeros,
                            "cantidad"=>$cantidad);
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function numeroInventarios($codigo,$cc){
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                FORMAT( SUM( alm_inventariodet.cant_ingr ), 2 ) AS cantidad,
                                                FORMAT( COUNT( alm_inventariodet.cant_ingr ), 2 ) AS numero 
                                            FROM
                                                alm_inventariodet
                                                INNER JOIN alm_inventariocab ON alm_inventariodet.idregistro = alm_inventariocab.idreg 
                                            WHERE
                                                alm_inventariodet.nflgActivo = 1 
                                                AND alm_inventariocab.idcostos = :cc 
                                                AND alm_inventariodet.codprod = :codigo");

                $sql->execute(["codigo"=>$codigo,"cc"=>$cc]);
                $result = $sql->fetchAll();

                $numeros = 0;
                $cantidad = 0;

                if ($numeros >= 0){
                    $numeros = $result[0]['numero'];
                    $cantidad = $result[0]['cantidad'];
                }

                return array("numeros"=>$numeros,
                            "cantidad"=>$cantidad);
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function numeroTransferencias($codigo,$cc){
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                        FORMAT(SUM(alm_transferdet.ncanti),2) AS cantidad,
                                                        FORMAT(COUNT(alm_transferdet.ncanti),2) AS numero
                                                    FROM
                                                        alm_transferdet
                                                    WHERE 
                                                        alm_transferdet.nflgactivo = 1
                                                        AND alm_transferdet.idcostos = :cc
                                                        AND alm_transferdet.idcprod = :codigo");

                $sql->execute(["codigo"=>$codigo,"cc"=>$cc]);
                $result = $sql->fetchAll();

                $numeros = 0;
                $cantidad = 0;

                if ($numeros >= 0){
                    $numeros = $result[0]['numero'];
                    $cantidad = $result[0]['cantidad'];
                }

                return array("numeros"=>$numeros,
                            "cantidad"=>$cantidad);
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function numeroConsumos($codigo,$cc){
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                            FORMAT(SUM( consumo.cantsalida ),2) AS cantidad,
                                            FORMAT(COUNT( consumo.cantsalida ),2) AS numero 
                                        FROM
                                            (
                                            SELECT
                                                alm_consumo.cantsalida,
                                                alm_consumo.cantdevolucion,
                                                alm_consumo.idprod 
                                            FROM
                                                alm_consumo 
                                            WHERE
                                                alm_consumo.ncostos = :cc
                                                AND alm_consumo.idprod = :codigo 
                                                AND alm_consumo.flgactivo = 1 
                                            GROUP BY
                                                alm_consumo.fechasalida,
                                                alm_consumo.nrodoc,
                                                alm_consumo.idprod 
                                            ) AS consumo");

                $sql->execute(["codigo"=>$codigo,"cc"=>$cc]);
                $result = $sql->fetchAll();

                $numeros = 0;
                $cantidad = 0;

                if ($numeros >= 0){
                    $numeros = $result[0]['numero'];
                    $cantidad = $result[0]['cantidad'];
                }

                return array("numeros"=>$numeros,
                            "cantidad"=>$cantidad);
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function numeroDevolucion($codigo,$cc){
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                            FORMAT(SUM( consumo.cantdevolucion ),2) AS cantidad,
                                            FORMAT(COUNT( consumo.cantdevolucion ),2) AS numero 
                                        FROM
                                            (
                                            SELECT
                                                alm_consumo.cantsalida,
                                                alm_consumo.cantdevolucion,
                                                alm_consumo.idprod 
                                            FROM
                                                alm_consumo 
                                            WHERE
                                                alm_consumo.ncostos = :cc
                                                AND alm_consumo.idprod = :codigo 
                                                AND alm_consumo.flgactivo = 1 
                                            GROUP BY
                                                alm_consumo.fechasalida,
                                                alm_consumo.nrodoc,
                                            alm_consumo.idprod 
                                            ) AS consumo");

                $sql->execute(["codigo"=>$codigo,"cc"=>$cc]);
                $result = $sql->fetchAll();

                $numeros = 0;
                $cantidad = 0;

                if ($numeros >= 0){
                    $numeros = $result[0]['numero'];
                    $cantidad = $result[0]['cantidad'];
                }

                return array("numeros"=>$numeros,
                            "cantidad"=>$cantidad);
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function registrosMinimos($codigo,$cc){
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                        alm_minimo.dfecha,
                                                        FORMAT(alm_minimo.ncantidad,2) AS cantidad,
                                                        UPPER(tb_user.cnombres) AS nombres,
                                                        cm_producto.ccodprod,
                                                        cm_producto.cdesprod,
                                                        tb_unimed.cabrevia 
                                                    FROM
                                                        alm_minimo
                                                        INNER JOIN tb_user ON alm_minimo.iduser = tb_user.iduser
                                                        INNER JOIN cm_producto ON alm_minimo.idprod = cm_producto.id_cprod
                                                        INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed 
                                                    WHERE
                                                        alm_minimo.ncostos = :cc 
                                                        AND alm_minimo.idprod =  :codigo
                                                    ORDER BY
                                                        alm_minimo.dfecha DESC");

                $sql->execute(["codigo"=>$codigo,"cc"=>$cc]);
                $rowCount = $sql->rowCount();
                $salida = "";

                if ($rowCount > 0){
                    
                    while ($rs = $sql->fetch()){
                        $salida .= '<tr>
                                        <td class="textoCentro">'.$rs['dfecha'].'</td>
                                        <td class="textoDerecha">'.$rs['cantidad'].'</td>
                                        <td class="pl10px">'.$rs['nombres'].'</td>
                                    </tr>';
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function preciosProductos($codigo){
            try {
                $salida = "";
                $sql=$this->db->connect()->prepare("SELECT
                                                        FORMAT( lg_ordendet.nunitario, 2 ) AS nunitario,
                                                        DATE_FORMAT( lg_ordencab.ffechadoc, '%d/%m/%Y' ) AS fecha,
                                                        tb_parametros.cabrevia,
                                                        FORMAT( lg_ordencab.ntcambio, 2 ) AS ntcambio,
                                                        lg_ordencab.id_regmov,
                                                        tb_proyectos.ccodproy 
                                                    FROM
                                                        lg_ordendet
                                                        INNER JOIN lg_ordencab ON lg_ordendet.id_regmov = lg_ordencab.id_regmov
                                                        INNER JOIN tb_parametros ON lg_ordencab.ncodmon = tb_parametros.nidreg
                                                        INNER JOIN tb_proyectos ON lg_ordendet.ncodcos = tb_proyectos.nidreg 
                                                    WHERE
                                                        lg_ordendet.id_cprod = :codigo
                                                        AND lg_ordendet.id_orden <> 105 
                                                        AND lg_ordendet.id_orden <> 0 
                                                    GROUP BY
                                                        lg_ordendet.nunitario,
                                                        lg_ordencab.ffechadoc,
                                                        lg_ordencab.ntcambio 
                                                    ORDER BY
                                                        lg_ordencab.ffechadoc DESC");
                $sql->execute(["codigo"=>$codigo]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()){
                        $salida .='<tr class="pointer">
                                        <td class="textoCentro">'.$rs['fecha'].'</td>
                                        <td class="textoCentro">'.$rs['cabrevia'].'</td>
                                        <td class="textoDerecha">'.$rs['ntcambio'].'</td>
                                        <td class="textoDerecha">'.$rs['nunitario'].'</td>
                                        <td class="textoDerecha">'.$rs['id_regmov'].'</td>
                                        <td class="textoDerecha">'.$rs['ccodproy'].'</td>
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
                $objPHPExcel->getActiveSheet()->mergeCells('A1:T1');
                $objPHPExcel->getActiveSheet()->mergeCells('M3:S3');

                //alineacion
                $objPHPExcel->getActiveSheet()->getStyle('A1:V4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('A1:V4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


                //ancho de columnas
                $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
                $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
                $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
                $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
                $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
                $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
                $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
                $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);

                $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
                $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(15);
                $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(15);
                $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(15);
                $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(15);
                $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(15);
                $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(15);
                        
                //Titulo 
                $objPHPExcel->getActiveSheet()->setCellValue('A1','Control de Almacén');

                $objPHPExcel->getActiveSheet()
                    ->getStyle('A1:v4')
                    ->getFill()
                    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setRGB('FDE9D9');

                $objPHPExcel->getActiveSheet()->setCellValue('A3','ITEM'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('B3','CODIGO'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('C3','DESCRIPCION'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('D3','UNIDAD'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('E3','CANTIDAD GUIAS'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('F3','INGRESO INVENTARIO'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('G3','CANTIDAD SALIDAS'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('H3','CANTIDAD DEVUELTO'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('I3','TRANSFERENCIAS'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('J3','AJUSTE'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('K3','MINIMOS'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('L3','SALDO');
                $objPHPExcel->getActiveSheet()->setCellValue('M3','CONDICION'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('T3','GRUPO'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('U3','CLASE'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('V3','FAMILIA'); // esto cambia

                $objPHPExcel->getActiveSheet()->setCellValue('M4','1A');
                $objPHPExcel->getActiveSheet()->setCellValue('N4','1B');
                $objPHPExcel->getActiveSheet()->setCellValue('O4','2A');
                $objPHPExcel->getActiveSheet()->setCellValue('P4','2B');
                $objPHPExcel->getActiveSheet()->setCellValue('Q4','3A');
                $objPHPExcel->getActiveSheet()->setCellValue('R4','3B');
                $objPHPExcel->getActiveSheet()->setCellValue('S4','3C');

                $objPHPExcel->getActiveSheet()->getStyle('A3:T3')->getAlignment()->setWrapText(true);
       
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
                    $objPHPExcel->getActiveSheet()->setCellValue('J'.$fila,$datos[$i]->ajustes);
                    $objPHPExcel->getActiveSheet()->setCellValue('K'.$fila,$datos[$i]->minimos);
                    $objPHPExcel->getActiveSheet()->setCellValue('L'.$fila,$datos[$i]->saldo);

                    $objPHPExcel->getActiveSheet()->setCellValue('M'.$fila,$datos[$i]->a1);
                    $objPHPExcel->getActiveSheet()->setCellValue('N'.$fila,$datos[$i]->a2);
                    $objPHPExcel->getActiveSheet()->setCellValue('O'.$fila,$datos[$i]->b1);
                    $objPHPExcel->getActiveSheet()->setCellValue('P'.$fila,$datos[$i]->b2);
                    $objPHPExcel->getActiveSheet()->setCellValue('Q'.$fila,$datos[$i]->a3);
                    $objPHPExcel->getActiveSheet()->setCellValue('R'.$fila,$datos[$i]->b3);
                    $objPHPExcel->getActiveSheet()->setCellValue('S'.$fila,$datos[$i]->c3);

                    $objPHPExcel->getActiveSheet()->setCellValue('T'.$fila,$datos[$i]->grupo);
                    $objPHPExcel->getActiveSheet()->setCellValue('U'.$fila,$datos[$i]->clase);
                    $objPHPExcel->getActiveSheet()->setCellValue('V'.$fila,$datos[$i]->familia);
                    
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

        public function contarRegistros(){
            return $_SESSION['progreso']++;
	        session_write_close();
        }

        public function nroVueltas($parametros){
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                COUNT(*) AS registros
                                            FROM
                                                cm_producto 
                                            WHERE
                                                cm_producto.flgActivo = 1 
                                                AND cm_producto.ntipo = 37");
                $sql->execute();
                $result = $sql->fetchAll();

                return $result[0]['registros'];

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function registroPedidos($cc,$id){
            try {
                
                $docData = [];
                
                $sql = $this->db->connect()->prepare("SELECT
                                                        LPAD(tb_pedidocab.nrodoc,6,0) AS pedido,
                                                        tb_pedidodet.cant_pedida,
                                                        tb_pedidodet.cant_aprob,
                                                        emite.cnombres AS elabora,
                                                        aprueba.cnombres AS aprueba,
                                                        UPPER(tb_area.cdesarea) AS cdesarea,
                                                        DATE_FORMAT(tb_pedidocab.emision,'%d/%m/%Y') AS emision 
                                                    FROM
                                                        tb_pedidodet
                                                        INNER JOIN tb_pedidocab ON tb_pedidodet.idpedido = tb_pedidocab.idreg
                                                        INNER JOIN tb_user AS emite ON tb_pedidocab.usuario = emite.iduser
                                                        INNER JOIN tb_user AS aprueba ON tb_pedidocab.aprueba = aprueba.iduser
                                                        INNER JOIN tb_area ON tb_pedidocab.idarea = tb_area.ncodarea 
                                                    WHERE
                                                        tb_pedidodet.idprod =:id
                                                        AND tb_pedidodet.nflgActivo = 1 
                                                        AND tb_pedidodet.idcostos = :costo
                                                    ORDER BY tb_pedidocab.emision ASC");
                
                $sql->execute(["costo" =>$cc,
                                "id"    =>$id]);

                $rowCount = $sql->rowCount();
                
                if ($rowCount) {
                    while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                        $docData[] = $row;
                    }
                }

                return array("registros"=>$docData);


            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function registroOrdenes($cc,$id){
            try {
                
                $docData = [];
                
                $sql = $this->db->connect()->prepare("SELECT
                                                        lg_ordendet.nitemord,
                                                        LPAD( lg_ordencab.cnumero, 6, 0 ) AS numero,
                                                        DATE_FORMAT( lg_ordencab.ffechadoc, '%d/%m/%Y' ) AS emision,
                                                        lg_ordendet.ncanti,
                                                        UPPER( tb_area.cdesarea ) AS cdesarea 
                                                    FROM
                                                        lg_ordendet
                                                        INNER JOIN lg_ordencab ON lg_ordendet.id_regmov = lg_ordencab.id_regmov
                                                        INNER JOIN tb_area ON lg_ordencab.ncodarea = tb_area.ncodarea 
                                                    WHERE
                                                        lg_ordendet.id_cprod = :id 
                                                        AND lg_ordendet.ncodcos = :costo");
                
                $sql->execute(["costo" =>$cc,
                                "id"    =>$id]);

                $rowCount = $sql->rowCount();
                
                if ($rowCount) {
                    while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                        $docData[] = $row;
                    }
                }

                return array("registros"=>$docData);
                
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }
    }
?>