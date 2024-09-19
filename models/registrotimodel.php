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
                                                          OR cm_producto.ccodprod LIKE '%B05010005%')
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
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function subirFirmaTi($detalles,$correo,$nombre,$cc) {
            if (array_key_exists('img',$_REQUEST)) {
                // convierte la imagen recibida en base64
                // Eliminamos los 22 primeros caracteres, que 
                // contienen el substring "data:image/png;base64,"
                $imgData = base64_decode(substr($_REQUEST['img'],22));
                
                $fechaActual = date('Y-m-d');
                $respuesta = false;
        
                $namefile = uniqid();

                // Path en donde se va a guardar la imagen
                $file = 'public/documentos/firmas/'.$namefile.'.png';
            
                // borrar primero la imagen si existía previamente
                if (file_exists($file)) { unlink($file); }
            
                // guarda en el fichero la imagen contenida en $imgData
                $fp = fopen($file, 'w');
                fwrite($fp, $imgData);
                fclose($fp);
                
                if ( file_exists($file) ){
                    $respuesta = true;

                    $datos = json_decode($detalles);
                    $nreg = count($datos);
                    $kardex = time();

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
                                        "hoja"=>$datos[$i]->hoja,
                                        "isometrico"=>$datos[$i]->isometrico,
                                        "observaciones"=>$datos[$i]->observac,
                                        "patrimonio"=>$datos[$i]->patrimonio,
                                        "estado"=>$datos[$i]->estado,
                                        "kardex"=>$kardex,
                                        "firma"=>$namefile,
                                        "serie"=>$datos[$i]->serie,
                                        "cc"=>$datos[$i]->costos,
                                        "cambio"=>$datos[$i]->cambio,
                                        "area"=>'TI']);
                    }
                }            
            }

            $this->correoMovimientoTi($detalles,$nombre,$correo,$kardex,$cc);
        
            return  $respuesta;
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
                //$mail->addAddress('lgrock@sepcon.net','Luis Grock');

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
    }
?>