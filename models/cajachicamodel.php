<?php
    class CajaChicaModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarPedidosUsuario($parametros){
            try {
                $anio = 2025;
                $costos = '%';

                if (isset($_POST)){
                    $costos = isset($_POST['costosSearch']) && $_POST['costosSearch'] !=-1 ? $_POST['costosSearch'] : '%';
                    $anio  = isset($_POST['anioSearch']) && $_POST['anioSearch'] != '' ? $_POST['anioSearch'] : '%';
                }

                $salida = "";
                $sql = $this->db->connect()->prepare("SELECT
                                                    ibis.tb_pedidocab.idreg,
                                                    ibis.tb_pedidocab.idcostos,
                                                    ibis.tb_pedidocab.idarea,
                                                    ibis.tb_pedidocab.emision,
                                                    ibis.tb_pedidocab.vence,
                                                    ibis.tb_pedidocab.idtipomov,
                                                    ibis.tb_pedidocab.estadodoc,
                                                    ibis.tb_pedidocab.nrodoc,
                                                    ibis.tb_pedidocab.ntotal,
                                                    ibis.tb_pedidocab.idcenti,
                                                    UPPER(ibis.tb_pedidocab.concepto) AS concepto,
                                                    CONCAT(rrhh.tabla_aquarius.nombres,' ',rrhh.tabla_aquarius.apellidos) AS nombres,
                                                    UPPER(CONCAT(ibis.tb_proyectos.ccodproy,' ',ibis.tb_proyectos.cdesproy)) AS costos,
                                                    ibis.tb_pedidocab.nivelAten,
                                                    atenciones.cdescripcion AS atencion,
                                                    estados.cdescripcion AS estado,
                                                    estados.cabrevia 
                                                FROM
                                                    ibis.tb_pedidocab
                                                    INNER JOIN rrhh.tabla_aquarius ON ibis.tb_pedidocab.idsolicita = rrhh.tabla_aquarius.internal
                                                    INNER JOIN ibis.tb_proyectos ON ibis.tb_pedidocab.idcostos = ibis.tb_proyectos.nidreg
                                                    INNER JOIN ibis.tb_parametros AS atenciones ON ibis.tb_pedidocab.nivelAten = atenciones.nidreg
                                                    INNER JOIN ibis.tb_parametros AS estados ON ibis.tb_pedidocab.estadodoc = estados.nidreg
                                                    INNER JOIN ibis.cm_entidad AS entidad ON ibis.tb_pedidocab.idcenti = entidad.id_centi
                                                WHERE
                                                    ibis.tb_pedidocab.usuario = :user 
                                                    AND ibis.tb_pedidocab.estadodoc = 230
                                                    AND ibis.tb_pedidocab.idcostos LIKE :costos
                                                    AND ibis.tb_pedidocab.anio LIKE :anio");
                $sql->execute(["user"=>$_SESSION['iduser'],
                                "costos"=>$costos,
                                "anio"=>$anio]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()) {
                        $tipo = $rs['idtipomov'] == 37 ? "B":"S";
                        $salida .='<tr class="pointer" data-indice="'.$rs['idreg'].'">
                                        <td class="textoCentro">'.str_pad($rs['nrodoc'],4,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.date("d/m/Y", strtotime($rs['emision'])).'</td>
                                        <td class="textoCentro">'.$tipo.'</td>
                                        <td class="pl20px">'.$rs['concepto'].'</td>
                                        <td class="pl20px">'.$rs['costos'].'</td>
                                        <td class="pl20px">'.$rs['nombres'].'</td>
                                        <td class="textoCentro '.$rs['cabrevia'].'">'.$rs['estado'].'</td>
                                        <td class="textoCentro '.strtolower($rs['atencion']).'">'.$rs['atencion'].'</td>
                                        <td class="textoCentro"><a href="'.$rs['idreg'].'"><i class="fa fa-trash-alt"></i></a></td>
                                    </tr>';
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function insertarCompra($datos,$detalles){
            try {
                $salida = false;
                $respuesta = false;
                $mensaje = "Error en el registro";
                $clase = "mensaje_error";

                if ( $datos['codigo_usuario'] != "" ){
                    $numero = $this->generarNumero($datos['codigo_costos'],"SELECT COUNT(idreg) AS numero FROM tb_pedidocab WHERE tb_pedidocab.idcostos =:cod");
               
                    $cmes = date("m",strtotime($datos['emision']));
                    $cper = date("Y",strtotime($datos['emision']));

                    $sql = $this->db->connect()->prepare("INSERT INTO tb_pedidocab SET idcostos=:cost,idarea=:area,idtrans=:trans,idsolicita=:soli,idtipomov=:mov,
                                                                                    emision=:emis,fentregaPedido=:entrega,estadodoc=:estdoc,nrodoc=:nro,usuario=:user,
                                                                                    anio=:ano,mes=:mes,concepto=:concep,detalle=:det,nivelAten=:aten,
                                                                                    docfPdfPrev=:dprev,nflgactivo=:est,verificacion=:ver,idpartida=:partida,
                                                                                    ntotal=:total,idcenti=:entidad");
                    $sql->execute([
                        "cost"=>$datos['codigo_costos'],
                        "area"=>$datos['codigo_area'],
                        "trans"=>$datos['codigo_transporte'],
                        "soli"=>$datos['codigo_solicitante'],
                        "mov"=>$datos['codigo_tipo'],
                        "emis"=>$datos['emision'],
                        "entrega"=>$datos['fecha_entrega'],
                        "estdoc"=>230,
                        "user"=>$datos['codigo_usuario'],
                        "nro"=>$numero['numero'],
                        "ano"=>$cper,
                        "mes"=>$cmes,
                        "concep"=>$datos['concepto'],
                        "det"=>$datos['espec_items'],
                        "aten"=>47,
                        "dprev"=>$datos['vista_previa'],
                        "est"=>1,
                        "ver"=>$datos['codigo_verificacion'],
                        "partida"=>$datos['codigo_partida'],
                        "total"=>$datos['total'],
                        "entidad"=>$datos['codigo_entidad']
                    ]);

                    $rowCount = $sql->rowCount();
                    

                    if ($rowCount > 0){
                        $indice = $this->ultimoIndiceTabla("SELECT MAX(idreg) AS indice FROM tb_pedidocab");

                        $this->saveItemsCompras($datos['codigo_verificacion'],
                                        230,
                                        $datos['codigo_atencion'],
                                        $datos['codigo_tipo'],
                                        $datos['codigo_costos'],
                                        $datos['codigo_area'],
                                        $detalles,
                                        $indice);
                        $respuesta = true;
                        $mensaje = "Pedido Grabado";
                        $clase = "mensaje_correcto";
                        
                    }
                }else {
                    $mensaje = "Error al registrar el pedido";
                    $indice = 0;
                }

                $salida = array("respuesta" =>$respuesta,
                                "mensaje"   =>$mensaje,
                                "clase"     =>$clase,
                                "indice"    =>$indice,
                                "numero"    =>$numero['numero'],
                                "items"     =>$this->consultarReqId($indice,230,null,null));
                
                return $salida;
                
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function modificarCompra($datos,$detalles){
            try {
                $salida = false;
                $respuesta = false;
                $mensaje = "Error en el registro";
                $clase = "mensaje_error";
                $rowDetails = 0;

                $sql = $this->db->connect()->prepare("UPDATE tb_pedidocab 
                                                    SET fentregaPedido=:entrega,
                                                        concepto=:concep,
                                                        detalle=:det,
                                                        nivelAten=:aten,
                                                        docfPdfPrev=:dprev,
                                                        ntotal =:total,
                                                        idcenti =:entidad,
                                                    WHERE idreg=:id");
                 $sql->execute([
                    "entrega"=>$datos['fecha_entrega'],
                    "concep"=>$datos['concepto'],
                    "det"=>$datos['espec_items'],
                    "aten"=>$datos['codigo_atencion'],
                    "dprev"=>$datos['vista_previa'],
                    "total"=>$datos['total'],
                    "entidad"=>$datos['codigo_entidad'],
                    "id"=>$datos['codigo_pedido']
                ]);

                $rowCount = $sql->rowCount();

                $details = json_decode($detalles);
                $nreg = count($details);
                
                for ($i=0; $i < $nreg; $i++) { 
                    //graba el item si no se ha insertado como nuevo
                    if( $details[$i]->itempedido == '-' ){
                        $this->saveItemCompras($datos['codigo_verificacion'],
                                        $datos['codigo_estado'],
                                        $datos['codigo_atencion'],
                                        $datos['codigo_tipo'],
                                        $datos['codigo_costos'],
                                        $datos['codigo_area'],
                                        $datos['cantidad'],
                                        $datos['precio'],
                                        $datos['total'],
                                        $details[$i]);
                    }else{
                    //cambia los datos 
                        for ($i=0; $i < count($details); $i++) { 
                            $rowDetails = $this->updateItems($datos['codigo_atencion'],
                                                             $details[$i]->cantidad,
                                                             $details[$i]->calidad,
                                                             $details[$i]->itempedido,
                                                             $details[$i]->especifica,
                                                             $details[$i]->precio,
                                                             $details[$i]->total);
                         }
                    }
                }

                if ($rowCount > 0 || $rowDetails > 0){
                    $respuesta = true;
                    $mensaje = "Pedido Modificado";
                    $clase = "mensaje_correcto";
                }else{
                    $respuesta = true;
                    $mensaje = "Pedido Modificado";
                    $clase = "mensaje_correcto";
                }

                $salida = array("respuesta"=>$respuesta,
                                "mensaje"=>$mensaje,
                                "clase"=>$clase,
                                "items"=>$this->consultarReqId($datos['codigo_pedido'],49,50,49,null));

                
                return $salida;

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }            
        }

        public function consultarReqIdCaja($id,$min,$max,$proceso){
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                        ibis.tb_pedidocab.idreg, 
                                                        ibis.tb_pedidocab.idcostos, 
                                                        ibis.tb_pedidocab.idarea, 
                                                        ibis.tb_pedidocab.idtrans, 
                                                        ibis.tb_pedidocab.idsolicita, 
                                                        ibis.tb_pedidocab.idtipomov, 
                                                        ibis.tb_pedidocab.emision, 
                                                        ibis.tb_pedidocab.vence, 
                                                        ibis.tb_pedidocab.estadodoc, 
                                                        ibis.tb_pedidocab.nrodoc, 
                                                        ibis.tb_pedidocab.usuario, 
                                                        UPPER(ibis.tb_pedidocab.concepto) AS concepto, 
                                                        ibis.tb_pedidocab.detalle, 
                                                        ibis.tb_pedidocab.nivelAten, 
                                                        ibis.tb_pedidocab.docfPdfPrev, 
                                                        ibis.tb_pedidocab.docPdfEmit, 
                                                        ibis.tb_pedidocab.docPdfAprob, 
                                                        ibis.tb_pedidocab.verificacion,
                                                        ibis.tb_pedidocab.aprueba,
                                                        ibis.tb_pedidocab.asigna,
                                                        ibis.tb_pedidocab.faprueba,
                                                        REPLACE(FORMAT(ibis.tb_pedidocab.ntotal,2),',','') AS ntotal,
                                                        ibis.tb_pedidocab.idcenti,
                                                        UPPER(ibis.entidad.crazonsoc) AS entidad,
                                                        CONCAT( rrhh.tabla_aquarius.apellidos, ' ', rrhh.tabla_aquarius.nombres ) AS nombres, 
                                                        UPPER(
                                                        CONCAT( ibis.tb_proyectos.ccodproy, ' ', ibis.tb_proyectos.cdesproy )) AS proyecto, 
                                                        UPPER(
                                                        CONCAT( ibis.tb_area.ccodarea, ' ', ibis.tb_area.cdesarea )) AS area, 
                                                        UPPER(
                                                        CONCAT( ibis.tb_parametros.nidreg, ' ', ibis.tb_parametros.cdescripcion )) AS transporte,
                                                        tb_parametros.cobservacion, 
                                                        estados.cdescripcion AS estado, 
                                                        estados.cabrevia, 
                                                        UPPER(
                                                        CONCAT_WS( ' ', tipos.nidreg, tipos.cdescripcion )) AS tipo, 
                                                        ibis.tb_proyectos.veralm, 
                                                        ibis.tb_user.cnombres,
                                                        ibis.tb_partidas.cdescripcion,
                                                        ibis.tb_pedidocab.idpartida
                                                    FROM
                                                        ibis.tb_pedidocab
                                                        LEFT JOIN
                                                        rrhh.tabla_aquarius
                                                        ON 
                                                            ibis.tb_pedidocab.idsolicita = rrhh.tabla_aquarius.internal
                                                        INNER JOIN
                                                    ibis.tb_proyectos ON ibis.tb_pedidocab.idcostos = ibis.tb_proyectos.nidreg
                                                    INNER JOIN ibis.tb_area ON ibis.tb_pedidocab.idarea = ibis.tb_area.ncodarea
                                                    INNER JOIN ibis.tb_parametros ON ibis.tb_pedidocab.idtrans = ibis.tb_parametros.nidreg
                                                    INNER JOIN ibis.tb_parametros AS transportes ON ibis.tb_pedidocab.idtrans = transportes.nidreg
                                                    INNER JOIN ibis.tb_parametros AS estados ON ibis.tb_pedidocab.estadodoc = estados.nidreg
                                                    INNER JOIN ibis.tb_parametros AS tipos ON ibis.tb_pedidocab.idtipomov = tipos.nidreg
                                                    INNER JOIN ibis.tb_user ON ibis.tb_pedidocab.usuario = ibis.tb_user.iduser
                                                    INNER JOIN ibis.cm_entidad AS entidad ON ibis.tb_pedidocab.idcenti = entidad.id_centi
                                                    LEFT JOIN ibis.tb_partidas ON ibis.tb_pedidocab.idpartida = ibis.tb_partidas.idreg 
                                                    WHERE
                                                        tb_pedidocab.idreg = :id");
                $sql->execute(['id'=>$id]);
                $rowCount = $sql->rowCount();
                
                if ($rowCount > 0) {
                    $docData = array();
                    while($row=$sql->fetch(PDO::FETCH_ASSOC)){
                        $docData[] = $row;
                    }
                }

                $detalles = $this->consultarDetallesCompra($id);

                return array("cabecera"=>$docData,
                            "detalles"=>$detalles,
                            "total_adjuntos"=>$this->contarAdjuntos($id,"PED"));
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        //Graba un solo Item de la modificacion
        private function saveItemCompras($codigo,$estado,$atencion,$tipo,$costos,$area,$detalles,$cantidad,$precio,$total){
            $indice = $this->obtenerIndice($codigo,"SELECT idreg AS numero FROM tb_pedidocab WHERE tb_pedidocab.verificacion =:id");

           try {
                $sql = $this->db->connect()->prepare("INSERT INTO tb_pedidodet SET idpedido=:ped,idprod=:prod,idtipo=:tipo,unid=:und,
                                                                                   cant_pedida=:cant,estadoItem=:est,tipoAten=:aten,
                                                                                   verificacion=:ver,nflgqaqc=:qaqc,idcostos=:costos,idarea=:area,
                                                                                   observaciones=:espec,item=:nropos,precio:=precio_unitario,total:=total");
                       $sql ->execute([
                                       "ped"=>$indice,
                                       "prod"=>$detalles->idprod,
                                       "tipo"=>$tipo,
                                       "und"=>$detalles->unidad,
                                       "cant"=>$detalles->cantidad,
                                       "est"=>$estado,
                                       "aten"=>$atencion,
                                       "ver"=>$codigo,
                                       "qaqc"=>$detalles->calidad,
                                       "costos"=>$costos,
                                       "area"=>$area,
                                       "espec"=>$detalles->especifica,
                                       "nropos"=>$detalles->item,
                                       "precio"=>$detalles->precio,
                                       "total"=>$detalles->total]);
                  
            } catch (PDOException $th) {
                   echo "Error: ".$th->getMessage();
                   return false;
            }
        }
       
        private function saveItemsCompras($codigo,$estado,$atencion,$tipo,$costos,$area,$detalles,$indice){

            $datos = json_decode($detalles);
            $nreg = count($datos);
            
            $sql = $this->db->connect()->prepare("INSERT INTO tb_pedidodet 
                                                                    SET idpedido=:ped,idprod=:prod,idtipo=:tipo,unid=:und,
                                                                        cant_pedida=:cant,cant_aprob=:aprob,estadoItem=:est,tipoAten=:aten,
                                                                        verificacion=:ver,nflgqaqc=:qaqc,idcostos=:costos,idarea=:area,
                                                                        observaciones=:espec,item=:nropos,precio=:precio,total=:total");

            for ($i=0; $i < $nreg; $i++) { 
                try {
                        //if ( $existe == 0) {
                            $sql ->execute([
                                "ped"=>$indice,
                                "prod"=>$datos[$i]->idprod,
                                "tipo"=>$tipo,
                                "und"=>$datos[$i]->unidad,
                                "cant"=>$datos[$i]->cantidad,
                                "est"=>$estado,
                                "aten"=>$atencion,
                                "ver"=>$codigo,
                                "qaqc"=>$datos[$i]->calidad,
                                "costos"=>$costos,
                                "area"=>$area,
                                "espec"=>$datos[$i]->especifica,
                                "nropos"=>$datos[$i]->item,
                                "aprob"=>$datos[$i]->cantidad,
                                "precio"=>$datos[$i]->precio,
                                "total"=>$datos[$i]->total]);
                        //}
                       
                   
                } catch (PDOException $th) {
                    echo "Error: ".$th->getMessage();
                    return false;
                }
            }
        }

        private function updateItems($aten,$cant,$qaqc,$idx,$especifica,$precio,$total){
            $sql = $this->db->connect()->prepare("UPDATE ibis.tb_pedidodet SET cant_pedida = :cant, 
                                        nflgqaqc = :qaqc,
                                        tipoAten = :aten,
                                        observaciones=:espec 
                                        precio=:precio,
                                        total=:total
                                        WHERE iditem = :id");
            $sql ->execute(["cant"=>$cant,
                            "qaqc"=>$qaqc,
                            "aten"=>$aten,
                            "espec"=>$especifica,
                            "id"=>$idx,
                            "precio"=>$precio,
                            "total"=>$total]);
            $rowCount = $sql->rowCount();
            return $rowCount;
        }

        private function consultarDetallesCompra($id){
            try {
                $salida ="";

                $sql=$this->db->connect()->prepare("SELECT
                                                        tb_pedidodet.iditem,
                                                        tb_pedidodet.idpedido,
                                                        tb_pedidodet.cant_atend,
                                                        tb_pedidodet.idprod,
                                                        tb_pedidodet.idtipo,
                                                        tb_pedidodet.estadoItem AS estado,
                                                        tb_pedidodet.nroparte,
                                                        tb_pedidodet.idorden,
                                                        tb_pedidodet.unid,
                                                        UPPER(tb_pedidodet.observaciones) AS observaciones,
                                                        REPLACE(FORMAT(tb_pedidodet.cant_pedida,2),',','') AS cant_pedida,
                                                        cm_producto.ccodprod,
                                                        UPPER(cm_producto.cdesprod) AS cdesprod,
                                                        tb_unimed.cabrevia,
                                                        tb_pedidodet.nflgqaqc,
                                                        tb_equipmtto.cdescripcion,
                                                        tb_equipmtto.cregistro,
                                                        REPLACE(FORMAT(tb_pedidodet.total,2),',','') AS total,
                                                        REPLACE(FORMAT(tb_pedidodet.precio,2),',','') AS precio
                                                    FROM
                                                        tb_pedidodet
                                                        LEFT JOIN cm_producto ON tb_pedidodet.idprod = cm_producto.id_cprod
                                                        LEFT JOIN tb_unimed ON tb_pedidodet.unid = tb_unimed.ncodmed
                                                        LEFT JOIN tb_equipmtto ON tb_pedidodet.nregistro = tb_equipmtto.idreg 
                                                    WHERE
                                                        tb_pedidodet.idpedido = :id 
                                                        AND tb_pedidodet.nflgActivo = 1");
                $sql->execute(["id"=>$id]);
                $rowCount = $sql->rowCount();
                
                if ($rowCount > 0){
                    $filas = 1;
                    while ( $rs = $sql->fetch() ) {

                        $checked = $rs['nflgqaqc'] == 1 ? "checked ": " ";
                        $anulado = $rs['estado'] == 105 ? "tituloClase": "";
                        $orden   = $rs['idorden'] == null ? "" : "tituloGrupo";
                        
                        $salida .='<tr data-grabado="1" 
                                        data-idprod="'.$rs['idprod'].'" 
                                        data-codund="'.$rs['unid'].'" 
                                        data-idx="'.$rs['iditem'].'"
                                        data-registro="'.$rs['idpedido'].'"
                                        data-estadoitem="'.$rs['estado'].'">
                                        <td class="textoCentro"><a href="'.$rs['iditem'].'" data-accion="eliminar" title="'.$rs['iditem'].'"><i class="fas fa-eraser"></i></a></td>
                                        <td class="textoCentro">'.str_pad($filas++,3,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro '.$anulado.' '.$orden.'">'.$rs['ccodprod'].'</td>
                                        <td class="pl20px">'.$rs['cdesprod'].'</td>
                                        <td class="textoCentro">'.$rs['cabrevia'].'</td>
                                        <td>
                                            <input type="number" 
                                                        step="any" 
                                                        placeholder="0.00" 
                                                        onchange="(function(el){el.value=parseFloat(el.value).toFixed(2);})(this)"
                                                        onclick="this.select()" 
                                                        value="'.$rs['cant_pedida'].'">
                                        </td>
                                        <td class="pl20px"><textarea>'.$rs['observaciones'].'</textarea></td>
                                        <td class="textoDerecha">'.$rs['precio'].'</td>
                                        <td class="textoDerecha">'.$rs['total'].'</td>
                                    </tr>';
                    }
                }
                
                return $salida;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function generarPedidoCaja($datos,$detalles){
            require_once('public/formatos/cajachica.php');
            
            $details = json_decode($detalles);
            $filename =  $datos['numero'].$datos['costos'].".pdf";

            $num = $datos['numero'];
            $fec = $datos['emision'];
            $usr = $_SESSION['iduser'];
            $pry = $datos['costos'];
            $are = $datos['area'];
            $cos = $datos['costos'];
            $tra = null;
            $con = $datos['concepto'];
            $sol = $datos['solicitante'];
            $esp = $datos['espec_items'];
            $tot = $datos['total'];
            
            $reg = ''; 
            $dti = "PEDIDO DE CAJA CHICA";
            $mmt = "";
            $cla = "NORMAL";
            $msj = "EMITIDO";
            $ruta = "public/documentos/pedidos/emitidos/";

            $pdf = new PDF($num,$fec,$pry,$cos,$are,$con,$mmt,$cla,$tra,$usr,$sol,$reg,$esp,$dti,$msj,"");
		    $pdf->AddPage();
            $pdf->AliasNbPages();
            $pdf->SetWidths(array(10,15,70,8,10,17,15,15,15,15));
            $pdf->SetFont('Arial','',5);
            $lc = 0;
            $rc = 0; 

            $nreg = count($details);

            for($i=1;$i<=$nreg;$i++){
                $registro = isset( $details[$rc]->activo) ? $details[$rc]->activo : "";

			    $pdf->SetAligns(array("L","L","L","L","R","L","L","L","L","L"));
                $pdf->Row(array($details[$rc]->item,
                                $details[$rc]->codigo,
                                utf8_decode($details[$rc]->descripcion."\n".$details[$rc]->especifica),
                                $details[$rc]->unidad,
                                $details[$rc]->cantidad,
                                '',
                                '',
                                '',
                                $details[$rc]->precio,
                                $registro));
                
                $lc++;
                $rc++;

                if ($lc == 54) {
				    $pdf->AddPage();
				    $lc = 0;
			    }	
		    }

            $pdf->Output($ruta.$filename,'F');
            
            return $filename;
        }
    }
?>