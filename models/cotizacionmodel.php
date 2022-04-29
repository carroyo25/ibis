<?php
    class CotizacionModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listaPedidos(){
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
                                                        AND tb_pedidocab.estadodoc BETWEEN 54 AND 55
                                                        AND tb_costusu.nflgactivo = 1");
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

        public function listarProveedores(){
            try {
                $salida = "";
                $sql = $this->db->connect()->query("SELECT id_centi,cnumdoc,UPPER(crazonsoc) AS crazonsoc,cemail FROM cm_entidad WHERE nflgactivo = 7");
                $sql ->execute();
                $rows = $sql->rowcount();
                
                if ($rows > 0) {
                    while ($rs = $sql->fetch()) {
                        $salida .='<tr data-entidad="'.$rs['id_centi'].'" data-doc="'.$rs['cnumdoc'].'">
                                    <td class="pl10px">'.$rs['crazonsoc'].'</td>
                                    <td class="pl10px">'.$rs['cemail'].'</td>
                                    <td class="textoCentro"><input type="checkbox"></td>
                                </tr>';
                    }
                }

                return $salida;

            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function enviarCorreo($pedido,$detalles,$correos,$asunto,$mensaje,$estado){
            try {
                require_once("public/PHPMailer/PHPMailerAutoload.php");
                
                $data       = json_decode($correos);
                $nreg       = count($data);

                $subject    = utf8_decode($asunto);
                
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
            
            $origen = $_SESSION['user']."@sepcon.net";
            $nombre_envio = $_SESSION['user'];

            $mail->setFrom($origen,$nombre_envio);

            try {
                for ($i=0; $i < $nreg; $i++) {
                    $url = '<p><a href="'.constant('URL').'public/cotizacion/?codped='.$pedido.'&codenti='.$data[$i]->codprov.'">Seguir enlace</a></p>';
                    $messaje    = utf8_decode($mensaje . $url);

                    $mail->addAddress($data[$i]->correo,$data[$i]->nombre);
    
                    $mail->Subject = $subject;
                    $mail->msgHTML(utf8_decode($messaje));
    
                    if (!$mail->send()) {
                        $msj = "Mensaje de correo no enviado";
                        $estadoEnvio = false;
                    }else {
                        $msj = "Mensaje de correo enviado";
                        $estadoEnvio = true; 
                        $mail->ClearAddresses();  // each AddAddress add to list
                        $mail->ClearCCs();
                        $mail->ClearBCCs();
                    }   
                }

                if ($estadoEnvio){
                    $clase = "mensaje_correcto";
                    $codcot = uniqid();
                    $this->grabarCabecera($correos,$pedido,$codcot);
                    $this->grabarDetalles($correos,$detalles,$pedido,$codcot);
                    $this->actCabPedCot($pedido,$estado);
                    $this->actDetPedCot($detalles,$estado);
                }

                $salida= array("estado"=>$estadoEnvio,
                                "mensaje"=>$msj,
                                "clase"=>$clase );

                return $salida;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }

            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function cerrarcotizaciones($id,$valor,$details){
            $this->actCabPedCot($id,$valor);
            $this->actDetPedCot($details,$valor);
        }

        private function grabarCabecera($mails,$pedido,$codcot){
            try {
                $correos = json_decode($mails);
                

                for ($i=0; $i < count($correos); $i++) {
                    $query = $this->db->connect()->prepare("INSERT INTO lg_cotizacab SET id_regmov=:idped,
                                                                                         id_centi=:idprov,
                                                                                         ccodcot=:coti,
                                                                                         nflgactivo = 1");
                    $query->execute(["idped"=>$pedido,
                                    "idprov"=>$correos[$i]->codprov,
                                    "coti"=>$codcot]);
                }

            } catch (PDOException $e) {
                echo $e->getMessage();
                return false;
            }
        }

        private function grabarDetalles($mails,$details,$pedido,$codcot){
            try {
                $correos = json_decode($mails);
                $detalles = json_decode($details);

                for ($j=0; $j < count($correos); $j++){
                    for ($i=0;$i<count($detalles);$i++){
                        $query = $this->db->connect()->prepare("INSERT INTO lg_cotizadet SET id_regmov=:idped,
                                                                                                niddet=:iddet,
                                                                                                id_cprod=:cprod,
                                                                                                cantcoti=:canti,
                                                                                                ncodmed=:unid,
                                                                                                id_centi=:prove,
                                                                                                nflgactivo=:flag,
                                                                                                cestadodoc=:esta,
                                                                                                ccodcot=:coti");
                        $query->execute(["idped"=>$pedido,
                                         "iddet"=>$detalles[$i]->itempedido,
                                         "cprod"=>$detalles[$i]->idprod,
                                         "canti"=>$detalles[$i]->cantidad,
                                         "unid"=>$detalles[$i]->unidad,
                                         "prove"=>$correos[$j]->codprov,
                                         "flag"=>1,
                                         "esta"=>0,
                                        "coti"=>$codcot]);
                    }
                }

                

            } catch (PDOException $e) {
                echo $e->getMessage();
                return false;
            }
        }

        private function actCabPedCot($id,$valor){
            try {
                $sql = $this->db->connect()->prepare("UPDATE tb_pedidocab SET estadodoc=:est WHERE idreg=:id");
                $sql->execute(["est"=>$valor,
                                "id"=>$id]);
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function actDetPedCot($detalles,$valor){
            $datos = json_decode($detalles);
            $nreg =  count($datos);

            try {
                for ($i=0; $i < $nreg; $i++) { 

                    $sql = $this->db->connect()->prepare("UPDATE tb_pedidodet 
                                                        SET estadoItem=:est,observAlmacen=:obs
                                                        WHERE iditem=:id");
                    $sql->execute(["est"=>$valor,
                                    "id"=>$datos[$i]->itempedido,
                                    "obs"=>$datos[$i]->observac]);
                }
                
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }
    }

?>