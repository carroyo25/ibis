<?php
    class SegPedComprasModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarPedidosSeguimientoCompras($datos){
            try {
                $salida = "";
                $pedido = $datos['pedido'] === "" ? "%" : "%".$datos['pedido']."%";
                $costos = $datos['costos'] === "-1" ? "%" : "%".$datos['costos']."%";

                $sql = $this->db->connect()->prepare("SELECT
                                                        LPAD( ibis.tb_pedidocab.nrodoc, 6, 0 ) AS nrodoc,
                                                        UPPER( ibis.tb_pedidocab.concepto ) AS concepto,
                                                        ibis.tb_pedidocab.idreg AS idreg,
                                                        ibis.tb_pedidocab.estadodoc,
                                                        DATE_FORMAT( ibis.tb_pedidocab.emision, '%d/%m/%Y' ) AS emision,
                                                        ibis.tb_pedidocab.vence,
                                                        ibis.tb_pedidocab.idtipomov,
                                                        UPPER(
                                                        CONCAT_WS( ' ', ibis.tb_proyectos.ccodproy, ibis.tb_proyectos.cdesproy )) AS costos,
                                                        ibis.tb_pedidocab.nivelAten,
                                                        estados.cdescripcion AS estado,
                                                        atencion.cdescripcion AS atencion,
                                                        estados.cabrevia,
                                                        ibis.tb_pedidocab.idcostos,
                                                        ibis.tb_proyectos.ccodproy,
                                                        UPPER( ibis.tb_proyectos.cdesproy ) AS cdesproy,
                                                        UPPER( ibis.tb_user.cnameuser ) AS cnameuser,
                                                        ibis.tb_pedidocab.estadoCompra,
                                                        ( SELECT COUNT( tb_pedidodet.estadoItem ) FROM tb_pedidodet WHERE tb_pedidodet.estadoItem != 52 AND tb_pedidodet.idpedido = idreg  AND tb_pedidodet.nflgActivo != 105) AS itemsFaltantes,
                                                    IF
                                                        ( ibis.tb_pedidocab.estadoCompra = 1 OR ISNULL( ibis.tb_pedidocab.estadoCompra ), '--', compras.cdescripcion ) AS textoEstadoCompra,
                                                        tb_pedidocab.comentariocompra,
                                                        UPPER(usuario.cnombres)	AS nombres
                                                    FROM
                                                        ibis.tb_pedidocab
                                                        LEFT JOIN ibis.tb_parametros AS estados ON ibis.tb_pedidocab.estadodoc = estados.nidreg
                                                        LEFT JOIN ibis.tb_parametros AS atencion ON ibis.tb_pedidocab.nivelAten = atencion.nidreg
                                                        LEFT JOIN ibis.tb_proyectos ON ibis.tb_pedidocab.idcostos = ibis.tb_proyectos.nidreg
                                                        LEFT JOIN ibis.tb_user ON ibis.tb_pedidocab.asigna = ibis.tb_user.iduser
                                                        LEFT JOIN ibis.tb_parametros AS compras ON ibis.tb_pedidocab.estadoCompra = compras.nidreg
                                                        LEFT JOIN ibis.tb_user AS usuario ON ibis.tb_pedidocab.usuario = usuario.iduser 
                                                    WHERE
                                                        (ibis.tb_pedidocab.estadodoc >= 54
                                                        AND ibis.tb_pedidocab.estadodoc != 105)
                                                        AND ibis.tb_pedidocab.nflgactivo = 1
                                                        AND ibis.tb_pedidocab.anio = :anio
                                                        AND ibis.tb_pedidocab.nrodoc LIKE :pedido
                                                        AND ibis.tb_pedidocab.idcostos LIKE :costo
                                                    ORDER BY
                                                        ibis.tb_pedidocab.emision DESC");
                $sql->execute(["anio"=>$datos['anio'],"pedido"=>$pedido, "costo"=>$costos, ]);

                $rowCount = $sql->rowCount();
                
                while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                    $docData[] = $row;
                }

                return array("datos"=>$docData);
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function asignarOperador($pedido,$detalles,$asignado){
            try {
                $sql = $this->db->connect()->prepare("UPDATE tb_pedidocab 
                                                        SET tb_pedidocab.asigna=:asignado
                                                        WHERE tb_pedidocab.idreg =:pedido");
                $sql->execute(["asignado"=>$asignado,
                                "pedido"=>$pedido]);

                $rowCount = $sql->rowCount();
                if ($rowCount > 0){
                    $this->asignarDetalles($detalles,$asignado);
                }
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function asignarDetalles($detalles,$asignado){
            $datos = json_decode($detalles);
            $nreg = count($datos);

            for ($i=0; $i < $nreg; $i++) {
                try {
                    $sql = $this->db->connect()->prepare("UPDATE tb_pedidodet 
                                                        SET tb_pedidodet.idasigna=:asignado
                                                        WHERE tb_pedidodet.iditem =:item
                                                            AND (tb_pedidodet.estadoItem = 54 OR tb_pedidodet.estadoItem = 230)");
                $sql->execute(["asignado"=>$asignado,
                                "item"=>$datos[$i]->itempedido]);
                } catch (PDOException $th) {
                    echo $th->getMessage();
                    return false;
                }
            }
        }

        public function filtroAsigna($parametros){
            try {
                $salida = "";
                $mes  = date("m");

                $tipo   = $parametros['tipoSearch'] == -1 ? "%" : $parametros['tipoSearch'];
                $costos = $parametros['costosSearch'] == -1 ? "%" : $parametros['costosSearch'];
                $mes    = $parametros['mesSearch'] == -1 ? "%" :  $parametros['mesSearch'];
                $anio   = $parametros['anioSearch'];
                

                $sql = $this->db->connect()->prepare("SELECT
                                                        ibis.tb_pedidocab.nrodoc,
                                                        UPPER( ibis.tb_pedidocab.concepto ) AS concepto,
                                                        ibis.tb_pedidocab.idreg,
                                                        ibis.tb_pedidocab.estadodoc,
                                                        ibis.tb_pedidocab.emision,
                                                        ibis.tb_pedidocab.vence,
                                                        ibis.tb_pedidocab.idtipomov,
                                                        UPPER(
                                                        CONCAT_WS( ' ', ibis.tb_proyectos.ccodproy, ibis.tb_proyectos.cdesproy )) AS costos,
                                                        ibis.tb_pedidocab.nivelAten,
                                                        CONCAT_WS( ' ', rrhh.tabla_aquarius.apellidos, rrhh.tabla_aquarius.nombres ) AS nombres,
                                                        estados.cdescripcion AS estado,
                                                        atencion.cdescripcion AS atencion,
                                                        estados.cabrevia,
                                                        ibis.tb_pedidocab.idcostos,
                                                        ibis.tb_proyectos.ccodproy,
                                                        ibis.tb_proyectos.cdesproy,
                                                        UPPER(ibis.tb_user.cnameuser) as cnameuser  
                                                    FROM
                                                        ibis.tb_pedidocab
                                                        INNER JOIN rrhh.tabla_aquarius ON ibis.tb_pedidocab.idsolicita = rrhh.tabla_aquarius.internal
                                                        INNER JOIN ibis.tb_parametros AS estados ON ibis.tb_pedidocab.estadodoc = estados.nidreg
                                                        INNER JOIN ibis.tb_parametros AS atencion ON ibis.tb_pedidocab.nivelAten = atencion.nidreg
                                                        INNER JOIN ibis.tb_proyectos ON ibis.tb_pedidocab.idcostos = ibis.tb_proyectos.nidreg
                                                        LEFT JOIN ibis.tb_user ON ibis.tb_pedidocab.asigna = ibis.tb_user.iduser 
                                                    WHERE
                                                        ibis.tb_pedidocab.estadodoc = 54
                                                        AND ibis.tb_pedidocab.nflgactivo = 1
                                                        AND ibis.tb_pedidocab.idtipomov LIKE :tipomov 
                                                        AND ibis.tb_pedidocab.idcostos LIKE :costos 
                                                        AND MONTH ( ibis.tb_pedidocab.emision ) LIKE :mes 
                                                        AND YEAR ( ibis.tb_pedidocab.emision ) LIKE :anio
                                                    ORDER BY ibis.tb_pedidocab.emision DESC");
                $sql->execute(["tipomov"=>$tipo,
                                "costos"=>$costos,
                                "mes"=>$mes,
                                "anio"=>$anio]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()) {
                        $tipo = $rs['idtipomov'] == 37 ? "B":"S";
                        $asignado = $rs['cnameuser'] == NULL ? "--" : $rs['cnameuser'];
                        $salida .='<tr class="pointer" data-indice="'.$rs['idreg'].'">
                                        <td class="textoCentro">'.str_pad($rs['nrodoc'],4,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.date("d/m/Y", strtotime($rs['emision'])).'</td>
                                        <td class="textoCentro">'.$tipo.'</td>
                                        <td class="pl20px">'.$rs['concepto'].'</td>
                                        <td class="pl20px">'.utf8_decode($rs['costos']).'</td>
                                        <td class="pl20px">'.$rs['nombres'].'</td>
                                        <td class="textoCentro '.$rs['cabrevia'].'">'.$rs['estado'].'</td>
                                        <td class="textoCentro '.strtolower($rs['atencion']).'">'.$rs['atencion'].'</td>
                                        <td class="textoCentro">'.$asignado.'</td>
                                    </tr>';
                    }
                }else {
                    $salida = '<tr class="pointer"><td colspan="9" class="textoCentro" data-costos="'.$costos.'">No se encontraron registros en la consulta</td></tr>';
                }

                return $salida;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function consultarReqId($id,$min,$max,$proceso){
            try {
                $docData = [];

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
                                                        UPPER(ibis.tb_pedidocab.concepto)  AS concepto, 
                                                        ibis.tb_pedidocab.detalle, 
                                                        ibis.tb_pedidocab.nivelAten, 
                                                        ibis.tb_pedidocab.docfPdfPrev, 
                                                        ibis.tb_pedidocab.docPdfEmit, 
                                                        ibis.tb_pedidocab.docPdfAprob, 
                                                        ibis.tb_pedidocab.verificacion,
                                                        ibis.tb_pedidocab.nmtto,
                                                        ibis.tb_pedidocab.asigna,
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
                                                        INNER JOIN
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
                                                        tb_pedidocab.idreg = :id 
                                                    AND tb_pedidocab.estadodoc BETWEEN :min 
                                                    AND :max");
                $sql->execute(['id'=>$id, 'min'=>$min, 'max'=>$max]);
                $rowCount = $sql->rowCount();
                
                if ($rowCount > 0) {
                    $docData = array();
                    while($row=$sql->fetch(PDO::FETCH_ASSOC)){
                        $docData[] = $row;
                    }
                }

                $detalles = $this->consultarDetallesSeguimiento($id);

                return array("cabecera"=>$docData,
                            "detalles"=>$detalles,
                            "total_adjuntos"=>$this->contarAdjuntos($id,"PED"));
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function consultarDetallesSeguimiento($id){
            try {
                $salida ="";

                $sql=$this->db->connect()->prepare("SELECT
                                                    tb_pedidodet.iditem,
                                                    tb_pedidodet.idpedido,
                                                    tb_pedidodet.idprod,
                                                    tb_pedidodet.idtipo,
                                                    tb_pedidodet.nroparte,
                                                    tb_pedidodet.unid,
                                                    UPPER(tb_pedidodet.obsAprueba) AS observaAprueba,
                                                    REPLACE ( FORMAT( tb_pedidodet.cant_pedida, 2 ), ',', '' ) AS cant_pedida,
                                                    REPLACE ( FORMAT( tb_pedidodet.cant_resto, 2 ), ',', '' ) AS cant_pendiente,
                                                    REPLACE ( FORMAT( tb_pedidodet.cant_atend, 2 ), ',', '' ) AS cant_atendida,
                                                    REPLACE ( FORMAT( tb_pedidodet.cant_aprob, 2 ), ',', '' ) AS cant_aprob,
                                                    tb_pedidodet.estadoItem,
                                                    cm_producto.ccodprod,
                                                    UPPER(CONCAT_WS( ' ', cm_producto.cdesprod, tb_pedidodet.observaciones )) AS cdesprod,
                                                    tb_unimed.cabrevia,
                                                    tb_pedidodet.nflgqaqc,
                                                    tb_pedidodet.especificaciones,
                                                    CONCAT_WS('/', tb_equipmtto.cregistro, tb_equipmtto.cdescripcion ) AS registro 
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
                    while ($rs = $sql->fetch()) {

                        $atendida = $rs['cant_atendida'] == NULL || $rs['cant_atendida'] == '' ? 0 : $rs['cant_atendida'];
                        //$aprobar =  $rs['cant_pedida'] - $rs['cant_atendida'];
                        $aprobar = $rs['cant_aprob'];

                        $estado_aprobar = $aprobar == 0 ? "desactivado" : "";
                        
                        $salida .='<tr data-grabado="1" data-idprod="'.$rs['idprod'].'" data-codund="'.$rs['unid'].'" data-idx="'.$rs['iditem'].'" class="'.$estado_aprobar.'">
                                        <td class="textoCentro">'.str_pad($filas++,3,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                        <td class="pl20px">'.$rs['cdesprod'].'</td>
                                        <td class="textoCentro">'.$rs['cabrevia'].'</td>
                                        <td class="textoCentro">'.$rs['cant_pedida'].'</td>
                                        <td class="textoCentro">'.$rs['cant_atendida'].'</td>
                                        <td>
                                            <input type="text" 
                                                        step="any" 
                                                        placeholder="0.00" 
                                                        onchange="(function(el){el.value=parseFloat(el.value).toFixed(2);})(this)"
                                                        onclick="this.select()" 
                                                        value="'.number_format($aprobar,2).'"
                                                        class="textoDerecha">
                                        </td>
                                        <td class="textoCentro">'.$rs['nroparte'].'</td>
                                        <td class="pl20px">'.$rs['observaAprueba'].'</td>
                                        <td class="textoCentro">'.$rs['registro'].'</td>
                                    </tr>';
                    }
                }
                
                return $salida;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public  function actualizarEstado($datos){
            try {
                $mensaje = "No se registro el comentario";
                $respuestaOK  = false;

                $sql = $this->db->connect()->prepare("UPDATE tb_pedidocab
                                                    SET tb_pedidocab.compras = :user,
                                                        tb_pedidocab.estadoCompra = :estado,
                                                        tb_pedidocab.comentariocompra = :comentario
                                                    WHERE tb_pedidocab.idreg = :pedido");
                
                $sql->execute(["user"=>$datos['user'],
                                "estado"=>$datos['estado'],
                                "comentario"=>$datos['comentario'],
                                "pedido"=>$datos['id']]);

                if ($sql->rowCount() > 0){
                    $mensaje = "Comentario actualizado...";
                    $respuestaOK  = true;
                }

                return array("id"=>$datos['id'],"estado"=>$datos['estado'],"respuesta"=>$respuestaOK,"mensaje"=>$mensaje);
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }
    }
?>