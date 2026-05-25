<?php
    class ReporteGuiasModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarGuias($datos) {
            try {
                // Validate required fields
                if (!isset($datos['anio']) || empty($datos['anio'])) {
                    throw new Exception("El campo 'anio' es obligatorio.");
                }
        
                // Sanitize inputs
                $anio  = (int) $datos['anio'];
                $guia  = !empty($datos['guia']) ? '%' . $datos['guia'] . '%' : '%';
                $sunat = !empty($datos['sunat']) ? '%' . $datos['sunat'] . '%' : '%';

                $inicio = $datos['inicio'];
                $items = $datos['items'];
        
                $sql = $this->db->connect()->prepare("
                    SELECT
                        lg_guias.cnumguia,
                        lg_guias.corigen,
                        lg_guias.cdestino,
                        lg_guias.ftraslado,
                        DATE_FORMAT(lg_guias.freg,'%d/%m/%Y') emision,
                        lg_guias.guiasunat,
                        YEAR(lg_guias.freg) AS anio,
                        lg_guias.cenvio,
                        UPPER(lg_guias.cobserva) cobserva
                    FROM
                        lg_guias 
                    WHERE
                        lg_guias.nflgActivo = 1 
                        AND IFNULL(lg_guias.guiasunat,'') LIKE :sunat
                        AND lg_guias.cnumguia LIKE :guia
                        AND YEAR(lg_guias.freg) = :anio
                    ORDER BY
                        lg_guias.freg DESC
                    LIMIT :inicio,:limite");
        
                $sql->execute([
                    "sunat" => $sunat,
                    "guia"  => $guia,
                    "anio"  => $anio,
                    "inicio"=> $inicio,
                    "limite"=> $items,
                ]);
        
                $docData = $sql->fetchAll(PDO::FETCH_ASSOC);
        
                if (empty($docData)) {
                    return [
                        "success" => false,
                        "message" => "No se encontraron registros."
                    ];
                }
        
                return [
                    "success" => true,
                    "datos"   => $docData,
                    "items"   => $this->contarGuias()
                ];
        
            } catch (PDOException $e) {
                error_log("Database Error: " . $e->getMessage());
                return [
                    "success" => false,
                    "message" => "Error en la base de datos."
                ];
            } catch (Exception $e) {
                error_log("General Error: " . $e->getMessage());
                return [
                    "success" => false,
                    "message" => $e->getMessage()
                ];
            }
        }

        public function contarGuias($filtros = []) {
            try {
                $anio  = isset($filtros['anio']) ? (int) $filtros['anio'] : date("Y");
                $guia  = isset($filtros['guia']) && !empty($filtros['guia']) ? '%' . $filtros['guia'] . '%' : '%';
                $sunat = isset($filtros['sunat']) && !empty($filtros['sunat']) ? '%' . $filtros['sunat'] . '%' : '%';

                $sql = $this->db->connect()->prepare("
                    SELECT COUNT(*) as total
                    FROM lg_guias 
                    WHERE lg_guias.nflgActivo = 1 
                        AND IFNULL(lg_guias.guiasunat,'') LIKE :sunat
                        AND lg_guias.cnumguia LIKE :guia
                        AND YEAR(lg_guias.freg) = :anio
                ");

                $sql->execute([
                    "sunat" => $sunat,
                    "guia"  => $guia,
                    "anio"  => $anio,
                ]);

                $result = $sql->fetch(PDO::FETCH_ASSOC);
                return $result['total'];

            } catch (PDOException $e) {
                error_log("Database Error: " . $e->getMessage());
                return 0;
            }
        }

        public function llenarFiltros($parametros){
            try {
                $campo = "g.".$parametros['campo'];
                $limite = $parametros['items'] == '0' ? ' LIMIT 100': '';

                $slqChain = "SELECT
                                $campo 
                            FROM
                                lg_guias g
                            WHERE 
                            g.nflgActivo = 1
                            ORDER BY
                                g.freg DESC
                            LIMIT 100";

                $sql = $this->db->connect()->prepare($slqChain);
                $sql->execute();
                
                $docData = $sql->fetchAll(PDO::FETCH_ASSOC);
        
                if (empty($docData)) {
                    return [
                        "success" => false,
                        "message" => "No se encontraron registros.",
                        "consulta" => $slqChain
                    ];
                }
        
                return [
                    "success" => true,
                    "datos"   => $docData
                ];

            } catch (Exception $e) {
                error_log("General Error: " . $e->getMessage());
                return [
                    "success" => false,
                    "message" => $e->getMessage()
                ];
            }
        }
    }
?>