<?php
    class EstudioModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarPedidosCotizados(){
            try {
                $salida = "";
                $sql = $this->db->connect()->prepare("SELECT
                                                        ibis.lg_cotizadet.id_regmov,
                                                        ibis.lg_cotizadet.niddet,
                                                        ibis.tb_pedidocab.idarea,
                                                        ibis.tb_pedidocab.idsolicita,
                                                        ibis.tb_pedidocab.emision,
                                                        ibis.tb_pedidocab.vence,
                                                        ibis.tb_pedidocab.estadodoc,
                                                        ibis.tb_pedidocab.nrodoc,
                                                        UPPER( ibis.tb_pedidocab.concepto ) AS concepto,
                                                        ibis.tb_pedidocab.idcostos,
                                                        UPPER(
                                                        CONCAT_WS( ' ', ibis.tb_proyectos.ccodproy, ibis.tb_proyectos.cdesproy )) AS costos,
                                                        ibis.tb_costusu.nflgactivo,
                                                        CONCAT_WS( ' ', rrhh.tabla_aquarius.apellidos, rrhh.tabla_aquarius.nombres ) AS nombres,
                                                        estados.cdescripcion AS estado,
                                                        estados.cabrevia,
                                                        atenciones.cdescripcion AS atencion,
                                                        ibis.tb_pedidocab.idreg,
                                                        ibis.tb_costusu.ncodproy 
                                                    FROM
                                                        ibis.lg_cotizadet
                                                        INNER JOIN ibis.tb_pedidocab ON lg_cotizadet.id_regmov = tb_pedidocab.idreg
                                                        INNER JOIN ibis.tb_costusu ON tb_pedidocab.idcostos = tb_costusu.ncodproy
                                                        INNER JOIN ibis.tb_proyectos ON tb_pedidocab.idcostos = tb_proyectos.nidreg
                                                        INNER JOIN rrhh.tabla_aquarius ON ibis.tb_pedidocab.idsolicita = rrhh.tabla_aquarius.internal
                                                        INNER JOIN ibis.tb_parametros AS estados ON ibis.tb_pedidocab.estadodoc = ibis.estados.nidreg
                                                        INNER JOIN ibis.tb_parametros AS atenciones ON ibis.tb_pedidocab.nivelAten = atenciones.nidreg 
                                                    WHERE
                                                        tb_costusu.nflgactivo = 1 
                                                        AND ibis.tb_costusu.id_cuser = :user  
                                                        AND tb_pedidocab.estadodoc BETWEEN 56 
                                                        AND 56 
                                                    GROUP BY
                                                        ibis.lg_cotizadet.niddet");
                $sql->execute(["user"=>$_SESSION['iduser']]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()) {
                        $salida .='<tr class="pointer" data-pedido="'.$rs['idreg'].'" data-item="'.$rs['niddet'].'">
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

        public function verProformas($id){
            try {
                $salida = "";
                $sql = $this->db->connect()->prepare("SELECT crefdocprof,cnameprof FROM lg_proformacab WHERE id_regmov=:id");
                $sql->execute(['id'=>$id]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()) {
                        $salida .= '<li><a href="'.$rs['crefdocprof'].'" data-archivo="'.$rs['crefdocprof'].'"><i class="far fa-file"></i><p>'.$rs['cnameprof'].'</p></a></li>';
                    }
                }
                
                return $salida;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function actualizarProformas($id,$datos){
            try {
                $pedidos = $this->actDetallePedido($datos);
                $this->actualizaCabeceraPedido($id);

                return $pedidos;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function actDetallePedido($datos){
            try {
                $data = json_decode($datos);
                $nreg = count($data);
                
                for ($i=0; $i < $nreg; $i++) { 
                    try {
                        $sql = $this->db->connect()->prepare("UPDATE tb_pedidodet 
                                                                SET precio =:prec,
                                                                    total=:tot,
                                                                    entidad=:ent,
                                                                    nflgAdjudicado=:adj,
                                                                    idproforma=:prof,
                                                                    estadoItem=:est,
                                                                    docEspec=:doc
                                                                WHERE iditem=:id");
                        $sql->execute(["prec"=>$data[$i]->unitario,
                                        "tot"=>$data[$i]->total,
                                        "ent"=>$data[$i]->entidad,
                                        "adj"=>1,
                                        "prof"=>$data[$i]->detprof,
                                        "doc"=>$data[$i]->espec,
                                        "id"=>$data[$i]->detpedido,
                                        "est"=>57]);
                        
                        return $i;

                    } catch (PDOException $th) {
                        echo "Error: ".$th->getMessage();
                        return false;
                    }
                }
                

            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function actualizaCabeceraPedido($id){
            try {
                $sql = $this->db->connect()->prepare("UPDATE tb_pedidocab 
                                                        SET estadodoc=:est 
                                                        WHERE idreg=:id");
                $sql->execute(["est"=>57,
                                "id"=>$id]);
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }
    }
?>