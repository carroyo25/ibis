<?php
    class RecepcionModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }


        public function insertar($cabecera, $detalles,$series){
            try {

                $sql = $this->db->connect()->prepare("INSERT INTO alm_recepcab SET ctipmov =:mov");
                $sql->execute(["mov"=>"I"]);
                $rowCount = $sql->rowCount();
                
                if ($rowCount > 0) {
                    $indice = $this->lastInsertId("SELECT MAX(id_regalm) AS id FROM alm_recepcab");
                }

                return $indice;

            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
            }
        }

        private function grabarDetalles($id,$detalles){
            try {
               
            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }

        private function grabarSeries($id,$series) {
            try {
                //code...
            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;

            }
        }

        public function listarOrdenes(){
            try {
                $salida = "";
                $sql = $this->db->connect()->prepare("SELECT
                                                    ibis.lg_ordencab.id_regmov,
                                                    ibis.tb_costusu.ncodproy, 
                                                    ibis.lg_ordencab.id_refpedi, 
                                                    ibis.lg_ordencab.ntipdoc, 
                                                    ibis.lg_ordencab.cnumero, 
                                                    ibis.lg_ordencab.ffechadoc, 
                                                    ibis.lg_ordencab.nEstadoDoc, 
                                                    CONCAT_WS(' ',ibis.tb_proyectos.ccodproy,UPPER(ibis.tb_proyectos.cdesproy)) AS costos, 
                                                    CONCAT_WS(' ',ibis.tb_area.ccodarea,UPPER(ibis.tb_area.cdesarea)) AS area
                                                FROM
                                                    ibis.tb_costusu
                                                    INNER JOIN
                                                    ibis.lg_ordencab
                                                    ON 
                                                        ibis.tb_costusu.ncodproy = ibis.lg_ordencab.ncodpry
                                                    INNER JOIN
                                                    ibis.tb_proyectos
                                                    ON 
                                                        ibis.lg_ordencab.ncodpry = ibis.tb_proyectos.nidreg
                                                    INNER JOIN
                                                    ibis.tb_area
                                                    ON 
                                                        ibis.lg_ordencab.ncodarea = ibis.tb_area.ncodarea
                                                WHERE
                                                    ibis.tb_costusu.id_cuser = :usr AND
                                                    ibis.tb_costusu.nflgactivo = 1 AND
                                                    ibis.lg_ordencab.nEstadoDoc = 60");
                $sql->execute(["usr"=>$_SESSION['iduser']]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()) {
                        $salida.='<tr data-orden="'.$rs['id_regmov'].'">
                                    <td class="textoCentro">'.$rs['cnumero'].'</td>
                                    <td class="textoCentro">'.$rs['ffechadoc'].'</td>
                                    <td class="pl20px">'.$rs['area'].'</td>
                                    <td class="pl20px">'.$rs['costos'].'</td>
                                </tr>';
                    }
                }
                return $salida;
            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }

        public function consultarOrdenIdRecepcion($id){
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                        ibis.lg_ordencab.id_regmov,
                                                        ibis.lg_ordencab.cnumero,
                                                        ibis.lg_ordencab.ffechadoc,
                                                        ibis.lg_ordencab.ncodcos,
                                                        ibis.lg_ordencab.ncodarea,
                                                        ibis.lg_ordencab.id_centi,
                                                        ibis.lg_ordencab.ncodcot,
                                                        ibis.lg_ordencab.cnumcot,
                                                        ibis.lg_ordencab.nEstadoDoc,
                                                        ibis.lg_ordencab.id_refpedi,
                                                        UPPER( tb_pedidocab.concepto ) AS concepto,
                                                        UPPER( tb_pedidocab.detalle ) AS detalle,
                                                        UPPER(
                                                        CONCAT_WS( ' ', tb_proyectos.ccodproy, tb_proyectos.cdesproy )) AS costos,
                                                        ibis.lg_ordencab.ncodpry,
                                                        UPPER(
                                                        CONCAT_WS( ' ', tb_area.ccodarea, tb_area.cdesarea )) AS area,
                                                        ibis.lg_ordencab.ncodmon,
                                                        ibis.lg_ordencab.ntipmov,
                                                        ibis.lg_ordencab.ffechaent,
                                                        ibis.cm_entidad.crazonsoc,
                                                        ibis.cm_entidad.cnumdoc,
                                                        UPPER( tb_almacen.cdesalm ) AS cdesalm,
                                                        ibis.cm_entidad.cemail AS mail_entidad,
                                                        ibis.lg_ordencab.cverificacion,
                                                        LPAD(ibis.tb_pedidocab.nrodoc,6,0) AS pedido,
                                                        ibis.tb_pedidocab.nivelAten,
                                                        CONCAT_WS(' ',rrhh.tabla_aquarius.nombres,rrhh.tabla_aquarius.apellidos) AS solicita
                                                            FROM
                                                            ibis.lg_ordencab
                                                            INNER JOIN ibis.tb_pedidocab ON lg_ordencab.id_refpedi = tb_pedidocab.idreg
                                                            INNER JOIN ibis.tb_proyectos ON lg_ordencab.ncodcos = tb_proyectos.nidreg
                                                            INNER JOIN ibis.tb_area ON lg_ordencab.ncodarea = tb_area.ncodarea
                                                            INNER JOIN ibis.tb_parametros AS monedas ON lg_ordencab.ncodmon = monedas.nidreg
                                                            INNER JOIN ibis.tb_parametros AS tipos ON lg_ordencab.ntipmov = tipos.nidreg
                                                            INNER JOIN ibis.tb_parametros AS pagos ON lg_ordencab.ncodpago = pagos.nidreg
                                                            INNER JOIN ibis.tb_parametros AS estados ON lg_ordencab.nEstadoDoc = estados.nidreg
                                                            INNER JOIN ibis.cm_entidad ON lg_ordencab.id_centi = cm_entidad.id_centi
                                                            INNER JOIN ibis.tb_parametros AS transportes ON lg_ordencab.ctiptransp = transportes.nidreg
                                                            INNER JOIN ibis.tb_almacen ON lg_ordencab.ncodalm = tb_almacen.ncodalm
                                                            INNER JOIN rrhh.tabla_aquarius ON ibis.tb_pedidocab.idsolicita = rrhh.tabla_aquarius.internal 
                                                            WHERE
                                                        lg_ordencab.id_regmov =:id 
                                                        AND lg_ordencab.nflgactivo = 1");
                $sql->execute(["id"=>$id]);
                $docData = array();
                while($row=$sql->fetch(PDO::FETCH_ASSOC)){
                    $docData[] = $row;
                }

                return array("cabecera"=>$docData,
                            "detalles"=>$this->ordenDetalles($id));
            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }

        private function ordenDetalles($id) {
            try {
                $salida ="";
                $sql = $this->db->connect()->prepare("SELECT
                                                lg_ordendet.nitemord,
                                                lg_ordendet.id_regmov,
                                                lg_ordendet.niddeta,
                                                lg_ordendet.nidpedi,
                                                lg_ordendet.id_cprod,
                                                cm_producto.ccodprod,
                                                cm_producto.cdesprod,
                                                cm_producto.nund,
                                                tb_unimed.cabrevia,
                                                tb_pedidodet.idpedido,
                                                tb_pedidodet.nroparte,
                                                FORMAT(lg_ordendet.ncanti,2) AS cantidad
                                            FROM
                                                lg_ordendet
                                                INNER JOIN cm_producto ON lg_ordendet.id_cprod = cm_producto.id_cprod
                                                INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed
                                                INNER JOIN tb_pedidodet ON lg_ordendet.niddeta = tb_pedidodet.iditem 
                                            WHERE
                                                lg_ordendet.nitemord =:id");
                $sql->execute(["id"=>$id]);
                
                $rowCount = $sql->rowCount();
                if ($rowCount > 0) {
                    $item=1;
                    while ($rs = $sql->fetch()){
                        $salida.='<tr data-detorden="'.$rs['nitemord'].'" 
                                        data-idprod="'.$rs['id_cprod'].'"
                                        data-iddetped="'.$rs['nidpedi'].'">

                                    <td class="textoCentro"><a href="'.$rs['nitemord'].'"><i class="fas fa-barcode"></i></a></td>
                                    <td class="textoCentro">'.str_pad($item++,3,0,STR_PAD_LEFT).'</td>
                                    <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                    <td>'.$rs['cdesprod'].'</td>
                                    <td class="textoCentro">'.$rs['cabrevia'].'</td>
                                    <td class="textoDerecha pr20px">'.$rs['cantidad'].'</td>
                                    <td><input type="number" step="any" placeholder="0.00" onchange="(function(el){el.value=parseFloat(el.value).toFixed(2);})(this)"></td>
                                    <td><input type="text"></td>
                                    <td><input type="date"></td>
                                </tr>';
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }

        public function subirAdjuntos($codigo,$adjuntos){
           
        }

        public function lastInsertId($query) {
            try {
                $sql = $this->db->connect()->query($query);
                $sql->execute();
                $result = $sql->fetchAll();
                
                return $result[0]['id'];
            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;

            }
        }
    }
?>