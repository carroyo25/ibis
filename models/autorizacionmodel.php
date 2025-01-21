<?php
    class AutorizacionModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function insertar($cabecera,$detalles){

            try {
                $sql=$this->db->connect()->prepare("INSERT INTO alm_autorizacab 
                                                    SET alm_autorizacab.femision=:emision,
                                                        alm_autorizacab.ncostos=:costos,
                                                        alm_autorizacab.ncostosd=:costosd,
                                                        alm_autorizacab.narea=:area,
                                                        alm_autorizacab.csolicita=:solicita,
                                                        alm_autorizacab.norigen=:origen,
                                                        alm_autorizacab.ndestino=:destino,
                                                        alm_autorizacab.ctransferencia=:transferencia,
                                                        alm_autorizacab.observac=:observacion,
                                                        alm_autorizacab.celabora=:elabora,
                                                        alm_autorizacab.cautoriza=:autoriza,
                                                        alm_autorizacab.ntipo=:tipo");

                $sql->execute(["emision"=>$cabecera['emitido'],
                                "costos"=>$cabecera['codigo_costos_origen'],
                                "costosd"=>$cabecera['codigo_costos_destino'],
                                "area"=>$cabecera['codigo_area'],
                                "solicita"=>$cabecera['codigo_usuario'],
                                "origen"=>$cabecera['codigo_origen'],
                                "destino"=>$cabecera['codigo_destino'],
                                "transferencia"=>$cabecera['codigo_tipo_transferencia'],
                                "tipo"=>$cabecera['codigo_tipo'],
                                "observacion"=>$cabecera['observaciones'],
                                "elabora"=>$cabecera['codigo_usuario'],
                                "autoriza"=>$cabecera['codigo_autoriza'],
                                "tipo"=>$cabecera['codigo_tipo']]);
                                
                if ($sql->rowCount() > 0) {
                    $numero = $this->numeroDocumento();
                    $this->grabarDetallesTransferencia($cabecera,$detalles,$numero);
                    //$this->vistaPreviaAutorizacion($cabecera,$detalles,$numero);
                    //$correo = $this->enviarCorreo($numero,$cabecera['codigo_area']);
                }
                
                return array("numero"=>$numero,"correo"=>null);

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }    
        }

        private function numeroDocumento(){
            $sql = $this->db->connect()->query("SELECT COUNT(idreg) AS numero FROM alm_autorizacab");
            $sql->execute();

            $result = $sql->fetchAll();

            return $result[0]['numero'];
        }

        private function grabarDetallesTransferencia($cabecera,$detalles,$numero){
            try {
                $datos = json_decode($detalles);
                $nreg = count($datos);

                for ($i=0;$i<$nreg;$i++){
                    try {
                        $sql = $this->db->connect()->prepare("INSERT INTO alm_autorizadet
                                                            SET alm_autorizadet.idautoriza=:numero,
                                                                alm_autorizadet.idcodprod=:codprod,
                                                                alm_autorizadet.idunidad=:unidad,
                                                                alm_autorizadet.ncantidad=:cantidad,
                                                                alm_autorizadet.cserie=:serie,
                                                                alm_autorizadet.cdestino=:area,
                                                                alm_autorizadet.cobserva=:observa,
                                                                alm_autorizadet.norigen=:origen,
                                                                alm_autorizadet.nparte=:parte,
                                                                alm_autorizadet.ndestino=:destino");

                        $sql->execute(["numero"=>$numero,
                                        "codprod"=>$datos[$i]->idprod,
                                        "unidad"=>$datos[$i]->unidad,
                                        "cantidad"=>$datos[$i]->cantidad,
                                        "serie"=>$datos[$i]->serie,
                                        "area"=>$datos[$i]->destino,
                                        "observa"=>$datos[$i]->observac,
                                        "parte"=>$datos[$i]->parte,
                                        "origen"=>$cabecera['codigo_origen'],
                                        "destino"=>$cabecera['codigo_destino']]);
                    } catch (PDOException $th) {
                        echo "Error: ".$th->getMessage();
                        return false;
                    }
                }
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            } 
        }

        private function enviarCorreo($numero,$area){
            try {
                require_once("public/PHPMailer/PHPMailerAutoload.php");

                $estadoEnvio = true;

                $destino = $_SESSION['user']."@sepcon.net";
                $nombre_destino = $_SESSION['nombres'];

                $adjunto = $numero.'.pdf';

                $subject    = utf8_decode("Autorización de traslado");

                $messaje= '<div style="width:100%;display: flex;flex-direction: column;justify-content: center;align-items: center;
                                    font-family: Futura, Arial, sans-serif;">
                            <div style="width: 45%;border: 1px solid #c2c2c2;background: #518FFB">
                                <h3 style="text-align: left;padding-left:20px">Aviso Solicitud de Autorización</h3>
                            </div>
                            <div style="width: 45%;
                                        border-left: 1px solid #c2c2c2;
                                        border-right: 1px solid #c2c2c2;
                                        border-bottom: 1px solid #c2c2c2;">
                                <p style="padding:.5rem;line-height: 1rem;">Se informa Ud. que se ha generado la solicitud de autorización de transporte Nro. '.$numero.'</p>
                                <p style="padding:.5rem">Fecha de Solicitud : '. date("d/m/Y h:i:s") .'</p>
                            </div>
                        </div>';

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

                $mail->setFrom("sistema_ibis@sepcon.net","Autorizacion de Traslado");
                $mail->addAddress($destino,$nombre_destino);

                if ($area == 19) {
                    $mail->addAddress("tgonzales@sepcon.net",utf8_decode("Teddy Gonzáles"));
                    //$mail->addAddress("caarroyo@hotmail.com",utf8_decode("Teddy Gonzáles"));
                }

                $mail->Subject = $subject;
                $mail->msgHTML(utf8_decode($messaje));
              
                $mail->AddAttachment('public/documentos/autorizaciones/'.$adjunto);

                if (!$mail->send()) {
                    $estadoEnvio = false;
                    echo 'Mailer Error: ' . $mail->ErrorInfo; 
                }else {
                    $estadoEnvio = true; 
                }

                return $estadoEnvio;

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            } 
        }

        public function autorizacionId($id,$tipo){
            try {
                $docData = [];
                $sql = $this->db->connect()->prepare("SELECT
                                                        alm_autorizacab.idreg,
                                                        alm_autorizacab.idreg AS indice,
                                                        LPAD( alm_autorizacab.idreg, 6, 0 ) AS numero,
                                                        DATE_FORMAT( alm_autorizacab.fregsys, '%Y-%m-%d' ) AS emision,
                                                        alm_autorizacab.ncostos,
                                                        alm_autorizacab.narea,
                                                        alm_autorizacab.csolicita,
                                                        alm_autorizacab.norigen,
                                                        alm_autorizacab.ndestino,
                                                        alm_autorizacab.ctransferencia,
                                                        alm_autorizacab.observac,
                                                        alm_autorizacab.celabora,
                                                        alm_autorizacab.nestado,
                                                        alm_autorizacab.nflgautoriza,
                                                        alm_autorizacab.ntipo,
                                                        costos_origen.ccodproy AS cc_codigo_origen,
                                                        UPPER( costos_origen.cdesproy ) AS cc_descripcion_origen,
                                                        UPPER( ibis.tb_area.cdesarea ) AS area,
                                                        usuario.cnombres AS solicita,
                                                        almacenorigen.cdesalm AS almacenorigen,
                                                        almacendestino.cdesalm AS almacendestino,
                                                        tb_parametros.cdescripcion AS transferencia,
                                                        alm_autorizacab.cautoriza,
                                                        UPPER( ibis.tb_user.cnombres ) AS autoriza,
                                                        tipos.cdescripcion AS tipo,
                                                        alm_autorizacab.ncostosd,
                                                        cc_destino.ccodproy,
                                                        UPPER( cc_destino.cdesproy ) AS cc_descripcion_destino,
                                                        guias.cnumguia 
                                                    FROM
                                                        alm_autorizacab
                                                        LEFT JOIN tb_proyectos AS costos_origen ON alm_autorizacab.ncostos = costos_origen.nidreg
                                                        LEFT JOIN tb_area ON alm_autorizacab.narea = tb_area.ncodarea
                                                        LEFT JOIN tb_user AS usuario ON alm_autorizacab.csolicita = usuario.iduser
                                                        LEFT JOIN tb_almacen AS almacenorigen ON alm_autorizacab.norigen = almacenorigen.ncodalm
                                                        LEFT JOIN tb_almacen AS almacendestino ON alm_autorizacab.ndestino = almacendestino.ncodalm
                                                        LEFT JOIN tb_parametros ON alm_autorizacab.ctransferencia = tb_parametros.nidreg
                                                        LEFT JOIN tb_user ON alm_autorizacab.cautoriza = tb_user.iduser
                                                        LEFT JOIN tb_parametros AS tipos ON alm_autorizacab.ntipo = tipos.nidreg
                                                        LEFT JOIN tb_proyectos AS cc_destino ON alm_autorizacab.ncostosd = cc_destino.nidreg
                                                        LEFT JOIN ( SELECT lg_guias.cnumguia, lg_guias.id_regalm FROM lg_guias WHERE lg_guias.cmotivo = 95 ) AS guias ON alm_autorizacab.idreg = guias.id_regalm 
                                                    WHERE
                                                        alm_autorizacab.nflgactivo = 1 
                                                        AND alm_autorizacab.idreg = :id");
                $sql->execute(["id"=>$id]);
                $docData = $sql->fetchAll();

                $rowCount = $sql->rowCount();
                
                if ($rowCount) {
                    $respuesta = true;
                    $i = 0;
                    
                    while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                        $docData[] = $row;
                    }

                    if ( $tipo == 277 )
                        $detalles = $this->detallesAutorizacionBienes($id);
                    else
                        $detalles = $this->detallesAutorizacionEquipos($id);
                }

                return array("datos"=>$docData, "detalles"=>$detalles);

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function detallesAutorizacionBienes($id){
            try {
                $docData = [];

                $sql = $this->db->connect()->prepare("SELECT
                                                        alm_autorizadet.ncantidad,
                                                        alm_autorizadet.cserie,
                                                        alm_autorizadet.cdestino,
                                                        alm_autorizadet.cobserva,
                                                        alm_autorizadet.nparte,
                                                        alm_autorizadet.idcodprod,
                                                        cm_producto.ccodprod,
                                                        UPPER(cm_producto.cdesprod) AS cdesprod,
                                                        tb_unimed.cabrevia 
                                                    FROM
                                                        alm_autorizadet
                                                        INNER JOIN cm_producto ON alm_autorizadet.idcodprod = cm_producto.id_cprod
                                                        INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed
                                                    WHERE 
                                                    alm_autorizadet.nflgactivo = 1
                                                    AND alm_autorizadet.idautoriza = :id");
                $sql->execute(["id"=>$id]);

                if ($sql->rowCount() > 0)
                    while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                        $docData[] = $row;
                    }

                return $docData;
                
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function detallesAutorizacionEquipos($id){
            try {
                $docData = [];

                $sql = $this->db->connect()->prepare("SELECT
                                                        alm_autorizadet.ncantidad,
                                                        alm_autorizadet.cserie,
                                                        alm_autorizadet.cdestino,
                                                        alm_autorizadet.cobserva,
                                                        alm_autorizadet.nparte,
                                                        alm_autorizadet.idcodprod,
                                                        tb_equipmtto.cregistro,
                                                        tb_equipmtto.cdescripcion,
                                                        tb_equipmtto.cserie AS serie_equipo 
                                                    FROM
                                                        alm_autorizadet
                                                        INNER JOIN tb_equipmtto ON alm_autorizadet.idcodprod = tb_equipmtto.idreg 
                                                    WHERE
                                                        alm_autorizadet.nflgactivo = 1 
                                                        AND alm_autorizadet.idautoriza = :id");
                $sql->execute(["id"=>$id]);

                if ($sql->rowCount() > 0)
                    while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                        $docData[] = $row;
                    }

                return $docData;
                
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function vistaPreviaAutorizacion($cabecera,$detalles,$numero){
            try {
                require_once("public/formatos/autorizaciones.php");
                
                $datos = json_decode($detalles);
                $nreg = count($datos);

                $valor_maximo_lineas  = 24;
                $contador_linea = 0;

                $fecha_emision = date("d/m/Y", strtotime($cabecera['emision']));
                $numero_autorizacion = $numero == null ? $cabecera['numero'] : $numero;

                $archivo = $cabecera['numero'].".pdf";
                $ruta = "public/documentos/autorizaciones/".$archivo;
                
                $pdf = new PDF($cabecera['numero'],
                                $cabecera['costosOrigen'],
                                $cabecera['area'],
                                $cabecera['solicitante'],
                                $cabecera['origen'],
                                $cabecera['destino'],
                                $cabecera['codigo_tipo'],
                                $cabecera['autorizacion'],
                                $cabecera['emision'],
                                $cabecera['observaciones']);

                $pdf->AliasNbPages();
                //$pdf->AddPage('P','A5');
                $pdf->AddPage('P','A4');
                $pdf->SetFont('Arial','',6);
                
                $x = 4;
                $y = $pdf->GetY();
                $rc = 0;
                $item = 1;
                $pdf->SetFont('Arial','',5);
                $alto_linea = 3;
                
                for($i=1;$i<=$nreg;$i++){
                    if ( $datos[$rc]->cantidad > 0 ){
                        $pdf->SetX(4);
                        $pdf->Multicell(8,$alto_linea,str_pad($item++,3,0,STR_PAD_LEFT),0,'R');
                        $pdf->SetXY(12,$pdf->GetY()-$alto_linea);
                        $pdf->Multicell(15,$alto_linea,$datos[$rc]->codigo,0,'C');
                        $pdf->SetXY(27,$pdf->GetY()-$alto_linea);
                        $pdf->Multicell(70,$alto_linea,utf8_decode($datos[$rc]->descripcion),0,'L');
                        $pdf->SetXY(97,$pdf->GetY()-$alto_linea);
                        $pdf->Multicell(10,$alto_linea,$datos[$rc]->unidad,'0','C');
                        $pdf->SetXY(107,$pdf->GetY()-$alto_linea);
                        $pdf->Multicell(15,$alto_linea,$datos[$rc]->cantidad,0,'C');
                        $pdf->SetXY(122,$pdf->GetY()-$alto_linea);
                        $pdf->Multicell(22,$alto_linea,$datos[$rc]->serie,0,'C');
                        $pdf->SetXY(144,$pdf->GetY()-$alto_linea);
                        $pdf->Multicell(25,$alto_linea,$datos[$rc]->destino,0,'R');
                        $pdf->SetXY(169,$pdf->GetY()-$alto_linea);
                        $pdf->Multicell(35,$alto_linea,utf8_decode($datos[$rc]->observac),0,'L');

                        $pdf->Line(5,$pdf->GetY(),204,$pdf->GetY());
                
                        if ( $pdf->GetY() > 164 ) {
                            $pdf->AddPage('P','A4');
                        }
                    }

                    $rc++;
                    $contador_linea++;
                }

                $pdf->Ln(1);
                    
                $pdf->Output($ruta,'F');
                    
                return array("archivo"=>$archivo);

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function recepcionAlmacen($id,$estado){
            try {
                $mensaje = "Error en la actualización";
                $fecha = date("Y-m-d");

                $sql = $this->db->connect()->prepare("UPDATE alm_autorizacab 
                                                        SET alm_autorizacab.nestado =:estado,
                                                            alm_autorizacab.urecepcli =:user,
                                                            alm_autorizacab.frecepcion =:fecha
                                                        WHERE alm_autorizacab.idreg =:id");
                                                        
                $sql->execute(["id"=>$id, "estado"=>$estado, "user"=>$_SESSION['iduser'], "fecha"=>$fecha]);

                if ($sql->rowCount() > 0){
                    $mensaje = "Recepcionado por almacen";
                }

                return array("mensaje"=>"Registro correcto");
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function entregarLogistica($id,$estado){
            try {
                $mensaje = "Error en la actualización";
                $fecha = date("Y-m-d");

                $sql = $this->db->connect()->prepare("UPDATE alm_autorizacab 
                                                        SET alm_autorizacab.nestado =:estado,
                                                            alm_autorizacab.uenvlog =:user,
                                                            alm_autorizacab.fentrelog =:fecha
                                                        WHERE alm_autorizacab.idreg =:id");
                                                        
                $sql->execute(["id"=>$id, "estado"=>$estado, "user"=>$_SESSION['iduser'], "fecha"=>$fecha]);

                if ( $sql->rowCount() > 0 ){
                    $mensaje = "Entregado para su traslado";
                }

                return array("mensaje"=>$mensaje);
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function recepcionLogistica($id,$estado){
            try {
                $mensaje = "Error en la actualización";
                $fecha = date("Y-m-d");

                $sql = $this->db->connect()->prepare("UPDATE alm_autorizacab 
                                                        SET alm_autorizacab.nestado =:estado,
                                                            alm_autorizacab.ureceplog =:user,
                                                            alm_autorizacab.freceplog =:fecha
                                                        WHERE alm_autorizacab.idreg =:id");
                                                        
                $sql->execute(["id"=>$id, "estado"=>$estado, "user"=>$_SESSION['iduser'], "fecha"=>$fecha]);

                if ( $sql->rowCount() > 0 ){
                    $mensaje = "Entregado para su traslado";
                }

                return array("mensaje"=>$mensaje);
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function entregarUsuario($id,$estado){
            try {
                $mensaje = "Error en la actualización";
                $fecha = date("Y-m-d");

                $sql = $this->db->connect()->prepare("UPDATE alm_autorizacab 
                                                        SET alm_autorizacab.nestado =:estado,
                                                            alm_autorizacab.uentrecli =:user,
                                                            alm_autorizacab.fentreuser =:fecha
                                                        WHERE alm_autorizacab.idreg =:id");
                                                        
                $sql->execute(["id"=>$id, "estado"=>$estado, "user"=>$_SESSION['iduser'], "fecha"=>$fecha]);

                if ( $sql->rowCount() > 0 ){
                    $mensaje = "Traslado Finalizado";
                }

                return array("mensaje"=>$mensaje);
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function obtenerEstado($id){
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                        DATE_FORMAT(alm_autorizacab.frecepcion,'%d/%m/%Y') AS frecepcion, 
                                                        DATE_FORMAT(alm_autorizacab.fentrelog,'%d/%m/%Y') AS fentrelog, 
                                                        DATE_FORMAT(alm_autorizacab.freceplog,'%d/%m/%Y') AS freceplog, 
                                                        DATE_FORMAT(alm_autorizacab.fentreuser,'%d/%m/%Y') AS fentreuser
                                                    FROM
                                                        alm_autorizacab
                                                    WHERE
                                                        alm_autorizacab.idreg = :id");
                
                $sql->execute(["id"=>$id]);

                if ($sql->rowCount() > 0)
                    while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                        $docData[] = $row;
                    }

                return $docData;

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function llamarEquipos($codigo,$equipo){
            try {
                $salida = "";

                $cod = $codigo == "-1" ? "%" : "%".$codigo."%";
                $equ = $equipo == "-1" ? "%" : "%".$equipo."%";

                $sql =  $this->db->connect()->prepare("SELECT
                                    tb_equipmtto.idreg,
                                    tb_equipmtto.cregistro,
                                    tb_equipmtto.cdescripcion,
                                    tb_equipmtto.cserie
                                FROM
                                    tb_equipmtto 
                                WHERE
                                    tb_equipmtto.nflgactivo = 1
                                    AND tb_equipmtto.cregistro LIKE :codigo
                                    AND tb_equipmtto.cdescripcion LIKE :equipo");

                $sql->execute(["codigo"=>$cod,"equipo"=>$equ]);

                $rowCount = $sql->rowCount();
                if ($rowCount > 0){
                    while ($rs = $sql->fetch()) {
                        $salida .='<tr class="pointer" data-idprod="'.$rs['idreg'].'" data-serie="'.$rs['cserie'].'" data-unidad="UND">
                                        <td class="textoCentro">'.$rs['cregistro'].'</td>
                                        <td class="pl20px">'.$rs['cdescripcion'].'</td>
                                        <td class="textoCentro">UND</td>
                                    </tr>';
                    }
                }

                return $salida;

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function generarVistaPreviaGuiaTraslado($cabecera,$detalles,$proyecto){
            try {
                require_once("public/formatos/guiaremision.php");
                
                $archivo = "public/documentos/guias_remision/".$cabecera['numero_guia'].".pdf";
                $datos = json_decode($detalles);
                $nreg = count($datos);
                $fecha_emision = date("d/m/Y", strtotime($cabecera['fgemision']));
                $fecha_traslado = date("d/m/Y", strtotime($cabecera['ftraslado']));
                $anio = explode('-',$cabecera['fgemision']);

                if ($cabecera['ftraslado'] !== "")
                    $fecha_traslado = date("d/m/Y", strtotime($cabecera['ftraslado']));
                else 
                    $fecha_traslado = "";

                $pdf = new PDF($cabecera['numero_guia'],
                                $fecha_emision,
                                $cabecera['destinatario_ruc'],
                                $cabecera['destinatario_razon'],
                                $cabecera['destinatario_direccion'],
                                $cabecera['empresa_transporte_razon'],
                                $cabecera['ruc_proveedor'],
                                $cabecera['direccion_proveedor'],
                                $cabecera['almacen_origen_direccion'],
                                null,
                                null,
                                null,
                                $fecha_traslado,
                                $cabecera['modalidad_traslado'],
                                $cabecera['almacen_destino_direccion'],
                                null,
                                null,
                                null,
                                $cabecera['marca'],
                                $cabecera['placa'],
                                $cabecera['nombre_conductor'],
                                $cabecera['licencia_conducir'],
                                $cabecera['tipo_envio'],
                                '',
                                $proyecto,
                                $anio[0],
                                $cabecera["observaciones"],
                                $cabecera["destinatario"],
                                $cabecera["tipo_documento"],
                                'A4');
                $pdf->AliasNbPages();
                $pdf->AddPage();
                $pdf->SetWidths(array(10,15,15,147));
                $pdf->SetFillColor(255,255,255);
                $pdf->SetTextColor(0,0,0);
                
                $pdf->SetFont('Arial','',6);
                $lc = 0;
                $rc = 0;
                $item = 1;

                //aca podria sumar la orden

                for($i=1;$i<=$nreg;$i++){

                    $cantidad = intval($datos[$rc]->cantidad);
                    
                    $pdf->SetX(13);

                    $serie = $datos[$rc]->serie == "" ? "" : ' S/N : '.$datos[$rc]->serie;
                    $parte = $datos[$rc]->parte == "" ? "" : ' PARTE : '.$datos[$rc]->parte;
                    $observa = $datos[$rc]->observac == "" ? "" : ' OBSERVACIONES : '.$datos[$rc]->observac;
                    $destino = $datos[$rc]->destino == "" ? "" : 'DESTINO : '.$datos[$rc]->destino;

                    $pdf->SetAligns(array("R","R","C","L"));
                    if ($cantidad > 0){
                         $pdf->Row(array(str_pad($item++,3,"0",STR_PAD_LEFT),
                                        $cantidad,
                                        $datos[$rc]->unidad,
                                        utf8_decode($datos[$rc]->codigo .' '. $datos[$rc]->descripcion  . $serie .' '.$parte.' '. $observa .' '. $destino)));
                    }
                   
                    $lc++;
                    $rc++;

                    if ($lc == 26) {
                        $pdf->AddPage();
                        $lc = 0;
                    }
                }

                $pdf->Ln(1);
                $pdf->SetX(13);
                $pdf->Ln(2);
                $pdf->SetX(13);
                $pdf->Output($archivo,'F');
                    
                return array("archivo"=>$archivo);

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }
    }
?>