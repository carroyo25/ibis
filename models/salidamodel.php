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
                                                        alm_despachocab.nEstadoDoc,
                                                        alm_despachocab.cnumguia,
                                                        UPPER(destino.cdesalm) AS destino,
                                                        UPPER(
                                                            CONCAT_WS(
                                                                ' ',
                                                                tb_proyectos.cdesproy,
                                                                tb_proyectos.ccodproy
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
            $indice = $this->lastInsertId("SELECT MAX(id_regalm) AS id FROM alm_despachocab"); 
            $indice = gettype($indice) == "NULL" ? 1 : $indice;

            return str_pad($indice,6,0,STR_PAD_LEFT);
        }

        public function listarIngresos(){
            try {
                $salida = "";
                $sql = $this->db->connect()->prepare("SELECT
                                                        alm_recepcab.id_regalm,
                                                        alm_recepcab.nnronota,
                                                        alm_recepcab.cnumguia,
                                                        tb_proyectos.nidreg,
                                                        alm_recepcab.idref_pedi AS pedido,
                                                         alm_recepcab.idref_abas AS orden,
                                                        CONCAT_WS(' ',tb_proyectos.ccodproy,tb_proyectos.cdesproy) AS proyecto,
                                                        tb_parametros.cdescripcion,
                                                        UPPER( tb_almacen.cdesalm ) AS almacen,
                                                        UPPER( tb_area.cdesarea ) AS area,
                                                        DATE_FORMAT(alm_recepcab.ffecdoc,'%d/%m/%Y') AS fecha  
                                                    FROM
                                                        tb_costusu
                                                        INNER JOIN alm_recepcab ON tb_costusu.ncodproy = alm_recepcab.ncodpry
                                                        INNER JOIN tb_proyectos ON alm_recepcab.ncodpry = tb_proyectos.nidreg
                                                        INNER JOIN tb_parametros ON alm_recepcab.ncodmov = tb_parametros.nidreg
                                                        INNER JOIN tb_almacen ON alm_recepcab.ncodalm1 = tb_almacen.ncodalm
                                                        INNER JOIN tb_area ON alm_recepcab.ncodarea = tb_area.ncodarea 
                                                    WHERE
                                                        tb_costusu.id_cuser = :user 
                                                        AND tb_costusu.nflgactivo = 1 
                                                        AND alm_recepcab.nEstadoDoc = 60
                                                    ORDER BY tb_proyectos.ccodproy");
                $sql->execute(["user"=>$_SESSION['iduser']]);
                $rowCount = $sql->rowCount();

                $item = 1;

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()) {
                        $salida .= '<tr class="pointer" data-pedido="'.$rs['pedido'].'"
                                                        data-orden="'.$rs['orden'].'"
                                                        data-ingreso="'.$rs['id_regalm'].'"
                                                        data-costos="'.$rs['nidreg'].'">
                                        <td class="textoCentro"><input type="checkbox"></td>
                                        <td class="textoCentro">'.$rs['fecha'].'</td>
                                        <td class="pl20px">'.$rs['proyecto'].'</td>
                                        <td class="textoCentro">'.str_pad($rs['pedido'],6,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.str_pad($rs['orden'],6,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.$rs['nnronota'].'</td>
                                        <td class="pl20px">'.$rs['area'].'</td>
                                        <td class="pl20px">'.$rs['cdescripcion'].'</td>
                                        <td class="pl20px">'.$rs['almacen'].'</td>
                                    </tr>';
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }
        
        public function filtrarIngresos($id) {
            $salida = "";
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                        alm_recepcab.id_regalm,
                                                        alm_recepcab.nnronota,
                                                        alm_recepcab.cnumguia,
                                                        alm_recepcab.idref_pedi AS pedido,
                                                        alm_recepcab.idref_abas AS orden,
                                                        tb_proyectos.nidreg,
                                                        CONCAT_WS(' ',tb_proyectos.ccodproy,tb_proyectos.cdesproy) AS proyecto,
                                                        tb_parametros.cdescripcion,
                                                        UPPER( tb_almacen.cdesalm ) AS almacen,
                                                        UPPER( tb_area.cdesarea ) AS area,
                                                        DATE_FORMAT(alm_recepcab.ffecdoc,'%d/%m/%Y') AS fecha  
                                                    FROM
                                                        tb_costusu
                                                        INNER JOIN alm_recepcab ON tb_costusu.ncodproy = alm_recepcab.ncodpry
                                                        INNER JOIN tb_proyectos ON alm_recepcab.ncodpry = tb_proyectos.nidreg
                                                        INNER JOIN tb_parametros ON alm_recepcab.ncodmov = tb_parametros.nidreg
                                                        INNER JOIN tb_almacen ON alm_recepcab.ncodalm1 = tb_almacen.ncodalm
                                                        INNER JOIN tb_area ON alm_recepcab.ncodarea = tb_area.ncodarea 
                                                    WHERE
                                                        tb_costusu.id_cuser = :usr 
                                                        AND tb_costusu.nflgactivo = 1 
                                                        AND alm_recepcab.nEstadoDoc = 62
                                                        AND alm_recepcab.id_regalm = :id
                                                    ORDER BY tb_proyectos.ccodproy");
                $sql->execute(["usr"=>$_SESSION['iduser'],'id'=>$id]);
                $rowCount = $sql->rowCount();
                $item = 1;

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()){
                        $salida .= '<tr class="pointer" data-pedido="'.$rs['pedido'].'"
                                                       data-orden="'.$rs['orden'].'"
                                                       data-ingreso="'.$rs['ingreso'].'"
                                                       data-costos="'.$rs['nidreg'].'">
                                        <td class="textoCentro"><input type="checkbox"></td>
                                        <td class="textoCentro">'.$rs['fecha'].'</td>
                                        <td class="pl20px">'.$rs['proyecto'].'</td>
                                        <td class="textoCentro">'.str_pad($rs['pedido'],6,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.str_pad($rs['orden'],6,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.$rs['nnronota'].'</td>
                                        <td class="pl20px">'.$rs['area'].'</td>
                                        <td class="pl20px">'.$rs['cdescripcion'].'</td>
                                        <td class="pl20px">'.$rs['almacen'].'</td>
                                    </tr>';
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function importarItems($data){
            $indices = implode($data);
            $indices = str_replace("[","(",$indices);
            $indices = str_replace("]",")",$indices);

            $salida = "";
            $qry = "SELECT
                        alm_recepdet.niddeta,
                        cm_producto.ccodprod,
                        cm_producto.id_cprod,
                        UPPER(cm_producto.cdesprod) AS descripcion,
                        tb_unimed.cabrevia AS unidad,
                        tb_pedidodet.observaciones,
                        tb_pedidodet.iditem AS iditem,
                        LPAD(tb_pedidocab.nrodoc, 6, 0) AS pedido,
                        REPLACE (
                            FORMAT(lg_ordendet.ncanti, 2),
                            '',
                            ','
                        ) AS cantidad,
                        tb_almacen.cdesalm,
                        alm_recepcab.nnronota AS ingreso,
                        lg_ordencab.id_regmov AS idorden,
                        LPAD(lg_ordencab.cnumero, 6, 0) AS orden
                    FROM
                        alm_recepdet
                    INNER JOIN cm_producto ON alm_recepdet.id_cprod = cm_producto.id_cprod
                    INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed
                    INNER JOIN tb_pedidodet ON alm_recepdet.niddetaPed = tb_pedidodet.iditem
                    INNER JOIN tb_pedidocab ON tb_pedidodet.idpedido = tb_pedidocab.idreg
                    INNER JOIN lg_ordendet ON alm_recepdet.niddetaOrd = lg_ordendet.nitemord
                    INNER JOIN tb_almacen ON alm_recepdet.ncodalm1 = tb_almacen.ncodalm
                    INNER JOIN alm_recepcab ON alm_recepdet.id_regalm = alm_recepcab.id_regalm
                    INNER JOIN lg_ordencab ON tb_pedidocab.idorden = lg_ordencab.id_regmov
                    WHERE
                        alm_recepdet.id_regalm IN $indices";

            try {
                $sql = $this->db->connect()->query($qry);
                $sql->execute();
                $rowCount = $sql->rowCount();
                $item=1;
                $numero = $this->ultimoIndice();

                if ($rowCount > 0){
                    while ($rs = $sql->fetch()) {
                        $salida .= '<tr class="pointer" data-pedido="'.$rs['pedido'].'" 
                                                        data-orden="'.$rs['orden'].'" 
                                                        data-ingreso="'.$rs['ingreso'].'" 
                                                        data-despacho="'.$numero.'"
                                                        data-idpedido="'.$rs['iditem'].'"
                                                        data-idproducto="'.$rs['id_cprod'].'">
                                    <td class="textoCentro"><a href="'.$rs['niddeta'].'" data-accion="deleteItem" class="eliminarItem"><i class="fas fa-minus"></i></a></td>
                                    <td class="textoCentro">'.str_pad($item++,3,0,STR_PAD_LEFT).'</td>
                                    <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                    <td class="pl20px">'.$rs['descripcion'].'</td>
                                    <td class="textoCentro">'.$rs['unidad'].'</td>
                                    <td class="textoDerecha pr5px">'.$rs['cantidad'].'</td>
                                    <td><input type="number" value="'.$rs['cantidad'].'"></td>
                                    <td><input type="text"></td>
                                    <td class="textoCentro">'.$rs['pedido'].'</td>
                                    <td class="textoCentro">'.$rs['orden'].'</td>
                                    <td class="textoCentro">'.$rs['ingreso'].'</td>
                                </tr>';
                    }
                }

                

                return array("items" => $salida,
                            "numero" => $numero);
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
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

                //$cargo = $this->rrhhCargo($cabecera['codigo_aprueba']);
                //aca probar el api
                $cargo = "Jefe de Almacen";

                $file = uniqid("NS")."_".$cabecera['numero']."_".$cabecera['codigo_almacen'].".pdf";

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
                                            utf8_decode($datos[$rc]->descripcion),
                                            $datos[$rc]->unidad,
                                            $datos[$rc]->cantidad,
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

        public function generarVistaPrevia($cabecera,$detalles){
            try {
                require_once("public/formatos/guiaremision.php");
                
                $archivo = "public/documentos/guias_remision/".$cabecera['numero_guia'].".pdf";
                $datos = json_decode($detalles);
                $nreg = count($datos);

                $pdf = new PDF($cabecera['numero_guia'],$cabecera['fgemision'],$cabecera['destinatario_ruc'],$cabecera['destinatario_razon'],$cabecera['destinatario_direccion'],
                                $cabecera['empresa_transporte_razon'],$cabecera['ruc_entidad_transporte'],$cabecera['direccion_entidad_transporte'],
                                $cabecera['almacen_origen_direccion'],null,
                                null,null,$cabecera['fgemision'],$cabecera['modalidad_traslado'],$cabecera['almacen_destino_direccion'],null,
                                null,null,$cabecera['marca'],$cabecera['placa'],$cabecera['nombre_conductor'],$cabecera['licencia_conducir'],'A4');
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
                    $pdf->SetCellHeight(4);

                    $pdf->SetAligns(array("R","R","C","L"));
                    $pdf->Row(array(str_pad($i,3,"0",STR_PAD_LEFT),
                                    $datos[$rc]->cantidad,
                                    $datos[$rc]->unidad,
                                    utf8_decode($datos[$rc]->codigo .' '. $datos[$rc]->descripcion  .' '.'P : '.$datos[$rc]->pedido.' O : '.$datos[$rc]->orden)));
                    $lc++;
                    $rc++;

                    if ($lc == 23) {
                        $pdf->AddPage();
                        $lc = 0;
                    }
                }

                $pdf->Ln(1);
                    $pdf->SetX(13);
                    $pdf->MultiCell(190,2,utf8_decode($cabecera["observaciones"]));
                    $pdf->Ln(2);
                    $pdf->SetX(13);
                    $pdf->Output($archivo,'F');
                    
                    return array("archivo"=>$archivo);

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function imprimirFormato($cabecera,$detalles){
            try {
                require_once("public/formatos/grpreimpreso.php");
                
                $archivo = "public/documentos/temp/".uniqid().".pdf";
                $datos = json_decode($detalles);
                $nreg = count($datos);

                $pdf = new PDF($cabecera['numero_guia'],$cabecera['fgemision'],$cabecera['destinatario_ruc'],$cabecera['destinatario_razon'],$cabecera['destinatario_direccion'],
                                $cabecera['empresa_transporte_razon'],$cabecera['ruc_entidad_transporte'],$cabecera['direccion_entidad_transporte'],
                                $cabecera['almacen_origen_direccion'],null,
                                null,null,$cabecera['fgemision'],$cabecera['modalidad_traslado'],$cabecera['almacen_destino_direccion'],null,
                                null,null,$cabecera['marca'],$cabecera['placa'],$cabecera['nombre_conductor'],$cabecera['licencia_conducir'],'A4');
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
                    $pdf->SetCellHeight(4);

                    $pdf->SetAligns(array("R","R","C","L"));
                    $pdf->Row(array(str_pad($i,3,"0",STR_PAD_LEFT),
                                    $datos[$rc]->cantidad,
                                    $datos[$rc]->unidad,
                                    utf8_decode($datos[$rc]->codigo .' '. $datos[$rc]->descripcion  .' '.'P : '.$datos[$rc]->pedido.' O : '.$datos[$rc]->orden)));
                    $lc++;
                    $rc++;

                    if ($lc == 23) {
                        $pdf->AddPage();
                        $lc = 0;
                    }
                }

                $pdf->Ln(1);
                    $pdf->SetX(13);
                    $pdf->MultiCell(190,2,utf8_decode($cabecera["observaciones"]));
                    $pdf->Ln(2);
                    $pdf->SetX(13);
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
                $numero = $this->generarNumero($cabecera["codigo_almacen"],$query);
                $indice = $this->lastInsertId("SELECT MAX(id_regalm) AS id FROM alm_despachocab");
                $indice = gettype($indice) == "NULL" ? 1 : $indice;

                $fecha = explode("-",$cabecera['fecha']);

                $sql = $this->db->connect()->prepare("INSERT INTO alm_despachocab SET ntipmov = :ntipmov,
                                                                                        nnromov = :nnromov,
                                                                                        cper = :cper,
                                                                                        cmes = :cmes,
                                                                                        ncodalm1 = :ncodalm1,
                                                                                        ncodalm2 = :ncodalm2,
                                                                                        ffecdoc = :ffecdoc,
                                                                                        ncodpry = :ncodpry,
                                                                                        ncodarea = :ncodarea,
                                                                                        idref_pedi = :idref_pedi,
                                                                                        idref_ord=:idref_ord,
                                                                                        idref_abas=:idref_abas,
                                                                                        nnronota=:nnronota,
                                                                                        id_userAprob = :id_userAprob,
                                                                                        nEstadoDoc = :nEstadoDoc,
                                                                                        nflgactivo = :nflgactivo,
                                                                                        cnumguia=:nguia");

                $sql->execute(["ntipmov"=>$cabecera['codigo_movimiento'],
                                "nnromov"=>$cabecera['movimiento'],
                                "cper"=>$fecha[0],
                                "cmes"=>$fecha[1],
                                "ncodalm1"=>$cabecera['codigo_almacen'],
                                "ncodalm2"=>$cabecera['codigo_almacen_destino'],
                                "ffecdoc"=>$cabecera['fecha'],
                                "ncodpry"=>$cabecera['codigo_costos'],
                                "ncodarea"=>null,
                                "idref_pedi"=>$cabecera['pedido'],
                                "idref_ord"=>$cabecera['orden'],
                                "idref_abas"=>$cabecera['ingreso'],
                                "nnronota"=>$numero['numero'],
                                "id_userAprob"=>$cabecera['codigo_aprueba'],
                                "nEstadoDoc"=>62,
                                "nflgactivo"=>1,
                                "nguia"=>null]);
                
                                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {

                    $mensaje = "Registro grabado";
                    $clase = "mensaje_correcto";
                    $error = "false";
                    $indice = $this->lastInsertId("SELECT MAX(id_regalm) AS id FROM alm_despachocab");
                    $indice = gettype($indice) == "NULL" ? 1 : $indice;
                    $this->grabarDetallesDespacho($indice,$detalles,$cabecera['codigo_almacen']);
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
                                                                                            cSerie=:ser,
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
                                        "ser"=>null,
                                        "idpedido"=>$datos[$i]->iddetped,
                                        "idorden"=>null,
                                        "flag"=>1,
                                        "estadoItem"=>49,
                                        "ingreso"=>$datos[$i]->ingreso,
                                        "saldo"=>null,
                                        "destino"=>$datos[$i]->destino,
                                        "candesp"=>$datos[$i]->cantdesp,
                                        "itemIngreso"=>null,
                                        "pedido"=>$datos[$i]->pedido,
                                        "orden"=>$datos[$i]->orden,
                                        "observac"=>$datos[$i]->obser
                                        ]);
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
    } 
?>