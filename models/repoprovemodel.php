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
                                                        id_regmov DESC
                                                    LIMIT 150");
                
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
            }else if ($campo == 'ffemision'){
                $valores = $this->listaAnioEmision();
            }else if ($campo == 'cCostos'){
                $valores = $this->listaCostos();
            }else if ($campo == 'cEntidad'){
                $valores = $this->listaProveedores();
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
                $sql = $this->db->connect()->query("SELECT
                                                        UPPER(CONCAT_WS(' ',tb_proyectos.ccodproy,tb_proyectos.cdesproy)) AS onumero,
                                                        tb_proyectos.nidreg AS id
                                                    FROM
                                                        lg_ordencab
                                                        INNER JOIN tb_proyectos ON lg_ordencab.ncodcos = tb_proyectos.nidreg 
                                                    GROUP BY
                                                        tb_proyectos.cdesproy 
                                                    ORDER BY
                                                        tb_proyectos.ccodproy ASC");
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

        private function listaAnioEmision(){
            try {
                $sql = $this->db->connect()->query("SELECT
                                                        lg_ordencab.cper AS onumero,
                                                        lg_ordencab.cper AS id
                                                    FROM
                                                        lg_ordencab 
                                                    WHERE
                                                        lg_ordencab.nflgactivo = 1 
                                                    GROUP BY
                                                        lg_ordencab.cper 
                                                    ORDER BY
                                                        lg_ordencab.cper DESC");
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

        private function listaProveedores(){
            try {
                $sql = $this->db->connect()->query("SELECT
                                                        UPPER(cm_entidad.crazonsoc) as onumero,
                                                        cm_entidad.id_centi AS id
                                                    FROM
                                                        lg_ordencab
                                                        INNER JOIN
                                                        cm_entidad
                                                        ON 
                                                            lg_ordencab.id_centi = cm_entidad.id_centi
                                                    GROUP BY
                                                        lg_ordencab.id_centi
                                                    ORDER BY
                                                        cm_entidad.crazonsoc ASC ");
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

        public function listaFiltradas($parametros){
            try {
                $emision  = $this->cambiarLista($parametros['filtro_emision']);
                $costos  = $this->cambiarLista($parametros['filtro_costos']);
                $entidad  = $this->cambiarLista($parametros['filtro_entidad']);

                return array("filas" => $this->mostrarTablaConFiltros($emision,$costos,$entidad),
                             "anios" => $parametros['filtro_emision'],
                             "ordenes" => $this->orden_total($emision,$costos,$entidad),
                             "compras" => $this->orden_tipo($emision,$costos,$entidad,37),
                             "servicios" => $this->orden_tipo($emision,$costos,$entidad,38),
                             "soles" => $this->totales($emision,$costos,$entidad,20),
                             "dolares" => $this->totales($emision,$costos,$entidad,21));

            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }

        private function mostrarTablaConFiltros($emision,$costos,$proveedor){
            try{
                $docData = [];

                $fecha = $emision == "" ? "LIKE '%'": "IN ($emision)";
                $costo = $costos == "" ? "LIKE '%'": "IN ($costos)";
                $entidad = $proveedor == "" ? "LIKE '%'": "IN ($proveedor)"; 
                
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
                                                        FORMAT(lg_ordencab.ntotal,2) as ntotal,
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
                                                        AND lg_ordencab.cper $fecha
                                                        AND tb_costusu.ncodproy $costo
                                                        AND cm_entidad.id_centi $entidad
                                                        AND ISNULL(lg_ordencab.ntipdoc)
                                                    ORDER BY
                                                        id_regmov DESC");

                $sql->execute(["user"=>$_SESSION['iduser']]);
                $rowCount = $sql->rowCount();

                if ($rowCount) {
                    $respuesta = true;
                    $i = 0;
                    
                    while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                        $docData[] = $row;
                    }
                }

                return $docData;

            }catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }

        private function orden_total($emision,$costos,$proveedor){
            try {
                $fecha = $emision == "" ? "LIKE '%'": "IN ($emision)";
                $costo = $costos == "" ? "LIKE '%'": "IN ($costos)";
                $entidad = $proveedor == "" ? "LIKE '%'": "IN ($proveedor)"; 

                $sql = $this->db->connect()->prepare("SELECT
                                                        COUNT( lg_ordencab.id_regmov ) AS ordenes 
                                                    FROM
                                                        tb_costusu
                                                        INNER JOIN lg_ordencab ON tb_costusu.ncodproy = lg_ordencab.ncodpry
                                                        INNER JOIN tb_proyectos ON lg_ordencab.ncodpry = tb_proyectos.nidreg
                                                        INNER JOIN cm_entidad ON lg_ordencab.id_centi = cm_entidad.id_centi 
                                                    WHERE
                                                        tb_costusu.id_cuser = :user 
                                                        AND tb_costusu.nflgactivo = 1 
                                                        AND lg_ordencab.cper $fecha
                                                        AND tb_costusu.ncodproy $costo
                                                        AND cm_entidad.id_centi $entidad
                                                        AND ISNULL(lg_ordencab.ntipdoc)");
                
                $sql->execute(["user"=>$_SESSION['iduser']]);
                $respuesta = $sql->fetchAll();

                return $respuesta[0]['ordenes'];

            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }

        private function orden_tipo($emision,$costos,$proveedor,$tipo){
            try {
                $fecha = $emision == "" ? "LIKE '%'": "IN ($emision)";
                $costo = $costos == "" ? "LIKE '%'": "IN ($costos)";
                $entidad = $proveedor == "" ? "LIKE '%'": "IN ($proveedor)"; 

                $sql = $this->db->connect()->prepare("SELECT
                                                        COUNT( lg_ordencab.id_regmov ) AS ordenes 
                                                    FROM
                                                        tb_costusu
                                                        INNER JOIN lg_ordencab ON tb_costusu.ncodproy = lg_ordencab.ncodpry
                                                        INNER JOIN tb_proyectos ON lg_ordencab.ncodpry = tb_proyectos.nidreg
                                                        INNER JOIN cm_entidad ON lg_ordencab.id_centi = cm_entidad.id_centi 
                                                    WHERE
                                                        tb_costusu.id_cuser = :user 
                                                        AND tb_costusu.nflgactivo = 1 
                                                        AND lg_ordencab.cper $fecha
                                                        AND tb_costusu.ncodproy $costo
                                                        AND cm_entidad.id_centi $entidad
                                                        AND ISNULL(lg_ordencab.ntipdoc)
                                                        AND lg_ordencab.ntipmov = :tipo");
                
                $sql->execute(["user"=>$_SESSION['iduser'],"tipo"=>$tipo]);
                $respuesta = $sql->fetchAll();

                return $respuesta[0]['ordenes'];

            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }
        
        private function totales($emision,$costos,$proveedor,$moneda){
            try {
                $fecha = $emision       == "" ? "LIKE '%'": "IN ($emision)";
                $costo = $costos        == "" ? "LIKE '%'": "IN ($costos)";
                $entidad = $proveedor   == "" ? "LIKE '%'": "IN ($proveedor)"; 

                $sql = $this->db->connect()->prepare("SELECT
                                                        FORMAT(SUM( lg_ordencab.ntotal ),2) AS totales 
                                                    FROM
                                                        tb_costusu
                                                        INNER JOIN lg_ordencab ON tb_costusu.ncodproy = lg_ordencab.ncodpry
                                                        INNER JOIN tb_proyectos ON lg_ordencab.ncodpry = tb_proyectos.nidreg
                                                        INNER JOIN cm_entidad ON lg_ordencab.id_centi = cm_entidad.id_centi 
                                                    WHERE
                                                        tb_costusu.id_cuser = :user 
                                                        AND tb_costusu.nflgactivo = 1
                                                        AND lg_ordencab.nflgactivo = 1  
                                                        AND lg_ordencab.cper $fecha
                                                        AND tb_costusu.ncodproy $costo
                                                        AND cm_entidad.id_centi $entidad
                                                        AND ISNULL(lg_ordencab.ntipdoc)
                                                        AND lg_ordencab.ncodmon = :moneda");
                
                $sql->execute(["user"=>$_SESSION['iduser'],"moneda"=>$moneda]);
                $respuesta = $sql->fetchAll();

                return $respuesta[0]['totales'];

            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }

        private function cambiarLista($items){
            $temp = array();
            
            $items = json_decode($items);

            foreach ($items as $item){
                array_push($temp,$item);
            }

            $string_from_array = implode(',',$temp);

            return $string_from_array;
        }
    } 
?>