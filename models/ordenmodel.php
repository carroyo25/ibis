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
                                                        monedas.cabrevia AS abrmoneda,
                                                        tb_pedidodet.nroparte,
                                                        monedas.nidreg AS moneda,
                                                        pagos.cdescripcion AS pago,
                                                        pagos.ccod 
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
                                                        INNER JOIN tb_parametros AS pagos ON lg_proformacab.ccondpago = pagos.nidreg 
                                                    WHERE
                                                        tb_costusu.id_cuser =:user 
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
                                                       data-abrmoneda="'.$rs['abrmoneda'].'"
                                                       data-desmoneda="'.$rs['cdescripcion'].'"
                                                       data-pago="'.$rs['pago'].'">
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

        public function verDatosCabecera($pedido,$profoma,$entidad){
            $datosPedido = $this->datosPedido($pedido);
            $sql = "SELECT COUNT(lg_ordencab.id_regmov) AS numero FROM lg_ordencab WHERE lg_ordencab.ncodcos =:cod";
            
            $numero = $this->generarNumero($datosPedido[0]["idcostos"],$sql);
            $entidad = $this->datosEntidad($entidad);

            $salida = array("pedido"=>$datosPedido,
                            "orden"=>$numero,
                            "entidad"=>$entidad);

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
                                                        INNER JOIN rrhh.tabla_aquarius ON ibis.tb_pedidocab.idsolicita = rrhh.tabla_aquarius.internal
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

        private function datosEntidad($entidad){
            try {
                $sql=$this->db->connect()->prepare("SELECT
                                                        cm_entidad.cnumdoc,
                                                        cm_entidad.crazonsoc,
                                                        UPPER(cm_entidadcon.cnombres) AS contacto 
                                                    FROM
                                                        cm_entidadcon
                                                        INNER JOIN cm_entidad ON cm_entidadcon.id_centi = cm_entidad.id_centi 
                                                    WHERE
                                                        cm_entidad.id_centi =:entidad 
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
    }
?>