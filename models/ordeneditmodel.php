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
                                                        AND tb_costusu.nflgactivo = 1
                                                        AND lg_ordencab.nEstadoDoc = 59");
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
                tb_pedidodet.idpedido,
                FORMAT(tb_pedidodet.cant_aprob, 2) AS cantidad,
                FORMAT(tb_pedidodet.cant_orden, 2) AS saldo,
                FORMAT(tb_pedidodet.precio, 2) AS precio,
                tb_pedidodet.igv,
                FORMAT(tb_pedidodet.total, 2) AS total,
                tb_pedidodet.estadoItem,
                UPPER(
                    CONCAT_WS(
                        ' ',
                        cm_producto.cdesprod,
                        tb_pedidodet.observaciones
                    )
                ) AS cdesprod,
                cm_producto.ccodprod,
                cm_producto.id_cprod,
                tb_unimed.ncodmed,
                tb_unimed.cabrevia AS unidad,
                UPPER(tb_proyectos.cdesproy) AS costos,
                tb_area.ncodarea,
                UPPER(tb_area.cdesarea) AS area,
                tb_pedidodet.iditem,
                tb_pedidodet.idcostos,
                tb_pedidodet.idarea,
                tb_pedidocab.idreg,
                tb_pedidocab.nrodoc,
                tb_pedidocab.emision,
                tb_pedidocab.concepto,
                tb_pedidodet.entidad,
                tb_pedidodet.total AS total_numero
            FROM
                tb_costusu
            INNER JOIN tb_pedidodet ON tb_costusu.ncodproy = tb_pedidodet.idcostos
            INNER JOIN cm_producto ON tb_pedidodet.idprod = cm_producto.id_cprod
            INNER JOIN tb_unimed ON tb_pedidodet.unid = tb_unimed.ncodmed
            INNER JOIN tb_proyectos ON tb_pedidodet.idcostos = tb_proyectos.nidreg
            INNER JOIN tb_area ON tb_pedidodet.idarea = tb_area.ncodarea
            INNER JOIN tb_pedidocab ON tb_pedidodet.idpedido = tb_pedidocab.idreg
            WHERE
                tb_costusu.nflgactivo = 1
            AND tb_costusu.id_cuser = :user
            AND tb_pedidodet.estadoItem = 54
            AND tb_pedidodet.cant_aprob > 0");

            //se cambia el 58 para llama los items directo con aprobacion
                $sql->execute(["user"=>$_SESSION['iduser']]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()) {
                        $saldo = $rs['saldo'] > 0 ? $rs['saldo'] : $rs['cantidad'];

                        $salida .='<tr class="pointer" data-pedido="'.$rs['idpedido'].'"
                                                       data-entidad="'.$rs['entidad'].'"
                                                       data-unidad="'.$rs['unidad'].'"
                                                       data-cantidad ="'.$saldo.'"
                                                       data-total="'.$rs['total_numero'].'"
                                                       data-codprod="'.$rs['id_cprod'].'"
                                                       data-iditem="'.$rs['iditem'].'">
                                        <td class="textoCentro">'.str_pad($rs['nrodoc'],6,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.date("d/m/Y", strtotime($rs['emision'])).'</td>
                                        <td class="pl5px">'.$rs['concepto'].'</td>
                                        <td class="pl5px">'.$rs['area'].'</td>
                                        <td class="pl5px">'.$rs['costos'].'</td>
                                        <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                        <td class="pl5px">'.$rs['cdesprod'].'</td>
                                    </tr>';
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo $th->getMessage();
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
            $titulo = $titulo . " " . $orden;
            
            $file = $prefix.$noc."_".$cabecera['codigo_costos'].".pdf";
            $entrega = $this->calcularDias($cabecera['fentrega']);

            $pdf = new PDF($titulo,$condicion,$cabecera['emision'],$cabecera['moneda'],$entrega,
                            $cabecera['lentrega'],$cabecera['ncotiz'],$cabecera['fentrega'],$cabecera['cpago'],$cabecera['total'],
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
                                                            ncodpago=:pago
                                                        WHERE id_regmov = :id");
                $sql->execute(['entrega'=>$cab->fentrega,
                                "total"=>$cab->total,
                                "transp"=>$cab->codigo_transporte,
                                "plazo"=>$entrega,
                                "alm"=>$cab->codigo_almacen,
                                "moneda"=>$cab->codigo_moneda,
                                "tcambio"=>$cab->tcambio,
                                "pago"=>$cab->codigo_pago,
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
                    $sql->execute(["id"=>$indice,
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
                                                                        ntotal = :total
                                                WHERE lg_ordendet.nitemord = :id");
                    $sql->execute([ "cantidad"=>$datos[$i]->cantidad,
                                    "precio"=>$datos[$i]->precio,
                                    "saldo" =>$datos[$i]->saldo,
                                    "total"=>$datos[$i]->total,
                                    "id"=>$datos[$i]->itemorden]);
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
    }
?>