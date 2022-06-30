<?php
    class OrdenModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarOrdenes($user){
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
                                                        UPPER( tb_pedidocab.concepto ) AS concepto,
                                                        UPPER( tb_pedidocab.detalle ) AS detalle,
                                                        UPPER(
                                                        CONCAT_WS( tb_area.ccodarea, tb_area.cdesarea )) AS area,
                                                        UPPER(
                                                        CONCAT_WS( tb_proyectos.ccodproy, tb_proyectos.cdesproy )) AS costos,
                                                        lg_ordencab.nfirmaLog,
                                                        lg_ordencab.nfirmaFin,
                                                        lg_ordencab.nfirmaOpe,
                                                        tb_parametros.cdescripcion AS atencion 
                                                    FROM
                                                        tb_costusu
                                                        INNER JOIN lg_ordencab ON tb_costusu.ncodproy = lg_ordencab.ncodpry
                                                        INNER JOIN tb_pedidocab ON lg_ordencab.id_refpedi = tb_pedidocab.idreg
                                                        INNER JOIN tb_area ON lg_ordencab.ncodarea = tb_area.ncodarea
                                                        INNER JOIN tb_proyectos ON lg_ordencab.ncodpry = tb_proyectos.nidreg
                                                        INNER JOIN tb_parametros ON lg_ordencab.nNivAten = tb_parametros.nidreg 
                                                    WHERE
                                                        tb_costusu.id_cuser = :user 
                                                        AND tb_costusu.nflgactivo = 1");
                $sql->execute(["user"=>$user]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0){
                    while ($rs = $sql->fetch()) {

                        $log = is_null($rs['nfirmaLog']) ? '<i class="far fa-square"></i>' : '<i class="far fa-check-square"></i>';
                        $ope = is_null($rs['nfirmaOpe']) ? '<i class="far fa-square"></i>' : '<i class="far fa-check-square"></i>';
                        $fin = is_null($rs['nfirmaFin']) ? '<i class="far fa-square"></i>' : '<i class="far fa-check-square"></i>';

                        $flog = is_null($rs['nfirmaLog']) ? 0 : 1;
                        $fope = is_null($rs['nfirmaOpe']) ? 0 : 1;
                        $ffin = is_null($rs['nfirmaFin']) ? 0 : 1;


                        $salida .='<tr class="pointer" data-indice="'.$rs['id_regmov'].'" 
                                                        data-estado="'.$rs['nEstadoDoc'].'"
                                                        data-finanzas="'.$ffin.'"
                                                        data-logistica="'.$flog.'"
                                                        data-operaciones="'.$fope.'">
                                    <td class="textoCentro">'.str_pad($rs['cnumero'],4,0,STR_PAD_LEFT).'</td>
                                    <td class="textoCentro">'.date("d/m/Y", strtotime($rs['ffechadoc'])).'</td>
                                    <td class="pl20px">'.$rs['concepto'].'</td>
                                    <td class="pl20px">'.utf8_decode($rs['costos']).'</td>
                                    <td class="pl20px">'.$rs['area'].'</td>
                                    <td class="textoCentro '.strtolower($rs['atencion']).'">'.$rs['atencion'].'</td>
                                    <td class="textoCentro">'.$log.'</td>
                                    <td class="textoCentro">'.$ope.'</td>
                                    <td class="textoCentro">'.$fin.'</td>
                                    </tr>';
                    }
                }

                return $salida;                    
           } catch (PDOException $th) {
               echo "Error: " . $th->getMessage();
               return false;
           }
        }

        public function importarPedidos(){
            try {
                $salida = "";
                $sql = $this->db->connect()->prepare("SELECT
                                                        tb_pedidocab.idcostos,
                                                        tb_pedidocab.idarea,
                                                        tb_pedidocab.idtrans,
                                                        tb_pedidocab.nrodoc,
                                                        UPPER(
                                                        CONCAT_WS( ' ', tb_proyectos.ccodproy, tb_proyectos.cdesproy )) AS costos,
                                                        UPPER(
                                                        CONCAT_WS( ' ', tb_area.ccodarea, tb_area.cdesarea )) AS area,
                                                        cm_producto.id_cprod,
                                                        cm_producto.ccodprod,
                                                        UPPER(CONCAT_WS(' ',cm_producto.cdesprod,tb_pedidodet.observaciones,tb_pedidodet.docEspec)) AS cdesprod,
                                                        tb_pedidodet.idpedido,
                                                        tb_pedidocab.emision,
                                                        UPPER( tb_pedidocab.concepto ) AS concepto,
                                                        tb_pedidocab.detalle,
                                                        tb_pedidodet.iditem,
                                                        cm_entidad.crazonsoc,
                                                        cm_entidad.id_centi,
                                                        tb_pedidodet.idproforma,
                                                        FORMAT( tb_pedidodet.cant_aprob, 2 ) AS cantidad,
                                                        FORMAT( tb_pedidodet.precio, 2 ) AS precio,
                                                        FORMAT((
                                                                tb_pedidodet.precio * tb_pedidodet.cant_aprob 
                                                                ) + ( tb_pedidodet.precio * tb_pedidodet.cant_aprob ) *
                                                        IF
                                                            ( lg_proformacab.nigv > 0, 0.18, 0 ),
                                                            2 
                                                        ) AS total,
                                                        FORMAT(( tb_pedidodet.precio * tb_pedidodet.cant_aprob ) * IF ( lg_proformacab.nigv > 0, 0.18, 0 ), 2 ) AS igv,
                                                        tb_unimed.cabrevia AS desunidad,
                                                        monedas.cdescripcion,
                                                        monedas.cabrevia AS abrmoneda,
                                                        tb_pedidodet.nroparte,
                                                        monedas.nidreg AS moneda,
                                                        pagos.cdescripcion AS pago,
                                                        pagos.ccod,
                                                        lg_proformacab.cnumero,
                                                        tb_pedidodet.total AS total_numero
                                                    FROM
                                                        tb_pedidodet
                                                        INNER JOIN tb_pedidocab ON tb_pedidodet.idpedido = tb_pedidocab.idreg
                                                        INNER JOIN tb_costusu ON tb_pedidocab.idcostos = tb_costusu.ncodproy
                                                        INNER JOIN tb_proyectos ON tb_pedidocab.idcostos = tb_proyectos.nidreg
                                                        INNER JOIN tb_area ON tb_pedidocab.idarea = tb_area.ncodarea
                                                        INNER JOIN cm_producto ON tb_pedidodet.idprod = cm_producto.id_cprod
                                                        INNER JOIN cm_entidad ON tb_pedidodet.entidad = cm_entidad.cnumdoc
                                                        INNER JOIN lg_proformacab ON tb_pedidodet.idproforma = lg_proformacab.nprof
                                                        INNER JOIN tb_unimed ON tb_pedidodet.unid = tb_unimed.ncodmed
                                                        INNER JOIN tb_parametros AS monedas ON lg_proformacab.ncodmon = monedas.nidreg
                                                        INNER JOIN tb_parametros AS pagos ON lg_proformacab.ccondpago = pagos.nidreg 
                                                    WHERE
                                                        tb_costusu.id_cuser =:user 
                                                        AND tb_costusu.nflgactivo = 1 
                                                        AND tb_pedidodet.estadoItem = 58");
                $sql->execute(["user"=>$_SESSION['iduser']]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()) {
                        $salida .='<tr class="pointer" data-pedido="'.$rs['idpedido'].'" 
                                                       data-iditem="'.$rs['iditem'].'" 
                                                       data-entidad="'.$rs['id_centi'].'"
                                                       data-proforma="'.$rs['idproforma'].'"
                                                       data-unidad="'.$rs['desunidad'].'"
                                                       data-cantidad="'.$rs['cantidad'].'"
                                                       data-precio="'.$rs['precio'].'"
                                                       data-igv="'.$rs['igv'].'"
                                                       data-total="'.$rs['total_numero'].'"
                                                       data-nroparte="'.$rs['nroparte'].'"
                                                       data-moneda="'.$rs['moneda'].'"
                                                       data-abrmoneda="'.$rs['abrmoneda'].'"
                                                       data-desmoneda="'.$rs['cdescripcion'].'"
                                                       data-pago="'.$rs['pago'].'"
                                                       data-codprod="'.$rs['id_cprod'].'"
                                                       data-nroprofoma="'.$rs['cnumero'].'">
                                        <td class="textoCentro">'.str_pad($rs['nrodoc'],6,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.date("d/m/Y", strtotime($rs['emision'])).'</td>
                                        <td class="pl5px">'.$rs['concepto'].'</td>
                                        <td class="pl5px">'.$rs['area'].'</td>
                                        <td class="pl5px">'.$rs['costos'].'</td>
                                        <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                        <td class="pl5px">'.$rs['cdesprod'].'</td>
                                        <td class="pl5px">'.$rs['crazonsoc'].'</td>
                                    </tr>';
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function verDatosCabecera($pedido,$proforma,$entidad){
            $datosPedido = $this->datosPedido($pedido);
            $sql = "SELECT COUNT(lg_ordencab.id_regmov) AS numero FROM lg_ordencab WHERE lg_ordencab.ncodcos =:cod";
            
            $numero = $this->generarNumero($datosPedido[0]["idcostos"],$sql);
            $entidad = $this->datosEntidad($entidad);
            $proforma = $this->datosProforma($proforma);

            $salida = array("pedido"=>$datosPedido,
                            "orden"=>$numero,
                            "entidad"=>$entidad,
                            "proforma"=>$proforma);

            return $salida;
        }

        public function generarDocumento($cabecera,$condicion,$detalles){
            require_once("public/formatos/ordenes.php");

            $bancos = $this->bancosProveedor($cabecera['codigo_entidad']);

            $sql = "SELECT COUNT(lg_ordencab.id_regmov) AS numero FROM lg_ordencab WHERE lg_ordencab.ncodcos =:cod";

            if ($condicion == 0) {
                $numero = $this->generarNumero($cabecera['codigo_costos'],$sql);
                $noc = $numero['numero'];
            }else{
                $noc = $cabecera['numero'];
            }
            
            if ($cabecera['codigo_tipo'] == "37") {
                $titulo = "ORDEN DE COMPRA" ;
                $prefix = "OC";
                $tipo = "B";
            }else{
                $titulo = "ORDEN DE SERVICIO";
                $prefix = "OS";
                $tipo = "S";
            }

            $anio = explode("-",$cabecera['emision']);

            $orden = $cabecera['sw'] == 0 ? $noc : $cabecera['numero'];
            $titulo = $titulo . " " . $orden;
            
            $file = $prefix.$noc."_".$cabecera['codigo_costos'].".pdf";
            $entrega = $this->calcularDias($cabecera['fentrega']);

            $pdf = new PDF($titulo,$condicion,$cabecera['emision'],$cabecera['moneda'],$entrega,
                            $cabecera['lentrega'],$cabecera['proforma'],$cabecera['fentrega'],$cabecera['cpago'],$cabecera['total'],
                            $cabecera['costos'],$cabecera['concepto'],$_SESSION['nombres'],$cabecera['entidad'],$cabecera['ruc_entidad'],
                            $cabecera['direccion_entidad'],$cabecera['telefono_entidad'],$cabecera['correo_entidad'],$cabecera['retencion'],
                            $cabecera['atencion'],$cabecera['telefono_contacto'],$cabecera['correo_contacto']);

            $pdf->AddPage();
            $pdf->AliasNbPages();
            $pdf->SetWidths(array(10,15,15,10,95,17,13,15));
            $pdf->SetFont('Arial','',5);
            $lc = 0;
            $rc = 0;

            //$pdf->Ln(3);

            $datos = json_decode($detalles);
            $nreg = count($datos);

            for ($i=0; $i < $nreg; $i++) { 
                $pdf->SetAligns(array("C","C","R","C","L","C","R","R"));
                $pdf->Row(array($datos[$i]->item,
                                $datos[$i]->codigo,
                                $datos[$i]->cantidad,
                                $datos[$i]->unidad,
                                utf8_decode($datos[$i]->descripcion),
                                $datos[$i]->pedido,
                                $datos[$i]->precio,
                                $datos[$i]->total));
                $lc++;
                $rc++;
                
                if ($lc == 52) {
                    $pdf->AddPage();
                    $lc = 0;
                }
            }

            $pdf->Ln(3);

            $pdf->SetFillColor(229, 229, 229);
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(20,6,"TOTAL :","LTB",0,"C",true);
            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(140,6,$this->convertir($cabecera['total']),"TBR",0,"L",true); 
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(30,6,$cabecera['total'],"1",1,"R",true);

            $pdf->Ln(1);
            $pdf->SetFont('Arial',"","7");
            $pdf->Cell(40,6,"Pedidos Asociados",1,0,"C",true);
            $pdf->Cell(5,6,"",0,0);
            $pdf->Cell(80,6,utf8_decode("Información Bancaria del Proveedor"),1,0,"C",true);
            $pdf->Cell(10,6,"",0,0);
            $pdf->Cell(40,6,"Valor Venta",0,0);
            $pdf->Cell(20,6,$cabecera['total'],0,1);
                                        
            $pdf->Cell(10,4,utf8_decode("Año"),1,0);
                    
            $pdf->Cell(10,4,"Tipo",1,0);
            $pdf->Cell(10,4,"Pedido",1,0);
            $pdf->Cell(10,4,"Mantto",1,0);
            $pdf->Cell(5,6,"",0,0);
            $pdf->Cell(35,4,"Detalle del Banco",1,0);
            $pdf->Cell(15,4,"Moneda",1,0);
            $pdf->Cell(30,4,"Nro. Cuenta Bancaria",1,0);
            
            $pdf->Cell(10,4,"",0,0);
            $pdf->SetFont('Arial',"B","8");
            $pdf->Cell(20,4,"TOTAL",1,0,"L",true);
            $pdf->Cell(15,4,$cabecera['moneda'],1,0,"C",true);
            $pdf->Cell(20,4,$cabecera['total'],1,1,"R",true);

            $pdf->SetFont('Arial',"","7");
            $pdf->Cell(10,4,$anio[0],1,0);
            $pdf->Cell(10,4,$tipo,1,0);
            $pdf->Cell(10,4,str_pad($cabecera['codigo_pedido'],6,0,STR_PAD_LEFT),1,0);
            $pdf->Cell(10,4,"",1,0);
            $pdf->Cell(5,6,"",0,0);
            
            $nreg = count($bancos);

            for ($i=0;$i<$nreg;$i++){
                $pdf->Cell(35,4,$bancos[$i]['banco'],1,0);
                $pdf->Cell(15,4,$bancos[$i]['moneda'],1,0);
                $pdf->Cell(30,4,$bancos[$i]['cuenta'],1,1);
                $pdf->Cell(45,4,"",0,0);
            }

            if ($condicion == 0){
                $filename = "public/documentos/ordenes/vistaprevia/".$file;
            }else if ($condicion == 1){
                $filename = "public/documentos/ordenes/emitidas/".$file;
            }else if ($condicion == 2){
                $filename = "public/documentos/ordenes/aprobadas/".$file;
            }

            $pdf->Output($filename,'F');

            return $file;
        }

        public function insertarOrden($cabecera,$detalles,$comentarios){
            try {
                $salida = false;
                $respuesta = false;
                $mensaje = "Error en el registro";
                $clase = "mensaje_error";

                $sql = "SELECT COUNT(lg_ordencab.id_regmov) AS numero FROM lg_ordencab WHERE lg_ordencab.ncodcos =:cod";
                $api = file_get_contents('https://api.apis.net.pe/v1/tipo-cambio-sunat');
                $cambio = json_decode($api);
                $entrega = $this->calcularDias($cabecera['fentrega']);
            
                $orden = $this->generarNumero($cabecera['codigo_costos'],$sql);
                $periodo = explode('-',$cabecera['emision']);
                

                $sql = $this->db->connect()->prepare("INSERT INTO lg_ordencab SET id_refpedi=:pedi,cper=:anio,cmes=:mes,ntipmov=:tipo,cnumero=:orden,
                                                                                ffechadoc=:fecha,ffechaent=:entrega,id_centi=:entidad,ncodmon=:moneda,ntcambio=:tcambio,
                                                                                nigv=:igv,ntotal=:total,ncodpry=:proyecto,ncodcos=:ccostos,ncodarea=:area,
                                                                                ctiptransp=:transporte,id_cuser=:elabora,ncodpago=:pago,nplazo=:pentrega,cnumcot=:cotizacion,
                                                                                cdocPDF=:adjunto,nEstadoDoc=:est,ncodalm=:almacen,nflgactivo=:flag,nNivAten=:atencion,
                                                                                cverificacion=:verif");

                $sql ->execute(["pedi"=>$cabecera['codigo_pedido'],
                                "anio"=>$periodo[0],
                                "mes"=>$periodo[1],
                                "tipo"=>$cabecera['codigo_tipo'],
                                "orden"=>$orden['numero'],
                                "fecha"=>$cabecera['emision'],
                                "entrega"=>$cabecera['fentrega'],
                                "entidad"=>$cabecera['codigo_entidad'],
                                "moneda"=>$cabecera['codigo_moneda'],
                                "tcambio"=>$cambio->compra,
                                "igv"=>0,
                                "total"=>$cabecera['total'],
                                "proyecto"=>$cabecera['codigo_costos'],
                                "ccostos"=>$cabecera['codigo_costos'],
                                "area"=>$cabecera['codigo_area'],
                                "transporte"=>$cabecera['codigo_transporte'],
                                "elabora"=>$_SESSION['iduser'],
                                "pago"=>$cabecera['codigo_pago'],
                                "pentrega"=>$entrega,
                                "cotizacion"=>$cabecera['proforma'],
                                "adjunto"=>$cabecera['vista_previa'],
                                "est"=>49,
                                "almacen"=>$cabecera['codigo_almacen'],
                                "flag"=>1,
                                "atencion"=>$cabecera['nivel_atencion'],
                                "verif"=>$cabecera['codigo_verificacion']]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0){
                    $this->grabarDetalles($cabecera['codigo_verificacion'],$detalles,$cabecera['codigo_costos']);
                    $this->grabarComentarios($cabecera['codigo_verificacion'],$comentarios);
                    $this->actualizarDetallesPedido(84,$detalles,$orden);
                    $respuesta = true;
                    $mensaje = "Orden Grabada";
                    $clase = "mensaje_correcto";
                }

                $salida = array("respuesta"=>$respuesta,
                                "mensaje"=>$mensaje,
                                "clase"=>$clase);

                
                return $salida;

                
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }    
        }

        private function grabarDetalles($codigo,$detalles,$costos){
            try {
                $indice = $this->obtenerIndice($codigo,"SELECT id_regmov AS numero FROM lg_ordencab WHERE lg_ordencab.cverificacion =:id");
                
                $datos = json_decode($detalles);
                
                $nreg = count($datos);

                for ($i=0; $i < $nreg; $i++) { 
                    if(!$datos[$i]->grabado) {
                        $sql = $this->db->connect()->prepare("INSERT INTO lg_ordendet SET id_regmov=:id,niddeta=:nidp,id_cprod=:cprod,ncanti=:cant,
                                                                                    nunitario=:unit,nigv=:igv,ntotal=:total,
                                                                                    nestado=:est,cverifica=:verif,nidpedi=:pedido,
                                                                                    nmonref=:moneda,ncodcos=:costos");
                        $sql->execute(["id"=>$indice,
                                        "nidp"=>$datos[$i]->itped,
                                        "pedido"=>$datos[$i]->pedido,
                                        "cprod"=>$datos[$i]->codprod,
                                        "cant"=>$datos[$i]->cantidad,
                                        "unit"=>$datos[$i]->precio,
                                        "igv"=>$datos[$i]->igv,
                                        "total"=>$datos[$i]->total,
                                        "est"=>1,
                                        "verif"=>$codigo,
                                        "moneda"=>$datos[$i]->moneda,
                                        "costos"=>$costos]);
                    }//aca poner para la modificacion de ordenes
                    
                }
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }


        public function modificarOrden($cabecera,$detalles,$comentarios){
            try {
                $entrega = $this->calcularDias($cabecera['fentrega']);

                $sql = $this->db->connect()->prepare("UPDATE lg_ordencab 
                                                        SET  ffechaent=:entrega,ntotal=:total,ctiptransp=:transp,
                                                             nplazo=:plazo,ncodalm=:alm
                                                        WHERE id_regmov = :id");
                $sql->execute(['entrega'=>$cabecera['fentrega'],
                                "total"=>$cabecera['total'],
                                "transp"=>$cabecera['codigo_transporte'],
                                "plazo"=>$entrega,
                                "alm"=>$cabecera['codigo_almacen'],
                                "id"=>$cabecera['codigo_orden']]);
                
                $this->grabarDetalles($cabecera['codigo_verificacion'],$detalles,$cabecera['codigo_costos']);
                $this->grabarComentarios($cabecera['codigo_verificacion'],$comentarios);

                $salida = array("respuesta"=>true,
                                "mensaje"=>"Registro modificado",
                                "clase"=>"mensaje_correcto");

                
                return $salida;

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            } 
        }

        public function enviarCorreo($cabecera,$detalles,$correos,$asunto,$mensaje){
            try {
                require_once("public/PHPMailer/PHPMailerAutoload.php");

                $documento = $this->generarDocumento($cabecera,1,$detalles);

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
                    $this->actualizarDetallesPedido(59,$detalles,$cabecera['codigo_orden']);
                    $this->actualizarCabeceraOrden(59,$cabecera['codigo_orden']);
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

        public function enviarCorreoProveedor($cabecera,$detalles){
            try {
                require_once("public/PHPMailer/PHPMailerAutoload.php");

                $documento = $this->generarDocumento($cabecera,2,$detalles);

                $subject    = utf8_decode("Atención de Orden de Compra");
                $messaje    = utf8_decode("Su atención en la orden de compra adjunta");

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
                $mail->addAddress($cabecera['correo_entidad'],$cabecera['entidad']);

                $mail->Subject = $subject;
                    $mail->msgHTML(utf8_decode($messaje));

                    if (file_exists( 'public/documentos/ordenes/aprobadas/'.$documento)) {
                        $mail->AddAttachment('public/documentos/ordenes/aprobadas/'.$documento);
                    }
        
                    if (!$mail->send()) {
                        return array("mensaje"=>"Hubo un error, en el envio",
                                    "clase"=>"mensaje_error");
                    }else {
                        $this->actualizarCabeceraPedido(59,$cabecera['codigo_pedido'],$cabecera['codigo_orden']);
                        $this->actualizarDetallesPedido(59,$detalles,$cabecera['codigo_orden']);
                        $this->actualizarCabeceraOrden(59,$cabecera['codigo_orden']);
                        return array("mensaje"=>"Correo enviado",
                                    "clase"=>"mensaje_correcto");
                    }
                        
                    $mail->clearAddresses();


            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            } 
        }

        private function calcularDias($fechaEntrega){
            $date1 = new DateTime(Date('Y-m-d'));
            $date2 = new DateTime($fechaEntrega);
            $diff = $date1->diff($date2);
            // will output 2 days
            return $diff->days . ' dias ';
        }

        private function datosPedido($pedido){
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                        ibis.tb_pedidocab.idreg,
                                                        ibis.tb_pedidocab.idcostos,
                                                        ibis.tb_pedidocab.idarea,
                                                        ibis.tb_pedidocab.idtrans,
                                                        ibis.tb_pedidocab.idsolicita,
                                                        ibis.tb_pedidocab.idtipomov,
                                                        ibis.tb_pedidocab.emision,
                                                        ibis.tb_pedidocab.vence,
                                                        ibis.tb_pedidocab.estadodoc,
                                                        ibis.tb_pedidocab.nrodoc,
                                                        ibis.tb_pedidocab.usuario,
                                                        UPPER(ibis.tb_pedidocab.concepto) AS concepto,
                                                        UPPER(ibis.tb_pedidocab.detalle) AS detalle,
                                                        ibis.tb_pedidocab.nivelAten,
                                                        ibis.tb_pedidocab.docPdfAprob,
                                                        ibis.tb_pedidocab.verificacion,
                                                        UPPER(
                                                        CONCAT( ibis.tb_proyectos.ccodproy, ' ', ibis.tb_proyectos.cdesproy )) AS proyecto,
                                                        UPPER(
                                                        CONCAT( ibis.tb_area.ccodarea, ' ', ibis.tb_area.cdesarea )) AS area,
                                                        UPPER(
                                                        CONCAT( ibis.tb_parametros.nidreg, ' ', ibis.tb_parametros.cdescripcion )) AS transporte,
                                                        estados.cdescripcion AS estado,
                                                        estados.cabrevia,
                                                        UPPER(
                                                        CONCAT_WS( ' ', tipos.nidreg, tipos.cdescripcion )) AS tipo,
                                                        ibis.tb_proyectos.veralm 
                                                    FROM
                                                        ibis.tb_pedidocab
                                                        INNER JOIN ibis.tb_proyectos ON ibis.tb_pedidocab.idcostos = ibis.tb_proyectos.nidreg
                                                        INNER JOIN ibis.tb_area ON ibis.tb_pedidocab.idarea = ibis.tb_area.ncodarea
                                                        INNER JOIN ibis.tb_parametros ON ibis.tb_pedidocab.idtrans = ibis.tb_parametros.nidreg
                                                        INNER JOIN ibis.tb_parametros AS transportes ON ibis.tb_pedidocab.idtrans = transportes.nidreg
                                                        INNER JOIN ibis.tb_parametros AS estados ON ibis.tb_pedidocab.estadodoc = estados.nidreg
                                                        INNER JOIN ibis.tb_parametros AS tipos ON ibis.tb_pedidocab.idtipomov = tipos.nidreg 
                                                    WHERE
                                                        tb_pedidocab.idreg = :pedido ");
                $sql->execute(["pedido"=>$pedido]);
                
                $rowCount = $sql->rowCount();
                
                if ($rowCount > 0) {
                    $docData = array();
                    while($row=$sql->fetch(PDO::FETCH_ASSOC)){
                        $docData[] = $row;
                    }
                }

                return $docData;

            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }

        private function datosEntidad($entidad){
            try {
                $sql=$this->db->connect()->prepare("SELECT
                                                        cm_entidad.cnumdoc,
                                                        cm_entidad.crazonsoc,
                                                        UPPER( cm_entidadcon.cnombres ) AS contacto,
                                                        cm_entidadcon.cemail AS correo_contacto,
                                                        cm_entidadcon.ctelefono1 AS telefono_contacto,
                                                        cm_entidad.id_centi,
                                                        cm_entidad.cemail AS correo_entidad,
                                                        cm_entidad.cviadireccion,
                                                        cm_entidad.ctelefono,
                                                        cm_entidad.nagenret  
                                                    FROM
                                                        cm_entidadcon
                                                        INNER JOIN cm_entidad ON cm_entidadcon.id_centi = cm_entidad.id_centi 
                                                    WHERE
                                                        cm_entidad.id_centi =:entidad 
                                                        LIMIT 1");
                $sql->execute(["entidad"=>$entidad]);

                $rowCount = $sql->rowCount();
                
                if ($rowCount > 0) {
                    $docData = array();
                    while($row=$sql->fetch(PDO::FETCH_ASSOC)){
                        $docData[] = $row;
                    }
                }

                return $docData;
            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }

        private function bancosProveedor($entidad){
            try {
                $bancos = [];
                $item = array();

                $sql = $this->db->connect()->prepare("SELECT
                                                    bancos.cdescripcion AS banco,
                                                    cm_entidadbco.cnrocta AS cuenta,
                                                    monedas.cdescripcion AS moneda
                                                FROM
                                                    cm_entidadbco
                                                    INNER JOIN tb_parametros AS bancos ON cm_entidadbco.ncodbco = bancos.nidreg
                                                    INNER JOIN tb_parametros AS monedas ON cm_entidadbco.cmoneda = monedas.nidreg 
                                                WHERE
                                                    cm_entidadbco.nflgactivo = 7 
                                                    AND cm_entidadbco.id_centi = :entidad");
                $sql->execute(["entidad"=>$entidad]);
                $rowCount = $sql->rowCount();

                if($rowCount > 0){
                    while ($rs = $sql->fetch()) {
                        $item['banco'] = $rs['banco'];
                        $item['moneda'] = $rs['moneda'];
                        $item['cuenta'] = $rs['cuenta'];
                        
                        array_push($bancos,$item);
                    }
                }

                return $bancos;

            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }

        private function datosProforma($proforma){
            try {
                $sql=$this->db->connect()->prepare("SELECT
                                                    lg_proformacab.id_regmov,
                                                    lg_proformacab.id_centi,
                                                    lg_proformacab.ffechadoc,
                                                    lg_proformacab.ffechaplazo,
                                                    lg_proformacab.cnumero,
                                                    lg_proformacab.ccondpago,
                                                    lg_proformacab.ncodmon,
                                                    lg_proformacab.nafecIgv,
                                                    lg_proformacab.nigv,
                                                    lg_proformacab.ntotal,
                                                    tb_parametros.cdescripcion 
                                                FROM
                                                    lg_proformacab
                                                    INNER JOIN tb_parametros ON lg_proformacab.ccondpago = tb_parametros.nidreg 
                                                WHERE
                                                    lg_proformacab.nprof =:proforma");
                $sql->execute(["proforma"=>$proforma]);

                $rowCount = $sql->rowCount();
                
                if ($rowCount > 0) {
                    $docData = array();
                    while($row=$sql->fetch(PDO::FETCH_ASSOC)){
                        $docData[] = $row;
                    }
                }

                return $docData;
            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
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

        private function actualizarDetallesPedido($estado,$detalles,$orden){
            try {
                $datos = json_decode($detalles);
                $nreg = count($datos);
                for ($i=0; $i <$nreg ; $i++) { 
                    $sql = $this->db->connect()->prepare("UPDATE tb_pedidodet SET estadoItem=:est,idorden=:orden WHERE iditem=:item");
                    $sql->execute(["item"=>$datos[$i]->itped,
                                    "est"=>$estado,
                                    "orden"=>$orden['numero']]);
                }
                
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function actualizarCabeceraOrden($estado,$orden){
            try {
                $sql = $this->db->connect()->prepare("UPDATE lg_ordencab SET nEstadoDoc=:est WHERE id_regmov=:id");
                $sql->execute(["est"=>$estado,
                                "id"=>$orden]);
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }
    }
?>