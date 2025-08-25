<?php
    class CajaChicaModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarPedidosUsuario(){
            try {
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
                                                WHERE
                                                    ibis.tb_pedidocab.usuario = :user 
                                                    AND ibis.tb_pedidocab.estadodoc = 230");
                $sql->execute(["user"=>$_SESSION['iduser']]);
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
                                                                                    total=:total,idcenti=:entidad");
                    $sql->execute([
                        "cost"=>$datos['codigo_costos'],
                        "area"=>$datos['codigo_area'],
                        "trans"=>$datos['codigo_transporte'],
                        "soli"=>$datos['codigo_solicitante'],
                        "mov"=>$datos['codigo_tipo'],
                        "emis"=>$datos['emision'],
                        "entrega"=>$datos['fecha_entrega'],
                        "estdoc"=>$datos['codigo_estado'],
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
                                        $datos['codigo_estado'],
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
                                "items"     =>$this->consultarReqId($indice,230,null));
                
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
                                                        total =:total,
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
    }
?>