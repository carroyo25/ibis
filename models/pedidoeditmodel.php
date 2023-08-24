<?php
    class PedidoEditModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarPedidosUsuario($parametros){

            $anio = isset($parametros['anioSearch']) ? $parametros['anioSearch']:2023;
            $cc   = isset($parametros['costosSearch']) ? $parametros['costosSearch']: "%";
            $nu   = isset($parametros['numeroSearch']) ? $parametros['numeroSearch']: "%";

            $c = $cc == -1 ? "%":$cc;
            $n = $nu == "" ? "%":$nu;

            try {
                $salida = "";
                $sql = $this->db->connect()->prepare("SELECT
                                                    ibis.tb_pedidocab.idreg,
                                                    ibis.tb_pedidocab.idcostos,
                                                    ibis.tb_pedidocab.idarea,
                                                    ibis.tb_pedidocab.emision,
                                                    ibis.tb_pedidocab.vence,
                                                    ibis.tb_pedidocab.estadodoc,
                                                    ibis.tb_pedidocab.nrodoc,
                                                    ibis.tb_pedidocab.idtipomov,
                                                    UPPER(ibis.tb_pedidocab.concepto) AS concepto,
                                                    CONCAT(rrhh.tabla_aquarius.nombres,' ',rrhh.tabla_aquarius.apellidos) AS nombres,
                                                    UPPER(CONCAT(ibis.tb_proyectos.ccodproy,' ',ibis.tb_proyectos.cdesproy)) AS costos,
                                                    ibis.tb_pedidocab.nivelAten,
                                                    atenciones.cdescripcion AS atencion,
                                                    estados.cdescripcion AS estado,
                                                    estados.cabrevia 
                                                FROM
                                                    ibis.tb_pedidocab
                                                    LEFT JOIN rrhh.tabla_aquarius ON ibis.tb_pedidocab.idsolicita = rrhh.tabla_aquarius.internal
                                                    INNER JOIN ibis.tb_proyectos ON ibis.tb_pedidocab.idcostos = ibis.tb_proyectos.nidreg
                                                    INNER JOIN ibis.tb_parametros AS atenciones ON ibis.tb_pedidocab.nivelAten = atenciones.nidreg
                                                    INNER JOIN ibis.tb_parametros AS estados ON ibis.tb_pedidocab.estadodoc = estados.nidreg
                                                WHERE 
                                                    YEAR(ibis.tb_pedidocab.emision) = :anio
                                                    AND ibis.tb_pedidocab.idcostos LIKE :cc
                                                    AND ibis.tb_pedidocab.nrodoc LIKE :num
                                                ORDER BY  ibis.tb_pedidocab.nrodoc DESC");
                $sql->execute(["anio"=>$anio,
                                "cc"=>$c,
                                "num"=>$n]);

                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()) {
                        $tipo = $rs['idtipomov'] == 37 ? "B":"S";
                        $salida .='<tr class="pointer" data-indice="'.$rs['idreg'].'">
                                        <td class="textoCentro">'.str_pad($rs['nrodoc'],4,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.$rs['idreg'].'</td>
                                        <td class="textoCentro">'.date("d/m/Y", strtotime($rs['emision'])).'</td>
                                        <td class="textoCentro">'.$tipo.'</td>
                                        <td class="pl20px">'.$rs['concepto'].'</td>
                                        <td class="pl20px">'.$rs['costos'].'</td>
                                        <td class="pl20px">'.$rs['nombres'].'</td>
                                        <td class="textoCentro '.$rs['cabrevia'].'">'.$rs['estado'].'</td>
                                        <td class="textoCentro '.strtolower($rs['atencion']).'">'.$rs['atencion'].'</td>
                                    </tr>';
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }
    
        public function consultarReqIdAdmin($id,$min,$max,$proceso){
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
                                                        ibis.tb_pedidocab.concepto, 
                                                        ibis.tb_pedidocab.detalle, 
                                                        ibis.tb_pedidocab.nivelAten, 
                                                        ibis.tb_pedidocab.docfPdfPrev, 
                                                        ibis.tb_pedidocab.docPdfEmit, 
                                                        ibis.tb_pedidocab.docPdfAprob, 
                                                        ibis.tb_pedidocab.verificacion,
                                                        ibis.tb_pedidocab.nmtto, 
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

                $detalles = $this->consultarDetallesAdmin($id);

                return array("cabecera"=>$docData,
                            "detalles"=>$detalles);
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function consultarDetallesAdmin($id){
            try {
                $salida ="";

                $sql=$this->db->connect()->prepare("SELECT
                                                        tb_pedidodet.iditem,
                                                        tb_pedidodet.idpedido,
                                                        tb_pedidodet.cant_atend,
                                                        tb_pedidodet.idprod,
                                                        tb_pedidodet.idtipo,
                                                        tb_pedidodet.estadoItem,
                                                        tb_pedidodet.nroparte,
                                                        tb_pedidodet.unid,
                                                        UPPER(tb_pedidodet.observaciones) AS observaciones,
                                                        REPLACE(FORMAT(tb_pedidodet.cant_pedida,2),',','') AS cant_pedida,
                                                        tb_pedidodet.estadoItem,
                                                        cm_producto.ccodprod,
                                                        UPPER(cm_producto.cdesprod) AS cdesprod,
                                                        tb_unimed.cabrevia,
                                                        tb_pedidodet.nflgqaqc,
                                                        tb_equipmtto.cdescripcion,
                                                        tb_equipmtto.cregistro 
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
                        
                        $salida .='<tr data-grabado="1" data-idprod="'.$rs['idprod'].'" 
                                        data-codund="'.$rs['unid'].'" 
                                        data-idx="'.$rs['iditem'].'"
                                        data-registro="'.$rs['cregistro'].'"
                                        data-estado="'.$rs['estadoItem'].'">
                                        <td class="textoCentro"><a href="'.$rs['iditem'].'" data-accion="eliminar" title="'.$rs['iditem'].'"><i class="fas fa-eraser"></i></a></td>
                                        <td class="textoCentro">'.str_pad($filas++,3,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.$rs['ccodprod'].'</td>
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
                                        <td class="textoCentro">'.$rs['nroparte'].'</td>
                                        <td class="textoCentro">'.$rs['cregistro'].'</td>
                                        <td>
                                            <input type="number" 
                                                        step="any" 
                                                        placeholder="0.00" 
                                                        onchange="(function(el){el.value=parseFloat(el.value).toFixed(2);})(this)"
                                                        onclick="this.select()" 
                                                        value="'.$rs['cant_atend'].'">
                                        </td>
                                        <td class="textoCentro"><a href="'.$rs['iditem'].'" title="Cambiar Item" data-accion="cambiar"><i class="fas fa-exchange-alt"></i></a></td>
                                        <td class="textoCentro"><a href="'.$rs['iditem'].'" title="Liberar Item" data-accion="liberar"><i class="fas fa-wrench"></i></a></td>
                                        <td class="textoCentro"><a href="'.$rs['iditem'].'" title="Agregar Item debajo" data-accion="agregar"><i class="far fa-calendar-plus"></i></a></td>
                                    </tr>';
                    }
                }
                
                return $salida;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function cambiarPedidoAdmin($id,$valor) {
            try {
                $mensaje = "";

                $nroOrden = $this->buscarItemOrden($id);

                if ( $nroOrden ) {
                    $mensaje = "El pedido ya tiene orden";
                }else {

                    $mensaje = $valor == 105 ? "Pedido Anulado":"Error en actualizar";
                    $mensaje = $valor == 49 ? "Pedido Actualizado":"Error en actualizar";

                    $sql = $this->db->connect()->prepare("UPDATE tb_pedidocab 
                                                    SET tb_pedidocab.estadodoc = :valor,
                                                        tb_pedidocab.anula =:user
                                                    WHERE idreg = :id
                                                    LIMIT 1");

                    $sql->execute(["id" => $id,"user"=>$_SESSION['iduser'],"valor"=>$valor]);
                    $rowCount = $sql->rowCount();

                    if ($rowCount > 0) {
                        $this->cambiarDetallesAdmin($id,$valor);
                    }
                };


                return array("mensaje"=>$mensaje,
                            "orden"=>$nroOrden,
                            "ingreso"=>"",
                            "salida"=>"");
                
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function cambiarDetallesAdmin($id,$valor){
            try {
                $sql = $this->db->connect()->prepare("UPDATE tb_pedidodet 
                                                SET tb_pedidodet.estadoItem = :valor
                                                WHERE idpedido = :id");
                $sql->execute(["id" => $id,"valor"=>$valor]);
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function itemActualizarAdmin($parametros){
            try {
                $id = $parametros['id'];
                $valor = $parametros['valor'];
                $estado = $parametros['estado'];
                $mensaje = "Error al actualizar";
                $sw = false;

                if ($estado == 105) {
                    $mensaje = "Item anulado";
                }else if ($estado == 54){
                    $mensaje = "Item cambiado";
                }

                $sql = $this->db->connect()->prepare("UPDATE tb_pedidodet 
                                                        SET tb_pedidodet.estadoItem =:estado,
                                                            tb_pedidodet.nflgActivo =:valor
                                                        WHERE tb_pedidodet.iditem =:id");
                $sql->execute(["id"=>$id,
                                "valor"=>$valor,
                                "estado"=>$estado]);

                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    $this->accionItemOrden($id,$valor,$estado);
                    $sw = true;
                }else {
                    $mensaje = "Error al actualizar";
                    $sw = false;
                }


                return array("mensaje"=>$mensaje,
                            "condicion"=>$sw);
                
            }  catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function buscarItemOrden($id){
            try {
                $sql=$this->db->connect()->prepare("SELECT
                                                COUNT( lg_ordencab.id_refpedi ) AS pedidos 
                                            FROM
                                                lg_ordencab 
                                            WHERE
                                                lg_ordencab.id_refpedi = :id 
                                                AND lg_ordencab.nflgactivo = 1 
                                                LIMIT 1");
                $sql->execute(["id" => $id]);

                $result = $sql->fetchAll();

                return $result[0]['pedidos']; 
            }  catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function buscarItemIngresos(){
            try {
                //code...
            }  catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function buscarItemDespachos(){
            try {
                //code...
            }  catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function grabarPedidoAdmin($cabecera,$detalles){
            $respuesta = false;
            $mensaje = "Pedido no se modifico";
            $clase = "mensaje_error";

            $sql = $this->db->connect()->prepare("UPDATE tb_pedidocab SET vence=:vence,concepto=:concep,detalle=:det,nivelAten=:aten
                                                                                WHERE idreg=:id");
            $sql->execute([
                "vence"=>$cabecera['vence'],
                "concep"=>$cabecera['concepto'],
                "det"=>$cabecera['espec_items'],
                "aten"=>$cabecera['codigo_atencion'],
                "id"=>$cabecera['codigo_pedido']
            ]);

            $rowCount = $sql->rowCount();
            
            $rowDetails = $this->actualizarDetallesAdmin($cabecera['codigo_verificacion'],
                                            $cabecera['codigo_estado'],
                                            $cabecera['codigo_atencion'],
                                            $cabecera['codigo_tipo'],
                                            $cabecera['codigo_costos'],
                                            $cabecera['codigo_area'],
                                            $cabecera['codigo_pedido'],
                                            $detalles);

            if ($rowCount > 0 || $rowDetails > 0){
                $respuesta = true;
                $mensaje = "Pedido Modificado";
                $clase = "mensaje_correcto";
            }

            $salida = array("respuesta"=>$respuesta,
                            "mensaje"=>$mensaje,
                            "clase"=>$clase);

                
            return $salida;
        }

        private function actualizarDetallesAdmin($codigo,$estado,$atencion,$tipo,$costos,$area,$idpedido,$detalles){
            $details = json_decode($detalles);
            $nreg = count($details);
            $rowCount = 0;

            for ($i=0; $i < $nreg; $i++) { 
                if( $details[$i]->itempedido == '-' ){
                    try {
                        $sql = $this->db->connect()->prepare("INSERT INTO tb_pedidodet 
                                                                     SET idpedido=:pedido,
                                                                         idprod=:prod,
                                                                         idtipo=:tipo,
                                                                         unid=:und,
                                                                         cant_pedida=:cant,
                                                                         cant_atend=:atendido,
                                                                         estadoItem=:est,
                                                                         tipoAten=:aten,
                                                                         verificacion=:ver,
                                                                         nflgqaqc=:qaqc,
                                                                         idcostos=:costos,
                                                                         idarea=:area,
                                                                         observaciones=:espec,
                                                                         nregistro=:registro,
                                                                         nroparte=:parte");

                        $sql->execute(["pedido"=>$idpedido,
                            "prod"=>$details[$i]->idprod,
                            "tipo"=>$tipo,
                            "und"=>$details[$i]->unidad,
                            "cant"=>$details[$i]->cantidad,
                            "atendido"=>$details[$i]->atendida,
                            "est"=>$estado,
                            "aten"=>$atencion,
                            "ver"=>$codigo,
                            "qaqc"=>$details[$i]->calidad,
                            "costos"=>$costos,
                            "area"=>$area,
                            "espec"=>$details[$i]->especifica,
                            "registro"=>$details[$i]->registro,
                            "parte"=>$details[$i]->nroparte]);
                        
                        $rowCount = $sql->rowCount();
                    }catch (PDOException $th) {
                        echo $th->getMessage();
                        return false;
                    }
                }else{
                    try {
                        $sql = $this->db->connect()->prepare("UPDATE tb_pedidodet SET 
                                                                        cant_pedida     =:cant,
                                                                        cant_aprob      =:aprob,
                                                                        cant_atend      =:atendido, 
                                                                        nflgqaqc        =:qaqc,
                                                                        tipoAten        =:aten,
                                                                        observaciones   =:espec,
                                                                        nroparte        =:parte,
                                                                        nregistro       =:nreg,
                                                                        idprod          =:prod
                                                                WHERE iditem = :id");
                        
                        $sql->execute(["cant"=>$details[$i]->cantidad,
                                        "atendido"=>$details[$i]->atendida,
                                        "qaqc"=>$details[$i]->calidad,
                                        "aten"=>$atencion,
                                        "espec"=>$details[$i]->especifica,
                                        "parte"=>$details[$i]->nroparte,
                                        "nreg"=>$details[$i]->registro,
                                        "prod"=>$details[$i]->idprod,
                                        "id"=>$details[$i]->itempedido,
                                        "aprob"=>$details[$i]->cantidad]);
                        $rowCount = $sql->rowCount();
                    } catch (PDOException $th) {
                        echo $th->getMessage();
                        return false;
                    }
                }
            }

            return $rowCount;
        }

        private function accionItemOrden($id,$valor,$estado){
            try {
                $sql = $this->db->connect()->prepare("UPDATE lg_ordendet 
                                                        SET lg_ordendet.nEstadoReg =:estado,
                                                            lg_ordendet.nflgActivo =:valor,
                                                            lg_ordendet.nestado =:activo
                                                        WHERE lg_ordendet.niddeta =:id");
                $sql->execute(["id"=>$id,
                                "valor"=>$valor,
                                "estado"=>$estado,
                                "activo"=>$valor]);
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }
    }    
?>