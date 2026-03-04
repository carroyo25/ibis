<?php
    class ActivosModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }


        public function buscarCodigos($parametros){
            try {
                $codigo = $parametros['codigo'];
                $costos = $parametros['costos'];

                $docData = [];
                $mensaje = "";
                $respuesta = false;

                $sql = $this->db->connect()->prepare("SELECT p.ccodprod,
                                                             p.id_cprod,
                                                             UPPER(p.cdesprod) descripcion,
                                                             u.cabrevia,
                                                             u.ncodmed
                                                        FROM cm_producto p
                                                        INNER JOIN tb_unimed u ON u.ncodmed = p.nund
                                                        WHERE p.ccodprod = :codigo");

                $sql->execute(["codigo"=>$codigo]);

                while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                    $docData[] = $row;
                }

                return array("datos"=>$docData);

            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function buscarIngresos($parametros){
            try {
                $docData = [];
                $costos = $parametros['costos'];
                $codigo = $parametros['codigo'];

                $sql = $this->db->connect()->prepare("SELECT
                                                        e.idpedido,
                                                        c.idreg,
                                                        c.idcostos,
                                                        LPAD(e.nropedido,6,0) pedido,
                                                        e.idorden,
                                                        SUM( e.cant_ingr ) AS cantidad,
                                                        DATE_FORMAT( c.ffechadoc, '%d/%m/%Y' ) AS emision,
                                                        tb_proyectos.ccodproy 
                                                    FROM
                                                        alm_existencia AS e
                                                        INNER JOIN alm_cabexist AS c ON c.idreg = e.idregistro
                                                        INNER JOIN tb_proyectos ON c.idcostos = tb_proyectos.nidreg 
                                                    WHERE
                                                        e.codprod = :codigo
                                                        AND c.idcostos = :costos 
                                                        AND e.nflgActivo = 1 
                                                    GROUP BY
                                                        c.idreg");

                $sql->execute(["codigo"=>$codigo,
                                "costos"=>$costos]);

                while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                    $docData[] = $row;
                }

                return array("datos"=>$docData);

            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function buscarInventarios($parametros){
            try {
                $docData = [];
                $costos = $parametros['costos'];
                $codigo = $parametros['codigo'];

                $sql = $this->db->connect()->prepare("SELECT
                                                        ic.idreg,
                                                        i.cant_ingr,
                                                        i.cserie,
                                                        i.condicion,
                                                        IFNULL(i.cestado,'') estado,
                                                        DATE_FORMAT(ic.ffechaInv, '%d/%m/%Y') AS fecha_inventario,
                                                        i.nflgActivo,
                                                        IFNULL(i.ubicacion, '') ubicacion,
                                                        p.ccodproy 
                                                    FROM
                                                        alm_inventariodet AS i
                                                        INNER JOIN alm_inventariocab AS ic ON ic.idreg = i.idregistro
                                                        INNER JOIN tb_proyectos AS p ON ic.idcostos = p.nidreg 
                                                    WHERE
                                                        i.codprod = :codigo 
                                                        AND ic.idcostos = :costos 
                                                        AND i.nflgActivo = 1 
                                                    GROUP BY
                                                        ic.idreg");

                $sql->execute(["codigo"=>$codigo,
                                "costos"=>$costos]);

                while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                    $docData[] = $row;
                }

                return array("datos"=>$docData);

            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function buscarAsignados($parametros){
            try {
                $docData = [];
                $datos = "";
                $serie  = $parametros['serie'];
                $codigo = $parametros['codigo'];
                $costos = $parametros['costos'];

                $ubicacion = "ALM";
                $nombre = "";
                $documento = "";
                $asignado = false;

                $sql = $this->db->connect()->prepare("SELECT
                                                            c.nrodoc 
                                                        FROM
                                                            alm_consumo c 
                                                        WHERE
                                                            c.cserie = :serie
                                                            AND c.ncostos = :costos 
                                                            AND c.idprod = :codigo
                                                            AND ISNULL(	c.cantdevolucion)");
                

                $sql->execute(["serie"=>$serie,"costos"=>$costos,"codigo"=>$codigo]);

                while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                    $docData[] = $row;
                }

                //return array($docData[0]['nrodoc']);
                if (count($docData) > 0 ){
                    $url = "http://179.49.67.42/api/activesapi.php?documento=".$docData[0]['nrodoc'];
                    $api = file_get_contents($url);

                    $datos =  json_decode($api);
                    $ubicacion = "";
                    $documento = $docData[0]['nrodoc'];
                    $asignado = true;
                }

                return array("datos"=>$datos,"ubicacion"=>$ubicacion,"documento"=>$documento,"asignado"=>$asignado);
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function registrarActivos($items) {
            try {
                // Si está vacío, verificar si hay datos en php://input
                if (empty($items)) {
                    $input = file_get_contents('php://input');
                    if (!empty($input)) {
                        $items = json_decode($input, true);
                    }
                }
                
                // Verificar si llegaron datos
                if (empty($items)) {
                    throw new Exception('No se recibieron datos');
                }
                
                // Si $items tiene clave 'items', extraer
                if (isset($items['items'])) {
                    if (is_string($items['items'])) {
                        $items = json_decode($items['items'], true);
                    } else {
                        $items = $items['items'];
                    }
                }
                
                $insertados = 0;
                $actualizados = 0;
                
                foreach ($items as $item) {
                    if ( $item['id'] == '-' && $item['grabado'] == 1 ) {
                         
                         $sql = $this->db->connect()->prepare("INSERT INTO alm_activos 
                                                             SET alm_activos.idprod =:codigo,
                                                                alm_activos.ncant =:cantidad,
                                                                alm_activos.nreg =:registro,
                                                                alm_activos.cestado=:estado,
                                                                alm_activos.cserie=:serie,
                                                                alm_activos.cubicacion=:ubicacion,
                                                                alm_activos.ffcalibra=:calibra,
                                                                alm_activos.ffvence=:vence,
                                                                alm_activos.fobservaciones=:observaciones");
                        
                        $sql->execute(['codigo'=>$item['idprod'],
                                    'cantidad'=>$item['cant'],
                                    'registro'=>$item['registro'],
                                    'estado'=>$item['estado'],
                                    'serie'=>$item['serie'],
                                    'ubicacion'=>$item['ubicacion'],
                                    'calibra'=>$item['calibra'],
                                    'vence'=>$item['vence'],
                                    'observaciones'=>$item['observa']]);
                        
                        $insertados++;

                    }
                }
                
                return [
                    'success' => true,
                    'message' => "Procesados: $insertados insertados, $actualizados actualizados",
                    'data' => $items
                ];
                
            } catch (Exception $e) {
                return [
                    'success' => false,
                    'message' => $e->getMessage()
                ];
            }
        }
             
    }
?>