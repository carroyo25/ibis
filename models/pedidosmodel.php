<?php
    class PedidosModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarPedidosUsuario(){
            try {
                $salida = "";
                $sql = $this->db->connect()->prepare("SELECT
                                                    ibis.tb_pedidocab.idreg,
                                                    ibis.tb_pedidocab.idcostos,
                                                    ibis.tb_pedidocab.idarea,
                                                    ibis.tb_pedidocab.emision,
                                                    ibis.tb_pedidocab.vence,
                                                    ibis.tb_pedidocab.idtipomov,
                                                    ibis.tb_pedidocab.estadodoc,
                                                    ibis.tb_pedidocab.nrodoc,
                                                    UPPER(ibis.tb_pedidocab.concepto) AS concepto,
                                                    CONCAT(rrhh.tabla_aquarius.nombres,' ',rrhh.tabla_aquarius.apellidos) AS nombres,
                                                    UPPER(CONCAT(ibis.tb_proyectos.ccodproy,' ',ibis.tb_proyectos.cdesproy)) AS costos,
                                                    ibis.tb_pedidocab.nivelAten,
                                                    atenciones.cdescripcion AS atencion,
                                                    estados.cdescripcion AS estado,
                                                    estados.cabrevia 
                                                FROM
                                                    ibis.tb_pedidocab
                                                    INNER JOIN rrhh.tabla_aquarius ON ibis.tb_pedidocab.idsolicita = rrhh.tabla_aquarius.internal
                                                    INNER JOIN ibis.tb_proyectos ON ibis.tb_pedidocab.idcostos = ibis.tb_proyectos.nidreg
                                                    INNER JOIN ibis.tb_parametros AS atenciones ON ibis.tb_pedidocab.nivelAten = atenciones.nidreg
                                                    INNER JOIN ibis.tb_parametros AS estados ON ibis.tb_pedidocab.estadodoc = estados.nidreg 
                                                WHERE
                                                    ibis.tb_pedidocab.usuario = :user 
                                                    AND ibis.tb_pedidocab.estadodoc BETWEEN 49 AND 50");
                $sql->execute(["user"=>$_SESSION['iduser']]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()) {
                        $tipo = $rs['idtipomov'] == 37 ? "B":"S";
                        $salida .='<tr class="pointer" data-indice="'.$rs['idreg'].'">
                                        <td class="textoCentro">'.str_pad($rs['nrodoc'],4,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.date("d/m/Y", strtotime($rs['emision'])).'</td>
                                        <td class="textoCentro">'.$tipo.'</td>
                                        <td class="pl20px">'.$rs['concepto'].'</td>
                                        <td class="pl20px">'.$rs['costos'].'</td>
                                        <td class="pl20px">'.$rs['nombres'].'</td>
                                        <td class="textoCentro '.$rs['cabrevia'].'">'.$rs['estado'].'</td>
                                        <td class="textoCentro '.strtolower($rs['atencion']).'">'.$rs['atencion'].'</td>
                                        <td class="textoCentro"><a href="'.$rs['idreg'].'"><i class="fa fa-trash-alt"></i></a></td>
                                    </tr>';
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }
        
        public function generarPedido($datos,$detalles){
            require_once('public/formatos/pedidos.php');
            
            $details = json_decode($detalles);
            $filename =  $datos['numero'].$datos['costos'].".pdf";

            $num = $datos['numero'];
            $fec = $datos['emision'];
            $usr = $_SESSION['iduser'];
            $pry = $datos['costos'];
            $are = $datos['area'];
            $cos = $datos['costos'];
            $tra = $datos['transporte'];
            $con = $datos['concepto'];
            $sol = $datos['solicitante'];
            $esp = $datos['espec_items'];
            
            $reg = ''; 
            $dti = $datos['codigo_tipo'] == 37 ? "PEDIDO DE COMPRA":"PEDIDO DE SERVICIO";
            $mmt = "";
            $cla = $datos['dias_atencion'] <= 46 ? "URGENTE":"NORMAL";
            $msj = $datos['codigo_estado'] == 49 ? "VISTA PREVIA":"EMITIDO";
            $ruta = $datos['codigo_estado'] == 49 ? "public/documentos/pedidos/vistaprevia/":"public/documentos/pedidos/emitidos/";

            $pdf = new PDF($num,$fec,$pry,$cos,$are,$con,$mmt,$cla,$tra,$usr,$sol,$reg,$esp,$dti,$msj,"");
		    $pdf->AddPage();
            $pdf->AliasNbPages();
            $pdf->SetWidths(array(10,15,70,8,10,17,15,15,15,15));
            $pdf->SetFont('Arial','',5);
            $lc = 0;
            $rc = 0; 

            $nreg = count($details);

            for($i=1;$i<=$nreg;$i++){
                $registro = isset( $details[$rc]->activo) ? $details[$rc]->activo : "";

			    $pdf->SetAligns(array("L","L","L","L","R","L","L","L","L","L"));
                $pdf->Row(array($details[$rc]->item,
                                $details[$rc]->codigo,
                                utf8_decode($details[$rc]->descripcion."\n".$details[$rc]->especifica),
                                $details[$rc]->unidad,
                                $details[$rc]->cantidad,
                                '',
                                '',
                                '',
                                $details[$rc]->nroparte,
                                $registro));
                
                $lc++;
                $rc++;

                if ($lc == 17) {
				    $pdf->AddPage();
				    $lc = 0;
			    }	
		    }

            $pdf->Output($ruta.$filename,'F');
            
            return $filename;
        }

        public function insertar($datos,$detalles){
            try {
                $salida = false;
                $respuesta = false;
                $mensaje = "Error en el registro";
                $clase = "mensaje_error";

                if ( $datos['codigo_usuario'] != ""){
                    $numero = $this->generarNumero($datos['codigo_costos'],"SELECT COUNT(idreg) AS numero FROM tb_pedidocab WHERE tb_pedidocab.idcostos =:cod");
               
                    $cmes = date("m",strtotime($datos['emision']));
                    $cper = date("Y",strtotime($datos['emision']));

                    $sql = $this->db->connect()->prepare("INSERT INTO tb_pedidocab SET idcostos=:cost,idarea=:area,idtrans=:trans,idsolicita=:soli,idtipomov=:mov,
                                                                                    emision=:emis,vence=:vence,estadodoc=:estdoc,nrodoc=:nro,usuario=:user,
                                                                                    anio=:ano,mes=:mes,concepto=:concep,detalle=:det,nivelAten=:aten,
                                                                                    docfPdfPrev=:dprev,nflgactivo=:est,verificacion=:ver,idpartida=:partida");
                    $sql->execute([
                        "cost"=>$datos['codigo_costos'],
                        "area"=>$datos['codigo_area'],
                        "trans"=>$datos['codigo_transporte'],
                        "soli"=>$datos['codigo_solicitante'],
                        "mov"=>$datos['codigo_tipo'],
                        "emis"=>$datos['emision'],
                        "vence"=>$datos['vence'],
                        "estdoc"=>$datos['codigo_estado'],
                        "user"=>$datos['codigo_usuario'],
                        "nro"=>$numero['numero'],
                        "ano"=>$cper,
                        "mes"=>$cmes,
                        "concep"=>$datos['concepto'],
                        "det"=>$datos['espec_items'],
                        "aten"=>47,
                        "dprev"=>$datos['vista_previa'],
                        "est"=>1,
                        "ver"=>$datos['codigo_verificacion'],
                        "partida"=>$datos['codigo_partida']
                    ]);

                    $rowCount = $sql->rowCount();
                    

                    if ($rowCount > 0){
                        $indice = $this->ultimoIndiceTabla("SELECT MAX(idreg) AS indice FROM tb_pedidocab");
                        $this->saveItems($datos['codigo_verificacion'],
                                        $datos['codigo_estado'],
                                        $datos['codigo_atencion'],
                                        $datos['codigo_tipo'],
                                        $datos['codigo_costos'],
                                        $datos['codigo_area'],
                                        $detalles,
                                        $indice);
                        $respuesta = true;
                        $mensaje = "Pedido Grabado";
                        $clase = "mensaje_correcto";
                        
                    }
                }else {
                    $mensaje = "Error al registrar el pedido";
                    $indice = 0;
                }

                $salida = array("respuesta" =>$respuesta,
                                "mensaje"   =>$mensaje,
                                "clase"     =>$clase,
                                "indice"    =>$indice,
                                "items"     =>$this->consultarReqId($indice,49,50,49,null));
                
                return $salida;
                
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function subirAdjuntos($codigo,$adjuntos){
            $countfiles = count( $adjuntos );

            for($i=0;$i<$countfiles;$i++){
                try {
                    $file = "file-".$i;
                    $ext = explode('.',$adjuntos[$file]['name']);
                    $filename = uniqid().".".end($ext);
                    // Upload file
                    if (move_uploaded_file($adjuntos[$file]['tmp_name'],'public/documentos/pedidos/adjuntos/'.$filename)){
                        $sql= $this->db->connect()->prepare("INSERT INTO lg_regdocumento 
                                                                    SET nidrefer=:cod,cmodulo=:mod,cdocumento=:doc,
                                                                        creferencia=:ref,nflgactivo=:est");
                        $sql->execute(["cod"=>$codigo,
                                        "mod"=>"PED",
                                        "ref"=>$filename,
                                        "doc"=>$adjuntos[$file]['name'],
                                        "est"=>1]);
                    }
                } catch (PDOException $th) {
                    echo "Error: ".$th->getMessage();
                    return false;
                }
            }

            return array("adjuntos"=>$this->contarAdjuntos($codigo,'PED'));
        }
        
        public function modificar($datos,$detalles){
            try {
                $salida = false;
                $respuesta = false;
                $mensaje = "Error en el registro";
                $clase = "mensaje_error";
                $rowDetails = 0;

                $sql = $this->db->connect()->prepare("UPDATE tb_pedidocab SET vence=:vence,concepto=:concep,detalle=:det,nivelAten=:aten,
                                                                                docfPdfPrev=:dprev
                                                                                WHERE idreg=:id");
                 $sql->execute([
                    "vence"=>$datos['vence'],
                    "concep"=>$datos['concepto'],
                    "det"=>$datos['espec_items'],
                    "aten"=>$datos['codigo_atencion'],
                    "dprev"=>$datos['vista_previa'],
                    "id"=>$datos['codigo_pedido']
                ]);

                $rowCount = $sql->rowCount();

                $details = json_decode($detalles);
                $nreg = count($details);
                
                for ($i=0; $i < $nreg; $i++) { 
                    //graba el item si no se ha insertado como nuevo
                    if( $details[$i]->itempedido == '-' ){
                        $this->saveItem($datos['codigo_verificacion'],
                                        $datos['codigo_estado'],
                                        $datos['codigo_atencion'],
                                        $datos['codigo_tipo'],
                                        $datos['codigo_costos'],
                                        $datos['codigo_area'],
                                        $details[$i]);
                    }else{
                    //cambia los datos 
                        for ($i=0; $i < count($details); $i++) { 
                            $rowDetails = $this->updateItems($datos['codigo_atencion'],
                                                             $details[$i]->cantidad,
                                                             $details[$i]->calidad,
                                                             $details[$i]->itempedido,
                                                             $details[$i]->especifica);
                         }
                    }
                }

                if ($rowCount > 0 || $rowDetails > 0){
                    $respuesta = true;
                    $mensaje = "Pedido Modificado";
                    $clase = "mensaje_correcto";
                }else{
                    $respuesta = true;
                    $mensaje = "Pedido Modificado";
                    $clase = "mensaje_correcto";
                }

                $salida = array("respuesta"=>$respuesta,
                                "mensaje"=>$mensaje,
                                "clase"=>$clase,
                                "items"=>$this->consultarReqId($datos['codigo_pedido'],49,50,49,null));

                
                return $salida;

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }            
        }

        //el tema es que debe cerrarse cuando se envia el correo
        public function enviarMensajes($asunto,$mensaje,$correos,$archivos,$pedido,$detalles,$estado,$emitido){
            require_once("public/PHPMailer/PHPMailerAutoload.php");

            $this->subirAdjuntoCorreo($archivos);
            
            $data       = json_decode($correos);
            $nreg       = count($data);
            $subject    = utf8_decode($asunto);
            $messaje    = utf8_decode($mensaje);
            $countfiles = count( $archivos['name'] );
            $estadoEnvio= false;
            $clase = "mensaje_error";
            $salida = "";
            
            $origen = $_SESSION['user']."@sepcon.net";
            $nombre_envio = $_SESSION['nombres'];

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
                $mail->setFrom($origen,$nombre_envio);
                $mail->addAddress($origen,$nombre_envio);

                for ($i=0; $i < $nreg; $i++) {
                    $mail->addAddress($data[$i]->correo,$data[$i]->nombre);
                }

                //$mail->addAddress("carroyo@sepcon.net","carroyo@sepcon.net");

                $mail->Subject = $subject;
                $mail->msgHTML(utf8_decode($messaje));
                    
                $mail->AddAttachment('public/documentos/pedidos/emitidos/'.$emitido);

                for($i=0;$i<$countfiles;$i++){
                    if (file_exists( 'public/documentos/correos/adjuntos/'.$archivos['name'][$i] )) {
                        $mail->AddAttachment('public/documentos/correos/adjuntos/'.$archivos['name'][$i]);
                    }
                }
    
                $mensaje = "Mensaje de correo no enviado";

                if (!$mail->send()) {
                    $estadoEnvio = false; 
                }else {
                    $this->actualizarDetalles("tb_pedidodet",$estado,$detalles);
                    $this->actualizarCabecera("tb_pedidocab",$estado,$pedido,$emitido,null);

                    $mensaje = "Mensaje de correo enviado";
                    $estadoEnvio = true; 
                    $clase = "mensaje_correcto";
                }

                $salida= array("estado"=>$estadoEnvio,
                                "mensaje"=>$mensaje,
                                "clase"=>$clase,
                                "proceso"=>$estado,
                                "pedidos"=>$this->listarPedidosUsuario());

                return $salida;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function updateItems($aten,$cant,$qaqc,$idx,$especifica){
            $sql = $this->db->connect()->prepare("UPDATE ibis.tb_pedidodet SET cant_pedida = :cant, 
                                        nflgqaqc = :qaqc,
                                        tipoAten = :aten,
                                        observaciones=:espec 
                                        WHERE iditem = :id");
            $sql ->execute(["cant"=>$cant,
                            "qaqc"=>$qaqc,
                            "aten"=>$aten,
                            "espec"=>$especifica,
                            "id"=>$idx]);
            $rowCount = $sql->rowCount();
            return $rowCount;
        }

        //Graba un solo Item de la modificacion
        private function saveItem($codigo,$estado,$atencion,$tipo,$costos,$area,$detalles){
            $indice = $this->obtenerIndice($codigo,"SELECT idreg AS numero FROM tb_pedidocab WHERE tb_pedidocab.verificacion =:id");

           try {
                $sql = $this->db->connect()->prepare("INSERT INTO tb_pedidodet SET idpedido=:ped,idprod=:prod,idtipo=:tipo,unid=:und,
                                                                                   cant_pedida=:cant,estadoItem=:est,tipoAten=:aten,
                                                                                   verificacion=:ver,nflgqaqc=:qaqc,idcostos=:costos,idarea=:area,
                                                                                   observaciones=:espec,item=:nropos");
                       $sql ->execute([
                                       "ped"=>$indice,
                                       "prod"=>$detalles->idprod,
                                       "tipo"=>$tipo,
                                       "und"=>$detalles->unidad,
                                       "cant"=>$detalles->cantidad,
                                       "est"=>$estado,
                                       "aten"=>$atencion,
                                       "ver"=>$codigo,
                                       "qaqc"=>$detalles->calidad,
                                       "costos"=>$costos,
                                       "area"=>$area,
                                       "espec"=>$detalles->especifica,
                                       "nropos"=>$detalles->item]);
                  
            } catch (PDOException $th) {
                   echo "Error: ".$th->getMessage();
                   return false;
            }
        }
       
        private function saveItems($codigo,$estado,$atencion,$tipo,$costos,$area,$detalles,$indice){

            $datos = json_decode($detalles);
            $nreg = count($datos);

            for ($i=0; $i < $nreg; $i++) { 
                try {

                        //if ( $existe == 0) {
                            $sql = $this->db->connect()->prepare("INSERT INTO tb_pedidodet 
                                                                    SET idpedido=:ped,idprod=:prod,idtipo=:tipo,unid=:und,
                                                                        cant_pedida=:cant,cant_aprob=:aprob,estadoItem=:est,tipoAten=:aten,
                                                                        verificacion=:ver,nflgqaqc=:qaqc,idcostos=:costos,idarea=:area,
                                                                        observaciones=:espec,item=:nropos");
                            $sql ->execute([
                                "ped"=>$indice,
                                "prod"=>$datos[$i]->idprod,
                                "tipo"=>$tipo,
                                "und"=>$datos[$i]->unidad,
                                "cant"=>$datos[$i]->cantidad,
                                "est"=>$estado,
                                "aten"=>$atencion,
                                "ver"=>$codigo,
                                "qaqc"=>$datos[$i]->calidad,
                                "costos"=>$costos,
                                "area"=>$area,
                                "espec"=>$datos[$i]->especifica,
                                "nropos"=>$datos[$i]->item,
                                "aprob"=>$datos[$i]->cantidad]);
                        //}
                       
                   
                } catch (PDOException $th) {
                    echo "Error: ".$th->getMessage();
                    return false;
                }
            }
        }

        private function vericaExiste($prod,$cant,$observa){
            try {
                $sql=$this->db->connect()->prepare("SELECT COUNT(idprod)  AS numero
                                                    FROM tb_pedidodet 
                                                    WHERE  idprod=:producto
                                                    AND cant_pedida=:cantidad");
                $sql->execute(["producto"=>$prod, "cantidad"=>$cant]);
                $result = $sql->fetchAll();

                return $result[0]['numero'];
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function filtrarItemsPedido($codigo,$descripcion,$tipo){
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

        public function pedidosFiltrados($parametros){
            try {
                $salida = "";
                $mes  = date("m");

                $tipo   = $parametros['tipoSearch'] == -1 ? "%" : "%".$parametros['tipoSearch']."%";
                $costos = $parametros['costosSearch'] == -1 ? "%" : $parametros['costosSearch'];
                $mes    = $parametros['mesSearch'] == -1 ? $mes :  $parametros['mesSearch'];
                $anio   = $parametros['anioSearch'];

                $sql = $this->db->connect()->prepare("SELECT
                                                        ibis.tb_pedidocab.idreg,
                                                        ibis.tb_pedidocab.idcostos,
                                                        ibis.tb_pedidocab.idarea,
                                                        ibis.tb_pedidocab.emision,
                                                        ibis.tb_pedidocab.vence,
                                                        ibis.tb_pedidocab.estadodoc,
                                                        ibis.tb_pedidocab.nrodoc,
                                                        ibis.tb_pedidocab.idtipomov,
                                                        UPPER(ibis.tb_pedidocab.concepto) AS concepto,
                                                        CONCAT(
                                                            rrhh.tabla_aquarius.nombres,
                                                            ' ',
                                                            rrhh.tabla_aquarius.apellidos
                                                        ) AS nombres,
                                                        UPPER(
                                                            CONCAT(
                                                                ibis.tb_proyectos.ccodproy,
                                                                ' ',
                                                                ibis.tb_proyectos.cdesproy
                                                            )
                                                        ) AS costos,
                                                        ibis.tb_pedidocab.nivelAten,
                                                        atenciones.cdescripcion AS atencion,
                                                        estados.cdescripcion AS estado,
                                                        estados.cabrevia
                                                    FROM
                                                        ibis.tb_pedidocab
                                                    INNER JOIN rrhh.tabla_aquarius ON ibis.tb_pedidocab.idsolicita = rrhh.tabla_aquarius.internal
                                                    INNER JOIN ibis.tb_proyectos ON ibis.tb_pedidocab.idcostos = ibis.tb_proyectos.nidreg
                                                    INNER JOIN ibis.tb_parametros AS atenciones ON ibis.tb_pedidocab.nivelAten = atenciones.nidreg
                                                    INNER JOIN ibis.tb_parametros AS estados ON ibis.tb_pedidocab.estadodoc = estados.nidreg
                                                    WHERE
                                                        ibis.tb_pedidocab.usuario = :user 
                                                    AND ibis.tb_pedidocab.idtipomov LIKE :tipomov
                                                    AND ibis.tb_pedidocab.idcostos = :costos
                                                    AND MONTH (ibis.tb_pedidocab.emision) = :mes
                                                    AND YEAR (ibis.tb_pedidocab.emision) = :anio
                                                    AND ibis.tb_pedidocab.estadodoc BETWEEN 49 AND 50");
                $sql->execute(["user"=>$_SESSION['iduser'],
                                "tipomov"=>$tipo,
                                "costos"=>$costos,
                                "mes"=>$mes,
                                "anio"=>$anio]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()) {
                        $tipo = $rs['idtipomov'] == 37 ? "B":"S";
                        $salida .='<tr class="pointer" data-indice="'.$rs['idreg'].'">
                                        <td class="textoCentro">'.str_pad($rs['nrodoc'],4,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.date("d/m/Y", strtotime($rs['emision'])).'</td>
                                        <td class="textoCentro">'.$tipo.'</td>
                                        <td class="pl20px">'.utf8_decode($rs['concepto']).'</td>
                                        <td class="pl20px">'.utf8_decode($rs['costos']).'</td>
                                        <td class="pl20px">'.$rs['nombres'].'</td>
                                        <td class="textoCentro '.$rs['cabrevia'].'">'.$rs['estado'].'</td>
                                        <td class="textoCentro '.strtolower($rs['atencion']).'">'.$rs['atencion'].'</td>
                                        <td class="textoCentro"><a href="'.$rs['idreg'].'"><i class="fa fa-trash-alt"></i></a></td>
                                    </tr>';
                    }
                }else {
                    $salida = '<tr class="pointer"><td colspan="9" class="textoCentro">No se encontraron registros en la consulta</td></tr>';
                }

                return $salida;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }
    }    
?>