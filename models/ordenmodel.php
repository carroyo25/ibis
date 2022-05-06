<?php
    class OrdenModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarOrdenes($user){

        }

        public function importarPedidos(){
            try {
                $salida = "";
                $sql = $this->db->connect()->prepare("SELECT
                                                            tb_pedidocab.idcostos,
                                                            tb_pedidocab.idarea,
                                                            tb_pedidocab.idtrans,
                                                            tb_pedidocab.nrodoc,
                                                            UPPER(
                                                            CONCAT_WS( ' ', tb_proyectos.ccodproy, tb_proyectos.cdesproy )) AS costos,
                                                            UPPER(
                                                            CONCAT_WS( ' ', tb_area.ccodarea, tb_area.cdesarea )) AS area,
                                                            cm_producto.ccodprod,
                                                            cm_producto.cdesprod,
                                                            tb_pedidodet.idpedido,
                                                            tb_pedidocab.emision,
                                                            UPPER( tb_pedidocab.concepto ) AS concepto,
                                                            tb_pedidocab.detalle,
                                                            tb_pedidodet.iditem,
                                                            cm_entidad.crazonsoc,
                                                            cm_entidad.id_centi,
                                                            tb_pedidodet.idproforma,
                                                            FORMAT( tb_pedidodet.cant_aprob, 2 ) AS cantidad,
                                                            FORMAT( tb_pedidodet.precio, 2 ) AS precio,
                                                            FORMAT((
                                                                    tb_pedidodet.precio * tb_pedidodet.cant_aprob 
                                                                    ) + ( tb_pedidodet.precio * tb_pedidodet.cant_aprob ) *
                                                            IF
                                                                ( lg_proformacab.nigv > 0, 0.18, 0 ),
                                                                2 
                                                            ) AS total,
                                                            FORMAT(( tb_pedidodet.precio * tb_pedidodet.cant_aprob ) * IF ( lg_proformacab.nigv > 0, 0.18, 0 ), 2 ) AS igv,
                                                            tb_unimed.cabrevia AS desunidad,
                                                            monedas.cdescripcion,
                                                            monedas.cabrevia AS desmoneda,
                                                            tb_pedidodet.nroparte,
                                                            monedas.nidreg AS moneda 
                                                        FROM
                                                            tb_pedidodet
                                                            INNER JOIN tb_pedidocab ON tb_pedidodet.idpedido = tb_pedidocab.idreg
                                                            INNER JOIN tb_costusu ON tb_pedidocab.idcostos = tb_costusu.ncodproy
                                                            INNER JOIN tb_proyectos ON tb_pedidocab.idcostos = tb_proyectos.nidreg
                                                            INNER JOIN tb_area ON tb_pedidocab.idarea = tb_area.ncodarea
                                                            INNER JOIN cm_producto ON tb_pedidodet.idprod = cm_producto.id_cprod
                                                            INNER JOIN cm_entidad ON tb_pedidodet.entidad = cm_entidad.cnumdoc
                                                            INNER JOIN lg_proformacab ON tb_pedidodet.idproforma = lg_proformacab.nprof
                                                            INNER JOIN tb_unimed ON tb_pedidodet.unid = tb_unimed.ncodmed
                                                            INNER JOIN tb_parametros AS monedas ON lg_proformacab.ncodmon = monedas.nidreg 
                                                        WHERE
                                                            tb_costusu.id_cuser = :user 
                                                            AND tb_costusu.nflgactivo = 1 
                                                            AND tb_pedidocab.estadodoc = 58");
                $sql->execute(["user"=>$_SESSION['iduser']]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()) {
                        $salida .='<tr class="pointer" data-pedido="'.$rs['idpedido'].'" 
                                                       data-iditem="'.$rs['iditem'].'" 
                                                       data-entidad="'.$rs['id_centi'].'"
                                                       data-proforma="'.$rs['idproforma'].'"
                                                       data-unidad="'.$rs['desunidad'].'"
                                                       data-cantidad="'.$rs['cantidad'].'"
                                                       data-precio="'.$rs['precio'].'"
                                                       data-igv="'.$rs['igv'].'"
                                                       data-total="'.$rs['total'].'"
                                                       data-nroparte="'.$rs['nroparte'].'"
                                                       data-moneda="'.$rs['moneda'].'"
                                                       data-desmoneda="'.$rs['desmoneda'].'">
                                        <td class="textoCentro">'.str_pad($rs['nrodoc'],6,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.date("d/m/Y", strtotime($rs['emision'])).'</td>
                                        <td class="pl5px">'.$rs['concepto'].'</td>
                                        <td class="pl5px">'.$rs['area'].'</td>
                                        <td class="pl5px">'.$rs['costos'].'</td>
                                        <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                        <td class="pl5px">'.$rs['cdesprod'].'</td>
                                        <td class="pl5px">'.$rs['crazonsoc'].'</td>
                                    </tr>';
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }
    }
?>