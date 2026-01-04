<?php
    class AdjuntoProveedorModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function archivosAdjuntos($parametros){
            try {
                $docData = [];
                $nombre = $parametros['nameSearch'] == '' ? '%' : '%'.$parametros['nameSearch'].'%';

                $sql = $this->db->connect()->prepare("SELECT
                                                        cm_entidadadj.id_centi,
                                                        cm_entidad.cnumdoc,
                                                        cm_entidad.crazonsoc
                                                    FROM
                                                        cm_entidadadj
                                                        INNER JOIN cm_entidad ON cm_entidadadj.id_centi = cm_entidad.id_centi
                                                    WHERE cm_entidad.nflgactivo = 7
                                                    AND cm_entidad.crazonsoc LIKE :nombre
                                                    GROUP BY cm_entidad.id_centi
                                                    ORDER BY cm_entidad.crazonsoc");
                $sql->execute(["nombre"=>$nombre]);
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

            } catch (PDOException $th) {
               echo "Error: " . $th->getMessage();
               return false;
           }
        }
    }
?>