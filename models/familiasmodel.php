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

                if($this->existeItem($datos['nombre'])){
                    $respuesta = false;
                    $mensaje = "🚧 El nombre de familia ya existe";
                    $clase = "mensaje_error";
                }else{
                    $sql = $this->db->connect()->prepare("INSERT INTO tb_familia 
                                                            SET ncodgrupo=:grupo,ncodclase=:clase,ccodcata=:codigo,
                                                                cdescrip=:nombre,nnivclas=:niv");
                    $sql->execute(["grupo"=>$datos['grupoId'],
                                    "clase" => $datos['claseId'],
                                    "cod"=>strtoupper($datos['codigo']),
                                    "descrip"=>strtoupper($datos['nombre']),
                                    "niv"=>3]);
                    $rowCount = $sql->rowCount();

                    if ($rowCount > 0) {
                        $respuesta = true;
                        $mensaje = "😀 Familia registrada";
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

        private function existeItem($nombre){
            try {
                $sql = $this->db->connect()->prepare("SELECT ncodfamilia FROM tb_familia WHERE cdescrip =:nombre");
                $sql->execute(["nombre"=>$nombre]);
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

        /*************21102026 */
        public function listarJerarquia() {
            try {
                $resultado = [];
                
                // Recibimos la paginación desde JS (por defecto página 1, 10 grupos por página)
                $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
                $porPagina = isset($_POST['porPagina']) ? (int)$_POST['porPagina'] : 10;
                $offset = ($page - 1) * $porPagina;

                // 1. Contar total de Grupos para el paginador
                $sqlTotal = $this->db->connect()->query("SELECT COUNT(*) as total FROM tb_grupo WHERE nflgactivo = 1");
                $totalRegistros = $sqlTotal->fetch(PDO::FETCH_ASSOC)['total'];

                // 2. Consultar Grupos paginados
                $sqlGrupos = $this->db->connect()->prepare("SELECT ncodgrupo as id, ccodcata as codigo, UPPER(cdescrip) as nombre 
                                                        FROM tb_grupo 
                                                        WHERE nflgactivo = 1 
                                                        ORDER BY ccodcata ASC 
                                                        LIMIT :limit OFFSET :offset");
                $sqlGrupos->bindValue(':limit', $porPagina, PDO::PARAM_INT);
                $sqlGrupos->bindValue(':offset', $offset, PDO::PARAM_INT);
                $sqlGrupos->execute();
                $grupos = $sqlGrupos->fetchAll(PDO::FETCH_ASSOC);

                foreach ($grupos as $grupo) {
                    $grupo['icon'] = 'fa-solid fa-folder'; 
                    $grupo['items'] = []; 

                    // 3. Consultar Clases (sin paginar, todas las del grupo)
                    $sqlClases = $this->db->connect()->prepare("SELECT ncodclase as id, ccodcata as codigo, UPPER(cdescrip) as nombre 
                                                                FROM tb_clase 
                                                                WHERE ncodgrupo = :grupo AND nflgactivo = 1 
                                                                ORDER BY ccodcata ASC");
                    $sqlClases->execute(['grupo' => $grupo['id']]);
                    $clases = $sqlClases->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($clases as $clase) {
                        $clase['icon'] = 'fa-solid fa-layer-group'; 
                        $clase['items'] = []; 

                        // 4. Consultar Familias (sin paginar, todas las de la clase)
                        $sqlFamilias = $this->db->connect()->prepare("SELECT ncodfamilia as id, ccodcata as codigo, UPPER(cdescrip) as nombre 
                                                                    FROM tb_familia 
                                                                    WHERE ncodgrupo = :grupo AND ncodclase = :clase AND nflgactivo = 1 
                                                                    ORDER BY ccodcata ASC");
                        $sqlFamilias->execute(['grupo' => $grupo['id'], 'clase' => $clase['id']]);
                        $clase['items'] = $sqlFamilias->fetchAll(PDO::FETCH_ASSOC);

                        $grupo['items'][] = $clase;
                    }

                    $resultado[] = $grupo;
                }

                // Devolvemos el JSON con el total de registros para que JS calcule las páginas
                header('Content-Type: application/json');
                echo json_encode([
                    'grupos' => $resultado,
                    'total_registros' => $totalRegistros
                ]);
                exit;

            } catch (PDOException $th) {
                echo json_encode(['error' => $th->getMessage()]);
                exit;
            }
        }

        public function clasesPorGrupo() {
            try {
                // Recibimos el ID del grupo desde el fetch
                $grupoId = isset($_POST['grupo']) ? (int)$_POST['grupo'] : 0;
                
                $sql = $this->db->connect()->prepare("SELECT ncodclase as id, ccodcata as codigo, UPPER(cdescrip) as nombre 
                                                    FROM tb_clase 
                                                    WHERE ncodgrupo = :grupo AND nflgactivo = 1 
                                                    ORDER BY ccodcata ASC");
                $sql->execute(['grupo' => $grupoId]);
                $clases = $sql->fetchAll(PDO::FETCH_ASSOC);

                header('Content-Type: application/json');
                return json_encode(['datos' => $clases]);
                exit;
            } catch (PDOException $th) {
                return json_encode(['error' => $th->getMessage()]);
            }
        }

        public function generarCodigoFamilia(){
            try {
                $grupo = $_POST['grupo'] ?? null;
                $clase = $_POST['clase'] ?? null;

                if ($grupo === null || $grupo === '') {
                    return ['error' => 'Falta el grupo'];
                }

                if ($clase === null || $clase === '') {
                    return ['error' => 'Falta el clase'];
                }

                $sql = $this->db->connect()->prepare("SELECT COUNT(CAST(ncodgrupo AS UNSIGNED)) AS max_numero
                                                        FROM tb_familia
                                                        WHERE ncodgrupo = :grupo
                                                        AND ncodclase = :clase");
                $sql->execute(['grupo' => $grupo,'clase' => $clase]);
                $fila = $sql->fetch(PDO::FETCH_ASSOC);

                $total = (int)($fila['max_numero'] ?? 0);
                $nuevoNumero = $total + 1;

                return [str_pad((string)$nuevoNumero, 4, "0", STR_PAD_LEFT)];

            } catch (PDOException $th) {
                return json_encode(['error' => $th->getMessage()]);
            }
        }
    }
?>