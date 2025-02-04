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
        }else if($_POST['funcion'] == "actualizarProveedor"){
            echo json_encode(actualizarProveedor($pdo, $_POST, $_FILES));
        }else if($_POST['funcion'] == "cambiarPass"){
            echo json_encode(cambiarPassword($pdo, $_POST));
        }else if($_POST['funcion'] =="verificar"){
            echo json_encode(verificar($pdo, $_POST));
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
        try{
            $bancos = json_decode($datos['bancos'], true);
            $retencion  = $datos['contacto_detraccion'] == "" ? 1 : 2;

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

            $clave = generarClaveAleatoria(8);
            $hashClave = password_hash($clave, PASSWORD_DEFAULT);

            $pdo->beginTransaction();

            //datos principales del proveedor

            $sql = "INSERT INTO cm_entidad 
                                    SET cnumdoc=:ruc,
                                        crazonsoc=:razon_social,
                                        cviadireccion=:direccion,
                                        cemail=:correo_electronico,
                                        ctelefono=:telefono,
                                        ncodpais=:pais, 
                                        cficharuc=:ficha_ruc, 
                                        ccatalogo=:catalogo_prod, 
                                        cpassword=:pass,
                                        nflgactivo=:activo,
                                        nagenret=:retencion,
                                        ncondpag=:forma_pago,
                                        nrubro=:actividad
                                        nflgactualizado = 1";
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
                ':pass' => $hashClave,
                ':activo' => 7,
                ':retencion' => $retencion,
                ':forma_pago' => $datos['forma_pago'],
                ':actividad' => $datos['actividad_economica']
            ]);

            $lastId = $pdo->lastInsertId();

            if ($lastId > 0) {
                    //detalles del proveedor
                    $sqlDet = "INSERT INTO cm_detallenti 
                                    SET idcenti = :idcenti,
                                        nomgercomer = :gerente,
                                        telgercomer = :telefonogerente,
                                        corgercomer = :correogerente,
                                        nomcontacto = :nombrecontacto,
                                        telcontacto = :telefonocontacto,
                                        corcontacto = :correocontacto,
                                        nomperdetra = :nombredetraccion,
                                        telperdetra = :telefonodetraccion,
                                        corperdetra = :correodetraccion,
                                        nctadetrac  = :cuentadetraccion";
            
                    $stmt = $pdo->prepare($sqlDet);
                    $stmt->execute([
                        ':idcenti'              =>$lastId,
                        ':gerente'              =>$datos['gerente_comercial'],
                        ':telefonogerente'      =>$datos['telefono_gerente'],
                        ':correogerente'        =>$datos['correo_gerente'],
                        ':nombrecontacto'       =>$datos['contacto'],
                        ':telefonocontacto'     =>$datos['telefono_contacto'],
                        ':correocontacto'       =>$datos['correo_contacto'],
                        ':nombredetraccion'     =>$datos['contacto_detraccion'],
                        ':telefonodetraccion'   =>$datos['telefono_contacto_detraccion'],
                        ':correodetraccion'     =>$datos['correo_contacto_detraccion'],
                        ':cuentadetraccion'     =>$datos['cta_detracciones'],
                    ]);

                    $slqContacto = "INSERT INTO cm_entidadcon
                                    SET id_centi     = :idcenti,
                                        cnombres     = :nombres,
                                        cdireccion  = :direccion,
                                        cemail      = :correo,
                                        ctelefono1  = :telefono,
                                        nflgactivo  = :activo";

                    $stmt = $pdo->prepare($slqContacto);
                    $stmt->execute([
                        ':idcenti'    =>$lastId,
                        ':nombres'    =>$datos['contacto'],
                        ':direccion'  =>$datos['telefono_contacto'],
                        ':correo'     =>$datos['correo_contacto'],
                        ':telefono'   =>$datos['telefono_contacto'],
                        ':activo'     =>7
                    ]);

                    //bancos del proveedor
                    $sqlBanco = "INSERT INTO cm_entidadbco 
                                    SET id_centi = :idcenti, 
                                        ncodbco  = :codigo_banco,
                                        cnrocta  = :nro_cuenta,
                                        ctipcta  = :tipo_cuenta,
                                        cmoneda  = :moneda";

                    if (count($bancos) > 0 ){
                        foreach($bancos as $banco){
                            $stmt = $pdo->prepare($sqlBanco);

                            $stmt->execute([
                                ':idcenti'      =>$lastId,
                                ':codigo_banco' =>$banco['idbanco'],
                                ':nro_cuenta'   =>$banco['nrocuenta'],
                                ':tipo_cuenta'  =>$banco['idcuenta'],
                                ':moneda'       =>$banco['idmoneda']
                            ]);
                        }
                    }
            }
           
            $pdo->commit();

            $email  = enviarEmail($datos['correo_electronico'],$datos['razon_social'],$datos['ruc'],$clave);

            return ['status' => 'success', 'id' => $lastId, 'claveGenerada' => $clave, 'email' => $email];
        }catch(PDOException $e){
            echo "Error al guardar los datos: " . $e->getMessage();
            $pdo->rollBack();
        }
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

    function generarClaveAleatoria($longitud = 8) {
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
            $fechaActual = date('Y-m-d');
            
            $uploadDir = '../documentos/';
            $nameFichaRuc = '';
            $nameCatalogo = '';
            $retencion  = $datos['contacto_detraccion'] == "" ? 1 : 2;

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

            $clave = generarClaveAleatoria(8);
            $hashClave = password_hash($clave, PASSWORD_DEFAULT);

            $pdo->beginTransaction();

            $sql = "UPDATE cm_entidad 
                    SET crazonsoc=:razon_social,
                        cviadireccion=:direccion,
                        cemail=:correo_electronico,
                        ctelefono=:telefono,
                        ncodpais=:pais, 
                        cficharuc=:ficha_ruc, 
                        ccatalogo=:catalogo_prod, 
                        cpassword=:pass,
                        nflgactivo=:activo,
                        nagenret=:retencion,
                        ncondpag=:forma_pago,
                        nrubro=:actividad,
                        nflgactualizado = 1
                    WHERE cnumdoc=:ruc";

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
                ':pass' => $hashClave,
                ':activo' => 7,
                ':retencion' => $retencion,
                ':forma_pago' => $datos['forma_pago'],
                ':actividad' => $datos['actividad_economica']
            ]);

            if ($datos['actualiza'] == 0 ){

                $id = $datos['id'];
                
                $sqlDet = "INSERT INTO cm_detallenti 
                                    SET idcenti = :idcenti,
                                        nomgercomer = :gerente,
                                        telgercomer = :telefonogerente,
                                        corgercomer = :correogerente,
                                        nomcontacto = :nombrecontacto,
                                        telcontacto = :telefonocontacto,
                                        corcontacto = :correocontacto,
                                        nomperdetra = :nombredetraccion,
                                        telperdetra = :telefonodetraccion,
                                        corperdetra = :correodetraccion,
                                        nctadetrac  = :cuentadetraccion";
            
                    $stmt = $pdo->prepare($sqlDet);
                    $stmt->execute([
                        ':idcenti'              =>$id,
                        ':gerente'              =>$datos['gerente_comercial'],
                        ':telefonogerente'      =>$datos['telefono_gerente'],
                        ':correogerente'        =>$datos['correo_gerente'],
                        ':nombrecontacto'       =>$datos['contacto'],
                        ':telefonocontacto'     =>$datos['telefono_contacto'],
                        ':correocontacto'       =>$datos['correo_contacto'],
                        ':nombredetraccion'     =>$datos['contacto_detraccion'],
                        ':telefonodetraccion'   =>$datos['telefono_contacto_detraccion'],
                        ':correodetraccion'     =>$datos['correo_contacto_detraccion'],
                        ':cuentadetraccion'     =>$datos['cta_detracciones'],
                    ]);

                    $slqContacto = "INSERT INTO cm_entidadcon
                                    SET id_centi     = :idcenti,
                                        cnombres     = :nombres,
                                        cdireccion  = :direccion,
                                        cemail      = :correo,
                                        ctelefono1  = :telefono,
                                        nflgactivo  = :activo";

                    $stmt = $pdo->prepare($slqContacto);
                    $stmt->execute([
                        ':idcenti'    =>$lastId,
                        ':nombres'    =>$datos['contacto'],
                        ':direccion'  =>$datos['telefono_contacto'],
                        ':correo'     =>$datos['correo_contacto'],
                        ':telefono'   =>$datos['telefono_contacto'],
                        ':activo'     =>7
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
         $password = 'aK8izG1WEQwwB1X';

         $mail = new PHPMailer;
         $mail->isSMTP();
         $mail->SMTPDebug = 0;
         $mail->Debugoutput = 'html';
         $mail->Host = 'mail.sepcon.net';
         $mail->SMTPAuth = true;
         $mail->Username = 'sistema_ibis@sepcon.net';
         $mail->Password = $password;
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
            $mail->addAddress($origen,$nombre);

            $subject    = utf8_decode("Registro Proveedores SEPCON");

            $messaje= '<div style="width:100%;display: flex;flex-direction: column;justify-content: center;align-items: center;
                        font-family: Futura, Arial, sans-serif;">
                        <div style="width: 50%;border: 1px solid #c2c2c2;background: #005C84">
                            <h3 style="text-align: left;padding-left:20px;color:#ffffff">Registro Proveedores SEPCON</h3>
                        </div>
                        <div style="width: 50%;
                                        border-left: 1px solid #c2c2c2;
                                        border-right: 1px solid #c2c2c2;
                                        border-bottom: 1px solid #c2c2c2;">
                                <p style="padding:.5rem;line-height: 1rem;">El presente correo es para informar que se ha realizado en el padron de proveedores SEPCON.</p>
                                <p style="padding:.5rem">Nombre       : '. $nombre.'</p>
                                <p style="padding:.5rem">RUC          : '. $ruc.'</p>
                                <p style="padding:.5rem">Clave        : '. $clave .'</p>
                            </div>
                        </div>';
            
            $mail->Subject = $subject;
            $mail->msgHTML(utf8_decode($messaje));


            if (!$mail->send()) {
                return array("mensaje"=>"Hubo un error, en el envio",
                            "clase"=>"mensaje_error");
            }else{
                return array("mensaje"=>"Envio correcto");
            }
                    
            $mail->clearAddresses();

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