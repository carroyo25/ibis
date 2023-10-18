<?php
    class MadresModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function importarGuias($cc,$guia){
            try {
                $salida = "";

                $g = $guia == "" ? "%": "%".$guia."%";

                $sql = $this->db->connect()->prepare("SELECT
                                                lg_guias.cnumguia,
                                                alm_despachocab.ncodpry,
                                                tb_proyectos.ccodproy,
                                                UPPER(tb_proyectos.cdesproy) AS cdesproy,
	                                            lg_guias.id_regalm,
                                                DATE_FORMAT(alm_despachocab.ffecdoc,'%d/%m/%Y') AS ffecdoc  
                                            FROM
                                                lg_guias
                                                LEFT JOIN alm_despachocab ON lg_guias.id_regalm = alm_despachocab.id_regalm
                                                INNER JOIN tb_proyectos ON alm_despachocab.ncodpry = tb_proyectos.nidreg 
                                            WHERE
                                                lg_guias.cnumguia <> ''
                                                AND alm_despachocab.nflgactivo = 1 
                                                AND lg_guias.cnumguia LIKE :guia 
                                                AND alm_despachocab.ncodpry LIKE :costos 
                                                AND lg_guias.flgmadre = 0");
                
                $sql->execute(["guia"=>$g,"costos"=>$cc]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()) {
                        $salida .='<tr data-despacho="'.$rs['id_regalm'].'" data-guia="'.$rs['cnumguia'].'">
                                        <td class="textoCentro">'.$rs['cnumguia'].'</td>
                                        <td class="textoCentro">'.$rs['ffecdoc'].'</td>
                                        <td class="pl10px">'.$rs['cdesproy'].'</td>
                                        <td class="textoCentro"><button>Seleccionar</button></td>
                                    </tr>';
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function importarItemsDespacho($idx){
            try {
                $salida = "";

                $sql = $this->db->connect()->prepare("SELECT
                                                        UPPER( cm_producto.ccodprod ) AS cccodprod,
                                                        UPPER( cm_producto.cdesprod ) AS cdesprod,
                                                        alm_despachodet.ncantidad,
                                                        tb_unimed.cabrevia,
                                                        alm_despachodet.id_regalm,
                                                        alm_despachodet.niddeta,
                                                        alm_despachocab.cnumguia 
                                                    FROM
                                                        alm_despachodet
                                                        INNER JOIN cm_producto ON alm_despachodet.id_cprod = cm_producto.id_cprod
                                                        INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed
                                                        INNER JOIN alm_despachocab ON alm_despachodet.id_regalm = alm_despachocab.id_regalm 
                                                    WHERE
                                                        alm_despachodet.id_regalm = :id 
                                                        AND alm_despachodet.nflgactivo = 1");
                
                $sql->execute(["id"=>$idx]);
                $rowCount = $sql->rowCount();

                $item = 0;

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()) {
                        $salida .='<tr data-despacho="'.$rs['id_regalm'].'" data-itemdespacho="'.$rs['niddeta'].'">
                                        <td class="textoCentro">'.str_pad($item++,3,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.$rs['cccodprod'].'</td>
                                        <td class="pl10px">'.$rs['cdesprod'].'</td>
                                        <td class="textoCentro">'.$rs['cabrevia'].'</td>
                                        <td class="textoDerecha">'.$rs['ncantidad'].'</td>
                                        <td class="textoDerecha">'.$rs['cnumguia'].'</td>
                                    </tr>';
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function grabarGuia($datos){
            $detalles = json_decode($datos['detalles']);
            $nreg = count($detalles);

            $mensaje = "Error, no se grabaron los datos";
            $clase = "mensaje error";

           
            try {
                $sql = $this->db->connect()->prepare("INSERT INTO lg_guiamadre SET ffecdoc =:emision,
                                                                            ffectraslado = :traslado,
                                                                            cnroguia = :guia,
                                                                            ncostos =:costos,
                                                                            nlamorigen =:origen,
                                                                            nalmdestino = :destino,
                                                                            nentitrans = :transporte,
                                                                            nmottranp = :motivo,
                                                                            ntipmov = :tipo,
                                                                            clincencia =:licencia,
                                                                            ndni =:dni,
                                                                            cmarca =:marca,
                                                                            cplaca =:placa,
                                                                            npeso =:peso,
                                                                            nbultos =:bultos,
                                                                            useremit =:user,
                                                                            idaprueba =:aprueba,
                                                                            iddestino =:recibe,
                                                                            nTipoEnvio =:envio,
                                                                            cConductor =:conductor,
                                                                            cObserva=:observaciones");
            
                $sql->execute(["emision"=>$datos['emision'],
                                "traslado"=>$datos['traslado'],
                                "guia"=>$datos['guia'],
                                "costos"=>$datos['costos'],
                                "origen"=>$datos['alm_origen'],
                                "destino"=>$datos['alm_destino'],
                                "transporte"=>$datos['transportista'],
                                "motivo"=>$datos['modalidad'],
                                "tipo"=>$datos['tipo'],
                                "licencia"=>$datos['licencia'],
                                "dni"=>$datos['dni'],
                                "marca"=>$datos['marca'],
                                "placa"=>$datos['placa'],
                                "peso"=>$datos['peso'],
                                "bultos"=>$datos['bultos'],
                                "user"=>$datos['useremit'],
                                "aprueba"=>$datos['aprueba'],
                                "recibe"=>$datos['recibe'],
                                "envio"=>$datos['envio'],
                                "conductor"=>$datos['conductor'],
                                "observaciones"=>$datos['observaciones']]);

                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    $indice = $this->generarNumeroSunat();
                    $this->grabarDetallesGuia($indice,$detalles,$datos['guia']);
                    $mensaje = "Guia grabada correctamente";
                    $clase = "mensaje_correcto";
                }

                return array("guia"=>$datos['guia'],"mensaje"=>$mensaje,"clase" => $clase );
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function grabarDetallesGuia($indice,$detalles,$guiaMadre){
            $nreg = count($detalles);

            for ($i=0; $i < $nreg; $i++) { 
                try {
                    $sql=$this->db->connect()->prepare("INSERT INTO lg_detallemadres SET ndespacho=:despacho,
                                                                                          itemdespacho=:item,
                                                                                          idguiasunat=:indice");
                    
                    $sql->execute(["indice"=>$indice,"item"=>$detalles[$i]->iddespacho,"despacho"=>$detalles[$i]->despacho]);

                    $rowCount = $sql->rowCount();

                    if ($rowCount > 0) {
                        $this->actualizaEstadoGuia($detalles[$i]->despacho,$guiaMadre);
                    }
                } catch (PDOException $th) {
                    echo $th->getMessage();
                    return false;
                }
            }
        }

        private function generarNumeroSunat(){
            try {
                $sql = $this->db->connect()->query("SELECT MAX(idreg) AS numero FROM lg_guiamadre");
                $sql->execute();

                $result = $sql->fetchAll();
                
                return $result[0]['numero'];
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function listarGuiasScroll($pagina,$cantidad){
            try {
                $inicio = ($pagina - 1) * $cantidad;
                $limite = $this->contarItems();

                if ($limite < 30) {
                    $cantidad = $limite;
                }

                $sql = $this->db->connect()->prepare("SELECT
                                                            lg_guiamadre.idreg,
                                                            lg_guiamadre.cnroguia,
                                                            lg_guiamadre.nflgSunat,
                                                            DATE_FORMAT( lg_guiamadre.ffecdoc, '%d/%m/%Y' ) AS emision,
                                                            DATE_FORMAT( lg_guiamadre.ffectraslado, '%d/%m/%Y' ) AS traslado,
                                                            UPPER( origen.cdesalm ) AS almacen_origen,
                                                            UPPER( destino.cdesalm ) AS almacen_destino 
                                                        FROM
                                                            lg_guiamadre
                                                            LEFT JOIN tb_almacen AS origen ON lg_guiamadre.nlamorigen = origen.ncodalm
                                                            LEFT JOIN tb_almacen AS destino ON lg_guiamadre.nalmdestino = destino.ncodalm
                                                        WHERE lg_guiamadre.nflgActivo = 1
                                                        LIMIT $inicio,$cantidad");
                
                $sql->execute();

                $rc = $sql->rowcount();

                if ($rc > 0){
                    while( $rs = $sql->fetch()) {
                        $guias[] = $rs;
                    }
                }

                if ($limite  > 30) {
                    if ( ($inicio + $cantidad) < $limite ){
                        $quedan = true;
                    }
                }else {
                    $quedan = false;
                }

                

                return array("guias"=>$guias,
                            'quedan'=> $quedan);

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function contarItems(){
            try {
                $sql = $this->db->connect()->query("SELECT COUNT(*) AS regs FROM lg_guiamadre WHERE nflgActivo = 1");
                $sql->execute();
                $filas = $sql->fetch();

                return $filas['regs'];
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function actualizaEstadoGuia($despacho,$guiaMadre){
            try {
                $sql = $this->db->connect()->prepare("UPDATE lg_guias SET lg_guias.flgmadre = 1,lg_guias.cnumadre=:guia WHERE id_regalm =:despacho");
                $sql->execute(["guia"=>$guiaMadre,"despacho"=>$despacho]);
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function guiaMadreID($id) {
            try {
                $salida = "";
                $sql= $this->db->connect()->prepare("SELECT
                                                        lg_guiamadre.idreg,
                                                        lg_guiamadre.cnroguia,
                                                        tb_user.cnombres AS autoriza,
                                                        lg_guiamadre.ffecdoc,
                                                        lg_guiamadre.ffectraslado,
                                                        UPPER( origen.cdesalm ) AS origen,
                                                        UPPER( destino.cdesalm ) AS destino,
                                                        lg_guiamadre.nflgSunat,
                                                        movimientos.cdescripcion AS movimiento,
                                                        UPPER( origen.ctipovia ) AS origen_direccion,
                                                        UPPER( destino.ctipovia ) AS destino_direccion,
                                                        cm_entidad.cnumdoc AS ruc_proveedor,
                                                        cm_entidad.crazonsoc AS nombre_proveedor,
                                                        UPPER( cm_entidad.cviadireccion ) AS direccion_proveedor,
                                                        UPPER( destinatarios.cnombres ) AS recibe,
                                                        lg_guiamadre.nTipoEnvio,
                                                        lg_guiamadre.clincencia,
                                                        lg_guiamadre.ndni,
                                                        lg_guiamadre.cmarca,
                                                        lg_guiamadre.cplaca,
                                                        lg_guiamadre.npeso,
                                                        lg_guiamadre.nbultos,
                                                        lg_guiamadre.cConductor,
                                                        lg_guiamadre.cObserva,
                                                        tb_parametros.cdescripcion AS tipo_envio,
                                                        lg_guiamadre.nmottranp,
	                                                    lg_guiamadre.ntipmov 
                                                    FROM
                                                        lg_guiamadre
                                                        LEFT JOIN tb_user ON lg_guiamadre.idaprueba = tb_user.iduser
                                                        LEFT JOIN tb_almacen AS origen ON lg_guiamadre.nlamorigen = origen.ncodalm
                                                        LEFT JOIN tb_almacen AS destino ON lg_guiamadre.nalmdestino = destino.ncodalm
                                                        LEFT JOIN tb_parametros AS movimientos ON lg_guiamadre.ntipmov = movimientos.nidreg
                                                        LEFT JOIN cm_entidad ON lg_guiamadre.nentitrans = cm_entidad.id_centi
                                                        LEFT JOIN tb_user AS destinatarios ON lg_guiamadre.iddestino = destinatarios.iduser
                                                        LEFT JOIN tb_parametros ON lg_guiamadre.nmottranp = tb_parametros.nidreg 
                                                    WHERE
                                                        lg_guiamadre.nflgActivo = :id 
                                                        AND lg_guiamadre.idreg = 1");

                $sql->execute(["id"=>$id]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while( $rs = $sql->fetch()) {
                        $guias[] = $rs;
                    }
                }

                return array("cabecera"=>$guias,
                             "detalles"=>$this->datosGuia($id));
                
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function datosGuia($id){
            $salida = "";

            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                        lg_detallemadres.idreg,
                                                        alm_despachodet.id_cprod,
                                                        FORMAT(alm_despachodet.ncantidad,2) AS ncantidad,
                                                        cm_producto.ccodprod,
                                                        UPPER( cm_producto.cdesprod ) AS cdesprod,
                                                        tb_unimed.cabrevia,
                                                        alm_despachocab.cnumguia 
                                                    FROM
                                                        lg_detallemadres
                                                        LEFT JOIN alm_despachodet ON lg_detallemadres.itemdespacho = alm_despachodet.niddeta
                                                        LEFT JOIN cm_producto ON alm_despachodet.id_cprod = cm_producto.id_cprod
                                                        LEFT JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed
                                                        LEFT JOIN alm_despachocab ON alm_despachodet.id_regalm = alm_despachocab.id_regalm 
                                                    WHERE
                                                        lg_detallemadres.idguiasunat = :id 
                                                        AND lg_detallemadres.nflgActivo = 1");
                $sql->execute(["id"=>$id]);

                $rowCount = $sql->rowCount();
                $item = 1;

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()) {
                        $salida.= '<tr>
                                    <td class="textoCentro">'.str_pad($item++,3,0,STR_PAD_LEFT).'</td>
                                    <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                    <td class="pl20px">'.$rs['cdesprod'].'</td>
                                    <td class="textoCentro">'.$rs['cabrevia'].'</td>
                                    <td class="textoDerecha">'.$rs['ncantidad'].'</td>
                                    <td class="textoCentro">'.$rs['cnumguia'].'</td>
                                </tr>';
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }

            return $salida;
        }

        public function enviarSunat($datos){
            $data = json_decode ($datos['datosGuia']);

            $envioSunat = $this->procesoSunat($datos);

            return array("archivo"=>$envioSunat['archivo'],
                         "token"=>$envioSunat['token']);
        }


        private function procesoSunat($datos){
            header('Access-Control-Allow-Origin: *');
            require 'public/libraries/efactura.php';

            $header = json_decode ($datos['datosGuia']);
            $body = json_decode ($datos['detalles']);

            $empresa = $header->destinatario_razon;
            $guia    = $header->numero_guia;

            $path = "public/documentos/guia_electronica/";

            $nombre_archivo = $header->destinatario_ruc.'-09-'.$header->serie_guia.'-'.$header->numero_guia;

            if(file_exists($path."XML/".$nombre_archivo.".xml")){
                unlink($path."XML/".$nombre_archivo.".xml");  
            }

            if ( $header->codigo_modalidad == 93) {
                $this->desarrollo_xml_externos($header,$body);
            }else if ($header->codigo_modalidad == 94){
                $this->desarrollo_xml_sepcon($header,$body);
            }else{
                $tipo_envio_sunat = 'Error en el tipo de envio';
            }

            //$token_access = $this->token('d12d8bf5-4b57-4c57-9569-9072b3e1bfcd', 'iLMGwQBEehJMXQ+Z/LR2KA==', '20504898173SISTEMA1', 'Lima123');
            $token_access = $this->token('test-85e5b0ae-255c-4891-a595-0b98c65c9854', 'test-Hty/M6QshYvPgItX2P0+Kw==', '20504898173MODDATOS', 'MODDATOS');

            return array("archivo"=>$nombre_archivo,
                         "token"=>$token_access);
        }

        private function token($client_id, $client_secret, $usuario_secundario, $usuario_password){
            //$url = "https://api-seguridad.sunat.gob.pe/v1/clientessol/".$client_id."/oauth2/token/";

            $url = "https://gre-test.nubefact.com/v1/clientessol/".$client_id."/oauth2/token/";

            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl, CURLOPT_POST, true);

            $datos = array(
                    'grant_type'    =>  'password',     
                    'scope'         =>  'https://api-cpe.sunat.gob.pe',
                    'client_id'     =>  $client_id,
                    'client_secret' =>  $client_secret,
                    'username'      =>  $usuario_secundario,
                    'password'      =>  $usuario_password
            );
            
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($datos));
            curl_setopt($curl, CURLOPT_COOKIEJAR, "public/documentos/cookies/cookies.txt");

            $headers = array('Content-Type' => 'Application/json');
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            $result = curl_exec($curl);
            curl_close($curl);

            $response = json_decode($result);

            return $response->access_token;
        }

        private function desarrollo_xml_sepcon($header,$detalles){
            $xml =  '<?xml version="1.0" encoding="UTF-8"?>
                    <DespatchAdvice xmlns="urn:oasis:names:specification:ubl:schema:xsd:DespatchAdvice-2" 
                                    xmlns:ds="http://www.w3.org/2000/09/xmldsig#" 
                                    xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" 
                                    xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2" 
                                    xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2">                    
                        <ext:UBLExtensions>
                            <ext:UBLExtension>
                                <ext:ExtensionContent></ext:ExtensionContent>
                            </ext:UBLExtension>
                        </ext:UBLExtensions>
                        <cbc:UBLVersionID>2.1</cbc:UBLVersionID>
                        <cbc:CustomizationID>2.0</cbc:CustomizationID>
                        <cbc:ID>'.$header->serie_guia.'-'.$header->numero_guia.'</cbc:ID>
                        <cbc:IssueDate>'.$header->fgemision.'</cbc:IssueDate>
                        <cbc:IssueTime>'.date("H:i:s").'</cbc:IssueTime>
                        <cbc:DespatchAdviceTypeCode>09</cbc:DespatchAdviceTypeCode>
                        <cac:Signature>
                            <cbc:ID>'.$header->destinatario_ruc.'</cbc:ID>
                            <cac:SignatoryParty>
                                <cac:PartyIdentification>
                                <cbc:ID>'.$header->destinatario_ruc.'</cbc:ID>
                                </cac:PartyIdentification>
                            </cac:SignatoryParty>
                            <cac:DigitalSignatureAttachment>
                                <cac:ExternalReference>
                                <cbc:URI>'.$header->destinatario_ruc.'</cbc:URI>
                                </cac:ExternalReference>
                            </cac:DigitalSignatureAttachment>
                        </cac:Signature>
                        <cac:DespatchSupplierParty>
                            <cac:Party>
                                    <cac:PartyIdentification>
                                        <cbc:ID schemeID="6" schemeName="Documento de Identidad" 
                                            schemeAgencyName="PE:SUNAT" 
                                            schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">'.$header->destinatario_ruc.'</cbc:ID>
                                    </cac:PartyIdentification>
                                    <cac:PartyName>
                                        <cbc:Name><![CDATA['.$header->destinatario_razon.']]></cbc:Name>
                                    </cac:PartyName>
                                    <cac:PartyLegalEntity>
                                        <cbc:RegistrationName><![CDATA['.$header->destinatario_razon.']]></cbc:RegistrationName>
                                    </cac:PartyLegalEntity>
                            </cac:Party>
                        </cac:DespatchSupplierParty>';

                $xml .= '<cac:DeliveryCustomerParty>
                                <cac:Party>
                                    <cac:PartyIdentification>
                                        <cbc:ID schemeID="6" 
                                        schemeName="Documento de Identidad" 
                                        schemeAgencyName="PE:SUNAT" 
                                        schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">'.$header->destinatario_ruc.'</cbc:ID>
                                    </cac:PartyIdentification>
                                    <cac:PartyLegalEntity>
                                        <cbc:RegistrationName><![CDATA['.$header->destinatario_razon.']]></cbc:RegistrationName>
                                    </cac:PartyLegalEntity>
                                </cac:Party>
                            </cac:DeliveryCustomerParty>';
                $xml .= '<cac:Shipment>
                            <cbc:ID>SUNAT_Envio</cbc:ID>
                            <cbc:HandlingCode 
                                listAgencyName="PE:SUNAT" 
                                listName="Motivo de traslado" 
                                listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo20">04</cbc:HandlingCode>
                            <cbc:GrossWeightMeasure unitCode="KGM">'.$header->peso.'</cbc:GrossWeightMeasure>
                            <cac:ShipmentStage>
                                <cbc:ID>1</cbc:ID>
                                <cbc:TransportModeCode listAgencyName="PE:SUNAT" 
                                    listName="Modalidad de traslado" 
                                    listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo18">01</cbc:TransportModeCode>
                                <cac:TransitPeriod>
                                    <cbc:StartDate>'.$header->ftraslado.'</cbc:StartDate>
                                </cac:TransitPeriod>
                                <cac:CarrierParty>
                                    <cac:PartyIdentification>
                                        <cbc:ID schemeID="6" 
                                            schemeName="Documento de Identidad" 
                                            schemeAgencyName="PE:SUNAT" 
                                            schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">'.$header->ruc_proveedor.'</cbc:ID>
                                    </cac:PartyIdentification>
                                    <cac:PartyLegalEntity>
                                        <cbc:RegistrationName><![CDATA['.$header->empresa_transporte_razon.']]></cbc:RegistrationName>
                                        <cbc:CompanyID>'.$header->registro_mtc.'</cbc:CompanyID>
                                    </cac:PartyLegalEntity>
                                </cac:CarrierParty>
                            </cac:ShipmentStage>
                            <cac:Delivery>
                                <cac:DeliveryAddress>
                                    <cbc:ID schemeName="Ubigeos" schemeAgencyName="PE:INEI">'.$header->ubig_destino.'</cbc:ID>
                                    <cbc:AddressTypeCode listID="'.$header->destinatario_ruc.'" listAgencyName="PE:SUNAT" listName="Establecimientos anexos">'.$header->csd.'</cbc:AddressTypeCode>
                                    <cac:AddressLine>
                                        <cbc:Line>'.utf8_encode($header->almacen_destino_direccion).'</cbc:Line>
                                    </cac:AddressLine>
                                </cac:DeliveryAddress>
                                <cac:Despatch>
                                    <cac:DespatchAddress>
                                        <cbc:ID schemeName="Ubigeos" schemeAgencyName="PE:INEI">'.$header->ubig_origen.'</cbc:ID>
                                        <cbc:AddressTypeCode listID="'.$header->destinatario_ruc.'" listAgencyName="PE:SUNAT" listName="Establecimientos anexos">'.$header->cso.'</cbc:AddressTypeCode>
                                        <cac:AddressLine>
                                            <cbc:Line>'.utf8_encode($header->almacen_origen_direccion).'</cbc:Line>
                                        </cac:AddressLine>
                                    </cac:DespatchAddress>
                                </cac:Despatch>
                            </cac:Delivery>
                        </cac:Shipment>';
            
            $i = 1;                        
            foreach($detalles as $detalle){
                $xml .=  '<cac:DespatchLine>
                            <cbc:ID>'.$i.'</cbc:ID>
                            <cbc:DeliveredQuantity unitCode="BJ" unitCodeListID="UN/ECE rec 20" unitCodeListAgencyName="United Nations Economic Commission for Europe">'.$detalle->cantidad.'</cbc:DeliveredQuantity>
                            <cac:OrderLineReference>
                                <cbc:LineID>1</cbc:LineID>
                            </cac:OrderLineReference>
                            <cac:Item>
                                <cbc:Description>'.$detalle->descripcion.'</cbc:Description>
                                    <cac:SellersItemIdentification>
                                    <cbc:ID>'.$detalle->codigo.'</cbc:ID>
                                    </cac:SellersItemIdentification>
                            </cac:Item>
                        </cac:DespatchLine>';                        
                $i++;                    
            }            
            
            $xml.=  '</DespatchAdvice>';

            return $xml;
        }

        private function desarrollo_xml_externos($header,$detalles){
            $xml =  '<?xml version="1.0" encoding="UTF-8"?>
                    <DespatchAdvice xmlns="urn:oasis:names:specification:ubl:schema:xsd:DespatchAdvice-2"
                        xmlns:ds="http://www.w3.org/2000/09/xmldsig#" 
                        xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" 
                        xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2" 
                        xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2">                    
                    <ext:UBLExtensions>
                        <ext:UBLExtension>
                            <ext:ExtensionContent></ext:ExtensionContent>
                        </ext:UBLExtension>
                    </ext:UBLExtensions>
                    <cbc:UBLVersionID>2.1</cbc:UBLVersionID>
                    <cbc:CustomizationID>2.0</cbc:CustomizationID>
                    <cbc:ID>'.$header->serie_guia.'-'.$header->numero_guia.'</cbc:ID>
                    <cbc:IssueDate>'.$header->fgemision.'</cbc:IssueDate>
                    <cbc:IssueTime>'.date("H:i:s").'</cbc:IssueTime>
                    <cbc:DespatchAdviceTypeCode>09</cbc:DespatchAdviceTypeCode>
                    <cac:Signature>
                      <cbc:ID>'.$header->destinatario_ruc.'</cbc:ID>
                      <cac:SignatoryParty>
                        <cac:PartyIdentification>
                          <cbc:ID>'.$header->destinatario_ruc.'</cbc:ID>
                        </cac:PartyIdentification>
                      </cac:SignatoryParty>
                      <cac:DigitalSignatureAttachment>
                        <cac:ExternalReference>
                          <cbc:URI>'.$header->destinatario_ruc.'</cbc:URI>
                        </cac:ExternalReference>
                      </cac:DigitalSignatureAttachment>
                    </cac:Signature>';
            $xml .= '<cac:DespatchSupplierParty>
                        <cac:Party>
                            <cac:PartyIdentification>
                                <cbc:ID schemeID="6" schemeName="Documento de Identidad" schemeAgencyName="PE:SUNAT" schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">'.$header->destinatario_ruc.'</cbc:ID>
                            </cac:PartyIdentification>
                            <cac:PartyName>
                                <cbc:Name><![CDATA['.$header->destinatario_razon.']]></cbc:Name>
                            </cac:PartyName>
                            <cac:PartyLegalEntity>
                                <cbc:RegistrationName><![CDATA['.$header->destinatario_razon.']]></cbc:RegistrationName>
                            </cac:PartyLegalEntity>
                        </cac:Party>
                    </cac:DespatchSupplierParty>';
                    
         $xml .=    '<cac:DeliveryCustomerParty>
                        <cac:Party>
                            <cac:PartyIdentification>
                                <cbc:ID schemeID="6" schemeName="Documento de Identidad" schemeAgencyName="PE:SUNAT" schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">'.$header->numero_guia.'</cbc:ID>
                            </cac:PartyIdentification>
                            <cac:PartyLegalEntity>
                                <cbc:RegistrationName><![CDATA['.$guia['entidad'].']]></cbc:RegistrationName>
                            </cac:PartyLegalEntity>
                        </cac:Party>
                    </cac:DeliveryCustomerParty>';
                    
            $xml .= '<cac:Shipment>
                        <cbc:ID>SUNAT_Envio</cbc:ID>
                        <cbc:HandlingCode listAgencyName="PE:SUNAT" listName="Motivo de traslado" listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo20">01</cbc:HandlingCode>
                        <cbc:GrossWeightMeasure unitCode="KGM">'.$guia['peso_total'].'</cbc:GrossWeightMeasure>';
                        
                        if($guia['guia_motivo_traslado_id'] == 7){//importaciones
                $xml .= '<cbc:TotalTransportHandlingUnitQuantity>'.$guia['numero_bultos'].'</cbc:TotalTransportHandlingUnitQuantity>';
                        }
                        
                $xml .= '<cac:ShipmentStage>
                            <cbc:ID>1</cbc:ID>
                            <cbc:TransportModeCode listAgencyName="PE:SUNAT" listName="Modalidad de traslado" listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo18">0'.$guia['guia_modalidad_traslado_id'].'</cbc:TransportModeCode>
                            <cac:TransitPeriod>
                                <cbc:StartDate>'.$guia['fecha_traslado_sf'].'</cbc:StartDate>
                            </cac:TransitPeriod>';
                
                if($guia['guia_modalidad_traslado_id'] == '1'){
                $xml .= '<cac:CarrierParty>
                                <cac:PartyIdentification>
                                    <cbc:ID schemeID="6" schemeName="Documento de Identidad" schemeAgencyName="PE:SUNAT" schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">'.$guia['numero_documento_transporte'].'</cbc:ID>
                                </cac:PartyIdentification>
                                <cac:PartyLegalEntity>
                                    <cbc:RegistrationName><![CDATA['.$guia['entidad_transporte'].']]></cbc:RegistrationName>';
                                    if($guia['numero_mtc_transporte'] != ''){
                $xml .=                 '<cbc:CompanyID>'.$guia['numero_mtc_transporte'].'</cbc:CompanyID>';
                                    }
                $xml .=         '</cac:PartyLegalEntity>
                            </cac:CarrierParty>';
                }
                if($guia['guia_modalidad_traslado_id'] == '2'){
                $xml .= '<cac:DriverPerson>
                                <cbc:ID schemeID="1" schemeName="Documento de Identidad" schemeAgencyName="PE:SUNAT" schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">'.$guia['conductor_dni'].'</cbc:ID>
                                <cbc:FirstName>'.$guia['conductor_nombres'].'</cbc:FirstName>
                                <cbc:FamilyName>'.$guia['conductor_apellidos'].'</cbc:FamilyName>
                                <cbc:JobTitle>Principal</cbc:JobTitle>
                                <cac:IdentityDocumentReference>
                                    <cbc:ID>'.$guia['conductor_licencia'].'</cbc:ID>
                                </cac:IdentityDocumentReference>
                            </cac:DriverPerson>';                                                                        
                }

                $xml .= '</cac:ShipmentStage>
                        <cac:Delivery>
                            <cac:DeliveryAddress>
                                <cbc:ID schemeName="Ubigeos" schemeAgencyName="PE:INEI">'.$guia['llegada_ubigeo'].'</cbc:ID>
                                <cac:AddressLine>
                                    <cbc:Line>'.$guia['llegada_direccion'].'</cbc:Line>
                                </cac:AddressLine>
                            </cac:DeliveryAddress>
                            <cac:Despatch>
                                <cac:DespatchAddress>
                                    <cbc:ID schemeName="Ubigeos" schemeAgencyName="PE:INEI">'.$guia['partida_ubigeo'].'</cbc:ID>
                                    <cac:AddressLine>
                                        <cbc:Line>'.$guia['partida_direccion'].'</cbc:Line>
                                    </cac:AddressLine>
                                </cac:DespatchAddress>
                            </cac:Despatch>
                        </cac:Delivery>';
                        
                        if($guia['guia_modalidad_traslado_id'] == '2'){
                $xml .= '<cac:TransportHandlingUnit>
                            <cac:TransportEquipment>
                                <cbc:ID>'.$guia['vehiculo_placa'].'</cbc:ID>
                            </cac:TransportEquipment>
                        </cac:TransportHandlingUnit>';
                        }
                $xml .= '</cac:Shipment>';        
                    
                    $i = 1;                        
                    foreach($detalles as $values){                    
                    $xml .=  '<cac:DespatchLine>
                        <cbc:ID>'.$i.'</cbc:ID>
                        <cbc:DeliveredQuantity unitCode="'.$values['codigo_unidad'].'">'.$values['cantidad'].'</cbc:DeliveredQuantity>
                        <cac:OrderLineReference>
                            <cbc:LineID>1</cbc:LineID>
                        </cac:OrderLineReference>
                        <cac:Item>
                            <cbc:Description>'.$values['producto'].'</cbc:Description>
                            <cac:SellersItemIdentification>
                            <cbc:ID>'.$values['producto_codigo'].'</cbc:ID>
                            </cac:SellersItemIdentification>
                        </cac:Item>
                    </cac:DespatchLine>';                        
                    $i++;                    
                    }
            $xml.=  '</DespatchAdvice>';
        }
    }
?>