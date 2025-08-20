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
                $sql = $this->db->connect()->prepare("SELECT
                                                    tb_costusu.ncodproy,
                                                    UPPER(
                                                    CONCAT_WS('  ', tb_proyectos.ccodproy, tb_proyectos.cdesproy )) AS onumero,
                                                    tb_proyectos.nidreg AS id
                                                FROM
                                                    tb_costusu
                                                    INNER JOIN tb_proyectos ON tb_costusu.ncodproy = tb_proyectos.nidreg 
                                                WHERE
                                                    tb_costusu.id_cuser = :user 
                                                    AND tb_costusu.nflgactivo = 1 
                                                ORDER BY
                                                    tb_proyectos.ccodproy ASC");
                $sql->execute(["user"=>$_SESSION['iduser']]);

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

                return array("filas"     => $this->mostrarTablaConFiltros($emision,$costos,$entidad),
                             "anios"     => $parametros['filtro_emision'],
                             "ordenes"   => $this->orden_total($emision,$costos,$entidad),
                             "compras"   => $this->orden_tipo($emision,$costos,$entidad,37),
                             "servicios" => $this->orden_tipo($emision,$costos,$entidad,38),
                             "soles"     => $this->totales($emision,$costos,$entidad,20),
                             "dolares"   => $this->totales($emision,$costos,$entidad,21),
                             "valores"   => $this->valoresBarras($emision,$costos,$entidad));

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
                                                        AND lg_ordencab.cper $fecha
                                                        AND tb_costusu.ncodproy $costo
                                                        AND cm_entidad.id_centi $entidad
                                                    ORDER BY
                                                        id_regmov DESC");

                $sql->execute(["user"=>$_SESSION['iduser']]);
                $rowCount = $sql->rowCount();

                if ($rowCount) {
                    $respuesta = true;
                    $i = 0;
                    
                    $rows = $sql->fetchAll(PDO::FETCH_ASSOC);
                    

                    
                    foreach($rows as $row) {
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

        private function valoresBarras($emision,$costos,$proveedor){
            try {
                $name = [];
                $data = [];

                $fecha      = $emision       == "" ? "LIKE '%'": "IN ($emision)";
                $costo      = $costos        == "" ? "LIKE '%'": "IN ($costos)";
                $entidad    = $proveedor     == "" ? "LIKE '%'": "IN ($proveedor)"; 

                $sql = $this->db->connect()->prepare("SELECT
                                                        lg_ordencab.cper AS per,
                                                        (
                                                        SELECT
                                                            COUNT( lg_ordencab.id_regmov ) 
                                                        FROM
                                                            lg_ordencab lg_ordencab 
                                                        WHERE
                                                            lg_ordencab.cmes = '01' 
                                                            AND lg_ordencab.cper = per 
                                                            AND lg_ordencab.ncodcos $costo 
                                                            AND lg_ordencab.id_centi $entidad 
                                                        ) AS ene,
                                                        (
                                                        SELECT
                                                            COUNT( lg_ordencab.id_regmov ) 
                                                        FROM
                                                            lg_ordencab lg_ordencab 
                                                        WHERE
                                                            lg_ordencab.cmes = '02' 
                                                            AND lg_ordencab.cper = per 
                                                            AND lg_ordencab.ncodcos $costo 
                                                            AND lg_ordencab.id_centi $entidad 
                                                        ) AS feb,
                                                        (
                                                        SELECT
                                                            COUNT( lg_ordencab.id_regmov ) 
                                                        FROM
                                                            lg_ordencab lg_ordencab 
                                                        WHERE
                                                            lg_ordencab.cmes = '03' 
                                                            AND lg_ordencab.cper = per 
                                                            AND lg_ordencab.ncodcos $costo 
                                                            AND lg_ordencab.id_centi $entidad 
                                                        ) AS mar,
                                                        (
                                                        SELECT
                                                            COUNT( lg_ordencab.id_regmov ) 
                                                        FROM
                                                            lg_ordencab lg_ordencab 
                                                        WHERE
                                                            lg_ordencab.cmes = '04' 
                                                            AND lg_ordencab.cper = per 
                                                            AND lg_ordencab.ncodcos $costo 
                                                            AND lg_ordencab.id_centi $entidad 
                                                        ) AS abr,
                                                        (
                                                        SELECT
                                                            COUNT( lg_ordencab.id_regmov ) 
                                                        FROM
                                                            lg_ordencab lg_ordencab 
                                                        WHERE
                                                            lg_ordencab.cmes = '05' 
                                                            AND lg_ordencab.cper = per 
                                                            AND lg_ordencab.ncodcos $costo 
                                                            AND lg_ordencab.id_centi $entidad  
                                                        ) AS may,
                                                        (
                                                        SELECT
                                                            COUNT( lg_ordencab.id_regmov ) 
                                                        FROM
                                                            lg_ordencab lg_ordencab 
                                                        WHERE
                                                            lg_ordencab.cmes = '06' 
                                                            AND lg_ordencab.cper = per 
                                                            AND lg_ordencab.ncodcos $costo 
                                                            AND lg_ordencab.id_centi $entidad  
                                                        ) AS jun,
                                                        (
                                                        SELECT
                                                            COUNT( lg_ordencab.id_regmov ) 
                                                        FROM
                                                            lg_ordencab lg_ordencab 
                                                        WHERE
                                                            lg_ordencab.cmes = '07' 
                                                            AND lg_ordencab.cper = per 
                                                            AND lg_ordencab.ncodcos $costo 
                                                            AND lg_ordencab.id_centi $entidad  
                                                        ) AS jul,
                                                        (
                                                        SELECT
                                                            COUNT( lg_ordencab.id_regmov ) 
                                                        FROM
                                                            lg_ordencab lg_ordencab 
                                                        WHERE
                                                            lg_ordencab.cmes = '08' 
                                                            AND lg_ordencab.cper = per 
                                                            AND lg_ordencab.ncodcos $costo 
                                                            AND lg_ordencab.id_centi $entidad 
                                                        ) AS ago,
                                                        (
                                                        SELECT
                                                            COUNT( lg_ordencab.id_regmov ) 
                                                        FROM
                                                            lg_ordencab lg_ordencab 
                                                        WHERE
                                                            lg_ordencab.cmes = '09' 
                                                            AND lg_ordencab.cper = per 
                                                            AND lg_ordencab.ncodcos $costo 
                                                            AND lg_ordencab.id_centi $entidad 
                                                        ) AS sep,
                                                        (
                                                        SELECT
                                                            COUNT( lg_ordencab.id_regmov ) 
                                                        FROM
                                                            lg_ordencab lg_ordencab 
                                                        WHERE
                                                            lg_ordencab.cmes = '10' 
                                                            AND lg_ordencab.cper = per 
                                                            AND lg_ordencab.ncodcos $costo 
                                                            AND lg_ordencab.id_centi $entidad  
                                                        ) AS oct,
                                                        (
                                                        SELECT
                                                            COUNT( lg_ordencab.id_regmov ) 
                                                        FROM
                                                            lg_ordencab lg_ordencab 
                                                        WHERE
                                                            lg_ordencab.cmes = '11' 
                                                            AND lg_ordencab.cper = per 
                                                            AND lg_ordencab.ncodcos $costo 
                                                            AND lg_ordencab.id_centi $entidad  
                                                        ) AS nov,
                                                        (
                                                        SELECT
                                                            COUNT( lg_ordencab.id_regmov ) 
                                                        FROM
                                                            lg_ordencab lg_ordencab 
                                                        WHERE
                                                            lg_ordencab.cmes = '12' 
                                                            AND lg_ordencab.cper = per 
                                                            AND lg_ordencab.ncodcos $costo 
                                                            AND lg_ordencab.id_centi $entidad  
                                                        ) AS dic 
                                                    FROM
                                                        tb_costusu
                                                        LEFT JOIN lg_ordencab ON tb_costusu.ncodproy = lg_ordencab.ncodpry 
                                                    WHERE
                                                        tb_costusu.id_cuser = :user 
                                                        AND tb_costusu.nflgactivo = 1 
                                                        AND ISNULL( lg_ordencab.ntipdoc ) 
                                                        AND lg_ordencab.cper $fecha 
                                                        AND lg_ordencab.ncodcos $costo 
                                                        AND lg_ordencab.id_centi $entidad  
                                                    GROUP BY
                                                        lg_ordencab.cper");

            $sql->execute(["user"=>$_SESSION['iduser']]);

            $valores = array();
            $valor = array();

            if( $sql->rowCount() ) {
                while ($rs = $sql->fetch()) {
                    $valor['nombre'] = $rs['per'];
                    $valor['series'] = [$rs['ene'],$rs['feb'],$rs['mar'],$rs['abr'],$rs['may'],$rs['jun'],$rs['jul'],$rs['ago'],$rs['sep'],$rs['oct'],$rs['nov'],$rs['dic']];

                    array_push($valores,$valor);
                }
            }

            return array($valores);

            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }

        public function crearExcelProveedores($detalles){
            require_once('public/PHPExcel/PHPExcel.php');
            try {
                $objPHPExcel = new PHPExcel();
                
                $objPHPExcel->getProperties()
                    ->setCreator("Sical")
                    ->setLastModifiedBy("Sical")
                    ->setTitle("Reporte Proveedores")
                    ->setSubject("Template excel")
                    ->setDescription("Cargo Plan")
                    ->setKeywords("Template excel");

                $cuerpo = array(
                    'font'  => array(
                    'bold'  => false,
                    'size'  => 7,
                ));

                $objWorkSheet = $objPHPExcel->createSheet(1);

                $objPHPExcel->setActiveSheetIndex(0);
                $objPHPExcel->getActiveSheet()->setTitle("Reporte Proveedores");

                $objPHPExcel->getActiveSheet()
                            ->getStyle('A2:N2')
                            ->getFill()
                            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB('BFCDDB');

                $objPHPExcel->getActiveSheet()->mergeCells('A1:H1');
                $objPHPExcel->getActiveSheet()->setCellValue('A1','REPORTE DE PROVEDORES');

                $objPHPExcel->getActiveSheet()->getStyle('A1:AH2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('A1:AH2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(60);

                $objPHPExcel->getActiveSheet()->getColumnDimension("B")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("C")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("D")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("E")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("F")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("G")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("H")->setAutoSize(true);
                


                $objPHPExcel->getActiveSheet()->getStyle('A1:AH2')->getAlignment()->setWrapText(true);

                $objPHPExcel->getActiveSheet()->setCellValue('A2','Orden'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('B2','Emision'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('C2','Descripcion'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('D2','Centro Costos'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('E2','Area'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('F2','Proveedor'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('G2','Precio Soles'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('H2','Precio Dolares'); // esto cambia

                $datos = json_decode($detalles);
                $fila = 3;
                $color_estado1 = "#FFD700";
                $color2 = "#FFD700";
                $color3 = "#FFD700";
                $color4 = "#FFD700";

                forEach($datos AS $dato){
                    
                    /*if ($dato->estado1 == 'Pendiente'){
                        $color_estado1 = "#FFD700";
                    }else if($dato->estado1 == 'Realizado'){
                        $color_estado1 = "#36DC2E";
                    }else if($dato->estado1 == 'Vencido'){
                        $color_estado1 = "#DC362E";
                    }

                    $color1 = array(
                        'fill' => array(
                            'type' => PHPExcel_Style_Fill::FILL_SOLID,
                            'startcolor' => array(
                                'argb' => $color_estado1,
                            ),
                            'endcolor' => array(
                                'argb' => $color_estado1,
                            ),
                        ),
                    );*/


                    $objPHPExcel->getActiveSheet()->setCellValue('A'.$fila,$dato->numero);
                    $objPHPExcel->getActiveSheet()->setCellValue('B'.$fila,$dato->emision);
                    $objPHPExcel->getActiveSheet()->setCellValue('C'.$fila,$dato->descripcion);
                    $objPHPExcel->getActiveSheet()->setCellValue('D'.$fila,$dato->costos);
                    $objPHPExcel->getActiveSheet()->setCellValue('E'.$fila,$dato->area);
                    $objPHPExcel->getActiveSheet()->setCellValue('F'.$fila,$dato->proveedor);
                    $objPHPExcel->getActiveSheet()->setCellValue('G'.$fila,$dato->soles);
                    $objPHPExcel->getActiveSheet()->setCellValue('H'.$fila,$dato->dolares);

                    $fila++;
                }

                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');
                $objWriter->save('public/documentos/reportes/repoprove.xlsx');

                return array("documento"=>'public/documentos/reportes/repoprove.xlsx');

                exit();

                
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }
    } 
?>