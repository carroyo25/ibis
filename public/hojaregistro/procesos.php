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
        }else if($_POST['funcion'] =="eliminarRegistroBanco"){
            echo json_encode(eliminarRegistroBanco($pdo, $_POST['id']));
        }
    }

    function eliminarRegistroBanco($pdo, $id){
        $estadoOK = false;

        $sql = "UPDATE cm_entidadbco 
                SET cm_entidadbco.nflgactivo = 8
                WHERE cm_entidadbco.nitem = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $rowCount = $stmt->rowCount();

        return array("id"=>$id,"registros"=>$rowCount);
    }

    function verificar($pdo,$datos){
        try {
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

    function generarClaveAleatoria($longitud) {
        $key = '';

        $pattern = '1234567890abcdefghijklmnopqrstuvwxyz';
        
        $max = strlen($pattern)-1;

        for($i=0;$i < $longitud;$i++) 
            $key .= $pattern{mt_rand(0,$max)};

        return $key;
    }
    
    function login($pdo, $datos){
        try{
            $sql = "SELECT 
                        cm_entidad.cnumdoc,
                        cm_entidad.cpassword,
                        cm_entidad.id_centi 
                    FROM cm_entidad 
                    WHERE cm_entidad.cnumdoc = :ruc
                    AND cm_entidad.nflgactivo = 7";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':ruc' => $datos['ruc']]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $count = $stmt->rowCount();
            
            $ruc_exist = false;

            if ( $count > 0 ) {
                $_SESSION['ruc'] = $datos['ruc'];
                
                if ( $result[0]['cpassword'] == null ){
                    $_SESSION['log'] = true;
                    $_SESSION{'haspassword'} = false;
                    $messaje = "Actualizar Registro";
                    $ruc_exist = true;
                }else{
                    if ( sha1($datos['clave']) == $result[0]['cpassword'] ){
                        $_SESSION['log'] = true;
                        $_SESSION{'haspassword'} = true;
                        $messaje = "Ingreso correcto";
                        $ruc_exist = true;
                    }else{
                        $_SESSION['log'] = false;
                        $_SESSION{'haspassword'} = false;
                        $messaje = "Error contraseña incorrecta";
                        $ruc_exist = false;
                    }
                } 
                return ['status' => 'success', 'message' => $messaje,'ruc_exist'=>$ruc_exist];
            }else{
                return ['status' => 'error', 'message' => 'Entidad no registrada','ruc_exist'=>false];
            }
        }catch(PDOException $e){
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    function crearProveedor($pdo, $datos, $files, $retencion) {   
        try{
            $pdo->beginTransaction();

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
                                        nrubro=:actividad,
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
                ':actividad' => $datos['actividad_economica']
            ]);

            $lastId = $pdo->lastInsertId();
           
            $pdo->commit();

            return $lastId;

        }catch(PDOException $e){
            echo "Error al guardar los datos: " . $e->getMessage();
            $pdo->rollBack();
        }
    }

    function actualizarProveedor($pdo,$datos,$files){
        try {
            $fechaActual = date('Y-m-d');
            
            $uploadDir = '../documentos/proveedores/presentados/';
            $nameFichaRuc = '';
            $nameCatalogo = '';
            $retencion  = $datos['contacto_detraccion'] == "" ? 1 : 2;

            $bancos = json_decode($datos['bancos'], true);

            $adj = [];
    
            $clave = generarClaveAleatoria(10);
            $hashClave = sha1($clave);
            $lastId  = "";
            $email = "";

            if ( $datos['actualiza'] !== 0 ){
                $id = $datos['id'];
                $proveedor = actualizarDatosProveedor($pdo,$datos,$id,$hashClave,$retencion);

                if ($proveedor){
                    bancos($pdo,$bancos,$id);
                    contactos($pdo,$datos,$id);
                    if (buscarRepresentante($pdo,$id) > 0){
                        actualizarRepresentantes($pdo,$datos,$id);
                    }else{
                        representantes($pdo,$datos,$id);
                    }
                    
                }
            }else{
                $id = crearProveedor($pdo,$datos,$id,$hashClave,$retencion);
            }

            // Procesar declaracion jurada
            $adj[1] = subirAdjunto($pdo,$_FILES['upload_declaracion'],$id);
            $adj[2] = subirAdjunto($pdo,$_FILES['upload_autoevaluacion'],$id);
            $adj[3] = subirAdjunto($pdo,$_FILES['upload_ruc'],$id);
            $adj[4] = subirAdjunto($pdo,$_FILES['upload_catalogo'],$id);
            $adj[5] = subirAdjunto($pdo,$_FILES['upload_plan'],$id);
            $adj[6] = subirAdjunto($pdo,$_FILES['upload_iper'],$id);
            $adj[7] = subirAdjunto($pdo,$_FILES['upload_procedimientos'],$id);
            $adj[8] = subirAdjunto($pdo,$_FILES['upload_epp'],$id);

            $email  = enviarEmail($datos['correo_electronico'],$datos['razon_social'],$datos['ruc'],$clave);
            
            return ['status' => 'success', 'id' => $lastId, 'claveGenerada' => $clave, 'email' => $email, 'adj' => $adj];

        } catch(PDOException $e){
            echo "Error al guardar los datos: " . $e->getMessage();
            $pdo->rollBack();
        }
    }

    function subirAdjunto($pdo, $archivo , $id) {
        
        if (isset($archivo) && $archivo['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../documentos/proveedores/presentados/';
            
            // Create directory if it doesn't exist
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $nameFile = uniqid(). '.' . pathinfo($archivo['name'], PATHINFO_EXTENSION);
            $originalName = $archivo['name'];
            $filePath = $uploadDir . basename($nameFile);
            
            // Validate file type and size (recommended)
            $allowedExtensions = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'xlsx'];
            $maxFileSize = 10 * 1024 * 1024; // 5MB
            
            $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
            
            if (!in_array($extension, $allowedExtensions)) {
                throw new Exception("Tipo de archivo no permitido: " . $extension);
            }
            
            if ($archivo['size'] > $maxFileSize) {
                throw new Exception("El archivo es demasiado grande. Máximo permitido: 5MB");
            }
            
            if (move_uploaded_file($archivo['tmp_name'], $filePath)) {
                registrarAdjunto($pdo, $nameFile, $originalName, $id);
                return true;
            } else {
                throw new Exception("Error al mover el archivo subido");
            }
        }

        return false;
    }

    function registrarAdjunto($pdo,$file,$nameDescrip,$entidad){
        try {
            $sql = "INSERT INTO cm_entidadadj 
                        SET cm_entidadadj.id_centi=?,
                            cm_entidadadj.cname=?,
                            cm_entidadadj.cdescrip=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$entidad,
                            $file,
                            $nameDescrip]);
        } catch(PDOException $e){
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    function actualizarDatosProveedor($pdo,$datos,$id,$hashClave,$retencion){
        try {
            $respuesta = false;

            $pdo->beginTransaction();

            $sql = "UPDATE cm_entidad 
                    SET crazonsoc=:razon_social,
                        cviadireccion=:direccion,
                        cemail=:correo_electronico,
                        ctelefono=:telefono,
                        ncodpais=:pais, 
                        cpassword=:pass,
                        nflgactivo=:activo,
                        nagenret=:retencion,
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
                ':pass' => $hashClave,
                ':activo' => 7,
                ':retencion' => $retencion,
                ':actividad' => $datos['actividad_economica']
            ]);

            $pdo->commit();

            if ($stmt->rowCount() > 0) {
                $respuesta = true;
            }

            return $respuesta;

        } catch(PDOException $e){
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
            $mail->addAddress("carroyo@sepcon.net","Cesar Arroyo");

            $subject = utf8_decode("Registro Proveedores SEPCON");

            $messaje= '<div style="width:100%;display: flex;flex-direction: column;justify-content: center;align-items: center;
                        font-family: Futura, Arial, sans-serif;">
                        <div style="width: 50%;border: 1px solid #c2c2c2;background: #005C84">
                            <h3 style="text-align: left;padding-left:20px;color:#ffffff">Registro Proveedores SEPCON</h3>
                        </div>
                        <div style="width: 50%;
                                        border-left: 1px solid #c2c2c2;
                                        border-right: 1px solid #c2c2c2;
                                        border-bottom: 1px solid #c2c2c2;">
                                <p style="padding:.5rem;line-height: 1rem;">El presente correo es para informar que se ha actualizado sus datos en el padron de proveedores SEPCON.</p>
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

    function bancos($pdo,$bancos,$id){
        try {
            ///bancos del proveedor
            $sqlBanco = "INSERT INTO cm_entidadbco 
                            SET id_centi = :idcenti, 
                                ncodbco  = :codigo_banco,
                                cnrocta  = :nro_cuenta,
                                ntipcta  = :tipo_cuenta,
                                cmoneda  = :moneda";

            if (count($bancos) > 0 ){
                foreach($bancos as $banco){ 
                    if ($banco['grabado'] == '0') {
                         $stmt = $pdo->prepare($sqlBanco);
                        $stmt->execute([
                            ':idcenti'      =>$id,
                            ':codigo_banco' =>$banco['idbanco'],
                            ':nro_cuenta'   =>$banco['nrocuenta'],
                            ':tipo_cuenta'  =>$banco['idcuenta'],
                            ':moneda'       =>$banco['idmoneda']
                        ]);
                    }
                }
            }
        } catch(PDOException $e){
            echo "Error al guardar los datos: " . $e->getMessage();
            $pdo->rollBack();
        }
    }

    function contactos($pdo,$datos,$id){
        try {
            $sql = "INSERT INTO cm_entidadcon
                    SET cnombres    = :nombres,
                        cemail      = :correo,
                        ctelefono1  = :telefono,
                        nflgactivo  = :activo,
                        id_centi    = :idcenti";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':idcenti'    =>$id,
                ':nombres'    =>$datos['contacto'],
                ':correo'     =>$datos['correo_contacto'],
                ':telefono'   =>$datos['telefono_contacto'],
                ':activo'     =>7
            ]);

        } catch(PDOException $e){
            echo "Error al guardar los datos: " . $e->getMessage();
            $pdo->rollBack();
        }
    }

    function representantes($pdo,$datos,$id){
        try {
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
        } catch(PDOException $e){
            echo "Error al guardar los datos: " . $e->getMessage();
            $pdo->rollBack();
        }
    }

    function actualizarRepresentantes($pdo,$datos,$id){
        try {
            $sqlDet = "UPDATE cm_detallenti 
                        SET nomgercomer = :gerente,
                            telgercomer = :telefonogerente,
                            corgercomer = :correogerente,
                            nomcontacto = :nombrecontacto,
                            telcontacto = :telefonocontacto,
                            corcontacto = :correocontacto,
                            nomperdetra = :nombredetraccion,
                            telperdetra = :telefonodetraccion,
                            corperdetra = :correodetraccion,
                            nctadetrac  = :cuentadetraccion
                        WHERE idcenti = :idcenti";
            
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
        } catch(PDOException $e){
            echo "Error al guardar los datos: " . $e->getMessage();
            $pdo->rollBack();
        }
    }

    function buscarRepresentante($pdo,$id){
        try {
            $sql = "SELECT COUNT(cd.idreg) AS registros
                    FROM cm_detallenti cd
                    WHERE  cd.idcenti = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);

            $result = $stmt->fetch();

            return $result['registros'];

        } catch(PDOException $e){
            echo "Error al guardar los datos: " . $e->getMessage();
            $pdo->rollBack();
        }
    }

?>
