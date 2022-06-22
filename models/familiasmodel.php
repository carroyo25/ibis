<?php
    class FamiliasModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function insertarFamilia($datos) {
            try {
                $respuesta = false;
                $mensaje = "Error al grabar el registro";
                $clase = "mensaje_error";

                if($this->existeItem($datos['codigo'])){
                    $respuesta = false;
                    $mensaje = "Código de familia duplicada";
                    $clase = "mensaje_error";
                }else{
                    $sql = $this->db->connect()->prepare("INSERT INTO tb_familia 
                                                            SET ncodgrupo=:grupo,ncodclase=:clase,ccodcata=:cod,
                                                                cdescrip=:descrip,nnivclas=:niv");
                    $sql->execute(["grupo"=>$datos['codgrupo'],
                                    "clase" => $datos['codclase'],
                                    "cod"=>strtoupper($datos['codigo']),
                                    "descrip"=>strtoupper($datos['descripcion']),
                                    "niv"=>3]);
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
                                 "items"=>$this->listarGrupos(),
                                 "numero"=>$this->crearCodigoClase($datos['codgrupo'],$datos['codclase']));
                return $salida;

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function modificarFamilia($datos){
            $respuesta = false;
            $mensaje = "Error al grabar el registro";
            $clase = "mensaje_error";

            $sql = $this->db->connect()->prepare("UPDATE tb_familia 
                                                 SET cdescrip=:descrip
                                                 WHERE ncodfamilia =:familia");
            $sql->execute(["familia"=>$datos['codfamilia'],
                            "descrip"=>strtoupper($datos['descripcion'])]);
            $rowCount = $sql->rowCount();

            if ($rowCount > 0) {
                $respuesta = false;
                $mensaje = "Grupo creado";
                $clase = "mensaje_correcto";
            }

            $salida = array("respuesta"=>$respuesta,
                        "mensaje"=>$mensaje,
                        "clase"=>$clase);

            return $salida;
        }

        public function listaFamiliaPrincipal(){
            return $this->model->listarGrupos();
        }

        public function listarGrupos(){
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
                                    </tr>'.$this->clases($rs['ncodgrupo']);
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

        public function consultaId($id){
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                        tb_familia.cdescrip,
                                                        tb_familia.ccodcata,
                                                        tb_familia.nnivclas,
                                                        tb_familia.ncodfamilia,
                                                        tb_familia.ncodclase,
                                                        tb_familia.ncodgrupo,
                                                        CONCAT( tb_clase.ccodcata, ' - ', tb_clase.cdescrip ) AS nombre_clase,
                                                        CONCAT( tb_grupo.ccodcata, ' - ', tb_grupo.cdescrip ) AS nombre_grupo 
                                                    FROM
                                                        tb_familia
                                                        INNER JOIN tb_clase ON tb_familia.ncodclase = tb_clase.ncodclase
                                                        INNER JOIN tb_grupo ON tb_familia.ncodgrupo = tb_grupo.ncodgrupo 
                                                    WHERE
                                                        tb_familia.ncodfamilia = :id");
                 $sql->execute(["id"=>$id]);
                 $rowCount = $sql->rowCount();
                 
                 if ($rowCount > 0) {
                     $docData = array();
                     while($row=$sql->fetch(PDO::FETCH_ASSOC)){
                         $docData[] = $row;
                     } 
                 }

                 return array("familia"=>$docData);
            } catch (PDOException $th) {
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function eliminaFamilia($id){
            try {
                $sql = $this->db->connect()->prepare("UPDATE tb_familia SET nflgactivo = 0 WHERE ncodfamilia=:id");
                $sql->execute([$id]);

                return $this->listarGrupos();
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            } 
        }

        private function existeItem($codigo){
            try {
                $sql = $this->db->connect()->prepare("SELECT ncodfamilia FROM tb_familia WHERE ccodcata =:codigo");
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

        private function clases($grupo){
            try {
                $salida = "";
                $sql = $this->db->connect()->prepare("SELECT ncodclase,ncodgrupo,ccodcata,UPPER(cdescrip) AS cdescrip 
                                                        FROM tb_clase
                                                        WHERE ncodgrupo =:grupo AND nflgactivo = 1
                                                        ORDER BY ccodcata ASC");
                $sql->execute(['grupo'=>$grupo]);
                $rowCount = $sql->rowCount();

                if($rowCount > 0) {
                    while($rs = $sql->fetch()){
                        $salida .='<tr class="tituloClase">
                                        <td class="pl20px" colspan="3">'.$rs['ccodcata'].' - '.$rs['cdescrip'].'</td>
                                    </tr>'.$this->familias($rs['ncodgrupo'],$rs['ncodclase']);

                    }
                }
                
                return $salida;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function familias($grupo,$clase){
            $salida = "";
            try {
                $sql = $this->db->connect()->prepare("SELECT ncodgrupo,ncodclase,ncodfamilia,ccodcata,UPPER(cdescrip) AS cdescrip 
                                                        FROM tb_familia
                                                        WHERE ncodgrupo=:grupo 
                                                        AND ncodclase =:clase
                                                        AND nflgactivo = 1
                                                        ORDER BY ccodcata ASC");
                $sql->execute(["grupo"=>$grupo,"clase"=>$clase]);
                $rowCount = $sql->rowCount();

                if($rowCount > 0){
                    while($rs = $sql->fetch()){
                        $salida .='<tr class="pointer" data-id="'.$rs['ncodfamilia'].'">
                                        <td class="textoCentro">'.$rs['ccodcata'].'</td>
                                        <td class="pl20px">'.$rs['cdescrip'].'</td>
                                        <td class="textoCentro"><a href="'.$rs['ncodclase'].'"><i class="fas fa-trash-alt"></i></a></td>
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