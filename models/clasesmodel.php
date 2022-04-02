<?php
    class ClasesModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function insertar($datos){
            try {
                $respuesta = false;
                $mensaje = "Error al grabar el registro";
                $clase = "mensaje_error";

                if($this->existeItem($datos['codigo'])){
                    $respuesta = false;
                    $mensaje = "Código de clase duplicada";
                    $clase = "mensaje_error";
                }else{
                    $sql = $this->db->connect()->prepare("INSERT INTO tb_grupo 
                                                            SET ncodclase=:clase,ccodcata=:cod,cdescrip=:descrip,nnivclas=:niv");
                    $sql->execute(["clase"=>$datos['codclase'],
                                    "cod"=>strtoupper($datos['codigo']),
                                    "descrip"=>strtoupper($datos['descripcion']),
                                    "niv"=>2]);
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
                $mensaje = "Error al grabar el registro";
                $clase = "mensaje_error";

                $sql = $this->db->connect()->prepare("UPDATE tb_grupo 
                                                      SET cdescrip=:descrip 
                                                      WHERE ncodgrupo=:cod");
                $sql->execute(["cod"=>$datos['codgrupo'],
                                "descrip"=>strtoupper($datos['descripcion'])]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    $respuesta = false;
                    $mensaje = "Grupo actualizado";
                    $clase = "mensaje_correcto";
                }
                
                $salida = array("respuesta"=>$respuesta, "mensaje"=>$mensaje,"clase"=>$clase);
                return $salida;

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            } 
        }

        public function desactivar($id){
            try {
                $sql = $this->db->connect()->prepare("UPDATE tb_grupo SET nflgactivo = 0 WHERE ncodgrupo=:id");
                $sql->execute([$id]);

                return $this->listarTitulosGrupos();
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            } 
        }

        public function listarTitulosGrupos(){
            try {
                $salida = "";

                $sql = $this->db->connect()->query("SELECT
                                                        tb_grupo.ncodclase,
                                                        tb_clase.cdescrip,
                                                        tb_clase.ccodcata 
                                                    FROM
                                                        tb_grupo
                                                        INNER JOIN tb_clase ON tb_grupo.ncodclase = tb_clase.ncodclase 
                                                    WHERE
                                                        tb_grupo.nflgactivo = 1 
                                                    GROUP BY
                                                        tb_grupo.ncodclase 
                                                    ORDER BY
                                                        tb_clase.cdescrip ASC");
                $sql->execute();
                $rowCount = $sql->rowcount();

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()){
                        $salida .='<tr class="tituloClase">
                                        <td class="pl20px" colspan="3">'.$rs['ccodcata'].' - '.strtoupper($rs['cdescrip']).'</td>
                                    </tr>'.$this->listarGrupos($rs['ncodclase']);
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

        public function consultarGrupoId($id){
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                        tb_grupo.ncodclase,
                                                        tb_grupo.ncodgrupo,
                                                        tb_grupo.ccodcata,
                                                        tb_grupo.cdescrip,
                                                        UPPER(CONCAT(tb_clase.ccodcata,' - ',tb_clase.cdescrip)) AS nombre_clase
                                                    FROM
                                                        tb_grupo
                                                        INNER JOIN tb_clase ON tb_grupo.ncodclase = tb_clase.ncodclase 
                                                    WHERE
                                                        tb_grupo.ncodgrupo = :id");
                 $sql->execute(["id"=>$id]);
                 $rowCount = $sql->rowCount();
                 
                 if ($rowCount > 0) {
                     $docData = array();
                     while($row=$sql->fetch(PDO::FETCH_ASSOC)){
                         $docData[] = $row;
                     } 
                 }

                 return array("clase"=>$docData);
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function listarGrupos($clase){
            try {
                $salida = "";
                $sql = $this->db->connect()->prepare("SELECT ncodgrupo,ccodcata,cdescrip 
                                                        FROM tb_grupo
                                                        WHERE ncodclase =:clase AND nflgactivo = 1
                                                        ORDER BY cdescrip DESC");
                $sql->execute(['clase'=>$clase]);
                $rowCount = $sql->rowCount();

                if($rowCount > 0) {
                    while($rs = $sql->fetch()){
                        $salida .='<tr class="pointer" data-id="'.$rs['ncodgrupo'].'">
                                        <td class="textoCentro">'.$rs['ccodcata'].'</td>
                                        <td class="pl20px">'.$rs['cdescrip'].'</td>
                                        <td class="textoCentro"><a href="'.$rs['ccodcata'].'"><i class="fas fa-trash-alt"></i></a></td>
                                    </tr>';

                    }
                }
                
                return $salida;
            } catch (PDOException $th) {
                echo $th->getMessage();
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