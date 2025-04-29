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
        
                $sql = $this->db->connect()->prepare("
                    SELECT
                        lg_guias.cnumguia,
                        lg_guias.corigen,
                        lg_guias.cdestino,
                        lg_guias.ftraslado,
                        lg_guias.freg,
                        lg_guias.guiasunat,
                        YEAR(lg_guias.freg) AS anio,
                        lg_guias.cenvio,
                        lg_guias.cobserva
                    FROM
                        lg_guias 
                    WHERE
                        lg_guias.nflgActivo = 1 
                        AND IFNULL(lg_guias.guiasunat,'') LIKE :sunat
                        AND lg_guias.cnumguia LIKE :guia
                        AND YEAR(lg_guias.freg) = :anio
                    ORDER BY
                        lg_guias.freg DESC");
        
                $sql->execute([
                    "sunat" => $sunat,
                    "guia"  => $guia,
                    "anio"  => $anio
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
                    "datos"   => $docData
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
    }
?>