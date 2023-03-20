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
                                                        ibis.tb_pedidocab.idtipomov,
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
                                                        AND tb_pedidocab.estadodoc = 53
                                                        AND tb_costusu.nflgactivo = 1
                                                    ORDER BY tb_pedidocab.emision DESC");
                $sql->execute(["user"=>$_SESSION['iduser']]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()) {
                        $tipo = $rs['idtipomov'] == 37 ? "B":"S";
                        $salida .='<tr class="pointer" data-indice="'.$rs['idreg'].'">
                                        <td class="textoCentro">'.str_pad($rs['nrodoc'],6,0,STR_PAD_LEFT).'</td>
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

        private function enviarCorreo($adjunto,$correos,$pedido,$costos){
            require_once("public/PHPMailer/PHPMailerAutoload.php");

            $estadoEnvio= false;
            
            //$origen = $_SESSION['user']."@sepcon.net";
            $origen = $_SESSION['correo'];
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
                
                $nreg = count($correos) ;

                for ($i=0; $i < $nreg; $i++) {
                    //$mail->addAddress($correos[$i]['ccorreo'],$correos[$i]['cnombres']);
                }

                $texto = "Pedido : " .$pedido . "-" .$costos;
                $mail->Subject = $texto;
                $mail->msgHTML(utf8_decode("Pedido aprobado en el sistema"));

                if (file_exists( 'public/documentos/pedidos/aprobados/'.$adjunto)) {
                    $mail->AddAttachment('public/documentos/pedidos/aprobados/'.$adjunto);
                }

                if (!$mail->send()) {
                    $mensaje = "Mensaje de correo no enviado";
                    $estadoEnvio = false; 
                    $enviados = 0;
                }else {
                    $mensaje = "Mensaje de correo enviado";
                    $estadoEnvio = true; 
                }

            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function aprobarPedido($cabecera,$detalles,$estado,$pedido){
            $adjunto = $this->genReqAprob($cabecera,$detalles);
            $correos = $this->correosLogisticos();
            $envio  = $this->enviarCorreo($adjunto,$correos,$cabecera['numero'],$cabecera['costos']);

            $clase = "mensaje_correcto";
            $this->actCabPedAprueba($estado,$pedido,$adjunto);
            $this->actDetPedAprueba($estado,$detalles);

            $salida= array("envio"=>$correos,
                            "mensaje"=>"Pedido Aprobado",
                            "clase"=>$clase,
                            "pedidos"=>$this->listarPedidos());

            return $salida;
        }

        //ACTUALIZA LA CABECERA
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

        //ACTUALIZAR DETALLES DEL PEDIDO
        private function actDetPedAprueba($estado,$detalles){
            $datos = json_decode($detalles);
            $nreg =  count($datos);

            try {
                for ($i=0; $i < $nreg ; $i++) { 
                    $sql = $this->db->connect()->prepare("UPDATE tb_pedidodet 
                                                            SET estadoItem=:est,
                                                                cant_aprob=:cantaprob,
                                                                nflgaprobado=:swaprob,
                                                                idaprueba=:usraprob,
                                                                obsAprueba=:obaprueba,
                                                                faprobado=:fecaprob 
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

        //genera el pdf del pedido
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

        private function correosLogisticos(){
            try {
                $sql = $this->db->connect()->query("SELECT ccorreo,cnombres 
                                                    FROM tb_user 
                                                    WHERE tb_user.nrol = 68");
                $sql->execute();
                $rowCount = $sql->rowCount();
                $correos = [];

                if ( $rowCount > 0){
                    while ( $rs = $sql->fetch(PDO::FETCH_ASSOC)) {
                        array_push($correos,$rs);
                    }
                }

                return $correos;

            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function filtroAprobados($parametros){
            try {
                $salida = "";
                $mes  = date("m");

                $tipo   = $parametros['tipoSearch'] == -1 ? "%" : "%".$parametros['tipoSearch']."%";
                $costos = $parametros['costosSearch'] == -1 ? "" : $parametros['costosSearch'];
                $mes    = $parametros['mesSearch'] == -1 ? "%".$mes :  $parametros['mesSearch'];
                $anio   = "%".$parametros['anioSearch'];

                $sql = $this->db->connect()->prepare("SELECT
                                                        ibis.tb_costusu.id_cuser,
                                                        ibis.tb_costusu.ncodproy,
                                                        ibis.tb_pedidocab.nrodoc,
                                                        UPPER( ibis.tb_pedidocab.concepto ) AS concepto,
                                                        ibis.tb_pedidocab.idreg,
                                                        ibis.tb_pedidocab.estadodoc,
                                                        ibis.tb_pedidocab.emision,
                                                        ibis.tb_pedidocab.vence,
                                                        ibis.tb_pedidocab.idtipomov,
                                                        UPPER(
                                                        CONCAT_WS( ' ', ibis.tb_proyectos.ccodproy, ibis.tb_proyectos.cdesproy )) AS costos,
                                                        ibis.tb_pedidocab.nivelAten,
                                                        CONCAT_WS( ' ', rrhh.tabla_aquarius.apellidos, rrhh.tabla_aquarius.nombres ) AS nombres,
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
                                                        AND tb_pedidocab.estadodoc = 53 
                                                        AND tb_costusu.nflgactivo = 1 
                                                        AND ibis.tb_pedidocab.idtipomov LIKE :tipomov 
                                                        AND ibis.tb_pedidocab.idcostos = :costos 
                                                        AND MONTH ( ibis.tb_pedidocab.emision ) LIKE :mes 
                                                        AND YEAR ( ibis.tb_pedidocab.emision ) LIKE :anio 
                                                    ORDER BY
                                                        tb_pedidocab.emision DESC");
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
                    $salida = '<tr class="pointer"><td colspan="9" class="textoCentro" data-costos="'.$costos.'">No se encontraron registros en la consulta</td></tr>';
                }

                return $salida;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function anularPedido($id) {
            try {
                $sql = $this->db->connect()->prepare("UPDATE tb_pedidocab 
                                                SET tb_pedidocab.estadodoc = 105,
                                                    tb_pedidocab.anula =:user
                                                WHERE idreg = :id
                                                LIMIT 1");
                $sql->execute(["id" => $id,"user"=>$_SESSION['iduser']]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    $this->anularDetalles($id);

                    return "Pedido Anulado";
                }
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function anularDetalles($id){
            try {
                $sql = $this->db->connect()->prepare("UPDATE tb_pedidodet 
                                                SET tb_pedidodet.estadoItem = 105
                                                WHERE idpedido = :id");
                $sql->execute(["id" => $id]);
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function cancelarPedido($id){
            try {
                $sql = $this->db->connect()->prepare("UPDATE tb_pedidocab 
                                                SET tb_pedidocab.estadodoc = 49,
                                                    tb_pedidocab.cancela =:user
                                                WHERE idreg = :id
                                                LIMIT 1");
                $sql->execute(["id" => $id,"user"=>$_SESSION['iduser']]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    $this->cancelarDetalles($id);

                    return "Pedido Cancelado";
                }
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function cancelarDetalles($id){
            try {
                $sql = $this->db->connect()->prepare("UPDATE tb_pedidodet 
                                                SET tb_pedidodet.estadoItem = 49
                                                WHERE idpedido = :id");
                $sql->execute(["id" => $id]);
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }
    }
?>