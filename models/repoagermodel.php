<?php
    class RepoagerModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarClasesReporte() {
            try {
                $sql = $this->db->connect()->query("SELECT
                                                        tb_clase.ncodclase,
                                                        tb_clase.ncodgrupo,
                                                        UPPER(tb_grupo.cdescrip) AS cdescrip,
                                                        tb_grupo.ccodcata,
                                                        COUNT( tb_clase.ncodgrupo ) AS repeticiones
                                                    FROM
                                                        tb_clase
                                                        INNER JOIN tb_grupo ON tb_clase.ncodgrupo = tb_grupo.ncodgrupo
                                                    WHERE
                                                        tb_clase.nflgactivo = 1
                                                    GROUP BY tb_grupo.cdescrip
                                                    HAVING COUNT( tb_clase.ncodgrupo ) > 0
                                                    ORDER BY tb_grupo.cdescrip");
                $sql->execute();
                $rowCount = $sql->rowCount();
                $salida = '<option value="0">Todos</option>';

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()) {
                        $salida .= '<option value ="'.$rs['ncodgrupo'].'">'.$rs['cdescrip'].'</option>';
                    }  
                }

                return $salida;
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function listarTipos($id) {
            try {
                $sql=$this->db->connect()->prepare("SELECT
                                                    tb_clase.ncodclase, 
                                                    tb_clase.ccodcata, 
                                                    tb_clase.cdescrip, 
                                                    tb_clase.ncodgrupo
                                                FROM
                                                    tb_clase
                                                WHERE
                                                    tb_clase.nflgactivo = 1
                                                    AND tb_clase.ncodgrupo = :id");
                
                $sql->execute(['id'=>$id]);
                $salida = '<option value="0">Todos</option>';
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()) {
                        $salida .= '<option value ="'.$rs['ncodclase'].'">'.$rs['cdescrip'].'</option>';
                    }  
                }

                return $salida;

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function mesActual() {
            $salida = null;
            $mes  = ['Todos','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Setiembre','Octubre','Noviembre','Diciembre'];

            foreach ($mes as $key =>$m) {
                $salida .= '<option value = '.$key.'>'.$m.'</option>';
            }

            return $salida;
        }

        public function tablaFamilias($grupo,$clase){
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                        UPPER(tb_familia.cdescrip) AS name,
                                                        SUM(lg_ordendet.ncanti) AS cantidad,
                                                        cm_producto.ngrupo,
                                                        cm_producto.nclase,
                                                        cm_producto.nfam,
                                                    IF
                                                        ( lg_ordencab.ncodmon = 20, SUM( lg_ordendet.ntotal )*1, SUM( lg_ordendet.ntotal )* lg_ordencab.ntcambio ) AS total
                                                    FROM
                                                        lg_ordendet
                                                        INNER JOIN cm_producto ON lg_ordendet.id_cprod = cm_producto.id_cprod
                                                        INNER JOIN tb_grupo ON cm_producto.ngrupo = tb_grupo.ncodgrupo
                                                        INNER JOIN tb_familia ON cm_producto.nfam = tb_familia.ncodfamilia
                                                        INNER JOIN lg_ordencab ON lg_ordendet.id_regmov = lg_ordencab.id_regmov 
                                                    WHERE
                                                        cm_producto.ngrupo = :grupo 
                                                        AND cm_producto.nclase = :clase
                                                        AND lg_ordendet.nestado = 1 
                                                    GROUP BY
                                                        cm_producto.nfam");
                $sql->execute(["grupo"=>$grupo,"clase"=>$clase]);

                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    $docData = array();
                    
                    while($row=$sql->fetch(PDO::FETCH_ASSOC)){
                        array_push( $docData,array("name"=>$row['name'],
                                                    "y"=>$row['cantidad'],
                                                    "total"=>$row['total'],
                                                    "grupo"=>$row['ngrupo'],
                                                    "clase"=>$row['nclase'],
                                                    "familia"=>$row['nfam']));
                    }
                }

                return $docData;

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function tablaItems($grupo,$clase,$familia){
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                        SUM(lg_ordendet.ncanti) AS cantidad,
                                                        FORMAT(IF
                                                        ( lg_ordencab.ncodmon = 20, SUM( lg_ordendet.ntotal )*1, SUM( lg_ordendet.ntotal )* lg_ordencab.ntcambio ),2) AS total,
                                                        lg_ordendet.id_cprod,
                                                        cm_producto.ccodprod,
                                                        UPPER(cm_producto.cdesprod) AS cdesprod,
                                                        cm_producto.ngrupo,
                                                        cm_producto.nclase,
                                                        cm_producto.nfam 
                                                    FROM
                                                        lg_ordendet
                                                        INNER JOIN cm_producto ON lg_ordendet.id_cprod = cm_producto.id_cprod
                                                        INNER JOIN lg_ordencab ON lg_ordendet.id_regmov = lg_ordencab.id_regmov 
                                                    WHERE
                                                        lg_ordendet.nestado = 1 
                                                        AND cm_producto.ngrupo =:grupo 
                                                        AND cm_producto.nclase =:clase 
                                                        AND cm_producto.nfam =:familia
                                                    GROUP BY cm_producto.id_cprod");

                $sql->execute(["grupo"=>$grupo,"clase"=>$clase,"familia"=>$familia]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    $docData = array();
                    
                    while($row=$sql->fetch(PDO::FETCH_ASSOC)){
                        array_push( $docData,array("name"=>$row['cdesprod'],
                                                    "y"=>$row['cantidad'],
                                                    "total"=>$row['total'],
                                                    "grupo"=>$row['ngrupo'],
                                                    "clase"=>$row['nclase'],
                                                    "familia"=>$row['nfam'],
                                                    "producto"=>$row['id_cprod']));
                    }
                }

                return array("datos"=>$docData);
                                    
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function dibujarLineas($cc,$anio,$producto){
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                        tb_meses.mes,
                                                        (
                                                        SELECT
                                                        IF
                                                            ( SUM( lg_ordendet.ncanti ) > 0, SUM( lg_ordendet.ncanti ), 0 ) 
                                                        FROM
                                                            lg_ordendet 
                                                        WHERE
                                                            MONTH ( lg_ordendet.fregsys ) = tb_meses.idreg 
                                                            AND lg_ordendet.id_cprod = :producto 
                                                            AND (
                                                                lg_ordendet.nEstadoReg != 105 
                                                            OR ISNULL( lg_ordendet.nEstadoReg )) 
                                                        ) AS suma 
                                                    FROM
                                                        tb_meses");
                

                $sql->execute(["producto"=>$producto]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    $docData = array();
                    
                    while($row=$sql->fetch(PDO::FETCH_ASSOC)){
                        array_push($docData,$row['suma']);
                    }
                }

                return array("lineas"=>$docData,
                             "barras"=>$this->barrasTotales($grupo,$clase,$familia,$producto));

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function barrasTotales($cc,$an,$pr){
            try {
                $costo = $cc == 0 ? '%' : $cc;

                $sql = $this->db->connect()->prepare("SELECT
                                                        tb_meses.mes,
                                                        (
                                                        SELECT
                                                         lg_ordendet.ntotal
                                                        
                                                        FROM
                                                            lg_ordendet 
                                                        WHERE
                                                            lg_ordendet.ncodcos LIKE :costos 
                                                            AND YEAR ( lg_ordendet.fregsys ) = :anio 
                                                            AND MONTH ( lg_ordendet.fregsys ) = tb_meses.idreg 
                                                            AND lg_ordendet.id_cprod = :producto
                                                            
                                                        ) AS suma 
                                                    FROM
                                                        tb_meses");
                

                $sql->execute(["producto"=>$pr,"anio"=>$an,"costos"=>$costo]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    $docData = array();
                    
                    while($row=$sql->fetch(PDO::FETCH_ASSOC)){
                        array_push($docData,$row['suma']);
                    }
                }

                return $docData;

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function consultarGrupos($cc,$anio,$mm) {
            try {

                $costo = $cc == 0 ? '%' : $cc;
                $mes = $mm == 0 ? '%' : $mm;

                $sql = $this->db->connect()->prepare("SELECT
                                                    lg_ordendet.id_cprod,
                                                    UPPER( tb_grupo.cdescrip ) AS name,
                                                    UPPER( tb_clase.cdescrip ) AS clase,
                                                    UPPER( tb_familia.cdescrip ) AS familia,
                                                    lg_ordendet.ncodcos,
                                                    lg_ordendet.nEstadoReg,
                                                    lg_ordendet.nflgactivo,
                                                    SUM( lg_ordendet.ncanti ) AS cantidad,
                                                    lg_ordendet.fregsys,
                                                    cm_producto.nclase,
                                                    cm_producto.ngrupo 
                                                FROM
                                                    lg_ordendet
                                                    LEFT JOIN cm_producto ON lg_ordendet.id_cprod = cm_producto.id_cprod
                                                    LEFT JOIN tb_familia ON cm_producto.nfam = tb_familia.ncodfamilia
                                                    LEFT JOIN tb_grupo ON cm_producto.ngrupo = tb_grupo.ncodgrupo
                                                    LEFT JOIN tb_clase ON cm_producto.nclase = tb_clase.ncodclase 
                                                WHERE
                                                    lg_ordendet.id_orden <> 0
                                                    AND lg_ordendet.nEstadoReg <> 105 
                                                    AND lg_ordendet.ncodcos LIKE :costos 
                                                    AND YEAR ( lg_ordendet.fregsys ) = :anio
                                                    AND MONTH ( lg_ordendet.fregsys ) LIKE :mes
                                                GROUP BY
                                                    tb_grupo.cdescrip 
                                                ORDER BY
                                                    tb_grupo.cdescrip ASC");

                $sql->execute(["costos"=>$costo,"mes"=>$mes,"anio"=>$anio]);

                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    $docData = array();
                    
                    while($row=$sql->fetch(PDO::FETCH_ASSOC)){
                        array_push( $docData,array("name"=>$row['name'],
                                                    "y"=>$row['cantidad'],
                                                    "grupo"=>$row['name'],
                                                    "clase"=>$row['clase'],
                                                    "familia"=>$row['familia'],
                                                    "cg"=>$row['ngrupo']));
                    }
                }

                return array("grupo"=>$docData);
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function consultarClases($cc,$gr,$anio,$mm) {
            try {

                $costo = $cc == 0 ? '%' : $cc;
                $grupo = $gr == 0 ? '%' : $gr;
                $mes = $mm == 0 ? '%' : $mm;

                $sql = $this->db->connect()->prepare("SELECT
                                                        lg_ordendet.id_cprod,
                                                        UPPER( tb_grupo.cdescrip ) AS grupo,
                                                        UPPER( tb_clase.cdescrip ) AS name,
                                                        UPPER( tb_familia.cdescrip ) AS familia,
                                                        lg_ordendet.ncodcos,
                                                        lg_ordendet.nEstadoReg,
                                                        lg_ordendet.nflgactivo,
                                                        SUM( lg_ordendet.ncanti ) AS cantidad,
                                                        FORMAT(SUM( lg_ordendet.nunitario ),2) as total,
                                                        FORMAT(SUM( lg_ordendet.ncanti ),2) AS conteo,
                                                        lg_ordendet.fregsys,
                                                        cm_producto.nclase,
                                                        cm_producto.nfam 
                                                    FROM
                                                        lg_ordendet
                                                        LEFT JOIN cm_producto ON lg_ordendet.id_cprod = cm_producto.id_cprod
                                                        LEFT JOIN tb_familia ON cm_producto.nfam = tb_familia.ncodfamilia
                                                        LEFT JOIN tb_grupo ON cm_producto.ngrupo = tb_grupo.ncodgrupo
                                                        LEFT JOIN tb_clase ON cm_producto.nclase = tb_clase.ncodclase 
                                                    WHERE
                                                        lg_ordendet.id_orden <> 0
                                                        AND lg_ordendet.nEstadoReg <> 105 
                                                        AND lg_ordendet.ncodcos LIKE :costos 
                                                        AND cm_producto.ngrupo LIKE :grupo 
                                                        AND YEAR ( lg_ordendet.fregsys ) = :anio
                                                        AND MONTH ( lg_ordendet.fregsys ) LIKE :mes
                                                    GROUP BY
                                                        tb_clase.cdescrip 
                                                    ORDER BY
                                                        tb_clase.cdescrip ASC");

                $sql->execute(["costos"=>$costo,"grupo"=>$grupo,"mes"=>$mes,"anio"=>$anio]);

                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    $docData = array();
                    
                    while($row=$sql->fetch(PDO::FETCH_ASSOC)){
                        array_push( $docData,array("name"=>$row['name'],
                                                    "y"=>$row['cantidad'],
                                                    "grupo"=>$row['grupo'],
                                                    "familia"=>$row['familia'],
                                                    "cc"=>$row['nclase'],
                                                    "cantidad"=>$row['cantidad'],
                                                    "total"=>$row['total'],
                                                    "conteo"=>$row['conteo'],
                                                    "cf"=>$row['nfam']));
                    }
                }

                return array("clase"=>$docData);
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function consultarFamilias($cc,$gr,$cl,$anio,$mm){
            try {

                $costo = $cc == 0 ? '%' : $cc;
                $grupo = $gr == 0 ? '%' : $gr;
                $clase = $cl == 0 ? '%' : $cl;
                $mes = $mm == 0 ? '%' : $mm;


                $sql = $this->db->connect()->prepare("SELECT
                                                        lg_ordendet.id_cprod,
                                                        UPPER( tb_grupo.cdescrip ) AS grupo,
                                                        UPPER( tb_clase.cdescrip ) AS clase,
                                                        UPPER( tb_familia.cdescrip ) AS name,
                                                        lg_ordendet.ncodcos,
                                                        lg_ordendet.nEstadoReg,
                                                        lg_ordendet.nflgactivo,
                                                        SUM( lg_ordendet.ncanti ) AS cantidad,
                                                        FORMAT(SUM( lg_ordendet.nunitario ),2) as total,
                                                        FORMAT(SUM( lg_ordendet.ncanti ),2) AS conteo,
                                                        lg_ordendet.fregsys,
                                                        cm_producto.nfam 
                                                    FROM
                                                        lg_ordendet
                                                        LEFT JOIN cm_producto ON lg_ordendet.id_cprod = cm_producto.id_cprod
                                                        LEFT JOIN tb_familia ON cm_producto.nfam = tb_familia.ncodfamilia
                                                        LEFT JOIN tb_grupo ON cm_producto.ngrupo = tb_grupo.ncodgrupo
                                                        LEFT JOIN tb_clase ON cm_producto.nclase = tb_clase.ncodclase 
                                                    WHERE
                                                        lg_ordendet.id_orden <> 0
                                                        AND lg_ordendet.nEstadoReg <> 105 
                                                        AND lg_ordendet.ncodcos LIKE :costo 
                                                        AND cm_producto.ngrupo LIKE :grupo
                                                        AND cm_producto.nclase LIKE :clase
                                                        AND YEAR ( lg_ordendet.fregsys ) = :anio 
                                                        AND MONTH ( lg_ordendet.fregsys ) LIKE :mes
                                                        AND tb_familia.nflgactivo = 1 
                                                    GROUP BY
                                                        tb_familia.cdescrip
                                                    ORDER BY
                                                        tb_familia.cdescrip ASC");

                $sql->execute(["costo"=>$costo,"grupo"=>$grupo,"clase"=>$clase,"mes"=>$mes,"anio"=>$anio]);

                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    $docData = array();
                    
                    while($row=$sql->fetch(PDO::FETCH_ASSOC)){
                        array_push( $docData,array("name"=>$row['name'],
                                                    "y"=>$row['cantidad'],
                                                    "grupo"=>$row['grupo'],
                                                    "clase"=>$row['clase'],
                                                    "cf"=>$row['nfam'],
                                                    "total"=>$row['total'],
                                                    "conteo"=>$row['conteo']));
                    }
                }

                return array("familias"=>$docData);
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function consultarItems($cc,$fa,$anio,$mm) {
            $costo = $cc == 0 ? '%' : $cc;
            $familia = $fa == 0 ? '%' : $fa;
            $mes = $mm == 0 ? '%' : $mm;

            $sql = $this->db->connect()->prepare("SELECT
                                                    cm_producto.ngrupo,
                                                    cm_producto.nclase,
                                                    cm_producto.nfam,
                                                    cm_producto.ccodprod,
                                                    UPPER( cm_producto.cdesprod ) AS name,
                                                    SUM(lg_ordendet.ntotal) AS total,
                                                    COUNT(lg_ordendet.ncanti) AS cantidad,
                                                    lg_ordendet.nunitario,
                                                    lg_ordendet.ncodcos,
                                                    lg_ordendet.id_cprod,
                                                    lg_ordendet.fregsys 
                                                FROM
                                                    lg_ordendet
                                                    INNER JOIN cm_producto ON lg_ordendet.id_cprod = cm_producto.id_cprod 
                                                WHERE
                                                    lg_ordendet.ncodcos LIKE :costo 
                                                    AND lg_ordendet.nEstadoReg <> 105 
                                                    AND cm_producto.nfam LIKE :familia 
                                                    AND YEAR ( lg_ordendet.fregsys ) = :anio 
                                                    AND MONTH ( lg_ordendet.fregsys ) LIKE :mes
                                                GROUP BY cm_producto.ccodprod
                                                ORDER BY
                                                    cm_producto.cdesprod ASC");
            
            $sql->execute(["costo"=>$costo,"familia"=>$familia,"mes"=>$mes,"anio"=>$anio]);

            $rowCount = $sql->rowCount();

            if ($rowCount > 0) {
                $docData = array();
                
                while($row=$sql->fetch(PDO::FETCH_ASSOC)){
                    array_push( $docData,array("name"=>$row['name'],
                                                "y"=>$row['cantidad'],
                                                "cf"=>$row['nfam'],
                                                "total"=>$row['total'],
                                                "producto"=>$row['id_cprod']));
                }
            }

            return array("items"=>$docData);
        }
    }
?>