<?php
    class CombustibleModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listaConsumosCombustibles($t,$c,$a){
            try {

                $docData = [];

                $costo = $c != -1 ? $c : "%";
                $tipo  = $t != -1 ? $t : "%";

                $sql = $this->db->connect()->prepare("SELECT
                                                    alm_combustible.idreg,
                                                    DATE_FORMAT(alm_combustible.fregistro,'%d/%m/%Y') AS fregistro,
                                                    alm_combustible.idalm,
                                                    alm_combustible.idtipo,
                                                    alm_combustible.idprod,
                                                    FORMAT(alm_combustible.ncantidad,2) AS ncantidad,
                                                    UPPER(alm_combustible.tobseritem) AS tobseritem,
                                                    UPPER(alm_combustible.cdocumento) AS cdocumento,
                                                    alm_combustible.idusuario,
                                                    alm_combustible.idproyecto,
                                                    alm_combustible.cguia,
                                                    UPPER(alm_combustible.tobserdocum) AS tobserdocum,
                                                    alm_combustible.nidref,
                                                    alm_combustible.idarea,
                                                    UPPER( tb_almacen.cdesalm ) AS cdesalm,
                                                    cm_producto.ccodprod,
                                                    cm_producto.cdesprod,
                                                    tb_unimed.cabrevia,
                                                    tb_proyectos.ccodproy,
                                                    UPPER( tb_proyectos.cdesproy ) AS desproy,
                                                    UPPER(tb_equipmtto.cregistro) AS cregistro,
                                                    tb_equipmtto.cdescripcion,
                                                    UPPER(tb_area.cdesarea) AS cdesarea,
                                                    MONTH(alm_combustible.fregistro) AS mes
                                                FROM
                                                    alm_combustible
                                                    LEFT JOIN tb_almacen ON alm_combustible.idalm = tb_almacen.ncodalm
                                                    LEFT JOIN cm_producto ON alm_combustible.idprod = cm_producto.id_cprod
                                                    LEFT JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed
                                                    LEFT JOIN tb_proyectos ON alm_combustible.idproyecto = tb_proyectos.nidreg
                                                    LEFT JOIN tb_equipmtto ON alm_combustible.nidref = tb_equipmtto.idreg
                                                    LEFT JOIN tb_area ON alm_combustible.idarea = tb_area.ncodarea 
                                                WHERE
                                                    alm_combustible.nflgactivo = 1
                                                    AND YEAR(alm_combustible.fregistro) = :anio
                                                    AND alm_combustible.idtipo LIKE :tipo
                                                    AND alm_combustible.idproyecto LIKE :costo");

                $sql->execute(["costo" =>$costo,"tipo" =>$tipo,"anio"=>$a]);
                $rowCount = $sql->rowCount();
                
                if ($rowCount) {
                    $respuesta = true;
                    $i = 0;
                    
                    while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                        $docData[] = $row;
                    }
                }


                return array("datos"=>$docData,"usuarios"=>$this->usuariosAquarius());

            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function consultarCodigo($codigo){
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                            cm_producto.ccodprod,
                                                            cm_producto.id_cprod,
                                                            UPPER(cm_producto.cdesprod) AS cdesprod,
                                                            tb_unimed.cdesmed 
                                                        FROM
                                                            cm_producto
                                                            INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed
                                                        WHERE
                                                            cm_producto.ccodprod =:codigo");
                
                $sql->execute(['codigo' => $codigo]);

                $rowCount = $sql->rowCount();
                
                if ($rowCount) {
                    while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                        $docData[] = $row;
                    }
                }

                return array("datos"=>$docData);
                
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function buscarDocumento($doc) {
            $registrado = false;

            $url = "http://sicalsepcon.net/api/activesapi.php?documento=".$doc;
            
            $api = file_get_contents($url);

            $datos =  json_decode($api);
            $nreg = count($datos);

            $registrado = $nreg > 0 ? true: false;

            return array("datos" => $datos, "registrado"=>$registrado);
        }

        public function registrarCombustible($datos){
            try {
                $sql=$this->db->connect()->prepare("INSERT INTO alm_combustible 
                                                    SET alm_combustible.fregistro=:fecha,
                                                        alm_combustible.idalm=:idalmacen,
                                                        alm_combustible.idtipo=:tipo,
                                                        alm_combustible.idprod=:producto,
                                                        alm_combustible.ncantidad=:cantidad,
                                                        alm_combustible.tobseritem=:obseritem,
                                                        alm_combustible.cdocumento=:nrodoc,
                                                        alm_combustible.idusuario=:usuario,
                                                        alm_combustible.idproyecto=:proyecto,
                                                        alm_combustible.cguia=:guia,
                                                        alm_combustible.tobserdocum=:obserdoc,
                                                        alm_combustible.nidref=:referencia,
                                                        alm_combustible.idarea=:area");
                $sql->execute([
                    "fecha"=>$datos['fechaRegistro'],
                    "idalmacen"=>$datos['almacen'],
                    "tipo"=>$datos['tipo'],
                    "producto"=>$datos['codigo_producto'],
                    "cantidad"=>$datos['cantidad'],
                    "obseritem"=>strtoupper($datos['observacionesItem']),
                    "nrodoc"=>$datos['documento'],
                    "usuario"=>$datos['usuario'],
                    "proyecto"=>$datos['proyecto'],
                    "guia"=>$datos['guia'],
                    "obserdoc"=>strtoupper($datos['observacionesDocumento']),
                    "referencia"=>$datos['referencia'],
                    "area"=>$datos['area']]);

                return array("mensaje"=>'Consumo registrado');

            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function tipoCombustible(){
            try {
                $docData = [];

                $sql=$this->db->connect()->query("SELECT
                                                    cm_producto.ccodprod,
                                                    cm_producto.cdesprod,
                                                    cm_producto.id_cprod 
                                                FROM
                                                    cm_producto 
                                                WHERE
                                                    cm_producto.ngrupo = 8 
                                                    AND cm_producto.nclase = 60 
                                                    AND cm_producto.nfam = 234 
                                                    AND cm_producto.flgActivo = 1");

                while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                    $docData[] = $row;
                }

                return array("datos"=>$docData);

            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function generarReporte($item){
            $stock_inicial = $this->stock_inicial($item);
            $ingreso_mes_actual = $this->ingreso_mes_actual($item);
            $consumo_mes_actual = $this->consumo_mes_actual($item);

            return array("stock_inicial"=>$stock_inicial,
                         "ingreso_mes_actual"=>$ingreso_mes_actual,
                         "consumo_mes_actual"=>$consumo_mes_actual);
        }

        private function stock_inicial($item){
            try {
                $sql=$this->db->connect()->prepare("SELECT
                                                alm_recepdet.id_cprod,
                                                alm_recepcab.cper,
                                                alm_recepcab.cmes,
                                                alm_recepcab.ncodpry,
                                                SUM(alm_recepdet.ncantidad) AS ingresos_mes_anterior
                                            FROM
                                                alm_recepdet
                                                INNER JOIN alm_recepcab ON alm_recepdet.id_regalm = alm_recepcab.id_regalm 
                                            WHERE
                                                alm_recepdet.nflgactivo = 1 
                                                AND alm_recepdet.id_cprod =:item
                                                AND alm_recepcab.cper = IF ( MONTH(NOW()) = 1,YEAR(NOW())-1,YEAR(NOW()))
                                                AND alm_recepcab.cmes = IF ( MONTH(NOW()) = 1,12,MONTH(NOW())-1)");
                $sql->execute(["item"=>$item]);

                $result = $sql->fetchAll();

                return $result[0]['ingresos_mes_anterior'];


            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function ingreso_mes_actual($item){
            try {
                $sql=$this->db->connect()->prepare("SELECT
                                                alm_recepdet.id_cprod,
                                                alm_recepcab.cper,
                                                alm_recepcab.cmes,
                                                alm_recepcab.ncodpry,
                                                FORMAT(SUM(alm_recepdet.ncantidad),2) AS ingresos_mes_actual
                                            FROM
                                                alm_recepdet
                                                INNER JOIN alm_recepcab ON alm_recepdet.id_regalm = alm_recepcab.id_regalm 
                                            WHERE
                                                alm_recepdet.nflgactivo = 1 
                                                AND alm_recepdet.id_cprod =:item
                                                AND alm_recepcab.cper = YEAR(NOW())
                                                AND alm_recepcab.cmes = MONTH(NOW())");
                $sql->execute(["item"=>$item]);

                $result = $sql->fetchAll();

                return $result[0]['ingresos_mes_actual'];


            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function consumo_mes_actual($item){
            try {
                $sql=$this->db->connect()->prepare("SELECT
                                                    SUM(alm_combustible.ncantidad) AS consumo_mes,
                                                    alm_combustible.idprod 
                                                FROM
                                                    alm_combustible 
                                                WHERE
                                                    alm_combustible.idprod = :item 
                                                    AND alm_combustible.nflgactivo = 1
                                                    AND MONTH(alm_combustible.fregistro) = MONTH(NOW())
                                                    AND YEAR(alm_combustible.fregistro) = YEAR(NOW())");
                $sql->execute(["item"=>$item]);

                $result = $sql->fetchAll();

                return $result[0]['consumo_mes'];


            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }
    }
?>