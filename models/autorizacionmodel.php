<?php
    class AutorizacionModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarTraslados(){
            try {
                $salida = "";

                $sql = $this->db->connect()->prepare("SELECT
                                                        ibis.alm_autorizacab.idreg,
                                                        ibis.tb_costusu.ncodproy,
                                                        ibis.alm_autorizacab.fregsys,
                                                        ibis.tb_proyectos.cdesproy,
                                                        origen.cdesalm AS origen,
                                                        destino.cdesalm AS destino,
                                                        UPPER( tb_area.cdesarea ) AS area,
                                                        CONCAT_WS(' ',rrhh.tabla_aquarius.apellidos,rrhh.tabla_aquarius.nombres) AS asigna,
                                                        tipos_autorizacion.cdescripcion 
                                                    FROM
                                                        ibis.tb_costusu
                                                        INNER JOIN ibis.alm_autorizacab ON tb_costusu.ncodproy = alm_autorizacab.ncostos
                                                        INNER JOIN ibis.tb_proyectos ON alm_autorizacab.ncostos = tb_proyectos.nidreg
                                                        INNER JOIN ibis.tb_almacen AS origen ON alm_autorizacab.norigen = origen.ncodalm
                                                        INNER JOIN ibis.tb_almacen AS destino ON alm_autorizacab.ndestino = destino.ncodalm
                                                        INNER JOIN ibis.tb_area ON alm_autorizacab.narea = tb_area.ncodarea
                                                        INNER JOIN rrhh.tabla_aquarius ON ibis.alm_autorizacab.csolicita = rrhh.tabla_aquarius.internal
                                                        INNER JOIN ibis.tb_parametros AS tipos_autorizacion ON ibis.alm_autorizacab.ctransferencia = tipos_autorizacion.nidreg 
                                                    WHERE
                                                        tb_costusu.id_cuser =:user 
                                                        AND tb_costusu.nflgactivo = 1
                                                        AND alm_autorizacab.nflgactivo = 1");

                $sql->execute(["user"=>$_SESSION['iduser']]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()) {
                        $salida .='<tr class="pointer" data-indice="'.$rs['idreg'].'">
                                        <td class="textoCentro">'.str_pad($rs['idreg'],4,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.date("d/m/Y", strtotime($rs['fregsys'])).'</td>
                                        <td class="textoCentro">'.$rs['cdescripcion'].'</td>
                                        <td class="pl20px">'.$rs['cdesproy'].'</td>
                                        <td class="pl20px">'.$rs['origen'].'</td>
                                        <td class="pl20px">'.$rs['destino'].'</td>
                                        <td class="pl20px">'.$rs['area'].'</td>
                                        <td class="pl20px">'.$rs['asigna'].'</td>
                                        <td class="textoCentro"><a href="'.$rs['idreg'].'"><i class="fa fa-trash-alt"></i></a></td>
                                    </tr>';
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function insertar($cabecera,$detalles){

            try {
                $sql=$this->db->connect()->prepare("INSERT INTO alm_autorizacab 
                                                    SET alm_autorizacab.femision=:emision,
                                                        alm_autorizacab.ncostos=:costos,
                                                        alm_autorizacab.narea=:area,
                                                        alm_autorizacab.csolicita=:solicita,
                                                        alm_autorizacab.norigen=:origen,
                                                        alm_autorizacab.ndestino=:destino,
                                                        alm_autorizacab.ctransferencia=:tipo,
                                                        alm_autorizacab.observac=:observacion,
                                                        alm_autorizacab.celabora=:elabora");

                $sql->execute(["emision"=>$cabecera['emitido'],
                                "costos"=>$cabecera['codigo_costos'],
                                "area"=>$cabecera['codigo_area'],
                                "solicita"=>$cabecera['codigo_solicitante'],
                                "origen"=>$cabecera['codigo_origen'],
                                "destino"=>$cabecera['codigo_destino'],
                                "tipo"=>$cabecera['codigo_tipo'],
                                "observacion"=>$cabecera['observaciones'],
                                "elabora"=>$cabecera['codigo_usuario']]);
                                
                if ($sql->rowCount() > 0) {
                    $numero = $this->numeroDocumento();
                    $this->grabarDetallesTransferencia($cabecera,$detalles,$numero);
                }
                
                return array("numero"=>$numero);
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }    
        }

        private function numeroDocumento(){
            $sql = $this->db->connect()->query("SELECT COUNT(idreg) AS numero FROM alm_autorizacab");
            $sql->execute();

            $result = $sql->fetchAll();

            return $result[0]['numero'];
        }

        private function grabarDetallesTransferencia($cabecera,$detalles,$numero){
            try {
                $datos = json_decode($detalles);
                $nreg = count($datos);

                for ($i=0;$i<$nreg;$i++){
                    try {
                        $sql = $this->db->connect()->prepare("INSERT INTO alm_autorizadet
                                                            SET alm_autorizadet.idautoriza=:numero,
                                                                alm_autorizadet.idcodprod=:codprod,
                                                                alm_autorizadet.idunidad=:unidad,
                                                                alm_autorizadet.ncantidad=:cantidad,
                                                                alm_autorizadet.cserie=:serie,
                                                                alm_autorizadet.cdestino=:area,
                                                                alm_autorizadet.cobserva=:observa,
                                                                alm_autorizadet.norigen=:origen,
                                                                alm_autorizadet.ndestino=:destino");

                        $sql->execute(["numero"=>$numero,
                                        "codprod"=>$datos[$i]->idprod,
                                        "unidad"=>$datos[$i]->unidad,
                                        "cantidad"=>$datos[$i]->cantidad,
                                        "serie"=>$datos[$i]->serie,
                                        "area"=>$datos[$i]->destino,
                                        "observa"=>$datos[$i]->observac,
                                        "origen"=>$cabecera['codigo_origen'],
                                        "destino"=>$cabecera['codigo_destino']]);
                    } catch (PDOException $th) {
                        echo "Error: ".$th->getMessage();
                        return false;
                    }
                }
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            } 
        }

        public function autorizacionId($id){
            try {
                $docData = [];
                $sql = $this->db->connect()->prepare("SELECT LPAD(alm_autorizacab.idreg,6,0) AS idreg,
                                                             alm_autorizacab.femision,
                                                             alm_autorizacab.ncostos,
                                                             alm_autorizacab.narea,
                                                             alm_autorizacab.csolicita,
                                                             alm_autorizacab.norigen,
                                                             alm_autorizacab.ndestino,
                                                             alm_autorizacab.ctransferencia,
                                                             alm_autorizacab.observac,
                                                             alm_autorizacab.celabora
                                                        FROM alm_autorizacab
                                                        WHERE alm_autorizacab.nflgactivo = 1
                                                            AND alm_autorizacab.idreg = :id");
                $sql->execute(["id"=>$id]);
                $docData = $sql->fetchAll();

                $rowCount = $sql->rowCount();
                
                if ($rowCount) {
                    $respuesta = true;
                    $i = 0;
                    
                    while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                        $docData[] = $row;
                    }
                }

                return array("datos"=>$docData);

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function detallesAutorizacion($id){

        }
    }
?>