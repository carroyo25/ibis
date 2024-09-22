<?php
    class AtencionModel extends Model{

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
                                                        AND tb_pedidocab.estadodoc = 51
                                                        AND tb_costusu.nflgactivo = 1");
                $sql->execute(["user"=>$_SESSION['iduser']]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()) {
                        $salida .='<tr class="pointer" data-indice="'.$rs['idreg'].'">
                                        <td class="textoCentro">'.str_pad($rs['nrodoc'],4,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.date("d/m/Y", strtotime($rs['emision'])).'</td>
                                        <td class="textoCentro">'.date("d/m/Y", strtotime($rs['vence'])).'</td>
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

        public function almacenUsuario($codprod){
            try {
                $salida = "";

                $sql = $this->db->connect()->prepare("SELECT
                                                        tb_almausu.ncodalm, 
                                                        tb_almausu.nalmacen, 
                                                        tb_almausu.id_cuser, 
                                                        tb_almacen.ccodalm, 
                                                        UPPER(tb_almacen.cdesalm) AS almacen
                                                    FROM
                                                        tb_almausu
                                                        INNER JOIN
                                                        tb_almacen
                                                        ON 
                                                        tb_almausu.nalmacen = tb_almacen.ncodalm
                                                    WHERE
                                                        tb_almausu.nflgactivo = 1 AND
                                                        tb_almausu.id_cuser = :user");
                $sql->execute(["user"=>$_SESSION['iduser']]);
                $rowCount = $sql->rowCount();
                if($rowCount > 0) {
                    while ($rs = $sql->fetch()) {
                        $cant = $this->existenciasAlmacen($codprod,$rs['nalmacen']);
                        $salida .='<tr>
                                        <td class="pl20px">'.$rs['almacen'].'</td>
                                        <td class="textoDerecha pr20px">'.number_format($cant, 2, '.', ',').'</td>
                                    </tr>';
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function existenciasAlmacen($id,$alm){
            try {
                $existencias = 0;
                $sql = $this->db->connect()->prepare("SELECT
                                                    alm_existencia.codprod,
                                                    alm_existencia.idprod,
                                                    SUM( alm_existencia.cant_ingr ) AS ingresos,
                                                    SUM( alm_existencia.cant_sal ) AS salidas 
                                                FROM
                                                    alm_existencia 
                                                WHERE
                                                    alm_existencia.idalm = :alm 
                                                    AND alm_existencia.idprod = :prod");
                $sql->execute(["prod"=>$id,
                                "alm"=>$alm]);
                
                $result = $sql->fetchAll();

                $existencias = $result[0]['ingresos'] - $result[0]['salidas'];

                return $existencias;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function enviarMensajeAprobacion($asunto,$mensaje,$correos,$pedido,$detalles,$estado,$emitido){
            require_once("public/PHPMailer/PHPMailerAutoload.php");

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

                    if (file_exists( 'public/documentos/pedidos/emitidos/'.$emitido)) {
                        $mail->AddAttachment('public/documentos/pedidos/emitidos/'.$emitido);
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
                    $this->modificarCabeceraAtencion(53,$pedido,$detalles);
                    $this->modificarItemsAtencion($estado,$detalles);
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

        private function modificarCabeceraAtencion($estado,$id,$detalles){
            try {
                $sql = $this->db->connect()->prepare("UPDATE 
                                                    tb_pedidocab 
                                                    SET estadodoc=:est,
                                                        atiende=:user 
                                                    WHERE idreg=:id");
                $sql->execute(["est"=>$estado,
                                "user" => $_SESSION['iduser'],
                                "id"=>$id]);

                return true;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function modificarItemsAtencion($estado,$detalles){
            try {
                $datos = json_decode($detalles);
                $nreg =  count($datos);
                
                for ($i=0; $i < $nreg; $i++) { 
                    
                    //agregar un parse float
                    $estado = $datos[$i]->cantidad == $datos[$i]->atendida ? 52:53;

                    $p = $datos[$i]->itempedido;
                    $c = $datos[$i]->atendida;
                    $sql = $this->db->connect()->prepare("UPDATE tb_pedidodet SET estadoItem=:est,cant_atend=:aten,cant_resto=:resto WHERE iditem=:id");
                    $sql->execute(["est"=>$estado,
                                    "id"=>$p,
                                    "aten"=>$c,
                                    "resto"=>0]);
                }
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }
    }
?>