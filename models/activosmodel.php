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
                                                            c.cserie LIKE :serie
                                                            AND c.ncostos = :costos 
                                                            AND c.idprod = :codigo
                                                            AND c.ncondicion = 1");
                

                $sql->execute(["serie"=>'%'.$serie,"costos"=>$costos,"codigo"=>$codigo]);

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
                // Validar datos requeridos
                if (empty($items['codigo_interno'])) {
                    return array(
                        "registrado" => false,
                        "mensaje" => "El código interno es obligatorio",
                        "clase" => "mensaje_error",
                        "ultimo_id" => null
                    );
                }

                $conexion = $this->db->connect();
                
                $sql = $conexion->prepare("INSERT INTO alm_activos
                                            SET alm_activos.idprod = :codigo,
                                                alm_activos.idcostos = :costos,
                                                alm_activos.iduser = :usuario,
                                                alm_activos.ncant = :cantidad,
                                                alm_activos.cestado = :estado,
                                                alm_activos.cserie = :serie,
                                                alm_activos.cmodelo = :modelo,
                                                alm_activos.cmarca = :marca,
                                                alm_activos.nfrecuencia = :frecuencia,
                                                alm_activos.ffcalibra = :calibra,
                                                alm_activos.ffvence = :vecimiento,
                                                alm_activos.cgrenvio = :guiaenvio,
                                                alm_activos.ffenvio = :fechaenvio,
                                                alm_activos.ffasignacion = :fechaasigna,
                                                alm_activos.cgrrecepcion = :guiarecepcion,
                                                alm_activos.ffrecepcion = :fecharecepcion,
                                                alm_activos.cobservaciones = :observaciones,
                                                alm_activos.ccontenedor = :contenedor,
                                                alm_activos.cestante = :estante,
                                                alm_activos.cletra = :letra,
                                                alm_activos.ccolumna = :columna,
                                                alm_activos.carea = :area,
                                                alm_activos.casigna = :asignado,
                                                alm_activos.cubica = :ubicacion");
                
                $resultado = $sql->execute([ 
                    "codigo" => $items['codigo_interno'] ?? null,
                    "costos" => $items['centro_costos'] ?? null,
                    "usuario" => $items['codigo_usuario'] ?? null,
                    "cantidad" => $items['cantidad'] ?? 1,
                    "estado" => $items['estado_actual'] ?? null,
                    "serie" => $items['serie'] ?? null,
                    "modelo" => $items['modelo'] ?? null,
                    "marca" => $items['marca'] ?? null,
                    "frecuencia" => $items['frecuencia'] ?? null,
                    "calibra" => $items['fecha_calibra'] ?? null,
                    "vecimiento" => $items['vence_calibra'] ?? null,
                    "guiaenvio" => $items['guia_envio'] ?? null,
                    "fechaenvio" => $items['fecha_envio'] ?? null,
                    "fechaasigna" => $items['fecha_asigna'] ?? null,
                    "guiarecepcion" => $items['guia_recepcion'] ?? null,
                    "fecharecepcion" => $items['fecha_recepcion'] ?? null,
                    "observaciones" => $items['observa_estado'] ?? null,
                    "contenedor" => $items['contenedor'] ?? null,
                    "estante" => $items['estante'] ?? null,
                    "letra" => $items['letra'] ?? null,
                    "columna" => $items['columna'] ?? null,
                    "area" => $items['area'] ?? null,
                    "asignado" => $items['dni'] ?? null,
                    "ubicacion" => $items['ubicacion'] ?? null
                ]);

                if ($resultado && $sql->rowCount() > 0) {
                    // Método 1: lastInsertId() sin parámetros
                    $ultimoId = $conexion->lastInsertId();
                    
                    // Si no funciona, intenta con el nombre de la secuencia
                    if (empty($ultimoId)) {
                        $ultimoId = $conexion->lastInsertId('id'); // Reemplaza 'id' con el nombre de tu columna auto_increment
                    }
                    
                    // Método 2: Consultar el último ID insertado (alternativa)
                    if (empty($ultimoId) || $ultimoId == 0) {
                        $queryId = $conexion->query("SELECT LAST_INSERT_ID() as last_id");
                        $ultimoId = $queryId->fetch(PDO::FETCH_ASSOC)['last_id'];
                    }
                    
                    // Para debug - puedes registrar esto en un log
                    error_log("ID insertado: " . $ultimoId);
                    
                    return array(
                        "registrado" => true,
                        "mensaje" => "Registrado Correctamente",
                        "clase" => "mensaje_correcto",
                        "ultimo_id" => $ultimoId
                    );
                } else {
                    return array(
                        "registrado" => false,
                        "mensaje" => "No se pudo registrar el equipo",
                        "clase" => "mensaje_error",
                        "ultimo_id" => null
                    );
                }
                
            } catch (PDOException $e) {
                error_log("Error al registrar activo: " . $e->getMessage());
                
                return array(
                    "registrado" => false,
                    "mensaje" => "Error en la base de datos: " . $e->getMessage(),
                    "clase" => "mensaje_error",
                    "ultimo_id" => null
                );
            }
        }

        public function modificarActivos($items) {
            try {
                $registrado = false;
                $mensaje = "Error al registrar el equipo";
                $clase = "mensaje_error"; 

                $sql = $this->db->connect()->prepare("UPDATE alm_activos
                                                    SET alm_activos.iduser =:usuario,
                                                        alm_activos.ncant =:cantidad,
                                                        alm_activos.cestado =:estado,
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
                                                        alm_activos.cubica=:ubicacion
                                                    WHERE alm_activos.idreg =:interno");
                $sql->execute([ "usuario"=>$items['codigo_usuario'],
                                "cantidad"=>$items['cantidad'],
                                "estado"=>$items['estado_actual'],
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
                                "ubicacion"=>$items['ubicacion'],
                                "interno"=>$items['codigo_registro']]);

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

                $estados = [
                    'CALIBRADO' => 306,
                    'VENCIDO' => 307,
                    'POR CALIBRAR' => 308,
                    'OPERATIVO' => 309,
                    'OTROS' => 310
                ];

                
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

                    $estado  = $estados[$fila[12]] ?? null;

                    // Ejecutar con los datos
                    $resultado = $sql->execute([
                        "costos" => $proyecto,
                        "codigo" => $interno['codigo'],
                        "registra" => $registra,
                        "cantidad" => 1,  // Movido al principio
                        "asigna" => $fila[19],
                        "estado" => $estado,
                        "serie" => $fila[6],
                        "modelo" => $fila[8],
                        "marca" => $fila[7],
                        "frecuencia" => $fila[9] == 'ANUAL' ? 303: 304,
                        "calibra" => $this->excelDateToMySQL($fila[10]),
                        "vence" => $this->excelDateToMySQL($fila[11]),
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

                $serie = $parametros['serie'] == '' ? '%' : '%'.$parametros['serie'].'%';
                $descripcion = $parametros['descripcion'] == '' ? '%' : '%'.$parametros['descripcion'].'%';
            
                // PASO 3: Verificar la consulta completa
                $sql = $conexion->prepare("SELECT
                                                a.idreg,
                                                a.idprod,
                                                p.ccodprod,
                                                UPPER(p.cdesprod) AS descripcion,
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
                                                a.casigna,
                                                f.cdescripcion AS frecuencia,
                                                e.cdescripcion AS estado,
                                                COUNT(d.nidrefer) AS archivos 
                                            FROM
                                                alm_activos a
                                                LEFT JOIN cm_producto p ON p.id_cprod = a.idprod
                                                LEFT JOIN tb_unimed u ON u.ncodmed = p.nund
                                                LEFT JOIN tb_parametros f ON a.nfrecuencia = f.nidreg
                                                LEFT JOIN lg_regdocumento d ON d.nidrefer = a.idreg AND d.cmodulo = 'CER'
                                                LEFT JOIN tb_parametros e ON a.cestado = e.nidreg 
                                            WHERE
                                                a.idcostos = :costos
                                                AND p.cdesprod LIKE :descripcion
                                                AND a.cserie LIKE :serie
                                            GROUP BY
                                                a.idreg, a.idprod, p.ccodprod, p.cdesprod, u.cabrevia, a.cserie, 
                                                a.cmodelo, a.cmarca, a.nfrecuencia, a.ffcalibra, a.ffvence, a.cgrenvio,
                                                a.ffenvio, a.ffrecepcion, a.ffasignacion, a.cgrrecepcion, a.cobservaciones,
                                                a.ccontenedor, a.cestante, a.cletra, a.ccolumna, a.carea, a.cubica,
                                                a.cestado, a.casigna, f.cdescripcion, e.cdescripcion
                                            ORDER BY p.cdesprod");
                                            
                $sql->execute(["costos"=>$parametros['costos'],"descripcion"=>$descripcion,"serie"=>$serie]);
                
                while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                    $docData[] = $row;
                }
                
                return array("datos"=>$docData);                               
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function excelDateToMySQL($excelDate) {
            if (empty($excelDate) || !is_numeric($excelDate)) {
                return null;
            }
                
            // Excel cuenta desde 1900-01-01
            // 25569 es el número de días entre 1900-01-01 y 1970-01-01 (Unix epoch)
            $unixTimestamp = ($excelDate - 25569) * 86400;
                
            return date('Y-m-d', $unixTimestamp);
        }

        public function consultarIDEquipo($parametros){
            try {
                $docData = [];
                $personal = "";

                $sql = $this->db->connect()->prepare("SELECT
                                                        a.idreg,
                                                        a.idprod,
                                                        a.idcostos,
                                                        a.ncant,
                                                        a.casigna,
                                                        a.cestado,
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
                                                        a.cubica,
                                                        a.carea,
                                                        p.ccodprod,
                                                        p.cdesprod,
                                                        p.nund,
                                                        u.cabrevia,
                                                        u.ncodmed,
                                                        o.fechasalida,
                                                        o.ncondicion 
                                                    FROM
                                                        alm_activos a
                                                        LEFT JOIN cm_producto p ON p.id_cprod = a.idprod
                                                        LEFT JOIN tb_unimed u ON p.nund = u.ncodmed
                                                        LEFT JOIN ( SELECT ac.cserie, ac.fechasalida, ac.ncondicion, ac.ncostos 
                                                                                FROM alm_consumo ac 
                                                                                WHERE ncondicion = 0 ) 
                                                                    AS o ON o.cserie LIKE CONCAT( a.cserie, '%' ) 
                                                        AND o.ncostos = a.idcostos 
                                                    WHERE
                                                        a.idreg = :codigo");

        
                $sql->execute(["codigo"=>$parametros['codigo']]);



                while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                    $docData[] = $row;
                }

                if ( $docData[0]['casigna'] !== null ){
                    $url        = "http://179.49.67.42/api/activesapi.php?documento=".$docData[0]['casigna'];
                    $api        = file_get_contents($url);
                    $personal   =  json_decode($api,true);
                }

                return array("datos"=>$docData,'personal'=>$personal);

            }catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function subirCertificados($codigo,$adjuntos){
            $countfiles = count( $adjuntos );
            $filesUpload = 0;
            $errors = [];

            for($i=0;$i<$countfiles;$i++){
                try {
                    $file = "file-".$i;
                    $originalName = $adjuntos[$file]['name'];
                    $ext = explode('.', $originalName);
                    $filename = uniqid().".".end($ext);
                    
                    // Verificar si el nombre del archivo ya existe en la base de datos
                    $checkSql = $this->db->connect()->prepare("SELECT COUNT(*) as total FROM lg_regdocumento WHERE cdocumento = :doc AND nflgactivo = 1");
                    $checkSql->execute(["doc"=>$originalName]);
                    $result = $checkSql->fetch(PDO::FETCH_ASSOC);
                    
                    if($result['total'] > 0){
                        // El nombre del archivo ya existe, no se sube
                        $errors[] = "El archivo '{$originalName}' ya existe en el sistema y no se puede subir.";
                        continue; // Saltar este archivo y continuar con el siguiente
                    }
                    
                    // Upload file
                    if (move_uploaded_file($adjuntos[$file]['tmp_name'], 'public/documentos/certificados/activos/'.$filename)){
                        $sql = $this->db->connect()->prepare("INSERT INTO lg_regdocumento 
                                                                    SET nidrefer=:cod, cmodulo=:mod, cdocumento=:doc,
                                                                        creferencia=:ref, nflgactivo=:est");
                        $sql->execute(["cod"=>$codigo,
                                        "mod"=>"CER",
                                        "ref"=>$filename,
                                        "doc"=>$originalName,
                                        "est"=>1]);

                        $filesUpload++;
                    } else {
                        $errors[] = "Error al subir el archivo '{$originalName}'";
                    }
                    
                } catch (PDOException $th) {
                    echo "Error: ".$th->getMessage();
                    return false;
                }
            }
            
            // Devolver resultados
            return [
                "total_adjuntos" => $filesUpload,
                "errores" => $errors,
                "mensaje" => count($errors) > 0 ? "Algunos archivos no se pudieron subir" : "Todos los archivos se subieron correctamente"
            ];
        }
    }
?>