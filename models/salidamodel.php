<?php
    class SalidaModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarNotasIngreso(){
            $salida = "";
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                        alm_despachocab.id_regalm,
                                                        tb_costusu.id_cuser,
                                                        LPAD(alm_despachocab.nnronota,6,0) AS salida,
                                                        UPPER(
                                                        CONCAT_WS( ' ', tb_proyectos.ccodproy, tb_proyectos.cdesproy )) AS costos,
                                                        UPPER(
                                                        CONCAT_WS( ' ', tb_area.ccodarea, tb_area.cdesarea )) AS area,
                                                        alm_despachocab.ffecdoc,
                                                        LPAD(alm_despachocab.nnromov,4,0) AS movimiento,
                                                        UPPER(
                                                        CONCAT_WS( ' ', tb_almacen.ccodalm, tb_almacen.cdesalm )) AS almacen,
                                                        YEAR ( alm_despachocab.ffecdoc ) AS anio,
                                                        lg_ordencab.cnumero AS orden,
                                                        alm_despachocab.cnumguia,
                                                        LPAD(tb_pedidocab.nrodoc,6,0) AS pedido,
                                                        UPPER( tb_pedidocab.concepto ) concepto,
                                                        tb_parametros.cdescripcion,
                                                        tb_parametros.cabrevia
                                                    FROM
                                                        tb_costusu
                                                        INNER JOIN alm_despachocab ON tb_costusu.ncodproy = alm_despachocab.ncodpry
                                                        INNER JOIN tb_proyectos ON alm_despachocab.ncodpry = tb_proyectos.nidreg
                                                        INNER JOIN tb_area ON alm_despachocab.ncodarea = tb_area.ncodarea
                                                        INNER JOIN tb_almacen ON alm_despachocab.ncodalm1 = tb_almacen.ncodalm
                                                        INNER JOIN lg_ordencab ON alm_despachocab.idref_ord = lg_ordencab.id_regmov
                                                        INNER JOIN tb_pedidocab ON alm_despachocab.idref_pedi = tb_pedidocab.idreg
                                                        INNER JOIN tb_parametros ON alm_despachocab.nEstadoDoc = tb_parametros.nidreg 
                                                    WHERE
                                                        tb_costusu.id_cuser = :usr 
                                                        AND tb_costusu.nflgactivo = 1");
                $sql->execute(["usr"=>$_SESSION['iduser']]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()){
                        $salida .='<tr data-indice="'.$rs['id_regalm'].'" class="pointer">
                                        <td class="textoCentro">'.$rs['salida'].'</td>
                                        <td class="textoCentro">'.date("d/m/Y", strtotime($rs['ffecdoc'])).'</td>
                                        <td class="textoCentro">'.$rs['movimiento'].'</td>
                                        <td class="pl20px">'.$rs['almacen'].'</td>
                                        <td class="pl20px">'.$rs['costos'].'</td>
                                        <td class="textoCentro">'.$rs['anio'].'</td>
                                        <td class="textoCentro">'.$rs['orden'].'</td>
                                        <td class="textoCentro">'.$rs['cnumguia'].'</td>
                                        <td class="textoCentro">'.$rs['pedido'].'</td>
                                        <td class="textoCentro"></td>
                                        <td class="textoCentro">'.$rs['concepto'].'</td>
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

        public function importarIngresos(){
            try {
                $salida = "";
                $sql = $this->db->connect()->prepare("SELECT
                                                    ibis.alm_recepcab.id_regalm,
                                                    ibis.tb_costusu.ncodproy,
                                                    ibis.alm_recepcab.nnronota,
                                                    ibis.alm_recepcab.cper,
                                                    ibis.alm_recepcab.cmes,
                                                    ibis.alm_recepcab.ncodalm1,
                                                    ibis.alm_recepcab.ffecdoc,
                                                    ibis.alm_recepcab.cnumguia,
                                                    ibis.alm_recepcab.ncodpry,
                                                    ibis.alm_recepcab.ncodarea,
                                                    ibis.alm_recepcab.idref_pedi,
                                                    ibis.alm_recepcab.idref_abas,
                                                    ibis.alm_recepcab.nEstadoDoc,
                                                    UPPER(
                                                    CONCAT_WS( ' ', ibis.tb_proyectos.ccodproy, ibis.tb_proyectos.cdesproy )) AS proyecto,
                                                    UPPER(
                                                    CONCAT_WS( ' ', ibis.tb_area.ccodarea, ibis.tb_area.cdesarea )) AS area 
                                                FROM
                                                    ibis.tb_costusu
                                                    INNER JOIN ibis.alm_recepcab ON ibis.tb_costusu.ncodproy = ibis.alm_recepcab.ncodpry
                                                    INNER JOIN ibis.tb_proyectos ON ibis.alm_recepcab.ncodpry = ibis.tb_proyectos.nidreg
                                                    INNER JOIN ibis.tb_area ON ibis.alm_recepcab.ncodarea = ibis.tb_area.ncodarea 
                                                WHERE
                                                    ibis.tb_costusu.nflgactivo = 1 
                                                    AND ibis.tb_costusu.id_cuser = :usr 
                                                    AND ibis.alm_recepcab.nEstadoDoc = 62");
                $sql->execute(["usr"=>$_SESSION['iduser']]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0){
                    while ($rs = $sql->fetch()){
                        $salida .='<tr class="pointer" data-idnit="'.$rs['id_regalm'].'">
                                        <td class="textoCentro">'.$rs['nnronota'].'</td>
                                        <td class="textoCentro">'.date("d/m/Y", strtotime($rs['ffecdoc'])).'</td>
                                        <td class="pl20px">'.$rs['area'].'</td>
                                        <td class="pl20px">'.$rs['proyecto'].'</td>
                                    </tr>';
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function llamarNotaIngresoId($id){
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                            ibis.alm_recepcab.id_regalm,
                                            ibis.alm_recepcab.nnronota,
                                            ibis.alm_recepcab.cper,
                                            ibis.alm_recepcab.cmes,
                                            ibis.alm_recepcab.ffecdoc,
                                            ibis.alm_recepcab.id_userAprob AS aprueba,
                                            ibis.alm_recepcab.nEstadoDoc,
                                            UPPER(
                                            CONCAT_WS( ' ', ibis.tb_proyectos.ccodproy, ibis.tb_proyectos.cdesproy )) AS proyecto,
                                            UPPER(
                                            CONCAT_WS( ' ', ibis.tb_area.ccodarea, ibis.tb_area.cdesarea )) AS area,
                                            ibis.tb_user.cnombres,
                                            LPAD( ibis.tb_pedidocab.nrodoc, 6, 0 ) AS pedido,
                                            CONCAT_WS( ' ', rrhh.tabla_aquarius.nombres, rrhh.tabla_aquarius.apellidos ) AS solicita,
                                            UPPER( ibis.tb_almacen.cdesalm ) AS almacen,
                                            UPPER( ibis.tb_pedidocab.concepto ) AS concepto,
                                            ibis.tb_parametros.cdescripcion,
                                            ibis.tb_parametros.cabrevia,
                                            ibis.lg_ordencab.cnumero AS orden,
                                            ibis.lg_ordencab.ffechadoc,
                                            ibis.tb_pedidocab.emision,
                                            ibis.alm_recepcab.ncodpry,
                                            ibis.alm_recepcab.ncodarea,
                                            ibis.alm_recepcab.ncodalm1,
                                            ibis.alm_recepcab.idref_pedi,
                                            ibis.alm_recepcab.idref_abas,
                                            ibis.alm_recepcab.cnumguia 
                                        FROM
                                            ibis.alm_recepcab
                                            INNER JOIN ibis.tb_proyectos ON ibis.alm_recepcab.ncodpry = ibis.tb_proyectos.nidreg
                                            INNER JOIN ibis.tb_area ON ibis.alm_recepcab.ncodarea = ibis.tb_area.ncodarea
                                            INNER JOIN ibis.tb_user ON ibis.alm_recepcab.id_userAprob = ibis.tb_user.iduser
                                            INNER JOIN ibis.tb_pedidocab ON ibis.alm_recepcab.idref_pedi = ibis.tb_pedidocab.idreg
                                            INNER JOIN rrhh.tabla_aquarius ON ibis.tb_pedidocab.idsolicita = rrhh.tabla_aquarius.internal
                                            INNER JOIN ibis.tb_almacen ON ibis.alm_recepcab.ncodalm1 = ibis.tb_almacen.ncodalm
                                            INNER JOIN ibis.tb_parametros ON ibis.alm_recepcab.nEstadoDoc = ibis.tb_parametros.nidreg
                                            INNER JOIN ibis.lg_ordencab ON ibis.alm_recepcab.idref_abas = ibis.lg_ordencab.id_regmov 
                                        WHERE
                                            ibis.alm_recepcab.id_regalm = :id 
                                            AND ibis.alm_recepcab.nEstadoDoc = 62");
                                        $sql->execute(["id"=>$id]);
            
            $docData = array();

            while($row=$sql->fetch(PDO::FETCH_ASSOC)){
                $docData[] = $row;
            }

            $query = "SELECT COUNT( alm_despachocab.id_regalm ) AS numero FROM alm_despachocab WHERE ncodalm1 =:cod";
            $movimiento = $this->genNumberSalidas($docData[0]["ncodalm1"]) + $this->genNumberIngresos($docData[0]["ncodalm1"]);

            return array("cabecera"=>$docData,
                        "detalles"=>$this->detallesNotaIngreso($id),
                        "movimiento"=>str_pad($movimiento,6,0,STR_PAD_LEFT),
                        "numero"=>$this->generarNumero($docData[0]["ncodalm1"],$query));
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function detallesNotaIngreso($id){
            try {
                $salida = "";
                $sql = $this->db->connect()->prepare("SELECT
                                                    alm_recepdet.niddeta,
                                                    alm_recepdet.id_regalm,
                                                    alm_recepdet.ncodalm1,
                                                    alm_recepdet.id_cprod,
                                                    FORMAT( alm_recepdet.ncantidad, 2 ) AS ncantidad,
                                                    alm_recepdet.niddetaPed,
                                                    alm_recepdet.niddetaOrd,
                                                    cm_producto.ccodprod,
                                                    cm_producto.cdesprod,
                                                    FORMAT( lg_ordendet.ncanti, 2 ) AS cantidad,
                                                    alm_recepdet.nestadoreg,
                                                    tb_unimed.cabrevia,
                                                    alm_recepdet.cobserva,
                                                    alm_recepdet.fvence 
                                                FROM
                                                    alm_recepdet
                                                    INNER JOIN cm_producto ON alm_recepdet.id_cprod = cm_producto.id_cprod
                                                    INNER JOIN lg_ordendet ON alm_recepdet.niddetaPed = lg_ordendet.nitemord
                                                    INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed 
                                                WHERE
                                                    alm_recepdet.id_regalm = :id");
                $sql->execute(["id"=>$id]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    $item = 1;
                    while ($rs = $sql->fetch()){

                        $estados = $this->listarSelect(13,$rs['nestadoreg']);

                        $fecha = $rs['fvence'] == "" ? date("d/m/Y", strtotime($rs['fvence'])) : "";

                        $salida.='<tr data-itemorden="'.$rs['niddetaOrd'].'" 
                                        data-itempedido="'.$rs['niddetaPed'].'" 
                                        data-itemingreso="'.$rs['niddeta'].'"
                                        data-idproducto ="'.$rs['id_cprod'].'">
                                        <td class="textoCentro">...</td>
                                        <td class="textoCentro">'.str_pad($item,3,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                        <td class="pl20px">'.$rs['cdesprod'].'</td>
                                        <td class="textoCentro">'.$rs['cabrevia'].'</td>
                                        <td><input type="number" step="any" value="'.$rs['cantidad'].'" onchange="(function(el){el.value=parseFloat(el.value).toFixed(2);})(this)"></td>
                                        <td class="pl20px"><input type="text"></td>
                                        <td class="textoCentro"></td>
                                        <td class="textoCentro">'.$fecha.'</td>
                                        <td><select name="estado" disabled>'. $estados .'</select></td>
                                    </tr>';
                    }
                }

                return $salida;
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
                                                                                        cobserva = :cobserva,
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
                                "ncodarea"=>$cabecera['codigo_area'],
                                "idref_pedi"=>$cabecera['codigo_pedido'],
                                "idref_ord"=>$cabecera['codigo_orden'],
                                "idref_abas"=>$cabecera['codigo_ingreso'],
                                "nnronota"=>$numero['numero'],
                                "cobserva"=>$cabecera['concepto'],
                                "id_userAprob"=>$cabecera['codigo_aprueba'],
                                "nEstadoDoc"=>$cabecera['codigo_estado'],
                                "nflgactivo"=>1,
                                "nguia"=>$cabecera['guia']]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    $mensaje = "Registro grabado";
                    $clase = "mensaje_correcto";
                    $error = "false";

                    $indice = $this->lastInsertId("SELECT MAX(id_regalm) AS id FROM alm_despachocab");
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
                        $sql=$this->db->connect()->prepare("INSERT INTO alm_despachodet SET id_regalm=:cod,ncodalm1=:ori,id_cprod=:cpro,ncantidad=:cant,
                                                                                        cSerie=:ser,niddetaPed=:pedido,niddetaOrd=:orden,nflgactivo=:flag,
                                                                                        nestadoreg=:estadoItem");
                         $sql->execute(["cod"=>$id,
                                        "ori"=>$almacen,
                                        "cpro"=>$datos[$i]->idprod,
                                        "cant"=>$datos[$i]->cantidad,
                                        "ser"=>$datos[$i]->serie,
                                        "pedido"=>$datos[$i]->pedido,
                                        "orden"=>$datos[$i]->orden,
                                        "flag"=>1,
                                        "estadoItem"=>$datos[$i]->nestado]);
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

                $cargo = $this->rrhhCargo($cabecera['codigo_aprueba']);

                $file = uniqid("NS")."_".$cabecera['numero']."_".$cabecera['codigo_almacen'].".pdf";

                if ($condicion == 0){
                    $filename = "public/documentos/notas_salida/vistaprevia/".$file;
                }else if ($condicion == 1){
                    $filename = "public/documentos/notas_salida/emitidas/".$file;
                }
                
                $pdf = new PDF($cabecera['numero'],$condicion,$dia,$mes,$anio,$cabecera['costos'],
                            $cabecera['almacen_origen_despacho'],$cabecera['tipo'],$cabecera['orden'],$cabecera['pedido'],
                            $cabecera['guia'],$cabecera['aprueba'],$cargo,'S');

                $pdf->AliasNbPages();
                $pdf->AddPage();
                $pdf->SetWidths(array(5,15,55,8,12,20,45,15,15));
                $pdf->SetFont('Arial','',4);

                for($i=1;$i<=$nreg;$i++){
                    $pdf->SetAligns(array("C","L","L","L","R","L","L","L","L"));
                    $pdf->Row(array(str_pad($i,3,"0",STR_PAD_LEFT),
                                            $datos[$rc]->codigo,
                                            utf8_decode($datos[$rc]->descripcion),
                                            $datos[$rc]->unidad,
                                            $datos[$rc]->cantidad,
                                            "",
                                            "",
                                            $datos[$rc]->cestado,
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

        public function consultarSalidaId($indice){
            try {
                $sql=$this->db->connect()->prepare("SELECT
                                                        ibis.alm_despachocab.id_userAprob,
                                                        ibis.alm_despachocab.id_regalm,
                                                        ibis.alm_despachocab.ntipmov,
                                                        LPAD(ibis.alm_despachocab.nnronota,6,0) AS nnronota,
                                                        ibis.alm_despachocab.ffecdoc,
                                                        ibis.alm_despachocab.nnromov,
                                                        ibis.alm_despachocab.cnumguia,
                                                        ibis.alm_despachocab.ncodpry,
                                                        ibis.alm_despachocab.ncodarea,
                                                        ibis.alm_despachocab.idref_ord,
                                                        ibis.alm_despachocab.idref_pedi,
                                                        ibis.alm_despachocab.idref_abas,
                                                        ibis.alm_despachocab.nEstadoDoc,
                                                        UPPER(
                                                        CONCAT_WS( ' ', tb_proyectos.ccodproy, tb_proyectos.cdesproy )) AS costos,
                                                        UPPER(
                                                        CONCAT_WS( ' ', tb_area.ccodarea, tb_area.cdesarea )) AS area,
                                                        ibis.alm_despachocab.ncodalm1,
                                                        UPPER( origen.cdesalm ) AS almacen,
                                                        ibis.tb_user.cnombres,
                                                        movimientos.cdescripcion,
                                                        LPAD( alm_despachocab.nnromov, 4, 0 ) AS movimiento,
                                                        UPPER( ibis.tb_pedidocab.concepto ) AS concepto,
                                                        ibis.tb_pedidocab.emision,
                                                        ibis.lg_ordencab.cnumero AS orden,
                                                        ibis.lg_ordencab.ffechadoc,
                                                        estados.cdescripcion AS estado,
                                                        estados.cabrevia,
                                                        CONCAT_WS( ' ', rrhh.tabla_aquarius.nombres, rrhh.tabla_aquarius.apellidos ) AS nombres,
                                                        ibis.lg_ordencab.id_centi,
	                                                    LPAD(ibis.tb_pedidocab.nrodoc,6, 0 ) AS pedido
                                                    FROM
                                                        ibis.alm_despachocab
                                                        INNER JOIN ibis.tb_proyectos ON alm_despachocab.ncodpry = tb_proyectos.nidreg
                                                        INNER JOIN ibis.tb_area ON alm_despachocab.ncodarea = tb_area.ncodarea
                                                        INNER JOIN ibis.tb_almacen AS origen ON alm_despachocab.ncodalm1 = origen.ncodalm
                                                        INNER JOIN ibis.tb_user ON alm_despachocab.id_userAprob = tb_user.iduser
                                                        INNER JOIN ibis.tb_parametros AS movimientos ON alm_despachocab.ntipmov = movimientos.nidreg
                                                        INNER JOIN ibis.tb_pedidocab ON alm_despachocab.idref_pedi = tb_pedidocab.idreg
                                                        INNER JOIN ibis.lg_ordencab ON alm_despachocab.idref_ord = lg_ordencab.id_regmov
                                                        INNER JOIN ibis.tb_parametros AS estados ON alm_despachocab.nEstadoDoc = estados.nidreg
                                                        INNER JOIN rrhh.tabla_aquarius ON ibis.tb_pedidocab.idsolicita = rrhh.tabla_aquarius.internal 
                                                    WHERE
                                                        alm_despachocab.id_regalm = :indice");
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
                $salida="";
                $sql=$this->db->connect()->prepare("SELECT
                                                    alm_despachodet.niddeta,
                                                    alm_despachodet.id_regalm,
                                                    alm_despachodet.id_cprod,
                                                    FORMAT(alm_despachodet.ncantidad,2) AS cantidad,
                                                    alm_despachodet.niddetaOrd,
                                                    alm_despachodet.niddetaPed,
                                                    cm_producto.ccodprod,
                                                    cm_producto.cdesprod,
                                                    tb_unimed.cabrevia,
                                                    tb_unimed.nfactor,
                                                    alm_despachodet.cobserva,
                                                    alm_despachodet.fvence,
                                                    alm_despachodet.cSerie,
                                                    alm_despachodet.nestadoreg 
                                                FROM
                                                    alm_despachodet
                                                    INNER JOIN cm_producto ON alm_despachodet.id_cprod = cm_producto.id_cprod
                                                    INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed 
                                                WHERE
                                                    alm_despachodet.id_regalm = :indice");
                $sql->execute(["indice"=>$indice]);

                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    $item = 1;
                    while ($rs = $sql->fetch()){

                        $estados = $this->listarSelect(13,$rs['nestadoreg']);

                        $fecha = $rs['fvence'] == "" ? "" : date("d/m/Y", strtotime($rs['fvence']));

                        $salida.='<tr data-itemorden="'.$rs['niddetaOrd'].'" 
                                        data-itempedido="'.$rs['niddetaPed'].'" 
                                        data-itemingreso="'.$rs['niddeta'].'"
                                        data-idproducto ="'.$rs['id_cprod'].'">
                                        <td class="textoCentro">...</td>
                                        <td class="textoCentro">'.str_pad($item,3,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                        <td class="pl20px">'.$rs['cdesprod'].'</td>
                                        <td class="textoCentro">'.$rs['cabrevia'].'</td>
                                        <td><input type="number" step="any" value="'.$rs['cantidad'].'" onchange="(function(el){el.value=parseFloat(el.value).toFixed(2);})(this)"></td>
                                        <td class="pl20px"><input type="text"></td>
                                        <td class="textoCentro"></td>
                                        <td class="textoCentro">'.$fecha.'</td>
                                        <td><select name="estado" disabled>'. $estados .'</select></td>
                                    </tr>';
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function grabarGuiaRemision($cabecera,$detalles,$despacho){
            try {

                $filename = "public/documentos/guias_remision/".$despacho.".pdf";
                $this->generarGuia($cabecera,$detalles,$filename);

                $sql = $this->db->connect()->prepare("INSERT lg_docusunat SET ccodtdoc=:ccodtdoc,ffechdoc=:ffechdoc,ffechreg=:ffechreg,ffechtrasl=:ffechtrasl,cserie=:cserie,
                                                                            cnumero=:cnumero,id_centi=:id_centi,cmotivo=:cmotivo,ccodmodtrasl=:ccodmodtrasl,cdesmodtrasl=:cdesmodtrasl,
                                                                            ctipoenvio=:ctipoenvio,nbultos=:nbultos,npesotot=:npesotot,cdniconduc=:cdniconduc,cdesconduc=:cdesconduc,
                                                                            cnrolicen=:cnrolicen,cnrocert=:cnrocert,cmarcaveh=:cmarcaveh,cplacaveh=:cplacaveh,cconfigveh=:cconfigveh,
                                                                            ncodalm1=:ncodalm1,ncodalm2=:ncodalm2,nEstadoImp=:nEstadoImp,cdocPDF=:cdocPDF,nflgactivo=:nflgactivo,
                                                                            id_despacho=:salida");
                $sql->execute([ "ccodtdoc"=>'09',
                                "ffechdoc"=>$cabecera['fgemision'],
                                "ffechreg"=>null,
                                "ffechtrasl"=>$cabecera['ftransporte'],
                                "cserie"=>'0001',
                                "cnumero"=>$cabecera['numero_guia'],
                                "id_centi"=>$cabecera['codigo_entidad_transporte'],
                                "cmotivo"=>$cabecera['motivo_traslado'],
                                "ccodmodtrasl"=>$cabecera['codigo_modalidad'],
                                "cdesmodtrasl"=>$cabecera['motivo_traslado'],
                                "ctipoenvio"=>$cabecera['codigo_tipo'],
                                "nbultos"=>$cabecera['nro_bultos'],
                                "npesotot"=>$cabecera['peso_bruto'],
                                "cdniconduc"=>$cabecera['dni_conductor'],
                                "cdesconduc"=>$cabecera['nombre_conductor'],
                                "cnrolicen"=>$cabecera['licencia_conducir'],
                                "cnrocert"=>$cabecera['nro_certificado'],
                                "cmarcaveh"=>$cabecera['marca'],
                                "cplacaveh"=>$cabecera['placa'],
                                "cconfigveh"=>$cabecera['configuracion'],
                                "ncodalm1"=>$cabecera['codigo_origen'],
                                "ncodalm2"=>$cabecera['codigo_destino'],
                                "nEstadoImp"=>null,
                                "cdocPDF"=>$filename,
                                "nflgactivo"=>1,
                                "salida"=>$despacho]);
                $rowCount = $sql->rowcount();

                if ($rowCount > 0){
                   $this->generarGuia($cabecera,$detalles,$filename);
                }
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function generarGuia($cabecera,$detalles,$archivo){
            try {
                require_once("public/formatos/guiaremision.php");
                
                $datos = json_decode($detalles);
                $nreg = count($datos);

                $pdf = new PDF($cabecera['numero_guia'],$cabecera['fgemision'],$cabecera['destinatario_ruc'],$cabecera['destinatario_razon'],$cabecera['destinatario_direccion'],
                                $cabecera['empresa_transporte_razon'],$cabecera['ruc_entidad_transporte'], $cabecera['direccion_entidad_transporte'],
                                $cabecera['almacen_origen_direccion'],null,
                                $cabecera['almacen_origen_dist'],null,$cabecera['ftransporte'],$cabecera['modalidad_traslado'],$cabecera['almacen_destino_direccion'],null,
                                null,$cabecera['almacen_destino_dpto'],$cabecera['marca'],$cabecera['placa'],$cabecera['nombre_conductor'],$cabecera['licencia_conducir'],'A4');
                $pdf->AliasNbPages();
                $pdf->AddPage();
                $pdf->SetWidths(array(10,15,15,147));
                $pdf->SetFillColor(255,255,255);
                $pdf->SetTextColor(0,0,0);
                
                $pdf->SetFont('Arial','',7);
                $lc = 0;
                $rc = 0;

                for($i=1;$i<=$nreg;$i++){
                    $pdf->SetX(13);
                    $pdf->SetCellHeight(5);
                    $pdf->SetAligns(array("R","R","C","L"));
                    $pdf->Row(array(str_pad($i,3,"0",STR_PAD_LEFT),
                                    $datos[$rc]->cantidad,
                                    $datos[$rc]->unidad,
                                    utf8_decode($datos[$rc]->codigo .' '. $datos[$rc]->descripcion .' '. $datos[$rc]->serie )));
                    $lc++;
                    $rc++;

                    if ($lc == 23) {
                        $pdf->AddPage();
                        $lc = 0;
                    }
                    
                    $pdf->Ln(1);
                    $pdf->SetX(13);
                    $pdf->MultiCell(190,2,utf8_decode($cabecera["observaciones"]));
                    $pdf->Ln(2);
                    $pdf->SetX(13);
                    $pdf->Cell(190,4,"Bultos : ".$cabecera["nro_bultos"],0,1);
                    $pdf->SetX(13);
                    $pdf->Cell(190,4,"Peso   : ".$cabecera["peso_bruto"]. "Kgs",0,1);
                    $pdf->Output($archivo,'F');
                    
                    $c = "PDFtoPrinter.exe ".$archivo;
                    $output = shell_exec($c);
                    
                    return $archivo;
                }

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function actualizarCabeceraPedido(){
            try {
                
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function actualizarDetallesPedido(){
            try {
                //code...
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function actualizarCabeceraDespacho(){
            try {
                //code...
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        } 
    }
?>