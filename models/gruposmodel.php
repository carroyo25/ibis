<?php
    class GruposModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarGrupos(){
            try {
                $salida = "";

                $sql = $this->db->connect()->query("SELECT ccodcata,cdescrip,ncodclase 
                                            FROM tb_clase 
                                            WHERE nflgactivo = 1
                                            ORDER BY cdescrip");
                $sql->execute();
                $rowCount = $sql->rowcount();

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()){
                        $salida .='<tr data-id ="'.$rs['ncodclase'].'" class="pointer">
                                        <td class="textoCentro">'.$rs['ccodcata'].'</td>
                                        <td class="pl20px">'.strtoupper($rs['cdescrip']).'</td>
                                        <td class="textoCentro"><a href="'.$rs['ncodclase'].'"><i class="fas fa-trash-alt"></i></a></td>
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
                    $tipo = $datos['tipoClase'] == NULL ? 1:0;

                    $sql = $this->db->connect()->prepare("INSERT INTO tb_clase 
                                                            SET ccodcata=:cod,cdescrip=:descrip,nnivclas=:niv,
                                                                ntipclase=:tipo");
                    $sql->execute(["cod"=>strtoupper($datos['codigo']),
                                    "descrip"=>strtoupper($datos['descripcion']),
                                    "niv"=>1,
                                    "tipo"=>$tipo]);
                    $rowCount = $sql->rowCount();

                    if ($rowCount > 0) {
                        $respuesta = false;
                        $mensaje = "Grupo creado";
                        $clase = "mensaje_correcto";
                    }
                }
                
                $salida = array("respuesta"=>$respuesta, "mensaje"=>$mensaje,"clase"=>$clase);
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

                $sql = $this->db->connect()->prepare("UPDATE tb_clase 
                                                        SET cdescrip=:descrip,ntipclase=:tipo
                                                        WHERE ncodclase=:id");
                $sql->execute(["descrip"=>$datos['descripcion'],
                                "tipo"=>$datos['ntipclase'],
                                "id"=>$datos['ncodclase']]);
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
                $sql = $this->db->connect()->prepare("UPDATE tb_clase SET nflgactivo = 0 WHERE ncodclase=:id");
                $sql->execute([$id]);

                return $this->listarGrupos();
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            } 
        }

        public function consultarId($id){
            try {
                $sql = $this->db->connect()->prepare("SELECT ncodclase,ccodcata,cdescrip,ntipclase
                                                        FROM tb_clase
                                                        WHERE ncodclase =:id");
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
                $sql = $this->db->connect()->prepare("SELECT ncodclase FROM tb_clase WHERE ccodcata =:codigo");
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