<?php
    class ReporteGuiasModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarGuias($params = []) {
            try {
                // Asegurar que $params sea un array
                if (!is_array($params)) {
                    $params = [];
                }
                
                // Extraer parámetros con valores por defecto
                $inicio = isset($params['inicio']) ? (int) $params['inicio'] : 0;
                $items = isset($params['items']) ? (int) $params['items'] : 15;
                $anio = isset($params['anio']) ? (int) $params['anio'] : date("Y");
                
                // Obtener guia y sunat, asegurando que sean strings o arrays
                $guia = isset($params['guia']) ? $params['guia'] : '';
                $sunat = isset($params['sunat']) ? $params['sunat'] : '';
                
                $conditions = ["lg_guias.nflgActivo = 1", "YEAR(lg_guias.freg) = ?"];
                $sqlParams = [$anio];
                
                // Manejar guia - puede ser string o array
                if (!empty($guia)) {
                    if (is_array($guia)) {
                        // Para arrays (múltiples checkboxes)
                        $placeholders = implode(',', array_fill(0, count($guia), '?'));
                        $conditions[] = "lg_guias.cnumguia IN ($placeholders)";
                        foreach ($guia as $valor) {
                            $sqlParams[] = $valor;
                        }
                    } else {
                        // Para string (búsqueda normal)
                        $conditions[] = "lg_guias.cnumguia LIKE ?";
                        $sqlParams[] = '%' . $guia . '%';
                    }
                }
                
                // Manejar sunat - puede ser string o array
                if (!empty($sunat)) {
                    if (is_array($sunat)) {
                        // Para arrays (múltiples checkboxes)
                        $placeholders = implode(',', array_fill(0, count($sunat), '?'));
                        $conditions[] = "IFNULL(lg_guias.guiasunat,'') IN ($placeholders)";
                        foreach ($sunat as $valor) {
                            $sqlParams[] = $valor;
                        }
                    } else {
                        // Para string (búsqueda normal)
                        $conditions[] = "IFNULL(lg_guias.guiasunat,'') LIKE ?";
                        $sqlParams[] = '%' . $sunat . '%';
                    }
                }
                
                $whereClause = implode(" AND ", $conditions);
                
                $sql = $this->db->connect()->prepare("
                    SELECT 
                        lg_guias.cnumguia,
                        DATE_FORMAT(lg_guias.freg, '%Y-%m-%d') as emision,
                        YEAR(lg_guias.freg) as anio,
                        lg_guias.guiasunat,
                        lg_guias.cenvio,
                        lg_guias.cobserva
                    FROM lg_guias 
                    WHERE $whereClause
                    ORDER BY lg_guias.freg DESC
                    LIMIT ?, ?
                ");
                
                // Agregar los parámetros de LIMIT
                $sqlParams[] = (int) $inicio;
                $sqlParams[] = (int) $items;
                
                error_log("SQL Params: " . print_r($sqlParams, true));
                
                $sql->execute($sqlParams);
                $result = $sql->fetchAll(PDO::FETCH_ASSOC);
                
                return [
                    'success' => true,
                    'datos' => $result
                ];
                
            } catch (PDOException $e) {
                error_log("Database Error listarGuias: " . $e->getMessage());
                error_log("SQL Params: " . print_r($sqlParams ?? [], true));
                return [
                    'success' => false,
                    'message' => 'Error al listar las guías: ' . $e->getMessage()
                ];
            }
        }

        public function contarGuias($filtros = []) {
            try {
                $anio = isset($filtros['anio']) ? (int) $filtros['anio'] : date("Y");
                
                $conditions = ["lg_guias.nflgActivo = 1", "YEAR(lg_guias.freg) = ?"];
                $params = [$anio];
                
                // Manejar guia - puede ser string o array
                if (isset($filtros['guia']) && !empty($filtros['guia'])) {
                    if (is_array($filtros['guia'])) {
                        $placeholders = implode(',', array_fill(0, count($filtros['guia']), '?'));
                        $conditions[] = "lg_guias.cnumguia IN ($placeholders)";
                        $params = array_merge($params, $filtros['guia']);
                    } else {
                        $conditions[] = "lg_guias.cnumguia LIKE ?";
                        $params[] = '%' . $filtros['guia'] . '%';
                    }
                }
                
                // Manejar sunat - puede ser string o array
                if (isset($filtros['sunat']) && !empty($filtros['sunat'])) {
                    if (is_array($filtros['sunat'])) {
                        $placeholders = implode(',', array_fill(0, count($filtros['sunat']), '?'));
                        $conditions[] = "IFNULL(lg_guias.guiasunat,'') IN ($placeholders)";
                        $params = array_merge($params, $filtros['sunat']);
                    } else {
                        $conditions[] = "IFNULL(lg_guias.guiasunat,'') LIKE ?";
                        $params[] = '%' . $filtros['sunat'] . '%';
                    }
                }
                
                $whereClause = implode(" AND ", $conditions);
                $sql = $this->db->connect()->prepare("
                    SELECT COUNT(*) as total
                    FROM lg_guias 
                    WHERE $whereClause
                ");
                
                $sql->execute($params);
                $result = $sql->fetch(PDO::FETCH_ASSOC);
                return $result['total'];
                
            } catch (PDOException $e) {
                error_log("Database Error contarGuias: " . $e->getMessage());
                error_log("Params: " . print_r($params, true));
                return 0;
            }
        }

        public function llenarFiltros($parametros){
            try {
                $campo = "g.".$parametros['campo'];
                $limite = $parametros['items'] == 0 ? ' LIMIT 25': '';
                $cadena = $parametros['string'] == '' ? "$campo LIKE '%'": "$campo LIKE '%".$parametros['string']."%'";
                $lista = $parametros['lista'];

                $slqChain = "SELECT
                                $campo 
                            FROM
                                lg_guias g
                            WHERE 
                                g.nflgActivo = 1
                                AND $cadena
                                AND $campo IS NOT NULL
                            ORDER BY
                                g.freg DESC
                            $limite";

                $sql = $this->db->connect()->prepare($slqChain);
                $sql->execute();
                
                $docData = $sql->fetchAll(PDO::FETCH_ASSOC);
        
                if (empty($docData)) {
                    return [
                        "success" => false,
                        "message" => "No se encontraron registros.",
                        "consulta" => htmlspecialchars($slqChain,ENT_QUOTES,'UTF-8')
                    ];
                }
        
                return [
                    "success" => true,
                    "datos"   => $docData,
                    "consulta" => $slqChain
                ];

            } catch (Exception $e) {
                error_log("General Error: " . $e->getMessage());
                return [
                    "success" => false,
                    "message" => $e->getMessage(),
                    "consulta" => htmlspecialchars($slqChain,ENT_QUOTES,'UTF-8')
                ];
            }
        }
    }
?>