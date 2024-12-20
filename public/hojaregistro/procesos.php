<?php
    require('connect.php');

    session_start();

    if(isset($_POST['funcion'])){
        if($_POST['funcion'] == "grabarProveedor"){
            echo json_encode(grabarProveedor($pdo, $_POST, $_FILES));
        }else if($_POST['funcion'] == "buscarRuc"){
            echo json_encode(buscarRuc($pdo, $_POST));
        }else if($_POST['funcion'] == "login"){
            echo json_encode(login($pdo, $_POST));
        }else if($_POST['funcion'] == "actualizar"){
            echo json_encode(actualizarProveedor($pdo, $_POST, $_FILES));
        }else if($_POST['funcion'] == "cambiarPass"){
            echo json_encode(cambiarPassword($pdo, $_POST));
        }else if($_POST['funcion'] =="verificar"){
            echo json_encode(verificar($pdo, $_POST));
        }
    }

    function verificar($pdo,$datos){
        try {
            $_SESSION['ruc'] = $datos['ruc'];
            
            $sql = "SELECT cm_entidad.cnumdoc FROM cm_entidad WHERE cm_entidad.cnumdoc = :ruc";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':ruc' => $datos['ruc']]);
            $count = $stmt->fetchColumn();
            
            return ['status' => 'success', 'ruc_exist' => $count > 0];

        } catch(PDOException $e){
            echo "Error al guardar los datos: " . $e->getMessage();
            $pdo->rollBack();
        }
    }

    function grabarProveedor($pdo, $datos, $files) {
        var_dump($datos);
        
        /*try{
            $pdo->beginTransaction();

            $fechaActual = date('Y-m-d');
            $uploadDir = '../documentos/'; 
            $nameFichaRuc = '';
            $nameCatalogo = '';
            // Procesar archivo RUC
            if (isset($_FILES['file_ruc']) && $_FILES['file_ruc']['error'] === UPLOAD_ERR_OK) {
                $fileRuc = $_FILES['file_ruc'];
                $nameFichaRuc = 'ficha_'.$datos['ruc'].'_'.$fechaActual.'.'.pathinfo($_FILES['file_ruc']['name'], PATHINFO_EXTENSION);
                $extensionRuc = pathinfo($fileRuc['name'], PATHINFO_EXTENSION);
                $filePathRuc = $uploadDir .'ficharuc/'. basename($nameFichaRuc);
                move_uploaded_file($fileRuc['tmp_name'], $filePathRuc);
            }

            // Procesar archivo Catálogo
            if (isset($_FILES['file_catalogo']) && $_FILES['file_ruc']['error'] === UPLOAD_ERR_OK) {
                $fileCatalogo = $_FILES['file_catalogo'];
                $nameCatalogo = 'catalogo_'.$datos['ruc'].'_'.$fechaActual.'.'.pathinfo($_FILES['file_ruc']['name'], PATHINFO_EXTENSION);
                $extensionCat = pathinfo($fileCatalogo['name'], PATHINFO_EXTENSION);
                $filePathCatalogo = $uploadDir .'catalogoproducto/'. basename($nameCatalogo);
                
                move_uploaded_file($fileCatalogo['tmp_name'], $filePathCatalogo);
            }

            $clave = generarClaveAleatoria(32);
            $hashClave = password_hash($clave, PASSWORD_DEFAULT);
            $sql = "INSERT INTO cm_entidad 
                                    SET cnumdoc=:ruc,
                                        crazonsoc=:razon_social,
                                        cviadireccion=:direccion,
                                        cemail=:correo_electronico,
                                        ctelefono=:telefono,
                                        ncodpais=:pais, 
                                        cficharuc=:ficha_ruc, 
                                        ccatalogo=:catalogo_prod, 
                                        cpassword=:pass";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':ruc' => $datos['ruc'],
                ':razon_social' => $datos['razon_social'],
                ':direccion' => $datos['direccion'],
                ':correo_electronico' => $datos['correo_electronico'],
                ':telefono' => $datos['telefono'],
                ':pais' => $datos['pais'],
                ':ficha_ruc' => $nameFichaRuc,
                ':catalogo_prod' => $nameCatalogo,
                ':pass' => $hashClave
            ]); 

            $lastId = $pdo->lastInsertId();

            $cuentas = json_decode($datos['cuentas'], true);

            $detalleSql = "INSERT INTO cm_detalle_entidad SET id_entidad=:id_entidad, cwebpage=:pagina_web, nformapago=:forma_pago, nacteconomica=:actividad_economica, cnomgerentec=:gerente_comercial, cnumdocgerentec=:documento_gerente, ctelgerentec=:telefono_gerente, cemailgerentec=:correo_gerente, cnomcontacto=:contacto,
            cnumdoccontacto=:documento_contacto, ctelcontacto=:telefono_contacto, cemailcontacto=:correo_contacto, ccuentadetrac=:cta_detracciones, nentifinan=:nombre_banco, ntipomoneda=:tipo_moneda, ntipocuenta=:tipo_cuenta, cnumcuenta=:numero_cuenta";

            $detalleStmt = $pdo->prepare($detalleSql);

            if(count($cuentas)>0){
                foreach($cuentas as $cuenta){
                    $detalleStmt->execute([
                        ':id_entidad' => $lastId,
                        ':pagina_web' => $datos['pagina_web'],
                        ':forma_pago' => $datos['forma_pago'],
                        ':actividad_economica' => $datos['actividad_economica'],
                        ':gerente_comercial' => $datos['gerente_comercial'],
                        ':documento_gerente' => $datos['documento_gerente'],
                        ':telefono_gerente' => $datos['telefono_gerente'],
                        ':correo_gerente' => $datos['correo_gerente'],
                        ':contacto' => $datos['contacto'],
                        ':documento_contacto' => $datos['documento_contacto'],
                        ':telefono_contacto' => $datos['telefono_contacto'],
                        ':correo_contacto' => $datos['correo_contacto'],
                        ':cta_detracciones' => $datos['cta_detracciones'],
                        ':nombre_banco' => $cuenta['nombreBanco'],
                        ':tipo_moneda' => $cuenta['tipoMoneda'],
                        ':tipo_cuenta' => $cuenta['tipoCuenta'],
                        ':numero_cuenta' => $cuenta['numeroCuenta']
                    ]);
                }
            }else {
                $detalleStmt->execute([
                    ':id_entidad' => $lastId,
                    ':pagina_web' => $datos['pagina_web'],
                    ':forma_pago' => $datos['forma_pago'],
                    ':actividad_economica' => $datos['actividad_economica'],
                    ':gerente_comercial' => $datos['gerente_comercial'],
                    ':documento_gerente' => $datos['documento_gerente'],
                    ':telefono_gerente' => $datos['telefono_gerente'],
                    ':correo_gerente' => $datos['correo_gerente'],
                    ':contacto' => $datos['contacto'],
                    ':documento_contacto' => $datos['documento_contacto'],
                    ':telefono_contacto' => $datos['telefono_contacto'],
                    ':correo_contacto' => $datos['correo_contacto'],
                    ':cta_detracciones' => $datos['cta_detracciones'],
                    ':nombre_banco' => -1,
                    ':tipo_moneda' => -1,
                    ':tipo_cuenta' => -1,
                    ':numero_cuenta' => ''
                ]);
            }
            

            
            $pdo->commit();

            enviarEmail($datos['correo_electronico'], $datos['razon_social'],$datos['ruc'] , $clave);
            return ['status' => 'success', 'id' => $lastId, 'claveGenerada' => $clave];
        }catch(PDOException $e){
            echo "Error al guardar los datos: " . $e->getMessage();
            $pdo->rollBack();
        }*/
    }

    function buscarRuc($pdo, $datos){
        try{
            $sql = 'SELECT cnumdoc FROM cm_entidad WHERE cnumdoc = :ruc';
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':ruc' => $datos['ruc']]);
            $count = $stmt->fetchColumn();
            return ['status' => 'success', 'ruc_exist' => $count > 0];
        }catch(PDOException $e){
            echo "Error al guardar los datos: " . $e->getMessage();
            $pdo->rollBack();
        }
    }

    function generarClaveAleatoria($longitud = 32) {
        $bytesAleatorios = random_bytes($longitud / 2);
        return bin2hex($bytesAleatorios);
    }
    
    function login($pdo, $datos){
        try{

            $sql = 'SELECT id_centi, cnumdoc, cpassword FROM cm_entidad WHERE cnumdoc = :ruc';
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':ruc' => $datos['ruc']]);

            // Obtiene la fila completa
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            /* if ($result) {
                $_SESSION['loggedin'] = TRUE;
                $_SESSION['name'] = $datos['ruc'];
                $_SESSION['id_centi'] = $result['id_centi'];
            } */
            if ($result) {
                // Verificar si la contraseña ingresada coincide con el hash almacenado
                if (password_verify($datos['password'], $result['cpassword'])) {
                    // Contraseña válida, crear sesión
                    $_SESSION['loggedin'] = TRUE;
                    $_SESSION['name'] = $datos['ruc'];
                    $_SESSION['id_centi'] = $result['id_centi'];
                    
                    return ['status' => 'success', 'logged' => $result];
                } else {
                    // Contraseña incorrecta
                    return ['status' => 'error', 'message' => 'Contraseña incorrecta'];
                }
            } else {
                // El usuario no existe
                return ['status' => 'error', 'message' => 'Usuario no encontrado'];
            }
        }catch(PDOException $e){
            /* echo "Error al ingresar los datos: " . $e->getMessage(); */
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    function actualizarProveedor($pdo, $datos, $files){
        try{
            $pdo->beginTransaction();
            $nameFichaRuc=$datos['cficharuc'];
            $nameCatalogo=$datos['ccatalogo'];
            $fechaActual = date('Y-m-d');
            
            
            $uploadDir = '../documentos/'; 

            //$sql = "UPDATE cm_entidad SET cnumdoc=:ruc,crazonsoc=:razon_social,cviadireccion=:direccion,cemail=:correo_electronico,ctelefono=:telefono,ncodpais=:pais, cficharuc=:ficha_ruc, ccatalogo=:catalogo_prod WHERE id_centi=:id";
            if (isset($_FILES['file_ruc']) && $_FILES['file_ruc']['error'] === UPLOAD_ERR_OK) {
                $nameFichaRuc = 'ficha_'.$datos['ruc'].'_'.$fechaActual.'.'.pathinfo($_FILES['file_ruc']['name'], PATHINFO_EXTENSION);
                $fileRuc = $_FILES['file_ruc'];
                $extensionRuc = pathinfo($fileRuc['name'], PATHINFO_EXTENSION);
                $filePathRuc = $uploadDir .'ficharuc/'. basename($nameFichaRuc);
                move_uploaded_file($fileRuc['tmp_name'], $filePathRuc);
            }

            // Procesar archivo Catálogo
            if (isset($_FILES['file_catalogo']) && $_FILES['file_ruc']['error'] === UPLOAD_ERR_OK) {
                $nameCatalogo = 'catalogo_'.$datos['ruc'].'_'.$fechaActual.'.'.pathinfo($_FILES['file_catalogo']['name'], PATHINFO_EXTENSION);
                $fileCatalogo = $_FILES['file_catalogo'];
                $extensionCat = pathinfo($fileCatalogo['name'], PATHINFO_EXTENSION);
                $filePathCatalogo = $uploadDir .'catalogoproducto/'. basename($nameCatalogo);
                
                move_uploaded_file($fileCatalogo['tmp_name'], $filePathCatalogo);
            }

            $sql = "UPDATE cm_entidad SET cnumdoc=:ruc,crazonsoc=:razon_social,cviadireccion=:direccion,cemail=:correo_electronico,ctelefono=:telefono,ncodpais=:pais, cficharuc=:ficha_ruc, ccatalogo=:catalogo_prod WHERE id_centi=:id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':ruc' => $datos['ruc'],
                ':razon_social' => $datos['razon_social'],
                ':direccion' => $datos['direccion'],
                ':correo_electronico' => $datos['correo_electronico'],
                ':telefono' => $datos['telefono'],
                ':pais' => $datos['pais'],
                ':ficha_ruc' => $nameFichaRuc,
                ':catalogo_prod' => $nameCatalogo,
                ':id' => $datos['id_centi']
            ]); 

            

            // Procesar archivo RUC
            
            
            $cuentas = json_decode($datos['cuentas'], true);

            $detalleSql = "UPDATE cm_detalle_entidad SET id_entidad=:id_entidad, cwebpage=:pagina_web, nformapago=:forma_pago, nacteconomica=:actividad_economica, cnomgerentec=:gerente_comercial, cnumdocgerentec=:documento_gerente, ctelgerentec=:telefono_gerente, cemailgerentec=:correo_gerente, cnomcontacto=:contacto,
            cnumdoccontacto=:documento_contacto, ctelcontacto=:telefono_contacto, cemailcontacto=:correo_contacto, ccuentadetrac=:cta_detracciones, nentifinan=:nombre_banco, ntipomoneda=:tipo_moneda, ntipocuenta=:tipo_cuenta, cnumcuenta=:numero_cuenta WHERE id_entidad=:id_entidad";

            $detalleStmt = $pdo->prepare($detalleSql);
            
            if(count($cuentas)>0){
                foreach($cuentas as $cuenta){
                    $detalleStmt->execute([
                        ':id_entidad' => $datos['id_centi'],
                        ':pagina_web' => $datos['pagina_web'],
                        ':forma_pago' => $datos['forma_pago'],
                        ':actividad_economica' => $datos['actividad_economica'],
                        ':gerente_comercial' => $datos['gerente_comercial'],
                        ':documento_gerente' => $datos['documento_gerente'],
                        ':telefono_gerente' => $datos['telefono_gerente'],
                        ':correo_gerente' => $datos['correo_gerente'],
                        ':contacto' => $datos['contacto'],
                        ':documento_contacto' => $datos['documento_contacto'],
                        ':telefono_contacto' => $datos['telefono_contacto'],
                        ':correo_contacto' => $datos['correo_contacto'],
                        ':cta_detracciones' => $datos['cta_detracciones'],
                        ':nombre_banco' => $cuenta['nombreBanco'],
                        ':tipo_moneda' => $cuenta['tipoMoneda'],
                        ':tipo_cuenta' => $cuenta['tipoCuenta'],
                        ':numero_cuenta' => $cuenta['numeroCuenta']
                    ]);
                }
            }else{
                $detalleStmt->execute([
                    ':id_entidad' => $id,
                    ':pagina_web' => $datos['pagina_web'],
                    ':forma_pago' => $datos['forma_pago'],
                    ':actividad_economica' => $datos['actividad_economica'],
                    ':gerente_comercial' => $datos['gerente_comercial'],
                    ':documento_gerente' => $datos['documento_gerente'],
                    ':telefono_gerente' => $datos['telefono_gerente'],
                    ':correo_gerente' => $datos['correo_gerente'],
                    ':contacto' => $datos['contacto'],
                    ':documento_contacto' => $datos['documento_contacto'],
                    ':telefono_contacto' => $datos['telefono_contacto'],
                    ':correo_contacto' => $datos['correo_contacto'],
                    ':cta_detracciones' => $datos['cta_detracciones'],
                    ':nombre_banco' => -1,
                    ':tipo_moneda' => -1,
                    ':tipo_cuenta' => -1,
                    ':numero_cuenta' => ''
                ]);
            }
            

            
            $pdo->commit();
            return ['status' => 'success'];
        }catch(PDOException $e){
            echo "Error al guardar los datos: " . $e->getMessage();
            $pdo->rollBack();
        }
    }

    function enviarEmail($correo,$nombre,$ruc, $clave){
        require_once("../PHPMailer/PHPMailerAutoload.php");

         $estadoEnvio= false;
         
         $origen = $correo;
         $nombre_envio = $nombre;

         $mail = new PHPMailer;
         $mail->isSMTP();
         $mail->SMTPDebug = 2;
         $mail->Debugoutput = 'html';
         $mail->Host = 'mail.sepcon.net';
         $mail->SMTPAuth = true;
         $mail->Username = 'sistema_ibis@sepcon.net';
         $mail->Password = 'aK8izG1WEQwwB1X';
         $mail->Port = 465;
         $mail->SMTPSecure = "ssl";
         $mail->SMTPOptions = array(
             'ssl' => array(
                 'verify_peer' => false,
                 //'verify_depth' => 3,
                 'verify_peer_name' => false,
                 'allow_self_signed' => true,
                'peer_name' => 'mail.sepcon.net',
             )
         );
         
         try {
             $mail->setFrom('sistema_ibis@sepcon.net','SEPCON');
             $mail->addAddress($origen,$nombre_envio);

             $texto = "Registro Exitoso";
             $mail->Subject = $texto;
             $mail->msgHTML(utf8_decode("Se ha registrado exitosamente al sistema, si desea modificar sus datos deberá ingresar con su RUC y la clave generada<br>
             RUC: ".$ruc.
             "<br>Clave Generada: ".$clave));
            $mail->send();
            $mail->ClearAddresses();

         } catch (PDOException $th) {
             echo $th->getMessage();
             return false;
         }
    }
    function cambiarPassword($pdo, $datos){
        try {
            $pdo->beginTransaction();
            $clave = $datos['password'];
            $hashClave = password_hash($clave, PASSWORD_DEFAULT);
            $sql = "UPDATE cm_entidad SET cpassword=:pass WHERE id_centi=:id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':pass' => $hashClave,
                ':id' => $datos['id_centi']
            ]);
            $pdo->commit();
            return ['status' => 'success'];
        }catch(PDOException $e){
            echo "Error al guardar los datos: " . $e->getMessage();
            $pdo->rollBack();
        }

    }
?>