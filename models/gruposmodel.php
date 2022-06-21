<?php
    class GruposModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarGrupos(){
            try {
                $salida = "";

                $sql = $this->db->connect()->query("SELECT ccodcata,cdescrip,ncodgrupo 
                                            FROM tb_grupo 
                                            WHERE nflgactivo = 1
                                            ORDER BY ccodcata");
                $sql->execute();
                $rowCount = $sql->rowcount();

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()){
                        $salida .='<tr data-id ="'.$rs['ncodgrupo'].'" class="pointer">
                                        <td class="textoCentro">'.$rs['ccodcata'].'</td>
                                        <td class="pl20px">'.strtoupper($rs['cdescrip']).'</td>
                                        <td class="textoCentro"><a href="'.$rs['ncodgrupo'].'"><i class="fas fa-trash-alt"></i></a></td>
                                    </tr>';
                    }
                }else{
                    $salida = '<tr>
                            <td colspan="3" class="textoCentro">No hay registros</td>
                        </tr>';
                }

                return $salida;

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function insertar($datos){
            try {
                $respuesta = false;
                $mensaje = "Error al grabar el registro";
                $clase = "mensaje_error";

                if($this->existeItem($datos['codigo'])){
                    $respuesta = false;
                    $mensaje = "Código de grupo duplicado";
                    $clase = "mensaje_error";
                }else{

                    $sql = $this->db->connect()->prepare("INSERT INTO tb_grupo 
                                                            SET ccodcata=:cod,cdescrip=:descrip,nnivclas=:niv,
                                                                ntipclase=:tipo");
                    $sql->execute(["cod"=>strtoupper($datos['codigo']),
                                    "descrip"=>strtoupper($datos['descripcion']),
                                    "niv"=>1,
                                    "tipo"=>$datos['tipoClase']]);
                    $rowCount = $sql->rowCount();

                    if ($rowCount > 0) {
                        $respuesta = false;
                        $mensaje = "Grupo creado";
                        $clase = "mensaje_correcto";
                    }
                }
                
                $salida = array("respuesta"=>$respuesta,
                                 "mensaje"=>$mensaje,
                                 "clase"=>$clase,
                                "items"=>$this->listarGrupos());
                return $salida;

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            } 
        }

        public function modificar($datos){
            try {
                $respuesta = false;
                $mensaje = "Error al actualizar el registro";
                $clase = "mensaje_error";

                $sql = $this->db->connect()->prepare("UPDATE tb_grupo 
                                                        SET cdescrip=:descrip
                                                        WHERE ncodgrupo=:id");
                $sql->execute(["descrip"=>$datos['descripcion'],
                                "id"=>$datos['codgrupo']]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    $respuesta = false;
                    $mensaje = "Grupo modificado";
                    $clase = "mensaje_correcto";
                }
                
                $salida = array("respuesta"=>$respuesta, "mensaje"=>$mensaje,"clase"=>$clase);
                return $salida;

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            } 
        }

        public function eliminar($id){
            try {
                $sql = $this->db->connect()->prepare("UPDATE tb_grupo SET nflgactivo = 0 WHERE ncodgrupo=:id");
                $sql->execute([$id]);

                return $this->listarGrupos();
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            } 
        }

        public function consultarId($id){
            try {
                $sql = $this->db->connect()->prepare("SELECT ncodgrupo,ccodcata,cdescrip,ntipclase
                                                        FROM tb_grupo
                                                        WHERE ncodgrupo =:id");
                $sql->execute(["id"=>$id]);
                $rowCount = $sql->rowCount();
                
                if ($rowCount > 0) {
                    $docData = array();
                    while($row=$sql->fetch(PDO::FETCH_ASSOC)){
                        $docData[] = $row;
                    } 
                }

                return array("grupo"=>$docData);

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function existeItem($codigo){
            try {
                $sql = $this->db->connect()->prepare("SELECT ncodgrupo FROM tb_clase WHERE ccodcata =:codigo");
                $sql->execute(["codigo"=>$codigo]);
                $rowcount = $sql->rowcount();

                if ($rowcount > 0) {
                    return true;
                }else {
                    return false;
                }
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }
    }
?>