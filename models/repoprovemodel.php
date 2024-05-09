<?php
    class RepoProveModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function ordenesProveedor($parametos){
            $orden  = isset($parametros['ordenSearch'])  && $parametros['ordenSearch'] != "" ?  $parametros['ordenSearch'] : "%";
            $costos = isset($parametros['costosSearch']) && $parametros['costosSearch'] == "" ? $parametros['costosSearch'] : "%";
            $anio   = isset($parametros['anioSearch'])   && $parametros['anioSearch'] != "" ? $parametros['anioSearch'] : "2024";
            $docData = [];

            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                        tb_costusu.ncodcos,
                                                        tb_costusu.ncodproy,
                                                        tb_costusu.id_cuser,
                                                        lg_ordencab.id_regmov,
                                                        lg_ordencab.cnumero,
                                                        lg_ordencab.ffechadoc,
                                                        lg_ordencab.nNivAten,
                                                        lg_ordencab.nEstadoDoc,
                                                        lg_ordencab.ncodpago,
                                                        lg_ordencab.nplazo,
                                                        lg_ordencab.cdocPDF,
                                                        lg_ordencab.ntotal,
                                                        lg_ordencab.ncodmon,
                                                        UPPER( tb_pedidocab.concepto ) AS concepto,
                                                        UPPER( tb_pedidocab.detalle ) AS detalle,
                                                        UPPER(
                                                        CONCAT_WS( tb_area.ccodarea, tb_area.cdesarea )) AS area,
                                                        UPPER(
                                                        CONCAT_WS( tb_proyectos.ccodproy, tb_proyectos.cdesproy )) AS costos,
                                                        tb_proyectos.ccodproy,
                                                        lg_ordencab.nfirmaLog,
                                                        lg_ordencab.nfirmaFin,
                                                        lg_ordencab.nfirmaOpe,
                                                        tb_parametros.cdescripcion AS atencion,
                                                        UPPER( cm_entidad.crazonsoc ) AS proveedor 
                                                    FROM
                                                        tb_costusu
                                                        INNER JOIN lg_ordencab ON tb_costusu.ncodproy = lg_ordencab.ncodpry
                                                        INNER JOIN tb_pedidocab ON lg_ordencab.id_refpedi = tb_pedidocab.idreg
                                                        INNER JOIN tb_area ON lg_ordencab.ncodarea = tb_area.ncodarea
                                                        INNER JOIN tb_proyectos ON lg_ordencab.ncodpry = tb_proyectos.nidreg
                                                        INNER JOIN tb_parametros ON lg_ordencab.nNivAten = tb_parametros.nidreg
                                                        INNER JOIN cm_entidad ON lg_ordencab.id_centi = cm_entidad.id_centi 
                                                    WHERE
                                                        tb_costusu.id_cuser = :user 
                                                        AND tb_costusu.nflgactivo = 1 
                                                        AND lg_ordencab.cper LIKE :anio
                                                        AND lg_ordencab.cnumero LIKE :orden
                                                        AND tb_costusu.ncodproy LIKE :costos
                                                        AND ISNULL(lg_ordencab.ntipdoc)
                                                    ORDER BY
                                                        id_regmov DESC
                                                    LIMIT 150");
                    
                    $sql->execute(["user"=>$_SESSION['iduser'],
                                   "anio"=>$anio,
                                   "orden"=>$orden,
                                   "costos"=>$costos]);

                $rowCount = $sql->rowCount();

                if ($rowCount) {
                    while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                        $docData[] = $row;
                    }
                }

                return array("ordenes"=>$docData);

            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }
    }
?>