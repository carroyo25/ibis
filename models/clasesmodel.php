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
                                                      WHERE ncodclase=:cod");
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

        /*NUEVA ESTRUCTURA DE DATOS*/
        public function listarGruposConClases($parametros, $page = 1, $limit = 15){
            $descripcion = $parametros['descripcion'] == '' ? '%':'%'.$parametros['descripcion'].'%';
            
            // Calcular offset
            $offset = ($page - 1) * $limit;

            try {
                $resultado = [
                    'grupos' => [],
                    'total_clases' => 0,
                    'total_paginas' => 0,
                    'pagina_actual' => (int)$page
                ];

                // =============================================
                // 1. CONSULTA PARA CONTAR TOTAL DE CLASES
                // =============================================
                $sqlCount = $this->db->connect()->prepare("
                    SELECT 
                        COUNT(*) AS total
                    FROM 
                        tb_grupo tg
                        INNER JOIN tb_clase tc ON tg.ncodgrupo = tc.ncodgrupo
                    WHERE 
                        tg.nflgactivo = 1 
                        AND tc.nflgactivo = 1
                        AND tc.cdescrip LIKE :descripcion
                ");
                $sqlCount->execute(["descripcion" => $descripcion]);
                $totalClases = $sqlCount->fetch(PDO::FETCH_ASSOC)['total'];
                
                $resultado['total_clases'] = (int)$totalClases;
                $resultado['total_paginas'] = ceil($totalClases / $limit);

            // =============================================
            // 2. CONSULTA CON PAGINACIÓN (LIMIT Y OFFSET)
            // =============================================
            $sql = $this->db->connect()->prepare("SELECT 
                    tg.ncodgrupo,
                    tg.ccodcata AS grupo_codigo,
                    UPPER(tg.cdescrip) AS grupo_descrip,
                    tc.ncodclase,
                    tc.ccodcata AS clase_codigo,
                    UPPER(tc.cdescrip) AS clase_descrip
                FROM 
                    tb_grupo tg
                    INNER JOIN tb_clase tc ON tg.ncodgrupo = tc.ncodgrupo
                WHERE 
                    tg.nflgactivo = 1 
                    AND tc.nflgactivo = 1
                    AND tc.cdescrip LIKE :descripcion
                ORDER BY 
                    tg.ccodcata ASC, 
                    tc.ccodcata ASC
                LIMIT :limit OFFSET :offset");

            $sql->bindParam(':descripcion', $descripcion);
            $sql->bindParam(':limit', $limit, PDO::PARAM_INT);
            $sql->bindParam(':offset', $offset, PDO::PARAM_INT);
            $sql->execute();
            
            $rows = $sql->fetchAll(PDO::FETCH_ASSOC);

            // =============================================
            // 3. AGRUPAR LOS DATOS POR GRUPO
            // =============================================
            $gruposMap = [];

            foreach ($rows as $row) {
                $grupoId = $row['ncodgrupo'];
                
                // Si el grupo no existe en el mapa, crearlo
                if (!isset($gruposMap[$grupoId])) {
                    $gruposMap[$grupoId] = [
                        'id' => (int)$row['ncodgrupo'],
                        'codigo' => $row['grupo_codigo'],
                        'nombre' => $row['grupo_descrip'],
                        'color' => $this->getColorGrupo($row['grupo_codigo']),
                        'icon' => $this->getIconGrupo($row['grupo_codigo']),
                        'items' => []
                    ];
                }

                // Agregar la clase al grupo
                $gruposMap[$grupoId]['items'][] = [
                    'code' => $row['clase_codigo'],
                    'desc' => $row['clase_descrip'],
                    'ncodclase' => (int)$row['ncodclase']
                ];
            }

            // Convertir mapa a array indexado
            $resultado['grupos'] = array_values($gruposMap);

            return $resultado;
            } catch (PDOException $th) {
                error_log("Error en listarGruposConClases: " . $th->getMessage());
                return false;
            }
        }

        // Métodos auxiliares para colores e íconos
        private function getColorGrupo($codigo)
        {
            $map = [
                'B01' => 'b01',
                'B02' => 'b02',
                'B03' => 'b03',
                'B04' => 'b04',
                'B05' => 'b05'
            ];
            return $map[$codigo] ?? 'b01';
        }

        private function getIconGrupo($codigo)
        {
            $map = [
                'B01' => 'fa-solid fa-pipe',
                'B02' => 'fa-solid fa-gear',
                'B03' => 'fa-solid fa-ruler',
                'B04' => 'fa-solid fa-radio',
                'B05' => 'fa-solid fa-laptop'
            ];
            return $map[$codigo] ?? 'fa-solid fa-folder';
        }

    }
?>