<?php
    class AsignaModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarPedidosAprobados(){
            try {
                $salida = "";
                $sql = $this->db->connect()->query("SELECT
                                                ibis.tb_pedidocab.nrodoc,
                                                UPPER( ibis.tb_pedidocab.concepto ) AS concepto,
                                                ibis.tb_pedidocab.idreg,
                                                ibis.tb_pedidocab.estadodoc,
                                                ibis.tb_pedidocab.emision,
                                                ibis.tb_pedidocab.vence,
                                                UPPER(
                                                CONCAT_WS( ' ', ibis.tb_proyectos.ccodproy, ibis.tb_proyectos.cdesproy )) AS costos,
                                                ibis.tb_pedidocab.nivelAten,
                                                CONCAT_WS( ' ', rrhh.tabla_aquarius.apellidos, rrhh.tabla_aquarius.nombres ) AS nombres,
                                                estados.cdescripcion AS estado,
                                                atencion.cdescripcion AS atencion,
                                                estados.cabrevia,
                                                ibis.tb_pedidocab.idcostos,
                                                ibis.tb_proyectos.ccodproy,
                                                ibis.tb_proyectos.cdesproy 
                                            FROM
                                                ibis.tb_pedidocab
                                                INNER JOIN rrhh.tabla_aquarius ON ibis.tb_pedidocab.idsolicita = rrhh.tabla_aquarius.internal
                                                INNER JOIN ibis.tb_parametros AS estados ON ibis.tb_pedidocab.estadodoc = estados.nidreg
                                                INNER JOIN ibis.tb_parametros AS atencion ON ibis.tb_pedidocab.nivelAten = atencion.nidreg
                                                INNER JOIN ibis.tb_proyectos ON ibis.tb_pedidocab.idcostos = ibis.tb_proyectos.nidreg 
                                            WHERE
                                                ibis.tb_pedidocab.estadodoc = 54
                                                AND ibis.tb_pedidocab.nflgactivo = 1
                                                AND ISNULL(ibis.tb_pedidocab.asigna)");
                $sql->execute();
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()) {
                        $salida .='<tr class="pointer" data-indice="'.$rs['idreg'].'">
                                        <td class="textoCentro">'.str_pad($rs['idreg'],4,0,STR_PAD_LEFT).'</td>
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

        public function asignarOperador($pedido,$detalles,$asignado){
            try {
                $sql = $this->db->connect()->prepare("UPDATE tb_pedidocab 
                                                        SET tb_pedidocab.asigna=:asignado
                                                        WHERE tb_pedidocab.idreg =:pedido");
                $sql->execute(["asignado"=>$asignado,
                                "pedido"=>$pedido]);

                $rowCount = $sql->rowCount();
                if ($rowCount > 0){
                    $this->asignarDetalles($detalles,$asignado);
                }
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function asignarDetalles($detalles,$asignado){
            $datos = json_decode($detalles);
            $nreg = count($datos);

            for ($i=0; $i < $nreg; $i++) {
                try {
                    $sql = $this->db->connect()->prepare("UPDATE tb_pedidodet 
                                                        SET tb_pedidodet.idasigna=:asignado
                                                        WHERE tb_pedidodet.iditem =:item");
                $sql->execute(["asignado"=>$asignado,
                                "item"=>$datos[$i]->itempedido]);
                } catch (PDOException $th) {
                    echo $th->getMessage();
                    return false;
                }
            }

        }
    }
?>