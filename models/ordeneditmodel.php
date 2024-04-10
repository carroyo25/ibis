<?php
    class OrdenEditModel extends Model{

        public function __construct(){
            parent::__construct();
        }

        public function listarOrdenesEdit($user){
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
                                                         lg_ordencab.ntotal,
                                                         lg_ordencab.ncodmon,
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
                                                         INNER JOIN tb_parametros AS estados ON lg_ordencab.nEstadoDoc = estados.nidreg 
                                                     WHERE
                                                         tb_costusu.id_cuser = :user 
                                                         AND tb_costusu.nflgactivo = 1
                                                         AND YEAR(lg_ordencab.ffechadoc) = YEAR(NOW())
                                                         ORDER BY id_regmov DESC");
                 $sql->execute(["user"=>$_SESSION['iduser']]);
                 $rowCount = $sql->rowCount();
 
                 
 
                 if ($rowCount > 0){
                     while ($rs = $sql->fetch()) {
 
                         $montoDolares = 0;
                         $montoSoles = 0;
                         $estado = '';
 
                         $log = is_null($rs['nfirmaLog']) ? '<i class="far fa-square"></i>' : '<i class="far fa-check-square"></i>';
                         $ope = is_null($rs['nfirmaOpe']) ? '<i class="far fa-square"></i>' : '<i class="far fa-check-square"></i>';
                         $fin = is_null($rs['nfirmaFin']) ? '<i class="far fa-square"></i>' : '<i class="far fa-check-square"></i>';
 
                         $flog = is_null($rs['nfirmaLog']) ? 0 : 1;
                         $fope = is_null($rs['nfirmaOpe']) ? 0 : 1;
                         $ffin = is_null($rs['nfirmaFin']) ? 0 : 1;
 
 
                         if ( $rs['ncodmon'] == 20) {
                             $montoSoles = "S/. ".number_format($rs['ntotal'],2);
                             $montoDolares = "";
                         }else{
                             $montoSoles = "";
                             $montoDolares =  "$ ".number_format($rs['ntotal'],2);
                         }
 
                         if ( $rs['nEstadoDoc'] == 49) {
                             $estado = "procesando";
                         }else if ( $rs['nEstadoDoc'] == 59 ) {
                             $estado = "firmas";
                         }else if ( $rs['nEstadoDoc'] == 60 ) {
                             $estado = "recepcion";
                         }else if ( $rs['nEstadoDoc'] == 62 ) {
                             $estado = "despacho";
                         }else if ( $rs['nEstadoDoc'] == 105 ) {
                             $estado = "anulado";
                             $montoDolares = "";
                             $montoSoles = "";
                         }
 
 
                         $salida .='<tr class="pointer " data-indice="'.$rs['id_regmov'].'" 
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
                                     <td class="textoDerecha">'.$montoSoles.'</td>
                                     <td class="textoDerecha">'.$montoDolares.'</td>
                                     <td class="textoCentro '.strtolower($rs['atencion']).'">'.$rs['atencion'].'</td>
                                     <td class="textoCentro '.$estado.'">'.strtoupper($estado).'</td>
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
                                "clase"=>"mensaje_correcto",
                                "user_modifica"=>$_SESSION['iduser']);

                
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
                                                            lg_ordendet.nestado = 0,
                                                            lg_ordendet.ncanti = 0,
                                                            lg_ordendet.niddeta = null
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
                                                    ORDER BY lg_ordencab.id_regmov DESC
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

        public function consultarOrdenEditId($id){
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                        lg_ordencab.id_regmov,
                                                        LPAD( lg_ordencab.cnumero, 6, 0 ) AS cnumero,
                                                        lg_ordencab.ffechadoc,
                                                        lg_ordencab.ncodcos,
                                                        lg_ordencab.ncodarea,
                                                        lg_ordencab.id_centi,
                                                        lg_ordencab.ctiptransp,
                                                        lg_ordencab.ncodpago,
                                                        lg_ordencab.nplazo,
                                                        lg_ordencab.ncodcot,
                                                        lg_ordencab.nEstadoDoc,
                                                        lg_ordencab.id_refpedi,
                                                        lg_ordencab.ntcambio,
                                                        lg_ordencab.cnumcot,
                                                        lg_ordencab.userModifica,
                                                        UPPER( lg_ordencab.cObservacion ) AS concepto,
                                                        UPPER( tb_pedidocab.detalle ) AS detalle,
                                                        tb_pedidocab.docPdfAprob,
                                                        UPPER(
                                                        CONCAT_WS( ' ', tb_proyectos.ccodproy, tb_proyectos.cdesproy )) AS costos,
                                                        lg_ordencab.ncodpry,
                                                        lg_ordencab.ncodalm,
                                                        UPPER(
                                                        CONCAT_WS( ' ', tb_area.ccodarea, tb_area.cdesarea )) AS area,
                                                        lg_ordencab.ncodmon,
                                                        monedas.cdescripcion AS nombre_moneda,
                                                        monedas.cabrevia AS abrevia_moneda,
                                                        lg_ordencab.ntipmov,
                                                        tipos.cdescripcion AS tipo,
                                                        pagos.cdescripcion AS pagos,
                                                        lg_ordencab.ffechaent,
                                                        estados.cabrevia AS estado,
                                                        estados.cdescripcion AS descripcion_estado,
                                                        cm_entidad.crazonsoc,
                                                        cm_entidad.cnumdoc,
                                                        UPPER( cm_entidadcon.cnombres ) AS cnombres,
                                                        cm_entidadcon.cemail,
                                                        cm_entidadcon.ctelefono1,
                                                        transportes.cdescripcion AS transporte,
                                                        UPPER( tb_almacen.cdesalm ) AS cdesalm,
                                                        UPPER( tb_almacen.ctipovia ) AS direccion,
                                                        cm_entidad.cviadireccion,
                                                        cm_entidad.cemail AS mail_entidad,
                                                        cm_entidad.nagenret,
                                                        lg_ordencab.cverificacion,
                                                        lg_ordencab.ntotal,
                                                        lg_ordencab.nigv,
                                                        lg_ordencab.lentrega,
                                                        lg_ordencab.cReferencia,
                                                        FORMAT( lg_ordencab.ntotal, 2 ) AS ctotal,
                                                        tb_pedidocab.nivelAten,
                                                        lg_ordencab.nNivAten AS autorizado,
                                                        lg_ordencab.nfirmaLog,
                                                        lg_ordencab.nfirmaFin,
                                                        lg_ordencab.nfirmaOpe,
                                                        LPAD( tb_pedidocab.nrodoc, 6, 0 ) AS nrodoc,
                                                        ( SELECT SUM( lg_ordendet.nunitario * lg_ordendet.ncanti ) FROM lg_ordendet WHERE lg_ordendet.id_orden = lg_ordencab.id_regmov ) AS total_multiplicado,
                                                        UPPER(lg_ordenextras.cdescription) AS condiciones
                                                    FROM
                                                        lg_ordencab
                                                        INNER JOIN tb_pedidocab ON lg_ordencab.id_refpedi = tb_pedidocab.idreg
                                                        INNER JOIN tb_proyectos ON lg_ordencab.ncodcos = tb_proyectos.nidreg
                                                        INNER JOIN tb_area ON lg_ordencab.ncodarea = tb_area.ncodarea
                                                        INNER JOIN tb_parametros AS monedas ON lg_ordencab.ncodmon = monedas.nidreg
                                                        INNER JOIN tb_parametros AS tipos ON lg_ordencab.ntipmov = tipos.nidreg
                                                        INNER JOIN tb_parametros AS pagos ON lg_ordencab.ncodpago = pagos.nidreg
                                                        INNER JOIN tb_parametros AS estados ON lg_ordencab.nEstadoDoc = estados.nidreg
                                                        INNER JOIN cm_entidad ON lg_ordencab.id_centi = cm_entidad.id_centi
                                                        INNER JOIN cm_entidadcon ON cm_entidad.id_centi = cm_entidadcon.id_centi
                                                        INNER JOIN tb_parametros AS transportes ON lg_ordencab.ctiptransp = transportes.nidreg
                                                        INNER JOIN tb_almacen ON lg_ordencab.ncodalm = tb_almacen.ncodalm
                                                        INNER JOIN lg_ordendet ON lg_ordencab.id_regmov = lg_ordendet.id_regmov
                                                        LEFT JOIN lg_ordenextras ON lg_ordencab.id_regmov = lg_ordenextras.idorden
                                                    WHERE
                                                        lg_ordencab.id_regmov = :id 
                                                        AND lg_ordencab.nflgactivo = 1 
                                                        LIMIT 1");
                $sql->execute(["id"=>$id]);
                $rowCount = $sql->rowCount();
                
                if ($rowCount > 0) {
                    $docData = array();
                    while($row=$sql->fetch(PDO::FETCH_ASSOC)){
                        $docData[] = $row;
                    }
                }

                $detalles = $this->consultarDetallesOrden($id);
                $comentarios = null;
                $total = $this->calculaTotalOrden($id);
                $ncomentarios = null;
                $adjuntos = null;
                $adicionales = null;
                $nro_adjuntos = null;

                return array("cabecera"=>$docData,
                            "detalles"=>$detalles,
                            "comentarios"=>$comentarios,
                            "total"=>$total,
                            "bocadillo"=>$ncomentarios,
                            "adjuntos"=>$adjuntos,
                            "adicionales"=>$adicionales,
                            "total_adicionales"=>null,
                            "total_adjuntos"=>$nro_adjuntos);

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function consultarDetallesOrden($id){
            try {
                $salida = "";
                $sql=$this->db->connect()->prepare("SELECT
                                            lg_ordendet.nitemord,
                                            lg_ordendet.id_regmov,
                                            lg_ordendet.niddeta,
                                            lg_ordendet.nidpedi,
                                            lg_ordendet.cobserva,
                                            lg_ordendet.id_cprod,
                                            REPLACE ( FORMAT( lg_ordendet.ncanti, 2 ), ',', '' ) AS ncanti,
                                            REPLACE ( FORMAT(lg_ordendet.nunitario,4), ',', '') AS nunitario,
                                            FORMAT( lg_ordendet.nigv, 4 ) AS nigv,
                                            FORMAT( tb_pedidodet.total - lg_ordendet.nigv, 2 ) AS subtotal,
                                            FORMAT( lg_ordendet.ntotal, 4 ) AS ntotal,
                                            REPLACE ( FORMAT( lg_ordendet.nunitario * lg_ordendet.ncanti, 2 ), ',', '' ) AS total_real,
                                            cm_producto.ccodprod,
                                            UPPER(cm_producto.cdesprod) AS cdesprod,
                                            cm_producto.nund,
                                            tb_unimed.cabrevia,
                                            FORMAT( tb_pedidodet.total, 2 ) AS total,
                                            tb_pedidodet.idpedido,
                                            tb_pedidodet.nroparte,
                                            tb_pedidodet.estadoItem,
                                            monedas.cabrevia AS moneda,
                                            tb_pedidodet.total AS total_numero,
                                            LPAD( tb_pedidocab.nrodoc, 6, 0 ) AS nro_pedido 
                                        FROM
                                            lg_ordendet
                                            INNER JOIN cm_producto ON lg_ordendet.id_cprod = cm_producto.id_cprod
                                            INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed
                                            INNER JOIN tb_pedidodet ON lg_ordendet.niddeta = tb_pedidodet.iditem
                                            INNER JOIN tb_parametros AS monedas ON lg_ordendet.nmonref = monedas.nidreg
                                            INNER JOIN tb_pedidocab ON lg_ordendet.nidpedi = tb_pedidocab.idreg 
                                        WHERE
                                            lg_ordendet.id_regmov = :id 
                                            AND ISNULL(
                                            lg_ordendet.nflgactivo)");
                $sql->execute(["id"=>$id]);
                $rowCount = $sql->rowCount();
                $item = 1;
                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()){

                        $observa = $rs['cobserva'] == 'undefined' ? '' : $rs['cobserva'];
                        $nroparte = $rs['nroparte'] == 'undefined' ? '' : $rs['nroparte'];


                        $salida.='<tr data-grabado="1" 
                                        data-total="'.$rs['ntotal'].'" 
                                        data-codprod="'.$rs['id_cprod'].'" 
                                        data-itPed="'.$rs['niddeta'].'"
                                        data-itOrd="'.$rs['nitemord'].'"
                                        data-cant="'.$rs['ncanti'].'"
                                        data-proceso="'.$rs['estadoItem'].'"
                                        data-pedido="'.$rs['nidpedi'].'">
                                    <td class="textoCentro"><a href="'.$rs['nitemord'].'" data-option="delete" title="Eliminar Item"><i class="fas fa-ban"></i></a></td>
                                    <td class="textoCentro">'.str_pad($item++,3,0,STR_PAD_LEFT).'</td>
                                    <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                    <td class="pl20px">'.$rs['cdesprod'].'</td>
                                    <td class="textoCentro">'.$rs['cabrevia'].'</td>
                                    <td class="textoDerecha pr5px"><input type="number" 
                                                                    step="any" 
                                                                    placeholder="0.00" 
                                                                    onchange="(function(el){el.value=parseFloat(el.value).toFixed(2);})(this)"
                                                                    onclick="this.select()"
                                                                    value='.$rs['ncanti'].'>
                                    </td>
                                    <td class="textoDerecha pr5px">
                                    <input type="number"
                                        step="any" 
                                        placeholder="0.00" 
                                        onclick="this.select()"
                                        onchange="(function(el){el.value=parseFloat(el.value).toFixed(4);})(this)"
                                        value='.$rs['nunitario'].'
                                        class="textoDerecha">
                                    </td>
                                    <td class="textoDerecha pr5px">'.$rs['total_real'].'</td>
                                    <td class="textoCentro">'.$nroparte.'</td>
                                    <td class="pl20px">'.$rs['nro_pedido'].'</td>
                                    <td><textarea>'.$observa.'</textarea></td>
                                    <td class="textoCentro"><a href="'.$rs['nitemord'].'" data-option="change" title="Cambiar Item"><i class="fas fa-exchange-alt"></i></a></td>
                                    <td class="textoCentro"><a href="'.$rs['nitemord'].'" data-option="free" title="Liberar Item"><i class="fas fa-eraser"></i></a></td>
                                </tr>';
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function calculaTotalOrden($id){
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                        FORMAT( SUM( lg_ordendet.ncanti * lg_ordendet.nunitario ) + lg_ordendet.nigv, 2 ) AS total 
                                                    FROM
                                                        lg_ordendet 
                                                    WHERE
                                                        lg_ordendet.id_regmov = :id");
                $sql->execute(["id"=>$id]);
                $result = $sql->fetchAll();

                return $result[0]["total"];
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function modificarItem($parametros){
           $idorden = $parametros['idorden'];
           $idpedido = $parametros['idpedido'];
           $orden = $parametros['orden'];
           $suma = $parametros['suma'];
           $accion = $parametros['accion'];
           $usuario = $parametros['usuario'];

           $respuesta = "Error al actualizar"; 

           if ( $accion === "d" || $accion === "f" ){
                try {
                    $sql = $this->db->connect()->prepare("UPDATE lg_ordendet 
                                                        SET lg_ordendet.id_regmov = null, 
                                                            lg_ordendet.niddeta = null, 
                                                            lg_ordendet.ncanti = 0, 
                                                            lg_ordendet.nestado = 0,
                                                            lg_ordendet.nEstadoReg = 105
                                                        WHERE lg_ordendet.nitemord =:indice
                                                        LIMIT 1");

                    $sql->execute(["indice"=>$idorden]);

                    if ( $sql->rowCount() > 0 ){
                        $this->actualizarPedido($accion,$idpedido);
                        $this->actualizarPrecioOrden($orden,$suma,$usuario);
                        $respuesta = "Item Anulado"; 
                    }
                } catch (PDOException $th) {
                    echo "Error: ".$th->getMessage();
                    return false;
                }

                
           }elseif ($accion === "c"){
                $respuesta = "Cambiar Item"; 
           }

           return array("respuesta"=>$respuesta);
        }

        private function actualizarPedido($accion,$indice){
            $estado = $accion == "d" ? 105:54;

            try {
                $sql = $this->db->connect()->prepare("UPDATE tb_pedidodet 
                                                        SET tb_pedidodet.idorden = null,
                                                            tb_pedidodet.estadoItem =:estado,
                                                            tb_pedidodet.nflgOrden = 0,
                                                            tb_pedidodet.cant_orden = 0
                                                        WHERE tb_pedidodet.iditem = :indice
                                                        LIMIT 1");
                $sql->execute(["estado"=>$estado,"indice"=>$indice]);

            } catch (PDOException $th) {
                    echo "Error: ".$th->getMessage();
                    return false;
            }
        }

        private function actualizarPrecioOrden($indice,$suma,$usuario){
            try {
                $sql = $this->db->connect()->prepare("UPDATE lg_ordencab 
                                                    SET lg_ordencab.ntotal = :suma,
                                                        lg_ordencab.userModifica = :usuario
                                                    WHERE lg_ordencab.id_regmov = :indice
                                                    LIMIT 1");
                $sql->execute(["suma"=>$suma,"indice"=>$indice,"usuario"=>$usuario]);
            } catch (PDOException $th) {
                    echo "Error: ".$th->getMessage();
                    return false;
                }
        }

    }
?>