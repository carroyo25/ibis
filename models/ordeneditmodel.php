<?php
    class OrdenEditModel extends Model{

        public function __construct(){
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
                                                        UPPER( lg_ordencab.cObservacion ) AS concepto,
                                                        UPPER( tb_pedidocab.detalle ) AS detalle,
                                                        UPPER(
                                                        CONCAT_WS( tb_area.ccodarea, tb_area.cdesarea )) AS area,
                                                        UPPER(
                                                        CONCAT_WS( tb_proyectos.ccodproy, tb_proyectos.cdesproy )) AS costos,
                                                        tb_proyectos.ccodproy,
                                                        lg_ordencab.nfirmaLog,
                                                        lg_ordencab.nfirmaFin,
                                                        lg_ordencab.nfirmaOpe,
                                                        tb_parametros.cdescripcion AS atencion,
                                                        UPPER(cm_entidad.crazonsoc) AS proveedor 
                                                    FROM
                                                        tb_costusu
                                                        INNER JOIN lg_ordencab ON tb_costusu.ncodproy = lg_ordencab.ncodpry
                                                        INNER JOIN tb_pedidocab ON lg_ordencab.id_refpedi = tb_pedidocab.idreg
                                                        INNER JOIN tb_area ON lg_ordencab.ncodarea = tb_area.ncodarea
                                                        INNER JOIN tb_proyectos ON lg_ordencab.ncodpry = tb_proyectos.nidreg
                                                        INNER JOIN tb_parametros ON lg_ordencab.nNivAten = tb_parametros.nidreg
                                                        INNER JOIN cm_entidad ON lg_ordencab.id_centi = cm_entidad.id_centi 
                                                    WHERE
                                                        tb_costusu.id_cuser = :user 
                                                        AND tb_costusu.nflgactivo = 1
            ORDER BY id_regmov DESC");
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

                        $salida .='<tr class="pointer '.$resaltado.'" data-indice="'.$rs['id_regmov'].'" 
                                                        data-estado="'.$rs['nEstadoDoc'].'"
                                                        data-finanzas="'.$ffin.'"
                                                        data-logistica="'.$flog.'"
                                                        data-operaciones="'.$fope.'">
                                    <td class="textoCentro">'.str_pad($rs['cnumero'],4,0,STR_PAD_LEFT).'</td>
                                    <td class="textoCentro">'.date("d/m/Y", strtotime($rs['ffechadoc'])).'</td>
                                    <td class="pl20px">'.$rs['concepto'].'</td>
                                    <td class="pl20px">'.utf8_decode($rs['ccodproy']).'</td>
                                    <td class="pl20px">'.$rs['area'].'</td>
                                    <td class="pl20px">'.$rs['proveedor'].'</td>
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


        public function verDatosCabecera($pedido){
            $datosPedido = $this->datosPedido($pedido);
            $sql = "SELECT COUNT(lg_ordencab.id_regmov) AS numero FROM lg_ordencab WHERE lg_ordencab.ncodcos =:cod";
            $api = file_get_contents('https://api.apis.net.pe/v1/tipo-cambio-sunat');
            $cambio = json_decode($api);

            $numero = $this->generarNumero($datosPedido[0]["idcostos"],$sql);

            $salida = array("pedido"=>$datosPedido,
                            "orden"=>$numero,
                            "cambio"=>$cambio->compra);

            return $salida;
        }

        /*public function generarDocumento($cabecera,$condicion,$detalles){
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
                $titulo = "ORDEN DE COMPRA R1. -" ;
                $prefix = "OC";
                $tipo = "B";
            }else{
                $titulo = "ORDEN DE SERVICIO R1. -";
                $prefix = "OS";
                $tipo = "S";
            }

            $anio = explode("-",$cabecera['emision']);

            $orden = $cabecera['sw'] == 0 ? $noc : $cabecera['numero'];
            $titulo = $titulo . " " .$anio[0]. " - " . $orden;
            
            $file = $prefix.$noc."_".$cabecera['codigo_costos'].".pdf";
            $entrega = $this->calcularDias($cabecera['fentrega']);

            $pdf = new PDF($titulo,$condicion,$cabecera['emision'],$cabecera['moneda'],$entrega,
                            $cabecera['lentrega'],$cabecera['ncotiz'],$cabecera['fentrega'],$cabecera['cpago'],$cabecera['total'],
                            $cabecera['costos'],$cabecera['concepto'],$_SESSION['nombres'],$cabecera['entidad'],$cabecera['ruc_entidad'],
                            $cabecera['direccion_entidad'],$cabecera['telefono_entidad'],$cabecera['correo_entidad'],$cabecera['retencion'],
                            $cabecera['atencion'],$cabecera['telefono_contacto'],$cabecera['correo_contacto'],
                            $cabecera['direccion_almacen']);

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

            if ($cabecera['radioIgv'] ==  0) {
                $pdf->Cell(48,6,"Valor Venta",0,0);
                $pdf->Cell(20,6,$cabecera['total_numero'],0,1);
            }else {
                
                $igv = round((floatval($cabecera['total_numero'])*0.18),2);
                $total_sin_igv = round($cabecera['total_numero'] - $igv,2);
                $pdf->Cell(45,6,"Valor Venta",0,0);
                $pdf->Cell(20,6,$total_sin_igv,0,1);
            }

            $pdf->Cell(10,6,utf8_decode("Año"),1,0);   
            $pdf->Cell(10,6,"Tipo",1,0);
            $pdf->Cell(10,6,"Pedido",1,0);
            $pdf->Cell(10,6,"Mantto",1,0);
            $pdf->Cell(5,6,"",0,0);
            $pdf->Cell(35,4,"Detalle del Banco",1,0);
            $pdf->Cell(15,4,"Moneda",1,0);
            $pdf->Cell(30,4,"Nro. Cuenta Bancaria",1,0);

            if($cabecera['radioIgv'] ==  0) {
                $pdf->SetX(146);
                $pdf->Cell(8,6,"",0,0);
                $pdf->Cell(20,6,"",0,0);
                $pdf->SetX(185);
                $pdf->Cell(20,6,"",0,1); 
            }else{
                $igv = round((floatval($cabecera['total_numero'])*0.18),2);
                $total_sin_igv = round($cabecera['total_numero'] - $igv,2);
                $pdf->SetX(146);
                $pdf->Cell(8,6,"IGV",0,0);
                $pdf->Cell(37,6,"(18%)",0,0);
                $pdf->Cell(25,6,$igv,0,1);
            }
            

            $pdf->SetFont('Arial',"","7");
            $pdf->Cell(10,6,$anio[0],1,0);
            $pdf->Cell(10,6,$tipo,1,0);
            $pdf->Cell(10,6,str_pad($cabecera['codigo_pedido'],6,0,STR_PAD_LEFT),1,0);
            $pdf->Cell(10,6,"",1,0);
            $pdf->Cell(5,6,"",0,0);

            $pdf->Cell(90,4,"",0,0);
            $pdf->SetFont('Arial',"B","8");
            $pdf->Cell(20,4,"TOTAL",1,0,"L",true);
            $pdf->Cell(15,4,$cabecera['moneda'],1,0,"C",true);
            $pdf->Cell(20,4,$cabecera['total'],1,1,"R",true);
            
            $nreg = count($bancos);

            
            $x = $pdf->GetX();
            $y = $pdf->GetY();
            

            $pdf->SetXY(55,$y-6);
            $pdf->SetFont('Arial',"B","6");

            for ($i=0;$i<$nreg;$i++){
                $pdf->Cell(35,4,$bancos[$i]['banco'],1,0);
                $pdf->Cell(15,4,$bancos[$i]['moneda'],1,0);
                $pdf->Cell(30,4,$bancos[$i]['cuenta'],1,1);
                $pdf->Cell(45,4,"",0,0);
            }
            $pdf->SetFont('Arial',"B","8");

            if ($condicion == 0){
                $filename = "public/documentos/ordenes/vistaprevia/".$file;
            }else if ($condicion == 1){
                $filename = "public/documentos/ordenes/emitidas/".$file;
            }else if ($condicion == 2){
                $filename = "public/documentos/ordenes/aprobadas/".$file;
            }

            $pdf->Output($filename,'F');

            return $file;
        }*/

        public function modificarOrden($cabecera,$detalles){
            try {
                $cab = json_decode($cabecera);

                $entrega = $this->calcularDias($cab->fentrega);

                $sql = $this->db->connect()->prepare("UPDATE lg_ordencab 
                                                        SET ffechaent=:entrega,
                                                            ntotal=:total,
                                                            ctiptransp=:transp,
                                                            nplazo=:plazo,
                                                            ncodalm=:alm,
                                                            ncodmon=:moneda,
                                                            ntcambio=:tcambio,
                                                            ncodpago=:pago,
                                                            userModifica=:modifica,
                                                            nigv=:igv
                                                        WHERE id_regmov = :id");
                $sql->execute(['entrega'=>$cab->fentrega,
                                "total"=>$cab->total_numero,
                                "transp"=>$cab->codigo_transporte,
                                "plazo"=>$entrega,
                                "alm"=>$cab->codigo_almacen,
                                "moneda"=>$cab->codigo_moneda,
                                "tcambio"=>$cab->tcambio,
                                "pago"=>$cab->codigo_pago,
                                "modifica"=>$_SESSION['iduser'],
                                "igv"=>$cab->radioIgv,
                                "id"=>$cab->codigo_orden]);
                
                $this->actualizarDetallesOrden($detalles);
                $this->actualizarDetallesPedido($detalles);

                $salida = array("respuesta"=>true,
                                "mensaje"=>"Registro modificado",
                                "clase"=>"mensaje_correcto");

                
                return $salida;

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            } 
        }

        private function grabarDetalles($codigo,$detalles,$costos,$idx){
            try {
                $datos = json_decode($detalles);
                
                $nreg = count($datos);

                for ($i=0; $i < $nreg; $i++) { 
                    $sql = $this->db->connect()->prepare("UPDATE lg_ordendet SET ncanti=:cant,nunitario=:unit,nigv=:igv,ntotal=:total,
                                                                                nmonref=:moneda,nSaldo=:saldo
                                                                                WHERE nitemord = :id");
                    $sql->execute(["id"=>$codigo,
                                    "cant"=>$datos[$i]->cantidad,
                                    "unit"=>$datos[$i]->precio,
                                    "igv"=>$datos[$i]->igv,
                                    "total"=>$datos[$i]->total,
                                    "moneda"=>$datos[$i]->moneda,
                                    "saldo"=>$datos[$i]->cantidad]);
                    
                }
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function actualizarDetallesOrden($detalles){
            $datos = json_decode($detalles);
            $nreg = count($datos);

            for ($i = 0; $i < $nreg; $i++) {
                try {
                    $sql = $this->db->connect()->prepare("UPDATE lg_ordendet SET ncanti = :cantidad, 
                                                                        nunitario=:precio,
                                                                        nsaldo = :saldo,
                                                                        ntotal = :total,
                                                                        cobserva =:observaciones 
                                                WHERE lg_ordendet.nitemord = :id");
                    $sql->execute([ "cantidad"=>$datos[$i]->cantidad,
                                    "precio"=>$datos[$i]->precio,
                                    "saldo" =>$datos[$i]->saldo,
                                    "total"=>$datos[$i]->total,
                                    "id"=>$datos[$i]->itemorden,
                                    "observaciones"=>$datos[$i]->detalles]);
                } catch (PDOException $th) {
                    echo "Error: ".$th->getMessage();
                    return false;
                }
                
            }
        }

        private function actualizarDetallesPedido($detalles){
            try {
                $datos = json_decode($detalles);
                $nreg = count($datos);

                for ($i=0; $i <$nreg ; $i++) { 
                    if( $datos[$i]->saldo == 0 ) {
                        $estado = 84;    
                    }else{
                        $estado = 54;
                    }

                    $sql = $this->db->connect()->prepare("UPDATE tb_pedidodet SET 
                                                        estadoItem=:est, 
                                                        cant_orden=:pendiente WHERE iditem=:item");
                    $sql->execute(["item"=>$datos[$i]->itped,
                                    "est"=>$estado,
                                    "pendiente"=>$datos[$i]->saldo]); 
                }
                
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
                    $this->actualizarDetallesPedido(59,$detalles,$cabecera['codigo_orden'],$cabecera['codigo_entidad']);
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

        public function eliminarItem($itemOrden,$itemPedido,$itemCantidad){
            try {
               $this->marcarDetalleOrden($itemOrden);
               $this->actualizarDetallePedido($itemPedido);
               
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function marcarDetalleOrden($itemOrden) {
            $sql = $this->db->connect()->prepare("UPDATE lg_ordendet SET nflgactivo = 2 WHERE nitemord = :item");
            $sql->execute(["item"=>$itemOrden]);
        }

        private function actualizarDetallePedido($itemPedido) {
            $sql = $this->db->connect()->prepare("UPDATE tb_pedidodet SET nflgactivo = 0,idorden = null,estadoItem = 54 WHERE iditem = :item");
            $sql->execute(["item"=>$itemPedido]);
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

        public function datosEntidad($entidad){
            try {
                $sql=$this->db->connect()->prepare("SELECT
                                                    cm_entidad.cnumdoc,
                                                    cm_entidad.crazonsoc,
                                                    UPPER(cm_entidadcon.cnombres) AS contacto,
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
                                                    cm_entidad.cnumdoc = :entidad
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
                                                    tb_parametros.cdescripcion  AS pago
                                                FROM
                                                    lg_proformacab
                                                    INNER JOIN tb_parametros ON lg_proformacab.ccondpago = tb_parametros.nidreg 
                                                WHERE
                                                    lg_proformacab.cotref =:proforma");
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

        public function ordenarItems($items) {
            $data = json_decode($items);
            $nreg = count($data);
            $counter = 0;

            //return array("actualizados"=>$nreg);
            
           
                try {
                    for ($i=0; $i < $nreg; $i++) {
                        $sql = $this->db->connect()->prepare("UPDATE lg_ordendet SET lg_ordendet.item =:nroItem WHERE lg_ordendet.nitemord =:idItem");
                        $sql->execute(["nroItem"=>$data[$i]->item,"idItem"=>$data[$i]->codigo]);
                        $counter++;
                    }
                    
                    return array("actualizados"=>$counter);

                }catch (PDOException $th) {
                    echo $th->getMessage();
                    return false;
                }
        }

        public function anularOrden($id) {
            try {
                $respuesta = true;
                $despachos = $this->verificarDespachos($id);
                $ingresos = $this->verificarIngresos($id);

                if ( $despachos > 0 || $ingresos > 0 ) {
                    $respuesta = false;
                }

                if ( $respuesta ) {
                    $sql = $this->db->connect()->prepare("UPDATE lg_ordencab 
                                                        SET lg_ordencab.nEstadoDoc = 105,
                                                            lg_ordencab.nflgactivo = 0
                                                        WHERE lg_ordencab.id_regmov = :id");
                    $sql->execute(["id"=>$id]);
                    $rowCount = $sql->rowCount();

                    if ($rowCount > 0) {
                        $this->anularItemsOrden($id);
                    }
                }

                return array("respuesta"=>$respuesta, 
                            "despachos"=>$despachos,
                            "ingresos"=>$ingresos);

            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function verificarIngresos($id) {
            try {
                $sql = $this->db->connect()->prepare("SELECT COUNT(alm_recepcab.idref_abas) AS ingresos 
                                                    FROM alm_recepcab 
                                                    WHERE alm_recepcab.idref_abas = :id");
                $sql->execute(["id"=>$id]);

                $result = $sql->fetchAll();
                return $result[0]['ingresos'];
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function verificarDespachos($id) {
            try {
                //no te olvides nropedido almacena el numero de orden
                $sql = $this->db->connect()->prepare("SELECT COUNT(alm_despachodet.nropedido) AS despachos 
                                                        FROM alm_despachodet 
                                                        WHERE alm_despachodet.nropedido = :id");
                $sql->execute(["id"=>$id]);

                $result = $sql->fetchAll();
                return $result[0]['despachos'];

            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function anularItemsOrden($id) {
            try {
                $sql = $this->db->connect()->prepare("UPDATE lg_ordendet 
                                                        SET lg_ordendet.nEstadoReg = 105,
                                                            lg_ordendet.nflgactivo = 0
                                                        WHERE lg_ordendet.id_orden = :id");
                $sql->execute(["id"=>$id]);

            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function listarOrdenScroll($pagina,$cantidad){
            try {
                $inicio = ($pagina - 1) * $cantidad;
                $limite = $this->contarItems();

                $sql = $this->db->connect()->prepare("SELECT
                                                        tb_costusu.ncodcos,
                                                        tb_costusu.ncodproy,
                                                        tb_costusu.id_cuser,
                                                        lg_ordencab.id_regmov,
                                                        LPAD(lg_ordencab.cnumero,6,0) AS cnumero,
                                                        DATE_FORMAT(lg_ordencab.ffechadoc,'%d/%m/%Y') AS emision,
                                                        lg_ordencab.nNivAten,
                                                        lg_ordencab.nEstadoDoc,
                                                        lg_ordencab.ncodpago,
                                                        lg_ordencab.nplazo,
                                                        lg_ordencab.cdocPDF,
                                                        UPPER( lg_ordencab.cObservacion ) AS concepto,
                                                        UPPER( tb_pedidocab.detalle ) AS detalle,
                                                        UPPER(
                                                        CONCAT_WS( tb_area.ccodarea, tb_area.cdesarea )) AS area,
                                                        UPPER(
                                                        CONCAT_WS( tb_proyectos.ccodproy, tb_proyectos.cdesproy )) AS costos,
                                                        tb_proyectos.ccodproy,
                                                        lg_ordencab.nfirmaLog,
                                                        lg_ordencab.nfirmaFin,
                                                        lg_ordencab.nfirmaOpe,
                                                        tb_parametros.cdescripcion AS atencion,
                                                        UPPER(cm_entidad.crazonsoc) AS proveedor,
                                                        IF(ISNULL(lg_ordencab.nfirmaLog),0,1) AS logistica,
                                                        IF(ISNULL(lg_ordencab.nfirmaFin),0,1) AS finanzas,
                                                        IF(ISNULL(lg_ordencab.nfirmaOpe),0,1) AS operaciones,
                                                        IF(lg_ordencab.nEstadoDoc = 59,'resaltado_firma','-') AS resaltado 
                                                    FROM
                                                        tb_costusu
                                                        INNER JOIN lg_ordencab ON tb_costusu.ncodproy = lg_ordencab.ncodpry
                                                        INNER JOIN tb_pedidocab ON lg_ordencab.id_refpedi = tb_pedidocab.idreg
                                                        INNER JOIN tb_area ON lg_ordencab.ncodarea = tb_area.ncodarea
                                                        INNER JOIN tb_proyectos ON lg_ordencab.ncodpry = tb_proyectos.nidreg
                                                        INNER JOIN tb_parametros ON lg_ordencab.nNivAten = tb_parametros.nidreg
                                                        INNER JOIN cm_entidad ON lg_ordencab.id_centi = cm_entidad.id_centi 
                                                    WHERE
                                                        tb_costusu.id_cuser = :user 
                                                        AND tb_costusu.nflgactivo = 1
                                                    ORDER BY lg_ordencab.ffechadoc DESC
                                                    LIMIT $inicio,$cantidad");
                
                $sql->execute(["user"=>$_SESSION['iduser']]);

                $rc = $sql->rowcount();
                $item = 1;

                if ($rc > 0){
                    while( $rs = $sql->fetch()) {
                        $datos[] = $rs;
                    }
                }

                return array("filas"=>$datos,
                            'quedan'=>($inicio + $cantidad) < $limite);

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function contarItems(){
            try {
                $sql = $this->db->connect()->query("SELECT COUNT(id_regmov) AS regs FROM lg_ordencab WHERE nflgactivo = 1");
                $sql->execute();
                $filas = $sql->fetch();

                return $filas['regs'];
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }
    }
?>