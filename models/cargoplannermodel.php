<?php
    class CargoPlannerModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarCargoPlan(){
            try {
                $salida="";
                $sql = $this->db->connect()->prepare("SELECT
                                                            tb_pedidodet.unid,
                                                            tb_pedidodet.iditem,
                                                            tb_pedidodet.idtipo,
                                                            FORMAT(tb_pedidodet.cant_aprob,2) AS cant_aprob,
                                                            FORMAT(tb_pedidodet.cant_pedida, 2) AS cant_pedida,
                                                            tb_pedidodet.estadoItem,
                                                            cm_producto.ccodprod,
                                                            tb_pedidodet.idtipo,
                                                            UPPER(
                                                                CONCAT_WS(
                                                                    ' ',
                                                                    cm_producto.cdesprod,
                                                                    tb_pedidodet.observaciones
                                                                )
                                                            ) AS descripcion,
                                                            DATE_FORMAT(tb_pedidocab.emision,'%d/%m/%Y') AS emision_pedido,
                                                            tb_pedidocab.concepto,
                                                            LPAD(tb_pedidocab.nrodoc, 6, 0) AS pedido,
                                                            tb_proyectos.ccodproy,
                                                            UPPER(tb_area.cdesarea) AS area,
                                                            DATE_FORMAT(tb_pedidocab.faprueba ,'%d/%m/%Y') AS faprueba,
                                                            tb_unimed.cabrevia AS unidad,
                                                            ordenes.cnumero AS orden,
                                                            DATE_FORMAT(
                                                                ordenes.ffechadoc,
                                                                '%d/%m/%Y'
                                                            ) AS ffechadoc,
                                                            DATE_FORMAT(
                                                                ordenes.ffechaent,
                                                                '%d/%m/%Y'
                                                            ) AS ffechaent,
                                                            proveedores.crazonsoc,
                                                            recepcion.ncantidad AS cant_recepcionada,
                                                            recepcab.nnronota,
                                                            recepcab.cnumguia,
                                                            despacho.ncantidad AS cant_despachada,
                                                            LPAD(despachocab.nnronota, 6, 0) AS nota_despacho,
                                                            guias.cnumero AS nro_guia,
                                                            DATE_FORMAT(guias.ffechdoc, '%d/%m/%Y') AS fecha_guia,
                                                            FORMAT(ingreso.cant_ingr, 2) AS ingreso_almacen,
                                                            LPAD(ingresocab.idreg, 6, 0) AS nota_recepcion,
                                                            DATE_FORMAT(
                                                                ingresocab.ffechadoc,
                                                                '%d/%m/%Y'
                                                            ) AS fecha_recepcion_almacen,
                                                            DATEDIFF(NOW(), ordenes.ffechaent) AS retraso,
                                                            tb_parametros.cabrevia,
                                                            tb_parametros.cobservacion,
                                                            ordenes.nplazo,
                                                            ingreso.observaciones,
                                                            tb_partidas.ccodigo,
                                                            tb_partidas.cdescripcion
                                                        FROM
                                                            tb_pedidodet
                                                        INNER JOIN cm_producto ON tb_pedidodet.idprod = cm_producto.id_cprod
                                                        INNER JOIN tb_pedidocab ON tb_pedidodet.idpedido = tb_pedidocab.idreg
                                                        INNER JOIN tb_proyectos ON tb_pedidocab.idcostos = tb_proyectos.nidreg
                                                        INNER JOIN tb_area ON tb_pedidocab.idarea = tb_area.ncodarea
                                                        INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed
                                                        LEFT JOIN tb_partidas ON tb_pedidocab.idpartida = tb_partidas.idreg
                                                        LEFT JOIN (
                                                            SELECT
                                                                id_regmov,
                                                                cnumero,
                                                                ffechadoc,
                                                                ffechaent,
                                                                id_centi,
                                                                nplazo
                                                            FROM
                                                                lg_ordencab
                                                        ) AS ordenes ON tb_pedidodet.idorden = ordenes.id_regmov
                                                        LEFT JOIN (
                                                            SELECT
                                                                id_centi,
                                                                crazonsoc
                                                            FROM
                                                                cm_entidad
                                                        ) AS proveedores ON ordenes.id_centi = proveedores.id_centi
                                                        LEFT JOIN (
                                                            SELECT
                                                                ncantidad,
                                                                niddetaPed,
                                                                niddetaOrd,
                                                                id_regalm,
                                                                niddeta
                                                            FROM
                                                                alm_recepdet
                                                        ) AS recepcion ON recepcion.niddetaPed = tb_pedidodet.iditem
                                                        AND recepcion.niddetaOrd = tb_pedidodet.idorden
                                                        LEFT JOIN (
                                                            SELECT
                                                                id_regalm,
                                                                nnronota,
                                                                cnumguia
                                                            FROM
                                                                alm_recepcab
                                                        ) AS recepcab ON recepcion.id_regalm = recepcab.id_regalm
                                                        LEFT JOIN (
                                                            SELECT
                                                                id_regalm,
                                                                ncantidad,
                                                                niddeta,
                                                                niddetaPed,
                                                                niddetaOrd,
                                                                niddetaIng
                                                            FROM
                                                                alm_despachodet
                                                        ) AS despacho ON despacho.niddetaPed = tb_pedidodet.iditem
                                                            AND despacho.niddetaOrd = tb_pedidodet.idorden
                                                            AND despacho.niddetaIng = recepcion.niddeta
                                                        LEFT JOIN (
                                                            SELECT
                                                                id_regalm,
                                                                ffecdoc,
                                                                nnronota
                                                            FROM
                                                                alm_despachocab
                                                        ) AS despachocab ON despacho.id_regalm = despachocab.id_regalm
                                                        
                                                        LEFT JOIN (
                                                            SELECT
                                                                id_despacho,
                                                                ffechdoc,
                                                                cnumero
                                                            FROM
                                                                lg_docusunat
                                                        ) AS guias ON despacho.id_regalm = guias.id_despacho
                                                        LEFT JOIN (
                                                            SELECT
                                                                idreg,
                                                                iddespacho,
                                                                cant_ingr,
                                                                idregistro,
                                                                observaciones
                                                            FROM
                                                                alm_existencia
                                                        ) AS ingreso ON despacho.niddeta = ingreso.iddespacho
                                                        LEFT JOIN (
                                                            SELECT
                                                                idreg,
                                                                ffechadoc
                                                            FROM
                                                                alm_cabexist
                                                        ) AS ingresocab ON ingreso.idregistro = ingresocab.idreg
                                                        INNER JOIN tb_parametros ON tb_pedidocab.estadodoc = tb_parametros.nidreg");
                $sql->execute();
                $rowCount = $sql->rowCount();
                $item = 1;
                $avance = 0;
                $colorAvance = "cero_avance";

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()) {
                        $cantidad = $rs['cant_aprob'] == null ? $rs['cant_pedida'] : $rs['cant_aprob'];

                        $saldo_recibir = $cantidad - $rs['cant_recepcionada'];
                        $saldo_mostrar = $saldo_recibir > 0 ? number_format($saldo_recibir, 2, '.', '') : " ";
                        $clase_avance = 0;

                        $retraso =  $rs['retraso'] <= 0 && $saldo_mostrar != "" ? " " : $rs['retraso'];
                        $tipo = $rs['idtipo'] == 37 ? "B" : "S";

                        $avance_entrega = ( $rs['cant_recepcionada']  * 100)/$cantidad;

                        $nplazo = (int)$rs['nplazo'] > 0 ? (int)$rs['nplazo']:" "; 

                        if ($avance_entrega > 0) {
                            $porcentaje_entrega = $avance_entrega."%";

                            if ($avance_entrega == 100) {
                                $clase_avance = "entrega__completa";
                            }else if ($avance_entrega <= 25) {
                                $clase_avance = "entrega__125";
                            }else if ($avance_entrega > 26) {
                                $clase_avance = "entrega__2699";
                            } 
                        }else {
                            $porcentaje_entrega = "";
                        }


                        $salida .='<tr class="pointer">
                                        <td class="textoCentro">'.str_pad($item++,6,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro '.$rs['cabrevia'].'" title="'.strtoupper($rs['cabrevia']).'">'.$rs['cobservacion'].'</td>
                                        <td class="textoDerecha pr10px">'.$rs['ccodproy'].'</td>
                                        <td class="pl20px">'.$rs['area'].'</td>
                                        <td class="pl20px">'.$rs['cdescripcion'].'</td>
                                        <td>'.$tipo.'</td>
                                        <td class="textoCentro">'.$rs['pedido'].'</td>
                                        <td class="textoCentro">'.$rs['emision_pedido'].'</td>
                                        <td class="textoCentro">'.$rs['faprueba'].'</td>
                                        <td class="pl20px">'.strtoupper($rs['concepto']).'</td>
                                        <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                        <td>'.$rs['unidad'].'</td>
                                        <td width="20%" class="pl20px">'.$rs['descripcion'].'</td>
                                        <td class="textoDerecha pr10px">'.$cantidad.'</td>
                                        <td class="textoCentro">'.$rs['orden'].'</td>
                                        <td class="textoCentro">'.$rs['ffechadoc'].'</td>
                                        <td>'.$rs['crazonsoc'].'</td>
                                        <td class="textoDerecha pr10px">'.$rs['cant_recepcionada'].'</td>
                                        <td class="textoDerecha pr10px">'.$saldo_mostrar.'</td>
                                        <td class="textoCentro">'.$rs['ffechadoc'].'</td>
                                        <td class="textoDerecha pr10px">'.$nplazo.'</td>
                                        <td class="textoCentro">'.$rs['ffechaent'].'</td>
                                        <td class="textoDerecha pr10px">'.$retraso.'</td>
                                        <td class="textoCentro '.$clase_avance.'">'.$porcentaje_entrega.'</td>
                                        <td class="textoCentro">'.$rs['nnronota'].'</td>
                                        <td>'.$rs['cnumguia'].'</td>
                                        <td class="textoCentro">'.$rs['nota_despacho'].'</td>
                                        <td>'.$rs['nro_guia'].'</td>
                                        <td class="textoCentro">'.$rs['fecha_guia'].'</td>
                                        <td class="textoDerecha pr10px">'.$rs['ingreso_almacen'].'</td>
                                        <td class="textoCentro">'.$rs['nota_recepcion'].'</td>
                                        <td class="textoCentro">'.$rs['fecha_recepcion_almacen'].'</td>
                                        <td>'.$rs['observaciones'].'</td>
                                    </tr>';
                    }
                }

                return $salida;
                
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function filtrarCargoPlan($parametros){
            try {
                $tipo = $parametros['tipoSearch'];
                $costos = $parametros['costosSearch'] == -1 ? "%" : "%".$parametros['costosSearch']."%";
                $codigo = "%".$parametros['codigoSearch']."%";
                $orden = "%".$parametros['ordenSearch']."%";
                $pedido = "%".$parametros['pedidoSearch']."%";
                $concepto = "%".$parametros['conceptoSearch']."%";

                $salida = "";

                    $sql = $this->db->connect()->prepare("SELECT
                                                    tb_pedidodet.unid,
                                                    tb_pedidodet.iditem,
                                                    tb_pedidodet.idtipo,
                                                    FORMAT(tb_pedidodet.cant_aprob,2) AS cant_aprob,
                                                    FORMAT(tb_pedidodet.cant_pedida, 2) AS cant_pedida,
                                                    tb_pedidodet.estadoItem,
                                                    cm_producto.ccodprod,
                                                    tb_pedidodet.idtipo,
                                                    UPPER(
                                                        CONCAT_WS(
                                                            ' ',
                                                            cm_producto.cdesprod,
                                                            tb_pedidodet.observaciones
                                                        )
                                                    ) AS descripcion,
                                                    DATE_FORMAT(
                                                        tb_pedidocab.emision,
                                                        '%d/%m/%Y'
                                                    ) AS emision_pedido,
                                                    tb_pedidocab.concepto,
                                                    LPAD(tb_pedidocab.nrodoc, 6, 0) AS pedido,
                                                    tb_proyectos.ccodproy,
                                                    UPPER(tb_area.cdesarea) AS area,
                                                    DATE_FORMAT(
                                                        tb_pedidocab.faprueba,
                                                        '%d/%m/%Y'
                                                    ) AS faprueba,
                                                    tb_unimed.cabrevia AS unidad,
                                                    ordenes.cnumero AS orden,
                                                    DATE_FORMAT(
                                                        ordenes.ffechadoc,
                                                        '%d/%m/%Y'
                                                    ) AS ffechadoc,
                                                    DATE_FORMAT(
                                                        ordenes.ffechaent,
                                                        '%d/%m/%Y'
                                                    ) AS ffechaent,
                                                    proveedores.crazonsoc,
                                                    recepcion.ncantidad AS cant_recepcionada,
                                                    recepcab.nnronota,
                                                    recepcab.cnumguia,
                                                    despacho.ncantidad AS cant_despachada,
                                                    LPAD(despachocab.nnronota, 6, 0) AS nota_despacho,
                                                    guias.cnumero AS nro_guia,
                                                    DATE_FORMAT(guias.ffechdoc, '%d/%m/%Y') AS fecha_guia,
                                                    FORMAT(ingreso.cant_ingr, 2) AS ingreso_almacen,
                                                    LPAD(ingresocab.idreg, 6, 0) AS nota_recepcion,
                                                    DATE_FORMAT(
                                                        ingresocab.ffechadoc,
                                                        '%d/%m/%Y'
                                                    ) AS fecha_recepcion_almacen,
                                                    DATEDIFF(NOW(), ordenes.ffechaent) AS retraso,
                                                    tb_parametros.cabrevia,
                                                    tb_parametros.cobservacion,
                                                    ordenes.nplazo,
                                                    ingreso.observaciones,
                                                    tb_pedidocab.idcostos,
                                                    tb_pedidocab.idarea,
                                                    tb_partidas.ccodigo,
                                                    tb_partidas.cdescripcion
                                                FROM
                                                    tb_pedidodet
                                                INNER JOIN cm_producto ON tb_pedidodet.idprod = cm_producto.id_cprod
                                                INNER JOIN tb_pedidocab ON tb_pedidodet.idpedido = tb_pedidocab.idreg
                                                INNER JOIN tb_proyectos ON tb_pedidocab.idcostos = tb_proyectos.nidreg
                                                INNER JOIN tb_area ON tb_pedidocab.idarea = tb_area.ncodarea
                                                INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed
                                                LEFT JOIN tb_partidas ON tb_pedidocab.idpartida = tb_partidas.idreg
                                                LEFT JOIN (
                                                    SELECT
                                                        id_regmov,
                                                        cnumero,
                                                        ffechadoc,
                                                        ffechaent,
                                                        id_centi,
                                                        nplazo
                                                    FROM
                                                        lg_ordencab
                                                ) AS ordenes ON tb_pedidodet.idorden = ordenes.id_regmov
                                                LEFT JOIN (
                                                    SELECT
                                                        id_centi,
                                                        crazonsoc
                                                    FROM
                                                        cm_entidad
                                                ) AS proveedores ON ordenes.id_centi = proveedores.id_centi
                                                LEFT JOIN (
                                                    SELECT
                                                        ncantidad,
                                                        niddetaPed,
                                                        niddetaOrd,
                                                        id_regalm,
                                                        niddeta
                                                    FROM
                                                        alm_recepdet
                                                ) AS recepcion ON recepcion.niddetaPed = tb_pedidodet.iditem
                                                AND recepcion.niddetaOrd = tb_pedidodet.idorden
                                                LEFT JOIN (
                                                    SELECT
                                                        id_regalm,
                                                        nnronota,
                                                        cnumguia
                                                    FROM
                                                        alm_recepcab
                                                ) AS recepcab ON recepcion.id_regalm = recepcab.id_regalm
                                                LEFT JOIN (
                                                    SELECT
                                                        id_regalm,
                                                        ncantidad,
                                                        niddeta,
                                                        niddetaPed,
                                                        niddetaOrd,
                                                        niddetaIng,
                                                        ingreso
                                                    FROM
                                                        alm_despachodet
                                                ) AS despacho ON despacho.niddetaPed = tb_pedidodet.iditem
                                                AND despacho.niddetaOrd = tb_pedidodet.idorden
                                                AND despacho.niddetaIng = recepcion.niddeta
                                                LEFT JOIN (
                                                    SELECT
                                                        id_regalm,
                                                        ffecdoc,
                                                        nnronota
                                                    FROM
                                                        alm_despachocab
                                                ) AS despachocab ON despacho.id_regalm = despachocab.id_regalm
                                                LEFT JOIN (
                                                    SELECT
                                                        id_despacho,
                                                        ffechdoc,
                                                        cnumero
                                                    FROM
                                                        lg_docusunat
                                                ) AS guias ON despacho.id_regalm = guias.id_despacho
                                                LEFT JOIN (
                                                    SELECT
                                                        idreg,
                                                        iddespacho,
                                                        cant_ingr,
                                                        idregistro,
                                                        observaciones
                                                    FROM
                                                        alm_existencia
                                                ) AS ingreso ON despacho.niddeta = ingreso.iddespacho
                                                LEFT JOIN (
                                                    SELECT
                                                        idreg,
                                                        ffechadoc
                                                    FROM
                                                        alm_cabexist
                                                ) AS ingresocab ON ingreso.idregistro = ingresocab.idreg
                                                INNER JOIN tb_parametros ON tb_pedidodet.estadoItem = tb_parametros.nidreg
                                                WHERE
                                                    tb_pedidodet.idtipo = :tipo
                                                    AND tb_pedidodet.idcostos LIKE :costos
                                                    AND cm_producto.ccodprod LIKE :codigo
                                                    AND tb_pedidocab.nrodoc LIKE :pedido
	                                                AND IFNULL(ordenes.cnumero, '') LIKE :orden
                                                    AND tb_pedidocab.concepto LIKE :concepto");

                        $sql->execute(["tipo"=>$tipo,
                                        "costos"=>$costos,
                                        "codigo"=>$codigo,
                                        "pedido"=>$pedido,
                                        "orden"=>$orden,
                                        "concepto"=>$concepto]);
                    

                        $rowCount = $sql->rowCount();
                        $item = 1;
                        $avance = 0;

                    if ($rowCount > 0) {
                        while ($rs = $sql->fetch()) {
                            $cantidad = $rs['cant_aprob'] == null ? $rs['cant_pedida'] : $rs['cant_aprob'];

                            $saldo_recibir = $cantidad - $rs['cant_recepcionada'];
                            $saldo_mostrar = $saldo_recibir > 0 ? number_format($saldo_recibir, 2, '.', '') : " ";
                            $clase_avance = 0;
    
                            $retraso =  $rs['retraso'] <= 0 && $saldo_mostrar != "" ? " " : $rs['retraso'];
                            $tipo = $rs['idtipo'] == 37 ? "B" : "S";
    
                            $avance_entrega = ( $rs['cant_recepcionada']  * 100)/$cantidad;

                            $nplazo = (int)$rs['nplazo'] > 0 ? (int)$rs['nplazo']:" "; 

                            if ($avance_entrega > 0) {
                                $porcentaje_entrega = $avance_entrega."%";

                                if ($avance_entrega == 100) {
                                    $clase_avance = "entrega__completa";
                                }else if ($avance_entrega <= 25) {
                                    $clase_avance = "entrega__125";
                                }else if ($avance_entrega > 26) {
                                    $clase_avance = "entrega__2699";
                                } 
                            }else {
                                $porcentaje_entrega = "";
                            }


                            $salida .='<tr class="pointer">
                                            <td class="textoCentro">'.str_pad($item++,6,0,STR_PAD_LEFT).'</td>
                                            <td class="textoCentro '.$rs['cabrevia'].'" title="'.strtoupper($rs['cabrevia']).'">'.$rs['cobservacion'].'</td>
                                            <td class="textoDerecha pr10px">'.$rs['ccodproy'].'</td>
                                            <td class="pl20px">'.$rs['area'].'</td>
                                            <td class="pl20px">'.$rs['cdescripcion'].'</td>
                                            <td>'.$tipo.'</td>
                                            <td class="textoCentro">'.$rs['pedido'].'</td>
                                            <td class="textoCentro">'.$rs['emision_pedido'].'</td>
                                            <td class="textoCentro">'.$rs['faprueba'].'</td>
                                            <td class="pl20px">'.strtoupper($rs['concepto']).'</td>
                                            <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                            <td>'.$rs['unidad'].'</td>
                                            <td width="20%" class="pl20px">'.$rs['descripcion'].'</td>
                                            <td class="textoDerecha pr10px">'.$cantidad.'</td>
                                            <td class="textoCentro">'.$rs['orden'].'</td>
                                            <td class="textoCentro">'.$rs['ffechadoc'].'</td>
                                            <td>'.$rs['crazonsoc'].'</td>
                                            <td class="textoDerecha pr10px">'.$rs['cant_recepcionada'].'</td>
                                            <td class="textoDerecha pr10px">'.$saldo_mostrar.'</td>
                                            <td class="textoCentro">'.$rs['ffechadoc'].'</td>
                                            <td class="textoDerecha pr10px">'.$nplazo.'</td>
                                            <td class="textoCentro">'.$rs['ffechaent'].'</td>
                                            <td class="textoDerecha pr10px">'.$retraso.'</td>
                                            <td class="textoCentro '.$clase_avance.'">'.$porcentaje_entrega.'</td>
                                            <td class="textoCentro">'.$rs['nnronota'].'</td>
                                            <td>'.$rs['cnumguia'].'</td>
                                            <td class="textoCentro">'.$rs['nota_despacho'].'</td>
                                            <td>'.$rs['nro_guia'].'</td>
                                            <td class="textoCentro">'.$rs['fecha_guia'].'</td>
                                            <td class="textoDerecha pr10px">'.$rs['ingreso_almacen'].'</td>
                                            <td class="textoCentro">'.$rs['nota_recepcion'].'</td>
                                            <td class="textoCentro">'.$rs['fecha_recepcion_almacen'].'</td>
                                            <td>'.$rs['observaciones'].'</td>
                                        </tr>';
                                    }
                }
            
            return $salida;

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function cantidadesRecepcio($pedido,$orden) {
            try {
                $sql = "";
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }
    }
?>