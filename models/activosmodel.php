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
                $existe = false;

                if ( $this->activoRegistrado($serie,$codigo,$costos) == 1){
                    $asignado = false;
                    $existe = true;
                }

                $sql = $this->db->connect()->prepare("SELECT
                                                            c.nrodoc,
                                                            c.fechasalida
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

                if ( count($docData) > 0 ){
                    $url = "http://179.49.67.42/api/activesapi.php?documento=".$docData[0]['nrodoc'];
                    $api = file_get_contents($url);

                    $datos      =  json_decode($api,true);

                    $ubicacion  = "";
                    $documento  = $docData[0]['nrodoc'];
                    $cargo      = $datos['cargo'] ?? null;
                    $salida     = $docData[0]['fechasalida'] ?? null;
                    $asignado   = true;

                    return array("datos"=>$datos,"ubicacion"=>$ubicacion,"documento"=>$documento,"asignado"=>$asignado,"salida"=>$salida,"existe"=>$existe);
                }else{
                    return array("asignado"=>$asignado,"existe"=>$existe);
                }

               
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function registrarActivos($items) {
            try {
                $registrado = false;
                $mensaje = "Error al registrar el equipo";
                $clase = "mensaje_error"; 

                $sql = $this->db->connect()->prepare("INSERT INTO alm_activos
                                                    SET alm_activos.idprod =:codigo,
                                                        alm_activos.idcostos =:costos,
                                                        alm_activos.iduser =:usuario,
                                                        alm_activos.ncant =:cantidad,
                                                        alm_activos.cestado =:estado,
                                                        alm_activos.cserie =:serie,
                                                        alm_activos.cmodelo =:modelo,
                                                        alm_activos.cmarca =:marca,
                                                        alm_activos.nfrecuencia =:frecuencia,
                                                        alm_activos.ffcalibra =:calibra,
                                                        alm_activos.ffvence =:vecimiento,
                                                        alm_activos.cgrenvio =:guiaenvio,
                                                        alm_activos.ffenvio =:fechaenvio,
                                                        alm_activos.ffasignacion =:fechaasigna,
                                                        alm_activos.cgrrecepcion =:guiarecepcion,
                                                        alm_activos.ffrecepcion =:fecharecepcion,
                                                        alm_activos.cobservaciones =:observaciones,
                                                        alm_activos.ccontenedor =:contenedor,
                                                        alm_activos.cestante =:estante,
                                                        alm_activos.cletra =:letra,
                                                        alm_activos.ccolumna =:columna,
                                                        alm_activos.carea =:area,
                                                        alm_activos.casigna =:asignado,
                                                        alm_activos.cubica=:ubicacion");
                $sql->execute([ "codigo"=>$items['codigo_interno'],
                                "costos"=>$items['centro_costos'],
                                "usuario"=>$items['codigo_usuario'],
                                "cantidad"=>$items['cantidad'],
                                "estado"=>$items['estado_actual'],
                                "serie"=>$items['serie'],
                                "modelo"=>$items['modelo'],
                                "marca"=>$items['marca'],
                                "frecuencia"=>$items['frecuencia'],
                                "calibra"=>$items['fecha_calibra'],
                                "vecimiento"=>$items['vence_calibra'],
                                "guiaenvio"=>$items['guia_envio'],
                                "fechaenvio"=>$items['fecha_envio'],
                                "fechaasigna"=>$items['fecha_asigna'],
                                "guiarecepcion"=>$items['guia_recepcion'],
                                "fecharecepcion"=>$items['fecha_recepcion'],
                                "observaciones"=>$items['observa_estado'],
                                "contenedor"=>$items['contenedor'],
                                "estante"=>$items['estante'],
                                "letra"=>$items['letra'],
                                "columna"=>$items['columna'],
                                "area"=>$items['area'],
                                "asignado"=>$items['dni'],
                                "ubicacion"=>$items['ubicacion']]);

                if ($sql->rowCount() > 0){
                    $registrado = true;
                    $mensaje = "Registrado Correctamente";
                    $clase = "mensaje_correcto";
                };

                return array("registrado"=>$registrado,"mensaje"=>$mensaje,"clase"=>$clase);

            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function activoRegistrado($serie,$codigo,$costos){
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                        count( a.cserie ) existe 
                                    FROM
                                        alm_activos a 
                                    WHERE
                                        a.idcostos =:costos 
                                        AND a.cserie =:serie
                                        AND a.idprod =:codigo");
                
                $sql->execute(["costos"=>$costos,"serie"=>$serie,"codigo"=>$codigo]);

                while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                    $docData[] = $row;
                }

                return $docData[0]['existe'];

            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }
             
    }
?>