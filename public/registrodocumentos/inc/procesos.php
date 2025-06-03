<?php
    // Establecer zona horaria de Lima
    date_default_timezone_set('America/Lima');
        
    header('Content-Type: application/json');

    require('connect.php');
    
    session_start();

    $response = [
        'success' => false,
        'message' => '',
        'uploadedFiles' => []
    ];


    if(isset($_POST['funcion'])){
        if($_POST['funcion'] == "login"){
            echo json_encode(login($pdo, $_POST));
        }else if($_POST['funcion'] == "listarOrdenesEntidad"){
            echo json_encode(listarOrdenesEntidad($pdo, $_POST));
        }else if($_POST['funcion'] == "registrarDocumentos"){
            echo json_encode(registrarDocumentos($pdo, $_POST));
        }else if($_POST['funcion'] == "consultarDocumentos"){
            echo json_encode(consultarDocumentos($pdo, $_POST));
        }else if($_POST['funcion'] == "listarOrdenesEntidadRevision"){
            echo json_encode(listarOrdenesEntidadRevision($pdo, $_POST));
        }else if($_POST['funcion'] == "validarTotal"){
            echo json_encode(validarTotal($pdo, $_POST));
        }
    }

    function login($pdo, $datos){
        try{
            $sql = "SELECT 
                        cm_entidad.cnumdoc,
                        cm_entidad.cpassword,
                        cm_entidad.id_centi,
                        cm_entidad.cnumdoc,
                        cm_entidad.crazonsoc 
                    FROM cm_entidad 
                    WHERE cm_entidad.cnumdoc = :ruc
                    AND cm_entidad.nflgactivo = 7
                    AND cm_entidad.cpassword = :pass
                    LIMIT 1";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([':ruc' => $datos['ruc'],
                            ':pass' => SHA1($datos['clave'])]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $count = $stmt->rowCount();
            
            $ruc_exist = false;
            $messaje = 'Entidad no registrada o contraseña incorrecta';

            if ( $count > 0 ) {
                $_SESSION['ruc'] = $result[0]['cnumdoc'];
                $_SESSION['entidad'] = $result[0]['crazonsoc'];
                $_SESSION['log'] = true;
                $messaje = "Bienvenido al registro de documentos";
                $ruc_exist = true;
                
                return ['status' => 'success', 
                        'message' => $messaje,
                        'ruc_exist'=>$ruc_exist,
                        'ruc'=>$result[0]['cnumdoc'],
                        'entidad'=>$result[0]['crazonsoc'],
                        'id'=>$result[0]['id_centi']];
            }else{
                return ['status' => 'error', 'message' => $messaje,'ruc_exist'=>false];
            }
        }catch(PDOException $e){
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    function listarOrdenesEntidad($pdo, $datos){
        try {
            $sql = "SELECT
                    LPAD( lg_ordencab.cnumero, 7, 0 ) AS cnumero,
                    lg_ordencab.cper,
                    lg_ordencab.cmes,
                    lg_ordencab.id_centi,
                    lg_ordencab.ntipmov,
                    lg_ordencab.nEstadoDoc,
                    lg_ordencab.id_regmov,
                    lg_ordencab.nEstadoReg,
                    adm_ordstatus.fechapresenta,
                    IF (ISNULL(adm_ordstatus.estadorden),0,adm_ordstatus.estadorden) AS estado 
                FROM
                    lg_ordencab
                    LEFT JOIN adm_ordstatus ON lg_ordencab.id_regmov = adm_ordstatus.idorden 
                WHERE
                    lg_ordencab.id_centi = :enti 
                    AND lg_ordencab.nEstadoDoc = 60
                    AND lg_ordencab.cper = YEAR(NOW()) 
                ORDER BY
                    lg_ordencab.cnumero DESC";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':enti' => $datos['id']]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $count = $stmt->rowCount();

            return $result;

        } catch(PDOException $e){
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    function registrarDocumentos($pdo,$datos){
        try {
            $files = json_decode($datos['files']);
            $nreg = count($files);
            $filesProcess = 0;

            // Check if files were uploaded
            if (!isset($_FILES['filesToUpload']) || $_FILES['filesToUpload']['error'][0] === UPLOAD_ERR_NO_FILE) {
                $response['message'] = 'No files were uploaded.';
                echo json_encode($response);
                exit;
            }

            // Allowed file types
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'txt'];
            $maxFileSize = 5 * 1024 * 1024; // 5MB

            $uploadDir = $_SERVER["DOCUMENT_ROOT"].'/ibis/public/documentos/proveedores/presentados/';

            $fileCount = count($_FILES['filesToUpload']['name']);

            for ($i = 0; $i < $fileCount; $i++){
                $fileName = $_FILES['filesToUpload']['name'][$i];
                $fileTmp = $_FILES['filesToUpload']['tmp_name'][$i];
                $fileSize = $_FILES['filesToUpload']['size'][$i];
                $fileType = $_FILES['filesToUpload']['type'][$i];


                // Get file extension
                $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                // Generate unique filename
                $newFileName = uniqid('', true) . '.' . $fileExt;
                $uploadPath = $uploadDir . $newFileName;

                //preparar la consulta
                $sql = "INSERT INTO adm_docsenti 
                                SET adm_docsenti.idcenti        =:enti,
                                    adm_docsenti.idorden        =:orden,
                                    adm_docsenti.namefile       =:namefile,
                                    adm_docsenti.internalname   =:internal,
                                    adm_docsenti.statusfile     = 1";

                // Move file to upload directory
                if (move_uploaded_file($fileTmp, $uploadPath)) {
                    $response['uploadedFiles'][] = [
                        'originalName'  => $fileName,
                        'name'          => $newFileName,
                        'path'          => $uploadPath,
                        'size'          => $fileSize,
                        'type'          => $fileType,
                        'success'       => true
                    ];

                    $filesProcess++;
                } else {
                    $response['uploadedFiles'][] = [
                        'originalName' => $fileName,
                        'success' => false,
                        'error' => 'Failed to move uploaded file'
                    ];
                }

                try {
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([":enti"     => $datos['entidad'],
                                    ":orden"    => $datos['ordenId'],
                                    ":internal" => $newFileName,
                                    ":namefile" => $fileName]);

                } catch(PDOException $e){
                    return ['status' => 'error', 'message' => $e->getMessage()];
                }
            }

            //if ($filesProcess > 0) {
                try {
                    
                    $fecha = date('Y-m-d H:i:s');
                    $sql = "INSERT INTO adm_ordstatus
                            SET adm_ordstatus.idcenti =:enti,
                                adm_ordstatus.idorden =:orden,
                                adm_ordstatus.fechapresenta =:presenta,
                                adm_ordstatus.estadorden = 1";
                    $stmt = $pdo->prepare($sql); 
                    $stmt->execute([":enti"     => $datos['entidad'],
                                    ":orden"    => $datos['ordenId'],
                                    ":presenta" => $fecha]);

                } catch(PDOException $e){
                    return ['status' => 'error', 'message' => $e->getMessage()];
                }
           // }


            return array("archivos"=>$nreg,"status"=>$response);
        } catch(PDOException $e){
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    function consultarDocumentos($pdo,$datos){
        try {
            $archivos = 0;
            $result = [];

            $sql = "SELECT adm_docsenti.idreg,
                            adm_docsenti.idcenti,
                            adm_docsenti.statusfile,
                            adm_docsenti.namefile,
                            adm_docsenti.fecrecep,
                            adm_docsenti.internalname
                    FROM adm_docsenti
                    WHERE adm_docsenti.flgActivo = 1
                        AND adm_docsenti.idorden =:orden
                        AND adm_docsenti.idcenti =:centi";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':orden' => $datos['orden'],
                            ':centi' => $datos['centi']]);

            $count = $stmt->rowCount();

            if ($count > 0){
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }

            return ["archivos"=>$count,"resultado"=>$result];
            
        } catch(PDOException $e){
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }


    /**** Procesos Administrativos */
    function listarOrdenesEntidadRevision($pdo, $datos){
        try {
            $sql = "SELECT
                    LPAD( lg_ordencab.cnumero, 7, 0 ) AS cnumero,
                    lg_ordencab.cper,
                    lg_ordencab.cmes,
                    lg_ordencab.id_centi,
                    lg_ordencab.ntipmov,
                    lg_ordencab.nEstadoDoc,
                    lg_ordencab.id_regmov,
                    lg_ordencab.nEstadoReg,
                    adm_ordstatus.fechapresenta,
                    IF (ISNULL(adm_ordstatus.estadorden),0,adm_ordstatus.estadorden) AS estado 
                FROM
                    lg_ordencab
                    LEFT JOIN adm_ordstatus ON lg_ordencab.id_regmov = adm_ordstatus.idorden 
                WHERE
                    adm_ordstatus.estadorden = 1
                    OR adm_ordstatus.estadorden = 2
                ORDER BY
                    lg_ordencab.cnumero DESC";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':enti' => $datos['id']]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $count = $stmt->rowCount();

            return $result;

        } catch(PDOException $e){
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    function validarTotal($pdo,$datos){
        try {
            $mensaje = "Error al procesar el envio";

            $sql = "UPDATE adm_ordstatus 
                    SET adm_ordstatus.estadorden = 3
                    WHERE adm_ordstatus.idorden =:orden";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([':orden' => $datos['id']]);

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $rowCount = $stmt->rowCount();

            if ($rowCount > 0){
                $mensaje = "Registro actualizado";
            }

            return array("mensaje"=>$mensaje);

        } catch(PDOException $e){
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
?>