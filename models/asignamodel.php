<?php
    class AsignaModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarPedidosAprobados(){
            try {
                $salida = "";
                $sql = $this->db->connect()->prepare("SELECT
                                                        ibis.tb_costusu.id_cuser,
                                                        ibis.tb_costusu.ncodproy,
                                                        ibis.tb_pedidocab.nrodoc,
                                                        UPPER( ibis.tb_pedidocab.concepto ) AS concepto,
                                                        ibis.tb_pedidocab.idreg,
                                                        ibis.tb_pedidocab.estadodoc,
                                                        ibis.tb_pedidocab.emision,
                                                        ibis.tb_pedidocab.vence,
                                                        UPPER(
                                                        CONCAT_WS( ' ', ibis.tb_proyectos.ccodproy, ibis.tb_proyectos.cdesproy )) AS costos,
                                                        ibis.tb_pedidocab.nivelAten,
                                                        CONCAT_WS(' ',rrhh.tabla_aquarius.apellidos,rrhh.tabla_aquarius.nombres) AS nombres,
                                                        estados.cdescripcion AS estado,
                                                        atencion.cdescripcion AS atencion,
                                                        estados.cabrevia 
                                                    FROM
                                                        ibis.tb_costusu
                                                        INNER JOIN ibis.tb_pedidocab ON tb_costusu.ncodproy = tb_pedidocab.idcostos
                                                        INNER JOIN ibis.tb_proyectos ON tb_costusu.ncodproy = tb_proyectos.nidreg
                                                        INNER JOIN rrhh.tabla_aquarius ON ibis.tb_pedidocab.idsolicita = rrhh.tabla_aquarius.internal
                                                        INNER JOIN ibis.tb_parametros AS estados ON ibis.tb_pedidocab.estadodoc = estados.nidreg
                                                        INNER JOIN ibis.tb_parametros AS atencion ON ibis.tb_pedidocab.nivelAten = atencion.nidreg 
                                                    WHERE
                                                        tb_costusu.id_cuser = :user 
                                                        AND tb_pedidocab.estadodoc = 49
                                                        AND tb_costusu.nflgactivo = 1
                                                        AND tb_pedidocab.nflgactivo = 1");
                //$sql->execute(["user"=>$_SESSION['iduser']]);
                $sql->execute(["user"=>'62daff7e8cd24']);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()) {
                        $salida .='<tr class="pointer" data-indice="'.$rs['idreg'].'">
                                        <td class="textoCentro">'.str_pad($rs['nrodoc'],4,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.date("d/m/Y", strtotime($rs['emision'])).'</td>
                                        <td class="textoCentro">'.date("d/m/Y", strtotime($rs['vence'])).'</td>
                                        <td class="pl20px">'.utf8_decode($rs['concepto']).'</td>
                                        <td class="pl20px">'.utf8_decode($rs['costos']).'</td>
                                        <td class="pl20px">'.$rs['nombres'].'</td>
                                        <td class="textoCentro '.$rs['cabrevia'].'">'.$rs['estado'].'</td>
                                        <td class="textoCentro '.strtolower($rs['atencion']).'">'.$rs['atencion'].'</td>
                                        <td class="textoCentro"><a href="'.$rs['idreg'].'"><i class="fa fa-trash-alt"></i></a></td>
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