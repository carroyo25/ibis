<?php
    class FirmasModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarOrdenesFirmas(){
            try {

                $filtroDocumento = $this->filtrarDocumentos();

                $cadena = "";

                if ($filtroDocumento == 1) {
                    $cadena = "AND lg_ordencab.ntipdoc IS NULL";
                }else if ($filtroDocumento == 2){
                    $cadena = "AND lg_ordencab.ntipdoc IS NOT NULL";
                }

                //echo $filtroDocumento;

                $salida = "";
                $sql = $this->db->connect()->query("SELECT
                                                    lg_ordencab.id_regmov,
                                                    lg_ordencab.cnumero,
                                                    lg_ordencab.ffechadoc,
                                                    lg_ordencab.nNivAten,
                                                    lg_ordencab.nEstadoDoc,
                                                    lg_ordencab.ncodpago,
                                                    lg_ordencab.nplazo,
                                                    lg_ordencab.nfirmaLog,
                                                    lg_ordencab.nfirmaFin,
                                                    lg_ordencab.nfirmaOpe,
                                                    FORMAT( lg_ordencab.ntotal, 2 ) AS ntotal,
                                                    UPPER( lg_ordencab.cObservacion ) AS concepto,
                                                    lg_ordencab.cdocPDF,
                                                    UPPER(cm_entidad.crazonsoc) AS crazonsoc,
                                                    tb_proyectos.ccodproy,
                                                    UPPER( CONCAT_WS( ' ', tb_area.ccodarea, tb_area.cdesarea ) ) AS area,
                                                    UPPER( CONCAT_WS( ' ', tb_proyectos.ccodproy, tb_proyectos.cdesproy ) ) AS costos,
                                                    tb_proyectos.nidreg,
                                                    tb_parametros.cdescripcion AS atencion,
                                                    monedas.cabrevia,
                                                    ( lg_ordencab.nfirmaLog + lg_ordencab.nfirmaFin + lg_ordencab.nfirmaOpe ) AS estado_firmas,
                                                    ( SELECT FORMAT( SUM( lg_ordendet.nunitario * lg_ordendet.ncanti ), 2 ) FROM lg_ordendet WHERE lg_ordendet.id_orden = lg_ordencab.id_regmov ) AS total_orden,
                                                    UPPER (tb_user.cnameuser) AS operador  
                                                FROM
                                                    lg_ordencab
                                                    INNER JOIN tb_pedidocab ON lg_ordencab.id_refpedi = tb_pedidocab.idreg
                                                    INNER JOIN tb_area ON lg_ordencab.ncodarea = tb_area.ncodarea
                                                    INNER JOIN tb_proyectos ON lg_ordencab.ncodpry = tb_proyectos.nidreg
                                                    INNER JOIN tb_parametros ON lg_ordencab.nNivAten = tb_parametros.nidreg
                                                    INNER JOIN cm_entidad ON lg_ordencab.id_centi = cm_entidad.id_centi
                                                    INNER JOIN lg_ordendet ON lg_ordencab.id_regmov = lg_ordendet.id_orden
                                                    INNER JOIN tb_user ON lg_ordencab.id_cuser = tb_user.iduser
                                                    INNER JOIN tb_parametros AS monedas ON lg_ordencab.ncodmon = monedas.nidreg
                                                WHERE
                                                    lg_ordencab.nEstadoDoc = 59
                                                    AND ( lg_ordencab.nfirmaLog IS NULL OR lg_ordencab.nfirmaOpe IS NULL OR lg_ordencab.nfirmaFin IS NULL )
                                                    $cadena
                                                GROUP BY
                                                    lg_ordencab.id_regmov 
                                                ORDER BY
                                                    lg_ordencab.id_regmov DESC");
                 $sql->execute();
                 $rowCount = $sql->rowCount();
 
                 if ($rowCount > 0){
                     while ($rs = $sql->fetch()) {
 
                         $log = is_null($rs['nfirmaLog']) ? '<i class="far fa-square"></i>' : '<i class="far fa-check-square"></i>';
                         $ope = is_null($rs['nfirmaOpe']) ? '<i class="far fa-square"></i>' : '<i class="far fa-check-square"></i>';
                         $fin = is_null($rs['nfirmaFin']) ? '<i class="far fa-square"></i>' : '<i class="far fa-check-square"></i>';
 
                         $flog = is_null($rs['nfirmaLog']) ? 0 : 1;
                         $fope = is_null($rs['nfirmaOpe']) ? 0 : 1;
                         $ffin = is_null($rs['nfirmaFin']) ? 0 : 1;

                         $resaltado = "";

                         if ($flog == 1 && $fope == 1 && $ffin == 1) {
                            $resaltado = "resaltado_firma";
                         }else {
                            $resaltado = "";
                         }

                         $atencion = $rs['nNivAten'] == 46 ? 'Aprobado </br> Urgente':'Normal';

                         $alerta_logistica = "";
                         $alerta_finanzas = "";
                         $alerta_operaciones = "";
                         $titulo_logistica  = "";
                         $titulo_operaciones = "";
                         $titulo_finanzas = "";
                         
                        $comentarios = $this->contarComentarios($rs['id_regmov']);

                        $nro        = "";
                        $obs_alerta = "";

                        if ( $comentarios['numero'] > 0 ) {
                            $nro = $comentarios['numero'] != 0 ?  $comentarios['numero'] :  "";
                            $obs_alerta = $comentarios['numero'] % 2 != 0 ?  "semaforoNaranja" :  "semaforoVerde";

                            $alerta_logistica   = $this-> buscarUserComentario($rs['id_regmov'],'633ae7e588a52') > 0 && $flog == 0 ? "urgente":"";  //logistica
                            $alerta_finanzas    = $this-> buscarUserComentario($rs['id_regmov'],'6288328f58068') > 0 && $ffin == 0 ? "urgente":"";  //Finanzas
                            $alerta_operaciones = $this-> buscarUserComentario($rs['id_regmov'],'62883306d1cd3') > 0 && $fope == 0 ? "urgente":"";  //operaciones
                        }
 
                        $salida .='<tr class="pointer '.$resaltado.'" data-indice="'.$rs['id_regmov'].'" 
                                                         data-estado="'.$rs['nEstadoDoc'].'"
                                                         data-finanzas="'.$ffin.'"
                                                         data-logistica="'.$flog.'"
                                                         data-operaciones="'.$fope.'"
                                                         data-firmas="'.$rs['estado_firmas'].'">
                                        <td class="textoCentro">'.str_pad($rs['cnumero'],4,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.date("d/m/Y", strtotime($rs['ffechadoc'])).'</td>
                                        <td class="pl20px">'.$rs['concepto'].'</td>
                                        <td class="pl20px">'.$rs['costos'].'</td>
                                        <td class="pl20px">'.$rs['crazonsoc'].'</td>
                                        <td class="pl20px">'.$rs['area'].'</td>
                                        <td class="textoCentro '.strtolower($rs['atencion']).'" style="font-size:.6rem">'.$atencion.'</td>
                                        <td class="textoDerecha pr10px">'.$rs['cabrevia'].' '.$rs['total_orden'].'</td>
                                        <td class="textoCentro">'.$rs['operador'].'</td>
                                        <td class="textoCentro '.$alerta_logistica.'" title="'.$titulo_logistica.'">'.$log.'</td>
                                        <td class="textoCentro '.$alerta_finanzas.'" title="'.$titulo_finanzas.'">'.$fin.'</td>
                                        <td class="textoCentro '.$alerta_operaciones.'" title="'.$titulo_operaciones.'">'.$ope.'</td>
                                        <td class="textoCentro '.$obs_alerta.'" ><span>'.$nro.'</span></td>
                                    </tr>';
                     }
                 }
 
                 return $salida;                    
            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }

        public function firmar($id){
            $operador = $this->obtenerOperador();
            $fecha =  date("Y-m-d");

            if ( $operador == "L" ) {
                $sql = $this->db->connect()->prepare("UPDATE lg_ordencab SET nfirmaLog=:fir,codperLog=:usr,fechaLog=:fecha WHERE id_regmov=:cod");
            }else if ($operador == "O") {
                $sql = $this->db->connect()->prepare("UPDATE lg_ordencab SET nfirmaOpe=:fir,codperOpe=:usr,fechaOpe=:fecha WHERE id_regmov=:cod");
            }else if ($operador == "F") {
                $sql = $this->db->connect()->prepare("UPDATE lg_ordencab SET nfirmaFin=:fir,codperFin=:usr,fechaFin=:fecha WHERE id_regmov=:cod");
            }
            //poner una funcion para verificar las tres firmas

            $sql->execute(["cod"=>$id,
                            "usr"=>$_SESSION['iduser'],
                            "fir"=>1,
                            "fecha"=>$fecha]);
            
            $rowCount = $sql->rowCount();
            
            if ($rowCount > 0){
                return array("mensaje"=>"Se autorizo la orden",
                            "clase"=>"mensaje_correcto",
                            "estado"=>true,
                            "listado"=>$this->listarOrdenesFirmas());
            }else {
                return array("mensaje"=>"Ya autorizo la orden",
                            "clase"=>"mensaje_error",
                            "operador"=>$operador,
                            "estado"=>false,
                            "listado"=>$this->listarOrdenesFirmas());
            }
        }

        public function firmarExpress($id,$numero) {
            $fecha =  date("Y-m-d");
            
            try {
                $operador = $this->obtenerOperador();
                $fecha =  date("Y-m-d");

                if ( $operador == "L" ) {
                    $sql = $this->db->connect()->prepare("UPDATE lg_ordencab SET nfirmaLog=:fir,codperLog=:usr,fechaLog=:fecha,nNivAten=:atencion WHERE id_regmov=:cod");
                }else if ($operador == "O") {
                    $sql = $this->db->connect()->prepare("UPDATE lg_ordencab SET nfirmaOpe=:fir,codperOpe=:usr,fechaOpe=:fecha,nNivAten=:atencion WHERE id_regmov=:cod");
                }else if ($operador == "F") {
                    $sql = $this->db->connect()->prepare("UPDATE lg_ordencab SET nfirmaFin=:fir,codperFin=:usr,fechaFin=:fecha,nNivAten=:atencion WHERE id_regmov=:cod");
                }

                $sql->execute(["cod"=>$id,
                                "usr"=>$_SESSION['iduser'],
                                "fir"=>1,
                                "fecha"=>$fecha,
                                "atencion"=> 46]);
                
                $rowCount = $sql->rowCount();
                
                if ($rowCount > 0){
                    $this->enviarCorreoAviso($numero);

                    return array("mensaje"=>"Se autorizo la orden",
                                "clase"=>"mensaje_correcto",
                                "estado"=>true,
                                "listado"=>$this->listarOrdenesFirmas());
                }else {
                    return array("mensaje"=>"Ya autorizo la orden",
                                "clase"=>"mensaje_error",
                                "operador"=>$operador,
                                "estado"=>false,
                                "listado"=>$this->listarOrdenesFirmas());
            }
            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }

        public function enviarCorreoAviso($id){
            try {
                require_once("public/PHPMailer/PHPMailerAutoload.php");

            
                $subject    = utf8_decode("Aprobación de orden urgente");
                

                $messaje= '<div style="width:100%;display: flex;flex-direction: column;justify-content: center;align-items: center;
                                    font-family: Futura, Arial, sans-serif;">
                            <div style="width: 45%;border: 1px solid #c2c2c2;background: gold">
                                <h1 style="text-align: center;">Alerta del sistema</h1>
                            </div>
                            <div style="width: 45%;
                                        border-left: 1px solid #c2c2c2;
                                        border-right: 1px solid #c2c2c2;
                                        border-bottom: 1px solid #c2c2c2;">
                                <p style="padding:.5rem"><strong style="font-style: italic;">Ing:</strong></p>
                                <p style="padding:.5rem;line-height: 1rem;">El presente correo es para informar que se ha aprobado la orden Nro. '.$id.' en forma urgente.</p>
                                <p style="padding:.5rem">Realizado por : '. $_SESSION['nombres'].'</p>
                                <p style="padding:.5rem">Fecha de Aprobación : '. date("d/m/Y h:i:s") .'</p>
                            </div>
                        </div>';

                $origen = "sical@sepcon.net";
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
                
                $mail->setFrom($origen,$nombre_envio);
                $mail->addAddress('carroyo@sepcon.net','Cesar Arroyo');
                $mail->addAddress('mvirreira@sepcon.net','Mauricio Virreira');
                $mail->addAddress('jpaniagua@sepcon.net','Jose Paniagua');
                $mail->addAddress('asolari@sepcon.net','Alberto Solari');
                
                $mail->Subject = $subject;
                $mail->msgHTML(utf8_decode($messaje));
   
                if (!$mail->send()) {
                    return array("mensaje"=>"Hubo un error, en el envio",
                                "clase"=>"mensaje_error");
                }
                        
                $mail->clearAddresses();


            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            } 
        }

        public function consultarPrecios($codigo,$descripcion){
            try {
                $salida = "";
                $sql = $this->db->connect()->prepare("SELECT
                                                        cm_producto.id_cprod,
                                                        cm_producto.ccodprod,
                                                        UPPER(cm_producto.cdesprod) AS cdesprod,
                                                        lg_ordendet.ncanti,
                                                        lg_ordendet.nunitario,
                                                        UPPER(lg_ordendet.cobserva) AS cobserva,
                                                        lg_ordencab.cnumero AS orden,
                                                        tb_unimed.cabrevia AS unidad,
                                                        tb_parametros.cabrevia AS moneda,
                                                        LPAD( tb_pedidocab.nrodoc, 6, 0 ) AS pedido,
                                                        tb_proyectos.ccodproy,
                                                        UPPER( tb_proyectos.cdesproy ) AS cdesproy,
                                                        DATE_FORMAT( lg_ordencab.ffechadoc, '%d/%m/%Y' ) AS fecha,
                                                    IF
                                                        ( lg_ordencab.ncodmon != 20, FORMAT( lg_ordencab.ntcambio, 2 ), 1 ) AS tipo_cambio
                                                    FROM
                                                        cm_producto
                                                        INNER JOIN lg_ordendet ON cm_producto.id_cprod = lg_ordendet.id_cprod
                                                        INNER JOIN lg_ordencab ON lg_ordendet.id_orden = lg_ordencab.id_regmov
                                                        INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed
                                                        INNER JOIN tb_parametros ON lg_ordencab.ncodmon = tb_parametros.nidreg
                                                        INNER JOIN tb_pedidocab ON lg_ordencab.id_refpedi = tb_pedidocab.idreg
                                                        INNER JOIN tb_proyectos ON lg_ordencab.ncodpry = tb_proyectos.nidreg
                                                        INNER JOIN cm_entidad ON lg_ordencab.id_centi = cm_entidad.id_centi
                                                        INNER JOIN tb_pedidodet ON lg_ordendet.niddeta = tb_pedidodet.iditem 
                                                    WHERE
                                                        cm_producto.id_cprod = :codigo
                                                        AND CONCAT(cm_producto.cdesprod,' ', tb_pedidodet.observaciones) = :descripcion
                                                    ORDER BY
                                                        lg_ordencab.ffechadoc ASC");
                $sql->execute(["codigo"=>$codigo, "descripcion"=>$descripcion]);
                $rowCount = $sql->rowcount();

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()) {

                        $tipo_cambio = $rs['tipo_cambio'] > 1 ? $rs['tipo_cambio'] : ''; 
 
                        $salida .='<tr>
                                        <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                        <td class="pl10px">'.$rs['cdesprod'].'</td>
                                        <td class="pl10px">'.$rs['ccodproy'].'</td>
                                        <td class="textoCentro">'.$rs['moneda'].'</td>
                                        <td class="textoDerecha pr20px">'.$rs['nunitario'].'</td>
                                        <td class="textoDerecha pr20px">'.$rs['ncanti'].'</td>
                                        <td class="textoCentro">'.$rs['pedido'].'</td>
                                        <td class="textoCentro">'.$rs['orden'].'</td>
                                        <td class="pl10px">'.$rs['cobserva'].'</td>
                                        <td class="textoDerecha pr20px">'.$tipo_cambio.'</td>
                                        <td class="textoCentro">'.$rs['fecha'].'</td>
                                    </tr>';
                    }

                }

                return $salida;

            } catch (PDOException $th) {
                    echo "Error: " . $th->getMessage();
                    return false;
                }
        }

        public function filtrarOrdenesFirmas($parametros){
            try {
                //$mes  = date("m");

                $tipo   = $parametros['tipoSearch'] == -1 ? "%" : $parametros['tipoSearch'];
                $costos = $parametros['costosSearch'] == -1 ? "%" : $parametros['costosSearch'];
                $mes    = $parametros['mesSearch'] == -1 ? "%" :  $parametros['mesSearch'];
                $anio   = $parametros['anioSearch'];

                $filtroDocumento = $this->filtrarDocumentos();

                $cadena = "";

                if ($filtroDocumento == 1) {
                    $cadena = "AND lg_ordencab.ntipdoc IS NULL";
                }else if ($filtroDocumento == 2){
                    $cadena = "AND lg_ordencab.ntipdoc IS NOT NULL";
                }


                $salida = "";
                 $sql = $this->db->connect()->prepare("SELECT
                                                            lg_ordencab.id_regmov,
                                                            lg_ordencab.cnumero,
                                                            lg_ordencab.ffechadoc,
                                                            lg_ordencab.nNivAten,
                                                            lg_ordencab.nEstadoDoc,
                                                            lg_ordencab.ncodpago,
                                                            lg_ordencab.nplazo,
                                                            lg_ordencab.nfirmaLog,
                                                            lg_ordencab.nfirmaFin,
                                                            lg_ordencab.nfirmaOpe,
                                                            FORMAT(lg_ordencab.ntotal,2) as ntotal,
                                                            UPPER(lg_ordencab.cObservacion) AS concepto,
                                                            lg_ordencab.cdocPDF,
                                                            UPPER(
                                                                    CONCAT_WS(
                                                                        ' ',
                                                                        tb_area.ccodarea,
                                                                        tb_area.cdesarea
                                                                    )
                                                                ) AS area,
                                                            UPPER(
                                                                    CONCAT_WS(
                                                                        ' ',
                                                                        tb_proyectos.ccodproy,
                                                                        tb_proyectos.cdesproy
                                                                    )
                                                                ) AS costos,
                                                            tb_proyectos.nidreg,
                                                            tb_proyectos.ccodproy,
                                                            tb_parametros.cdescripcion AS atencion,
                                                            (
                                                                lg_ordencab.nfirmaLog + lg_ordencab.nfirmaFin + lg_ordencab.nfirmaOpe
                                                            ) AS estado_firmas,
                                                            UPPER(cm_entidad.crazonsoc) AS crazonsoc,
                                                            UPPER (tb_user.cnameuser) AS operador
                                                            FROM
                                                            lg_ordencab
                                                            INNER JOIN tb_pedidocab ON lg_ordencab.id_refpedi = tb_pedidocab.idreg
                                                            INNER JOIN tb_area ON lg_ordencab.ncodarea = tb_area.ncodarea
                                                            INNER JOIN tb_proyectos ON lg_ordencab.ncodpry = tb_proyectos.nidreg
                                                            INNER JOIN tb_parametros ON lg_ordencab.nNivAten = tb_parametros.nidreg
                                                            INNER JOIN cm_entidad ON lg_ordencab.id_centi = cm_entidad.id_centi
                                                            INNER JOIN tb_user ON lg_ordencab.id_cuser = tb_user.iduser
                                                            WHERE
                                                                lg_ordencab.nEstadoDoc = 59 
                                                                AND lg_ordencab.ncodpry LIKE :costos 
                                                                AND lg_ordencab.ntipmov LIKE :tipomov 
                                                                AND MONTH ( lg_ordencab.ffechadoc ) LIKE :mes
                                                                AND YEAR ( lg_ordencab.ffechadoc ) LIKE :anio
                                                                AND (lg_ordencab.nfirmaLog IS NULL OR lg_ordencab.nfirmaOpe IS NULL  OR lg_ordencab.nfirmaFin IS NULL )
                                                                $cadena");
                                                                
                 $sql->execute(["tipomov"=>$tipo,
                                "costos"=>$costos,
                                "mes"=>$mes,
                                "anio"=>$anio]);

                 $rowCount = $sql->rowCount();
 
                 if ($rowCount > 0){
                     while ($rs = $sql->fetch()) {
 
                         $log = is_null($rs['nfirmaLog']) ? '<i class="far fa-square"></i>' : '<i class="far fa-check-square"></i>';
                         $ope = is_null($rs['nfirmaOpe']) ? '<i class="far fa-square"></i>' : '<i class="far fa-check-square"></i>';
                         $fin = is_null($rs['nfirmaFin']) ? '<i class="far fa-square"></i>' : '<i class="far fa-check-square"></i>';
 
                         $flog = is_null($rs['nfirmaLog']) ? 0 : 1;
                         $fope = is_null($rs['nfirmaOpe']) ? 0 : 1;
                         $ffin = is_null($rs['nfirmaFin']) ? 0 : 1;

                         $resaltado = "";

                         if ($flog == 1 && $fope == 1 && $ffin == 1) {
                            $resaltado = "resaltado_firma";
                         }else {
                            $resaltado = "";
                         }
 
                         $salida .='<tr class="pointer '.$resaltado.'" data-indice="'.$rs['id_regmov'].'" 
                                                         data-estado="'.$rs['nEstadoDoc'].'"
                                                         data-finanzas="'.$ffin.'"
                                                         data-logistica="'.$flog.'"
                                                         data-operaciones="'.$fope.'"
                                                         data-firmas="'.$rs['estado_firmas'].'">
                                     <td class="textoCentro">'.str_pad($rs['cnumero'],4,0,STR_PAD_LEFT).'</td>
                                     <td class="textoCentro">'.date("d/m/Y", strtotime($rs['ffechadoc'])).'</td>
                                     <td class="pl20px">'.$rs['concepto'].'</td>
                                     <td class="pl20px">'.utf8_decode($rs['ccodproy']).'</td>
                                     <td class="pl20px">'.$rs['crazonsoc'].'</td>
                                     <td class="pl20px">'.$rs['area'].'</td>
                                     <td class="textoCentro '.strtolower($rs['atencion']).'">'.$rs['atencion'].'</td>
                                     <td class="textoDerecha pr10px">'.$rs['ntotal'].'</td>
                                     <td class="textoCentro">'.$rs['operador'].'</td>
                                     <td class="textoCentro">'.$log.'</td>
                                     <td class="textoCentro">'.$fin.'</td>
                                     <td class="textoCentro">'.$ope.'</td>
                                     </tr>';
                     }
                 }
 
                 return $salida; 


            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }

        private function obtenerOperador(){
            try {
                
                $sql = $this->db->connect()->prepare("SELECT rol FROM tb_user WHERE iduser=:usr");
                $sql->execute(["usr"=>$_SESSION["iduser"]]);
                $result = $sql->fetchAll();

                return $result[0]['rol'];

            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }

        private function filtrarDocumentos(){
            try {
                $sql = $this->db->connect()->prepare("SELECT tb_user.nrol,tb_user.nflgvista,tb_user.ccargo FROM tb_user WHERE tb_user.iduser =:usr");
                $sql->execute(["usr"=>$_SESSION["iduser"]]);
                $result = $sql->fetchAll();

                return $result[0]['nflgvista'];

            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }
    }
?>