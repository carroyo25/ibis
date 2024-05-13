<?php
    class RepoProveModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarOrdenesProveedor($parametros){
            $orden          = isset($parametros['ordenSearch'])  && $parametros['ordenSearch'] != "" ? $parametros['ordenSearch'] : "%";
            $entidad        = isset($parametros['entidad'])  && $parametros['entidad'] != "" ? $parametros['entidad'] : "%";
            $costos         = isset($parametros['costosSearch'])  && $parametros['costosSearch'] != "" ? $parametros['costosSearch'] : "%";
            $anioInicial    = isset($parametros['inicialSearch']) && $parametros['inicialSearch'] != "" ? $parametros['inicialSearch'] : "%";
            $anioFinal      = isset($parametros['finalSearch']) && $parametros['finalSearch'] != "" ? $parametros['finalSearch'] : "%";
            $docData = [];

            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                        tb_costusu.ncodcos,
                                                        tb_costusu.ncodproy,
                                                        tb_costusu.id_cuser,
                                                        lg_ordencab.id_regmov,
                                                        LPAD(lg_ordencab.cnumero,6,0) AS cnumero,
                                                        DATE_FORMAT(lg_ordencab.ffechadoc,'%d/%m/%Y') AS ffechadoc,
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
                                                        AND cm_entidad.crazonsoc LIKE :entidad
                                                        AND ISNULL(lg_ordencab.ntipdoc)
                                                    ORDER BY
                                                        id_regmov DESC");
                
                $sql->execute(["user"=>$_SESSION['iduser'],"anio"=>2024,"orden"=>$orden,"costos"=>$costos,"entidad"=>$entidad]);
                $rowCount = $sql->rowCount();
                
                if ($rowCount) {
                    $respuesta = true;
                    $i = 0;
                    
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

        public function valoresfiltros($campo) {
            if ($campo == 'cnumero') {
                $valores = $this->listaNumeroOrden();
            }

            return array("valores"=>$valores);
        }

        private function listaNumeroOrden() {
            try {
                $sql = $this->db->connect()->query("SELECT
                                                        LPAD( lg_ordencab.cnumero, 6, 0 ) AS onumero 
                                                    FROM
                                                        lg_ordencab 
                                                    GROUP BY
                                                        lg_ordencab.cnumero 
                                                    ORDER BY
                                                        lg_ordencab.ffechadoc DESC");
                $sql->execute();

                if( $sql->rowCount() ) {
                    while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                        $docData[] = $row;
                    }
                }

                return $docData;
            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }

        private function listaCostos(){
            try {
                //code...
            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }

        private function listaAnio(){
            try {
                //code...
            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }

        private function listaProveedores(){
            try {
                //code...
            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }
    } 
?>