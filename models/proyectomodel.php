<?php
    class ProyectoModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarProyectos(){
            try {
                $salida = "";
                $sql = $this->db->connect()->query("SELECT 
                                                    nidreg,ccodproy,cdesproy,cubica,cabrevia 
                                                    FROM tb_proyectos 
                                                    WHERE nflgactivo=1
                                                    ORDER BY ccodproy ASC");
                $sql->execute();
                $rc = $sql->rowcount();
                $item = 1;

                if ($rc > 0){
                    while( $rs = $sql->fetch()) {
                        $salida .='<tr data-id="'.$rs['nidreg'].'" class="pointer">
                                        <td class="textoCentro">'.str_pad($item,3,0,STR_PAD_LEFT).'</td>
                                        <td class="pl20px">'.strtoupper($rs['ccodproy']).'</td>
                                        <td class="pl20px">'.strtoupper($rs['cdesproy']).'</td>
                                        <td class="textoCentro"><a href="'.$rs['nidreg'].'"><i class="fas fa-trash-alt"></i></a></td>
                                    </tr>';
                        $item++;
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function crearProyecto($datos,$costos){
            $salida = false;
            $respuesta = false;
            $mensaje = "Error en el registro";
            $clase = "mensaje_error";
            $id = 0;
            $verifica = array_key_exists('chkVerAlm',$datos)  ? 1 : 0;
            
            try {
                $sql = $this->db->connect()->prepare("INSERT INTO tb_proyectos SET ccodproy=:cod,
                                                                                    cdesproy=:nom,
                                                                                    cubica=:ubig,
                                                                                    ncosto=:costo,
                                                                                    nflgactivo=:est,
                                                                                    veralm=:verifica");
                $sql->execute(["cod"=>$datos['codigo'],
                                "nom"=>$datos['descripcion'],
                                "ubig"=>$datos['ubigeo'],
                                "costo"=>$datos['costo'],
                                "verifica"=>$verifica,
                                "est"=>1]);
                $rc = $sql->rowcount();

                if ($rc > 0) {
                    $respuesta = true;
                    $mensaje = "Se registro correctamente";
                    $clase = "mensaje_correcto";
                    $id = $this->obtenerIdRegistro($datos['descripcion']);
                    
                    if ($id !=0)
                        $this->grabarItems($costos,$id);
                }

                $salida = array("respuesta"=>$respuesta,
                                "mensaje"=>$mensaje,
                                "clase"=>$clase,
                                "id"=> $id);
                return $salida;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function modificarProyecto($datos,$costos){
            $salida = false;
            $respuesta = true;
            $mensaje = "Se actualizo correctamente";
            $clase = "mensaje_correcto";

            $verifica = array_key_exists('chkVerAlm',$datos)  ? 1 : 0;
            
            try {
                $sql = $this->db->connect()->prepare("UPDATE tb_proyectos 
                                                        SET ccodproy=:cod,
                                                            cdesproy=:nom,
                                                            cubica=:ubig,
                                                            ncosto=:costo,
                                                            veralm=:verifica
                                                        WHERE nidreg=:id");
                $sql->execute(["cod"=>$datos['codigo'],
                                "nom"=>$datos['descripcion'],
                                "ubig"=>$datos['ubigeo'],
                                "costo"=>$datos['costo'],
                                "id"=>$datos['codproy'],
                                "verifica"=>$verifica]);
                $rc = $sql->rowcount();

                $this->grabarItems($costos,$datos['codproy']);

                
                $salida = array("respuesta"=>$respuesta,
                                    "mensaje"=>$mensaje,
                                    "clase"=>$clase);
                return $salida;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function consultarProyectoId($id) {
            try {
                $sql=$this->db->connect()->prepare("SELECT
                                                    tb_proyectos.nidreg,
                                                    tb_proyectos.ccodproy,
                                                    tb_proyectos.cdesproy,
                                                    tb_proyectos.cubica,
                                                    tb_proyectos.ncosto,
                                                    tb_proyectos.veralm,
                                                    distritos.cdubigeo AS distrito,
                                                    provincias.cdubigeo AS provincia,
                                                    dptos.cdubigeo AS departamento 
                                                FROM
                                                    tb_proyectos
                                                    LEFT JOIN tb_ubigeo AS distritos ON tb_proyectos.cubica = distritos.ccubigeo
                                                    LEFT JOIN tb_ubigeo AS provincias ON SUBSTR( tb_proyectos.cubica, 1, 4 ) = provincias.ccubigeo
                                                    LEFT JOIN tb_ubigeo AS dptos ON SUBSTR( tb_proyectos.cubica, 1, 2 ) = dptos.ccubigeo 
                                                WHERE
                                                    tb_proyectos.nidreg =:id");
                $sql->execute(["id"=>$id]);
                $rowCount = $sql->rowCount();
                
                if ($rowCount > 0) {
                    $docData = array();
                    while($row=$sql->fetch(PDO::FETCH_ASSOC)){
                        $docData[] = $row;
                    }

                    $partidas = $this->consultarDetalles($id);
                }

                return array("proyecto"=>$docData,"partidas"=>$partidas);
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function borrarProyecto($id){
            try {
                $sql=$this->db->connect()->prepare("UPDATE tb_proyectos SET nflgactivo = 0 WHERE nidreg=:id");
                $sql->execute(["id"=>$id]);
                $rc = $sql->rowcount();

                if ($rc > 0) {
                    $salida = $this->listarProyectos();
                }

                return $salida;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function borrarCostos($id){
            try {
                $sql = $this->db->connect()->prepare("UPDATE tb_ccostos SET nflgactivo = 0 WHERE ncodcos =:id");
                $sql->execute(["id" => $id]);
                if ($sql->rowcount() > 0){
                    return true;
                }
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function llamarPartidasGenerales(){
            try {
                $docData = [];
                $sql = $this->db->connect()->query("SELECT tb_partidasgeneral.idreg,
                                                            tb_partidasgeneral.ccodigo,
                                                            tb_partidasgeneral.cdescripcion,
                                                            tb_partidasgeneral.nflgactivo
                                                    FROM tb_partidasgeneral
                                                    WHERE tb_partidasgeneral.nflgactivo = 1");
                $sql->execute();

                while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                    $docData[] = $row;
                }
                
                return $docData;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function obtenerIdRegistro($nombre){
            try {
                $sql = $this->db->connect()->prepare("SELECT nidreg FROM tb_proyectos WHERE cdesproy=:proyecto");
                $sql->execute(["proyecto"=>$nombre]);
                $result = $sql->fetchAll();

                return $result[0]['nidreg'];

            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function grabarItems($items,$id){
            $datos = json_decode($items);

            for ($i=0; $i < count($datos) ; $i++) { 
                try {
                    $query= $this->db->connect()->prepare("INSERT INTO tb_partidas 
                                                            SET idcc=:costo,
                                                                ccodigo=:codigo,
                                                                cdescripcion=:dsc,
                                                                nflgactivo=:est");
                    $query->execute(["costo" =>$id,
                                     "dsc" =>$datos[$i]->descripcion,
                                     "est" =>1,
                                     "codigo"=>$datos[$i]->codigo]);
                } catch (PDOException $th) {
                    echo $th->getMessage();
                    return false;
                } 
            }
        }

        private function consultarDetalles($id){
            $salida = "";
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                        tb_partidas.idreg,
                                                        tb_partidas.idcc,
                                                        tb_partidas.ccodigo,
                                                        UPPER(tb_partidas.cdescripcion) AS cdescripcion
                                                    FROM
                                                        tb_partidas
                                                    WHERE
                                                        tb_partidas.idcc = :id
                                                    AND tb_partidas.nflgactivo = 1");
                $sql->execute(["id"=>$id]);
                $rowCount = $sql->rowCount();
                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()) {

                        $salida.='<tr data-estado="1">
                                    <td class="pl5px">'.$rs['ccodigo'].'</td>
                                    <td class="pl5px">'.$rs['cdescripcion'].'</td>
                                    <td class="textoCentro"><a href="'.$rs['idreg'].'"><i class="far fa-trash-alt"></i></a></td>
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