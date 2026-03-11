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

        public function registrarDeArchivo($parametros){
            try {
                $proyecto = $parametros['proyecto'];
                $registra = $parametros['registra'];
                $filas = json_decode($parametros['filas'],true);

                foreach($filas as $fila){

                    $interno = $this->buscarCodigoInterno($fila[1]);

                    $sql = $this->db->connect()->prepare("INSERT INTO alm_activos SET 
                                                    idcostos = :costos,
                                                    idprod = :codigo,
                                                    iduser = :registra,
                                                    ncant = :cantidad,
                                                    casigna = :asigna,
                                                    cestado = :estado,
                                                    cserie = :serie,
                                                    cmodelo = :modelo,
                                                    cmarca = :marca,
                                                    nfrecuencia = :frecuencia,
                                                    ffcalibra = :calibra,
                                                    ffvence = :vence,
                                                    cgrenvio = :grenvio,
                                                    ffenvio = :envio,
                                                    cgrrecepcion = :grrecepcion,
                                                    ffrecepcion = :recepcion,
                                                    cobservaciones = :observacion,
                                                    ccontenedor = :contenedor,
                                                    cestante = :estante,
                                                    cletra = :letra,
                                                    ccolumna = :columna,
                                                    carea = :area,
                                                    cubica = :ubica");

                    // Validar que $fila tenga suficientes elementos
                    $indices_requeridos = [6,7,8,9,10,11,12,13,14,15,16,17,18,19,22,26,27,28,29];
                    foreach ($indices_requeridos as $indice) {
                        if (!isset($fila[$indice])) {
                            $fila[$indice] = null; // o valor por defecto
                        }
                    }

                    // Ejecutar con los datos
                    $resultado = $sql->execute([
                        "costos" => $proyecto,
                        "codigo" => $interno['codigo'],
                        "registra" => $registra,
                        "cantidad" => 1,  // Movido al principio
                        "asigna" => $fila[19],
                        "estado" => $fila[12],
                        "serie" => $fila[6],
                        "modelo" => $fila[8],
                        "marca" => $fila[7],
                        "frecuencia" => $fila[9] == 'ANUAL' ? 303: 304,
                        "calibra" => $fila[10],
                        "vence" => $fila[11],
                        "grenvio" => $fila[14],
                        "envio" => $fila[15],
                        "grrecepcion" => $fila[16],
                        "recepcion" => $fila[17],
                        "observacion" => $fila[13],
                        "contenedor" => $fila[26],
                        "estante" => $fila[27],
                        "letra" => $fila[28],
                        "columna" => $fila[29],
                        "area" => $fila[22],
                        "ubica" => $fila[18]
                    ]);
                    
                }

                return array("proyecto"=>$proyecto,$filas);
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }
        
        private function buscarCodigoInterno($codigo){
            try {
                $docData = [];
                $sql = $this->db->connect()->prepare("SELECT
                                                cm_producto.ccodprod,
                                                cm_producto.nund,
                                                cm_producto.id_cprod 
                                            FROM
                                                cm_producto 
                                            WHERE
                                                cm_producto.ccodprod = :codigo 
                                            LIMIT 1");
                $sql->execute(["codigo"=>$codigo]);

                $result = $sql->fetchAll();

                if( gettype($result) == NULL )
                    return array("codigo"=>"X","unidad"=>"X");
                else    
                    return array("codigo"=>$result[0]['id_cprod'],"unidad"=>$result[0]['nund']);
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function consultarEquipos($parametros){
            try {
                $docData = [];
                $conexion = $this->db->connect();
            
                // PASO 3: Verificar la consulta completa
                $sql = $conexion->prepare("SELECT
                                                a.idprod,
                                                p.ccodprod,
                                                UPPER(p.cdesprod) as descripcion,
                                                u.cabrevia,
                                                a.cserie,
                                                a.cmodelo,
                                                a.cmarca,
                                                a.nfrecuencia,
                                                a.ffcalibra,
                                                a.ffvence,
                                                a.cgrenvio,
                                                a.ffenvio,
                                                a.ffrecepcion,
                                                a.ffasignacion,
                                                a.cgrrecepcion,
                                                a.cobservaciones,
                                                a.ccontenedor,
                                                a.cestante,
                                                a.cletra,
                                                a.ccolumna,
                                                a.carea,
                                                a.cubica,
                                                a.cestado,
                                                a.casigna
                                            FROM
                                                alm_activos a
                                                LEFT JOIN cm_producto p ON p.id_cprod = a.idprod
                                                LEFT JOIN tb_unimed u ON u.ncodmed = p.nund
                                            WHERE
                                                a.idcostos = :costos");
                $sql->execute(["costos"=>98]);
                
                while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                    $docData[] = $row;
                }
                
                return array("datos"=>$docData);                               
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }
    }
?>