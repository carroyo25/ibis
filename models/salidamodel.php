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

        public function importarItemIngresos($costos){
            try {
                $salida = "";
                
                $sql = $this->db->connect()->prepare("SELECT
                                                        alm_recepdet.niddeta,
                                                        alm_recepcab.ncodpry,
                                                        alm_recepdet.ncantidad,
                                                        alm_recepdet.id_cprod,
                                                        UPPER(
                                                            CONCAT_WS(
                                                                ' ',
                                                                cm_producto.cdesprod,
                                                                tb_pedidodet.observaciones
                                                            )
                                                        ) AS descripcion,
                                                        cm_producto.ccodprod,
                                                        tb_unimed.cabrevia,
                                                        UPPER(
                                                            CONCAT_WS(
                                                                ' ',
                                                                tb_proyectos.ccodproy,
                                                                tb_proyectos.cdesproy
                                                            )
                                                        ) AS costos,
                                                        UPPER(tb_area.cdesarea) AS area,
                                                        tb_area.ccodarea,
                                                        UPPER(tb_pedidocab.concepto) AS concepto,
                                                        LPAD(alm_recepdet.orden, 6, 0) AS orden,
                                                        LPAD(tb_pedidocab.nrodoc, 6, 0) AS pedido,
                                                        LPAD(alm_recepcab.id_regalm, 6, 0) AS nota_ingreso,
                                                        tb_partidas.cdescripcion AS partida,
                                                        DATE_FORMAT(
                                                            alm_recepcab.ffecdoc,
                                                            '%d/%m/%Y'
                                                        ) AS fecha_recepcion,
                                                        alm_recepcab.ncodalm1 AS codigo_almacen_origen,
                                                        UPPER(tb_almacen.cdesalm) AS almacen_origen,
                                                        alm_despachodet.nsaldo
                                                    FROM
                                                        tb_costusu
                                                    INNER JOIN alm_recepcab ON tb_costusu.ncodproy = alm_recepcab.ncodpry
                                                    INNER JOIN alm_recepdet ON alm_recepcab.id_regalm = alm_recepdet.id_regalm
                                                    INNER JOIN cm_producto ON alm_recepdet.id_cprod = cm_producto.id_cprod
                                                    INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed
                                                    INNER JOIN tb_pedidodet ON alm_recepdet.niddetaPed = tb_pedidodet.iditem
                                                    INNER JOIN tb_proyectos ON tb_pedidodet.idcostos = tb_proyectos.nidreg
                                                    INNER JOIN tb_area ON tb_pedidodet.idarea = tb_area.ncodarea
                                                    INNER JOIN tb_pedidocab ON tb_pedidodet.idpedido = tb_pedidocab.idreg
                                                    INNER JOIN lg_ordencab ON tb_pedidocab.idorden = lg_ordencab.id_regmov
                                                    LEFT JOIN tb_partidas ON tb_pedidocab.idpartida = tb_partidas.idreg
                                                    INNER JOIN tb_almacen ON alm_recepcab.ncodalm1 = tb_almacen.ncodalm
                                                    LEFT JOIN alm_despachodet ON alm_recepdet.niddeta = alm_despachodet.niddetaIng
                                                    WHERE
                                                        tb_costusu.nflgactivo = 1
                                                    AND tb_costusu.id_cuser = :user
                                                    AND alm_recepcab.nEstadoDoc = 62
                                                    AND alm_recepcab.ncodpry = :costos
                                                    AND (alm_despachodet.nsaldo > 0 OR ISNULL(alm_despachodet.nsaldo))");
                $sql->execute(["user"=>$_SESSION['iduser'],
                                "costos"=>$costos]);
                $rowCount = $sql->rowCount();
                $item = 1;

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()) {
                        $sw = false;

                        if ( $this->calcularDespachos($rs['niddeta']) == $rs['ncantidad']) {  
                            $sw = true;
                        } ;

                        if (!$sw) {
                            $salida .='<tr class="pointer" 
                                            data-indice="'.$rs['niddeta'].'"
                                            data-codigocostos="'.$rs['ncodpry'].'"
                                            data-codigoarea="'.$rs['ccodarea'].'"
                                            data-codigoalmaenorigen="'.$rs['codigo_almacen_origen'].'"
                                            data-costos="'.$rs['costos'].'"
                                            data-almacen="'.$rs['almacen_origen'].'">
                                        <td class="textoCentro">'.str_pad($item++,3,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.$rs['fecha_recepcion'].'</td>
                                        <td class="pl20px">'.$rs['costos'].'</td>
                                        <td class="textoCentro">'.$rs['pedido'].'</td>
                                        <td class="textoCentro">'.$rs['orden'].'</td>
                                        <td class="textoCentro">'.$rs['nota_ingreso'].'</td>
                                        <td class="pl20px">'.$rs['area'].'</td>
                                        <td class="pl20px">'.$rs['concepto'].'</td>
                                        <td class="pl20px">'.$rs['ccodprod'].'</td>
                                        <td class="pl20px">'.$rs['descripcion'].'</td>
                                   </tr>';
                        }
                        
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function detallesNotaIngreso($id){
            try {
                $salida = "";
                $sql = $this->db->connect()->prepare("SELECT
                                                        alm_recepdet.id_regalm,
                                                        alm_recepdet.ncodalm1,
                                                        alm_recepdet.fvence,
                                                        alm_recepdet.ncantidad,
                                                        alm_recepdet.id_cprod,
                                                        alm_recepdet.niddetaPed,
                                                        alm_recepdet.niddetaOrd,
                                                        alm_recepdet.niddeta,
                                                        cm_producto.ccodprod,
                                                        FORMAT(alm_recepdet.ncantidad, 2) AS cantidad,
                                                        UPPER(
                                                            CONCAT_WS(
                                                                ' ',
                                                                cm_producto.cdesprod,
                                                                tb_pedidodet.observaciones
                                                            )
                                                        ) AS cdesprod,
                                                        tb_unimed.nfactor,
                                                        tb_unimed.cabrevia,
                                                        alm_recepdet.nestadoreg
                                                    FROM
                                                        alm_recepdet
                                                    INNER JOIN cm_producto ON alm_recepdet.id_cprod = cm_producto.id_cprod
                                                    INNER JOIN tb_pedidodet ON alm_recepdet.niddetaPed = tb_pedidodet.iditem
                                                    INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed
                                                    WHERE
                                                        alm_recepdet.id_regalm = :id");
                $sql->execute(["id"=>$id]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    $item = 1;
                    while ($rs = $sql->fetch()){

                        //$estados = $this->listarSelect(13,$rs['nestadoreg']);

                        $series = $this->buscarSeries($rs['id_cprod'],$rs['id_regalm'],$rs['ncodalm1']);

                        $fecha = $rs['fvence'] == "" ? date("d/m/Y", strtotime($rs['fvence'])) : "";

                        $salida.='<tr data-itemorden="'.$rs['niddetaOrd'].'" 
                                        data-itempedido="'.$rs['niddetaPed'].'" 
                                        data-itemingreso="'.$rs['niddeta'].'"
                                        data-idproducto ="'.$rs['id_cprod'].'"
                                        data-recepcion ="'.$rs['id_regalm'].'">
                                        <td class="textoCentro"><input type="checkbox"></td>
                                        <td class="textoCentro">'.str_pad($item++,3,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                        <td class="pl20px">'.$rs['cdesprod'].' '.$series.'</td>
                                        <td class="textoCentro">'.$rs['cabrevia'].'</td>
                                        <td class="textoDerecha pr20px">'.$rs['cantidad'].'</td>
                                        <td><input type="number" step="any" onchange="(function(el){el.value=parseFloat(el.value).toFixed(2);})(this)"
                                            value=""></td>
                                        <td class="pl20px"><input type="text"></td>
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
                                "ncodarea"=>$cabecera['codigo_area'],
                                "idref_pedi"=>$cabecera['codigo_pedido'],
                                "idref_ord"=>$cabecera['codigo_orden'],
                                "idref_abas"=>$cabecera['codigo_ingreso'],
                                "nnronota"=>$numero['numero'],
                                "id_userAprob"=>$cabecera['codigo_aprueba'],
                                "nEstadoDoc"=>62,
                                "nflgactivo"=>1,
                                "nguia"=>$cabecera['guia']]);
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
                                                                                            niddetaOrd=:idorden,
                                                                                            nflgactivo=:flag,
                                                                                            nestadoreg=:estadoItem,
                                                                                            ingreso=:ingreso,
                                                                                            nsaldo=:saldo,
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
                                        "ser"=>$datos[$i]->serie,
                                        "idpedido"=>$datos[$i]->iddetped,
                                        "idorden"=>$datos[$i]->iddetorden,
                                        "flag"=>1,
                                        "estadoItem"=>$datos[$i]->nestado,
                                        "ingreso"=>$datos[$i]->ingreso,
                                        "saldo"=>$datos[$i]->saldo,
                                        "destino"=>$datos[$i]->destino,
                                        "candesp"=>$datos[$i]->cantdesp,
                                        "itemIngreso"=>$datos[$i]->idingreso,
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

        public function actualizarProcesos($detalles,$despacho,$pedido,$orden,$ingreso){
            $this->actualizarCabeceraPedido($pedido,67);
            $this->actualizarCabeceraOrden($orden,67);
            $this->actualizarDetallesPedido($detalles,$despacho,67);
            $this->actualizarCabeceraDespacho($despacho,67);
            $this->actualizarCabeceraIngreso($ingreso,67);
        
            $despachos  = $this->listarNotasDespacho();

            return $despachos;
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
                                                    UPPER(destino.cdesalm) AS destino,
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
                                                        alm_despachodet.nropedido,
                                                        alm_despachodet.nroorden,
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
                                                        tb_pedidocab.nrodoc,
                                                        lg_ordencab.id_regmov 
                                                    FROM
                                                        alm_despachodet
                                                    INNER JOIN cm_producto ON alm_despachodet.id_cprod = cm_producto.id_cprod
                                                    INNER JOIN tb_pedidodet ON alm_despachodet.niddetaPed = tb_pedidodet.iditem
                                                    INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed
                                                    INNER JOIN tb_pedidocab ON alm_despachodet.nropedido = tb_pedidocab.idreg
                                                    INNER JOIN lg_ordencab ON alm_despachodet.nroorden = lg_ordencab.id_regmov
                                                    WHERE
                                                        alm_despachodet.id_regalm = :id");
                $sql->execute(["id"=>$indice]);

                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    $item = 1;
                    while ($rs = $sql->fetch()){

                        $fecha = $rs['fvence'] == "0000-00-00" ? "" : date("d-m-Y", strtotime($rs['fvence']));
                        $series = $this->buscarSeries($rs['id_cprod'],$rs['id_regalm'],$rs['ncodalm1']);

                        $salida.='<tr data-itemorden="'.$rs['niddetaOrd'].'" 
                                        data-itempedido="'.$rs['niddetaPed'].'" 
                                        data-itemingreso="'.$rs['niddetaIng'].'"
                                        data-itemdespacho="'.$rs['niddeta'].'"
                                        data-idproducto ="'.$rs['id_cprod'].'">
                                        <td class="textoCentro"><input type="checkbox"></td>
                                        <td class="textoCentro">'.str_pad($item,3,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                        <td class="pl20px">'.$rs['cdesprod'].'  :'.$series.'</td>
                                        <td class="textoCentro">'.$rs['cabrevia'].'</td>
                                        <td class="textoDerecha pr20px">'.$rs['cantidad'].'</td>
                                        <td><input type="number" step="any" onchange="(function(el){el.value=parseFloat(el.value).toFixed(2);})(this)"
                                            value="'.$rs['nsaldo'].'" readonly></td>
                                        <td class="pl20px"><input type="text" value="'.$rs['ndespacho'].'"></td>
                                        <td></td>
                                        <td class="pr20px"><input type="text" value="'.$rs['cobserva'].'"></td>
                                        <td class="textoCentro">'.str_pad($rs['nrodoc'],6,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.str_pad($rs['id_regmov'],6,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.str_pad($rs['ingreso'],6,0,STR_PAD_LEFT).'</td>
                                    </tr>';
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function grabarGuiaRemision($cabecera,$detalles,$despacho,$pedido,$orden,$ingreso){
            try {

                $filename = "public/documentos/guias_remision/".$cabecera['numero_guia'].".pdf";
                
                $sql = $this->db->connect()->prepare("INSERT lg_docusunat SET ccodtdoc=:ccodtdoc,ffechdoc=:ffechdoc,ffechreg=:ffechreg,ffechtrasl=:ffechtrasl,cserie=:cserie,
                                                                            cnumero=:cnumero,id_centi=:id_centi,cmotivo=:cmotivo,ccodmodtrasl=:ccodmodtrasl,cdesmodtrasl=:cdesmodtrasl,
                                                                            ctipoenvio=:ctipoenvio,nbultos=:nbultos,npesotot=:npesotot,cdniconduc=:cdniconduc,cdesconduc=:cdesconduc,
                                                                            cnrolicen=:cnrolicen,cnrocert=:cnrocert,cmarcaveh=:cmarcaveh,cplacaveh=:cplacaveh,cconfigveh=:cconfigveh,
                                                                            ncodalm1=:ncodalm1,ncodalm2=:ncodalm2,nEstadoImp=:nEstadoImp,cdocPDF=:cdocPDF,nflgactivo=:nflgactivo,
                                                                            id_despacho=:salida,nEstadoDoc=:estado,orden=:nrorden");
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
                                "salida"=>$despacho,
                                "estado"=>60,
                                "nrorden"=>$orden]);
                $rowCount = $sql->rowcount();

                if ($rowCount > 0){
                    $this->actualizarCabeceraDespacho($despacho,67);
                    $this->actualizarDetallesDespacho($detalles,$cabecera['numero_guia'],67);
                    $this->actualizarDetallesPedido($detalles,$despacho,67);

                    return $this->generarGuia($cabecera,$detalles,$filename);
                }
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function actualizarCabeceraDespacho($despacho,$estado){
            try {
                $sql = $this->db->connect()->prepare("UPDATE alm_despachocab SET nEstadoDoc=:estado WHERE id_regalm=:despacho");
                $sql->execute(["estado"=>$estado,
                                "despacho"=>$despacho]);
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function actualizarDetallesDespacho($detalles,$guia,$estado){
            try {
                $datos = json_decode($detalles);
                $nreg = count($datos);

                for ($i=0; $i < $nreg; $i++) { 
                    $sql = $this->db->connect()->prepare("UPDATE alm_despachodet SET nestadoreg =:estado, nGuia =:guia WHERE niddeta=:id");
                    $sql->execute(["id"=>$datos[$i]->iddespacho,"guia"=>$guia,"estado"=>$estado]);

                }
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function actualizarDetallesPedido($detalles,$despacho,$estado){
            try {
                $datos = json_decode($detalles);
                $nreg = count($datos);

                for ($i=0; $i < $nreg; $i++) { 
                    try {
                        $sql = $this->db->connect()->prepare("UPDATE tb_pedidodet SET estadoItem=:estado,
                                                                                        cant_env=:enviado,
                                                                                        iddespacho=:despacho
                                                                                 WHERE iditem=:id");
                        $sql->execute(["estado"=>$estado,
                                        "id"=>$datos[$i]->iddetped,
                                        "enviado"=>$datos[$i]->cantidad,
                                        "despacho"=>$despacho]);
                    } catch (PDOException $th) {
                        echo "Error: " . $th->getMessage();
                        return false;
                    }
                }
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function calcularSaldosIngresos($recepcion){
            try {
                $sql = $this->db->connect()->prepare("SELECT SUM(ncantidad) AS nSaldo FROM alm_recepdet WHERE id_regalm = :rec");
                $sql->execute(["rec"=>$recepcion]);
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function calcularSaldosDespachos($despacho){
            try {
                $sql = $this->db->connect()->prepare("SELECT SUM(ncantidad) FROM alm_recepcab WHERE id_regalm = :rec");
                $sql->execute(["rec"=>$despacho]);
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

                //aca podria sumar la orden

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
                    
                    return $archivo;

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function actualizarCabeceraPedido($pedido,$estado){
            try {
                $sql = $this->db->connect()->prepare("UPDATE tb_pedidocab SET estadodoc=:estado WHERE idreg=:pedido");
                $sql->execute(["estado"=>$estado,
                                "pedido"=>$pedido]);
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function actualizarCabeceraOrden($orden,$estado){
            try {
                $sql = $this->db->connect()->prepare("UPDATE lg_ordencab SET nEstadoDoc=:estado WHERE id_regmov=:orden");
                $sql->execute(["estado"=>$estado,
                                "orden"=>$orden]);
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function actualizarCabeceraIngreso($ingreso,$estado){
            try {
                $sql = $this->db->connect()->prepare("UPDATE alm_recepcab SET nEstadoDoc=:estado WHERE id_regalm=:ingreso");
                $sql->execute(["estado"=>$estado,
                                "ingreso"=>$ingreso]);
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function consultarAlmacenes($origen,$destino){
            try {
                $almorigen = $this->datosAlmacen($origen);
                $almdestino = $this->datosAlmacen($destino);

                return array($almorigen,$almdestino);

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function datosAlmacen($almacen){
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                            ncodalm,
                                                            UPPER(cdesalm) AS almacen,
                                                            UPPER(
                                                                CONCAT_WS(' ', cdesvia, cnrovia)
                                                            ) AS direccion,
                                                            distritos.cdubigeo AS dist,
                                                            provincias.cdubigeo AS prov,
                                                            dptos.cdubigeo AS dpto
                                                        FROM
                                                            tb_almacen
                                                        LEFT JOIN tb_ubigeo AS distritos ON tb_almacen.ncubigeo = distritos.ccubigeo
                                                        LEFT JOIN tb_ubigeo AS provincias ON SUBSTR(tb_almacen.ncubigeo, 1, 4) = provincias.ccubigeo
                                                        LEFT JOIN tb_ubigeo AS dptos ON SUBSTR(tb_almacen.ncubigeo, 1, 2) = dptos.ccubigeo
                                                        WHERE
                                                            nflgactivo = 1
                                                        AND tb_almacen.ncodalm = :id");
                $sql->execute(["id"=>$almacen]);
                $result = $sql->fetchAll();

                return $result[0];
                
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function buscarItemRecepcion($id) {
            try {
                $salida = "";
                $sql= $this->db->connect()->prepare("SELECT
                                                        alm_recepdet.ncantidad,
                                                        alm_recepdet.niddetaPed,
                                                        alm_recepdet.niddetaOrd,
                                                        LPAD(alm_recepdet.orden, 6, 0) AS orden,
                                                        cm_producto.ccodprod,
                                                        cm_producto.id_cprod,
                                                        UPPER(
                                                            CONCAT_WS(
                                                                ' ',
                                                                cm_producto.cdesprod,
                                                                tb_pedidodet.observaciones
                                                            )
                                                        ) AS descripcion,
                                                        tb_unimed.cabrevia,
                                                        LPAD(alm_recepcab.id_regalm, 6, 0) AS nota_ingreso,
                                                        LPAD(tb_pedidocab.nrodoc, 6, 0) AS pedido,
                                                        alm_recepdet.niddeta,
                                                    
                                                    IF (
                                                        ISNULL(alm_despachodet.nsaldo),
                                                        0,
                                                        alm_despachodet.nsaldo
                                                    ) AS saldo
                                                    FROM
                                                        alm_recepdet
                                                    INNER JOIN cm_producto ON alm_recepdet.id_cprod = cm_producto.id_cprod
                                                    INNER JOIN tb_pedidodet ON alm_recepdet.niddetaPed = tb_pedidodet.iditem
                                                    INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed
                                                    INNER JOIN alm_recepcab ON alm_recepdet.id_regalm = alm_recepcab.id_regalm
                                                    INNER JOIN tb_pedidocab ON tb_pedidodet.idpedido = tb_pedidocab.idreg
                                                    LEFT JOIN alm_despachodet ON alm_recepdet.niddeta = alm_despachodet.niddetaIng
                                                    WHERE
                                                        alm_recepdet.niddeta = :id
                                                    LIMIT 1");
                $sql->execute(["id"=>$id]);
                $rowCount = $sql->rowcount();
                $result = $sql->fetchAll();

                if ($rowCount > 0) {
                        $saldo = $result[0]['saldo'];

                        if ( $result[0]['saldo'] == 0.00) {
                            $saldo = $result[0]['ncantidad'];
                        }

                         $salida = '<tr data-idproducto="'.$result[0]['id_cprod'].'"
                                   data-idpedido="'.$result[0]['niddetaPed'].'"
                                   data-idorden="'.$result[0]['niddetaOrd'].'"
                                   data-idingreso="'.$result[0]['niddeta'].'"
                                   data-pedido="'.$result[0]['pedido'].'"
                                   data-orden="'.$result[0]['orden'].'"
                                   data-ingreso="'.$result[0]['nota_ingreso'].'">
                                    <td class="textoCentro"><a href="'.$result[0]['niddeta'].'"><i class="fas fa-trash"></i></a></td>
                                    <td class="textoCentro"></td>
                                    <td class="textoCentro">'.$result[0]['ccodprod'].'</td>
                                    <td class="pl20px">'.$result[0]['descripcion'].'</td>
                                    <td class="textoCentro">'.$result[0]['cabrevia'].'</td>
                                    <td class="textoDerecha pr20px">'.$result[0]['ncantidad'].'</td>
                                    <td class="textoDerecha pr20px">'.$saldo.'</td>
                                    <td class="textoDerecha pr5px">
                                        <input type="number" 
                                            step="any" 
                                            placeholder="0.00" 
                                            onchange="(function(el){el.value=parseFloat(el.value).toFixed(2);})(this)"
                                            onclick="this.select()">
                                    </td>
                                    <td class="textoDerecha pr5px"></td>
                                    <td>
                                        <input type="text">
                                    </td>
                                    <td class="textoCentro">'.$result[0]['pedido'].'</td>
                                    <td class="textoCentro">'.$result[0]['orden'].'</td>
                                    <td class="textoCentro">'.$result[0]['nota_ingreso'].'</td>
                                </tr>';
                }

                return $salida;

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function calcularDespachos($id) {
            try {
                $sql = $this->db->connect()->prepare("SELECT SUM(ndespacho) AS saldo ,ncantidad FROM alm_despachodet WHERE niddetaIng =:id");
                $sql->execute(["id" => $id]);

                $result = $sql->fetchAll();

                return $result[0]['saldo'];

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function listarDespachosFiltrados(){
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
    } 
?>