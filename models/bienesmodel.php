<?php
    class BienesModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarItemsPaginado($criterio, $page, $porPagina) {
            try {
                $offset = ($page - 1) * $porPagina;
                $palabra = $criterio == "" ? "%" : "%" . $criterio . "%";

                // 1. Contar el total de registros para el paginador
                $sqlCount = $this->db->connect()->prepare("SELECT COUNT(cm_producto.id_cprod) as total 
                                                        FROM cm_producto 
                                                        WHERE cm_producto.cdesprod LIKE :criterio 
                                                        AND cm_producto.flgVisible = 1");
                $sqlCount->execute(["criterio" => $palabra]);
                $total = $sqlCount->fetch(PDO::FETCH_ASSOC)['total'];

                // 2. Traer los datos de la página actual
                $sql = $this->db->connect()->prepare("SELECT
                                                        cm_producto.id_cprod,
                                                        cm_producto.ccodprod,
                                                        UPPER(cm_producto.cdesprod) AS cdesprod,
                                                        cm_producto.flgActivo,
                                                        tb_parametros.cdescripcion AS tipo,
                                                        tb_unimed.cabrevia 
                                                    FROM cm_producto
                                                    INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed
                                                    INNER JOIN tb_parametros ON cm_producto.ntipo = tb_parametros.nidreg 
                                                    WHERE cm_producto.cdesprod LIKE :criterio
                                                    AND cm_producto.flgVisible = 1
                                                    ORDER BY id_cprod ASC
                                                    LIMIT :limit OFFSET :offset");
                
                $sql->bindValue(':criterio', $palabra, PDO::PARAM_STR);
                $sql->bindValue(':limit', $porPagina, PDO::PARAM_INT);
                $sql->bindValue(':offset', $offset, PDO::PARAM_INT);
                $sql->execute();
                
                $items = $sql->fetchAll(PDO::FETCH_ASSOC);

                // Devolvemos el JSON estructurado
                return array(
                    "total" => $total,
                    "items" => $items
                );

            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }

        public function obtenerJerarquiaCompleta() {
            try {
                // 1. Traer Tipos
                $stmt = $this->db->connect()->query("SELECT nidreg AS id, cdescripcion AS nombre, cdescripcion AS codigo 
                                                        FROM tb_parametros 
                                                        WHERE cclase = '07'
                                                        AND cdescripcion IN ('BIENES', 'SERVICIOS')");
                $tipos = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // 2. Traer Unidades
                $stmt = $this->db->connect()->query("SELECT ncodmed AS id, cdesmed AS nombre, ccodmed AS codigo FROM tb_unimed");
                $unidades = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // 3. Traer Grupos, Clases y Familias
                $stmt = $this->db->connect()->query("SELECT ncodgrupo AS id, ccodcata AS codigo, UPPER(cdescrip) AS nombre FROM tb_grupo WHERE nflgactivo = 1");
                $grupos = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($grupos as &$grupo) {
                    $stmtC = $this->db->connect()->prepare("SELECT ncodclase AS id, ccodcata AS codigo, UPPER(cdescrip) AS nombre FROM tb_clase WHERE ncodgrupo = :id");
                    $stmtC->execute(['id' => $grupo['id']]);
                    $grupo['clases'] = $stmtC->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($grupo['clases'] as &$clase) {
                        $stmtF = $this->db->connect()->prepare("SELECT ncodfamilia AS id, ccodcata AS codigo, UPPER(cdescrip) AS nombre FROM tb_familia WHERE ncodclase = :id");
                        $stmtF->execute(['id' => $clase['id']]);
                        $clase['familias'] = $stmtF->fetchAll(PDO::FETCH_ASSOC);
                    }
                }

                return array(
                    "tipos" => $tipos,
                    "unidades" => $unidades,
                    "grupos" => $grupos
                );

            } catch (PDOException $th) {
                return array("error" => $th->getMessage());
            }
        }

        public function generarCodigo($codigo){
            try {
                $sql = $this->db->connect()->prepare("SELECT COUNT( cm_producto.ccodprod ) AS generado 
                                                    FROM cm_producto 
                                                    WHERE cm_producto.ccodprod LIKE :codigo");
                $sql->execute(["codigo"=>$codigo."%"]);
                $result = $sql->fetchAll();

                return $codigo.str_pad(($result[0]['generado']+1),4,0,STR_PAD_LEFT);

            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function insertar($datos){
            $salida = false;
            $respuesta = false;
            $mensaje = "Error en el registro";
            $clase = "mensaje_error";

            if(!$this->verificar($datos['codigo'])){
                try {

                    $serie = array_key_exists('serie', $datos)? 1 : 0;
                    $detraccion = array_key_exists("detraccion",$datos)? 1 : 0;
                    // Por esto:
                    $actfij = array_key_exists("actfij",$datos)? 1 : 0; 

                    $sql = $this->db->connect()->prepare("INSERT INTO cm_producto 
                                                            SET ccodprod=:cod,cdesprod=:descripcion,ntipo=:tipo,ngrupo=:grupo,
                                                                nclase=:clase,nfam=:familia,nund=:unidad,cnparte=:parte,flgSerie=:serie,
                                                                flgDetrac=:detrac,flgActivo=:activo,iduser=:user,
                                                                flgActFij=:actfij,cCodPat=:codpat");
                    $sql->execute(["cod"=>$datos['codigo'],
                                    "descripcion"=>$datos['descripcion'],
                                    "tipo"=>$datos['codigo_tipo'],
                                    "grupo"=>$datos['codigo_grupo'],
                                    "clase"=>$datos['codigo_clase'],
                                    "familia"=>$datos['codigo_familia'],
                                    "unidad"=>$datos['codigo_unidad'],
                                    "parte"=>$datos['nro_parte'],
                                    "serie"=>$serie,
                                    "detrac"=>$detraccion,
                                    "activo"=>1,
                                    "user"=>$_SESSION['iduser'],
                                    "actfij"=>$actfij,
                                    "codpat"=>$datos['cod_pat']]);
                    $rowCount = $sql->rowCount();

                    if ($rowCount > 0){
                        $respuesta = true;
                        $mensaje = "Se registro correctamente";
                        $clase = "mensaje_correcto";
                    }
                    
                    $salida = array("respuesta"=>$respuesta,
                                    "mensaje"=>$mensaje,
                                    "clase"=>$clase);
                } catch (PDOException $th) {
                    echo $th->getMessage();
                    return false;
                }
            }else{
                $salida = array("respuesta"=>false,
                                "mensaje"=>"Codigo duplicado",
                                "clase"=>"mensaje_error");
            }

            return $salida;
        }

        private function verificar($codigo){
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                        COUNT( cm_producto.ccodprod ) AS numero 
                                                    FROM
                                                        cm_producto 
                                                    WHERE
                                                        cm_producto.ccodprod =:codigo");
                $sql->execute(["codigo"=>$codigo]);
                $result = $sql->fetchAll();
    
                return $result[0]['numero'];

            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }  
        }

        public function enviarCorreoCodigos($codigos, $correoDestino) {
            require_once("public/PHPMailer/PHPMailerAutoload.php");

            $estadoEnvio = false;
            $clase = "mensaje_error";
            $mensaje = "Error al enviar el correo";

            try {
                // 1. Armar la tabla HTML con los códigos
                $tablaHtml = '<table style="width: 100%; border-collapse: collapse; margin-top: 15px; font-family: Arial, sans-serif;">
                                <thead>
                                    <tr style="background-color: #2563eb; color: white;">
                                        <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Código</th>
                                        <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Descripción</th>
                                    </tr>
                                </thead>
                                <tbody>';

                foreach ($codigos as $item) {
                    $tablaHtml .= '<tr>
                                    <td style="padding: 8px 10px; border: 1px solid #ddd; font-weight: bold; color: #1d4ed8;">' . $item['codigo'] . '</td>
                                    <td style="padding: 8px 10px; border: 1px solid #ddd;">' . $item['descripcion'] . '</td>
                                </tr>';
                }

                $tablaHtml .= ' </tbody></table>';

                // 2. Armar el cuerpo del correo (reutilizando tu diseño)
                $mensajeHtml = '<div style="width:100%; display: flex; flex-direction: column; justify-content: center; align-items: center; font-family: Futura, Arial, sans-serif;">
                                    <div style="width: 80%; max-width: 600px; border: 1px solid #c2c2c2; background: #BBDAFF;">
                                        <h3 style="text-align: left; padding-left:20px;">Bienes y Servicios Sical</h3>
                                    </div>
                                    <div style="width: 80%; max-width: 600px; border-left: 1px solid #c2c2c2; border-right: 1px solid #c2c2c2; border-bottom: 1px solid #c2c2c2; background: #ffffff;">
                                        <p style="padding: 1rem; line-height: 1.2rem;">Se han registrado los siguientes códigos en el catálogo:</p>
                                        ' . $tablaHtml . '
                                        <p style="padding: 1rem; font-size: 12px; color: #666;">Este es un mensaje automático del sistema.</p>
                                    </div>
                                </div>';

                // 3. Configurar PHPMailer
                $mail = new PHPMailer;
                $mail->isSMTP();
                $mail->SMTPDebug = 0;
                $mail->Debugoutput = 'html';
                $mail->Host = 'mail.sepcon.net';
                $mail->SMTPAuth = true;
                $mail->Username = 'sistema_ibis@sepcon.net';
                $mail->Password = constant('MAILPASSWORD');
                $mail->Port = 465;
                $mail->SMTPSecure = "ssl";
                $mail->SMTPOptions = array(
                    'ssl' => array(
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => false
                    )
                );

                // 4. Configurar Remitente y Destinatario
                $mail->setFrom('sistema_ibis@sepcon.net', 'Sistema de Catálogo');
                $mail->addAddress($correoDestino); // El correo que el usuario ingresó en el input
                $mail->addAddress('sistema_ibis@sepcon.net', 'Copia de Seguridad'); // Copia oculta a ti mismo

                $mail->Subject = utf8_decode("Nuevos Códigos Creados - " . date('d/m/Y H:i'));
                $mail->msgHTML(utf8_decode($mensajeHtml));

                // 5. Enviar
                if (!$mail->send()) {
                    $estadoEnvio = false;
                    $mensaje = "No se pudo enviar el correo: " . $mail->ErrorInfo;
                } else {
                    $estadoEnvio = true;
                    $mensaje = "Correo enviado correctamente a " . $correoDestino;
                    $clase = "mensaje_correcto";
                }

                return array(
                    "respuesta" => $estadoEnvio,
                    "mensaje" => $mensaje,
                    "clase" => $clase
                );

            } catch (Exception $th) {
                return array(
                    "respuesta" => false,
                    "mensaje" => "Error: " . $th->getMessage(),
                    "clase" => "mensaje_error"
                );
            }
        }

        public function consultarId($id){
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                    cm_producto.id_cprod,
                                                    cm_producto.ccodprod,
                                                    cm_producto.cdesprod,
                                                    cm_producto.ntipo,
                                                    cm_producto.flgActivo,
                                                    cm_producto.cCodPat,
                                                    cm_producto.cnparte,
                                                    cm_producto.flgSerie, 
	                                                cm_producto.flgDetrac,
                                                    UPPER(CONCAT( tb_grupo.ccodcata, ' - ', tb_grupo.cdescrip )) AS grupo,
                                                    UPPER(CONCAT( tb_clase.ccodcata, ' - ', tb_clase.cdescrip )) AS clase,
                                                    UPPER(CONCAT( tb_familia.ccodcata, ' - ', tb_familia.cdescrip )) AS familia,
                                                    cm_producto.ngrupo,
                                                    cm_producto.nclase,
                                                    cm_producto.nfam,
                                                    cm_producto.nund,
                                                    tb_parametros.cdescripcion AS tipo,
                                                    UPPER(CONCAT( tb_unimed.ccodmed, ' - ', tb_unimed.cdesmed )) AS unidad 
                                                FROM
                                                    cm_producto
                                                    INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed
                                                    INNER JOIN tb_grupo ON cm_producto.ngrupo = tb_grupo.ncodgrupo
                                                    INNER JOIN tb_clase ON cm_producto.nclase = tb_clase.ncodclase
                                                    INNER JOIN tb_familia ON cm_producto.nfam = tb_familia.ncodfamilia
                                                    INNER JOIN tb_parametros ON cm_producto.ntipo = tb_parametros.nidreg 
                                                WHERE
                                                    cm_producto.id_cprod = :id");
                 $sql->execute(["id"=>$id]);
                 $rowCount = $sql->rowCount();
                 
                 if ($rowCount > 0) {
                     $docData = array();
                     while($row=$sql->fetch(PDO::FETCH_ASSOC)){
                         $docData[] = $row;
                     } 
                 }

                 return array("item"=>$docData);
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            } 
        }
    }
?>