<?php
    class PedidoSegModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarPedidosUsuario(){
            try {
                $salida = "";
                $sql = $this->db->connect()->prepare("SELECT
                                                    ibis.tb_pedidocab.idreg,
                                                    ibis.tb_pedidocab.idcostos,
                                                    ibis.tb_pedidocab.idarea,
                                                    ibis.tb_pedidocab.emision,
                                                    ibis.tb_pedidocab.vence,
                                                    ibis.tb_pedidocab.estadodoc,
                                                    ibis.tb_pedidocab.nrodoc,
                                                    UPPER(ibis.tb_pedidocab.concepto) AS concepto,
                                                    CONCAT(rrhh.tabla_aquarius.nombres,' ',rrhh.tabla_aquarius.apellidos) AS nombres,
                                                    UPPER(CONCAT(ibis.tb_proyectos.ccodproy,' ',ibis.tb_proyectos.cdesproy)) AS costos,
                                                    ibis.tb_pedidocab.nivelAten,
                                                    atenciones.cdescripcion AS atencion,
                                                    estados.cdescripcion AS estado,
                                                    estados.cabrevia 
                                                FROM
                                                    ibis.tb_pedidocab
                                                    INNER JOIN rrhh.tabla_aquarius ON ibis.tb_pedidocab.idsolicita = rrhh.tabla_aquarius.internal
                                                    INNER JOIN ibis.tb_proyectos ON ibis.tb_pedidocab.idcostos = ibis.tb_proyectos.nidreg
                                                    INNER JOIN ibis.tb_parametros AS atenciones ON ibis.tb_pedidocab.nivelAten = atenciones.nidreg
                                                    INNER JOIN ibis.tb_parametros AS estados ON ibis.tb_pedidocab.estadodoc = estados.nidreg 
                                                WHERE
                                                    ibis.tb_pedidocab.usuario = :user 
                                                    AND ibis.tb_pedidocab.estadodoc BETWEEN 49 AND 63");
                $sql->execute(["user"=>$_SESSION['iduser']]);
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

        public function consultarInfo($id){
            try {
                $sql=$this->db->connect()->prepare("SELECT
                                    tb_pedidocab.idcostos,
                                    DATE_FORMAT( tb_pedidocab.emision, '%d/%m/%Y' ) AS emision,
                                    DATE_FORMAT( tb_pedidocab.faprueba, '%d/%m/%Y' ) AS aprobacion,
                                    UPPER(
                                    CONCAT_WS( ' ', tb_proyectos.ccodproy, tb_proyectos.cdesproy )) AS proyecto,
                                    elbora.cnombres AS elaborado,
                                    LPAD( tb_pedidocab.nrodoc, 6, 0 ) AS pedido,
                                    aprueba.cnombres AS aprobador,
                                    tb_parametros.cdescripcion,
                                    FORMAT( tb_parametros.cobservacion, 0 ) AS avance,
                                    tb_pedidocab.idreg 
                                FROM
                                    tb_pedidocab
                                    INNER JOIN tb_proyectos ON tb_pedidocab.idcostos = tb_proyectos.nidreg
                                    INNER JOIN tb_user AS elbora ON tb_pedidocab.usuario = elbora.iduser
                                    LEFT JOIN tb_user AS aprueba ON tb_pedidocab.aprueba = aprueba.iduser
                                    INNER JOIN tb_parametros ON tb_pedidocab.estadodoc = tb_parametros.nidreg 
                                WHERE
                                    tb_pedidocab.idreg =:id");
                $sql->execute(["id"=>$id]);
                $result = $sql->fetchAll();

                $json_result = array("pedido"=>$result[0]['pedido'],
                                    "emision"=>$result[0]['emision'],
                                    "costos"=>$result[0]['proyecto'],
                                    "elaborado"=>$result[0]['elaborado'],
                                    "aprobador"=>$result[0]['aprobador'],
                                    "aprobacion"=>$result[0]['aprobacion']);

                return $json_result;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }
    }
?>