<?php
    class PedidoMttoModel extends Model{

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
                                                    AND ibis.tb_pedidocab.estadodoc BETWEEN 49 AND 50");
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

        public function listarEquipos(){
            try {
                $salida=array();

                $sql = $this->db->connect()->query("SELECT
                                                        tb_equipmtto.idreg,
                                                        UPPER( tb_equipmtto.cregistro) AS registro,
                                                        UPPER(tb_equipmtto.cdescripcion) AS descripcion
                                                    FROM
                                                        tb_equipmtto 
                                                    WHERE
                                                        tb_equipmtto.nflgactivo = 1 
                                                    ORDER BY
                                                        tb_equipmtto.cregistro");
                $sql->execute();
                $rowCount = $sql->rowCount();


                if ($rowCount > 0){
                    while ($rs = $sql->fetch()) {
                        $item['valor']    =$rs['idreg'];
                        $item['registro'] =$rs['registro'];
                        $item['descripcion'] =$rs['descripcion'];

                        array_push($salida,$item);

                    }
                }
                
                return $salida;

            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function insertarMtto($datos,$detalles){
            try {
                $salida = false;
                $respuesta = false;
                $mensaje = "Error en el registro";
                $clase = "mensaje_error";

                $numero = $this->generarNumero($datos['codigo_costos'],"SELECT COUNT(idreg) AS numero FROM tb_pedidocab WHERE tb_pedidocab.idcostos =:cod");
               
                $cmes = date("m",strtotime($datos['emision']));
                $cper = date("Y",strtotime($datos['emision']));

                $sql = $this->db->connect()->prepare("INSERT INTO tb_pedidocab SET idcostos=:cost,idarea=:area,idtrans=:trans,idsolicita=:soli,idtipomov=:mov,
                                                                                emision=:emis,vence=:vence,estadodoc=:estdoc,nrodoc=:nro,usuario=:user,
                                                                                anio=:ano,mes=:mes,concepto=:concep,detalle=:det,nivelAten=:aten,
                                                                                docfPdfPrev=:dprev,nflgactivo=:est,verificacion=:ver,idpartida=:partida,
                                                                                nmtto=:mtto");
                $sql->execute([
                    "cost"=>$datos['codigo_costos'],
                    "area"=>$datos['codigo_area'],
                    "trans"=>$datos['codigo_transporte'],
                    "soli"=>$datos['codigo_solicitante'],
                    "mov"=>$datos['codigo_tipo'],
                    "emis"=>$datos['emision'],
                    "vence"=>$datos['vence'],
                    "estdoc"=>$datos['codigo_estado'],
                    "user"=>$_SESSION['iduser'],
                    "nro"=>$numero['numero'],
                    "ano"=>$cper,
                    "mes"=>$cmes,
                    "concep"=>$datos['concepto'],
                    "det"=>$datos['espec_items'],
                    "aten"=>$datos['codigo_atencion'],
                    "dprev"=>$datos['vista_previa'],
                    "est"=>1,
                    "ver"=>$datos['codigo_verificacion'],
                    "partida"=>$datos['codigo_partida'],
                    "mtto"=>$datos['pedidommto']
                ]);

                $rowCount = $sql->rowCount();
                

                if ($rowCount > 0){
                    $indice = $this->ultimoIndiceTabla("SELECT MAX(idreg) AS indice FROM tb_pedidocab");
                    $this->saveItemsMtto($datos['codigo_verificacion'],
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

                $salida = array("respuesta"=>$respuesta,
                                "mensaje"=>$mensaje,
                                "clase"=>$clase,
                                "indice"=>$indice);

                
                return $salida;
                
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function modificar($datos,$detalles){
            try {
                $salida = false;
                $respuesta = false;
                $mensaje = "Error en el registro";
                $clase = "mensaje_error";
                $rowDetails = 0;

                $sql = $this->db->connect()->prepare("UPDATE tb_pedidocab SET vence=:vence,concepto=:concep,detalle=:det,nivelAten=:aten,
                                                                                docfPdfPrev=:dprev
                                                                                WHERE idreg=:id");
                 $sql->execute([
                    "vence"=>$datos['vence'],
                    "concep"=>$datos['concepto'],
                    "det"=>$datos['espec_items'],
                    "aten"=>$datos['codigo_atencion'],
                    "dprev"=>$datos['vista_previa'],
                    "id"=>$datos['codigo_pedido']
                ]);

                $rowCount = $sql->rowCount();

                $details = json_decode($detalles);
                $nreg = count($details);
                
                for ($i=0; $i < $nreg; $i++) { 
                    //graba el item si no se ha insertado como nuevo
                    if( $details[$i]->itempedido == '-' ){
                        $this->saveItemMtto($datos['codigo_verificacion'],
                                        $datos['codigo_estado'],
                                        $datos['codigo_atencion'],
                                        $datos['codigo_tipo'],
                                        $datos['codigo_costos'],
                                        $datos['codigo_area'],
                                        $details[$i]);
                    }else{
                    //cambia los datos 
                        for ($i=0; $i < count($details); $i++) { 
                            $rowDetails = $this->updateItemsMtto($datos['codigo_atencion'],
                                                             $details[$i]->cantidad,
                                                             $details[$i]->calidad,
                                                             $details[$i]->itempedido,
                                                             $details[$i]->especifica,
                                                             $details[$i]->nroparte,
                                                             $details[$i]->registro,
                                                             $details[$i]->idprod);
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
                                "clase"=>$clase);

                
                return $salida;

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }            
        }

        private function updateItemsMtto($aten,$cant,$qaqc,$idx,$especifica,$parte,$registro,$producto){
            $sql = $this->db->connect()->prepare("UPDATE ibis.tb_pedidodet SET 
                                            cant_pedida = :cant, 
                                            nflgqaqc = :qaqc,
                                            tipoAten = :aten,
                                            observaciones=:espec,
                                            nroparte=:parte,
                                            nregistro=:nreg,
                                            idprod=:prod
                                        WHERE iditem = :id");
            $sql ->execute(["cant"=>$cant,
                            "qaqc"=>$qaqc,
                            "aten"=>$aten,
                            "espec"=>$especifica,
                            "id"=>$idx,
                            "parte"=>$parte,
                            "nreg"=>$registro,
                            "prod"=>$producto]);
            $rowCount = $sql->rowCount();
            return $rowCount;
        }

        //Graba un solo Item de la modificacion
        private function saveItemMtto($codigo,$estado,$atencion,$tipo,$costos,$area,$detalles){
            $indice = $this->obtenerIndice($codigo,"SELECT idreg AS numero FROM tb_pedidocab WHERE tb_pedidocab.verificacion =:id");

           try {
                $sql = $this->db->connect()->prepare("INSERT INTO tb_pedidodet SET idpedido=:ped,idprod=:prod,idtipo=:tipo,unid=:und,
                                                                                   cant_pedida=:cant,estadoItem=:est,tipoAten=:aten,
                                                                                   verificacion=:ver,nflgqaqc=:qaqc,idcostos=:costos,idarea=:area,
                                                                                   observaciones=:espec,nregistro=:registro,nroparte=:parte");
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
                                       "registro"=>$detalles->registro,
                                       "nroparte"=>$datos[$i]->nroparte
                                    ]);
                  
            } catch (PDOException $th) {
                   echo "Error: ".$th->getMessage();
                   return false;
            }
        }

        private function saveItemsMtto($codigo,$estado,$atencion,$tipo,$costos,$area,$detalles,$indice){

            $datos = json_decode($detalles);
            $nreg = count($datos);

            for ($i=0; $i < $nreg; $i++) { 
                $registro = isset($datos[$i]->registro) ? $datos[$i]->registro : NULL; 

                try {
                        $sql = $this->db->connect()->prepare("INSERT INTO tb_pedidodet 
                                                                SET idpedido=:ped,idprod=:prod,idtipo=:tipo,unid=:und,
                                                                    cant_pedida=:cant,estadoItem=:est,tipoAten=:aten,
                                                                    verificacion=:ver,nflgqaqc=:qaqc,idcostos=:costos,idarea=:area,
                                                                    observaciones=:espec,nregistro=:registro,nroparte=:parte");
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
                                "registro"=>$registro,
                                "parte"=>$datos[$i]->nroparte]);    
                } catch (PDOException $th) {
                    echo "Error: ".$th->getMessage();
                    return false;
                }
            }
        }
    }
?>