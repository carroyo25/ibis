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
                                                        cu.id_cuser,
                                                        cu.ncodproy,
                                                        pc.nrodoc,
                                                        UPPER(pc.concepto) AS concepto,
                                                        pc.idreg,
                                                        pc.estadodoc,
                                                        pc.emision,
                                                        CASE 
                                                            WHEN pc.fentregaPedido IS NULL OR pc.fentregaPedido = '0000-00-00' THEN '-'
                                                            ELSE DATE_FORMAT(pc.fentregaPedido, '%d/%m/%Y') 
                                                        END AS entrega,
                                                        UPPER(CONCAT_WS(' ', p.ccodproy, p.cdesproy)) AS costos,
                                                        pc.nivelAten,
                                                        CONCAT_WS(' ', a.apellidos, a.nombres) AS nombres,
                                                        est.cdescripcion AS estado,
                                                        aten.cdescripcion AS atencion,
                                                        est.cabrevia
                                                    FROM
                                                        ibis.tb_costusu cu
                                                        INNER JOIN ibis.tb_pedidocab pc ON cu.ncodproy = pc.idcostos
                                                        INNER JOIN ibis.tb_proyectos p ON cu.ncodproy = p.nidreg
                                                        INNER JOIN rrhh.tabla_aquarius a ON pc.idsolicita = a.internal
                                                        INNER JOIN ibis.tb_parametros est ON pc.estadodoc = est.nidreg
                                                        INNER JOIN ibis.tb_parametros aten ON pc.nivelAten = aten.nidreg
                                                    WHERE
                                                        cu.id_cuser = :user 
                                                        AND pc.estadodoc = 51
                                                        AND cu.nflgactivo = 1");
                $sql->execute(["user"=>$_SESSION['iduser']]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()) {
                        $fecha_entrega = $rs['entrega'] == "0000-00-00" ? "" : $rs['entrega'];

                        $salida .='<tr class="pointer" data-indice="'.$rs['idreg'].'">
                                        <td class="textoCentro">'.str_pad($rs['nrodoc'],4,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.date("d/m/Y", strtotime($rs['emision'])).'</td>
                                        <td class="textoCentro">'.$fecha_entrega.'</td>
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

        public function centroCostosUsuario($producto){
            try {
                $docData = [];

                $sql = $this->db->connect()->prepare("SELECT
                                                    UPPER( tb_proyectos.ccodproy ) AS codigo_costos,
                                                    UPPER( tb_proyectos.cdesproy ) AS descripcion_costos,
                                                    tb_costusu.ncodproy
                                                FROM
                                                    tb_costusu
                                                    INNER JOIN tb_proyectos ON tb_costusu.ncodproy = tb_proyectos.nidreg 
                                                WHERE
                                                    tb_costusu.id_cuser = :user 
                                                    AND tb_proyectos.nflgactivo = 1
                                                    AND tb_costusu.nflgactivo = 1
                                                ORDER BY tb_proyectos.ccodproy");

                $sql->execute(["user"=>$_SESSION['iduser']]);

                while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                    $docData[] = [
                        'codigo_costos' => $row['codigo_costos'],
                        'descripcion_costos' => $row['descripcion_costos'],
                        'ncodproy' => $row['ncodproy'],
                        'existencia' => 200
                    ];
                }

                return $docData;

            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function verirficarStock($costos,$codigo){
            try {
                $sql = $this->db->connect()->prepare("");
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }
    }
?>