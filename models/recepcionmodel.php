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
                                                        tb_proyectos.ccodproy,
                                                        lg_ordencab.id_regmov,
                                                        UPPER( tb_almacen.cdesalm ) AS almacen,
                                                        UPPER( tb_proyectos.cdesproy ) AS proyecto,
                                                        UPPER( tb_area.cdesarea ) AS area,
                                                        LPAD(lg_ordencab.cnumero,6,0) AS orden,
                                                        LPAD(tb_pedidocab.nrodoc,6,0 ) pedido,
                                                        cm_entidad.crazonsoc
                                                    FROM
                                                        tb_costusu
                                                        INNER JOIN alm_recepcab ON tb_costusu.ncodproy = alm_recepcab.ncodpry
                                                        INNER JOIN tb_almacen ON alm_recepcab.ncodalm1 = tb_almacen.ncodalm
                                                        INNER JOIN tb_proyectos ON alm_recepcab.ncodpry = tb_proyectos.nidreg
                                                        INNER JOIN tb_area ON alm_recepcab.ncodarea = tb_area.ncodarea
                                                        INNER JOIN lg_ordencab ON alm_recepcab.idref_abas = lg_ordencab.id_regmov
                                                        INNER JOIN tb_pedidocab ON alm_recepcab.idref_pedi = tb_pedidocab.idreg
                                                        INNER JOIN cm_entidad ON lg_ordencab.id_centi = cm_entidad.id_centi 
                                                    WHERE
                                                        tb_costusu.id_cuser = :usr
                                                        AND alm_recepcab.nflgactivo = 1 
                                                        AND tb_costusu.nflgactivo = 1
                                                        AND alm_recepcab.nEstadoDoc = 60
                                                        AND alm_recepcab.cper = YEAR(NOW())
                                                        AND alm_recepcab.cmes BETWEEN MONTH(NOW())-1 AND MONTH(NOW())
                                                    ORDER BY lg_ordencab.id_regmov DESC");
                $sql->execute(["usr"=>$_SESSION['iduser']]);
                $rowCount = $sql->rowcount();
                if ($rowCount > 0){
                    while($rs = $sql->fetch()){
                        $salida.='<tr class="pointer" data-indice="'.$rs['id_regalm'].'">
                                    <td class="textoCentro">'.$rs['cnumguia'].'</td>
                                    <td class="textoCentro">'.date("d/m/Y", strtotime($rs['ffecdoc'])).'</td>
                                    <td class="textoCentro">'.$rs['nnronota'].'</td>
                                    <td class="pl20px">'.$rs['almacen'].'</td>
                                    <td class="textoDerecha pr5px">'.$rs['ccodproy'].'</td>
                                    <td class="pl20px">'.$rs['area'].'</td>
                                    <td class="pl20px">'.$rs['crazonsoc'].'</td>
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
                $indice = $this->lastInsertId("SELECT MAX(id_regalm) AS id FROM alm_recepcab");

                $fecha = explode("-",$cabecera['fecha']); 
                
                $calidad = array_key_exists('qaqc', $cabecera)? 1 : 0;

                $nota = $this->generarNumero($cabecera["codigo_almacen"],"SELECT COUNT( alm_recepcab.id_regalm ) AS numero FROM alm_recepcab WHERE ncodalm1 =:cod");

                //inserta la nota de ingreso

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
                    $this->actualizar_detalles_orden($detalles);
                    $this->calcularSaldosOrden($cabecera['codigo_orden']);
                    $this->calcularSaldosPedido($cabecera['codigo_pedido']);
                    $this->saldosDetallesPedidos($detalles);
                    $this->generarPdf($cabecera,$detalles,1); 
                }

                return array("indice"=>$indice,
                            "mensaje"=>"Nota de ingreso registrada",
                            "clase"=>"mensaje_correcto",
                            "listado"=>$this->listarNotas());
                            
            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }
        
        //
        public function enviarCorreIngreso($cabecera,$detalles,$condicion){
            $this->actualizar_nota($cabecera['codigo_ingreso'],62);
        }

        private function calcularSaldosOrden($id){
            try {
                $diferencia_ingreso = $this->calcularIngresosOrden($id) - $this->calcularCantidadIngresa($id);

                if ( $diferencia_ingreso == 0 ) {
                    $this->actualizar_orden($id,62);
                }
            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }

        private function calcularSaldosPedido($id){
            try {
                $diferencia_ingreso = $this->calcularIngresosOrden($id) - $this->calcularCantidadIngresa($id);

                if ( $diferencia_ingreso == 0 ) {
                    $this->actualizar_pedido($id,62);
                }
            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }

        //actualizar items de la orden
        private function actualizar_detalles_orden($detalles){
            try {
                $datos = json_decode($detalles);
                $nreg = count($datos);

                for ($i=0; $i < $nreg; $i++) { 
                    try {
                        $estado = $datos[$i]->cantsal == 0 ? 62 : 60;

                        $sql = $this->db->connect()->prepare("UPDATE lg_ordendet SET nSaldo=:saldo, nEstadoReg=:estado WHERE nitemord=:id");

                        $sql->execute(["estado"=>$estado,
                                        "saldo"=>$datos[$i]->cantsal,
                                        "id"=>$datos[$i]->iddetorden]);
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

        private function saldosDetallesPedidos($detalles){
                $datos = json_decode($detalles);
                $nreg = count($datos);

                for ($i = 0; $i < $nreg; $i++) {
                    try {
                        $sql = $this->db->connect()->prepare("SELECT SUM(nsaldo) AS nSaldo FROM lg_ordendet WHERE niddeta =:id");
                        $sql->execute(['id' => $datos[$i]->iddetped]);
                        $result = $sql->fetchAll();

                        if ($result[0]['nSaldo'] == 0){
                            $this->actualizar_detalles_pedido($datos[$i]->iddetped,62);
                        }
                    } catch (PDOException $th) {
                        echo "Error: " . $th->getMessage();
                        return false;
                    }
                }  
        }

        private function actualizar_detalles_pedido($id,$estado){
            try {
                $sql = $this->db->connect()->prepare("UPDATE tb_pedidodet 
                                                        SET estadoItem=:estado 
                                                        WHERE iditem=:id");
                $sql->execute(["estado"=>$estado,"id"=>$id]);
            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
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
                                                                                            nestadoreg=:estado,orden=:idorden,pedido=:idpedido");
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
                                        "estado"=>$datos[$i]->nestado,
                                        "idorden"=>$datos[$i]->orden,
                                        "idpedido"=>$datos[$i]->pedido]);
                   
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
               //echo $nreg;

                if ($nreg > 0 ) {
                    for ($i=0; $i < $nreg; $i++) { 
                        $sql= $this->db->connect()->prepare("INSERT INTO alm_recepserie SET id_cprod=:cprod,idref_movi=:nota,idref_alma=:almacen,
                                                                                            cdesserie=:serie,idref_pedido=:itempedido");
                         $sql ->execute(["cprod"=>$datos[$i]->producto,
                                         "almacen"=>$datos[$i]->almacen,
                                         "nota"=>$id,
                                         "serie"=>$datos[$i]->serie,
                                         "itempedido"=>$datos[$i]->idped]);
                    }
                }
                
            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }

        public function consultarOrdenIdRecepcion($id){
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
                            "detalles"=>$this->ordenDetalles($id),
                            "numero"=>$this->numeroIngreso($almacen));
            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }

        private function numeroIngreso($almacen){
            $sql ="SELECT COUNT( alm_recepcab.id_regalm ) AS numero FROM alm_recepcab WHERE ncodalm1 =:cod";
            return $this->generarNumero($almacen,$sql);
        }

        public function modificarRegistro($cabecera,$detalles){
            try {
                $sql = $this->db->connect()->prepare("UPDATE alm_recepcab 
                                                        SET cnumguia=:guia,
                                                            id_userAprob=:aprueba
                                                        WHERE id_regalm=:idnota");
                $sql->execute(["guia"=>$cabecera['guia'],
                                "aprueba"=>$cabecera['codigo_aprueba'],
                                "idnota"=>$cabecera['codigo_ingreso']]);
                
                $this->actualizarItems($detalles);                
            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }

        private function actualizarItems($detalles){
            try {
                $datos = json_decode($detalles);
                $nreg = count($datos);

                for ($i=0; $i < $nreg; $i++) { 
                        try {
                            $sql = $this->db->connect()->prepare("UPDATE alm_recepdet SET ncantidad=:cantidad,
                                                                                    cObserva=:observacion,
                                                                                    ncodalm1=:almacen
                                                                        WHERE niddeta=:id");
                            $sql ->execute(["id"=>$datos[$i]->iddeting,
                                            "almacen"=>$datos[$i]->almacen,
                                            "cantidad"=>$datos[$i]->cantrec,
                                            "observacion"=>$datos[$i]->obser]);
                    
                    } catch (PDOException $th) {
                        echo "Error: ".$th->getMessage();
                        return false;
                    }
                }
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
                                                    lg_ordendet.id_orden,
                                                    cm_producto.ccodprod,
                                                    UPPER( CONCAT_WS( ' ', cm_producto.cdesprod, tb_pedidodet.observaciones, tb_pedidodet.docEspec ) ) AS cdesprod,
                                                    cm_producto.nund,
                                                    tb_unimed.cabrevia,
                                                    tb_pedidodet.idpedido,
                                                    tb_pedidodet.nroparte,
                                                    REPLACE ( FORMAT( lg_ordendet.ncanti, 2 ), ',', '' ) AS cantidad,
                                                    ( SELECT SUM( alm_recepdet.ncantidad ) FROM alm_recepdet WHERE alm_recepdet.niddetaOrd = lg_ordendet.nitemord AND alm_recepdet.nflgactivo = 1 ) AS pendiente 
                                                FROM
                                                    lg_ordendet
                                                    INNER JOIN cm_producto ON lg_ordendet.id_cprod = cm_producto.id_cprod
                                                    INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed
                                                    INNER JOIN tb_pedidodet ON lg_ordendet.niddeta = tb_pedidodet.iditem 
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
                                        data-saldo="'.$saldo.'">
                                    <td class="textoCentro"><a href="'.$rs['id_orden'].'" data-accion="deleteItem" class="eliminarItem"><i class="fas fa-minus"></i></a></td>
                                    <td class="textoCentro"><input type="checkbox"></td>
                                    <td class="textoCentro">'.str_pad($item++,3,0,STR_PAD_LEFT).'</td>
                                    <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                    <td class="pl20px">'.$rs['cdesprod'].'</td>
                                    <td class="textoCentro">'.$rs['cabrevia'].'</td>
                                    <td class="textoDerecha pr20px">'.$rs['cantidad'].'</td>
                                    <td>
                                        <input type="number" 
                                            step="any" 
                                            placeholder="0.00" 
                                            onchange="(function(el){el.value=parseFloat(el.value).toFixed(2);})(this)" value="'.$saldo.'">
                                    </td>
                                    <td><input type="text"></td>
                                    <td></td>
                                    <td class="textoCentro"><a href="'.$rs['id_orden'].'" data-accion="setSerial"><i class="fas fa-barcode"></i></a></td>
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

        //calcula los saldos de los items
        private function calcularSaldosIngresados($id){
            try {
                $sql = $this->db->connect()->prepare("SELECT SUM(alm_recepdet.ncantidad) AS pendiente,niddetaOrd FROM alm_recepdet
                                                        WHERE alm_recepdet.niddetaOrd =:id");
                $sql->execute(["id"=>$id]);
                $result = $sql->fetchAll();

                return $result[0]['pendiente'];

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
                            $cabecera['pedido'],$cabecera['guia'],$cabecera['aprueba'],$cargo,"I",$cabecera['codigo_aprueba']);
            
            $pdf->AliasNbPages();
            $pdf->AddPage();
            $pdf->SetWidths(array(7,20,55,8,12,15,45,13,15));
            $pdf->SetFont('Arial','',6);
            $lc = 0;

            for($i=1;$i<=$nreg;$i++){
                $pdf->SetAligns(array("C","L","L","L","R","L","L","L","L"));

                $series = strlen($this->itemSeries($datos[$rc]->iddetped)) == 0 ? "" : "N/S: ".$this->itemSeries($datos[$rc]->iddetped);


                $pdf->Row(array(str_pad($i,3,"0",STR_PAD_LEFT),
                                        $datos[$rc]->codigo,
                                        utf8_decode($datos[$rc]->descripcion).' '.strtoupper($series) ,
                                        $datos[$rc]->unidad,
                                        $datos[$rc]->cantrec,
                                        $datos[$rc]->obser,
                                        $cabecera['razon'],
                                        $datos[$rc]->cestado,
                                        $datos[$rc]->ubicacion));
                $lc++;
                $rc++;
                
                if ($pdf->getY() >= 190) {
                    $pdf->AddPage();
                    $lc = 0;
                }	
            }
            
            $pdf->Output($filename,'F');
            
            return $filename;

        }
        
        private function actualizar_nota($id,$estado){
            try {
                $sql = $this->db->connect()->prepare("UPDATE alm_recepcab SET nEstadoDoc=:estado WHERE id_regalm = :id");
                $sql->execute(["estado"=>$estado,"id"=>$id]);
                $rowCount = $sql->rowCount();
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

        public function filtrarNotasIngreso($parametros){
            try {

                $mes  = date("m");

                $orden   = $parametros['ordenSearch'] == "" ? "%" : "%".$parametros['ordenSearch']."%";
                $costos = $parametros['costosSearch'] == -1 ? "%" : "%".$parametros['costosSearch']."%";
                $mes    = $parametros['mesSearch'] == -1 ? $mes :  $parametros['mesSearch'];
                $anio   = $parametros['anioSearch'];

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
                                                        LPAD(lg_ordencab.cnumero,6,0 )AS orden,
                                                        LPAD( tb_pedidocab.nrodoc, 6, 0 ) pedido,
                                                        cm_entidad.crazonsoc,
                                                        tb_proyectos.ccodproy 
                                                    FROM
                                                        tb_costusu
                                                        INNER JOIN alm_recepcab ON tb_costusu.ncodproy = alm_recepcab.ncodpry
                                                        INNER JOIN tb_almacen ON alm_recepcab.ncodalm1 = tb_almacen.ncodalm
                                                        INNER JOIN tb_proyectos ON alm_recepcab.ncodpry = tb_proyectos.nidreg
                                                        INNER JOIN tb_area ON alm_recepcab.ncodarea = tb_area.ncodarea
                                                        INNER JOIN lg_ordencab ON alm_recepcab.idref_abas = lg_ordencab.id_regmov
                                                        INNER JOIN tb_pedidocab ON alm_recepcab.idref_pedi = tb_pedidocab.idreg
                                                        INNER JOIN cm_entidad ON lg_ordencab.id_centi = cm_entidad.id_centi
                                                    WHERE
                                                        tb_costusu.id_cuser = :usr 
                                                        AND alm_recepcab.nflgactivo = 1
                                                        AND tb_costusu.nflgactivo = 1
                                                        AND alm_recepcab.nEstadoDoc BETWEEN  60 AND 62
                                                        AND alm_recepcab.ncodpry LIKE :costos 
                                                        AND alm_recepcab.idref_abas LIKE :orden 
                                                        AND YEAR ( alm_recepcab.ffecdoc ) = :anio
                                                    ORDER BY lg_ordencab.id_regmov DESC");
                $sql->execute(["usr"=>$_SESSION['iduser'],
                                "orden"=>$orden,
                                "costos"=>$costos,
                                "anio"=>$anio]);

                $rowCount = $sql->rowcount();
                if ($rowCount > 0){
                    while($rs = $sql->fetch()){
                        $salida.='<tr class="pointer" data-indice="'.$rs['id_regalm'].'">
                                    <td class="textoCentro">'.$rs['cnumguia'].'</td>
                                    <td class="textoCentro">'.date("d/m/Y", strtotime($rs['ffecdoc'])).'</td>
                                    <td class="textoCentro">'.$rs['nnronota'].'</td>
                                    <td class="pl20px">'.$rs['almacen'].'</td>
                                    <td class="textoDerecha pr5px">'.$rs['ccodproy'].'</td>
                                    <td class="pl20px">'.$rs['area'].'</td>
                                    <td class="pl20px">'.$rs['crazonsoc'].'</td>
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

        public function mostrarOrdenes($id){
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
                                                        cm_entidad.crazonsoc 
                                                    FROM
                                                        tb_costusu
                                                        INNER JOIN lg_ordencab ON tb_costusu.ncodproy = lg_ordencab.ncodpry
                                                        INNER JOIN tb_proyectos ON lg_ordencab.ncodpry = tb_proyectos.nidreg
                                                        INNER JOIN tb_area ON lg_ordencab.ncodarea = tb_area.ncodarea
                                                        INNER JOIN cm_entidad ON lg_ordencab.id_centi = cm_entidad.id_centi 
                                                    WHERE
                                                        tb_costusu.id_cuser = :usr
                                                        AND lg_ordencab.ncodpry = :id 
                                                        AND tb_costusu.nflgactivo = 1 
                                                        AND lg_ordencab.nEstadoDoc = 60");
                $sql->execute(["usr"=>$_SESSION['iduser'],"id"=>$id]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()) {
                        //compara la orden si fue ingresada esta completa y no la muestra
                        $diferencia_ingreso = $this->calcularIngresosOrden($rs['id_regmov']) - $this->calcularCantidadIngresa($rs['id_regmov']);

                        if (($diferencia_ingreso) > 0 ) {
                            $salida.='<tr data-orden="'.$rs['id_regmov'].'">
                                    <td class="textoCentro">'.$rs['cnumero'].'</td>
                                    <td class="textoCentro">'.$rs['ffechadoc'].'</td>
                                    <td class="pl20px">'.$rs['area'].'</td>
                                    <td class="pl20px">'.$rs['ccodproy'].'</td>
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
                                                        CONCAT_WS(
                                                            ' ',
                                                            tb_proyectos.ccodproy,
                                                        UPPER( tb_proyectos.cdesproy )) AS costos,
                                                        CONCAT_WS(
                                                            ' ',
                                                            tb_area.ccodarea,
                                                        UPPER( tb_area.cdesarea )) AS area,
                                                        cm_entidad.crazonsoc,
                                                        ( SELECT SUM( alm_recepdet.ncantidad ) FROM alm_recepdet WHERE pedido = lg_ordencab.id_regmov AND nflgactivo = 1 ) AS ingresos,
                                                        ( SELECT SUM( lg_ordendet.ncanti ) FROM lg_ordendet WHERE lg_ordendet.id_orden = lg_ordencab.id_regmov ) AS cantidad_orden 
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
                                                        AND lg_ordencab.ntipmov = 37 
                                                        AND lg_ordencab.nEstadoDoc BETWEEN 60 AND 62 
                                                    ORDER BY
                                                        id_regmov DESC");
                $sql->execute(["usr"=>$_SESSION['iduser'],"id"=>$id]);
                $rowCount = $sql->rowCount();

                //AND lg_ordencab.cnumero = 1 Esto cambiaria para ir considerando

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

        public function verDespacho($id){
            try {
               $sql = $this->db->connect()->prepare("SELECT COUNT(alm_despachodet.niddeta) AS existe FROM alm_despachodet WHERE niddetaPed =:despacho");
               $sql->execute(["despacho"=>$id]);
               $result =$sql->fetchAll();
               
               return $result[0]['existe'];

            }catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }

        public function marcarItem($id) {
            try {
                $sql = $this->db->connect()->prepare("UPDATE alm_recepdet SET alm_recepdet.nflgactivo=0 WHERE alm_recepdet.niddetaPed =:id");
                $sql->execute(["id"=>$id]);
            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }
    }
?>