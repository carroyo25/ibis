<?php
    class ContratosModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarContratos($user){
           try {
                $salida = "";
                $sql = $this->db->connect()->prepare("SELECT
                                                        tb_costusu.ncodcos,
                                                        tb_costusu.ncodproy,
                                                        tb_costusu.id_cuser,
                                                        lg_ordencab.id_regmov,
                                                        lg_ordencab.cnumero,
                                                        lg_ordencab.ffechadoc,
                                                        lg_ordencab.nNivAten,
                                                        lg_ordencab.nEstadoDoc,
                                                        lg_ordencab.ncodpago,
                                                        lg_ordencab.nplazo,
                                                        lg_ordencab.cdocPDF,
                                                        FORMAT(lg_ordencab.ntotal,2) AS ntotal,
                                                        tb_proyectos.ccodproy,
                                                        UPPER( lg_ordencab.cObservacion ) AS concepto,
                                                        UPPER( tb_pedidocab.detalle ) AS detalle,
                                                        UPPER(
                                                        CONCAT_WS( tb_area.ccodarea, tb_area.cdesarea )) AS area,
                                                        UPPER(
                                                        CONCAT_WS( tb_proyectos.ccodproy, tb_proyectos.cdesproy )) AS costos,
                                                        lg_ordencab.nfirmaLog,
                                                        lg_ordencab.nfirmaFin,
                                                        lg_ordencab.nfirmaOpe,
                                                        tb_parametros.cdescripcion AS atencion,
                                                        UPPER(cm_entidad.crazonsoc) AS crazonsoc,
                                                        UPPER( tb_user.cnameuser ) AS cnameuser,
                                                        monedas.cabrevia,
                                                        ( SELECT COUNT( lg_ordencomenta.id_regmov ) FROM lg_ordencomenta WHERE lg_ordencomenta.id_regmov = lg_ordencab.id_regmov ) AS comentario 
                                                    FROM
                                                        tb_costusu
                                                        INNER JOIN lg_ordencab ON tb_costusu.ncodproy = lg_ordencab.ncodpry
                                                        INNER JOIN tb_pedidocab ON lg_ordencab.id_refpedi = tb_pedidocab.idreg
                                                        INNER JOIN tb_area ON lg_ordencab.ncodarea = tb_area.ncodarea
                                                        INNER JOIN tb_proyectos ON lg_ordencab.ncodpry = tb_proyectos.nidreg
                                                        INNER JOIN tb_parametros ON lg_ordencab.nNivAten = tb_parametros.nidreg
                                                        INNER JOIN cm_entidad ON lg_ordencab.id_centi = cm_entidad.id_centi
                                                        INNER JOIN tb_user ON lg_ordencab.id_cuser = tb_user.iduser
                                                        INNER JOIN tb_parametros AS monedas ON lg_ordencab.ncodmon = monedas.nidreg  
                                                    WHERE
                                                        tb_costusu.id_cuser = :user
                                                        AND lg_ordencab.ntipdoc = 2
                                                        AND tb_costusu.nflgactivo = 1
                                                        AND lg_ordencab.nEstadoDoc BETWEEN 49 
                                                        AND 59
                                                    ORDER BY  lg_ordencab.id_regmov DESC");
                $sql->execute(["user"=>$_SESSION['iduser']]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0){
                    while ($rs = $sql->fetch()) {

                        $log = is_null($rs['nfirmaLog']) ? '<i class="far fa-square"></i>' : '<i class="far fa-check-square"></i>';
                        $ope = is_null($rs['nfirmaOpe']) ? '<i class="far fa-square"></i>' : '<i class="far fa-check-square"></i>';
                        $fin = is_null($rs['nfirmaFin']) ? '<i class="far fa-square"></i>' : '<i class="far fa-check-square"></i>';

                        $flog = is_null($rs['nfirmaLog']) ? 0 : 1;
                        $fope = is_null($rs['nfirmaOpe']) ? 0 : 1;
                        $ffin = is_null($rs['nfirmaFin']) ? 0 : 1;

                        $resaltado = $rs['nEstadoDoc'] == 59 ? "resaltado_firma" :  "";
                        $observado = "";
                        $obs_alerta = "";
                        $alerta_logistica   = "";  //logistica
                        $alerta_finanzas    = "";  //Finanzas
                        $alerta_operaciones = "";  //operaciones

                        if ( $rs['comentario'] > 0 ) {
                            $observado = $rs['comentario'] != 0 ?  $rs['comentario'] :  "";
                            $obs_alerta = $rs['comentario'] % 2 > 0 ?  "semaforoNaranja" :  "semaforoVerde";

                            $alerta_logistica   = $this-> buscarUserComentario($rs['id_regmov'],'633ae7e588a52') > 0 && $flog == 0 ? "urgente":"";  //logistica
                            $alerta_finanzas    = $this-> buscarUserComentario($rs['id_regmov'],'6288328f58068') > 0 && $ffin == 0 ? "urgente":"";  //Finanzas
                            $alerta_operaciones = $this-> buscarUserComentario($rs['id_regmov'],'62883306d1cd3') > 0 && $fope == 0 ? "urgente":"";  //operaciones
                        }


                        $salida .='<tr class="pointer '.$resaltado.'" data-indice="'.$rs['id_regmov'].'" 
                                                        data-estado="'.$rs['nEstadoDoc'].'"
                                                        data-finanzas="'.$ffin.'"
                                                        data-logistica="'.$flog.'"
                                                        data-operaciones="'.$fope.'">
                                    <td class="textoCentro">'.str_pad($rs['cnumero'],6,0,STR_PAD_LEFT).'</td>
                                    <td class="textoCentro">'.date("d/m/Y", strtotime($rs['ffechadoc'])).'</td>
                                    <td class="pl20px">'.$rs['concepto'].'</td>
                                    <td class="pl20px">'.utf8_decode($rs['ccodproy']).'</td>
                                    <td class="pl20px">'.$rs['area'].'</td>
                                    <td class="pl20px">'.$rs['crazonsoc'].'</td>
                                    <td class="pl5px">'.$rs['cnameuser'].'</td>
                                    <td class="textoDerecha">'.$rs['cabrevia'].' '. $rs['ntotal'].'</td>
                                    <td class="textoCentro '.strtolower($rs['atencion']).'">'.$rs['atencion'].'</td>
                                    <td class="textoCentro '.$alerta_logistica.'">'.$log.'</td>
                                    <td class="textoCentro '.$alerta_finanzas.'">'.$fin.'</td>
                                    <td class="textoCentro '.$alerta_operaciones.'">'.$ope.'</td>
                                    <td class="textoCentro '.$obs_alerta.'" >'.$observado.'</td>
                                    </tr>';
                    }
                }

                return $salida;                    
           } catch (PDOException $th) {
               echo "Error: " . $th->getMessage();
               return false;
           }
        }

        public function insertarContrato($cabecera,$detalles,$comentarios,$adicionales,$adjuntos,$usuario,$descripcion){
            try {
                $salida = false;
                $respuesta = false;
                $mensaje = "Error en el registro";
                $clase = "mensaje_error";
                $cab = json_decode($cabecera);

                $orden = $this->generarNumeroOrden();
                
                $periodo = explode('-',$cab->emision);
                $dias_entrega = intval($cab->dias);

                $sql = $this->db->connect()->prepare("INSERT INTO lg_ordencab SET id_refpedi=:pedi,cper=:anio,cmes=:mes,ntipmov=:tipo,cnumero=:orden,
                                                                                ffechadoc=:fecha,ffechaent=:entrega,id_centi=:entidad,ncodmon=:moneda,ntcambio=:tcambio,
                                                                                nigv=:igv,ntotal=:total,ncodpry=:proyecto,ncodcos=:ccostos,ncodarea=:area,
                                                                                ctiptransp=:transporte,id_cuser=:elabora,ncodpago=:pago,nplazo=:pentrega,cnumcot=:cotizacion,
                                                                                cdocPDF=:adjunto,nEstadoDoc=:est,ncodalm=:almacen,nflgactivo=:flag,nNivAten=:atencion,
                                                                                cverificacion=:verif,cObservacion=:observacion,cReferencia=:referencia,
                                                                                nAdicional=:adicional,lentrega=:lugar,ntipdoc=:tipoDocum");

                $sql ->execute(["pedi"=>$cab->codigo_pedido,
                                "anio"       =>$periodo[0],
                                "mes"        =>$periodo[1],
                                "tipo"       =>$cab->codigo_tipo,
                                "orden"      =>$orden,
                                "fecha"      =>$cab->emision,
                                "entrega"    =>$cab->fentrega,
                                "entidad"    =>$cab->codigo_entidad,
                                "moneda"     =>$cab->codigo_moneda,
                                "tcambio"    =>$cab->tcambio,
                                "igv"        =>$cab->radioIgv,
                                "total"      =>$cab->total_numero,
                                "proyecto"   =>$cab->codigo_costos,
                                "ccostos"    =>$cab->codigo_costos,
                                "area"       =>$cab->codigo_area,
                                "transporte" =>$cab->codigo_transporte,
                                "elabora"    =>$usuario,
                                "pago"       =>$cab->codigo_pago,
                                "pentrega"   =>$dias_entrega,
                                "cotizacion" =>$cab->proforma,
                                "adjunto"    =>$cab->vista_previa,
                                "est"        =>49,
                                "almacen"    =>$cab->codigo_almacen,
                                "flag"       =>1,
                                "atencion"   =>47,
                                "verif"      =>$cab->codigo_verificacion,
                                "cotizacion" =>$cab->ncotiz,
                                "observacion"=>$cab->concepto,
                                "referencia" =>$cab->referencia,
                                "adicional"  =>$cab->total_adicional,
                                "lugar"      =>$cab->lentrega,
                                "tipoDocum"  =>2]); //el numero 2 es para los contratos
                $rowCount = $sql->rowCount();

                if ($rowCount > 0){
                    $indice = $this->lastInsertOrder();
                    $this->grabarDetalles($indice,$detalles,$cab->codigo_costos,$orden);
                    $this->grabarComentarios($indice,$comentarios,$usuario);
                    $this->grabarAdicionales($indice,$adicionales);
                    $this->grabarCondicionesContrato($indice,$descripcion);
                    $this->actualizarDetallesPedido(84,$detalles,$orden,$cab->codigo_entidad);
                    $this->actualizarCabeceraPedido(58,$cab->codigo_pedido,$orden);
                    $respuesta = true;
                    $mensaje = "Orden Grabada";
                    $clase = "mensaje_correcto";
                }

                $salida = array("respuesta"=>$respuesta,
                                "mensaje"=>$mensaje,
                                "clase"=>$clase,
                                "orden"=>$orden);

            
                return $salida;
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }    
        }

        public function modificarContrato($cabecera,$detalles,$comentarios,$usuario,$condiciones){
            try {
                $entrega = $cabecera['dias'];

                $sql = $this->db->connect()->prepare("UPDATE lg_ordencab 
                                                        SET  ffechaent=:entrega,ntotal=:total,ctiptransp=:transp,
                                                             nplazo=:plazo,ncodalm=:alm,nigv =:igv,id_centi=:enti,
                                                             ncodpago=:pago,cnumcot=:cotizacion,creferencia=:referencia,
                                                             lentrega=:lugar,ncodmon=:moneda,cObservacion=:observacion
                                                        WHERE id_regmov = :id");
                $sql->execute(['entrega'=>$cabecera['fentrega'],
                                "total"=>$cabecera['total_numero'],
                                "transp"=>$cabecera['codigo_transporte'],
                                "plazo"=>$entrega,
                                "alm"=>$cabecera['codigo_almacen'],
                                "igv"=>$cabecera['radioIgv'],
                                "id"=>$cabecera['codigo_orden'],
                                "enti"=>$cabecera['codigo_entidad'],
                                "pago"=>$cabecera['codigo_pago'],
                                "cotizacion"=>$cabecera['ncotiz'],
                                "referencia"=>$cabecera['referencia'],
                                "lugar"=>$cabecera['lentrega'],
                                "moneda"=>$cabecera['codigo_moneda'],
                                "observacion"=>$cabecera['concepto']]);
                
                $this->grabarDetalles($cabecera['codigo_orden'],$detalles,$cabecera['codigo_costos'],$cabecera['codigo_orden']);
                $this->grabarComentarios($cabecera['codigo_verificacion'],$comentarios,$usuario);

                if ($this->buscarCondicciones( $cabecera['codigo_orden'] ) === 0 ) {
                    $this->grabarCondicionesContrato($cabecera['codigo_orden'],$condiciones);
                }else {
                    $this->actualizarCondicionesContrato($cabecera['codigo_orden'],$condiciones);
                }

                $salida = array("respuesta"=>true,
                                "mensaje"=>"Registro modificado",
                                "clase"=>"mensaje_correcto");

                
                return $salida;

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            } 
        }

        private function grabarAdicionales($indice,$adicionales){
            try {
                
                $datos = json_decode($adicionales);
                $nreg = count($datos);

                for ($i=0; $i < $nreg ; $i++) { 
                    $sql = $this->db->connect()->prepare("INSERT INTO lg_ordenadic SET idorden=:orden,
                                                                                        idcenti=:entidad,
                                                                                        cconcepto=:concepto,
                                                                                        nmonto=:total");
                    $sql->execute(["orden"=>$indice,
                                    "entidad"=>$datos[$i]->entidad,
                                    "concepto"=>$datos[$i]->descripcion,
                                    "total"=>$datos[$i]->valor]);
                }
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function grabarDetalles($indice,$detalles,$costos,$idx){
            try {
                $datos = json_decode($detalles);
                
                $nreg = count($datos);

                for ($i=0; $i < $nreg; $i++) { 
                    if(!$datos[$i]->grabado) {
                        $total = $datos[$i]->cantidad * $datos[$i]->precio;
                        $sql = $this->db->connect()->prepare("INSERT INTO lg_ordendet SET id_regmov=:id,niddeta=:nidp,id_cprod=:cprod,ncanti=:cant,
                                                                                    nunitario=:unit,nigv=:igv,ntotal=:total,
                                                                                    nestado=:est,cverifica=:verif,nidpedi=:pedido,
                                                                                    nmonref=:moneda,ncodcos=:costos,id_orden=:ordenidx,
                                                                                    nSaldo=:saldo,cobserva=:detalles,item=:itemord");
                        $sql->execute(["id"=>$indice,
                                        "nidp"=>$datos[$i]->itped,
                                        "pedido"=>$datos[$i]->refpedi,
                                        "cprod"=>$datos[$i]->codprod,
                                        "cant"=>$datos[$i]->cantidad,
                                        "unit"=>$datos[$i]->precio,
                                        "igv"=>$datos[$i]->igv,
                                        "total"=>$total,
                                        "est"=>1,
                                        "verif"=>"",
                                        "moneda"=>$datos[$i]->moneda,
                                        "costos"=>$costos,
                                        "ordenidx"=>$idx,
                                        "saldo"=>$datos[$i]->cantidad,
                                        "detalles"=>$datos[$i]->detalles,
                                        "itemord"=>$datos[$i]->item]);
                    }else{
                        $sql = $this->db->connect()->prepare("UPDATE lg_ordendet 
                                                            SET ncanti=:cant,nunitario=:unit,nigv=:igv,
                                                                ntotal=:total,cobserva=:detalles
                                                            WHERE lg_ordendet.nitemord =:idx");
                        $sql->execute(["cant"=>$datos[$i]->cantidad,
                                        "unit"=>$datos[$i]->precio,
                                        "igv"=>$datos[$i]->igv,
                                        "total"=>$datos[$i]->total,
                                        "detalles"=>$datos[$i]->detalles,
                                        "idx"=>$datos[$i]->indice]);
                    }
                    
                }
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function actualizarDetallesPedido($estado,$detalles,$orden,$entidad){
            try {
                $datos = json_decode($detalles);
                $nreg = count($datos);

                for ($i=0; $i <$nreg ; $i++) { 
                    if($datos[$i]->cantidad == $datos[$i]->cantped) {
                        $estado = 84;
                        $swOrden = 1;    
                    }else{
                        $estado = 54;
                        $swOrden = 0;
                    }

                    $sql = $this->db->connect()->prepare("UPDATE tb_pedidodet SET 
                                                        estadoItem=:est,
                                                        idorden=:orden,
                                                        nflgOrden=:swOrden, 
                                                        cant_orden=:pendiente WHERE iditem=:item");
                    $sql->execute(["item"=>$datos[$i]->itped,
                                    "est"=>$estado,
                                    "orden"=>$orden,
                                    "swOrden"=>$swOrden,
                                    "pendiente"=>$datos[$i]->cantidad]);
                    
                    $this->registrarOrdenesItems($datos[$i]->itped,$orden,$entidad);                
                }
                
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function actualizarDetallesPedidoCorreo($estado,$detalles){
            try {
                $datos = json_decode($detalles);
                $nreg = count($datos);

                for ($i=0; $i <$nreg ; $i++) { 
                    $sql = $this->db->connect()->prepare("UPDATE tb_pedidodet SET 
                                                        estadoItem=:est WHERE iditem=:item");
                    $sql->execute(["item"=>$datos[$i]->itped,
                                    "est"=>$estado]);
                }
                
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function generarNumeroOrden(){
            try {
                //$sql = $this->db->connect()->query("SELECT MAX(id_regmov) AS numero FROM lg_ordencab");
                $sql = $this->db->connect()->query("SELECT MAX(id_regmov) AS numero FROM lg_ordencab WHERE YEAR(lg_ordencab.fregsys) = YEAR(NOW());");
                $sql->execute();

                $result = $sql->fetchAll();
                
                return $result[0]['numero']+1;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function grabarCondicionesContrato($indice,$descripcion){
            try {
                $sql = $this->db->connect()->prepare("INSERT INTO lg_ordenextras SET idorden=:indice,cdescription=:descripcion,nflgactivo=:activo");
                $sql->execute(["indice"=>$indice, "descripcion"=>$descripcion, "activo"=>1]);
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            } 
        }

        private function registrarOrdenesItems($item,$orden,$entidad){
            try {
                $sql = $this->db->connect()->prepare("INSERT INTO tb_itemorden SET item=:item, orden=:orden, entidad=:entidad");
                $sql->execute(["item"=>$item, "orden"=>$orden, "entidad"=>$entidad]);
            } catch (PDOException $th) {
                echo "Error: ". $th->getMessage();
                return false;
            }
        }

        private function actualizarCabeceraPedido($estado,$pedido,$orden){
            try {
                $sql = $this->db->connect()->prepare("UPDATE tb_pedidocab SET estadodoc=:est,idorden=:orden WHERE idreg=:id");
                $sql->execute(["est"=>$estado,
                                "id"=>$pedido,
                                "orden"=>$orden]);
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function actualizarCabeceraOrden($estado,$orden,$fecha){
            try {
                $sql = $this->db->connect()->prepare("UPDATE lg_ordencab 
                                                        SET lg_ordencab.nEstadoDoc=:est,
                                                            lg_ordencab.ffechades=:descarga  
                                                        WHERE id_regmov=:id");
                $sql->execute(["est"=>$estado,
                                "id"=>$orden,
                                "descarga"=>$fecha]);
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function buscarCondicciones($orden){
            try {
                $sql = $this->db->connect()->prepare("SELECT COUNT(*) AS condiciones FROM lg_ordenextras WHERE lg_ordenextras.idorden=:id AND lg_ordenextras.nflgactivo=1");
                $sql->execute(["id"=>$orden]);

                $result = $sql ->fetchAll();

                return $result[0]['condiciones'];
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function actualizarCondicionesContrato($indice,$descripcion){
            try {
                $sql = $this->db->connect()->prepare("UPDATE lg_ordenextras SET cdescription=:descripcion WHERE idorden=:indice AND nflgactivo=1");
                $sql->execute(["indice"=>$indice, "descripcion"=>$descripcion]);
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            } 
        }

        public function enviarCorreoContrato($cabecera,$detalles,$correos,$asunto,$mensaje,$condiciones){
            try {
                require_once("public/PHPMailer/PHPMailerAutoload.php");

                $documento = $this->generarContrato($cabecera,1,$detalles,$condiciones);

                $data       = json_decode($correos);
                $nreg       = count($data);
                $subject    = utf8_decode($asunto);

                $messaje    = '<div style="width:100%;display: flex;flex-direction: column;justify-content: center;align-items: center;
                                    font-family: Futura, Arial, sans-serif;">
                            <div style="width: 45%;border: 1px solid #c2c2c2;background: #0078D4; padding:1rem">
                                <h1 style="text-align: center;">Aprobación</h1>
                            </div>
                            <div style="width: 45%;
                                        border-left: 1px solid #c2c2c2;
                                        border-right: 1px solid #c2c2c2;
                                        border-bottom: 1px solid #c2c2c2;
                                        padding:1rem">
                                <p style="padding:.5rem"><strong style="font-style: italic;">Ing:</strong></p>
                                <p style="padding:.5rem;line-height: 1rem;">  '.$mensaje.'</p>
                                <p style="padding:.5rem;line-height: 1rem;">   Moneda   : '.$cabecera['moneda'].'</p>
                                <p style="padding:.5rem;line-height: 1rem;">   Proveedor: '.$cabecera['entidad'].'</p>
                                <p style="padding:.5rem">Fecha de Emisión : '. date("d/m/Y h:i:s") .'</p>
                            </div>
                        </div>';

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
                
                $mail->setFrom($origen,$nombre_envio);

                for ($i=0; $i < $nreg; $i++) {
                    $mail->addAddress($data[$i]->correo,$data[$i]->nombre);
        
                    $mail->Subject = $subject;
                    $mail->msgHTML(utf8_decode($messaje));

                    if (file_exists( 'public/documentos/ordenes/emitidas/'.$documento)) {
                        $mail->AddAttachment('public/documentos/ordenes/emitidas/'.$documento);
                    }
        
                    if (!$mail->send()) {
                        $mensaje = "Mensaje de correo no enviado";
                        $estadoEnvio = false; 
                    }else {
                        $mensaje = "Mensaje de correo enviado";
                        $estadoEnvio = true; 
                    }
                        
                    $mail->clearAddresses();
                }

                if ($estadoEnvio){
                    $clase = "mensaje_correcto";
                    $this->actualizarCabeceraPedido(59,$cabecera['codigo_pedido'],$cabecera['codigo_orden']);
                    $this->actualizarDetallesPedido(59,$detalles,$cabecera['codigo_orden'],$cabecera['codigo_entidad']);
                    $this->actualizarCabeceraOrden(59,$cabecera['codigo_orden'],$cabecera['fentrega']);
                }

                $salida= array("estado"=>$estadoEnvio,
                                "mensaje"=>$mensaje,
                                "clase"=>$clase );

                return $salida;
            
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            } 
        }
    }
?>