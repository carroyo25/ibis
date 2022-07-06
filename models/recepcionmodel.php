<?php
    class RecepcionModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarNotas(){
            try {
                $salida = "";
                $sql = $this->db->connect()->prepare("SELECT
                                                        tb_costusu.id_cuser,
                                                        tb_costusu.ncodproy,
                                                        alm_recepcab.id_regalm,
                                                        alm_recepcab.ncodmov,
                                                        alm_recepcab.nnromov,
                                                        alm_recepcab.nnronota,
                                                        alm_recepcab.cper,
                                                        alm_recepcab.cmes,
                                                        alm_recepcab.ncodalm1,
                                                        alm_recepcab.ffecdoc,
                                                        alm_recepcab.id_centi,
                                                        alm_recepcab.cnumguia,
                                                        alm_recepcab.ncodpry,
                                                        alm_recepcab.ncodarea,
                                                        alm_recepcab.ncodcos,
                                                        alm_recepcab.idref_pedi,
                                                        alm_recepcab.idref_abas,
                                                        alm_recepcab.nEstadoDoc,
                                                        alm_recepcab.nflgCalidad,
                                                        UPPER( tb_almacen.cdesalm ) AS almacen,
                                                        UPPER( tb_proyectos.cdesproy ) AS proyecto,
                                                        UPPER( tb_area.cdesarea ) AS area,
                                                        lg_ordencab.cnumero AS orden,
                                                        LPAD( tb_pedidocab.nrodoc, 6, 0 ) pedido 
                                                    FROM
                                                        tb_costusu
                                                        INNER JOIN alm_recepcab ON tb_costusu.ncodproy = alm_recepcab.ncodpry
                                                        INNER JOIN tb_almacen ON alm_recepcab.ncodalm1 = tb_almacen.ncodalm
                                                        INNER JOIN tb_proyectos ON alm_recepcab.ncodpry = tb_proyectos.nidreg
                                                        INNER JOIN tb_area ON alm_recepcab.ncodarea = tb_area.ncodarea
                                                        INNER JOIN lg_ordencab ON alm_recepcab.idref_abas = lg_ordencab.id_regmov
                                                        INNER JOIN tb_pedidocab ON alm_recepcab.idref_pedi = tb_pedidocab.idreg 
                                                    WHERE
                                                        tb_costusu.id_cuser = :usr 
                                                        AND tb_costusu.nflgactivo = 1
                                                        AND alm_recepcab.nEstadoDoc = 60");
                $sql->execute(["usr"=>$_SESSION['iduser']]);
                $rowCount = $sql->rowcount();
                if ($rowCount > 0){
                    while($rs = $sql->fetch()){
                        $salida.='<tr class="pointer" data-indice="'.$rs['id_regalm'].'">
                                    <td class="textoCentro">'.$rs['cnumguia'].'</td>
                                    <td class="textoCentro">'.date("d/m/Y", strtotime($rs['ffecdoc'])).'</td>
                                    <td class="pl20px">'.$rs['almacen'].'</td>
                                    <td class="pl20px">'.$rs['proyecto'].'</td>
                                    <td class="pl20px">'.$rs['area'].'</td>
                                    <td class="textoCentro">'.$rs['orden'].'</td>
                                    <td class="textoCentro">'.$rs['pedido'].'</td>
                                </tr>';
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }

        public function insertar($cabecera,$detalles,$series){
            try {
               
                $fecha = explode("-",$cabecera['fecha']); 
                
                $calidad = array_key_exists('qaqc', $cabecera)? 1 : 0;

                $nota = $this->generarNumero($cabecera["codigo_almacen"],"SELECT COUNT( alm_recepcab.id_regalm ) AS numero FROM alm_recepcab WHERE ncodalm1 =:cod");

                $sql = $this->db->connect()->prepare("INSERT INTO alm_recepcab SET ctipmov =:mov,cper=:anio,cmes=:mes,ncodalm1=:almacen,ffecdoc=:emision,
                                                                                    id_centi=:entidad,cnumguia=:guia,idref_pedi=:pedido,id_userAprob=:aprueba,
                                                                                    nEstadoDoc=:estado,nflgactivo=:activo,nnronota=:nota,idref_abas=:orden,
                                                                                    ncodpry=:costos,ncodarea=:area,nflgCalidad=:calidad,nnromov=:movimiento,
                                                                                    ncodmov=:codigo_movimiento");
                $sql->execute(["mov"=>"I",
                                "anio"=>$fecha[0],
                                "mes"=>$fecha[1],
                                "almacen"=>$cabecera['codigo_almacen'],
                                "emision"=>$cabecera['fecha'],
                                "entidad"=>$cabecera['codigo_entidad'],
                                "guia"=>$cabecera['guia'],
                                "pedido"=>$cabecera['codigo_pedido'],
                                "aprueba"=>$cabecera['codigo_aprueba'],
                                "estado"=>$cabecera['codigo_estado'],
                                "activo"=>1,
                                "nota"=>$nota['numero'],
                                "orden"=>$cabecera['codigo_orden'],
                                "costos"=>$cabecera['codigo_costos'],
                                "area"=>$cabecera['codigo_area'],
                                "calidad"=>$calidad,
                                "movimiento"=>1,
                                "codigo_movimiento"=>$cabecera['codigo_movimiento']]);
                $rowCount = $sql->rowCount();
                
                if ($rowCount > 0) {
                    $indice = $this->lastInsertId("SELECT MAX(id_regalm) AS id FROM alm_recepcab");
                    $this->grabarDetalles($indice,$detalles);
                    $this->grabarSeries($indice,$series);
                }

                return $indice;

            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
            }
        }

        private function grabarDetalles($id,$detalles){
            $datos = json_decode($detalles);
            $nreg = count($datos);

            for ($i=0; $i < $nreg; $i++) { 
                try {
                        $sql = $this->db->connect()->prepare("INSERT INTO alm_recepdet SET id_regalm=:id,ncodalm1=:almacen,id_cprod=:cprod,ncantidad=:cantidad,
                                                                                            niddetaPed=:itempedido,niddetaOrd=:itemorden,nflgactivo=:flag,
                                                                                            nsaldo=:saldo,cObserva=:observacion,fVence=:vencimiento,
                                                                                            nestadoreg=:estado");
                        $sql ->execute(["id"=>$id,
                                        "almacen"=>$datos[$i]->almacen,
                                        "cprod"=>$datos[$i]->idprod,
                                        "cantidad"=>$datos[$i]->cantrec,
                                        "itempedido"=>$datos[$i]->iddetped,
                                        "itemorden"=>$datos[$i]->iddetorden,
                                        "flag"=>1,
                                        "saldo"=>$datos[$i]->cantsol-$datos[$i]->cantrec,
                                        "observacion"=>$datos[$i]->obser,
                                        "vencimiento"=>$datos[$i]->vence,
                                        "estado"=>$datos[$i]->nestado]);
                   
                } catch (PDOException $th) {
                    echo "Error: ".$th->getMessage();
                    return false;
                }
            }
        }

        private function grabarSeries($id,$series) {
            try {
                $datos = json_decode($series);
                $nreg = count($datos);

                for ($i=0; $i < $nreg; $i++) { 
                    $sql= $this->db->connect()->prepare("INSERT INTO alm_recepserie SET id_cprod=:cprod,idref_movi=:nota,idref_alma=:almacen,
                                                                                        cdesserie=:serie");
                     $sql ->execute(["cprod"=>$datos[$i]->producto,
                                    "almacen"=>$datos[$i]->almacen,
                                    "nota"=>$id,
                                    "serie"=>$datos[$i]->serie]);
                }
            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }

        public function listarOrdenes(){
            try {
                $salida = "";
                $sql = $this->db->connect()->prepare("SELECT
                                                    ibis.lg_ordencab.id_regmov,
                                                    ibis.tb_costusu.ncodproy, 
                                                    ibis.lg_ordencab.id_refpedi, 
                                                    ibis.lg_ordencab.ntipdoc, 
                                                    ibis.lg_ordencab.cnumero, 
                                                    ibis.lg_ordencab.ffechadoc, 
                                                    ibis.lg_ordencab.nEstadoDoc, 
                                                    CONCAT_WS(' ',ibis.tb_proyectos.ccodproy,UPPER(ibis.tb_proyectos.cdesproy)) AS costos, 
                                                    CONCAT_WS(' ',ibis.tb_area.ccodarea,UPPER(ibis.tb_area.cdesarea)) AS area
                                                FROM
                                                    ibis.tb_costusu
                                                    INNER JOIN
                                                    ibis.lg_ordencab
                                                    ON 
                                                        ibis.tb_costusu.ncodproy = ibis.lg_ordencab.ncodpry
                                                    INNER JOIN
                                                    ibis.tb_proyectos
                                                    ON 
                                                        ibis.lg_ordencab.ncodpry = ibis.tb_proyectos.nidreg
                                                    INNER JOIN
                                                    ibis.tb_area
                                                    ON 
                                                        ibis.lg_ordencab.ncodarea = ibis.tb_area.ncodarea
                                                WHERE
                                                    ibis.tb_costusu.id_cuser = :usr AND
                                                    ibis.tb_costusu.nflgactivo = 1 AND
                                                    ibis.lg_ordencab.nEstadoDoc = 60");
                $sql->execute(["usr"=>$_SESSION['iduser']]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()) {
                        $salida.='<tr data-orden="'.$rs['id_regmov'].'">
                                    <td class="textoCentro">'.$rs['cnumero'].'</td>
                                    <td class="textoCentro">'.$rs['ffechadoc'].'</td>
                                    <td class="pl20px">'.$rs['area'].'</td>
                                    <td class="pl20px">'.$rs['costos'].'</td>
                                </tr>';
                    }
                }
                return $salida;
            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }

        public function consultarOrdenIdRecepcion($id){
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                        ibis.lg_ordencab.id_regmov,
                                                        ibis.lg_ordencab.cnumero,
                                                        ibis.lg_ordencab.ffechadoc,
                                                        ibis.lg_ordencab.ncodcos,
                                                        ibis.lg_ordencab.ncodarea,
                                                        ibis.lg_ordencab.id_centi,
                                                        ibis.lg_ordencab.ncodcot,
                                                        ibis.lg_ordencab.cnumcot,
                                                        ibis.lg_ordencab.nEstadoDoc,
                                                        ibis.lg_ordencab.id_refpedi,
                                                        UPPER( tb_pedidocab.concepto ) AS concepto,
                                                        UPPER( tb_pedidocab.detalle ) AS detalle,
                                                        UPPER(
                                                        CONCAT_WS( ' ', tb_proyectos.ccodproy, tb_proyectos.cdesproy )) AS costos,
                                                        ibis.lg_ordencab.ncodpry,
                                                        UPPER(
                                                        CONCAT_WS( ' ', tb_area.ccodarea, tb_area.cdesarea )) AS area,
                                                        ibis.lg_ordencab.ncodmon,
                                                        ibis.lg_ordencab.ntipmov,
                                                        ibis.lg_ordencab.ffechaent,
                                                        ibis.cm_entidad.crazonsoc,
                                                        ibis.cm_entidad.cnumdoc,
                                                        UPPER( tb_almacen.cdesalm ) AS cdesalm,
                                                        ibis.cm_entidad.cemail AS mail_entidad,
                                                        ibis.lg_ordencab.cverificacion,
                                                        LPAD(ibis.tb_pedidocab.nrodoc,6,0) AS pedido,
                                                        ibis.tb_pedidocab.nivelAten,
                                                        CONCAT_WS(' ',rrhh.tabla_aquarius.nombres,rrhh.tabla_aquarius.apellidos) AS solicita
                                                            FROM
                                                            ibis.lg_ordencab
                                                            INNER JOIN ibis.tb_pedidocab ON lg_ordencab.id_refpedi = tb_pedidocab.idreg
                                                            INNER JOIN ibis.tb_proyectos ON lg_ordencab.ncodcos = tb_proyectos.nidreg
                                                            INNER JOIN ibis.tb_area ON lg_ordencab.ncodarea = tb_area.ncodarea
                                                            INNER JOIN ibis.tb_parametros AS monedas ON lg_ordencab.ncodmon = monedas.nidreg
                                                            INNER JOIN ibis.tb_parametros AS tipos ON lg_ordencab.ntipmov = tipos.nidreg
                                                            INNER JOIN ibis.tb_parametros AS pagos ON lg_ordencab.ncodpago = pagos.nidreg
                                                            INNER JOIN ibis.tb_parametros AS estados ON lg_ordencab.nEstadoDoc = estados.nidreg
                                                            INNER JOIN ibis.cm_entidad ON lg_ordencab.id_centi = cm_entidad.id_centi
                                                            INNER JOIN ibis.tb_parametros AS transportes ON lg_ordencab.ctiptransp = transportes.nidreg
                                                            INNER JOIN ibis.tb_almacen ON lg_ordencab.ncodalm = tb_almacen.ncodalm
                                                            INNER JOIN rrhh.tabla_aquarius ON ibis.tb_pedidocab.idsolicita = rrhh.tabla_aquarius.internal 
                                                            WHERE
                                                        lg_ordencab.id_regmov =:id 
                                                        AND lg_ordencab.nflgactivo = 1");
                $sql->execute(["id"=>$id]);
                $docData = array();
                while($row=$sql->fetch(PDO::FETCH_ASSOC)){
                    $docData[] = $row;
                }

                return array("cabecera"=>$docData,
                            "detalles"=>$this->ordenDetalles($id));
            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }

        private function ordenDetalles($id) {
            try {
                $salida ="";
                $estados = $this->listarSelect(13,96);

                $sql = $this->db->connect()->prepare("SELECT
                                                lg_ordendet.nitemord,
                                                lg_ordendet.id_regmov,
                                                lg_ordendet.niddeta,
                                                lg_ordendet.nidpedi,
                                                lg_ordendet.id_cprod,
                                                cm_producto.ccodprod,
                                                UPPER(CONCAT_WS(' ',cm_producto.cdesprod,tb_pedidodet.observaciones,tb_pedidodet.docEspec)) AS cdesprod,
                                                cm_producto.nund,
                                                tb_unimed.cabrevia,
                                                tb_pedidodet.idpedido,
                                                tb_pedidodet.nroparte,
                                                FORMAT(lg_ordendet.ncanti,2) AS cantidad
                                            FROM
                                                lg_ordendet
                                                INNER JOIN cm_producto ON lg_ordendet.id_cprod = cm_producto.id_cprod
                                                INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed
                                                INNER JOIN tb_pedidodet ON lg_ordendet.niddeta = tb_pedidodet.iditem 
                                            WHERE
                                                lg_ordendet.nitemord =:id");
                $sql->execute(["id"=>$id]);
                
                $rowCount = $sql->rowCount();
                if ($rowCount > 0) {
                    $item=1;
                    while ($rs = $sql->fetch()){
                        $salida.='<tr data-detorden="'.$rs['nitemord'].'" 
                                        data-idprod="'.$rs['id_cprod'].'"
                                        data-iddetped="'.$rs['nidpedi'].'">

                                    <td class="textoCentro"><a href="'.$rs['nitemord'].'"><i class="fas fa-barcode"></i></a></td>
                                    <td class="textoCentro">'.str_pad($item++,3,0,STR_PAD_LEFT).'</td>
                                    <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                    <td class="pl20px">'.$rs['cdesprod'].'</td>
                                    <td class="textoCentro">'.$rs['cabrevia'].'</td>
                                    <td class="textoDerecha pr20px">'.$rs['cantidad'].'</td>
                                    <td><input type="number" step="any" placeholder="0.00" onchange="(function(el){el.value=parseFloat(el.value).toFixed(2);})(this)"></td>
                                    <td><input type="text"></td>
                                    <td><input type="date"></td>
                                    <td><select name="estado">'. $estados .'</select></td>
                                </tr>';
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }

        public function subirAdjuntos($codigo,$adjuntos){
            $countfiles = count( $adjuntos['name'] );

            for($i=0;$i<$countfiles;$i++){
                try {
                    $ext = explode('.',$adjuntos['name'][$i]);
                    $filename = uniqid().".".end($ext);
                    // Upload file
                    if (move_uploaded_file($adjuntos['tmp_name'][$i],'public/documentos/notas_ingreso/adjuntos/'.$filename)){
                        $sql= $this->db->connect()->prepare("INSERT INTO lg_regdocumento 
                                                                    SET nidrefer=:cod,cmodulo=:mod,cdocumento=:doc,
                                                                        creferencia=:ref,nflgactivo=:est");
                        $sql->execute(["cod"=>$codigo,
                                        "mod"=>"NI",
                                        "ref"=>$filename,
                                        "doc"=>$adjuntos['name'][$i],
                                        "est"=>1]);
                    }
                    

                } catch (PDOException $th) {
                    echo "Error: ".$th->getMessage();
                    return false;
                }
            }
        }

        public function generarPdf($cabecera,$detalles,$condicion){
            require_once("public/formatos/notaingreso.php");

            $datos = json_decode($detalles);
            $nreg = count($datos);

            $cargo = $this->rrhhCargo($cabecera['codigo_aprueba']);

            $fecha = explode("-",$cabecera['fecha']);
            
            $rc = 0;

            $dia = $fecha[2];
            $mes = $fecha[1];
            $anio = $fecha[0];

            $file = uniqid("NI")."_".$cabecera['numero']."_".$cabecera['codigo_almacen'].".pdf";

            if ($condicion == 0){
                $filename = "public/documentos/notas_ingreso/vistaprevia/".$file;
            }else if ($condicion == 1){
                $filename = "public/documentos/notas_ingreso/emitidas/".$file;
            }

            $pdf = new PDF($cabecera['numero'],$condicion,$dia,$mes,$anio,
                            $cabecera['proyecto'],$cabecera['almacen'],$cabecera['tipo'],$cabecera['orden'],
                            $cabecera['pedido'],$cabecera['guia'],$cabecera['aprueba'],$cargo,"I");
            
            $pdf->AliasNbPages();
            $pdf->AddPage();
            $pdf->SetWidths(array(5,15,55,8,12,20,45,15,15));
            $pdf->SetFont('Arial','',4);
            $lc = 0;

            for($i=1;$i<=$nreg;$i++){
                $pdf->SetAligns(array("C","L","L","L","R","L","L","L","L"));
                $pdf->Row(array(str_pad($i,3,"0",STR_PAD_LEFT),
                                        $datos[$rc]->codigo,
                                        utf8_decode($datos[$rc]->descripcion),
                                        $datos[$rc]->unidad,
                                        $datos[$rc]->cantrec,
                                        $datos[$rc]->obser,
                                        $cabecera['razon'],
                                        $datos[$rc]->cestado,
                                        $datos[$rc]->ubicacion));
                $lc++;
                $rc++;
                
                if ($lc == 52) {
                    $pdf->AddPage();
                    $lc = 0;
                }	
            }

            
            
            $pdf->Output($filename,'F');
            echo $filename;

        }
        
        public function cerrar($cabecera,$detalles){
            $estado = array_key_exists('qaqc', $cabecera)? 61 : 62;

            $nota = $this->actualizar_nota($cabecera['codigo_ingreso'],$estado);
            $orden = $this->actualizar_orden($cabecera['codigo_orden'],$estado);
            $pedido = $this->actualizar_pedido($cabecera['codigo_pedido'],$estado);
            $items = $this->actualizar_detalles($detalles,$estado);

            $this->generarPdf($cabecera,$detalles,1);

            return true;

        }

        private function actualizar_nota($id,$estado){
            try {
                $sql = $this->db->connect()->prepare("UPDATE alm_recepcab SET nEstadoDoc=:estado WHERE id_regalm = :id");
                $sql->execute(["estado"=>$estado,"id"=>$id]);
                $rowCount = $sql->rowCount();
                
                return "Notas Actualizadas -> " . $rowCount;

            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }

        private function actualizar_orden($id,$estado){
            try {
                $sql = $this->db->connect()->prepare("UPDATE lg_ordencab SET nEstadoDoc=:estado WHERE id_regmov = :id");
                $sql->execute(["estado"=>$estado,"id"=>$id]);
                $rowCount = $sql->rowCount();
                
                return "Orden Actualizadas -> " . $rowCount;
            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }

        private function actualizar_pedido($id,$estado){
            try {
                $sql = $this->db->connect()->prepare("UPDATE tb_pedidocab SET estadodoc=:estado WHERE idreg = :id");
                $sql->execute(["estado"=>$estado,"id"=>$id]);
                $rowCount = $sql->rowCount();
                
                return "Orden Actualizadas -> " . $rowCount;
            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }

        //aca actualizar la cantidad recibida
        private function actualizar_detalles($detalles,$estado){
            try {
                $datos = json_decode($detalles);
                $nreg = count($datos);

                for ($i=0; $i < $nreg; $i++) { 
                    try {
                        $sql = $this->db->connect()->prepare("UPDATE tb_pedidodet SET estadoItem=:estado WHERE iditem=:id");
                        $sql->execute(["estado"=>$estado,"id"=>$datos[$i]->iddetped]);
                    } catch (PDOException $th) {
                        echo "Error: " . $th->getMessage();
                        return false;
                    }
                }
            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }
    }
?>