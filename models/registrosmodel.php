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
    }
?>