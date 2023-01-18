<?php
    class ValorizadoModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarOrdenes($costo){
            try {
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
                                                    AND lg_ordencab.ncodcos = :costo");
                $sql->execute(["costo" => $costo]);
                
                $rowCount = $sql->rowcount();

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()) {
                        $salida .= $this->detallesOrden($rs['id_regmov']);
                        $salida .='<tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td><b>Total Orden</b></td>
                                        <td></td>
                                        <td></td>
                                        <td class="textoDerecha">'.$rs['ntotal'].'</td>
                                        <td></td>
                                        <td class="textoDerecha">'.$rs['total_dolares'].'</td>
                                        <td class="textoDerecha">'.$rs['total_soles'].'</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
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
                                                    UPPER(
                                                    CONCAT_WS( '', cm_producto.cdesprod, tb_pedidodet.observaciones )) AS descripcion,
                                                    tb_unimed.cabrevia AS unidad,
                                                    cm_producto.ccodprod,
                                                    tb_proyectos.ccodproy,
                                                    UPPER( tb_proyectos.cdesproy ) AS proyecto,
                                                    UPPER( tb_area.cdesarea ) AS area,
                                                    lg_ordencab.ntcambio,
                                                    lg_ordencab.cper,
                                                    lg_ordencab.ntipmov,
                                                    DATE_FORMAT(lg_ordencab.ffechadoc,'%d/%m/%Y') AS fecha_registro,
                                                    cm_entidad.crazonsoc,
                                                    LPAD( tb_pedidocab.nrodoc, 6, 0 ) AS pedido,
                                                    lg_ordencab.ncodmon,
                                                    lg_ordencab.FechaFin,
                                                    lg_ordencab.nplazo,
                                                    lg_ordencab.cnumcot,
                                                    tb_pedidodet.nroparte,
                                                    tb_pedidodet.nregistro,
                                                    fpagos.cdescripcion,
                                                    cm_entidad.cnumdoc,
                                                    tb_grupo.cdescrip AS grupo,
                                                    cm_entidad.cviadireccion,
                                                    lg_ordencab.nfirmaOpe,
                                                    lg_ordencab.nfirmaFin,
                                                    lg_ordencab.nfirmaLog,
                                                    alm_recepdet.ncantidad,
                                                    tb_clase.cdescrip AS clase,
                                                    LPAD(lg_ordencab.id_regmov,6,0) AS orden,
                                                    tb_pedidocab.anio 
                                                FROM
                                                    lg_ordendet
                                                    INNER JOIN cm_producto ON lg_ordendet.id_cprod = cm_producto.id_cprod
                                                    INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed
                                                    INNER JOIN lg_ordencab ON lg_ordendet.id_regmov = lg_ordencab.id_regmov
                                                    INNER JOIN tb_pedidodet ON lg_ordendet.niddeta = tb_pedidodet.iditem
                                                    INNER JOIN tb_proyectos ON lg_ordendet.ncodcos = tb_proyectos.nidreg
                                                    INNER JOIN tb_area ON tb_pedidodet.idarea = tb_area.ncodarea
                                                    INNER JOIN cm_entidad ON lg_ordencab.id_centi = cm_entidad.id_centi
                                                    INNER JOIN tb_pedidocab ON tb_pedidodet.idpedido = tb_pedidocab.idreg
                                                    INNER JOIN tb_parametros AS fpagos ON lg_ordencab.ncodpago = fpagos.nidreg
                                                    INNER JOIN tb_grupo ON cm_producto.ngrupo = tb_grupo.ncodgrupo
                                                    LEFT JOIN alm_recepdet ON lg_ordendet.niddeta = alm_recepdet.niddeta
                                                    INNER JOIN tb_clase ON cm_producto.nclase = tb_clase.ncodclase
                                                WHERE lg_ordendet.id_orden = :id");

                $sql->execute(["id" => $id]);

                $rowCount = $sql->rowcount();

                $item = 1;

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()) {
                        $tipo = $rs['ntipmov'] == 37 ? 'B':'S';
                        $salida .='<tr class="pointer">
                                        <td class="textoCentro">'.str_pad($item++,4,0,STR_PAD_LEFT).'</td>
                                        <td class="pl20px">'.$rs['ccodproy'].'</td>
                                        <td class="pl20px">'.$rs['proyecto'].'</td>
                                        <td class="pl20px">'.$rs['area'].'</td>
                                        <td class="textoCentro">'.$rs['fecha_registro'].'</td>
                                        <td class="textoCentro">'.$rs['cper'].'</td>
                                        <td class="textoCentro">'.$tipo.'</td>
                                        <td class="textoCentro">'.$rs['anio'].'</td>
                                        <td class="textoCentro">'.$rs['pedido'].'</td>
                                        <td class="textoCentro">'.$rs['orden'].'</td>
                                        <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                        <td class="pl20px">'.$rs['descripcion'].'</td>
                                        <td class="textoCentro">'.$rs['unidad'].'</td>
                                        <td class="pl20px">'.$rs['crazonsoc'].'</td>
                                        <td class="textoDerecha">'.$rs['ncanti'].'</td>
                                        <td class="textoDerecha">'.$rs['nunitario'].'</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
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
                echo $th->getMessage();
                return false;
            }
        }
    }
?>