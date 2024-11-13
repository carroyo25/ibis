<?php
    class AutorizaTrasladoModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function aprobarTraslado($id,$user){
            try {
                $respuesta = false;
                $mensaje = "No se pudo autorizar";

                $sql = $this->db->connect()->prepare("UPDATE alm_autorizacab 
                                                    SET alm_autorizacab.uautoriza = :user,
                                                        alm_autorizacab.nflgautoriza = :flag
                                                    WHERE 
                                                        alm_autorizacab.idreg = :indice");

                $sql->execute(["user"=>$user,"indice"=>$id,"flag"=>1]);

                if ($sql->rowCount() > 0) {
                    $respuesta = true;
                    $mensaje = "Traslado autorizado";
                }

                return array("respuesta"=>$respuesta, "mensaje"=>$mensaje);

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }  
        }

        public function listarTrasladosAprobados($tipo){
            try {
                $salida = "";

                $sql = $this->db->connect()->prepare("SELECT
                                                        ibis.alm_autorizacab.idreg,
                                                        ibis.tb_costusu.ncodproy,
                                                        ibis.alm_autorizacab.fregsys,
                                                        ibis.alm_autorizacab.ntipo,
                                                        ibis.alm_autorizacab.ctransferencia,
                                                        UPPER(ibis.tb_proyectos.cdesproy) AS cdesproy,
                                                        UPPER(origen.cdesalm) AS origen,
                                                        UPPER(destino.cdesalm) AS destino,
                                                        UPPER( tb_area.cdesarea ) AS area,
                                                        ibis.alm_autorizacab.nestado,
                                                        tipos_autorizacion.cdescripcion,
                                                        estados.cdescripcion AS estado,
                                                        usuario.cnombres AS asigna 
                                                    FROM
                                                        ibis.tb_costusu
                                                        INNER JOIN ibis.alm_autorizacab ON tb_costusu.ncodproy = alm_autorizacab.ncostos
                                                        INNER JOIN ibis.tb_proyectos ON alm_autorizacab.ncostos = tb_proyectos.nidreg
                                                        INNER JOIN ibis.tb_almacen AS origen ON alm_autorizacab.norigen = origen.ncodalm
                                                        INNER JOIN ibis.tb_almacen AS destino ON alm_autorizacab.ndestino = destino.ncodalm
                                                        INNER JOIN ibis.tb_area ON alm_autorizacab.narea = tb_area.ncodarea
                                                        INNER JOIN ibis.tb_parametros AS estados ON ibis.alm_autorizacab.nestado = estados.nidreg 
                                                        INNER JOIN ibis.tb_parametros AS tipos_autorizacion ON ibis.alm_autorizacab.ctransferencia = tipos_autorizacion.nidreg 
                                                        INNER JOIN ibis.tb_user AS usuario ON ibis.alm_autorizacab.csolicita = usuario.iduser
                                                    WHERE
                                                        tb_costusu.id_cuser =:user 
                                                        AND tb_costusu.nflgactivo = 1
                                                        AND alm_autorizacab.nflgactivo = 1
                                                        AND alm_autorizacab.nflgautoriza = 0
                                                        AND alm_autorizacab.ntipo LIKE :tipo
                                                    ORDER BY ibis.alm_autorizacab.fregsys DESC");

                $sql->execute(["user"=>$_SESSION['iduser'],"tipo"=>$tipo]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()) {
                        $salida .='<tr class="pointer" data-indice="'.$rs['idreg'].'" 
                                                        data-transferencia="'.$rs['ctransferencia'].'"
                                                        data-tipo ="'.$rs['ntipo'].'">
                                        <td class="textoCentro">'.str_pad($rs['idreg'],4,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.date("d/m/Y", strtotime($rs['fregsys'])).'</td>
                                        <td class="pl20px">'.$rs['cdescripcion'].'</td>
                                        <td class="pl20px">'.$rs['cdesproy'].'</td>
                                        <td class="pl20px">'.$rs['origen'].'</td>
                                        <td class="pl20px">'.$rs['destino'].'</td>
                                        <td class="pl20px">'.$rs['area'].'</td>
                                        <td class="pl20px">'.$rs['asigna'].'</td>
                                        <td class="textoCentro '.strtolower($rs['estado']).'">'.$rs['estado'].'</td>
                                        <td class="textoCentro"><a href="'.$rs['idreg'].'" data-accion="status"><i class="fas fa-chart-line"></i></a></td>
                                        <td class="textoCentro"><a href="'.$rs['idreg'].'" data-accion="delete"><i class="fa fa-trash-alt"></i></a></td>
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