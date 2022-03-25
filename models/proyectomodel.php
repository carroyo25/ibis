<?php
    class ProyectoModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarProyectos(){
            try {
                $salida = "";
                $sql = $this->db->connect()->query("SELECT nidreg,ccodproy,cdesproy,cubica,cabrevia FROM tb_proyectos WHERE nflgactivo=1");
                $sql->execute();
                $rc = $sql->rowcount();
                $item = 1;

                if ($rc > 0){
                    while( $rs = $sql->fetch()) {
                        $salida .='<tr data-id="'.$rs['nidreg'].'">
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
            
            try {
                $sql = $this->db->connect()->prepare("INSERT INTO tb_proyectos SET ccodproy=:cod,
                                                                                    cdesproy=:nom,
                                                                                    cubica=:ubig,
                                                                                    cabrevia=:abre,
                                                                                    nflgactivo=:est");
                $sql->execute(["cod"=>$datos['codigo'],
                                "nom"=>$datos['descripcion'],
                                "ubig"=>$datos['ubigeo'],
                                "abre"=>$datos['abreviatura'],
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
            
            try {
                $sql = $this->db->connect()->prepare("UPDATE tb_proyectos 
                                                        SET ccodproy=:cod,
                                                            cdesproy=:nom,
                                                            cubica=:ubig,
                                                            cabrevia=:abre
                                                        WHERE nidreg=:id");
                $sql->execute(["cod"=>$datos['codigo'],
                                "nom"=>$datos['descripcion'],
                                "ubig"=>$datos['ubigeo'],
                                "abre"=>$datos['abreviatura'],
                                "id"=>$datos['codproy']]);
                $rc = $sql->rowcount();

                $this->grabarItems($costos,$datos['codproy']);

                /*if ($rc > 0) {
                    $respuesta = true;
                    $mensaje = "Se actualizo correctamente";
                    $clase = "mensaje_correcto";
                }*/

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
                                                    tb_proyectos.cabrevia,
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

                    $costos = $this->consultarDetalles($id);
                }

                return array("proyecto"=>$docData,
                            "costos"=>$costos);
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
                    $query= $this->db->connect()->prepare("INSERT INTO tb_ccostos SET ccodcos=:cod,cdescos=:dsc,nflgactivo=:est,ncodpry=:proy,nflgVeryAlm=:alm");
                    $query->execute(["cod" =>$datos[$i]->codigo,
                                 "dsc" =>$datos[$i]->descripcion,
                                 "est" =>1,
                                 "proy"=>$id,
                                 "alm" =>$datos[$i]->almacen]);
                } catch (PDOException $th) {
                    echo $th->getMessage();
                    return false;
                } 
            }

            
        }

        private function consultarDetalles($id){
            $salida = "";
            try {
                $sql = $this->db->connect()->prepare("SELECT tb_ccostos.ccodcos, 
                                                             tb_ccostos.cdescos, 
                                                             tb_ccostos.nflgactivo, 
                                                             tb_ccostos.ncodcos, 
                                                             tb_ccostos.ncodpry, 
                                                             tb_ccostos.nflgVeryAlm
                                                      FROM tb_ccostos
                                                      WHERE tb_ccostos.ncodpry =:id 
                                                      AND nflgactivo = 1");
                $sql->execute(["id"=>$id]);
                $rowCount = $sql->rowCount();
                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()) {

                        $sw = $rs['nflgVeryAlm'] == 1 ? "checked":"";

                        $salida.='<tr data-estado="1">
                                    <td class="pl5px">'.$rs['ccodcos'].'</td>
                                    <td class="pl5px">'.$rs['cdescos'].'</td>
                                    <td class="textoCentro"><input type="checkbox" '.$sw.'></td>
                                    <td class="textoCentro"><a href="'.$rs['ncodcos'].'"><i class="far fa-trash-alt"></i></a></td>
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