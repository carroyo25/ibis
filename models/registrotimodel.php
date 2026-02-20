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
                echo "Error: ".$th->getMessage();
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
    }
?>