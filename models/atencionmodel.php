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

        /*public function centroCostosUsuario($producto){
            try {
                $docData = [];

                $sql = $this->db->connect()->prepare("SELECT
                                                    UPPER( tb_proyectos.ccodproy ) AS codigo_costos,
                                                    UPPER( tb_proyectos.cdesproy ) AS descripcion_costos,
                                                    tb_proyectos.nidreg,
                                                    tb_proyectos.ccodproy
                                                FROM
                                                    tb_costusu
                                                    INNER JOIN tb_proyectos ON tb_costusu.ncodproy = tb_proyectos.nidreg 
                                                WHERE
                                                    tb_costusu.id_cuser = :user 
                                                    AND tb_proyectos.nflgactivo = 1
                                                    AND tb_costusu.nflgactivo = 1
                                                    AND tb_proyectos.veralm = 1
                                                ORDER BY tb_proyectos.ccodproy");

                

                $sql->execute(["user"=>$_SESSION['iduser']]);

                while($row = $sql->fetch(PDO::FETCH_ASSOC)){

                    $existencias = $this->verificarStock($row['nidreg'],$producto);
                    $transferencias = $this->verificarTransferencias($row['nidreg'],$producto);
                    $consumos = $this->verificarConsumos($row['nidreg'],$producto);

                    $docData[] = [
                        'codigo_costos' => $row['codigo_costos'],
                        'descripcion_costos' => $row['descripcion_costos'],
                        'ncodproy' => $row['nidreg'],
                        'total' => $existencias["stocks"],
                        'transferencias' => $transferencias['transferencias'],
                        'consumos' =>  $consumos['consumos']
                    ];
                }

                return $docData;

            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function verificarTransferencias($costos,$codigo){
            try {
                $sql = $this->db->connect()->prepare("SELECT 
                                                        COALESCE(SUM(t.ncanti), 0) AS transferencias
                                                    FROM
                                                        alm_transferdet t
                                                    WHERE
                                                        t.idcprod = :codigo
                                                        AND t.idcostos = :costos
                                                    LIMIT 1");

                $sql->execute(["codigo"=>$codigo,"costos"=>$costos]);
                $result = $sql->fetchAll();

                return array("transferencias"=>$result['0']['transferencias']);
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function verificarStock($costos,$codigo){
            try {
                $sql = $this->db->connect()->prepare("SELECT 
                                                        COALESCE(SUM(e.cant_ingr), 0) AS ingresos,
                                                        COALESCE(SUM(e.cant_sal), 0) AS salidas,
                                                        e.codprod,
                                                        c.idcostos
                                                    FROM
                                                        alm_existencia e
                                                    LEFT JOIN alm_cabexist c ON e.idregistro = c.idreg
                                                    LEFT JOIN tb_proyectos p ON c.idcostos = p.nidreg
                                                    WHERE
                                                        e.codprod = :codigo
                                                        AND c.idcostos = :costos
                                                    LIMIT 1");

                $sql->execute(["codigo"=>$codigo,"costos"=>$costos]);
                $result = $sql->fetchAll();

                $total = $result['0']['ingresos'] -  $result['0']['salidas'] ;

                return array("stocks"=>$total);
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function verificarConsumos($costos,$codigo){
            try {
                $sql = $this->db->connect()->prepare("SELECT 
                                                        COALESCE(SUM(c.cantsalida), 0) AS salidas,
                                                        COALESCE(SUM(c.cantdevolucion), 0) AS devoluciones
                                                    FROM
                                                        alm_consumo c
                                                    WHERE
                                                        c.idprod = :codigo
                                                        AND c.ncostos = :costos
                                                    LIMIT 1");

                $sql->execute(["codigo"=>$codigo,"costos"=>$costos]);
                $result = $sql->fetchAll();

                $consumos = $result['0']['salidas'] -  $result['0']['devoluciones'] ;

                return array("consumos"=>$consumos);
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }*/

        public function centroCostosUsuario($producto) {
        try {
            $sql = $this->db->connect()->prepare("SELECT
                                                    UPPER(p.ccodproy) AS codigo_costos,
                                                    UPPER(p.cdesproy) AS descripcion_costos,
                                                    p.nidreg AS ncodproy,
                                                    COALESCE(SUM(e.cant_ingr), 0) AS ingresos,
                                                    COALESCE((
                                                        SELECT SUM(td.ncanti)
                                                        FROM alm_transferdet td
                                                        WHERE td.idcprod = :codigo1 AND td.idcostos = p.nidreg
                                                    ), 0) AS transferencias,
                                                    COALESCE((
                                                        SELECT SUM(ac.cantsalida)
                                                        FROM alm_consumo ac
                                                        WHERE ac.idprod = :codigo2 AND ac.ncostos = p.nidreg
                                                    ), 0) AS consumos,
                                                    COALESCE((
                                                        SELECT SUM(ac.cantdevolucion)
                                                        FROM alm_consumo ac
                                                        WHERE ac.idprod = :codigo3 AND ac.ncostos = p.nidreg
                                                    ), 0) AS devolucion
                                                FROM
                                                    tb_costusu cu
                                                    INNER JOIN tb_proyectos p ON cu.ncodproy = p.nidreg
                                                    LEFT JOIN alm_cabexist c ON c.idcostos = p.nidreg
                                                    LEFT JOIN alm_existencia e ON e.idregistro = c.idreg AND e.codprod = :codigo4
                                                WHERE
                                                    cu.id_cuser = :user 
                                                    AND p.nflgactivo = 1
                                                    AND cu.nflgactivo = 1
                                                    AND p.veralm = 1
                                                GROUP BY
                                                    p.ccodproy, p.cdesproy, p.nidreg
                                                HAVING
                                                    COALESCE(SUM(e.cant_ingr), 0) > 0
                                                ORDER BY
                                                    p.ccodproy");

                $sql->execute([
                    "user" => $_SESSION['iduser'],
                    "codigo1" => $producto,
                    "codigo2" => $producto,
                    "codigo3" => $producto,
                    "codigo4" => $producto // Se repite porque se usa en múltiples subconsultas
                ]);

                $docData = [];

                while($row = $sql->fetch(PDO::FETCH_ASSOC)) {
                    $total = $row['ingresos'] - ($row['transferencias'] + $row['consumos'] - $row['devolucion']);
                    
                    $docData[] = [
                        'codigo_costos' => $row['codigo_costos'],
                        'descripcion_costos' => $row['descripcion_costos'],
                        'ncodproy' => $row['ncodproy'],
                        'total' => $total
                    ];
                }

                return $docData;
            } catch (PDOException $th) {
                echo($th->getMessage());
                
                return false;
            }
        }
    }
?>