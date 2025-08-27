<?php
    class RepoTransporteModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarTransportes($datos){
            try {
                $anio        = !isset($datos['anio']) || empty($datos['anio']) ? 2025 : $datos['anio'];
                $pedido      = !empty($datos['pedido']) ? '%' . $datos['pedido'] . '%' : '%';
                $orden       = !empty($datos['orden']) ? $datos['orden'] : '%';
                $descripcion = !empty($datos['descripcion']) ? '%' . $datos['descripcion'] . '%' : '%';
                $proyecto    = $datos['proyecto'] == -1 ? '%' : $datos['proyecto'];

                $sql = $this->db->connect()->prepare("SELECT
                                                            lg_ordendet.id_cprod,
                                                            LPAD( lg_ordencab.cnumero, 6, 0 ) AS orden,
                                                            lg_ordencab.nEstadoDoc,
                                                            lg_ordendet.cobserva,
                                                            lg_ordencab.cper,
                                                            tb_proyectos.cdesproy,
                                                            LPAD( tb_pedidocab.nrodoc, 6, 0 ) AS pedido,
                                                            UPPER( cm_producto.cdesprod ) AS cdesprod,
                                                            tb_proyectos.ccodproy,
                                                            lg_ordencab.id_regmov,
	                                                        tb_pedidocab.idreg,
                                                            cm_producto.ccodprod 
                                                        FROM
                                                            lg_ordendet
                                                            INNER JOIN lg_ordencab ON lg_ordendet.id_regmov = lg_ordencab.id_regmov
                                                            INNER JOIN tb_proyectos ON lg_ordencab.ncodcos = tb_proyectos.nidreg
                                                            INNER JOIN tb_pedidocab ON lg_ordencab.id_refpedi = tb_pedidocab.idreg
                                                            INNER JOIN cm_producto ON lg_ordendet.id_cprod = cm_producto.id_cprod 
                                                        WHERE
                                                            lg_ordendet.id_cprod = 3162 
                                                            AND lg_ordencab.nEstadoDoc <> 105 
                                                            AND tb_proyectos.nidreg LIKE :proyecto
                                                            AND lg_ordencab.cnumero LIKE :orden
                                                            AND tb_pedidocab.nrodoc LIKE :pedido
                                                            AND lg_ordendet.cobserva LIKE :descripcion
                                                            AND lg_ordencab.cper LIKE :anio
                                                        ORDER BY
                                                            lg_ordencab.cper DESC");

                
                $sql->execute([
                    "pedido"        => $pedido,
                    "orden"         => $orden,
                    "descripcion"   => $descripcion,
                    "proyecto"      => $proyecto,
                    "anio"          => $anio,
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

            } catch (Exception $e) {
                error_log("General Error: " . $e->getMessage());
                return [
                    "success" => false,
                    "message" => $e->getMessage()
                ];
            }
        }

        public function listarAdjuntos($orden){
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                        lg_regdocumento.creferencia,
                                                        lg_regdocumento.cdocumento 
                                                    FROM
                                                        lg_regdocumento 
                                                    WHERE
                                                        lg_regdocumento.cmodulo = 'PED' 
                                                        AND lg_regdocumento.nidrefer = :orden 
                                                        AND lg_regdocumento.nflgactivo = 1");
                $sql->execute(["orden"=> $orden]);
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