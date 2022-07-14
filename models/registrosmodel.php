<?php
    class RegistrosModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarGuias(){
            try {
                $salida = "";
                $sql = $this->db->connect()->prepare("SELECT
                                                        tb_almausu.nalmacen,
                                                        UPPER(tb_almacen.cdesalm) AS destino,
                                                        lg_docusunat.ffechdoc,
                                                        lg_docusunat.ffechtrasl,
                                                        lg_docusunat.cnumero,
                                                        lg_docusunat.nbultos,
                                                        lg_docusunat.npesotot,
                                                        alm_despachocab.nnronota,
                                                        UPPER(
                                                                CONCAT_WS(
                                                                    ' ',
                                                                    tb_proyectos.ccodproy,
                                                                    tb_proyectos.cdesproy
                                                                )
                                                            ) AS costos,
                                                        UPPER(tb_area.cdesarea) AS area,
                                                        tb_pedidocab.concepto,
                                                        alm_despachocab.id_regalm AS despacho,
                                                        YEAR (ffechdoc) AS anio,
                                                        LPAD(tb_pedidocab.nrodoc, 6, 0) AS pedido,
                                                        lg_ordencab.cnumero AS orden,
                                                        tb_parametros.cdescripcion AS estado
                                                        FROM
                                                        tb_almausu
                                                        INNER JOIN tb_almacen ON tb_almausu.nalmacen = tb_almacen.ncodalm
                                                        INNER JOIN lg_docusunat ON tb_almausu.nalmacen = lg_docusunat.ncodalm2
                                                        INNER JOIN alm_despachocab ON lg_docusunat.id_despacho = alm_despachocab.id_regalm
                                                        INNER JOIN tb_proyectos ON alm_despachocab.ncodpry = tb_proyectos.nidreg
                                                        INNER JOIN tb_area ON alm_despachocab.ncodarea = tb_area.ncodarea
                                                        INNER JOIN tb_pedidocab ON alm_despachocab.idref_pedi = tb_pedidocab.idreg
                                                        INNER JOIN lg_ordencab ON alm_despachocab.idref_ord = lg_ordencab.id_regmov
                                                        INNER JOIN tb_parametros ON lg_docusunat.nEstadoDoc = tb_parametros.nidreg
                                                        WHERE
                                                            tb_almausu.id_cuser = :usr
                                                        AND tb_almausu.nflgactivo = 1");
                $sql->execute(["usr"=>$_SESSION['iduser']]);
                $rowCount = $sql->rowcount();
                $item = 1;
                
                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()) {
                        $salida .= '<tr class="pointer" data-despacho="'.$rs['despacho'].'">
                                        <td class="textoCentro">'.str_pad($item++,4,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.date("d/m/Y", strtotime($rs['ffechdoc'])).'</td>
                                        <td class="pl20px">'.$rs['destino'].'</td>
                                        <td class="pl20px">'.$rs['costos'].'</td>
                                        <td class="textoCentro">'.$rs['anio'].'</td>
                                        <td class="textoCentro">'.$rs['orden'].'</td>
                                        <td class="textoCentro">'.$rs['pedido'].'</td>
                                        <td class="textoCentro">'.$rs['cnumero'].'</td>
                                        <td class="pl20px">'.$rs['concepto'].'</td>
                                        <td class="textoCentro">'.$rs['estado'].'</td>
                                    </tr>';
                    }
                }

                return $salida;

            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function importarDespacho($id){
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                        ibis.lg_docusunat.id_despacho,
                                                        ibis.lg_docusunat.cnumero AS guia,
                                                        ibis.lg_docusunat.cdocPDF,
                                                        ibis.lg_docusunat.ffechdoc,
                                                        ibis.lg_docusunat.ffechtrasl,
                                                        ibis.lg_docusunat.nEstadoDoc,
                                                        FORMAT(ibis.lg_docusunat.nbultos,2) AS nbultos,
                                                        FORMAT(ibis.lg_docusunat.npesotot,2) AS npesotot,
                                                        LPAD(ibis.tb_pedidocab.nrodoc,6,0) AS pedido,
                                                        ibis.tb_pedidocab.concepto,
                                                        ibis.tb_proyectos.ccodproy,
                                                        UPPER(ibis.tb_proyectos.cdesproy) AS costos,
                                                        ibis.tb_area.ccodarea,
                                                        UPPER(ibis.tb_area.cdesarea) AS area,
                                                        CONCAT_WS(
                                                                ' ',
                                                                rrhh.tabla_aquarius.apellidos,
                                                                rrhh.tabla_aquarius.nombres
                                                            ) AS solicita,
                                                        UPPER(origen.cdesalm) AS origen,
                                                        UPPER(ibis.tb_almacen.cdesalm) AS destino,
                                                        ibis.tb_pedidocab.emision,
                                                        ibis.lg_ordencab.cnumero AS orden,
                                                        ibis.tb_area.ncodarea AS codigo_area,
                                                        ibis.tb_proyectos.nidreg AS codigo_costos,
                                                        ibis.tb_pedidocab.idreg AS codigo_pedido,
                                                        ibis.lg_ordencab.id_regmov AS codigo_orden,
                                                        ibis.tb_almacen.ncodalm AS codigo_origen,
                                                        origen.ncodalm AS codigo_destino,
                                                        ibis.lg_ordencab.ffechadoc AS fecha_orden
                                                        FROM
                                                            ibis.lg_docusunat
                                                        INNER JOIN ibis.alm_despachocab ON ibis.lg_docusunat.id_despacho = ibis.alm_despachocab.id_regalm
                                                        INNER JOIN ibis.tb_pedidocab ON ibis.alm_despachocab.idref_pedi = ibis.tb_pedidocab.idreg
                                                        INNER JOIN ibis.tb_proyectos ON ibis.tb_pedidocab.idcostos = ibis.tb_proyectos.nidreg
                                                        INNER JOIN ibis.tb_area ON ibis.tb_pedidocab.idarea = ibis.tb_area.ncodarea
                                                        INNER JOIN rrhh.tabla_aquarius ON ibis.tb_pedidocab.idsolicita = rrhh.tabla_aquarius.internal
                                                        INNER JOIN ibis.tb_almacen AS origen ON ibis.alm_despachocab.ncodalm1 = origen.ncodalm
                                                        INNER JOIN ibis.tb_almacen ON ibis.alm_despachocab.ncodalm2 = ibis.tb_almacen.ncodalm
                                                        INNER JOIN ibis.lg_ordencab ON ibis.alm_despachocab.idref_ord = ibis.lg_ordencab.id_regmov
                                                        WHERE
                                                            ibis.lg_docusunat.id_despacho = :id");
                $sql->execute(["id"=>$id]);

                $docData = array();

                while($row=$sql->fetch(PDO::FETCH_ASSOC)){
                    $docData[] = $row;
                }

                return array("cabecera"=>$docData,
                            "detalles"=>$this->detallesDespacho($id));


            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function detallesDespacho($id){
            try {
                $salida="";
                $sql=$this->db->connect()->prepare("SELECT
                                                    alm_recepdet.niddeta,
                                                    alm_recepdet.id_regalm,
                                                    alm_recepdet.ncodalm1,
                                                    alm_recepdet.id_cprod,
                                                    FORMAT(alm_recepdet.ncantidad, 2) AS ncantidad,
                                                    alm_recepdet.niddetaPed,
                                                    alm_recepdet.niddetaOrd,
                                                    alm_recepdet.nestadoreg,
                                                    cm_producto.ccodprod,
                                                    alm_recepdet.fvence,
                                                    UPPER(
                                                            CONCAT_WS(
                                                                ' ',
                                                                cm_producto.cdesprod,
                                                                tb_pedidodet.observaciones,
                                                                tb_pedidodet.docEspec
                                                            )
                                                        ) AS cdesprod,
                                                    tb_unimed.cabrevia,
                                                    FORMAT(lg_ordendet.ncanti, 2) AS cantidad,
                                                    alm_recepserie.cdesserie
                                                    FROM
                                                    alm_recepdet
                                                    INNER JOIN tb_pedidodet ON alm_recepdet.niddetaPed = tb_pedidodet.iditem
                                                    INNER JOIN cm_producto ON alm_recepdet.id_cprod = cm_producto.id_cprod
                                                    INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed
                                                    INNER JOIN lg_ordendet ON alm_recepdet.niddetaOrd = lg_ordendet.nitemord
                                                    LEFT JOIN alm_recepserie ON alm_recepdet.niddeta = alm_recepserie.ncodserie
                                                    WHERE
                                                        alm_recepdet.id_regalm = :id");
                $sql->execute(["id"=>$id]);

                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    $item = 1;
                    while ($rs = $sql->fetch()){

                        $estados = $this->listarSelect(13,$rs['nestadoreg']);

                        $fecha = $rs['fvence'] == null ? "" : date("d-m-Y", strtotime($rs['fvence']));

                        $salida.='<tr data-itemorden="'.$rs['niddetaOrd'].'" 
                                        data-itempedido="'.$rs['niddetaPed'].'" 
                                        data-itemingreso="'.$rs['niddeta'].'"
                                        data-idproducto ="'.$rs['id_cprod'].'">
                                        <td class="textoCentro"><input type="checkbox"></td>
                                        <td class="textoCentro">'.str_pad($item,3,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                        <td class="pl20px">'.$rs['cdesprod'].'</td>
                                        <td class="textoCentro">'.$rs['cabrevia'].'</td>
                                        <td><input type="number" step="any" value="'.$rs['cantidad'].'" onchange="(function(el){el.value=parseFloat(el.value).toFixed(2);})(this)"></td>
                                        <td class="pl20px"><input type="text"></td>
                                        <td class="textoCentro">'.$rs['cdesserie'].'</td>
                                        <td class="textoCentro">'.$fecha.'</td>
                                        <td><input type="text"></td>
                                        <td><select name="estado" disabled>'. $estados .'</select></td>
                                    </tr>';
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }
    }
?>