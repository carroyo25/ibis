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
                $cc = '%';

                $sql = $this->db->connect()->prepare("SELECT
                                                        lg.cnumguia,
                                                        ad.ncodpry,
                                                        p.ccodproy,
                                                        UPPER(p.cdesproy) AS cdesproy,
                                                        lg.id_regalm,
                                                        DATE_FORMAT(ad.ffecdoc, '%d/%m/%Y') AS ffecdoc  
                                                    FROM
                                                        lg_guias lg
                                                        INNER JOIN alm_despachocab ad ON lg.id_regalm = ad.id_regalm
                                                        INNER JOIN tb_proyectos p ON ad.ncodpry = p.nidreg 
                                                    WHERE
                                                        lg.cnumguia <> ''
                                                        AND ad.nflgactivo = 1 
                                                        AND lg.cnumguia LIKE :guia 
                                                        AND ad.ncodpry LIKE :costos 
                                                        AND lg.flgmadre = 0
                                                        AND ad.ffecdoc >= DATE_SUB(CURRENT_DATE, INTERVAL 1 YEAR)
                                                    ORDER BY 
                                                        ad.ffecdoc DESC
                                                    LIMIT 100");
                
                $sql->execute(["guia"=>$g,"costos"=>$cc]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()) {
                        $salida .='<tr data-despacho="'.$rs['id_regalm'].'" data-guia="'.$rs['cnumguia'].'">
                                        <td class="textoCentro">'.$rs['cnumguia'].'</td>
                                        <td class="textoCentro">'.$rs['ffecdoc'].'</td>
                                        <td class="pl10px">'.$rs['cdesproy'].'</td>
                                        <td class="textoCentro"></td>
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
                                                        alm_despachodet.ndespacho,
                                                        tb_unimed.cabrevia,
                                                        alm_despachodet.id_regalm,
                                                        alm_despachodet.niddeta,
                                                        alm_despachodet.niddetaOrd,
                                                        alm_despachodet.niddetaPed,
                                                        alm_despachodet.nroorden,
                                                        alm_despachodet.nropedido,
                                                        alm_despachocab.cnumguia,
                                                        cm_producto.id_cprod  
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

                $item = 1;

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()) {
                        $salida .='<tr data-despacho="'.$rs['id_regalm'].'" 
                                        data-itemdespacho="'.$rs['niddeta'].'" 
                                        data-idprod="'.$rs['id_cprod'].'"
                                        data-orden="'.$rs['nropedido'].'"
                                        data-pedido="'.$rs['nroorden'].'"
                                        data-itempedido="'.$rs['niddetaPed'].'">
                                        <td class="textoCentro">'.str_pad($item++,3,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.$rs['cccodprod'].'</td>
                                        <td class="pl10px">'.$rs['cdesprod'].'</td>
                                        <td class="textoCentro">'.$rs['cabrevia'].'</td>
                                        <td class="textoDerecha"><input type="number" value="'.$rs['ndespacho'].'"></td>
                                        <td class="textoDerecha">'.$rs['cnumguia'].'</td>
                                        <td class="textoDerecha"><input type="text" class="input-tabla" name="track1"></td>
                                        <td class="textoDerecha"><input type="text" class="input-tabla" name="track2"></td>
                                        <td class="textoCentro"><a href="#"><i class="fas fa-trash-alt"></i></a></td>
                                    </tr>';
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function grabarGuia($guia,$form,$detalles,$operacion){
            $mensaje = "error de creacion";
            $guiaAutomatica = "";

            try {
                if ( $operacion == 'n' ){
                    $guiaAutomatica = $this->numeroGuia();
                    $mensaje = "Se grabo la guia de remision";
                    
                    $this->grabarDatosCabeceraGuiaMadre($form,$detalles,$guiaAutomatica);
                    $this->grabarDatosGuia($guia,$form,$guiaAutomatica);

                }else if( $operacion == 'u' ){
                    $mensaje = "Se actualizo la guia de remision";
                }

                return array("mensaje"=>$mensaje,"guia"=>$guiaAutomatica);

            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function grabarDatosCabeceraGuiaMadre($formCab,$detalles,$guia){
            try {
                
                $fecha = explode("-",$formCab['fecha']);

                $sql = $this->db->connect()->prepare("INSERT INTO alm_madrescab SET ntipmov = :ntipmov,
                                                                                    nnromov = :nnromov,
                                                                                    cper = :cper,
                                                                                    cmes = :cmes,
                                                                                    ncodalm1 = :ncodalm1,
                                                                                    ncodalm2 = :ncodalm2,
                                                                                    ffecdoc = :ffecdoc,
                                                                                    ncodpry = :ncodpry,
                                                                                    nnronota=:nnronota,
                                                                                    id_userAprob = :id_userAprob,
                                                                                    id_userElabora = :id_user,
                                                                                    nEstadoDoc = :nEstadoDoc,
                                                                                    nflgactivo = :nflgactivo,
                                                                                    cnumguia =:guia,
                                                                                    ncodcos = :costos");

                $sql->execute(["ntipmov"=>$formCab['codigo_movimiento'],
                                "nnromov"=>null,
                                "cper"=>null,
                                "cmes"=>$fecha[1],
                                "ncodalm1"=>$formCab['codigo_almacen_origen'],
                                "ncodalm2"=>$formCab['codigo_almacen_destino'],
                                "ffecdoc"=>$formCab['fecha'],
                                "ncodpry"=>null,
                                "nnronota"=>null,
                                "id_userAprob"=>$formCab['codigo_aprueba'],
                                "nEstadoDoc"=>62,
                                "nflgactivo"=>1,
                                "id_user"=>$_SESSION['iduser'],
                                "guia"=>$guia,
                                "costos"=>$formCab['codigo_costos_origen']]);

                $rowCount = $sql->rowCount();
                
                if ($rowCount > 0) {
                    $indice = $this->lastInsertId("SELECT COUNT(id_regalm) AS id FROM alm_madrescab");
                    $this->grabarDetallesMadre($indice,$detalles,$formCab['codigo_almacen_origen'],$guia);
                }
                
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function grabarDatosGuia($guiaCab,$formCab,$nroguia){
            try {
                $sql = $this->db->connect()->prepare("INSERT INTO lg_guias SET id_regalm=:despacho,cnumguia=:guia,corigen=:origen,
                                                                                cdirorigen=:direccion_origen,cdestino=:destino,
                                                                                cdirdest=:direccion_destino,centi=:entidad,centidir=:direccion_entidad,
                                                                                centiruc=:ruc_entidad,ctraslado=:traslado,cenvio=:envio,
                                                                                cautoriza=:autoriza,cdestinatario=:destinatario,cobserva=:observaciones,
                                                                                cnombre=:nombres,cmarca=:marca,clicencia=:licencia,cplaca=:placa,
                                                                                ftraslado=:fecha_traslado,fguia=:fecha_guia,cserie=:serie,
                                                                                cmotivo=:tipo,nPeso=:peso,nBultos=:bultos,
                                                                                fechaEmbarca=:fembarca,nombreEmbarca=:nembarca");

                $sql->execute([ "despacho"=>null,
                                "guia"=>$nroguia,
                                "origen"=>$guiaCab['almacen_origen'],
                                "direccion_origen"=>$guiaCab['almacen_origen_direccion'],
                                "destino"=>$guiaCab['almacen_destino'],
                                "direccion_destino"=>$guiaCab['almacen_destino_direccion'],
                                "entidad"=>$guiaCab['empresa_transporte_razon'],
                                "direccion_entidad"=>$guiaCab['direccion_proveedor'],
                                "ruc_entidad"=>$guiaCab['ruc_proveedor'],
                                "traslado"=>$guiaCab['modalidad_traslado'],
                                "envio"=>$guiaCab['tipo_envio'],
                                "autoriza"=>null,
                                "destinatario"=>null,
                                "observaciones"=>$guiaCab['observaciones'],
                                "nombres"=>$guiaCab['nombre_conductor'],
                                "marca"=>$guiaCab['marca'],
                                "licencia"=>$guiaCab['licencia_conducir'],
                                "placa"=>$guiaCab['placa'],
                                "fecha_traslado"=>$guiaCab['ftraslado'],
                                "fecha_guia"=>$guiaCab['fgemision'],
                                "peso"=>$guiaCab['peso'],
                                "bultos"=>$guiaCab['bultos'],
                                "serie"=>'F001',
                                "tipo"=>248,
                                "fembarca"=>$guiaCab['fecha_embarque'],
                                "nembarca"=>$guiaCab['nombre_embarque']]);
                
                $rowCount = $sql->rowcount();

                if ($rowCount > 0) {
                    $mensaje = "Registro grabado";
                }else {
                    $mensaje = "Error al crear el registro";
                }

                return $mensaje;                
            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }

        private function grabarDetallesMadre($indice,$detalles,$almacen,$guia){
            try {
                $datos = json_decode($detalles);
                $nreg = count($datos);

                $sql=$this->db->connect()->prepare("INSERT INTO alm_madresdet SET id_regalm=:cod,
                                                                                            ncodalm1=:ori,
                                                                                            id_cprod=:cpro,
                                                                                            nflgactivo=:flag,
                                                                                            nestadoreg=:estadoItem,
                                                                                            cobserva=:observac,
                                                                                            nGuia=:guia,
                                                                                            nGuiaMadre=:guiaMadre,
                                                                                            ncantidad=:cantidad,
                                                                                            niddetaPed=:pedido,
                                                                                            niddetaOrd=:orden,
                                                                                            tracking=:pucallpa,
                                                                                            trackinglurin=:lurin,
                                                                                            nropedido=:idpedido");
                for ($i=0; $i < $nreg; $i++) { 
                    try {
                        
                         $sql->execute(["cod"=>$indice,
                                        "ori"=>$almacen,
                                        "cpro"=>$datos[$i]->idprod,
                                        "flag"=>1,
                                        "estadoItem"=>32,
                                        "observac"=>$datos[$i]->obser,
                                        "guia"=>$datos[$i]->guia,
                                        "guiaMadre"=>$guia,
                                        "cantidad"=>$datos[$i]->cantdesp,
                                        "pedido"=>$datos[$i]->pedido,
                                        "orden"=>$datos[$i]->orden,
                                        "pucallpa"=>$datos[$i]->pucallpa,
                                        "lurin"=>$datos[$i]->lurin,
                                        "idpedido"=>$datos[$i]->iddetped]);

                        $this->actualizarItemsPedido($datos[$i]->iddetped);

                    } catch (PDOException $th) {
                        echo $th->getMessage();
                        return false;
                    }
                }

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function generarNumeroSunat(){
            try {
                $sql = $this->db->connect()->query("SELECT MAX(cnroguia) AS numero FROM lg_guiamadre");
                $sql->execute();

                $result = $sql->fetchAll();
                
                return $result[0]['numero'] + 1;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function listarGuiasMadre(){
            try {

                $salida = "";
                $sql = $this->db->connect()->prepare("SELECT
                                                        am.id_regalm,
                                                        am.cnumguia,
                                                        am.ncodcos,
                                                        DATE_FORMAT(am.ffecdoc, '%d/%m/%Y') AS emitido,
                                                        DATE_FORMAT(lg.ftraslado, '%d/%m/%Y') AS traslado,
                                                        lg.ticketsunat,
                                                        lg.guiasunat,
                                                        lg.estadoSunat,
                                                        lg.corigen, 
                                                        lg.cdestino  
                                                    FROM
                                                        alm_madrescab am
                                                        LEFT JOIN lg_guias lg ON am.cnumguia = lg.cnumguia 
                                                    WHERE
                                                        am.nflgactivo = 1
                                                        AND am.ffecdoc >= DATE_FORMAT(DATE_SUB(CURRENT_DATE, INTERVAL 1 MONTH), '%Y-%m-01')
                                                        AND am.ffecdoc < DATE_FORMAT(DATE_ADD(CURRENT_DATE, INTERVAL 1 MONTH), '%Y-%m-01')
                                                    ORDER BY 
                                                        am.ffecdoc DESC");

                $sql->execute();
                $item = 1;

                if( $sql->rowCount() > 0 ){
                    while($rs = $sql->fetch()){

                        $icono = null;
                        $color = null;

                        if ( $rs['estadoSunat'] === 0 ) {
                            $icono = '<i class="far fa-check-circle"></i>';
                            $color = 'green';
                        }else if ($rs['estadoSunat'] === 98){
                            $icono = '<i class="far fa-clock"></i>';
                            $color = 'gold';
                        }else if ($rs['estadoSunat'] === 99) {
                            $icono = '<i class="fas fa-wrench"></i>';
                            $color = 'red';
                        }

                        $salida .= '<tr class="pointer" data-indice="'.$rs['id_regalm'].'" data-guiasunat="'.$rs['guiasunat'].'">
                                        <td class="textoCentro">'.str_pad($item++,4,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.$rs['emitido'].'</td>
                                        <td class="textoCentro">'.$rs['traslado'].'</td>
                                        <td class="pl20px">'.$rs['corigen'].'</td>
                                        <td class="pl20px">'.$rs['cdestino'].'</td>
                                        <td class="textoCentro">'.$rs['cnumguia'].'</td>
                                        <td class="textoCentro">'.$rs['guiasunat'].'</td>
                                        <td class="textoCentro" style="color:'.$color.';font-weight: bolder;font-size: 1rem;vertical-align: middle;">'.$icono.'</td>
                                    </tr>';
                    }
                }

                return $salida;

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function contarItems(){
            try {
                $sql = $this->db->connect()->query("SELECT COUNT(*) AS regs FROM alm_madrescab WHERE nflgActivo = 1");
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
                                                        alm_madrescab.id_regalm,
                                                        alm_madrescab.cnumguia,
                                                        tb_user.iduser,
                                                        alm_madrescab.ffecdoc AS emision,
                                                        lg_guias.ftraslado AS traslado,
                                                        origen.cnumdoc AS origen_ruc,
                                                        UPPER(origen.crazonsoc) AS origen,
                                                        UPPER(destino.crazonsoc) AS destino,
                                                        destino.cnumdoc AS destino_ruc,
                                                        UPPER(destino.cviadireccion) AS destino_direccion,
                                                        UPPER(origen.cviadireccion) AS origen_direccion,
                                                        lg_guias.cdestinatario AS recibe,
                                                        lg_guias.corigen,
                                                        lg_guias.cdirorigen,
                                                        lg_guias.cdestino,
                                                        lg_guias.cdirdest,
                                                        lg_guias.cmarca,
                                                        lg_guias.cplaca,
                                                        lg_guias.clicencia,
                                                        lg_guias.nPeso,
                                                        lg_guias.nBultos,
                                                        lg_guias.cenvio,
                                                        lg_guias.cobserva,
                                                        lg_guias.ticketsunat,
                                                        lg_guias.guiasunat,
                                                        lg_guias.estadoSunat,
                                                        lg_guias.centi AS nombre_proveedor,
                                                        lg_guias.centiruc AS ruc_proveedor,
                                                        lg_guias.centidir AS direccion_proveedor,
                                                        CONCAT_WS(' ',tb_proyectos.ccodproy,tb_proyectos.cdesproy ) AS proyecto,
                                                        tb_user.cnombres AS autoriza
                                                    FROM
                                                        alm_madrescab
                                                        INNER JOIN tb_user ON alm_madrescab.id_userAprob = tb_user.iduser
                                                        INNER JOIN lg_guias ON alm_madrescab.cnumguia = lg_guias.cnumguia
                                                        INNER JOIN cm_entidad AS origen ON alm_madrescab.ncodalm1 = origen.id_centi
                                                        INNER JOIN cm_entidad AS destino ON alm_madrescab.ncodalm2 = destino.id_centi
                                                        INNER JOIN tb_proyectos ON alm_madrescab.ncodcos = tb_proyectos.nidreg
                                                    WHERE alm_madrescab.id_regalm =:id");

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
                                                        alm_madresdet.niddeta,
                                                        alm_madresdet.id_cprod,
                                                        alm_madresdet.ncantidad,
                                                        cm_producto.ccodprod,
                                                        UPPER( cm_producto.cdesprod ) AS producto,
                                                        alm_madresdet.niddetaPed,
                                                        alm_madresdet.niddetaOrd,
                                                        alm_madresdet.nroorden,
                                                        alm_madresdet.nropedido,
                                                        alm_madresdet.nGuia,
                                                        tb_unimed.cabrevia,
                                                        tb_pedidocab.nrodoc,
	                                                    lg_ordencab.cnumero,
                                                        alm_madresdet.tracking,
                                                        alm_madresdet.trackinglurin
                                                        FROM
                                                            alm_madresdet
                                                            LEFT JOIN cm_producto ON alm_madresdet.id_cprod = cm_producto.id_cprod
                                                            LEFT JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed
                                                            LEFT JOIN tb_pedidodet ON alm_madresdet.niddetaOrd = tb_pedidodet.iditem
                                                            LEFT JOIN tb_pedidocab ON tb_pedidodet.idpedido = tb_pedidocab.idreg
                                                            LEFT JOIN lg_ordendet ON alm_madresdet.niddetaPed = lg_ordendet.nitemord
                                                            LEFT JOIN lg_ordencab ON lg_ordendet.id_regmov = lg_ordencab.id_regmov 
                                                        WHERE
                                                        alm_madresdet.id_regalm = :id");
                $sql->execute(["id"=>$id]);

                $rowCount = $sql->rowCount();
                $item = 1;

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()) {
                        $salida.= '<tr data-orden="'.$rs['niddetaOrd'].'" data-pedido="'.$rs['niddetaPed'].'">
                                    <td class="textoCentro">'.str_pad($item++,3,0,STR_PAD_LEFT).'</td>
                                    <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                    <td class="pl20px">'.$rs['producto'].'</td>
                                    <td class="textoCentro">'.$rs['cabrevia'].'</td>
                                    <td><input type="num" value="'.$rs['ncantidad'].'" class="textoDerecha" readonly></td>
                                    <td class="textoCentro">'.$rs['nGuia'].'</td>
                                    <td class="textoCentro">'.$rs['tracking'].'</td>
                                    <td class="textoCentro">'.$rs['trackinglurin'].'</td>
                                    <td></td>
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

            $header = json_decode($datos['datosGuia']);
            $body = json_decode($datos['detalles']);
            $formulario = json_decode($datos['datosFormulario']);

            $empresa = $header->destinatario_razon;
            $guia    = $header->numero_guia;

            $path = "public/documentos/guia_electronica/";

            $nombre_archivo = $header->destinatario_ruc.'-09-'.$header->serie_guia.'-'.$header->numero_guia;

            if(file_exists($path."XML/".$nombre_archivo.".xml")){
                unlink($path."XML/".$nombre_archivo.".xml");  
            }

            $token_access = $this->token('d12d8bf5-4b57-4c57-9569-9072b3e1bfcd', 'iLMGwQBEehJMXQ+Z/LR2KA==', '20504898173SISTEMA1', 'Lima123');
            $firma = $this->crear_files($header->codigo_modalidad,$path, $nombre_archivo, $header, $body);
            $respuesta = $this->envio_xml($path.'FIRMA/', $nombre_archivo, $token_access);
            $numero_ticket = $respuesta->numTicket;

            var_dump($respuesta);

            sleep(2);//damos tiempo para que SUNAT procese y responda.
            $respuesta_ticket = $this->envio_ticket($path.'CDR/', $numero_ticket, $token_access, $header->destinatario_ruc, $nombre_archivo);

            ($respuesta_ticket);
        
            return array("archivo"=>$nombre_archivo,"token"=>$token_access);
        }

        private function token($client_id, $client_secret, $usuario_secundario, $usuario_password){
            $url = "https://api-seguridad.sunat.gob.pe/v1/clientessol/".$client_id."/oauth2/token/";

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

        private function crear_files($movimiento,$path,$nombre_archivo,$header,$body){
            if ( $movimiento == 108 ) {
                //$xml = $this->desarrollo_xml_externos($header,$body);
                $xml = null;
            }else if ($movimiento == 107 ){
                $xml = $this->desarrollo_xml_almacenes_internos($header,$body);
            }else{
                $tipo_envio_sunat = 'Error en el tipo de envio';
            }

            $archivo = fopen($path."XML/".$nombre_archivo.".xml", "w+");
            fwrite($archivo, utf8_decode($xml));
            fclose($archivo);

            $this->firmar_xml($nombre_archivo.".xml", "1");

            $zip = new ZipArchive();
            if($zip->open($path."FIRMA/".$nombre_archivo.".zip", ZipArchive::CREATE) === true){
                $zip->addFile($path."FIRMA/".$nombre_archivo.".xml", $nombre_archivo.".xml");
            }

            return $nombre_archivo;
        }
        
        private function firmar_xml($name_file, $entorno, $baja = ''){        
            $xmlstr = file_get_contents("public/documentos/guia_electronica/XML/".$name_file);
        
            $domDocument = new \DOMDocument();
            $domDocument->loadXML($xmlstr);
            $factura  = new Factura();
            $xml = $factura->firmar($domDocument, '', $entorno);
            $content = $xml->saveXML();
            file_put_contents("public/documentos/guia_electronica/FIRMA/".$name_file, $content);
        }

        private function envio_ticket($ruta_archivo_cdr, $ticket, $token_access, $ruc, $nombre_file){
            if(($ticket == "") || ($ticket == null)){
                $mensaje['cdr_hash'] = '';
                $mensaje['cdr_msj_sunat'] = 'Ticket vacio';
                $mensaje['cdr_ResponseCode']  = null;
                $mensaje['numerror'] = null;
            }else{
            
                $mensaje['ticket'] = $ticket;
                $curl = curl_init();
        
                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://api-cpe.sunat.gob.pe/v1/contribuyente/gem/comprobantes/envios/'.$ticket,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_HTTPHEADER => array(
                        'numRucEnvia: '.$ruc,
                        'numTicket: '.$ticket,
                        'Authorization: Bearer '. $token_access,
                    ),
                ));
        
                $response_1  = curl_exec($curl);
                $response3  = json_decode($response_1);
                $codRespuesta = $response3->codRespuesta;
                curl_close($curl); 

                var_dump($response3);
                
                //exit;

                $mensaje['ticket_rpta'] = $codRespuesta;

                if($codRespuesta == '99'){
                    $error = $response3->error;
                    $mensaje['cdr_hash'] = '';
                    $mensaje['cdr_msj_sunat'] = $error->desError;
                    $mensaje['cdr_ResponseCode'] = '99';
                    $mensaje['numerror'] = $error->numError;            	            
                }else if($codRespuesta == '98'){
                    $mensaje['cdr_hash'] = '';
                    $mensaje['cdr_msj_sunat'] = 'Envío en proceso';
                    $mensaje['cdr_ResponseCode']  = '98';
                    $mensaje['numerror'] = '98';                        
                }else if($codRespuesta == '0'){
                    $mensaje['arcCdr'] = $response3->arcCdr;
                    $mensaje['indCdrGenerado'] = $response3->indCdrGenerado;
                    
                    file_put_contents($ruta_archivo_cdr . 'R-' . $nombre_file . '.ZIP', base64_decode($response3->arcCdr));
        
                    $zip = new ZipArchive;
                    if ($zip->open($ruta_archivo_cdr . 'R-' . $nombre_file . '.ZIP') === TRUE) {
                        $zip->extractTo($ruta_archivo_cdr);
                        $zip->close();
                    }
                    //unlink($ruta_archivo_cdr . 'R-' . $nombre_file . '.ZIP');
        
                 //=============hash CDR=================
                    $doc_cdr = new DOMDocument();
                    $doc_cdr->load($ruta_archivo_cdr . 'R-' . $nombre_file . '.xml');
                    
                    $mensaje['cdr_hash']            = $doc_cdr->getElementsByTagName('DigestValue')->item(0)->nodeValue;
                    $mensaje['cdr_msj_sunat']       = $doc_cdr->getElementsByTagName('Description')->item(0)->nodeValue;
                    $mensaje['cdr_ResponseCode']    = $doc_cdr->getElementsByTagName('ResponseCode')->item(0)->nodeValue;        
                    $mensaje['numerror']            = '';
                }else{
                    $mensaje['cdr_hash']            = '';
                    $mensaje['cdr_msj_sunat']       = 'SUNAT FUERA DE SERVICIO';
                    $mensaje['cdr_ResponseCode']    = '88';            
                    $mensaje['numerror']            = '88';
                }
            }
            return $mensaje;
        }

        private function envio_xml($path,$nombre_file,$token_access){
            $curl = curl_init();
            $data = array(
                        'nomArchivo'  =>  $nombre_file.".zip",
                        'arcGreZip'   =>  base64_encode(file_get_contents($path.$nombre_file.'.zip')),
                        'hashZip'     =>  hash_file("sha256", $path.$nombre_file.'.zip')
                    );
            curl_setopt_array($curl, array(
                        CURLOPT_URL => "https://api-cpe.sunat.gob.pe/v1/contribuyente/gem/comprobantes/".$nombre_file,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_POSTFIELDS =>json_encode(array('archivo' => $data)),
                        CURLOPT_HTTPHEADER => array(
                            'Authorization: Bearer '. $token_access,
                            'Content-Type: application/json'
                        ),
                    ));
                
            $response2 = curl_exec($curl);
            curl_close($curl);
            return json_decode($response2);

            $original_file =  $path."XML/".$nombre_file.'.xml';
            $destination_file = $path."FIRMA/".$nombre_file.'.zip';
            
            $zip = new ZipArchive();
            $zip->open($destination_file,ZipArchive::CREATE);
            $zip->addFile($original_file);
            $zip->close();
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
                                        <cbc:CompanyID>'.$header->mut_proveedor.'</cbc:CompanyID>
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
                                <cbc:Description>'.utf8_encode($detalle->descripcion).'</cbc:Description>
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
                                <cbc:RegistrationName><![CDATA['.utf8_encode($header->destinatario_razon).']]></cbc:RegistrationName>
                            </cac:PartyLegalEntity>
                        </cac:Party>
                    </cac:DespatchSupplierParty>';
                    
         $xml .=    '<cac:DeliveryCustomerParty>
                        <cac:Party>
                            <cac:PartyIdentification>
                                <cbc:ID schemeID="6" schemeName="Documento de Identidad" schemeAgencyName="PE:SUNAT" schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">20100039207</cbc:ID>
                            </cac:PartyIdentification>
                            <cac:PartyLegalEntity>
                                <cbc:RegistrationName><![CDATA[RANSA COMERCIAL S.A.]]></cbc:RegistrationName>
                            </cac:PartyLegalEntity>
                        </cac:Party>
                    </cac:DeliveryCustomerParty>';
                    
            $xml .= '<cac:Shipment>
                        <cbc:ID>SUNAT_Envio</cbc:ID>
                        <cbc:HandlingCode listAgencyName="PE:SUNAT" listName="Motivo de traslado" listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo20">17</cbc:HandlingCode>
                        <cbc:GrossWeightMeasure unitCode="KGM">'.$header->peso.'</cbc:GrossWeightMeasure>';
                        
                        /*if($guia['guia_motivo_traslado_id'] == 7){//importaciones
                            $xml .= '<cbc:TotalTransportHandlingUnitQuantity>'.$header->bultos.'</cbc:TotalTransportHandlingUnitQuantity>';
                        }*/
                        
            $xml .= '<cac:ShipmentStage>
                        <cbc:ID>1</cbc:ID>
                        <cbc:TransportModeCode listAgencyName="PE:SUNAT" listName="Modalidad de traslado" listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo18">01</cbc:TransportModeCode>
                        <cac:TransitPeriod>
                                <cbc:StartDate>'.$header->ftraslado.'</cbc:StartDate>
                        </cac:TransitPeriod>';
                
                if($header->tipoTrasladoSunat == '1'){
                $xml .= '<cac:CarrierParty>
                                <cac:PartyIdentification>
                                    <cbc:ID schemeID="6" schemeName="Documento de Identidad" schemeAgencyName="PE:SUNAT" schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">'.$header->ruc_proveedor.'</cbc:ID>
                                </cac:PartyIdentification>
                                <cac:PartyLegalEntity>
                                    <cbc:RegistrationName><![CDATA['.$header->empresa_transporte_razon.']]></cbc:RegistrationName>';
                                if($header->registro_mtc != ''){
                $xml .=                 '<cbc:CompanyID>'.$header->registro_mtc.'</cbc:CompanyID>';
                                }
                $xml .=         '</cac:PartyLegalEntity>
                            </cac:CarrierParty>';
                }
                if($header->tipoTrasladoSunat == '2'){
                $xml .= '<cac:DriverPerson>
                                <cbc:ID schemeID="1" schemeName="Documento de Identidad" schemeAgencyName="PE:SUNAT" schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">'.$header->conductor_dni.'</cbc:ID>
                                <cbc:FirstName>'.utf8_encode($header->nombre_conductor).'</cbc:FirstName>
                                <cbc:FamilyName>'.utf8_encode($header->nombre_conductor).'</cbc:FamilyName>
                                <cbc:JobTitle>Principal</cbc:JobTitle>
                                <cac:IdentityDocumentReference>
                                    <cbc:ID>'.$header->licencia_conducir.'</cbc:ID>
                                </cac:IdentityDocumentReference>
                            </cac:DriverPerson>';                                                                        
                }

                $xml .= '</cac:ShipmentStage>
                        <cac:Delivery>
                            <cac:DeliveryAddress>
                                <cbc:ID schemeName="Ubigeos" schemeAgencyName="PE:INEI">'.$header->ubig_origen.'</cbc:ID>
                                <cac:AddressLine>
                                    <cbc:Line>'.utf8_encode($header->almacen_origen_direccion).'</cbc:Line>
                                </cac:AddressLine>
                            </cac:DeliveryAddress>
                            <cac:Despatch>
                                <cac:DespatchAddress>
                                    <cbc:ID schemeName="Ubigeos" schemeAgencyName="PE:INEI">'.$header->ubig_destino.'</cbc:ID>
                                    <cac:AddressLine>
                                        <cbc:Line>'.utf8_encode($header->almacen_destino_direccion).'</cbc:Line>
                                    </cac:AddressLine>
                                </cac:DespatchAddress>
                            </cac:Despatch>
                        </cac:Delivery>';
                        
                        if($header->tipoTrasladoSunat == '2'){
                $xml .= '<cac:TransportHandlingUnit>
                            <cac:TransportEquipment>
                                <cbc:ID>'.$header->placa.'</cbc:ID>
                            </cac:TransportEquipment>
                        </cac:TransportHandlingUnit>';
                        }
                $xml .= '</cac:Shipment>';        
                    
                    $i = 1;                        
                    foreach($detalles as $detalle){                    
                        $xml .=  '<cac:DespatchLine>
                            <cbc:ID>'.$i.'</cbc:ID>
                            <cbc:DeliveredQuantity unitCode="'.$detalle->codigo.'">'.$detalle->cantdesp.'</cbc:DeliveredQuantity>
                            <cac:OrderLineReference>
                                <cbc:LineID>1</cbc:LineID>
                            </cac:OrderLineReference>
                            <cac:Item>
                                <cbc:Description>'.utf8_encode($detalle->descripcion).'</cbc:Description>
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

        private function desarrollo_xml_almacenes_internos($header,$detalles){
           $xml = '<?xml version="1.0" encoding="UTF-8"?>
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
                        <!-- FECHA Y HORA DE EMISION -->
                        <cbc:IssueDate>'.$header->fgemision.'</cbc:IssueDate>
                        <cbc:IssueTime>'.date("H:i:s").'</cbc:IssueTime>
                        <cbc:DespatchAdviceTypeCode listAgencyName="PE:SUNAT" listName="Tipo de Documento" listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo01">09</cbc:DespatchAdviceTypeCode>
                        <!-- DOCUMENTOS ADICIONALES (Catalogo 41)-->
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
                        <!-- DATOS DEL EMISOR (REMITENTE) -->
                        <cac:DespatchSupplierParty>
                            <cac:Party>
                                <cac:PartyIdentification>
                                    <cbc:ID schemeID="6" schemeName="Documento de Identidad" schemeAgencyName="PE:SUNAT" schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">'.$header->destinatario_ruc.'</cbc:ID>
                                </cac:PartyIdentification>
                                <cac:PartyLegalEntity>
                                    <cbc:RegistrationName><![CDATA['.$header->destinatario_razon.']]></cbc:RegistrationName>
                                </cac:PartyLegalEntity>
                            </cac:Party>
                        </cac:DespatchSupplierParty>
                        <!-- DATOS DEL RECEPTOR (DESTINATARIO) -->
                        <cac:DeliveryCustomerParty>
                            <cac:Party>
                                <cac:PartyIdentification>
                                    <cbc:ID schemeID="6" schemeName="Documento de Identidad" schemeAgencyName="PE:SUNAT" schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">'.$header->destinatario_ruc.'</cbc:ID>
                                </cac:PartyIdentification>
                                <cac:PartyLegalEntity>
                                    <cbc:RegistrationName><![CDATA['.$header->destinatario_razon.']]></cbc:RegistrationName>
                                </cac:PartyLegalEntity>
                            </cac:Party>
                        </cac:DeliveryCustomerParty>
                        <!-- DATOS DEL PROVEEDOR -->
                        <!-- DATOS DEL TRASLADO -->
                        <cac:Shipment>
                            <!-- ID OBLIGATORIO POR UBL -->
                            <cbc:ID>SUNAT_Envio</cbc:ID>
                            <!-- MOTIVO DEL TRASLADO -->
                            <cbc:HandlingCode listAgencyName="PE:SUNAT" listName="Motivo de traslado" listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogoD05">04</cbc:HandlingCode>
                            <cbc:HandlingInstructions>Traslado entre establecimientos de la misma empresa</cbc:HandlingInstructions>
                            <!-- PESO BRUTO TOTAL DE LA CARGA-->
                            <cbc:GrossWeightMeasure unitCode="KGM">'.$header->peso.'</cbc:GrossWeightMeasure>
                            <!-- INDICADORES -->
                            <cac:ShipmentStage>
                                <!-- MODALIDAD DE TRASLADO  -->
                                <cbc:TransportModeCode listName="Modalidad de traslado" listAgencyName="PE:SUNAT" listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo18">02</cbc:TransportModeCode>
                                <!--FECHA DE INICIO DEL TRASLADO o FECHA DE ENTREGA DE BIENES AL TRANSPORTISTA -->
                                <cac:TransitPeriod> <cbc:StartDate>'.$header->ftraslado.'</cbc:StartDate></cac:TransitPeriod>
                                <!-- CONDUCTOR PRINCIPAL -->
                                <cac:DriverPerson>
                                    <!-- TIPO Y NUMERO DE DOCUMENTO DE IDENTIDAD -->
                                    <cbc:ID schemeID="1" schemeName="Documento de Identidad" schemeAgencyName="PE:SUNAT" schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">'.$header->conductor_dni.'</cbc:ID>
                                    <!-- NOMBRES -->
                                    <cbc:FirstName>'.$header->nombre_conductor.'</cbc:FirstName>
                                    <!-- APELLIDOS -->
                                    <cbc:FamilyName>'.$header->apellido_conductor.'</cbc:FamilyName>
                                    <!-- TIPO DE CONDUCTOR: PRINCIPAL -->
                                    <cbc:JobTitle>Principal</cbc:JobTitle>
                                    <cac:IdentityDocumentReference>
                                        <!-- LICENCIA DE CONDUCIR -->
                                        <cbc:ID>'.$header->licencia_conducir.'</cbc:ID>
                                    </cac:IdentityDocumentReference>
                                </cac:DriverPerson>
                            </cac:ShipmentStage>
                            <cac:Delivery>
                                <cac:DeliveryAddress>
                                    <!-- UBIGEO DE LLEGADA -->
                                    <cbc:ID schemeName="Ubigeos" schemeAgencyName="PE:INEI">'.$header->ubig_destino.'</cbc:ID>
                                    <!-- CODIGO DE ESTABLECIMIENTO ANEXO DE LLEGADA -->
                                    <cbc:AddressTypeCode listID="'.$header->destinatario_ruc.'" listAgencyName="PE:SUNAT" listName="Establecimientos anexos">'.$header->csd.'</cbc:AddressTypeCode>
                                    <!-- DIRECCION COMPLETA Y DETALLADA DE LLEGADA -->
                                    <cac:AddressLine><cbc:Line>'.utf8_encode($header->almacen_destino_direccion).'</cbc:Line></cac:AddressLine>
                                </cac:DeliveryAddress>
                                <cac:Despatch>
                                    <!-- DIRECCION DEL PUNTO DE PARTIDA -->
                                    <cac:DespatchAddress>
                                        <!-- UBIGEO DE PARTIDA -->
                                        <cbc:ID schemeName="Ubigeos" schemeAgencyName="PE:INEI">'.$header->ubig_origen.'</cbc:ID>
                                        <!-- CODIGO DE ESTABLECIMIENTO ANEXO DE PARTIDA -->
                                        <cbc:AddressTypeCode listID="'.$header->destinatario_ruc.'" listAgencyName="PE:SUNAT" listName="Establecimientos anexos">'.$header->cso.'</cbc:AddressTypeCode>
                                        <!-- DIRECCION COMPLETA Y DETALLADA DE PARTIDA -->
                                        <cac:AddressLine><cbc:Line>'.utf8_encode($header->almacen_origen_direccion).'</cbc:Line></cac:AddressLine>
                                    </cac:DespatchAddress>
                                    <!-- DATOS DEL REMITENTE -->
                                    <cac:DespatchParty>
                                        <!-- AUTORIZACIONES ESPECIALES DEL REMITENTE-->
                                        <cac:AgentParty>
                                            <!-- AUTORIZACION -->
                                            <cac:PartyLegalEntity>
                                            <cbc:CompanyID schemeID="06" schemeName="Entidad Autorizadora" schemeAgencyName="PE:SUNAT" schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:anexoD-37">'.$header->mut_proveedor.'</cbc:CompanyID>
                                            </cac:PartyLegalEntity>
                                        </cac:AgentParty>
                                    </cac:DespatchParty>
                                </cac:Despatch>
                            </cac:Delivery>
                            <cac:TransportHandlingUnit>
                                <cac:TransportEquipment>
                                    <!-- VEHICULO PRINCIPAL -->
                                    <!-- PLACA - VEHICULO PRINCIPAL -->
                                    <cbc:ID>C5A435</cbc:ID>
                                    <!-- AUTORIZACIONES ESPECIALES - VEHICULO PRINCIPAL -->
                                    <cac:ShipmentDocumentReference>
                                        <cbc:ID schemeID="06" schemeName="Entidad Autorizadora" schemeAgencyName="PE:SUNAT" schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogoD37">'.$header->mut_proveedor.'</cbc:ID>
                                    </cac:ShipmentDocumentReference>
                                    </cac:TransportEquipment>
                            </cac:TransportHandlingUnit>
                            <!-- PUERTO O AEROPUERTO DE EMBARQUE / DESEMBARQUE -->
                        </cac:Shipment>';
            $i = 1;                        
            foreach($detalles as $detalle){
                $xml .=  '<cac:DespatchLine>
                            <!-- NUMERO DE ORDEN DEL ITEM -->
                            <cbc:ID>'.$i.'</cbc:ID>
                            <!-- CANTIDAD -->
                            <cbc:DeliveredQuantity unitCode="BJ" unitCodeListID="UN/ECE rec 20" unitCodeListAgencyName="United Nations Economic Commission for Europe">'.$detalle->cantidad.'</cbc:DeliveredQuantity>
                            <cac:OrderLineReference><cbc:LineID>1</cbc:LineID></cac:OrderLineReference>
                            <cac:Item>
                                <!-- DESCRIPCION -->
                                <cbc:Description>'.utf8_encode($detalle->descripcion).'</cbc:Description>
                                <!-- CODIGO DEL ITEM -->
                                <cac:SellersItemIdentification>
                                <!-- CODIGO GTIN -->
                                <!--INDICADOR DE BIEN REGULADO POR SUNAT -->
                                <cbc:ID>'.$detalle->codigo.'</cbc:ID></cac:SellersItemIdentification>
                                <cac:AdditionalItemProperty>
                                    <cbc:Name>Indicador de bien regulado por SUNAT</cbc:Name>
                                    <cbc:NameCode listAgencyName="PE:SUNAT" listName="Propiedad del Item" listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo55">7022</cbc:NameCode>
                                    <cbc:Value>0</cbc:Value>
                                </cac:AdditionalItemProperty>
                            </cac:Item>
                        </cac:DespatchLine>';                        
                $i++;                    
            }    
            $xml.=  '</DespatchAdvice>';

            return $xml;
        }

        private function actualizarItemsPedido($id){
            try {
                $sql = $this->db->connect()->prepare("UPDATE tb_pedidodet 
                                                        SET estadoItem=:est
                                                        WHERE iditem=:item");
                $sql->execute(["item"=>$id,"est" => 99]);              
                
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }
    }
?>