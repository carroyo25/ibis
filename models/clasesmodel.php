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
                    $sql = $this->db->connect()->prepare("INSERT INTO tb_clase 
                                                            SET ncodgrupo=:grupo,ccodcata=:cod,cdescrip=:descrip,nnivclas=:niv");
                    $sql->execute(["grupo"=>$datos['codgrupo'],
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
                
                $salida = array("respuesta"=>$respuesta,
                                 "mensaje"=>$mensaje,
                                 "clase"=>$clase,
                                "items"=>$this->listarTitulosGrupos());
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

                $sql = $this->db->connect()->prepare("UPDATE tb_clase 
                                                      SET cdescrip=:descrip 
                                                      WHERE ncodgrupo=:cod");
                $sql->execute(["cod"=>$datos['codclase'],
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
                $sql = $this->db->connect()->prepare("UPDATE tb_clase SET nflgactivo = 0 WHERE ncodclase=:id");
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
                                                        tb_grupo.ncodgrupo,
                                                        UPPER(tb_grupo.cdescrip) AS cdescrip,
                                                        tb_grupo.ccodcata 
                                                    FROM
                                                        tb_grupo
                                                        INNER JOIN tb_clase ON tb_grupo.ncodgrupo = tb_clase.ncodgrupo 
                                                    WHERE
                                                        tb_grupo.nflgactivo = 1 
                                                    GROUP BY
                                                        tb_grupo.ncodgrupo 
                                                    ORDER BY
                                                        tb_grupo.ccodcata ASC");
                $sql->execute();
                $rowCount = $sql->rowcount();

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()){
                        $salida .='<tr class="tituloGrupo">
                                        <td class="pl20px" colspan="3">'.$rs['ccodcata'].' - '.strtoupper($rs['cdescrip']).'</td>
                                    </tr>'.$this->listarClases($rs['ncodgrupo']);
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
                                                tb_clase.ncodgrupo,
                                                tb_clase.ncodclase,
                                                tb_clase.ccodcata,
                                                tb_clase.cdescrip,
                                                UPPER(
                                                CONCAT( tb_grupo.ccodcata, ' - ', tb_grupo.cdescrip )) AS nombre_grupo 
                                            FROM
                                                tb_clase
                                                INNER JOIN tb_grupo ON tb_clase.ncodgrupo = tb_grupo.ncodgrupo 
                                            WHERE
                                                tb_clase.ncodclase = :id");
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