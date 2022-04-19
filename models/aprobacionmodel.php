<?php
    class AprobacionModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarPedidos(){
            try {
                $salida = "";
                $sql = $this->db->connect()->prepare("SELECT
                                                        ibis.tb_costusu.id_cuser,
                                                        ibis.tb_costusu.ncodproy,
                                                        ibis.tb_pedidocab.nrodoc,
                                                        UPPER( ibis.tb_pedidocab.concepto ) AS concepto,
                                                        ibis.tb_pedidocab.idreg,
                                                        ibis.tb_pedidocab.estadodoc,
                                                        ibis.tb_pedidocab.emision,
                                                        ibis.tb_pedidocab.vence,
                                                        UPPER(
                                                        CONCAT_WS( ' ', ibis.tb_proyectos.ccodproy, ibis.tb_proyectos.cdesproy )) AS costos,
                                                        ibis.tb_pedidocab.nivelAten,
                                                        CONCAT_WS(' ',rrhh.tabla_aquarius.apellidos,rrhh.tabla_aquarius.nombres) AS nombres,
                                                        estados.cdescripcion AS estado,
                                                        atencion.cdescripcion AS atencion,
                                                        estados.cabrevia 
                                                    FROM
                                                        ibis.tb_costusu
                                                        INNER JOIN ibis.tb_pedidocab ON tb_costusu.ncodproy = tb_pedidocab.idcostos
                                                        INNER JOIN ibis.tb_proyectos ON tb_costusu.ncodproy = tb_proyectos.nidreg
                                                        INNER JOIN rrhh.tabla_aquarius ON ibis.tb_pedidocab.idsolicita = rrhh.tabla_aquarius.internal
                                                        INNER JOIN ibis.tb_parametros AS estados ON ibis.tb_pedidocab.estadodoc = estados.nidreg
                                                        INNER JOIN ibis.tb_parametros AS atencion ON ibis.tb_pedidocab.nivelAten = atencion.nidreg 
                                                    WHERE
                                                        tb_costusu.id_cuser = :user 
                                                        AND tb_pedidocab.estadodoc = 53");
                $sql->execute(["user"=>$_SESSION['iduser']]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()) {
                        $salida .='<tr class="pointer" data-indice="'.$rs['idreg'].'">
                                        <td class="textoCentro">'.str_pad($rs['nrodoc'],4,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.date("d/m/Y", strtotime($rs['emision'])).'</td>
                                        <td class="textoCentro">'.date("d/m/Y", strtotime($rs['vence'])).'</td>
                                        <td class="pl20px">'.utf8_decode($rs['concepto']).'</td>
                                        <td class="pl20px">'.utf8_decode($rs['costos']).'</td>
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

        public function llamarAdjuntos($id){
            try {
                $salida = "";
                $sql = $this->db->connect()->prepare("SELECT creferencia,cdocumento FROM lg_regdocumento WHERE nidrefer=:id");
                $sql->execute(['id'=>$id]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()) {
                        $salida .= '<li><a href="'.$rs['creferencia'].'" data-archivo="'.$rs['creferencia'].'"><i class="far fa-file"></i><p>'.$rs['cdocumento'].'</p></a></li>';
                    }
                }
                
                $ret = array("adjuntos"=>$salida,
                            "archivos"=>$rowCount);

                return $ret;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function enviarMensajeAprobado($asunto,$mensaje,$correos,$pedido,$detalles,$estado,$cabecera){
            require_once("public/PHPMailer/PHPMailerAutoload.php");

            $adjunto = $this->genReqAprob($cabecera,$detalles);
            $adjunto = $adjunto.".pdf"; 

            $data       = json_decode($correos);
            $nreg       = count($data);
            $subject    = utf8_decode($asunto);
            $messaje    = utf8_decode($mensaje);
            $estadoEnvio= false;
            $clase = "mensaje_error";
            $salida = "";
            
            $origen = $_SESSION['user']."@sepcon.net";
            $nombre_envio = $_SESSION['user'];

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

                for ($i=0; $i < $nreg; $i++) {
                    $mail->addAddress($data[$i]->correo,$data[$i]->nombre);
    
                    $mail->Subject = $subject;
                    $mail->msgHTML(utf8_decode($messaje));

                    if (file_exists( 'public/documentos/pedidos/aprobados/'.$adjunto)) {
                        $mail->AddAttachment('public/documentos/pedidos/aprobados/'.$adjunto);
                    }
    
                    if (!$mail->send()) {
                        $mensaje = "Mensaje de correo no enviado";
                        $estadoEnvio = false; 
                    }else {
                        $mensaje = "Mensaje de correo enviado";
                        $estadoEnvio = true; 
                    }   
                }

                if ($estadoEnvio){
                    $clase = "mensaje_correcto";
                    $this->actCabPedAprueba($estado,$pedido,$adjunto);
                    $this->actDetPedAprueba($estado,$detalles);
                }

                $salida= array("estado"=>$estadoEnvio,
                                "mensaje"=>$mensaje,
                                "clase"=>$clase );

                return $salida;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
            
        }

        private function actCabPedAprueba($estado,$pedido,$aprobado){
            try {
                $sql = $this->db->connect()->prepare("UPDATE tb_pedidocab 
                                                            SET estadodoc=:est,aprueba=:apr,faprueba=:fec,docPdfAprob=:doc 
                                                            WHERE idreg=:id");
                $sql->execute(['est'=>$estado,
                                'id'=>$pedido,
                                'apr'=>$_SESSION['iduser'],
                                'fec'=>date('Y-m-d'),
                                'doc'=>$aprobado]);
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function actDetPedAprueba($estado,$detalles){
            $datos = json_decode($detalles);
            $nreg =  count($datos);

            try {
                for ($i=0; $i < $nreg ; $i++) { 
                    $sql = $this->db->connect()->prepare("UPDATE tb_pedidodet 
                                                            SET estadoItem=:est,
                                                                cant_aprob=cantaprob,
                                                                nflgaprobado=swaprob,
                                                                idaprueba=usraprob,
                                                                obsAprueba=obaprueba,
                                                                faprobado=fecaprob 
                                                            WHERE iditem=:id");
                    $sql->execute(["est"=>$estado,
                                    "id"=>$datos[$i]->itempedido,
                                    "cantaprob"=>$datos[$i]->aprobada,
                                    "swaprob"=>$datos[$i]->verifica,
                                    "usraprob"=>$_SESSION['iduser'],
                                    "obaprueba"=>$datos[$i]->observa,
                                    "fecaprob"=>date("Y-m-d")]);
                }
               
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function genReqAprob($datos,$detalles){
            require_once('public/formatos/pedidos.php');
            
            $details = json_decode($detalles);
            $filename =  uniqid().".pdf";

            $num = $datos['numero'];
            $fec = $datos['emision'];
            $usr = $datos['elabora'];
            $pry = $datos['costos'];
            $are = $datos['area'];
            $cos = $datos['costos'];
            $tra = $datos['transporte'];
            $con = $datos['concepto'];
            $sol = $datos['solicitante'];
            $esp = $datos['espec_items'];
            $apr = $_SESSION['nombres'];
            
            $reg = ''; 
            $dti = $datos['codigo_tipo'] == 37 ? "PEDIDO DE COMPRA":"PEDIDO DE SERVICIO";
            $mmt = "";
            $cla = $datos['codigo_atencion'] <= 46 ? "URGENTE":"NORMAL";
            $msj = "APROBADO";
            $ruta = "public/documentos/pedidos/aprobados/";

            $pdf = new PDF($num,$fec,$pry,$cos,$are,$con,$mmt,$cla,$tra,$usr,$sol,$reg,$esp,$dti,$msj,$apr);
		    $pdf->AddPage();
            $pdf->AliasNbPages();
            $pdf->SetWidths(array(10,15,70,8,10,17,15,15,15,15));
            $pdf->SetFont('Arial','',5);
            $lc = 0;
            $rc = 0; 

            $nreg = count($details);

            for($i=1;$i<=$nreg;$i++){
			    $pdf->SetAligns(array("L","L","L","L","R","L","L","L","L","L"));
                $pdf->Row(array($details[$rc]->item,
                                $details[$rc]->codigo,
                                utf8_decode($details[$rc]->descripcion),
                                $details[$rc]->unidad,
                                $details[$rc]->cantidad,
                                '',
                                '',
                                '',
                                $details[$rc]->nroparte,
                                ''));
                
                $lc++;
                $rc++;

                if ($lc == 52) {
				    $pdf->AddPage();
				    $lc = 0;
			    }	
		    }

            $pdf->Output($ruta.$filename,'F');
            
            return $filename;
        }
    }
?>