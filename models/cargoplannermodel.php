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
                tb_pedidodet.cant_aprob,
                tb_pedidodet.cant_pedida,
                tb_pedidodet.estadoItem,
                cm_producto.ccodprod,
                UPPER(
                    CONCAT_WS(
                        ' ',
                        cm_producto.cdesprod,
                        tb_pedidodet.observaciones
                    )
                ) AS descripcion,
                tb_pedidocab.emision AS emision_pedido,
                LPAD(tb_pedidocab.nrodoc, 6, 0) AS pedido,
                tb_proyectos.ccodproy,
                UPPER(tb_area.cdesarea) AS area,
                tb_pedidocab.faprueba,
                tb_unimed.cabrevia AS unidad,
                ordenes.cnumero AS orden,
                ordenes.ffechadoc,
                ordenes.ffechaent,
                proveedores.crazonsoc,
                recepcion.ncantidad,
                recepcab.nnronota,
                recepcab.cnumguia
            FROM
                tb_pedidodet
            INNER JOIN cm_producto ON tb_pedidodet.idprod = cm_producto.id_cprod
            INNER JOIN tb_pedidocab ON tb_pedidodet.idpedido = tb_pedidocab.idreg
            INNER JOIN tb_proyectos ON tb_pedidocab.idcostos = tb_proyectos.nidreg
            INNER JOIN tb_area ON tb_pedidocab.idarea = tb_area.ncodarea
            INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed
            LEFT JOIN (
                SELECT
                    id_regmov,
                    cnumero,
                    ffechadoc,
                    ffechaent,
                    id_centi
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
                    id_regalm
                FROM
                    alm_recepdet
            ) AS recepcion ON recepcion.niddetaPed = tb_pedidodet.iditem
            AND recepcion.niddetaOrd = tb_pedidodet.idorden
            LEFT JOIN (SELECT id_regalm,nnronota,cnumguia FROM alm_recepcab ) AS recepcab ON recepcion.id_regalm = recepcab.id_regalm");
                $sql->execute();
                $rowCount = $sql->rowCount();
                $item = 1;

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()) {
                        $salida .='<tr class="pointer">
                                        <td class="textoCentro">'.str_pad($item++,6,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">0%</td>
                                        <td>'.$rs['ccodproy'].'</td>
                                        <td>'.$rs['area'].'</td>
                                        <td></td>
                                        <td>'.$rs['pedido'].'</td>
                                        <td>'.$rs['faprueba'].'</td>
                                        <td>'.$rs['ccodprod'].'</td>
                                        <td>'.$rs['unidad'].'</td>
                                        <td width="20%">'.$rs['descripcion'].'</td>
                                        <td>'.$rs['cant_pedida'].'</td>
                                        <td>'.$rs['orden'].'</td>
                                        <td>'.$rs['ffechadoc'].'</td>
                                        <td>'.$rs['crazonsoc'].'</td>
                                        <td>'.$rs['ncantidad'].'</td>
                                        <td></td>
                                        <td>'.$rs['ffechadoc'].'</td>
                                        <td></td>
                                        <td>'.$rs['ffechaent'].'</td>
                                        <td></td>
                                        <td></td>
                                        <td>'.$rs['nnronota'].'</td>
                                        <td>'.$rs['cnumguia'].'</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
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

        private function catidadesRecepcio($pedido,$orden) {
            try {
                $sql = $this->db->connect()->prepare("SELECT");
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }
    }
?>