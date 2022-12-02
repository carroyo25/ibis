<?php
    class SalidaModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarNotasDespacho(){
            $salida = "";
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                        alm_despachocab.id_regalm,
                                                        alm_despachocab.cmes,
                                                        DATE_FORMAT(
                                                            alm_despachocab.ffecdoc,
                                                            '%d/%m/%Y'
                                                        ) AS ffecdoc,
                                                        YEAR(ffecdoc) AS anio,
                                                        alm_despachocab.ncodpry,
                                                        UPPER(origen.cdesalm) AS origen,
                                                        UPPER(origen.ctipovia) AS direccion_origen,
                                                        alm_despachocab.nEstadoDoc,
                                                        alm_despachocab.cnumguia,
                                                        UPPER(destino.cdesalm) AS destino,
                                                        UPPER(destino.ctipovia) AS direccion_destino,
                                                        UPPER(
                                                            CONCAT_WS(
                                                                ' ',
                                                                tb_proyectos.ccodproy,
                                                                tb_proyectos.cdesproy
                                                                
                                                            )
                                                        ) AS costos,
                                                        tb_parametros.cdescripcion,
                                                        tb_parametros.cabrevia
                                                    FROM
                                                        tb_costusu
                                                    INNER JOIN alm_despachocab ON tb_costusu.ncodproy = alm_despachocab.ncodpry
                                                    INNER JOIN tb_almacen AS origen ON alm_despachocab.ncodalm1 = origen.ncodalm
                                                    INNER JOIN tb_almacen AS destino ON alm_despachocab.ncodalm2 = destino.ncodalm
                                                    INNER JOIN tb_proyectos ON alm_despachocab.ncodpry = tb_proyectos.nidreg
                                                    INNER JOIN tb_parametros ON alm_despachocab.nEstadoDoc = tb_parametros.nidreg
                                                    WHERE
                                                        tb_costusu.nflgactivo = 1
                                                    AND tb_costusu.id_cuser = :usr
                                                    AND alm_despachocab.nEstadoDoc = 62
                                                    ORDER BY alm_despachocab.ffecdoc ASC");
                $sql->execute(["usr"=>$_SESSION['iduser']]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()){
                        $salida .='<tr data-indice="'.$rs['id_regalm'].'" class="pointer">
                                        <td class="textoCentro">'.str_pad($rs['id_regalm'],6,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.$rs['ffecdoc'].'</td>
                                        <td class="textoCentro">'.$rs['origen'].'</td>
                                        <td class="pl20px">'.$rs['destino'].'</td>
                                        <td class="pl20px">'.$rs['costos'].'</td>
                                        <td class="textoCentro">'.$rs['anio'].'</td>
                                        <td class="textoCentro">'.$rs['cnumguia'].'</td>
                                        <td class="textoCentro '.$rs['cabrevia'].'">'.$rs['cdescripcion'].'</td>
                                    </tr>';
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        //esto se usara para todos los documentos
        private function ultimoIndice(){
            $indice = $this->lastInsertId("SELECT COUNT(id_regalm) + 1 AS id FROM alm_despachocab"); 
            return str_pad($indice++,6,0,STR_PAD_LEFT);
        }

        public function pasarDetallesOrden($id,$costo){
            try {
                $indice = $this->ultimoIndice();
                return array("numero"=>$indice++,
                            "items"=>$this->ordenDetalles($id),
                            "costos"=>$this->centroCostos($costo));
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function ordenDetalles($id) {
            try {
                $salida ="";

                $sql = $this->db->connect()->prepare("SELECT
                                                        lg_ordendet.nitemord,
                                                        lg_ordendet.id_regmov,
                                                        lg_ordendet.niddeta,
                                                        lg_ordendet.nidpedi,
                                                        lg_ordendet.id_cprod,
                                                        lg_ordendet.id_orden,
                                                        cm_producto.ccodprod,
                                                        LPAD(tb_pedidocab.nrodoc,6,0) AS pedido,
                                                        UPPER( CONCAT_WS( ' ', cm_producto.cdesprod, tb_pedidodet.observaciones, tb_pedidodet.docEspec ) ) AS cdesprod,
                                                        cm_producto.nund,
                                                        tb_unimed.cabrevia,
                                                        tb_pedidodet.idpedido,
                                                        tb_pedidodet.nroparte,
                                                        REPLACE ( FORMAT( lg_ordendet.ncanti, 2 ), ',', '' ) AS cantidad,
                                                        despacho.pendiente AS total_despachado,
                                                        @id := lg_ordendet.nitemord AS idorden 
                                                    FROM
                                                        lg_ordendet
                                                        INNER JOIN cm_producto ON lg_ordendet.id_cprod = cm_producto.id_cprod
                                                        INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed
                                                        INNER JOIN tb_pedidodet ON lg_ordendet.niddeta = tb_pedidodet.iditem
                                                        INNER JOIN tb_pedidocab ON tb_pedidocab.idreg = tb_pedidodet.idpedido
                                                        LEFT JOIN ( SELECT SUM( alm_despachodet.ncantidad ) 
                                                            AS pendiente, niddetaOrd 
                                                            FROM alm_despachodet 
                                                            WHERE alm_despachodet.niddetaOrd = @id ) AS despacho ON lg_ordendet.nitemord = despacho.niddetaOrd 
                                                    WHERE
                                                        lg_ordendet.id_orden = :id");
                $sql->execute(["id"=>$id]);
                
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    $item=1;
                    
                    while ($rs = $sql->fetch()){
                        $saldo = $rs['cantidad'] - $this->calcularSaldosItemsDespachados($rs['id_orden'],$rs['id_cprod']);
                        $cantidad_ingresada = $this->ingresosRegistrados($rs['id_regmov'],$rs['nitemord']);
                        $pendientes = $rs['cantidad'] - $this->ingresosRegistrados($rs['id_regmov'],$rs['nitemord']);
                       
                        if ( $saldo > 0) {
                            $salida.='<tr data-detorden="'.$rs['nitemord'].'" 
                                        data-idprod="'.$rs['id_cprod'].'"
                                        data-iddetped="'.$rs['niddeta'].'"
                                        data-saldo="'.$saldo.'"
                                        data_pedido="'.$rs['nidpedi'].'"
                                        data_orden="'.$rs['id_orden'].'">
                                    <td class="textoCentro"><a href="'.$rs['id_orden'].'" data-accion="deleteItem" class="eliminarItem"><i class="fas fa-minus"></i></a></td>
                                    <td class="textoCentro"><input type="checkbox"></td>
                                    <td class="textoCentro">'.str_pad($item++,3,0,STR_PAD_LEFT).'</td>
                                    <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                    <td class="pl20px">'.$rs['cdesprod'].'</td>
                                    <td class="textoCentro">'.$rs['cabrevia'].'</td>
                                    <td class="textoDerecha pr20px">'.$rs['cantidad'].'</td>
                                    <td class="textoDerecha pr20px">'.number_format($cantidad_ingresada,2).'</td>
                                    <td>
                                        <input type="number" 
                                            step="any" 
                                            placeholder="0.00" 
                                            onchange="(function(el){el.value=parseFloat(el.value).toFixed(2);})(this)" value="'.$cantidad_ingresada.'">
                                    </td>
                                    <td class="textoDerecha pr20px">'. number_format($pendientes,2) .'</td>
                                    <td><input type="text"></td>
                                    <td class="textoCentro">'.$rs['pedido'].'</td>
                                    <td class="textoCentro">'.str_pad($rs['id_orden'],6,0,STR_PAD_LEFT).'</td>
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

        public function generarPdfSalida($cabecera,$detalles,$condicion){
            require_once("public/formatos/notasalida.php");
            try {
                $datos = json_decode($detalles);
                $nreg = count($datos);

                $fecha = explode("-",$cabecera['fecha']);

                $lc = 0;
                $rc = 0;

                $dia = $fecha[2];
                $mes = $fecha[1];
                $anio = $fecha[0];

                $cargo = "Jefe de Almacen";

                $file = uniqid("NS")."_".$cabecera['numero']."_".$cabecera['almacen_origen_despacho'].".pdf";

                if ($condicion == 0){
                    $filename = "public/documentos/notas_salida/vistaprevia/".$file;
                }else if ($condicion == 1){
                    $filename = "public/documentos/notas_salida/emitidas/".$file;
                }
                
                $pdf = new PDF($cabecera['numero'],$condicion,$dia,$mes,$anio,$cabecera['costos'],
                            $cabecera['almacen_origen_despacho'],$cabecera['almacen_destino_despacho'],
                            $cabecera['tipo'],$cabecera['guia'],$cabecera['aprueba'],$cargo);

                $pdf->AliasNbPages();
                $pdf->AddPage();
                $pdf->SetWidths(array(10,15,70,8,10,30,17,15,15));
                $pdf->SetFont('Arial','',4);

                for($i=1;$i<=$nreg;$i++){
                    $pdf->SetAligns(array("C","L","L","L","R","L","L","L","L"));
                    $pdf->Row(array(str_pad($i,3,"0",STR_PAD_LEFT),
                                            $datos[$rc]->codigo,
                                            'P:'.$datos[$rc]->orden.' '.'O:'.$datos[$rc]->pedido.' '.utf8_decode($datos[$rc]->descripcion),
                                            $datos[$rc]->unidad,
                                            $datos[$rc]->cantdesp,
                                            $datos[$rc]->obser,
                                            "",
                                            "",
                                            ""));
                    $lc++;
                    $rc++;
                    
                    if ($lc == 52) {
                        $pdf->AddPage();
                        $lc = 0;
                    }	
                }
                
            $pdf->Output($filename,'F');
                
            return $filename;
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }    
        }

        public function generarVistaPrevia($cabecera,$detalles,$proyecto){
            try {
                require_once("public/formatos/guiaremision.php");
                
                $archivo = "public/documentos/guias_remision/".$cabecera['numero_guia'].".pdf";
                $datos = json_decode($detalles);
                $nreg = count($datos);
                $fecha_emision = date("d/m/Y", strtotime($cabecera['fgemision']));
                $fecha_traslado = date("d/m/Y", strtotime($cabecera['ftraslado']));
                $referido = $this->generarRS(); 
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
                                $referido,
                                $proyecto,
                                $anio[0],
                                $cabecera["observaciones"],
                                $cabecera["destinatario"],
                                'A4');
                $pdf->AliasNbPages();
                $pdf->AddPage();
                $pdf->SetWidths(array(10,15,15,147));
                $pdf->SetFillColor(255,255,255);
                $pdf->SetTextColor(0,0,0);
                
                $pdf->SetFont('Arial','',7);
                $lc = 0;
                $rc = 0;

                //aca podria sumar la orden

                for($i=1;$i<=$nreg;$i++){

                    $pdf->SetX(13);
                    $pdf->SetCellHeight(3);
                    //$pdf->SetFont('Arial','',3);

                    $pdf->SetAligns(array("R","R","C","L"));
                    $pdf->Row(array(str_pad($i,3,"0",STR_PAD_LEFT),
                                    $datos[$rc]->cantdesp,
                                    $datos[$rc]->unidad,
                                    utf8_decode($datos[$rc]->codigo .' '. $datos[$rc]->descripcion  .' '.'O : '.$datos[$rc]->pedido.' P : '.$datos[$rc]->orden)));
                    $lc++;
                    $rc++;

                    if ($lc == 26) {
                        $pdf->AddPage();
                        $lc = 0;
                    }
                }

                $pdf->Ln(1);
                    $pdf->SetX(13);
                    //$pdf->MultiCell(190,2,utf8_decode($cabecera["observaciones"]));
                    $pdf->Ln(2);
                    $pdf->SetX(13);
                    $pdf->Output($archivo,'F');
                    
                    return array("archivo"=>$archivo);

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function imprimirFormato($cabecera,$detalles,$proyecto,$nro_despacho){
            try {
                require_once("public/formatos/grpreimpreso.php");
                
                $archivo = "public/documentos/temp/".uniqid().".pdf";
                $datos = json_decode($detalles);
                $nreg = count($datos);
                
                $fecha_emision = date("d/m/Y", strtotime($cabecera['fgemision']));
                
                if ($cabecera['ftraslado'] !== "")
                    $fecha_traslado = date("d/m/Y", strtotime($cabecera['ftraslado']));
                else 
                    $fecha_traslado = "";

                $referido = $this->generarRS(); 
                $anio = explode('-',$cabecera['fgemision']);

                $sql = $this->db->connect()->prepare("UPDATE alm_despachocab 
                                                        SET ffecenvio=:envio,nReferido=:referido,cnumguia=:guia,id_centi=:entidad
                                                        WHERE id_regalm =:despacho");
                
                $sql->execute(["envio"=>$cabecera['ftraslado'],
                                "referido"=>$referido,
                                "guia"=>$cabecera['numero_guia'],
                                "entidad"=>$cabecera['codigo_entidad_transporte'],
                                "despacho"=>$nro_despacho]);

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
                                $referido,
                                $proyecto,
                                $anio[0],
                                $cabecera["observaciones"],
                                $cabecera["destinatario"],
                                'A4');
                $pdf->AliasNbPages();
                $pdf->AddPage();
                $pdf->SetWidths(array(10,15,15,147));
                $pdf->SetFillColor(255,255,255);
                $pdf->SetTextColor(0,0,0);
                
                $pdf->SetFont('Arial','',9);
                $lc = 0;
                $rc = 0;

                //aca podria sumar la orden

                for($i=1;$i<=$nreg;$i++){

                    $pdf->SetX(3);
                    $pdf->SetCellHeight(3);
                    //$pdf->SetFont('Arial','',3);

                    $pdf->SetAligns(array("R","R","C","L"));
                    $pdf->Row(array(str_pad($i,3,"0",STR_PAD_LEFT),
                                    $datos[$rc]->cantdesp,
                                    $datos[$rc]->unidad,
                                    '  O : '.$datos[$rc]->pedido.' P : '.$datos[$rc]->orden .' '. utf8_decode($datos[$rc]->codigo .' '. $datos[$rc]->descripcion )));
                    $lc++;
                    $rc++;

                    if ($lc == 26) {
                        $pdf->AddPage();
                        $lc = 0;
                    }
                }

                $pdf->Ln(1);
                    
                    $pdf->Output($archivo,'F');
                    
                    return array("archivo"=>$archivo);

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function grabarDespacho($cabecera,$detalles){
            try {
                $mensaje = "Error al grabar el registro";
                $clase = "mensaje_error";
                $error = true;

                $query = "SELECT COUNT( alm_despachocab.id_regalm ) AS numero FROM alm_despachocab WHERE ncodalm1 =:cod";
                $nota = $this->generarNumero($cabecera["codigo_almacen_origen"],$query);
                $indice = $this->lastInsertId("SELECT COUNT(id_regalm) + 1 AS id FROM alm_despachocab");

                $fecha = explode("-",$cabecera['fecha']);

                $sql = $this->db->connect()->prepare("INSERT INTO alm_despachocab SET ntipmov = :ntipmov,
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
                                                                                        nflgactivo = :nflgactivo");

                $sql->execute(["ntipmov"=>$cabecera['codigo_movimiento'],
                                "nnromov"=>null,
                                "cper"=>$fecha[0],
                                "cmes"=>$fecha[1],
                                "ncodalm1"=>$cabecera['codigo_almacen_origen'],
                                "ncodalm2"=>$cabecera['codigo_almacen_destino'],
                                "ffecdoc"=>$cabecera['fecha'],
                                "ncodpry"=>$cabecera['codigo_costos'],
                                "nnronota"=>$nota['numero'],
                                "id_userAprob"=>$cabecera['codigo_aprueba'],
                                "nEstadoDoc"=>62,
                                "nflgactivo"=>1,
                                "id_user"=>$_SESSION['iduser']]);
                
                                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    $mensaje = "Registro grabado";
                    $clase = "mensaje_correcto";
                    $error = "false";
                    $this->grabarDetallesDespacho($indice,$detalles,$cabecera['codigo_almacen_origen']);
                    $this->actualizarDetallesPedido($indice,$detalles);
                }
                
                return array("mensaje"=>$mensaje, 
                             "clase"=>$clase,
                             "error"=>$error,
                             "indice"=>$indice);

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function grabarDetallesDespacho($id,$detalles,$almacen){
            try {
                $datos = json_decode($detalles);
                $nreg = count($datos);

                for ($i=0; $i < $nreg; $i++) { 
                    try {
                        $sql=$this->db->connect()->prepare("INSERT INTO alm_despachodet SET id_regalm=:cod,
                                                                                            ncodalm1=:ori,
                                                                                            id_cprod=:cpro,
                                                                                            ncantidad=:cant,
                                                                                            niddetaPed=:idpedido,
                                                                                            nflgactivo=:flag,
                                                                                            nestadoreg=:estadoItem,
                                                                                            ingreso=:ingreso,
                                                                                            ncodalm2=:destino,
                                                                                            niddetaIng=:itemIngreso,
                                                                                            nroorden=:orden,
                                                                                            nropedido=:pedido,
                                                                                            ndespacho=:candesp,
                                                                                            cobserva=:observac");
                         $sql->execute(["cod"=>$id,
                                        "ori"=>$almacen,
                                        "cpro"=>$datos[$i]->idprod,
                                        "cant"=>$datos[$i]->cantidad,
                                        "idpedido"=>$datos[$i]->iddetped,
                                        "flag"=>1,
                                        "estadoItem"=>49,
                                        "ingreso"=>null,
                                        "destino"=>$datos[$i]->destino,
                                        "candesp"=>$datos[$i]->cantdesp,
                                        "itemIngreso"=>null,
                                        "pedido"=>$datos[$i]->pedido,
                                        "orden"=>$datos[$i]->orden,
                                        "observac"=>$datos[$i]->obser]);
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

        private function actualizarDetallesPedido($despacho,$detalles){
            
            try {
                $datos = json_decode($detalles);
                $nreg = count($datos);

                for ($i=0; $i < $nreg; $i++) {
                    $sql = $this->db->connect()->prepare("UPDATE tb_pedidodet 
                                                            SET iddespacho=:despacho,
                                                                estadoItem=:estado
                                                            WHERE iditem=:id");
                    $sql->execute(["despacho"=>$despacho,
                                    "estado"=>62,
                                    "id"=>$datos[$i]->iddetped]);
                }

            } catch (PDOException $th) {
                        echo $th->getMessage();
                        return false;
            }
        }

        private function centroCostos($id){
            try {
                $sql=$this->db->connect()->prepare("SELECT UPPER(CONCAT_WS(' ',tb_proyectos.ccodproy,tb_proyectos.cdesproy)) AS nombre
                                                    FROM tb_proyectos
                                                    WHERE tb_proyectos.nidreg =:id");
                $sql->execute(["id"=>$id]);
                $result=$sql->fetchAll();

                return $result[0]['nombre'];

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function filtrarOrdenesID($id){
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
                                                        tb_proyectos.nidreg,
                                                        CONCAT_WS(
                                                            ' ',
                                                            tb_proyectos.ccodproy,
                                                        UPPER( tb_proyectos.cdesproy )) AS costos,
                                                        CONCAT_WS(
                                                            ' ',
                                                            tb_area.ccodarea,
                                                        UPPER( tb_area.cdesarea )) AS area,
                                                        cm_entidad.crazonsoc 
                                                    FROM
                                                        tb_costusu
                                                        INNER JOIN lg_ordencab ON tb_costusu.ncodproy = lg_ordencab.ncodpry
                                                        INNER JOIN tb_proyectos ON lg_ordencab.ncodpry = tb_proyectos.nidreg
                                                        INNER JOIN tb_area ON lg_ordencab.ncodarea = tb_area.ncodarea
                                                        INNER JOIN cm_entidad ON lg_ordencab.id_centi = cm_entidad.id_centi 
                                                    WHERE
                                                        tb_costusu.id_cuser = :usr
                                                        AND lg_ordencab.id_regmov = :id 
                                                        AND tb_costusu.nflgactivo = 1 
                                                        AND lg_ordencab.nEstadoDoc BETWEEN 60 AND 62
                                                    ORDER BY id_regmov DESC");
                $sql->execute(["usr"=>$_SESSION['iduser'],"id"=>$id]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()) {
                        //compara la orden si fue ingresada esta completa y no la muestra
                        $diferencia_ingreso = $this->calcularIngresosOrden($rs['id_regmov']) - $this->calcularCantidadDespacha($rs['id_regmov']);

                        if (($diferencia_ingreso) > 0 ) {
                            $salida.='<tr data-orden="'.$rs['id_regmov'].'" data-idcosto="'.$rs['nidreg'].'">
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

        public function generarRS() {
            try {
                $sql = $this->db->connect()->query("SELECT MAX(nReferido) AS rs FROM alm_despachocab");
                $sql->execute();
                $resultado = $sql->fetchAll();

                $rs = gettype($resultado[0]['rs']) == "NULL" ? 5000 : $resultado[0]['rs'];
                $rs = $rs+1; 

                return $rs;
            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }

        public function consultarSalidaId($indice){
            try {
                $sql=$this->db->connect()->prepare("SELECT
                                                    alm_despachocab.id_regalm,
                                                    alm_despachocab.ncodalm1,
                                                    alm_despachocab.ncodalm2,
                                                    DATE_FORMAT(
                                                        alm_despachocab.ffecdoc,
                                                        '%d/%m/%Y'
                                                    ) AS fecha_despacho,
                                                    alm_despachocab.ffecdoc,
                                                    alm_despachocab.cnumguia,
                                                    alm_despachocab.ncodpry,
                                                    alm_despachocab.nEstadoDoc,
                                                    UPPER(origen.cdesalm) AS origen,
                                                    UPPER(origen.ctipovia) AS direccion_origen,
                                                    UPPER(destino.cdesalm) AS destino,
                                                    UPPER(destino.ctipovia) AS direccion_destino,
                                                    UPPER(
                                                        CONCAT_WS(
                                                            ' ',
                                                            tb_proyectos.ccodproy,
                                                            tb_proyectos.cdesproy
                                                        )
                                                    ) AS costos,
                                                    alm_despachocab.id_userAprob,
                                                    tb_user.cnombres,
                                                    movimientos.nidreg,
                                                    movimientos.cdescripcion AS tipo_movimiento,
                                                    estado.cdescripcion AS estado
                                                FROM
                                                    alm_despachocab
                                                INNER JOIN tb_almacen AS origen ON alm_despachocab.ncodalm1 = origen.ncodalm
                                                INNER JOIN tb_almacen AS destino ON alm_despachocab.ncodalm2 = destino.ncodalm
                                                INNER JOIN tb_proyectos ON alm_despachocab.ncodpry = tb_proyectos.nidreg
                                                INNER JOIN tb_user ON alm_despachocab.id_userAprob = tb_user.iduser
                                                INNER JOIN tb_parametros AS movimientos ON alm_despachocab.ntipmov = movimientos.nidreg
                                                INNER JOIN tb_parametros AS estado ON alm_despachocab.nEstadoDoc = estado.nidreg
                                                WHERE
                                                    id_regalm = :indice");
                $sql->execute(["indice"=>$indice]);
                $docData = array();
                while($row=$sql->fetch(PDO::FETCH_ASSOC)){
                    $docData[] = $row;
                }

                return array("cabecera"=>$docData,
                            "detalles"=>$this->salidaDetalles($indice));
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }  
        }

        private function salidaDetalles($indice){
            try {
                //aca hubo un problema orden por pedido
                $salida="";
                $sql=$this->db->connect()->prepare("SELECT
                                                        alm_despachodet.id_regalm,
                                                        alm_despachodet.ncodalm1,
                                                        alm_despachodet.fvence,
                                                        alm_despachodet.ncantidad,
                                                        alm_despachodet.id_cprod,
                                                        alm_despachodet.niddetaPed,
                                                        alm_despachodet.niddetaOrd,
                                                        alm_despachodet.niddeta,
                                                        alm_despachodet.ndespacho,
                                                        alm_despachodet.cobserva,
                                                        alm_despachodet.niddetaIng,
                                                        LPAD(alm_despachodet.nropedido,6,0) AS orden,
                                                        LPAD(alm_despachodet.nroorden,6,0) AS pedido,
                                                        alm_despachodet.ingreso,
                                                        FORMAT(alm_despachodet.nsaldo, 2) AS nsaldo,
                                                        cm_producto.ccodprod,
                                                        FORMAT(alm_despachodet.ncantidad, 2) AS cantidad,
                                                        UPPER(
                                                            CONCAT_WS(
                                                                ' ',
                                                                cm_producto.cdesprod,
                                                                tb_pedidodet.observaciones
                                                            )
                                                        ) AS cdesprod,
                                                        tb_unimed.nfactor,
                                                        tb_unimed.cabrevia,
                                                        tb_pedidocab.nrodoc
                                                    FROM
                                                        alm_despachodet
                                                    INNER JOIN cm_producto ON alm_despachodet.id_cprod = cm_producto.id_cprod
                                                    INNER JOIN tb_pedidodet ON alm_despachodet.niddetaPed = tb_pedidodet.iditem
                                                    INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed
                                                    INNER JOIN tb_pedidocab ON alm_despachodet.nropedido = tb_pedidocab.idreg
                                                    WHERE
                                                        alm_despachodet.id_regalm = :id");
                $sql->execute(["id"=>$indice]);

                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    $item = 1;
                    while ($rs = $sql->fetch()){

                        $series = $this->buscarSeries($rs['id_cprod'],$rs['id_regalm'],$rs['ncodalm1']);
                        $pendiente = $rs['cantidad'] - $rs['ndespacho'];

                        $salida.='<tr   data-idorden="'.$rs['niddetaOrd'].'" 
                                        data-idpedido="'.$rs['niddetaPed'].'" 
                                        data-idingreso="'.$rs['niddetaIng'].'"
                                        data-iddespacho="'.$rs['niddeta'].'"
                                        data-idproducto ="'.$rs['id_cprod'].'"
                                        data-pedido ="'.$rs['pedido'].'"
                                        data-orden ="'.$rs['orden'].'">
                                        <td></td>
                                        <td class="textoCentro"><input type="checkbox" checked></td>
                                        <td class="textoCentro">'.str_pad($item,3,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                        <td class="pl20px">'.$rs['cdesprod'].'  :'.$series.'</td>
                                        <td class="textoCentro">'.$rs['cabrevia'].'</td>
                                        <td class="textoDerecha pr20px">'.$rs['cantidad'].'</td>
                                        <td class="textoDerecha pr20px">'.$rs['ndespacho'].'</td>
                                        <td><input type="number" step="any" onchange="(function(el){el.value=parseFloat(el.value).toFixed(2);})(this)"
                                        value="'.$rs['ndespacho'].'" ></td>
                                        <td class="textoDerecha pr20px">'.$pendiente.'</td>
                                        <td class="pr20px"><input type="text" value="'.$rs['cobserva'].'"></td>
                                        <td class="textoCentro">'.str_pad($rs['pedido'],6,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.str_pad($rs['orden'],6,0,STR_PAD_LEFT).'</td>
                                    </tr>';
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function filtrarNotasDespacho($parametros){
            try {

                $mes  = date("m");

                $guia   = $parametros['guiaSearch'] == "" ? "%" : "%".$parametros['guiaSearch']."%";
                $costos = $parametros['costosSearch'] == -1 ? "%" : "%".$parametros['costosSearch']."%";
                $mes    = $parametros['mesSearch'] == -1 ? $mes :  $parametros['mesSearch'];
                $anio   = $parametros['anioSearch'];

                $salida = "";
                $sql = $this->db->connect()->prepare("SELECT
                                                        alm_despachocab.id_regalm,
                                                        alm_despachocab.cmes,
                                                        DATE_FORMAT(
                                                            alm_despachocab.ffecdoc,
                                                            '%d/%m/%Y'
                                                        ) AS ffecdoc,
                                                        YEAR(ffecdoc) AS anio,
                                                        alm_despachocab.ncodpry,
                                                        UPPER(origen.cdesalm) AS origen,
                                                        UPPER(origen.ctipovia) AS direccion_origen,
                                                        alm_despachocab.nEstadoDoc,
                                                        alm_despachocab.cnumguia,
                                                        UPPER(destino.cdesalm) AS destino,
                                                        UPPER(destino.ctipovia) AS direccion_destino,
                                                        UPPER(
                                                            CONCAT_WS(
                                                                ' ',
                                                                tb_proyectos.ccodproy,
                                                                tb_proyectos.cdesproy
                                                                
                                                            )
                                                        ) AS costos,
                                                        tb_parametros.cdescripcion,
                                                        tb_parametros.cabrevia
                                                    FROM
                                                        tb_costusu
                                                    INNER JOIN alm_despachocab ON tb_costusu.ncodproy = alm_despachocab.ncodpry
                                                    INNER JOIN tb_almacen AS origen ON alm_despachocab.ncodalm1 = origen.ncodalm
                                                    INNER JOIN tb_almacen AS destino ON alm_despachocab.ncodalm2 = destino.ncodalm
                                                    INNER JOIN tb_proyectos ON alm_despachocab.ncodpry = tb_proyectos.nidreg
                                                    INNER JOIN tb_parametros ON alm_despachocab.nEstadoDoc = tb_parametros.nidreg
                                                    WHERE
                                                        tb_costusu.nflgactivo = 1
                                                        AND tb_costusu.id_cuser = :usr
                                                        AND alm_despachocab.nEstadoDoc = 62
                                                        AND alm_despachocab.ncodpry LIKE :costos 
                                                        AND alm_despachocab.cnumguia LIKE :guia 
                                                        AND MONTH ( alm_despachocab.ffecdoc ) = :mes
                                                        AND YEAR ( alm_despachocab.ffecdoc ) = :anio
                                                    ORDER BY alm_despachocab.ffecdoc ASC");
                $sql->execute(["usr"=>$_SESSION['iduser'],
                                "guia"=>$guia,
                                "costos"=>$costos,
                                "mes"=>$mes,
                                "anio"=>$anio]);

                $rowCount = $sql->rowcount();
                if ($rowCount > 0){
                    while($rs = $sql->fetch()){
                        $salida.='<tr data-indice="'.$rs['id_regalm'].'" class="pointer">
                        <td class="textoCentro">'.str_pad($rs['id_regalm'],6,0,STR_PAD_LEFT).'</td>
                        <td class="textoCentro">'.$rs['ffecdoc'].'</td>
                        <td class="textoCentro">'.$rs['origen'].'</td>
                        <td class="pl20px">'.$rs['destino'].'</td>
                        <td class="pl20px">'.$rs['costos'].'</td>
                        <td class="textoCentro">'.$rs['anio'].'</td>
                        <td class="textoCentro">'.$rs['cnumguia'].'</td>
                        <td class="textoCentro '.$rs['cabrevia'].'">'.$rs['cdescripcion'].'</td>
                    </tr>';
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }

        public function calcularSaldosItemsDespachados($orden,$idprod){
            try {
                $sql = $this->db->connect()->prepare("SELECT SUM(ndespacho) AS totalItemDespachado 
                                                        FROM alm_despachodet 
                                                        WHERE nroorden = :orden AND id_cprod = :producto");
                $sql->execute(["orden"=>$orden,"producto"=>$idprod]);
                $result = $sql->fetchAll();

                return $result[0]['totalItemDespachado'];
            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }

        private function ingresosRegistrados($orden,$idOrden){
            try {
                $sql = $this->db->connect()->prepare("SELECT 
                                                        FORMAT(SUM(ncantidad),2) AS  totalItemIngresado 
                                                        FROM alm_recepdet 
                                                        WHERE pedido = :orden AND niddetaOrd=:item");
                $sql->execute(["orden"=>$orden,"item"=>$idOrden]);
                $result = $sql->fetchAll();

                return $result[0]['totalItemIngresado'];
            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }

    } 
?>