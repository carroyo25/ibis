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
                        tb_costusu.id_cuser,
                        tb_pedidodet.iditem,
                        tb_pedidodet.idtipo,
                        tb_pedidodet.cant_aprob,
                        tb_pedidodet.cant_pedida,
                        tb_pedidodet.cant_atend,
                        tb_pedidodet.estadoItem,
                        tb_pedidodet.idcostos,
                        tb_pedidodet.idtipo,
                        cm_producto.ccodprod,
                        tb_unimed.cabrevia AS unidad,
                        UPPER(CONCAT_WS(' ',cm_producto.cdesprod,tb_pedidodet.observaciones)) AS descripcion,
                        DATE_FORMAT(tb_pedidocab.emision,'%d/%m/%Y') AS emision_pedido,
                        DATE_FORMAT(tb_pedidocab.faprueba,'%d/%m/%Y') AS faprueba,
                        tb_pedidocab.concepto,LPAD(tb_pedidocab.idreg, 6, 0) AS pedido,
                        tb_proyectos.ccodproy,
                        UPPER(tb_area.cdesarea) AS area,
                        LPAD(detalles_orden.id_orden,6,0) AS orden,
                        FORMAT(detalles_orden.ncanti,2) AS cantidad_orden,
                        DATE_FORMAT(ffechadoc,'%d/%m/%Y') AS emision_orden,
                        DATE_FORMAT(ffechaent,'%d/%m/%Y') AS entrega_proveedor,
                        DATEDIFF(NOW(), cabecera_orden.ffechaent) AS retraso,
                        FORMAT(cabecera_orden.nplazo,0) AS nplazo,
                        recepcion_detalles.ncantidad AS cantidad_recibida,
                        DATE_FORMAT(recepcion_cabecera.ffecdoc,'%d/%m/%Y') AS fecha_recepcion,
                        proveedores.crazonsoc,
                        LPAD(recepcion_cabecera.id_regalm,6,0) AS nota_ingreso,
                        recepcion_cabecera.cnumguia AS guia_proveedor,
                        detalle_despacho.ncantidad AS cantidad_despachada,
                        LPAD(despacho_cabecera.id_regalm,6,0) AS nota_salida,
                        despacho_cabecera.cnumguia AS guia_remision_sepcon,
                        DATE_FORMAT(guias.ffechdoc,'%d/%m/%Y') AS fecha_guia_sepcon,
                        tb_parametros.cabrevia,
                        tb_parametros.cobservacion,
                        tb_partidas.ccodigo,
                        tb_partidas.cdescripcion
                        FROM
                        tb_costusu
                        INNER JOIN tb_pedidodet ON tb_costusu.ncodproy = tb_pedidodet.idcostos
                        INNER JOIN cm_producto ON tb_pedidodet.idprod = cm_producto.id_cprod
                        INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed
                        INNER JOIN tb_pedidocab ON tb_pedidodet.idpedido = tb_pedidocab.idreg
                        INNER JOIN tb_proyectos ON tb_pedidocab.idcostos = tb_proyectos.nidreg
                        INNER JOIN tb_area ON tb_pedidocab.idarea = tb_area.ncodarea
                        INNER JOIN tb_parametros ON tb_pedidocab.estadodoc = tb_parametros.nidreg
                        LEFT JOIN (SELECT id_orden,ncanti,niddeta FROM lg_ordendet) AS detalles_orden ON tb_pedidodet.iditem = detalles_orden.niddeta
                        LEFT JOIN (SELECT id_regmov,ffechadoc,ffechaent,id_centi,ncodmon,nplazo FROM lg_ordencab) AS cabecera_orden ON detalles_orden.id_orden = cabecera_orden.id_regmov
                        LEFT JOIN (SELECT id_centi,crazonsoc FROM cm_entidad) AS proveedores ON cabecera_orden.id_centi = proveedores.id_centi
                        LEFT JOIN (SELECT ncantidad,nsaldo,ncodalm1,id_regalm,niddetaPed,niddeta FROM alm_recepdet ) AS recepcion_detalles ON tb_pedidodet.iditem = recepcion_detalles.niddetaPed
                        LEFT JOIN (SELECT id_regalm,cnumguia,ffecdoc FROM alm_recepcab ) AS recepcion_cabecera ON recepcion_detalles.id_regalm = recepcion_cabecera.id_regalm
                        LEFT JOIN (SELECT ncantidad,niddetaPed,id_regalm,niddetaIng FROM alm_despachodet ) AS detalle_despacho ON recepcion_detalles.niddeta = detalle_despacho.niddetaIng
                        LEFT JOIN (SELECT id_regalm,cnumguia,ffecdoc FROM alm_despachocab) AS despacho_cabecera ON detalle_despacho.id_regalm = despacho_cabecera.id_regalm
                        LEFT JOIN tb_partidas ON tb_pedidocab.idpartida = tb_partidas.idreg
                        LEFT JOIN (SELECT id_despacho,ffechdoc,cnumero FROM lg_docusunat ) AS guias ON despacho_cabecera.id_regalm = guias.id_despacho
                        WHERE 
                            tb_costusu.id_cuser = :user 
                            AND tb_costusu.nflgactivo = 1
                            AND tb_pedidodet.nflgActivo = 1");
                
                $sql->execute(["user"=>$_SESSION['iduser']]);
                $rowCount = $sql->rowCount();
                $item = 1;
                $avance = 0;
                $colorAvance = "cero_avance";

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()) {
                        $cantidad = $rs['cant_aprob'] == null ? $rs['cant_pedida'] : $rs['cant_aprob'];

                        $saldo_recibir = $cantidad - $rs['cantidad_recibida'];
                        $saldo_mostrar = $saldo_recibir > 0 ? number_format($saldo_recibir, 2, '.', '') : " ";
                        $clase_avance = 0;

                        $retraso =  $rs['retraso'] <= 0 && $saldo_mostrar != "" ? " " : $rs['retraso'];
                        $tipo = $rs['idtipo'] == 37 ? "B" : "S";

                        $avance_entrega = ( $rs['cantidad_recibida']  * 100)/$cantidad;

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
                                        <td class="textoCentro">'.$rs['emision_orden'].'</td>
                                        <td class="textoDerecha pr10px">'.$rs['cantidad_orden'].'</td>
                                        <td>'.$rs['crazonsoc'].'</td>
                                        <td class="textoDerecha pr10px">'.$rs['entrega_proveedor'].'</td>
                                        <td class="textoCentro">'.$nplazo.'</td>
                                        <td class="textoDerecha pr10px">'.$retraso.'</td>
                                        <td class="textoCentro '.$clase_avance.'">'.$porcentaje_entrega.'</td>
                                        <td class="textoDerecha pr10px">'.$rs['cantidad_recibida'].'</td>
                                        <td class="textoCentro "></td>
                                        <td class="textoCentro">'.$rs['nota_ingreso'].'</td>
                                        <td>'.$rs['guia_proveedor'].'</td>
                                        <td class="textoCentro">'.$rs['fecha_recepcion'].'</td>
                                        <td class="textoCentro">'.$rs['nota_salida'].'</td>
                                        <td>'.$rs['guia_remision_sepcon'].'</td>
                                        <td class="textoCentro">'.$rs['fecha_guia_sepcon'].'</td>
                                        <td class="textoDerecha pr10px"></td>
                                        <td class="textoCentro"></td>
                                        <td class="textoCentro"></td>
                                        <td></td>
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
                                                        tb_costusu.id_cuser,
                                                        tb_pedidodet.iditem,
                                                        tb_pedidodet.idtipo,
                                                        tb_pedidodet.cant_aprob,
                                                        tb_pedidodet.cant_pedida,
                                                        tb_pedidodet.cant_atend,
                                                        tb_pedidodet.estadoItem,
                                                        tb_pedidodet.idcostos,
                                                        tb_pedidodet.idtipo,
                                                        cm_producto.ccodprod,
                                                        tb_unimed.cabrevia AS unidad,
                                                        UPPER(CONCAT_WS(' ',cm_producto.cdesprod,tb_pedidodet.observaciones)) AS descripcion,
                                                        DATE_FORMAT(tb_pedidocab.emision,'%d/%m/%Y') AS emision_pedido,
                                                        DATE_FORMAT(tb_pedidocab.faprueba,'%d/%m/%Y') AS faprueba,
                                                        tb_pedidocab.concepto,LPAD(tb_pedidocab.idreg, 6, 0) AS pedido,
                                                        tb_proyectos.ccodproy,
                                                        UPPER(tb_area.cdesarea) AS area,
                                                        LPAD(detalles_orden.id_orden,6,0) AS orden,
                                                        FORMAT(detalles_orden.ncanti,2) AS cantidad_orden,
                                                        DATE_FORMAT(ffechadoc,'%d/%m/%Y') AS emision_orden,
                                                        DATE_FORMAT(ffechaent,'%d/%m/%Y') AS entrega_proveedor,
                                                        DATEDIFF(NOW(), cabecera_orden.ffechaent) AS retraso,
                                                        FORMAT(cabecera_orden.nplazo,0) AS nplazo,
                                                        recepcion_detalles.ncantidad AS cantidad_recibida,
                                                        DATE_FORMAT(recepcion_cabecera.ffecdoc,'%d/%m/%Y') AS fecha_recepcion,
                                                        proveedores.crazonsoc,
                                                        LPAD(recepcion_cabecera.id_regalm,6,0) AS nota_ingreso,
                                                        recepcion_cabecera.cnumguia AS guia_proveedor,
                                                        detalle_despacho.ncantidad AS cantidad_despachada,
                                                        LPAD(despacho_cabecera.id_regalm,6,0) AS nota_salida,
                                                        despacho_cabecera.cnumguia AS guia_remision_sepcon,
                                                        DATE_FORMAT(guias.ffechdoc,'%d/%m/%Y') AS fecha_guia_sepcon,
                                                        tb_parametros.cabrevia,
                                                        tb_parametros.cobservacion,
                                                        tb_partidas.ccodigo,
                                                        tb_partidas.cdescripcion
                                                        FROM
                                                        tb_costusu
                                                        INNER JOIN tb_pedidodet ON tb_costusu.ncodproy = tb_pedidodet.idcostos
                                                        INNER JOIN cm_producto ON tb_pedidodet.idprod = cm_producto.id_cprod
                                                        INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed
                                                        INNER JOIN tb_pedidocab ON tb_pedidodet.idpedido = tb_pedidocab.idreg
                                                        INNER JOIN tb_proyectos ON tb_pedidocab.idcostos = tb_proyectos.nidreg
                                                        INNER JOIN tb_area ON tb_pedidocab.idarea = tb_area.ncodarea
                                                        INNER JOIN tb_parametros ON tb_pedidocab.estadodoc = tb_parametros.nidreg
                                                        LEFT JOIN (SELECT id_orden,ncanti,niddeta FROM lg_ordendet) AS detalles_orden ON tb_pedidodet.iditem = detalles_orden.niddeta
                                                        LEFT JOIN (SELECT id_regmov,ffechadoc,ffechaent,id_centi,ncodmon,nplazo FROM lg_ordencab) AS cabecera_orden ON detalles_orden.id_orden = cabecera_orden.id_regmov
                                                        LEFT JOIN (SELECT id_centi,crazonsoc FROM cm_entidad) AS proveedores ON cabecera_orden.id_centi = proveedores.id_centi
                                                        LEFT JOIN (SELECT ncantidad,nsaldo,ncodalm1,id_regalm,niddetaPed FROM alm_recepdet ) AS recepcion_detalles ON tb_pedidodet.iditem = recepcion_detalles.niddetaPed
                                                        LEFT JOIN (SELECT id_regalm,cnumguia,ffecdoc FROM alm_recepcab ) AS recepcion_cabecera ON recepcion_detalles.id_regalm = recepcion_cabecera.id_regalm
                                                        LEFT JOIN (SELECT ncantidad,niddetaPed,id_regalm FROM alm_despachodet ) AS detalle_despacho ON tb_pedidodet.iditem = detalle_despacho.niddetaPed
                                                        LEFT JOIN (SELECT id_regalm,cnumguia,ffecdoc FROM alm_despachocab) AS despacho_cabecera ON detalle_despacho.id_regalm = despacho_cabecera.id_regalm
                                                        LEFT JOIN tb_partidas ON tb_pedidocab.idpartida = tb_partidas.idreg
                                                        LEFT JOIN (SELECT id_despacho,ffechdoc,cnumero FROM lg_docusunat ) AS guias ON despacho_cabecera.id_regalm = guias.id_despacho
                                                        WHERE 
                                                            tb_costusu.id_cuser = :user 
                                                            AND tb_costusu.nflgactivo = 1
                                                            AND tb_pedidodet.idtipo = :tipo
                                                            AND tb_pedidodet.idcostos LIKE :costos
                                                            AND cm_producto.ccodprod LIKE :codigo
                                                            AND tb_pedidocab.idreg LIKE :pedido
                                                            AND IFNULL(cabecera_orden.id_regmov, '') LIKE :orden
                                                            AND tb_pedidocab.concepto LIKE :concepto");
                        $sql->execute(["tipo"=>$tipo,
                                        "costos"=>$costos,
                                        "codigo"=>$codigo,
                                        "pedido"=>$pedido,
                                        "orden"=>$orden,
                                        "concepto"=>$concepto,
                                        "user"=>$_SESSION['iduser']]);
                    

                        $rowCount = $sql->rowCount();
                        $item = 1;
                        $avance = 0;

                        if ($rowCount > 0) {
                            while ($rs = $sql->fetch()) {
                                $cantidad = $rs['cant_aprob'] == null ? $rs['cant_pedida'] : $rs['cant_aprob'];
        
                                $saldo_recibir = $cantidad - $rs['cantidad_recibida'];
                                $saldo_mostrar = $saldo_recibir > 0 ? number_format($saldo_recibir, 2, '.', '') : " ";
                                $clase_avance = 0;
        
                                $retraso =  $rs['retraso'] <= 0 && $saldo_mostrar != "" ? " " : $rs['retraso'];
                                $tipo = $rs['idtipo'] == 37 ? "B" : "S";
        
                                $avance_entrega = ( $rs['cantidad_recibida']  * 100)/$cantidad;
        
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
                                                <td class="textoCentro">'.$rs['emision_orden'].'</td>
                                                <td class="textoDerecha pr10px">'.$rs['cantidad_orden'].'</td>
                                                <td>'.$rs['crazonsoc'].'</td>
                                                <td class="textoDerecha pr10px">'.$rs['entrega_proveedor'].'</td>
                                                <td class="textoCentro">'.$nplazo.'</td>
                                                <td class="textoDerecha pr10px">'.$retraso.'</td>
                                                <td class="textoCentro '.$clase_avance.'">'.$porcentaje_entrega.'</td>
                                                <td class="textoDerecha pr10px">'.$rs['cantidad_recibida'].'</td>
                                                <td class="textoCentro "></td>
                                                <td class="textoCentro">'.$rs['nota_ingreso'].'</td>
                                                <td>'.$rs['guia_proveedor'].'</td>
                                                <td class="textoCentro">'.$rs['fecha_recepcion'].'</td>
                                                <td class="textoCentro">'.$rs['nota_salida'].'</td>
                                                <td>'.$rs['guia_remision_sepcon'].'</td>
                                                <td class="textoCentro">'.$rs['fecha_guia_sepcon'].'</td>
                                                <td class="textoDerecha pr10px"></td>
                                                <td class="textoCentro"></td>
                                                <td class="textoCentro"></td>
                                                <td></td>
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