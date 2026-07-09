<?php
    class GruposModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarGruposPaginados($parametros, $page = 1, $limit = 15){
            $offset = ($page - 1) * $limit;
            $descrip = $parametros['descripcion'] == '' ? '%' : '%' . $parametros['descripcion'] . '%';

            try {
                $db = $this->db->connect();
                
                // Contar total
                $sqlCount = "SELECT
                                COUNT( g.ncodgrupo ) AS total 
                            FROM
                                tb_grupo g
                            WHERE  g.nflgactivo = 1
                                AND g.cdescrip LIKE :descripcion";

                $stmt = $db->prepare($sqlCount);
                $stmt->execute(["descripcion" => $descrip]);
                $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
                $totalPaginas = ceil($total / $limit);

                // Consulta con LIMIT
                $sql = "SELECT g.ccodcata,
                                UPPER(g.cdescrip) cdescrip,
                                g.ncodgrupo,
                                g.ntipclase
                        FROM tb_grupo g
                        WHERE 
                            g.nflgactivo = 1
                            AND g.cdescrip LIKE :descripcion  
                        ORDER BY ccodcata
                        LIMIT :offset, :limit";

                $stmt = $db->prepare($sql);
                $stmt->bindParam(':descripcion', $descrip);
                $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
                $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
                $stmt->execute();

                $data = [];
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $data[] = $row;
                }

                //retorno del a funcion
                return [
                    'success' => true,
                    'data' => $data,
                    'total' => intval($total),
                    'pagina' => $page,
                    'total_paginas' => $totalPaginas
                ];

            } catch (PDOException $th) {
                return [
                    'success' => false,
                    'message' => $e->getMessage()
                ];
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
                                "items"=>null);
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

                //return $this->listarGrupos1();
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