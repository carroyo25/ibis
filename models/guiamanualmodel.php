<?php
    class GuiaManualModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarGuiasManuales($g,$c,$a){
            try {
                $salida = ""; 

                $guia = $g == null ? "%" : "%".$g."%";
                $costo = $c == -1  ? "%" : "%".$g."%";
                $anio = $a == "" ? 2024 : $a; 

                $sql = $this->db->connect()->prepare("SELECT
                                                        alm_desplibrescab.id_regalm,
                                                        DATE_FORMAT(alm_desplibrescab.ffecdoc,'%d/%m/%Y') AS fechaDocumento,
                                                        alm_desplibrescab.cnumguia,
                                                        entidad_origen.id_centi AS id_origen,
                                                        entidad_origen.cviadireccion,
                                                        UPPER( entidad_destino.crazonsoc ) AS razon_destino,
                                                        entidad_destino.cnumdoc AS ruc_destino,
                                                        entidad_origen.cnumdoc AS ruc_origen,
                                                        UPPER( entidad_origen.crazonsoc ) AS razon_origen,
                                                        entidad_destino.id_centi AS id_destino,
                                                        UPPER(tb_proyectos.cdesproy) AS costos,
                                                        tb_proyectos.ccodproy,
                                                        lg_guias.ticketsunat,
                                                        lg_guias.guiasunat,
                                                        lg_guias.estadoSunat  
                                                    FROM
                                                        alm_desplibrescab
                                                        LEFT JOIN cm_entidad AS entidad_origen ON alm_desplibrescab.ncodalm1 = entidad_origen.id_centi
                                                        LEFT JOIN cm_entidad AS entidad_destino ON alm_desplibrescab.ncodalm2 = entidad_destino.id_centi
                                                        LEFT JOIN tb_proyectos ON alm_desplibrescab.ncodpry = tb_proyectos.nidreg
                                                        LEFT JOIN lg_guias ON alm_desplibrescab.cnumguia = lg_guias.cnumguia  
                                                    WHERE
                                                        alm_desplibrescab.nflgactivo = 1
                                                        AND alm_desplibrescab.cnumguia LIKE :guia
                                                        AND YEAR(alm_desplibrescab.ffecdoc) LIKE :anio
                                                    ORDER BY alm_desplibrescab.ffecdoc DESC");
                $sql->execute(["guia"=>$guia,"anio"=>$anio]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                
                    while ($rs = $sql->fetch()){
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
                        
                        $salida .='<tr data-indice="'.$rs['id_regalm'].'" data-guia="cnumguia" class="pointer">
                                        <td class="textoCentro">'.str_pad($rs['id_regalm'],6,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.$rs['fechaDocumento'].'</td>
                                        <td class="textoCentro">'.$rs['razon_origen'].'</td>
                                        <td class="pl20px">'.$rs['razon_destino'].'</td>
                                        <td class="pl20px">'.$rs['costos'].'</td>
                                        <td class="textoCentro">'.$rs['cnumguia'].'</td>
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

        public function grabarGuiaManual($guia,$form,$detalles,$operacion){
            $mensaje = "error de creacion";
            $guiaAutomatica = "";

            try {
                if ( $operacion == 'n' ){
                    $guiaAutomatica = $this->numeroGuia();
                    $mensaje = "Se grabo la guia de remision";
                    
                    $this->grabarDatosDocumento($form,$detalles,$guiaAutomatica);
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

        private function grabarDatosDocumento($formCab,$detalles,$guia){
            try {
                
                $fecha = explode("-",$formCab['fecha']);

                $sql = $this->db->connect()->prepare("INSERT INTO alm_desplibrescab SET ntipmov = :ntipmov,
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
                                                                                        cnumguia =:guia");

                $sql->execute(["ntipmov"=>$formCab['codigo_movimiento'],
                                "nnromov"=>null,
                                "cper"=>$fecha[0],
                                "cmes"=>$fecha[1],
                                "ncodalm1"=>$formCab['codigo_almacen_origen'],
                                "ncodalm2"=>$formCab['codigo_almacen_destino'],
                                "ffecdoc"=>$formCab['fecha'],
                                "ncodpry"=>$formCab['codigo_costos'],
                                "nnronota"=>null,
                                "id_userAprob"=>$formCab['codigo_aprueba'],
                                "nEstadoDoc"=>62,
                                "nflgactivo"=>1,
                                "id_user"=>$_SESSION['iduser'],
                                "guia"=>$guia]);

                $rowCount = $sql->rowCount();
                
                if ($rowCount > 0) {
                    $indice = $this->lastInsertId("SELECT COUNT(id_regalm) AS id FROM alm_desplibrescab");
                    $this->grabarDetalles($indice,$detalles,$formCab['codigo_almacen_origen']);
                }
                
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function grabarDetalles($indice,$detalles,$almacen){
            try {
                $datos = json_decode($detalles);
                $nreg = count($datos);

                for ($i=0; $i < $nreg; $i++) { 
                    try {
                        $sql=$this->db->connect()->prepare("INSERT INTO alm_desplibresdet SET id_regalm=:cod,
                                                                                            ncodalm1=:ori,
                                                                                            cCodigo=:cpro,
                                                                                            ncantidad=:cant,
                                                                                            niddetaPed=:idpedido,
                                                                                            niddetaOrd=:idorden,
                                                                                            nflgactivo=:flag,
                                                                                            nestadoreg=:estadoItem,
                                                                                            ingreso=:ingreso,
                                                                                            ncodalm2=:destino,
                                                                                            niddetaIng=:itemIngreso,
                                                                                            nroorden=:orden,
                                                                                            nropedido=:pedido,
                                                                                            ndespacho=:candesp,
                                                                                            cobserva=:observac,
                                                                                            cDescripcion=:descripcion,
                                                                                            cUnidad=:unidad");
                         $sql->execute(["cod"=>$indice,
                                        "ori"=>$almacen,
                                        "cpro"=>$datos[$i]->codigo,
                                        "cant"=>$datos[$i]->cantidad,
                                        "idpedido"=>$datos[$i]->iddetped,
                                        "idorden"=>$datos[$i]->iddetorden,
                                        "flag"=>1,
                                        "estadoItem"=>32,
                                        "ingreso"=>null,
                                        "destino"=>$datos[$i]->destino,
                                        "candesp"=>$datos[$i]->cantdesp,
                                        "itemIngreso"=>null,
                                        "pedido"=>$datos[$i]->pedido,
                                        "orden"=>$datos[$i]->orden,
                                        "observac"=>$datos[$i]->obser,
                                        "unidad"=>$datos[$i]->unidad,
                                        "descripcion"=>$datos[$i]->descripcion]);
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

        public function grabarDatosGuia($guiaCab,$formCab,$nroguia){
            try {
                $sql = $this->db->connect()->prepare("INSERT INTO lg_guias SET id_regalm=:despacho,cnumguia=:guia,corigen=:origen,
                                                                                cdirorigen=:direccion_origen,cdestino=:destino,
                                                                                cdirdest=:direccion_destino,centi=:entidad,centidir=:direccion_entidad,
                                                                                centiruc=:ruc_entidad,ctraslado=:traslado,cenvio=:envio,
                                                                                cautoriza=:autoriza,cdestinatario=:destinatario,cobserva=:observaciones,
                                                                                cnombre=:nombres,cmarca=:marca,clicencia=:licencia,cplaca=:placa,
                                                                                ftraslado=:fecha_traslado,fguia=:fecha_guia,cserie=:serie");

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
                                "autoriza"=>$guiaCab['autoriza'],
                                "destinatario"=>$guiaCab['destinatario'],
                                "observaciones"=>$guiaCab['observaciones'],
                                "nombres"=>$guiaCab['nombre_conductor'],
                                "marca"=>$guiaCab['marca'],
                                "licencia"=>$guiaCab['licencia_conducir'],
                                "placa"=>$guiaCab['placa'],
                                "fecha_traslado"=>$guiaCab['ftraslado'],
                                "fecha_guia"=>$guiaCab['fgemision'],
                                "serie"=>'F001']);
                
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

        public function generarGuiaPdf($cabecera,$detalles,$proyecto){
            try {
                require_once("public/formatos/guiaremision.php");
                
                $archivo = "public/documentos/guias_remision/20504898173-09-T001-".$cabecera['numero_guia_sunat'].".pdf";
                $qrsunat = "20504898173-09-T001-".$cabecera['numero_guia_sunat'].".png";
                $qrprint = null;

                $datos = json_decode($detalles);
                $nreg = count($datos);
                $fecha_emision = date("d/m/Y", strtotime($cabecera['fgemision']));
                $fecha_traslado = date("d/m/Y", strtotime($cabecera['ftraslado']));
                $anio = explode('-',$cabecera['fgemision']);

                if ($cabecera['ftraslado'] !== "")
                    $fecha_traslado = date("d/m/Y", strtotime($cabecera['ftraslado']));
                else 
                    $fecha_traslado = "";

                    $pdf = new PDF($cabecera['numero_guia_sunat'],
                    $fecha_emision,
                    $cabecera['destinatario_ruc'],
                    $cabecera['destinatario_razon'],
                    $cabecera['destinatario_direccion'],
                    $cabecera['empresa_transporte_razon'],
                    $cabecera['ruc_proveedor'],
                    $cabecera['direccion_proveedor'],
                    $cabecera['almacen_origen_direccion'],
                    null,
                    null,
                    null,
                    $fecha_traslado,
                    $cabecera['modalidad_traslado'],
                    $cabecera['almacen_destino_direccion'],
                    null,
                    null,
                    null,
                    $cabecera['marca'],
                    $cabecera['placa'],
                    $cabecera['nombre_conductor'],
                    $cabecera['licencia_conducir'],
                    $cabecera['tipo_envio'],
                    '',
                    $proyecto,
                    $anio[0],
                    $cabecera["observaciones"],
                    $cabecera["destinatario"],
                    $cabecera["tipo_documento"],
                    'A4');
                
                $pdf->AliasNbPages();
                $pdf->AddPage();
                $pdf->SetWidths(array(10,15,15,147));
                $pdf->SetFillColor(255,255,255);
                $pdf->SetTextColor(0,0,0);
                
                $pdf->SetFont('Arial','',6);
                $lc = 0;
                $rc = 0;
                $item = 1;

                if (file_exists("public/documentos/guia_electronica/qr/".$qrsunat)) {
                    $qrprint =  "public/documentos/guia_electronica/qr/".$qrsunat;

                    $pdf->Image($qrprint,165,210,35);
                }

                for($i=1;$i<=$nreg;$i++){

                    $cantidad = floatval($datos[$rc]->cantdesp);

                    $pdf->SetX(13);

                    $pdf->SetAligns(array("R","R","C","L"));
                    if ( $cantidad > 0 ){
                        $pdf->Row(array(str_pad($item++,3,"0",STR_PAD_LEFT),
                                        $cantidad,
                                        $datos[$rc]->unidad,
                                        $datos[$rc]->codigo." ".$datos[$rc]->descripcion));
                    }
                
                    $lc++;
                    $rc++;

                    if ($lc == 26) {
                        $pdf->AddPage();
                        $lc = 0;
                    }
                }

                $pdf->Ln(1);
                $pdf->SetX(13);
                $pdf->Ln(2);
                $pdf->SetX(13);
                $pdf->Output($archivo,'F');

                return array("archivo"=>$archivo);

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function consultarGuiaManualId($indice,$guia){
            try {
                $docdata = [];

                $sql = $this->db->connect()->prepare("SELECT
                                                    alm_desplibrescab.ffecdoc fechadocumento,
                                                    alm_desplibrescab.cnumguia,
                                                    entidad_origen.id_centi AS id_origen,
                                                    UPPER( entidad_origen.cviadireccion ) AS direccion_origen,
                                                    UPPER( entidad_destino.cviadireccion ) AS direccion_destino,
                                                    UPPER( entidad_destino.crazonsoc ) AS razon_destino,
                                                    entidad_destino.cnumdoc AS ruc_destino,
                                                    entidad_origen.cnumdoc AS ruc_origen,
                                                    UPPER( entidad_origen.crazonsoc ) AS razon_origen,
                                                    entidad_destino.id_centi AS id_destino,
                                                    tb_proyectos.cdesproy,
                                                    tb_proyectos.ccodproy,
                                                    lg_guias.cserie,
                                                    lg_guias.ctraslado,
                                                    lg_guias.cmarca,
                                                    lg_guias.cplaca,
                                                    lg_guias.cnombre,
                                                    lg_guias.clicencia,
                                                    lg_guias.ftraslado,
                                                    lg_guias.cdestinatario,
                                                    lg_guias.cmotivo,
                                                    lg_guias.corigen,
                                                    lg_guias.cdirorigen,
                                                    lg_guias.cdestino,
                                                    lg_guias.cdirdest,
                                                    lg_guias.centi,
                                                    lg_guias.nDniConductor,
                                                    lg_guias.nPeso,
                                                    lg_guias.nBultos,
                                                    lg_guias.ticketsunat,
                                                    lg_guias.guiasunat,
                                                    lg_guias.estadoSunat, 
                                                    UPPER(lg_guias.centidir) AS centidir,
                                                    lg_guias.centiruc,
                                                    lg_guias.cenvio,
                                                    lg_guias.cautoriza,
                                                    tb_user.cnombres, 
	                                                tb_user.iduser,
                                                    tipos.cdescripcion AS tipo,
                                                    estados.cdescripcion AS estado  
                                                FROM
                                                    alm_desplibrescab
                                                    LEFT JOIN cm_entidad AS entidad_origen ON alm_desplibrescab.ncodalm1 = entidad_origen.id_centi
                                                    LEFT JOIN cm_entidad AS entidad_destino ON alm_desplibrescab.ncodalm2 = entidad_destino.id_centi
                                                    LEFT JOIN tb_proyectos ON alm_desplibrescab.ncodpry = tb_proyectos.nidreg
                                                    LEFT JOIN lg_guias ON alm_desplibrescab.cnumguia = lg_guias.cnumguia
                                                    LEFT JOIN tb_user ON alm_desplibrescab.id_userAprob = tb_user.iduser
                                                    INNER JOIN tb_parametros AS tipos ON alm_desplibrescab.ntipmov = tipos.nidreg
	                                                INNER JOIN tb_parametros AS estados ON alm_desplibrescab.nEstadoDoc = estados.nidreg 
                                                WHERE
                                                    alm_desplibrescab.nflgactivo = 1 
                                                    AND alm_desplibrescab.id_regalm =:indice");
                $sql->execute(["indice"=>$indice]);

                $rowCount = $sql->rowCount();
                
                if ($rowCount) {
                    $respuesta = true;
                    $i = 0;
                    
                    while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                        $docData[] = $row;
                    }
                }

                return array("cabecera"=>$docData,
                            "detalles"=>$this->detallesGuia($indice));

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function detallesGuia($indice){
            try {
                $salida = "";
                $sql = $this->db->connect()->prepare("SELECT
                                                        alm_desplibresdet.id_regalm, 
                                                        alm_desplibresdet.ndespacho, 
                                                        alm_desplibresdet.cDescripcion,
                                                        alm_desplibresdet.cCodigo, 
                                                        alm_desplibresdet.cSerie, 
                                                        alm_desplibresdet.cobserva,
                                                        alm_desplibresdet.cUnidad
                                                    FROM
                                                        alm_desplibresdet
                                                    WHERE
                                                        alm_desplibresdet.nflgactivo = 1 AND
                                                        alm_desplibresdet.id_regalm = :indice");
                $sql->execute(["indice"=>$indice]);

                $rowCount = $sql->rowCount();
                $item = 1;

                if($rowCount > 0) {
                    while ($rs = $sql->fetch()){
                        $salida .='<tr data-grabado="1" >
                                        <td class="textoCentro"><a href="delete"><i class="fas fa-trash-alt"></i></a></td>
                                        <td class="textoCentro"><a href="search"><i class="fas fa-search"></i></a></td>
                                        <td class="textoCentro">'.$item++.'</td>
                                        <td class="textoCentro"><input type="text" value="'.$rs['cCodigo'].'" readOnly></td>
                                        <td class="pl20px"><textarea readOnly>'.$rs['cDescripcion'].'</textarea></td>
                                        <td><input type="text" value="'.$rs['cUnidad'].'"></td>
                                        <td><input type="number" value="'.$rs['ndespacho'].'" min=1></td>
                                        <td class="pl20px"><textarea>'.$rs['cobserva'].'</textarea></td>
                                        <td></td>
                                        <td></td>
                                    </tr>';
                    }
                }

                return $salida;

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }
    }
?>