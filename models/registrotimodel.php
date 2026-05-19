<?php
    class RegistroTiModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function filtrarItemsTi($codigo,$descripcion,$tipo){
            try {
                $codigo = "%".$codigo."%";
                $descripcion = "%".$descripcion."%";

                $salida = '<tr><td class="textoCentro" colspan="3">No existe el producto buscado</tr>';
                
                $sql = $this->db->connect()->prepare("SELECT
                                                    cm_producto.id_cprod,
                                                    cm_producto.ccodprod,
                                                    UPPER(cm_producto.cdesprod) AS cdesprod,
                                                    cm_producto.flgActivo,
                                                    tb_parametros.cdescripcion AS tipo,
                                                    tb_unimed.cabrevia,
                                                    tb_unimed.ncodmed 
                                                FROM
                                                    cm_producto
                                                    INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed
                                                    INNER JOIN tb_parametros ON cm_producto.ntipo = tb_parametros.nidreg 
                                                WHERE
                                                    cm_producto.flgActivo = 1 AND
                                                    cm_producto.cdesprod LIKE :descripcion AND
                                                    cm_producto.ccodprod LIKE :codigo AND
                                                    cm_producto.ntipo=:tipo
                                                    AND ( cm_producto.ccodprod LIKE '%B05010002%' 
                                                          OR cm_producto.ccodprod LIKE '%B05010006%'
                                                          OR cm_producto.ccodprod LIKE '%B05010005%'
                                                          OR cm_producto.ccodprod LIKE '%B05010003%')
                                                LIMIT 100");
                $sql->execute(["descripcion"=>$descripcion,
                                "tipo"=>$tipo,
                                "codigo"=>$codigo]);
                $rc = $sql->rowcount();
                $item = 1;

                if ($rc > 0){
                    $salida = "";
                    while( $rs = $sql->fetch()) {
                        $salida .='<tr class="pointer" data-idprod="'.$rs['id_cprod'].'" data-ncomed="'.$rs['ncodmed'].'" data-unidad="'.$rs['cabrevia'].'">
                                        <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                        <td class="pl20px">'.$rs['cdesprod'].'</td>
                                        <td class="textoCentro">'.$rs['cabrevia'].'</td>
                                    </tr>';
                        $item++;
                    }
                }

                return $salida;

            } catch (PDOException $th) {
                return "Error: ".$th->getMessage();
                return false;
            }
        }

        public function subirFirmaTi($detalles,$correo,$nombre,$cc) {
            $datos = json_decode($detalles);
            $nreg = count($datos);
            $kardex = time();
            $respuesta = true;

            for ($i=0; $i<$nreg; $i++){
                $sql = $this->db->connect()->prepare("INSERT INTO alm_consumo 
                                                                    SET reguser=:user,
                                                                        nrodoc=:documento,
                                                                        idprod=:producto,
                                                                        cantsalida=:cantidad,
                                                                        fechasalida=:salida,
                                                                        nhoja=:hoja,
                                                                        cisometrico=:isometrico,
                                                                        cobserentrega=:observaciones,
                                                                        flgdevolver=:patrimonio,
                                                                        cestado=:estado,
                                                                        nkardex=:kardex,
                                                                        cfirma=:firma,
                                                                        cserie=:serie,
                                                                        ncostos=:cc,
                                                                        ncambioepp=:cambio,
                                                                        cempresa=:area");
                $sql->execute(["user"=>$_SESSION['iduser'],
                                        "documento"=>$datos[$i]->nrodoc,
                                        "producto"=>$datos[$i]->idprod,
                                        "cantidad"=>$datos[$i]->cantidad,
                                        "salida"=>$datos[$i]->fecha,
                                        "hoja"=>null,
                                        "isometrico"=>null,
                                        "observaciones"=>$datos[$i]->observac,
                                        "patrimonio"=>$datos[$i]->patrimonio,
                                        "estado"=>null,
                                        "kardex"=>$kardex,
                                        "firma"=>null,
                                        "serie"=>$datos[$i]->serie,
                                        "cc"=>$datos[$i]->costos,
                                        "cambio"=>$datos[$i]->cambio,
                                        "area"=>'TI']);
                    }

            $this->correoMovimientoTi($detalles,$nombre,$correo,$kardex,$cc);
        
            return  $respuesta;
        }

        public function buscarDatosNombre($parametros){
            try {
                $nombre = $parametros['nombre'];
                $costos = $parametros['costos'];
                $dni = $parametros['dni'];

                $registrado = false;

                if ($nombre !== "") 
                    $url = "http://179.49.67.42/api/activesapinombres.php?nombres=".urlencode($nombre);
                elseif($dni !== "")
                    $url = "http://179.49.67.42/api/activesapi.php?documento=".$dni;

                $api = file_get_contents($url);
                $datos =  json_decode($api);

                $nreg = count($datos);

                $registrado = $nreg > 0 ? true: false;

                return array("datos" => $datos,
                            "registrado"=>$registrado,
                            "anteriores"=>$this->kardexEquipos($datos[0]->dni,$costos),
                            "firma"=>$this->buscarFirma($dni),
                            "ruta"=>'');

            }catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function correoMovimientoTi($detalles,$nombre,$correo,$kardex,$cc){
            require_once("public/PHPMailer/PHPMailerAutoload.php");

            $datos      = json_decode($detalles);
            $nreg       = count($datos);
            $subject    = utf8_decode('Entrega de EPPS/Materiales '.' - '.$nombre.' - '.$kardex);
            $fecha_actual = date("d-m-Y h:i:s");
            
            $origen = $_SESSION['correo'];
            $nombre_envio = $_SESSION['nombres'];

            $estadoEnvio= false;
            $clase = "mensaje_error";
            $salida = "";

            $mail = new PHPMailer;
            $mail->isSMTP();
            $mail->SMTPDebug = 0;
            $mail->Debugoutput = 'html';
            $mail->Host = 'mail.sepcon.net';
            $mail->SMTPAuth = true;
            $mail->Username = 'sistema_ibis@sepcon.net';
            $mail->Password = $_SESSION['password'];
            $mail->Port = 465;
            $mail->SMTPSecure = "ssl";
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => false
                )
            );

            try {
                $mail->setFrom('kardex@sepcon.net', 'Almacen Sepcon'); 
                $mail->addAddress($origen,$nombre_envio);
                $mail->addAddress($correo,utf8_decode($nombre));
                $mail->addAddress('kardex@sepcon.net','kardex@sepcon.net');

                $mail->Subject = $subject;
                $contador = 1;

                $mensaje = '<p>Estimado : <strong style="font-style: italic;">'. utf8_decode($nombre) .' </strong></p>';
                $mensaje .=  utf8_decode('<p>Realizaste un retiro de almacén: '.$cc.', con el registro de kardex Nro: <strong>'. $kardex.'</strong></p>');
                $mensaje .= '<p>Para constancia de lo entregado te enviamos los datos de tu retiro:</p>';

                $mensaje.= '<table style="width: 80%; border:1px solid #c2c2c2; border-collapse: collapse; font-size:.9rem">
                                <thead>
                                    <tr style="color:white; background:#0364B8; padding: 0 5px">
                                        <th>ITEM</th>
                                        <th>CODIGO</th>
                                        <th>DESCRIPCION</th>
                                        <th>UNIDAD</th>
                                        <th>FECHA</th>
                                        <th>SERIE</th>
                                        <th>CANTIDAD</th>
                                    </tr>
                                </thead>
                                <tbody>';
                
                for ($i=0; $i < $nreg; $i++) { 
                    $mensaje .= '<tr style="border:1px dotted #c2c2c2">
                                    <td>'.$contador++.'</td>
                                    <td>'.$datos[$i]->codigo.'</td>
                                    <td>'.$datos[$i]->descripcion.'</td>
                                    <td>'.$datos[$i]->unidad.'</td>
                                    <td>'.$fecha_actual.'</td>
                                    <td>'.$datos[$i]->serie.'</td>
                                    <td>'.$datos[$i]->cantidad.'</td>
                                </tr>';
                }

                $mensaje.='</tbody></table>';
                $mensaje.= '<p>Atentamente</p>';
                $mensaje.= '<p>Almacenes Sepcon</p>';

                $mensaje.= '<p style="font-size:.6rem; color:#0364B8; font-style:italic;">No responda este correo</p>';


                $mail->msgHTML($mensaje);

                $mail->send();
                $mail->ClearAddresses();

            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            } 
        }

        private function kardexEquipos($d,$c){
            try {
                $salida = "";
                $sql = $this->db->connect()->prepare("SELECT
                                                        alm_consumo.idreg,
                                                        alm_consumo.reguser,
                                                        alm_consumo.idprod,
                                                        alm_consumo.cantsalida,
                                                        DATE_FORMAT(alm_consumo.fechasalida,'%d/%m/%Y') AS fechasalida,
                                                        DATE_FORMAT(alm_consumo.fechadevolucion,'%d/%m/%Y') AS fechadevolucion,
                                                        alm_consumo.nhoja,
                                                        alm_consumo.cisometrico,
                                                        alm_consumo.cobserentrega,
                                                        alm_consumo.cobserdevuelto,
                                                        alm_consumo.cestado,
                                                        alm_consumo.cserie,
                                                        alm_consumo.flgdevolver,
                                                        alm_consumo.cfirma,
                                                        cm_producto.ccodprod,
                                                        alm_consumo.nkardex,
                                                        alm_consumo.calmacen,
                                                        UPPER(cm_producto.cdesprod) AS cdesprod,
                                                        tb_unimed.cabrevia,
                                                        tb_parametros.cdescripcion  AS motivo_epp
                                                    FROM
                                                        alm_consumo
                                                        LEFT JOIN cm_producto ON alm_consumo.idprod = cm_producto.id_cprod
                                                        LEFT JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed
                                                        LEFT JOIN tb_parametros ON alm_consumo.ncambioepp = tb_parametros.nidreg  
                                                    WHERE
                                                        alm_consumo.nrodoc = :documento 
                                                        AND ncostos = :cc
                                                        AND alm_consumo.flgactivo = 1
                                                        AND (cm_producto.ccodprod LIKE '%B05010002%' 
                                                          OR cm_producto.ccodprod LIKE '%B05010006%'
                                                          OR cm_producto.ccodprod LIKE '%B05010005%'
                                                          OR cm_producto.ccodprod LIKE '%B05010003%')
                                                    ORDER BY alm_consumo.freg DESC" );
                $sql->execute(["documento"=>$d,"cc"=>$c]);
                $rowCount = $sql->rowCount();
                $item = 1;
                $salida ="";
                $numero_item = $this->cantidadItems($d,$c);


                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()){

                        $marcado = $rs['flgdevolver'] == 1 ? "checked" : "";
                        $firma = "public/documentos/firmas/".$rs['cfirma'].".png";

                        $salida .= '<tr class="pointer" data-grabado="1" 
                                                        data-registrado="1" 
                                                        data-kardex = "'.$rs['nkardex'].'"
                                                        data-firma = "'.$rs['cfirma'].'"
                                                        data-devolucion = "'.$rs['fechadevolucion'].'"
                                                        data-firmadevolucion ="'.$rs['calmacen'].'"
                                                        data-registro="'.$rs['idreg'].'"
                                                        id="'.$item--.'">
                                        <td class="textoDerecha">'.$rowCount--.'</td>
                                        <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                        <td class="pl5px">'.$rs['cdesprod'].'</td>
                                        <td class="textoCentro">'.$rs['cabrevia'].'</td>
                                        <td class="textoDerecha">'.$rs['cantsalida'].'</td>
                                        <td class="textoCentro">'.$rs['fechasalida'].'</td>
                                        <td class="textoCentro">'.$rs['nhoja'].'</td>
                                        <td class="pl5px">'.$rs['cisometrico'].'</td>
                                        <td class="pl5px">'.$rs['cobserentrega'].'</td>
                                        <td class="pl5px">'.$rs['cserie'].'</td>
                                        <td class="textoCentro"><input type="checkbox" '.$marcado.'></td>
                                        <td class="pl5px">'.$rs['motivo_epp'].'</td>
                                        <td class="pl5px">'.$rs['cestado'].'</td>
                                        <td class="textoCentro">
                                            <div style ="width:110px !important; text-align:center">
                                                <img src = '.$firma.' style ="width:100% !important">
                                            </div>
                                        </td>
                                        <td class="textoCentro"><a href="'.$rs['idreg'].'" 
                                                        data-codigo     = "'.$rs['ccodprod'].'"
                                                        data-cantidad   = "'.$rs['cantsalida'].'"
                                                        data-patrimonio = "'.$rs['flgdevolver'].'"
                                                        data-hoja       = "'.$rs['nhoja'].'"
                                                        data-serie      = "'.$rs['cserie'].'">
                                                        <i class="fas fa-wrench"></i></a></td>
                                    </tr>';
                    }
                }

                return $salida;

            }catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }  
        }

        private function buscarFirma($dni){
            $archivo = dirname(__DIR__)."/public/documentos/ti/firmas/${dni}.png";
            if (file_exists($archivo)){
                return "/ibis/public/documentos/ti/firmas/${dni}.png";
            }else{
                return 'public/img/spbfirma.png';
            }
        }

        public function crearFirma($parametros){
            try {
                error_reporting(E_ALL);
                ini_set('display_errors', 1);

                // Sanitizar y validar entradas
                $nombre = explode(' ',$parametros['nombres']);
                $apellidos = trim($parametros['apellidos']);
                $documento = $parametros['documento'];
                $correo = $parametros['correo'];
                $cargo = trim($parametros['cargo']);
                $anexo = isset($parametros['anexo']) ? trim($parametros['anexo']) : '';


                // Verificar que el archivo de imagen base existe
                $baseImagePath = dirname(__DIR__)."/public/img/spbfirma.png";
                if (!file_exists($baseImagePath)) {
                    return json_encode(["error" => "Imagen base no encontrada"]);
                    exit;
                }

                // Crear imagen desde archivo PNG
                $img = imagecreatefrompng($baseImagePath);
                if (!$img) {
                    return json_encode(["error" => "Error al crear la imagen"]);
                    exit;
                }

                // Definir colores
                $colorNegro = imagecolorallocate($img, 35, 31, 32);
                $colorGris = imagecolorallocate($img, 129, 140, 163);
                $colorGrisOscuro = imagecolorallocate($img, 88, 89, 91);

                // Fuentes - Usar rutas relativas para mejor portabilidad
                $fontPath = dirname(__DIR__) . "/public/fonts/";
                $fontBold = $fontPath . "GothamBold.otf";
                $fontRegular = $fontPath . "ARLRDBD.TTF";

                // Verificar que las fuentes existen
                if (!file_exists($fontBold) || !file_exists($fontRegular)) {
                    echo json_encode(["error" => "Fuentes no encontradas"]);
                    imagedestroy($img);
                    exit;
                }

                // Convertir nombre a formato título (primera letra de cada palabra mayúscula)
                $nombreFormateado = ucwords($nombre[0]).' '.$apellidos;

                // Agregar texto del nombre
                imagettftext($img, 15, 0, 263, 45, $colorGrisOscuro, $fontRegular, $nombreFormateado);

                // Agregar texto del cargo
                imagettftext($img, 12, 0, 265, 63, $colorGris, $fontRegular, $cargo);

                // Agregar teléfono y anexo
                $telefonoTexto = 'T.(511) 2016870';
                if (!empty($anexo)) {
                    $telefonoTexto .= ' A.' . $anexo;
                }
                imagettftext($img, 12, 0, 265, 130, $colorGrisOscuro, $fontBold, $telefonoTexto);

                $directory = dirname(__DIR__);

                // Ruta completa del archivo
                $filePath = 'public\\documentos\\ti\\firmas\\'.$documento.'.png';

                // Guardar la imagen
                if (imagepng($img, $filePath, 6, NULL)) { // Nivel de compresión 6 (balance calidad/tamaño)
                    $salidaJson = ["archivo" => $filePath, "success" => true];
                } else {
                    $salidaJson = ["error" => "Error al guardar la imagen"];
                }

                // Limpiar memoria
                imagedestroy($img);

                return $salidaJson;

            } catch (PDOException $th) {
                return $th->getMessage();
            }
        }

        public function enviarCorreoFirma($parametros){
            try {
                require_once("public/PHPMailer/PHPMailerAutoload.php");

                // 🔹 FORZAR CODIFICACIÓN UTF-8 DESDE EL INICIO
                header('Content-Type: text/html; charset=utf-8');
                
                // Datos de la firma
                $nombre = utf8_decode($parametros['nombre']);
                $destino = $parametros['destino']; // Email del destinatario
                $documento = $parametros['dni']; // Imagen de la firma en base64
                $fecha_actual = date("d/m/Y H:i:s");
                $firma = 'public/documentos/ti/firmas/'.$parametros['dni'].'.png';
                $base64 = base64_encode($firma);
                
                // Configuración del correo
                $subject = 'Firma de Correo Electrónico - ' . $nombre;
                $origen = "sicalsepcon@sepcon.net";
                $nombre_envio = "Sical - Soporte Técnico";
                
                // Configuración SMTP (mejor desde variables de entorno o archivo de configuración)
                $mail = new PHPMailer;
                $mail->isSMTP();
                $mail->SMTPDebug = 0; // 0 = sin debug, 1 = errores, 2 = todo
                $mail->Debugoutput = 'html';
                $mail->Host = 'mail.sepcon.net';
                $mail->SMTPAuth = true;
                $mail->Username = 'sistema_ibis@sepcon.net';
                $mail->Password = SMTP_PASS; // ⚠️ No usar sesión, poner fijo o en config
                $mail->Port = 465;
                $mail->SMTPSecure = "ssl";
                $mail->SMTPOptions = array(
                    'ssl' => array(
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    )
                );

                // 🔹 CONFIGURACIÓN DE CODIFICACIÓN UTF-8 EN PHPMailer
                $mail->CharSet = 'UTF-8';
                $mail->Encoding = 'quoted-printable';
                $mail->setLanguage('es', 'public/PHPMailer/language/');
                
                // Configurar remitentes y destinatarios
                $mail->setFrom($origen, $nombre_envio);
                $mail->addAddress($destino, $nombre); // Destinatario principal
                //$mail->addAddress($origen, $nombre_envio); // Copia al sistema
                
                // Si se requiere copia al remitente
                if (isset($parametros['copia']) && $parametros['copia'] == true) {
                    $mail->addAddress($parametros['correo_tecnico'], utf8_decode($parametros['nombre_tecnico']));
                }
                
                $mail->Subject = $subject;
                
                // Construir mensaje HTML con la firma
                $mensaje = '
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset="UTF-8">
                    <style>
                        body { font-family: Arial, sans-serif; }
                        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                        .header { background: #0364B8; color: white; padding: 15px; text-align: center; }
                        .content { padding: 20px; background: #f9f9f9; }
                        .firma { margin: 20px 0; text-align: center; padding: 15px; background: white; border: 1px solid #ddd; }
                        .footer { font-size: 0.7rem; color: #0364B8; font-style: italic; text-align: center; margin-top: 20px; }
                        .cargo { color: #666; font-size: 0.9rem; }
                    </style>
                </head>
                <body>
                    <div class="container">
                        <div class="header">
                            <h3>Sistema de Firmas Digitales</h3>
                        </div>
                        <div class="content">
                            <p>Estimado: <strong>' . utf8_decode($parametros['nombre']) . '</strong></p>
                            <p>Se adjunta su firma digital generada el día <strong>' . $fecha_actual . '</strong></p>
                            
                            <p>La firma ha sido generada exitosamente y puede ser utilizada en sus comunicaciones oficiales.</p>
                            <p>Atentamente,</p>
                            <p><strong>Área de Soporte Técnico</strong></p>
                        </div>
                        <div class="footer">
                            <p>Este es un correo generado automáticamente, por favor no responder.</p>
                            <p>Sistema de Firmas Digitales - Sical</p>
                        </div>
                    </div>
                </body>
                </html>';
                
                $mail->msgHTML($mensaje);
                
                // Adjuntar la firma como imagen (opcional)
                
                if (file_exists($firma)) {
                     $mail->AddAttachment($firma);
                }
                
                // Enviar correo
                if ($mail->send()) {
                    $estadoEnvio = true;
                    $clase = "mensaje_exito";
                    $salida = "Correo enviado exitosamente a: " . $destino;
                    $mail->ClearAddresses();
                } else {
                    $estadoEnvio = false;
                    $clase = "mensaje_error";
                    $salida = "Error al enviar: " . $mail->ErrorInfo;
                }
                
                // Retornar respuesta JSON
                return array([
                    'success' => $estadoEnvio,
                    'message' => $salida,
                    'class' => $clase
                ]);
                
            } catch (Exception $th) {
                return array([
                    'success' => false,
                    'message' => 'Error: ' . $th->getMessage(),
                    'class' => 'mensaje_error'
                ]);
            }
        } 
    }
?>