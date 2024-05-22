<?php
    class GuiasServiciosModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarGuiasServicio($g,$c,$a){
            try {
                $salida = ""; 

                $guia = $g == null ? "%" : "%".$g."%";
                $costo = $c == -1  ? "%" : "%".$g."%";
                $anio = $a == "" ? 2024 : $a; 

                $sql = $this->db->connect()->prepare("SELECT
                                                        alm_servicioscab.id_regalm,
                                                        DATE_FORMAT(alm_servicioscab.ffecdoc,'%d/%m/%Y') AS fechaDocumento,
                                                        alm_servicioscab.cnumguia,
                                                        entidad_origen.id_centi AS id_origen,
                                                        entidad_origen.cviadireccion,
                                                        UPPER( entidad_destino.crazonsoc ) AS razon_destino,
                                                        entidad_destino.cnumdoc AS ruc_destino,
                                                        entidad_origen.cnumdoc AS ruc_origen,
                                                        UPPER( entidad_origen.crazonsoc ) AS razon_origen,
                                                        entidad_destino.id_centi AS id_destino,
                                                        UPPER(tb_proyectos.cdesproy) AS costos,
                                                        tb_proyectos.ccodproy 
                                                    FROM
                                                        alm_servicioscab
                                                        LEFT JOIN cm_entidad AS entidad_origen ON alm_servicioscab.ncodalm1 = entidad_origen.id_centi
                                                        LEFT JOIN cm_entidad AS entidad_destino ON alm_servicioscab.ncodalm2 = entidad_destino.id_centi
                                                        LEFT JOIN tb_proyectos ON alm_servicioscab.ncodpry = tb_proyectos.nidreg 
                                                    WHERE
                                                        alm_servicioscab.nflgactivo = 1
                                                        AND alm_servicioscab.cnumguia LIKE :guia
                                                        AND YEAR(alm_servicioscab.ffecdoc) LIKE :anio");
                $sql->execute(["guia"=>$guia,"anio"=>$anio]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                
                    while ($rs = $sql->fetch()){
                        $salida .='<tr data-indice="'.$rs['id_regalm'].'" data-guia="cnumguia" class="pointer">
                                        <td class="textoCentro">'.str_pad($rs['id_regalm'],6,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.$rs['fechaDocumento'].'</td>
                                        <td class="textoCentro">'.$rs['razon_origen'].'</td>
                                        <td class="pl20px">'.$rs['razon_destino'].'</td>
                                        <td class="pl20px">'.$rs['costos'].'</td>
                                        <td class="textoCentro">'.$rs['cnumguia'].'</td>
                                    </tr>';
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function grabarDatosDocumento($formCab,$detalles,$guia){
            try {
                
                $fecha = explode("-",$formCab['fecha']);

                $sql = $this->db->connect()->prepare("INSERT INTO alm_servicioscab SET ntipmov = :ntipmov,
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
                    $indice = $this->lastInsertId("SELECT COUNT(id_regalm) AS id FROM alm_servicioscab");
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
                        $sql=$this->db->connect()->prepare("INSERT INTO alm_serviciosdet SET id_regalm=:cod,
                                                                                            ncodalm1=:ori,
                                                                                            id_cprod=:idprod,
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
                                        "idprod"=>$datos[$i]->idprod,
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

                require_once("public/formatos/guiaremision.php");
                
                $archivo = "public/documentos/guias_remision/".$cabecera['numero_guia'].".pdf";
                $datos = json_decode($detalles);
                $nreg = count($datos);
                $fecha_emision = date("d/m/Y", strtotime($cabecera['fgemision']));
                $fecha_traslado = date("d/m/Y", strtotime($cabecera['ftraslado']));
                $anio = explode('-',$cabecera['fgemision']);

                if ($cabecera['ftraslado'] !== "")
                    $fecha_traslado = date("d/m/Y", strtotime($cabecera['ftraslado']));
                else 
                    $fecha_traslado = "";

                    $pdf = new PDF($cabecera['numero_guia'],
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

                //$pdf->Cell(190,5,$nreg,1,1);

                for($i=1;$i<=$nreg;$i++){

                    $cantidad = intval($datos[$rc]->cantdesp);

                    $pdf->SetX(13);

                    $pdf->SetAligns(array("R","R","C","L"));
                    if ($cantidad > 0){
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
                                                    alm_servicioscab.ffecdoc fechadocumento,
                                                    alm_servicioscab.cnumguia,
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
                                                    UPPER(lg_guias.centidir) AS centidir,
                                                    lg_guias.centiruc,
                                                    lg_guias.cenvio,
                                                    lg_guias.cautoriza,
                                                    tb_user.cnombres, 
	                                                tb_user.iduser,
                                                    tipos.cdescripcion AS tipo,
                                                    estados.cdescripcion AS estado  
                                                FROM
                                                    alm_servicioscab
                                                    LEFT JOIN cm_entidad AS entidad_origen ON alm_servicioscab.ncodalm1 = entidad_origen.id_centi
                                                    LEFT JOIN cm_entidad AS entidad_destino ON alm_servicioscab.ncodalm2 = entidad_destino.id_centi
                                                    LEFT JOIN tb_proyectos ON alm_servicioscab.ncodpry = tb_proyectos.nidreg
                                                    LEFT JOIN lg_guias ON alm_servicioscab.cnumguia = lg_guias.cnumguia
                                                    LEFT JOIN tb_user ON alm_servicioscab.id_userAprob = tb_user.iduser
                                                    INNER JOIN tb_parametros AS tipos ON alm_servicioscab.ntipmov = tipos.nidreg
	                                                INNER JOIN tb_parametros AS estados ON alm_servicioscab.nEstadoDoc = estados.nidreg 
                                                WHERE
                                                    alm_servicioscab.nflgactivo = 1 
                                                    AND alm_servicioscab.id_regalm =:indice");
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
                                                        alm_serviciosdet.id_regalm, 
                                                        alm_serviciosdet.ndespacho, 
                                                        alm_serviciosdet.cDescripcion,
                                                        alm_serviciosdet.cCodigo, 
                                                        alm_serviciosdet.cSerie, 
                                                        alm_serviciosdet.cobserva,
                                                        alm_serviciosdet.cUnidad
                                                    FROM
                                                        alm_serviciosdet
                                                    WHERE
                                                        alm_serviciosdet.nflgactivo = 1 AND
                                                        alm_serviciosdet.id_regalm = :indice");
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

        public function filtrarOrdenesServicioID($id){
            try {
                $salida = "";
                $sql = $this->db->connect()->prepare("SELECT
                                                        lg_ordencab.id_regmov,
                                                        tb_costusu.ncodproy,
                                                        lg_ordencab.id_refpedi,
                                                        lg_ordencab.ntipdoc,
                                                        LPAD( lg_ordencab.cnumero, 6, 0 ) AS cnumero,
                                                        DATE_FORMAT( lg_ordencab.ffechadoc, '%d/%m/%Y' ) AS ffechadoc,
                                                        lg_ordencab.nEstadoDoc,
                                                        tb_proyectos.ccodproy,
                                                        CONCAT_WS(
                                                            ' ',
                                                            tb_proyectos.ccodproy,
                                                        UPPER( tb_proyectos.cdesproy )) AS costos,
                                                        CONCAT_WS(
                                                            ' ',
                                                            tb_area.ccodarea,
                                                        UPPER( tb_area.cdesarea )) AS area,
                                                        cm_entidad.crazonsoc,
                                                        (
                                                            SELECT
                                                                SUM( alm_recepdet.ncantidad ) 
                                                            FROM
                                                                alm_recepdet 
                                                            WHERE
                                                                alm_recepdet.pedido = lg_ordencab.id_regmov 
                                                                AND alm_recepdet.orden = lg_ordencab.id_refpedi 
                                                                AND nflgactivo = 1 
                                                            ) AS ingresos,
                                                        ( SELECT SUM( lg_ordendet.ncanti ) FROM lg_ordendet WHERE lg_ordendet.id_orden = lg_ordencab.id_regmov ) AS cantidad_orden 
                                                    FROM
                                                        tb_costusu
                                                        INNER JOIN lg_ordencab ON tb_costusu.ncodproy = lg_ordencab.ncodpry
                                                        INNER JOIN tb_proyectos ON lg_ordencab.ncodpry = tb_proyectos.nidreg
                                                        INNER JOIN tb_area ON lg_ordencab.ncodarea = tb_area.ncodarea
                                                        INNER JOIN cm_entidad ON lg_ordencab.id_centi = cm_entidad.id_centi 
                                                    WHERE
                                                        tb_costusu.id_cuser = :usr 
                                                        AND lg_ordencab.cnumero = :id 
                                                        AND tb_costusu.nflgactivo = 1
                                                        AND lg_ordencab.ntipmov = 38 
                                                        AND lg_ordencab.nEstadoDoc BETWEEN 60 AND 62 
                                                    ORDER BY
                                                        id_regmov DESC");
                $sql->execute(["usr"=>$_SESSION['iduser'],"id"=>$id]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()) {
                        //compara la orden si fue ingresada esta completa y no la muestra
                        $diferencia_ingreso = $rs['cantidad_orden'] - $rs['ingresos'];

                        if (($diferencia_ingreso) > 0 ) {
                            $salida.='<tr data-orden="'.$rs['id_regmov'].'">
                                    <td class="textoCentro">'.$rs['cnumero'].'</td>
                                    <td class="textoCentro">'.$rs['ffechadoc'].'</td>
                                    <td class="pl20px">'.$rs['area'].'</td>
                                    <td class="textoDerecha pr5px">'.$rs['ccodproy'].'</td>
                                    <td class="pl20px">'.$rs['crazonsoc'].'</td>
                                </tr>';
                        }
                    }
                }
                return $salida;
            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }

        public function filtrarPedidoServicioID($id,$costos){
            try {
                $salida = "";

                $sql = $this->db->connect()->prepare("SELECT
                                                    tb_pedidocab.idreg,
                                                    tb_pedidocab.idcostos,
                                                    tb_pedidocab.idarea,
                                                    tb_pedidocab.emision,
                                                    tb_pedidocab.vence,
                                                    tb_pedidocab.estadodoc,
                                                    tb_pedidocab.nrodoc,
                                                    tb_pedidocab.idtipomov,
                                                    UPPER( ibis.tb_pedidocab.concepto ) AS concepto,
                                                    UPPER(
                                                    CONCAT( ibis.tb_proyectos.ccodproy, ' ', ibis.tb_proyectos.cdesproy )) AS costos,
                                                    tb_pedidocab.nivelAten,
                                                    atenciones.cdescripcion AS atencion,
                                                    estados.cdescripcion AS estado,
                                                    estados.cabrevia,
                                                    tb_area.cdesarea 
                                                FROM
                                                    tb_pedidocab
                                                    LEFT JOIN tb_proyectos ON tb_pedidocab.idcostos = tb_proyectos.nidreg
                                                    LEFT JOIN tb_parametros AS atenciones ON tb_pedidocab.nivelAten = atenciones.nidreg
                                                    LEFT JOIN tb_parametros AS estados ON tb_pedidocab.estadodoc = estados.nidreg
                                                    LEFT JOIN tb_area ON tb_pedidocab.idarea = tb_area.ncodarea 
                                                WHERE
                                                    tb_pedidocab.estadodoc = 54 
                                                    AND tb_pedidocab.idtipomov = 38 
                                                    AND tb_pedidocab.idcostos LIKE '%' 
                                                    AND tb_pedidocab.nrodoc = :numero");

                $sql->execute(["numero"=>$id]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {

                    while ($rs = $sql->fetch()) {
                        $salida.='<tr data-orden="'.$rs['idreg'].'">
                                <td class="textoCentro">'.$rs['nrodoc'].'</td>
                                <td class="textoCentro">'.$rs['emision'].'</td>
                                <td class="pl20px">'.$rs['concepto'].'</td>
                                <td class="textoDerecha pr5px">'.$rs['costos'].'</td>
                         </tr>';
                    }

                }
                return $salida;
            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }

        public function consultarOrdenIdServicio($id){
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                        lg_ordencab.id_regmov,
                                                        LPAD(lg_ordencab.cnumero,6,0) AS cnumero,
                                                        lg_ordencab.ffechadoc,
                                                        lg_ordencab.ncodcos,
                                                        lg_ordencab.ncodarea,
                                                        lg_ordencab.id_centi,
                                                        lg_ordencab.ncodcot,
                                                        lg_ordencab.cnumcot,
                                                        lg_ordencab.nEstadoDoc,
                                                        lg_ordencab.id_refpedi,
                                                        UPPER( tb_pedidocab.concepto ) AS concepto,
                                                        UPPER( tb_pedidocab.detalle ) AS detalle,
                                                        UPPER(
                                                        CONCAT_WS( ' ', tb_proyectos.ccodproy, tb_proyectos.cdesproy )) AS costos,
                                                        lg_ordencab.ncodpry,
                                                        UPPER(
                                                        CONCAT_WS( ' ', tb_area.ccodarea, tb_area.cdesarea )) AS area,
                                                        lg_ordencab.ncodmon,
                                                        lg_ordencab.ntipmov,
                                                        lg_ordencab.ffechaent,
                                                        cm_entidad.crazonsoc,
                                                        cm_entidad.cnumdoc,
                                                        UPPER( tb_almacen.cdesalm ) AS cdesalm,
                                                        cm_entidad.cemail AS mail_entidad,
                                                        lg_ordencab.cverificacion,
                                                        lg_ordencab.ncodalm,
                                                        LPAD(tb_pedidocab.nrodoc,6,0) AS pedido,
                                                        tb_pedidocab.nivelAten,
                                                        CONCAT_WS(' ',rrhh.tabla_aquarius.nombres,rrhh.tabla_aquarius.apellidos) AS solicita
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
                                                            INNER JOIN tb_parametros AS transportes ON lg_ordencab.ctiptransp = transportes.nidreg
                                                            INNER JOIN tb_almacen ON lg_ordencab.ncodalm = tb_almacen.ncodalm
                                                            INNER JOIN rrhh.tabla_aquarius ON tb_pedidocab.idsolicita = rrhh.tabla_aquarius.internal 
                                                            WHERE
                                                        lg_ordencab.id_regmov =:id 
                                                        AND lg_ordencab.nflgactivo = 1");
                $sql->execute(["id"=>$id]);
                $docData = array();
                while($row=$sql->fetch(PDO::FETCH_ASSOC)){
                    $docData[] = $row;
                }

                $almacen = $docData[0]['ncodalm'];

                return array("cabecera"=>$docData,
                            "detalles"=>$this->ordenDetallesServicio($id),
                            "numero"=>1);
            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }

        private function ordenDetallesServicio($id) {
            try {
                $salida ="";
                $estados = $this->listarSelect(13,96);

                $sql = $this->db->connect()->prepare("SELECT
                                                    lg_ordendet.nitemord,
                                                    lg_ordendet.id_regmov,
                                                    lg_ordendet.niddeta,
                                                    lg_ordendet.nidpedi,
                                                    lg_ordendet.id_cprod,
                                                    lg_ordendet.id_orden,
                                                    cm_producto.ccodprod,
                                                    UPPER( CONCAT_WS( ' ', cm_producto.cdesprod, tb_pedidodet.observaciones, tb_pedidodet.docEspec ) ) AS cdesprod,
                                                    cm_producto.nund,
                                                    tb_unimed.cabrevia,
                                                    tb_pedidodet.idpedido,
                                                    tb_pedidodet.nroparte,
                                                    tb_pedidocab.nrodoc,
	                                                lg_ordencab.cnumero,
                                                    REPLACE ( FORMAT( lg_ordendet.ncanti, 2 ), ',', '' ) AS cantidad,
                                                    ( SELECT SUM( alm_recepdet.ncantidad ) FROM alm_recepdet WHERE alm_recepdet.niddetaOrd = lg_ordendet.nitemord AND alm_recepdet.nflgactivo = 1 ) AS pendiente 
                                                FROM
                                                    lg_ordendet
                                                    INNER JOIN cm_producto ON lg_ordendet.id_cprod = cm_producto.id_cprod
                                                    INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed
                                                    INNER JOIN tb_pedidodet ON lg_ordendet.niddeta = tb_pedidodet.iditem
                                                    INNER JOIN tb_pedidocab ON tb_pedidodet.idpedido = tb_pedidocab.idreg
                                                    INNER JOIN lg_ordencab ON lg_ordendet.id_regmov = lg_ordencab.id_regmov 
                                                WHERE
                                                    lg_ordendet.id_orden = :id");
                $sql->execute(["id"=>$id]);
                
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    $item=1;
                    
                    while ($rs = $sql->fetch()){
                        $saldo = $rs['cantidad'] - $rs['pendiente'];

                        if ( $saldo > 0 ) {
                            $salida.='<tr data-detorden="'.$rs['nitemord'].'" 
                                        data-idprod="'.$rs['id_cprod'].'"
                                        data-iddetped="'.$rs['niddeta'].'"
                                        data-saldo="'.$saldo.'"
                                        data-grabado="0"
                                        data-id="-">
                                            <td class="textoCentro"><a href="'.$rs['id_orden'].'" data-accion="deleteItem" class="eliminarItem"><i class="fas fa-minus"></i></a></td>
                                            <td class="textoCentro"><input type="checkbox"></td>
                                            <td class="textoCentro">'.str_pad($item++,3,0,STR_PAD_LEFT).'</td>
                                            <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                            <td class="pl20px">'.$rs['cdesprod'].'</td>
                                            <td class="textoCentro">'.$rs['cabrevia'].'</td>
                                            <td class="textoDerecha pr20px"><input type="text" value="'.$rs['cantidad'].'" readonly></td>
                                            <td class="textoCentro"><input type="hidden" readonly></td>
                                            <td class="textoCentro"><input type="hidden" readonly></td>
                                            <td class="textoCentro"><input type="hidden" readonly></td>
                                            <td class="textoCentro">'.$rs['nrodoc'].'</td>
                                            <td class="textoCentro">'.$rs['cnumero'].'</td>
                                    </tr>';
                        }
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }

        public function detallesPedidoServicio($id){
            try {
                $sql=$this->db->connect()->prepare("SELECT
                                                    tb_pedidodet.iditem,
                                                    tb_pedidodet.idpedido,
                                                    tb_pedidodet.idprod,
                                                    tb_pedidodet.idcostos,
                                                    tb_pedidodet.unid,
                                                    tb_pedidodet.cant_pedida,
                                                    UPPER( tb_pedidodet.observaciones ) AS observaciones,
                                                    tb_pedidodet.idorden, 
	                                                cm_producto.cdesprod,
                                                    cm_producto.ccodprod,
                                                    UPPER( cm_producto.cdesprod ) AS cdesprod,
                                                    tb_unimed.cabrevia 
                                                FROM
                                                    tb_pedidodet
                                                    INNER JOIN cm_producto ON tb_pedidodet.idprod = cm_producto.id_cprod
                                                    INNER JOIN tb_unimed ON tb_pedidodet.unid = tb_unimed.ncodmed 
                                                WHERE
                                                    tb_pedidodet.idpedido = :id 
                                                    AND tb_pedidodet.nflgActivo = 1");
                $sql->execute(["id"=>$id]);
                
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    $item=1;
                    
                    while ($rs = $sql->fetch()){
                        $saldo = $rs['cantidad'] - $rs['pendiente'];

                        if ( $saldo > 0 ) {
                            $salida.='<tr data-detorden="'.$rs['idorden'].'" 
                                        data-idprod="'.$rs['idprod'].'"
                                        data-iddetped="'.$rs['idpedido'].'"
                                        data-saldo="'.$rs['cant_pedida'].'"
                                        data-grabado="0"
                                        data-id="-">
                                            <td class="textoCentro"><a href="'.$rs['id_orden'].'" data-accion="deleteItem" class="eliminarItem"><i class="fas fa-minus"></i></a></td>
                                            <td class="textoCentro"><input type="checkbox"></td>
                                            <td class="textoCentro">'.str_pad($item++,3,0,STR_PAD_LEFT).'</td>
                                            <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                            <td class="pl20px">'.$rs['cdesprod'].'</td>
                                            <td class="textoCentro">'.$rs['cabrevia'].'</td>
                                            <td class="textoDerecha pr20px"><input type="text" value="'.$rs['cantidad'].'" readonly></td>
                                            <td class="textoCentro"><input type="hidden" readonly></td>
                                            <td class="textoCentro"><input type="hidden" readonly></td>
                                            <td class="textoCentro"><input type="hidden" readonly></td>
                                            <td class="textoCentro">'.$rs['nrodoc'].'</td>
                                            <td class="textoCentro">'.$rs['cnumero'].'</td>
                                    </tr>';
                        }
                    }
                }

            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }

        public function grabarGuiaServicios($guia,$form,$detalles,$operacion){
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

        public function consultarGuiaServicioId($indice,$guia){
            try {
                $docdata = [];

                $sql = $this->db->connect()->prepare("SELECT
                                                    alm_servicioscab.ffecdoc fechadocumento,
                                                    alm_servicioscab.cnumguia,
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
                                                    UPPER(lg_guias.centidir) AS centidir,
                                                    lg_guias.centiruc,
                                                    lg_guias.cenvio,
                                                    lg_guias.cautoriza,
                                                    tb_user.cnombres, 
	                                                tb_user.iduser,
                                                    tipos.cdescripcion AS tipo,
                                                    estados.cdescripcion AS estado  
                                                FROM
                                                    alm_servicioscab
                                                    LEFT JOIN cm_entidad AS entidad_origen ON alm_servicioscab.ncodalm1 = entidad_origen.id_centi
                                                    LEFT JOIN cm_entidad AS entidad_destino ON alm_servicioscab.ncodalm2 = entidad_destino.id_centi
                                                    LEFT JOIN tb_proyectos ON alm_servicioscab.ncodpry = tb_proyectos.nidreg
                                                    LEFT JOIN lg_guias ON alm_servicioscab.cnumguia = lg_guias.cnumguia
                                                    LEFT JOIN tb_user ON alm_servicioscab.id_userAprob = tb_user.iduser
                                                    INNER JOIN tb_parametros AS tipos ON alm_servicioscab.ntipmov = tipos.nidreg
	                                                INNER JOIN tb_parametros AS estados ON alm_servicioscab.nEstadoDoc = estados.nidreg 
                                                WHERE
                                                    alm_servicioscab.nflgactivo = 1 
                                                    AND alm_servicioscab.id_regalm =:indice");
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
                            "detalles"=>$this->detallesGuiaServicio($indice));

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function detallesGuiaServicio($indice){
            try {
                $salida = "";
                $sql = $this->db->connect()->prepare("SELECT
                                                        alm_serviciosdet.id_regalm, 
                                                        alm_serviciosdet.ndespacho, 
                                                        alm_serviciosdet.cDescripcion,
                                                        alm_serviciosdet.cCodigo, 
                                                        alm_serviciosdet.cSerie, 
                                                        alm_serviciosdet.cobserva,
                                                        alm_serviciosdet.cUnidad
                                                    FROM
                                                        alm_serviciosdet
                                                    WHERE
                                                        alm_serviciosdet.nflgactivo = 1 AND
                                                        alm_serviciosdet.id_regalm = :indice");
                $sql->execute(["indice"=>$indice]);

                $rowCount = $sql->rowCount();
                $item = 1;

                if($rowCount > 0) {
                    while ($rs = $sql->fetch()){
                        $salida .='<tr data-grabado="1" >
                                        <td class="textoCentro"><a href="delete"><i class="fas fa-trash-alt"></i></a></td>
                                        <td class="textoCentro"><a href="search"><i class="fas fa-search"></i></a></td>
                                        <td class="textoCentro">'.str_pad($item++,3,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro"><input type="text" value="'.$rs['cCodigo'].'" readOnly></td>
                                        <td class="pl20px"><textarea readOnly>'.$rs['cDescripcion'].'</textarea></td>
                                        <td class="textoCentro">'.$rs['cUnidad'].'</td>
                                        <td><input type="number" value="'.$rs['ndespacho'].'" min=1></td>
                                        <td class="pl20px"><textarea>'.$rs['cobserva'].'</textarea></td>
                                        <td></td>
                                        <td></td>
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