<?php
    class CargoplanModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarCargoPlan(){
            try {
                $salida = "";
                $sql=$this->db->connect()->prepare(" SELECT
                                                        tb_pedidodet.iditem,
                                                        tb_pedidodet.idpedido,
                                                        tb_pedidodet.idorden,
                                                        tb_pedidodet.idingreso,
                                                        tb_pedidodet.iddespacho,
                                                        tb_pedidodet.idguia,
                                                        tb_pedidodet.idprod,
                                                        tb_pedidodet.idcostos,
                                                        tb_pedidodet.idarea,
                                                        tb_pedidodet.unid,
                                                        tb_pedidodet.cant_pedida,
                                                        tb_pedidodet.cant_recib,
                                                        tb_pedidodet.cant_env,
                                                        tb_pedidodet.estadoItem,
                                                        tb_pedidodet.tipoAten,
                                                        tb_pedidodet.observaciones,
                                                        UPPER(
                                                        CONCAT_WS( ' ', tb_proyectos.ccodproy, tb_proyectos.cdesproy )) AS costos,
                                                        cm_producto.ccodprod,
                                                        cm_producto.cdesprod,
                                                        tb_unimed.cabrevia AS unidad,
                                                        tb_pedidocab.nrodoc,
                                                        lg_ordencab.cnumero,
                                                        UPPER(tb_pedidocab.concepto) AS concepto,
                                                        tb_pedidodet.cant_aprob,
                                                        tb_pedidodet.cant_atend,
                                                        estados.cdescripcion,
                                                        estados.cabrevia AS estado,
                                                        tb_pedidodet.idtipo,
                                                        tipos.cdescripcion AS tipo,
                                                        atencion.cdescripcion AS atencion,
                                                        YEAR(tb_pedidodet.fregsys) as anio,
                                                        tb_pedidodet.observaciones 
                                                        FROM
                                                            tb_pedidodet
                                                            LEFT JOIN cm_producto ON tb_pedidodet.idprod = cm_producto.id_cprod
                                                            INNER JOIN tb_unimed ON tb_pedidodet.unid = tb_unimed.ncodmed
                                                            INNER JOIN tb_pedidocab ON tb_pedidodet.idpedido = tb_pedidocab.idreg
                                                            LEFT JOIN lg_ordencab ON tb_pedidodet.idorden = lg_ordencab.id_regmov
                                                            INNER JOIN tb_proyectos ON tb_pedidodet.idcostos = tb_proyectos.nidreg
                                                            INNER JOIN tb_costusu ON tb_pedidodet.idcostos = tb_costusu.ncodproy
                                                            INNER JOIN tb_parametros AS estados ON tb_pedidodet.estadoItem = estados.nidreg
                                                            INNER JOIN tb_parametros AS tipos ON tb_pedidodet.idtipo = tipos.nidreg
                                                            INNER JOIN tb_parametros AS atencion ON tb_pedidodet.tipoAten = atencion.nidreg 
                                                        WHERE
                                                            tb_costusu.id_cuser = :usr 
                                                            AND tb_costusu.nflgactivo = 1");
                $sql->execute(["usr"=>$_SESSION['iduser']]);
                $rowCount = $sql->rowCount();
                $item = 1;
                if ($rowCount > 0){
                    while ($rs = $sql->fetch()){
                        $tipo = $rs['idtipo'] == 37 ? "B" : "S";

                        $salida .='<tr data-producto="'.$rs['idprod'].'"
                                     data-pedido="'.$rs['idpedido'].'" 
                                     data-orden="'.$rs['idorden'].'" 
                                     data-ingreso="'.$rs['idingreso'].'" 
                                     data-despacho="'.$rs['iddespacho'].'" 
                                     data-item ="'.$rs['iditem'].'"
                                     data-status="'.$rs['estadoItem'].'"
                                     class="pointer">
                                    <td class="textoCentro">'.str_pad($item++,4,0,STR_PAD_LEFT).'</td>
                                    <td class="textoCentro '.$rs['estado'].'">'.$rs['cdescripcion'].'</td>
                                    <td class="pl20px">'.$rs['costos'].'</td>
                                    <td class="textoCentro '.strtolower($rs['atencion']).'">'.$rs['atencion'].'</td>
                                    <td class="textoCentro">'.$tipo.'</td>
                                    <td class="textoCentro">'.$rs['anio'].'</td>
                                    <td class="textoCentro">'.str_pad($rs['nrodoc'],6,0,STR_PAD_LEFT).'</td>
                                    <td class="textoCentro">'.$rs['unidad'].'</td>
                                    <td class="pl20px">'.strtoupper($rs['cdesprod'].' '.$rs['observaciones']).'</td>
                                    <td class="textoCentro">'.$rs['cnumero'].'</td>
                                </tr>';
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        /*public function consultarCargoPlan($codigo,$pedido,$orden,$ingreso,$despacho,$item,$estado){
            $detallePedido = $this->detallePedido($item);
            $datosPedido = $this->pedido($pedido);
            $datosOrden = $this->orden($orden);
            $datosIngreso = $this->ingreso($ingreso,$item);
            $datosDespacho = $this->despacho($despacho,$item);

            return array("producto"=>$detallePedido,
                         "pedido"=>$datosPedido,
                         "orden"=>$datosOrden,
                         "ingreso"=>$datosIngreso,
                         "despacho"=>$datosDespacho);
        }*/
        
        private function detallePedido($item){
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                        tb_pedidodet.iditem,
                                                        tb_pedidodet.idpedido,
                                                        FORMAT( tb_pedidodet.cant_pedida, 2 ) AS pedida,
                                                        FORMAT( tb_pedidodet.cant_atend, 2 ) AS atendida,
                                                        FORMAT( tb_pedidodet.cant_aprob, 2 ) AS aprobada,
                                                        IFNULL( tb_pedidodet.cant_aprob, tb_pedidodet.cant_pedida ) AS cantidad,
                                                        tb_pedidodet.estadoItem,
                                                        cm_producto.ccodprod,
                                                        CONCAT_WS(' ', cm_producto.cdesprod, tb_pedidodet.observaciones ) AS producto,
                                                        tb_unimed.cabrevia,
                                                        tb_parametros.cabrevia AS estado,
                                                        tb_parametros.cdescripcion 
                                                    FROM
                                                        tb_pedidodet
                                                        INNER JOIN cm_producto ON tb_pedidodet.idprod = cm_producto.id_cprod
                                                        INNER JOIN tb_unimed ON tb_pedidodet.unid = tb_unimed.ncodmed
                                                        INNER JOIN tb_parametros ON tb_pedidodet.estadoItem = tb_parametros.nidreg 
                                                    WHERE
                                                        tb_pedidodet.iditem = :item");
                $sql->execute(['item'=>$item]);

                $docData = array();

                while($row=$sql->fetch(PDO::FETCH_ASSOC)){
                    $docData[] = $row;
                }

                return $docData;
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function pedido($pedido){
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                        LPAD( tb_pedidocab.nrodoc, 6, 0 ) AS pedido,
                                                        tb_pedidocab.aprueba,
                                                        tb_pedidocab.faprueba,
                                                        tb_pedidocab.emision,
                                                    IF
                                                        ( tb_pedidocab.idtipomov = 37, 'B', 'S' ) AS tipo,
                                                        tb_user.cnombres 
                                                    FROM
                                                        tb_pedidocab
                                                        INNER JOIN tb_user ON tb_pedidocab.aprueba = tb_user.iduser 
                                                    WHERE
                                                        tb_pedidocab.idreg = :pedido");
                $sql->execute(["pedido"=>$pedido]);

                $docData = array();

                while($row=$sql->fetch(PDO::FETCH_ASSOC)){
                    $docData[] = $row;
                }

                return $docData;

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function orden($orden){
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                        lg_ordencab.id_regmov, 
                                                        lg_ordencab.cnumero, 
                                                        lg_ordencab.ffechadoc, 
                                                        lg_ordencab.nEstadoReg, 
                                                        lg_ordencab.fechaLog, 
                                                        lg_ordencab.fechaOpe, 
                                                        lg_ordencab.FechaFin
                                                    FROM
                                                        lg_ordencab
                                                    WHERE
                                                        lg_ordencab.id_regmov = :orden");
                $sql->execute(["orden"=>$orden]);

                $docData = array();

                while($row=$sql->fetch(PDO::FETCH_ASSOC)){
                    $docData[] = $row;
                }

                return $docData;

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function ingreso($ingreso,$item){
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                            FORMAT( alm_recepdet.ncantidad, 2 ) AS cantidad, 
                                            alm_recepdet.niddetaPed, 
                                            alm_recepdet.id_regalm, 
                                            alm_recepcab.nnronota, 
                                            alm_recepcab.ffecdoc
                                        FROM
                                            alm_recepdet
                                            INNER JOIN
                                            alm_recepcab
                                            ON 
                                                alm_recepdet.id_regalm = alm_recepcab.id_regalm
                                        WHERE
                                            alm_recepdet.niddetaPed = :item 
                                        AND
                                            alm_recepdet.id_regalm = :ingreso
                                        AND alm_recepdet.nflactivo = 1");
                $sql->execute(["ingreso"=>$ingreso, "item"=>$item]);

                $docData = array();

                while($row=$sql->fetch(PDO::FETCH_ASSOC)){
                    $docData[] = $row;
                }

                return $docData;

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function despacho($despacho,$item){
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                            alm_despachodet.niddeta,
                                                            alm_despachodet.id_regalm,
                                                            FORMAT(alm_despachodet.ncantidad,2) AS cantidad,
                                                            LPAD(alm_despachocab.nnronota, 6, 0 ) AS despacho,
                                                            alm_despachodet.niddetaPed,
                                                            alm_despachocab.ffecdoc 
                                                        FROM
                                                            alm_despachodet
                                                            INNER JOIN alm_despachocab ON alm_despachodet.id_regalm = alm_despachocab.id_regalm 
                                                        WHERE
                                                            alm_despachodet.niddetaOrd = :item 
                                                            AND alm_despachodet.id_regalm = :despacho");
                $sql->execute(["despacho"=>$despacho,"item"=>$item]);

                $docData = array();

                while($row=$sql->fetch(PDO::FETCH_ASSOC)){
                    $docData[] = $row;
                }

                return $docData;

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }
    }
?>