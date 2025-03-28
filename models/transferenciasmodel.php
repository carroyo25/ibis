<?php
    use \setasign\Fpdi\Fpdi;

    class TransferenciasModel extends Model{
        
        public function __construct()
        {
            parent::__construct();
        }

        public function listarPedidosAtendidos(){
            try {
                $salida = "";
                $sql = $this->db->connect()->prepare("SELECT
                                                            tb_costusu.id_cuser,
                                                            alm_transfercab.almorigen,
                                                            alm_transfercab.almdestino,
                                                            alm_transfercab.idreg,
                                                            alm_transfercab.ftraslado,
                                                            alm_transfercab.cnumguia,
                                                            UPPER( c_origen.cdesproy ) AS origen_cc,
                                                            UPPER( c_destino.cdesproy ) AS destino_cc,
                                                            tb_pedidocab.nrodoc 
                                                        FROM
                                                            tb_costusu
                                                            INNER JOIN alm_transfercab ON tb_costusu.ncodproy = alm_transfercab.idcc
                                                            INNER JOIN tb_almacen AS origen ON alm_transfercab.almorigen = origen.ncodalm
                                                            INNER JOIN tb_almacen AS destino ON alm_transfercab.almdestino = destino.ncodalm
                                                            INNER JOIN tb_proyectos AS c_origen ON alm_transfercab.idcc = c_origen.nidreg
                                                            INNER JOIN tb_proyectos AS c_destino ON alm_transfercab.idcd = c_destino.nidreg
                                                            LEFT JOIN alm_transferdet ON alm_transfercab.idreg = alm_transferdet.idtransfer
                                                            INNER JOIN tb_pedidodet ON alm_transferdet.iddetped = tb_pedidodet.iditem
                                                            INNER JOIN tb_pedidocab ON tb_pedidodet.idpedido = tb_pedidocab.idreg 
                                                        WHERE
                                                            tb_costusu.nflgactivo = 1 
                                                            AND alm_transfercab.nflgactivo = 1 
                                                            AND tb_costusu.id_cuser = :user
                                                        AND tb_pedidocab.nrodoc LIKE '%'
                                                        GROUP BY
                                                            idreg
                                                        ORDER BY
                                                            alm_transfercab.idreg DESC ");
                $sql->execute(["user"=>$_SESSION['iduser']]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()) {
                        $salida .='<tr class="pointer" data-indice="'.$rs['idreg'].'" data-guia="'.$rs['cnumguia'].'">
                                        <td class="textoCentro">'.str_pad($rs['idreg'],4,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.date("d/m/Y", strtotime($rs['ftraslado'])).'</td>
                                        <td class="pl20px">'.$rs['origen_cc'].'</td>
                                        <td class="pl20px">'.$rs['destino_cc'].'</td>
                                        <td class="pl20px">'.$rs['cnumguia'].'</td>
                                        <td class="pl20px">'.str_pad($rs['nrodoc'],6,0,STR_PAD_LEFT).'</td>
                                    </tr>';
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function consultarTransferencia($id,$guia) {
            try {
                $cabecera = "";
                $result = [];

                /**/

                $sql = $this->db->connect()->prepare("SELECT
                                                        alm_transfercab.idreg,
                                                        alm_transfercab.idcc,
                                                        alm_transfercab.idaprueba,
                                                        alm_transfercab.almorigen,
                                                        alm_transfercab.almdestino,
                                                        alm_transfercab.ftraslado,
                                                        alm_transfercab.cnumguia,
                                                         alm_transfercab.movalma,
                                                        tb_user.cnombres,
                                                        UPPER( almacenOrigen.cdesalm ) AS origen,
                                                        UPPER( almacenDestino.cdesalm ) AS destino,
                                                        tipos.cdescripcion AS tipo,
                                                        alm_transfercab.ntipmov,
                                                        UPPER( costos_origen.cdesproy ) AS costo_origen,
                                                        costos_origen.nidreg AS codigo_origen,
                                                        UPPER( costos_destino.cdesproy ) AS costo_destino,
                                                        costos_destino.nidreg AS codigo_destino,
                                                        alm_transfercab.cusuario,
                                                        movimientos.cdescripcion AS movimiento,
                                                        movimientos.cdescripcion AS ccod  
                                                    FROM
                                                        alm_transfercab
                                                        INNER JOIN tb_user ON alm_transfercab.idaprueba = tb_user.iduser COLLATE utf8_unicode_ci
                                                        INNER JOIN tb_almacen AS almacenOrigen ON alm_transfercab.almorigen = almacenOrigen.ncodalm
                                                        INNER JOIN tb_almacen AS almacenDestino ON alm_transfercab.almdestino = almacenDestino.ncodalm
                                                        INNER JOIN tb_proyectos AS costos_origen ON alm_transfercab.idcc = costos_origen.nidreg
                                                        INNER JOIN tb_parametros AS tipos ON alm_transfercab.ntipmov = tipos.nidreg
                                                        LEFT JOIN tb_proyectos AS costos_destino ON alm_transfercab.idcd = costos_destino.nidreg
                                                        LEFT JOIN tb_parametros AS movimientos ON alm_transfercab.movalma = movimientos.nidreg 
                                                    WHERE
                                                        alm_transfercab.idreg = :id 
                                                        AND alm_transfercab.nflgactivo = 1");
                
                $sql->execute(["id"=>$id]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    $docData = array();
                    while($row=$sql->fetch(PDO::FETCH_ASSOC)){
                        $docData[] = $row;
                    }
                }

                return array("cabecera"=>$docData,
                            "detalles"=>$this->detalles($id),
                            "guias"=>$this->consultarDatosGuiaTrans($id,$guia));


            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function detalles($id){
            try {
                $salida = "";

                $sql = $this->db->connect()->prepare("SELECT
                                                    alm_transferdet.iditem,
                                                    alm_transferdet.idtransfer,
                                                    alm_transferdet.iddetped,
                                                    alm_transferdet.idcprod,
                                                    alm_transferdet.ncanti,
                                                    alm_transferdet.cobserva,
                                                    alm_transferdet.idPedido,
                                                    cm_producto.ccodprod,
                                                    UPPER(CONCAT_WS(' ',cm_producto.cdesprod,tb_pedidodet.observaciones)) AS producto,
                                                    tb_pedidodet.cant_aprob,
                                                    tb_pedidodet.cant_orden,
                                                    FORMAT(tb_pedidodet.cant_atend,2) AS cant_atend,
                                                    alm_transferdet.nflgactivo,
                                                    tb_unimed.cabrevia,
                                                    LPAD(tb_pedidocab.nrodoc,3,0) AS pedido  
                                                FROM
                                                    alm_transferdet
                                                    INNER JOIN cm_producto ON alm_transferdet.idcprod = cm_producto.id_cprod
                                                    INNER JOIN tb_pedidodet ON alm_transferdet.iddetped = tb_pedidodet.iditem
                                                    INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed
                                                    INNER JOIN tb_pedidocab ON tb_pedidodet.idpedido = tb_pedidocab.idreg  
                                                WHERE
                                                    alm_transferdet.idtransfer = :id 
                                                    AND alm_transferdet.nflgactivo = 1
                                                    AND alm_transferdet.ncanti > 0");
                $sql->execute(["id"=>$id]);

                $rowCount = $sql->rowCount();
                $item = 1;

                if ($rowCount > 0) {
                    while ( $rs = $sql->fetch()){
                        $salida .= '<tr class="pointer"
                                            data-grabado="1"
                                            data-itempedido="'.$rs['iddetped'].'"
                                            data-idprod="'.$rs['idcprod'].'" 
                                            data-codund="" 
                                            data-idx="'.$rs['iditem'].'">
                                        <td class="textoCentro">'.str_pad($item++,3,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                        <td class="pl20px">'.$rs['producto'].'</td>
                                        <td class="textoCentro">'.$rs['cabrevia'].'</td>
                                        <td class="textoDerecha">'.$rs['cant_atend'].'</td>
                                        <td class="textoDerecha">'.$rs['cant_orden'].'</td>
                                        <td><input type="text" class="textoDerecha" value="'.$rs['ncanti'].'" readonly></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td><textarea readonly></textarea>'.$rs['cobserva'].'</textarea></td>
                                        <td class="textoCentro">'.$rs['pedido'].'</td>
                                    </tr>';
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function consultarDatosGuiaTrans($transferencia,$guia){
            try {
                $guiaData="";
                $sql=$this->db->connect()->prepare("SELECT
                                                        lg_guias.idreg,
                                                        lg_guias.id_regalm,
                                                        lg_guias.cnumguia,
                                                        lg_guias.corigen,
                                                        lg_guias.cdirorigen,
                                                        lg_guias.cdestino,
                                                        lg_guias.cdirdest,
                                                        lg_guias.centi,
                                                        lg_guias.centidir,
                                                        lg_guias.centiruc,
                                                        lg_guias.ctraslado,
                                                        lg_guias.cenvio,
                                                        lg_guias.cautoriza,
                                                        lg_guias.cmarca,
                                                        lg_guias.cplaca,
                                                        lg_guias.cnombre,
                                                        lg_guias.clicencia,
                                                        lg_guias.ftraslado,
                                                        lg_guias.fguia,
                                                        lg_guias.cobserva,
                                                        lg_guias.cdestinatario,
                                                        lg_guias.cmotivo 
                                                FROM
                                                    lg_guias 
                                                WHERE
                                                    lg_guias.id_regalm =:transferencia
                                                AND lg_guias.cnumguia =:guia");
                $sql->execute(["transferencia"=>$transferencia,"guia"=>$guia]);

                $rowCount = $sql->rowCount();
                
                if ($rowCount > 0) {
                    $guiaData = array();
                    while($row=$sql->fetch(PDO::FETCH_ASSOC)){
                        $guiaData[] = $row;
                    }
                }

                return $guiaData;

            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }

        public function consultarStocks($cc,$cod,$desc){
            try {
                $codigo      = $cod == "" ? '%': '%'.$cod.'%';
                $descripcion = $desc == "" ? '%': '%'.$desc.'%' ;

                $salida = '';

                $sql = $this->db->connect()->prepare("SELECT
                                                        cm_producto.id_cprod,
                                                        cm_producto.ccodprod,
                                                        cm_producto.ntipo,
                                                        UPPER( cm_producto.cdesprod ) AS descripcion,
                                                        SUM( alm_inventariodet.cant_ingr ) AS ingreso_inventario,
                                                        SUM( alm_existencia.cant_ingr ) AS ingreso_guias,
                                                        alm_inventariocab.idcostos AS cc_inventario,
                                                        alm_cabexist.idcostos AS cc_guias,
                                                        tb_unimed.cabrevia,
                                                        tb_unimed.ncodmed,
                                                    IF
                                                        ( ISNULL( alm_cabexist.idcostos ), alm_inventariocab.idcostos, alm_cabexist.idcostos ) AS costos 
                                                    FROM
                                                        cm_producto
                                                        LEFT JOIN alm_inventariodet ON cm_producto.id_cprod = alm_inventariodet.codprod
                                                        LEFT JOIN alm_existencia ON cm_producto.id_cprod = alm_existencia.codprod
                                                        LEFT JOIN alm_inventariocab ON alm_inventariodet.idregistro = alm_inventariocab.idreg
                                                        LEFT JOIN alm_cabexist ON alm_existencia.idregistro = alm_cabexist.idreg
                                                        INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed 
                                                    WHERE
                                                        cm_producto.ntipo = 37 
                                                        AND ( alm_inventariocab.idcostos > 0 OR alm_existencia.cant_ingr > 0 )
                                                        AND cm_producto.ccodprod LIKE :codigo
                                                        AND cm_producto.cdesprod LIKE :descripcion
                                                    GROUP BY
                                                        cm_producto.id_cprod
                                                    ORDER BY cm_producto.cdesprod ASC");
                $sql->execute(["codigo"=>$codigo,"descripcion"=>$descripcion]);
                $rowCount = $sql->rowCount();
                $item = 1;
                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()){
                        $saldo = $rs['ingreso_guias']+$rs['ingreso_inventario'];
                        $estado = $saldo > 0 ? "semaforoVerde":"semaforoRojo";

                        if ( $rs['costos'] == $cc ){
                            $salida.='<tr class="pointer" data-idprod="'.$rs['id_cprod'].'" 
                                                          data-costos="'.$rs['costos'].'"
                                                          data-ncomed="'.$rs['ncodmed'].'">
                                            <td class="textoCentro">'.str_pad($item++,4,0,STR_PAD_LEFT).'</td>
                                            <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                            <td class="pl20px">'.$rs['descripcion'].'</td>
                                            <td class="textoCentro">'.$rs['cabrevia'].'</td>
                                            <td class="textoDerecha '.$estado.'"><div>'.number_format($saldo,2).'</div></td>
                                    </tr>';
                        }
                    }
                }else {
                    $salida = '<tr colspan="8">No hay registros</tr>';
                }

                return $salida;

            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function listarPedidosAtencion($cc,$pedido){
            try {
                $p = $pedido == "" ? "%" : $pedido;

                $salida = "";
                
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
                                                        COUNT(ibis.tb_pedidodet.cant_atend) AS atendido 
                                                    FROM
                                                        ibis.tb_pedidocab
                                                        LEFT JOIN rrhh.tabla_aquarius ON ibis.tb_pedidocab.idsolicita = rrhh.tabla_aquarius.internal
                                                        LEFT JOIN ibis.tb_parametros AS estados ON ibis.tb_pedidocab.estadodoc = estados.nidreg
                                                        LEFT JOIN ibis.tb_parametros AS atencion ON ibis.tb_pedidocab.nivelAten = atencion.nidreg
                                                        LEFT JOIN ibis.tb_proyectos ON ibis.tb_pedidocab.idcostos = ibis.tb_proyectos.nidreg
                                                        LEFT JOIN ibis.tb_pedidodet ON ibis.tb_pedidocab.idreg = ibis.tb_pedidodet.idpedido 
                                                    WHERE
                                                        ( ibis.tb_pedidocab.estadodoc = 54 
                                                            OR ibis.tb_pedidocab.estadodoc = 59 
                                                            OR ibis.tb_pedidocab.estadodoc = 60
                                                            OR ibis.tb_pedidocab.estadodoc = 62
                                                            OR ibis.tb_pedidocab.estadodoc = 58
                                                            OR ibis.tb_pedidocab.estadodoc = 52
                                                            OR ibis.tb_pedidocab.estadodoc = 84
                                                            OR ibis.tb_pedidocab.estadodoc = 230) 
                                                        AND ibis.tb_pedidocab.nflgactivo = 1 
                                                        AND ibis.tb_pedidocab.idtipomov = 37 
                                                        AND tb_pedidocab.nrodoc LIKE :pedido
                                                        AND tb_pedidocab.idcostos = :cc
                                                    GROUP BY ibis.tb_pedidocab.idreg
                                                    ORDER BY
                                                        ibis.tb_pedidocab.emision DESC");
                $sql->execute(["pedido"=>$p,"cc"=>$cc]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()) {
                        $tipo = $rs['idtipomov'] == 37 ? "B":"S";
                        $salida .='<tr class="pointer" 
                                        data-indice="'.$rs['idreg'].'" 
                                        data-pedido="'.$rs['nrodoc'].'">
                                        <td class="textoCentro">'.str_pad($rs['nrodoc'],4,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.date("d/m/Y", strtotime($rs['emision'])).'</td>
                                        <td class="pl20px">'.$rs['concepto'].'</td>
                                        <td class="pl20px">'.utf8_decode($rs['costos']).'</td>
                                        <td class="pl20px">'.$rs['nombres'].'</td>
                                    </tr>';
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function consultarPedidos($indice,$origen){
            try {
                $salida = "";
                $item = 1;

                $sql = $this->db->connect()->prepare("SELECT
                                                        tb_pedidodet.iditem,
                                                        tb_pedidodet.idpedido,
                                                        tb_pedidodet.idprod,
                                                        tb_pedidodet.cant_pedida,
                                                        tb_pedidodet.cant_orden,
                                                        tb_pedidodet.cant_aprob,
                                                        IF(ISNULL(tb_pedidodet.cant_atend),0,tb_pedidodet.cant_atend) AS cant_atend,
                                                        cm_producto.ccodprod,
                                                        UPPER(CONCAT_WS(' ',cm_producto.cdesprod,tb_pedidodet.observaciones)) AS cdesprod,
                                                        tb_unimed.cabrevia,
                                                        tb_pedidocab.idreg,
                                                        tb_pedidocab.idcostos,
                                                        LPAD( tb_pedidocab.nrodoc, 6, 0 ) AS nrodoc,
                                                        IF(ISNULL( SUM( alm_transferdet.ncanti ) ),0,SUM( alm_transferdet.ncanti )) AS total_atendido,
                                                        tb_pedidodet.estadoItem 
                                                    FROM
                                                        tb_pedidodet
                                                        INNER JOIN cm_producto ON tb_pedidodet.idprod = cm_producto.id_cprod
                                                        INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed
                                                        INNER JOIN tb_pedidocab ON tb_pedidodet.idpedido = tb_pedidocab.idreg
                                                        LEFT JOIN alm_transferdet ON tb_pedidodet.iditem = alm_transferdet.iddetped 
                                                    WHERE
                                                        tb_pedidodet.idpedido = :indice 
                                                        AND tb_pedidodet.nflgActivo = 1 
                                                        AND tb_pedidodet.cant_atend > 0
                                                        AND ( tb_pedidodet.estadoItem = 54 
                                                                OR tb_pedidodet.estadoItem = 230 
                                                                OR tb_pedidodet.estadoItem = 52 
                                                                OR tb_pedidodet.estadoItem = 84) 
                                                    GROUP BY
                                                        tb_pedidodet.iditem");
                $sql -> execute(['indice'=>$indice]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while($rs = $sql->fetch()) {
                        
                        $existencia = 0;
                        $enviar = $rs['cant_aprob'] - $rs['cant_orden'];
                        $pedido = $rs['idpedido'];

                        $salida .= '<tr data-iditem="'.$rs['iditem'].'" 
                                        data-aprobado="'.$rs['cant_aprob'].'" 
                                        data-pedido="'.$rs['idreg'].'"
                                        data-idprod="'.$rs['idprod'].'"
                                        data-costos="'.$rs['idcostos'].'"
                                        data-orden="'.$rs['cant_orden'].'"
                                        data-almacen="'.$rs['cant_atend'].'"
                                        data-grabado="0"
                                        data-separado="0">
                                        <td class="textoCentro">'.str_pad($item++,3,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                        <td class="pl20px">'.$rs['cdesprod'].'</td>
                                        <td class="textoCentro">'.$rs['cabrevia'].'</td>
                                        <td class="textoDerecha">'.$rs['cant_atend'].'</td>
                                        <td class="textoDerecha">'.$rs['total_atendido'].'</td>
                                        <td><input type="number" step="any" placeholder="0.00" 
                                                            onchange="(function(el){el.value=parseFloat(el.value).toFixed(2);})(this)" 
                                                            onclick="this.select()">
                                        </td>
                                        <td><input type="date"></td>
                                        <td><input type="text"></td>
                                        <td class="textoDerecha">'.number_format($existencia,2).'</td>
                                        <td><input type="text"></td>
                                        <td  class="textoCentro">'.$rs['nrodoc'].'</td>
                                    </tr>';
                    }
                }

                $respuesta = $salida == "" ? false:true;

                return array("items"=>$salida,
                            "total_items"=>$this->cantidadItemsAtencionPedido($indice),
                            "respuesta"=>$respuesta);


            }catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function cantidadItemsAtencionPedido($pedido){
            try {
                $sql = $this->db->connect()->prepare("SELECT SUM(tb_pedidodet.cant_aprob) AS total_items
                                                        FROM tb_pedidodet 
                                                        WHERE tb_pedidodet.nflgactivo = 1
                                                        AND tb_pedidodet.idpedido =:pedido");
                                                        
                $sql->execute(["pedido"=>$pedido]);

                $result = $sql->fetchAll();

                return $result[0]['total_items'];
                                                            
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function insertarTransferencia($cabecera,$detalles,$pedido,$atendidos){
            try {
                $mensaje = "Error al grabar el registro";
                $sw = false;

                $sql = $this->db->connect()->prepare("INSERT INTO alm_transfercab 
                                                        SET idcc=:corigen,idaprueba=:aprueba,almorigen=:origen,almdestino=:destino,
                                                            ftraslado=:fecha_traslado,ntipmov=:tipo_movimiento,nestado=:estado,
                                                            idcd=:cdestino,movalma=:motivo_transferencia,cusuario=:usuario_genera");
                
                $sql->execute([
                    "corigen"=>$cabecera['codigo_costos_origen'],
                    "cdestino"=>$cabecera['codigo_costos_destino'],
                    "aprueba"=>$cabecera['codigo_aprueba'],
                    "origen"=>$cabecera['codigo_almacen_origen'],
                    "destino"=>$cabecera['codigo_almacen_destino'],
                    "fecha_traslado"=>$cabecera['fecha'],
                    "tipo_movimiento"=>$cabecera['codigo_movimiento'],
                    "usuario_genera"=>$cabecera['usuario_genera'],
                    "motivo_transferencia"=>$cabecera['motivo_transferencia'],
                    "estado"=>1,
                ]);

                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    $mensaje = "Registro insertado";
                    $sw = true;

                    $indice = $this->lastInsertId("SELECT MAX(idreg) AS id FROM alm_transfercab");
                    
                    $this->insertarDetalles($indice,$detalles);

                    $estado = $atendidos == $cabecera['total_items'] ? 52:54;

                    $this->actualizarCabeceraPedido($pedido,$estado);
                }

                return array("mensaje"=>$mensaje,
                             "estado"=>$sw,
                             "documento"=>str_pad($indice,4,0,STR_PAD_LEFT),
                             "indice"=>$indice);
                
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function insertarDetalles($indice,$detalles){
            $datos = json_decode($detalles);
            $nreg = count($datos);

            for ($i=0; $i < $nreg; $i++) { 
               try {

                    $sql = $this->db->connect()->prepare("INSERT INTO alm_transferdet 
                                                                SET idtransfer=:transferencia,iddetped=:iditem,
                                                                    idcprod=:producto,ncanti=:cantidad,nflgactivo=:activo,
                                                                    nEstadoReg=:estado,cobserva=:observa,
                                                                    idPedido=:pedido,idcostos=:costos,
                                                                    fvence=:vence,ccondicion=:condicion");
                    
                    $sql->execute(["transferencia"=>$indice,
                        "iditem"    =>$datos[$i]->iditem,
                        "producto"  =>$datos[$i]->idprod,
                        "cantidad"  =>$datos[$i]->cantidad,
                        "activo"    =>1,
                        "estado"    =>52,
                        "observa"   =>$datos[$i]->obser,
                        "pedido"    =>$datos[$i]->pedido,
                        "costos"    =>$datos[$i]->costos,
                        "vence"     =>$datos[$i]->vence,
                        "condicion" =>$datos[$i]->condicion]);

                    if ( $datos[$i]->cantidad != 0 ){
                        $this->actualizarDetallesPedido($datos[$i]->iditem,$datos[$i]->cantidad,$datos[$i]->aprobado,$datos[$i]->atendido);
                    }
                } catch (PDOException $th) {
                    echo $th->getMessage();
                    return false;
                }
            }
        }

        private function actualizarDetallesPedido($item,$cantidad,$aprobado,$atendido){
            try {
                $total_atendido_almacenes = $cantidad + $atendido;
                $estado = 52;

                $sql = $this->db->connect()->prepare("UPDATE tb_pedidodet 
                                                        SET tb_pedidodet.estadoItem = :estado,
                                                            tb_pedidodet.cant_atend = :cantidad
                                                        WHERE tb_pedidodet.iditem =:item");
                $sql->execute(["item"=>$item,
                                "cantidad"=>$cantidad,
                                "estado"=>$estado]);

            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function verificarAtendidos($pedido){
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                            COUNT( tb_pedidodet.estadoItem ) AS pendientes 
                                                        FROM
                                                            tb_pedidodet 
                                                        WHERE
                                                            tb_pedidodet.estadoItem = 54 
                                                            AND tb_pedidodet.idpedido =:pedido");
                $sql->execute(["pedido"=>$pedido]);
                $result = $sql->fetchAll();

                return $result[0]['pendientes'];
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function actualizarCabeceraPedido($pedido,$estado){
            try {
                $sql = $this->db->connect()->prepare("UPDATE tb_pedidocab 
                                                        SET tb_pedidocab.estadodoc = :estado
                                                        WHERE tb_pedidocab.idreg=:pedido");

                $sql->execute(["pedido"=>$pedido,"estado"=>$estado]);

            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function grabarGuiaTransferencia($cabeceraGuia,$nota,$operacion){
            try {
                if ($operacion == "n") {

                    $guiaAutomatica = $this->numeroGuia();

                    $sql = $this->db->connect()->prepare("INSERT INTO lg_guias SET id_regalm=:nota,cnumguia=:guia,corigen=:origen,
                                                                                        cdirorigen=:direccion_origen,cdestino=:destino,
                                                                                        cdirdest=:direccion_destino,centi=:entidad,centidir=:direccion_entidad,
                                                                                        centiruc=:ruc_entidad,ctraslado=:traslado,cenvio=:envio,
                                                                                        cautoriza=:autoriza,cdestinatario=:destinatario,cobserva=:observaciones,
                                                                                        cnombre=:nombres,cmarca=:marca,clicencia=:licencia,cplaca=:placa,
                                                                                        ftraslado=:fecha_traslado,fguia=:fecha_guia,cmotivo=:motivo,
                                                                                        cserie=:serie");

                    $sql->execute([ "nota"=>$nota,
                                        "guia"=>$guiaAutomatica,
                                        "origen"=>$cabeceraGuia['almacen_origen'],
                                        "direccion_origen"=>$cabeceraGuia['almacen_origen_direccion'],
                                        "destino"=>$cabeceraGuia['almacen_destino'],
                                        "direccion_destino"=>$cabeceraGuia['almacen_destino_direccion'],
                                        "entidad"=>$cabeceraGuia['empresa_transporte_razon'],
                                        "direccion_entidad"=>$cabeceraGuia['direccion_proveedor'],
                                        "ruc_entidad"=>$cabeceraGuia['ruc_proveedor'],
                                        "traslado"=>$cabeceraGuia['modalidad_traslado'],
                                        "envio"=>$cabeceraGuia['tipo_envio'],
                                        "autoriza"=>$cabeceraGuia['autoriza'],
                                        "destinatario"=>$cabeceraGuia['destinatario'],
                                        "observaciones"=>$cabeceraGuia['observaciones'],
                                        "nombres"=>$cabeceraGuia['nombre_conductor'],
                                        "marca"=>$cabeceraGuia['marca'],
                                        "licencia"=>$cabeceraGuia['licencia_conducir'],
                                        "placa"=>$cabeceraGuia['placa'],
                                        "fecha_traslado"=>$cabeceraGuia['ftraslado'],
                                        "fecha_guia"=>$cabeceraGuia['fgemision'],
                                        "motivo"=>$cabeceraGuia['motivo_guia'],
                                        "serie"=>"F001"]);
                        
                        $rowCount = $sql->rowcount();

                        if ($rowCount > 0) {
                            $mensaje = "Registro grabado";
                            $this->actualizarGuiaEnNota($cabeceraGuia,$nota,$guiaAutomatica);
                        }else {
                            $mensaje = "No se actualizo el registro";
                        }
                }else {
                    $guiaAutomatica = $cabeceraGuia['numero_guia'];

                    $sql = $this->db->connect()->prepare("UPDATE lg_guias SET corigen=:origen,
                                                                              cdirorigen=:direccion_origen,cdestino=:destino,
                                                                              cdirdest=:direccion_destino,centi=:entidad,centidir=:direccion_entidad,
                                                                              centiruc=:ruc_entidad,ctraslado=:traslado,cenvio=:envio,
                                                                              cautoriza=:autoriza,cdestinatario=:destinatario,cobserva=:observaciones,
                                                                              cnombre=:nombres,cmarca=:marca,clicencia=:licencia,cplaca=:placa,
                                                                              ftraslado=:fecha_traslado,fguia=:fecha_guia
                                                            WHERE cnumguia=:guia
                                                            LIMIT 1");

                    $sql->execute([ "origen"=>$cabeceraGuia['almacen_origen'],
                                    "direccion_origen"=>$cabeceraGuia['almacen_origen_direccion'],
                                    "destino"=>$cabeceraGuia['almacen_destino'],
                                    "direccion_destino"=>$cabeceraGuia['almacen_destino_direccion'],
                                    "entidad"=>$cabeceraGuia['empresa_transporte_razon'],
                                    "direccion_entidad"=>$cabeceraGuia['direccion_proveedor'],
                                    "ruc_entidad"=>$cabeceraGuia['ruc_proveedor'],
                                    "traslado"=>$cabeceraGuia['modalidad_traslado'],
                                    "envio"=>$cabeceraGuia['tipo_envio'],
                                    "autoriza"=>$cabeceraGuia['autoriza'],
                                    "destinatario"=>$cabeceraGuia['destinatario'],
                                    "observaciones"=>$cabeceraGuia['observaciones'],
                                    "nombres"=>$cabeceraGuia['nombre_conductor'],
                                    "marca"=>$cabeceraGuia['marca'],
                                    "licencia"=>$cabeceraGuia['licencia_conducir'],
                                    "placa"=>$cabeceraGuia['placa'],
                                    "fecha_traslado"=>$cabeceraGuia['ftraslado'],
                                    "fecha_guia"=>$cabeceraGuia['fgemision'],
                                    "guia"=>$guiaAutomatica,]);

                    $rowCount = $sql->rowcount();
    
                    if ($rowCount > 0) {
                        $mensaje = "Registro modificado";
                        $this->actualizarGuiaEnNota($cabeceraGuia,$nota,$guiaAutomatica);
                    }else {
                        $mensaje = "No se modifico la guia";
                    }  
                }

                return array("mensaje"=>$mensaje,"guia"=>$guiaAutomatica);              
                
            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }

        private function verificarGuiaTrans($nota,$guia){
            $sql = $this->db->connect()->prepare("SELECT COUNT(alm_transfercab.idreg) AS existe 
                                                    FROM alm_transfercab 
                                                    WHERE alm_transfercab.idreg=:nota 
                                                    AND alm_transfercab.cnumguia=:guia");
            $sql->execute(["nota"=>$nota,"guia"=>$guia]);
            $result = $sql->fetchAll();

            return $result['0']['existe'];
        }

        private function actualizarGuiaEnNota($cabecera,$nota,$guia){
            try {
                $sql = $this->db->connect()->prepare("UPDATE alm_transfercab 
                                                        SET ffreg=:envio,
                                                        cnumguia=:guia
                                                        WHERE idreg =:nota");
                
                $sql->execute(["envio"=>$cabecera['ftraslado'],
                                "guia"=>$guia,
                                "nota"=>$nota]);
            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }
    
        public function generarVistaPreviaGuiaNota($cabecera,$detalles,$proyecto){
            try {
                require_once("public/formatos/guiaremision.php");
                
                $archivo = "public/documentos/guias_remision/".$cabecera['numero_guia'].".pdf";
                $datos = json_decode($detalles);
                $nreg = count($datos);
                $fecha_emision = date("d/m/Y", strtotime($cabecera['fgemision']));
                $fecha_traslado = date("d/m/Y", strtotime($cabecera['ftraslado']));
                $anio = explode('-',$cabecera['fgemision']);

                if ($cabecera['ftraslado'] !== "")
                    $fecha_traslado = date("d/m/Y", strtotime($cabecera['ftraslado']));
                else 
                    $fecha_traslado = "";

                $pdf = new PDF($cabecera['numero_guia'],
                                $fecha_emision,
                                $cabecera['destinatario_ruc'],
                                $cabecera['destinatario_razon'],
                                $cabecera['destinatario_direccion'],
                                $cabecera['empresa_transporte_razon'],
                                $cabecera['ruc_proveedor'],
                                $cabecera['direccion_proveedor'],
                                $cabecera['almacen_origen_direccion'],
                                null,
                                null,
                                null,
                                $fecha_traslado,
                                $cabecera['modalidad_traslado'],
                                $cabecera['almacen_destino_direccion'],
                                null,
                                null,
                                null,
                                $cabecera['marca'],
                                $cabecera['placa'],
                                $cabecera['nombre_conductor'],
                                $cabecera['licencia_conducir'],
                                $cabecera['tipo_envio'],
                                '',
                                $proyecto,
                                $anio[0],
                                $cabecera["observaciones"],
                                $cabecera["destinatario"],
                                $cabecera["tipo_documento"],
                                'A4');
                $pdf->AliasNbPages();
                $pdf->AddPage();
                $pdf->SetWidths(array(10,15,15,147));
                $pdf->SetFillColor(255,255,255);
                $pdf->SetTextColor(0,0,0);
                
                $pdf->SetFont('Arial','',6);
                $lc = 0;
                $rc = 0;
                $item = 1;

                //aca podria sumar la orden

                for($i=1;$i<=$nreg;$i++){

                    $cantidad = intval($datos[$rc]->cantidad);

                    
                    $pdf->SetX(13);

                    $pdf->SetAligns(array("R","R","C","L"));
                    if ($cantidad > 0){
                         $pdf->Row(array(str_pad($item++,3,"0",STR_PAD_LEFT),
                                        $cantidad,
                                        $datos[$rc]->unidad,
                                        utf8_decode($datos[$rc]->codigo .' '. $datos[$rc]->descripcion  .' P : '.$datos[$rc]->nropedido)));
                    }
                   
                    $lc++;
                    $rc++;

                    if ($lc == 26) {
                        $pdf->AddPage();
                        $lc = 0;
                    }
                }

                $pdf->Ln(1);
                $pdf->SetX(13);
                $pdf->Ln(2);
                $pdf->SetX(13);
                $pdf->Output($archivo,'F');
                    
                return array("archivo"=>$archivo);

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function imprimirFormatoGuiaTransf($cabecera,$detalles,$proyecto,$nota,$operacion){
            try {
                require_once("public/formatos/grpreimpreso.php");
                
                $archivo = "public/documentos/temp/".uniqid().".pdf";
                $datos = json_decode($detalles);
                $nreg = count($datos);
                
                $fecha_emision = date("d/m/Y", strtotime($cabecera['fgemision']));

                if ($cabecera['ftraslado'] !== "")
                    $fecha_traslado = date("d/m/Y", strtotime($cabecera['ftraslado']));
                else 
                    $fecha_traslado = "";

                $anio = explode('-',$cabecera['fgemision']);

                
                $pdf = new PDF($cabecera['numero_guia'],
                                $fecha_emision,
                                $cabecera['destinatario_ruc'],
                                $cabecera['destinatario_razon'],
                                $cabecera['destinatario_direccion'],
                                $cabecera['empresa_transporte_razon'],
                                $cabecera['ruc_proveedor'],
                                $cabecera['direccion_proveedor'],
                                $cabecera['almacen_origen_direccion'],
                                null,
                                null,
                                null,
                                $fecha_traslado,
                                $cabecera['modalidad_traslado'],
                                $cabecera['almacen_destino_direccion'],
                                null,
                                null,
                                null,
                                $cabecera['marca'],
                                $cabecera['placa'],
                                $cabecera['nombre_conductor'],
                                $cabecera['licencia_conducir'],
                                $cabecera['tipo_envio'],
                                null,
                                $proyecto,
                                $anio[0],
                                $cabecera["observaciones"],
                                $cabecera["destinatario"],
                                'A4');
                $pdf->AliasNbPages();
                $pdf->AddPage();
                $pdf->SetWidths(array(10,15,15,147));
                $pdf->SetFillColor(255,255,255);
                $pdf->SetTextColor(0,0,0);
                
                $pdf->SetFont('Arial','',9);
                $lc = 0;
                $rc = 0;

                for($i=1;$i<=$nreg;$i++){

                    $pdf->SetX(3);
                    $pdf->SetCellHeight(3);
                    //$pdf->SetFont('Arial','',3);

                    if( $datos[$rc]->cantidad > 0) {
                        $pdf->SetAligns(array("R","R","C","L"));
                        $pdf->Row(array(str_pad($i,3,"0",STR_PAD_LEFT),
                                    $datos[$rc]->cantidad,
                                    $datos[$rc]->unidad,
                                    utf8_decode($datos[$rc]->codigo .' '. $datos[$rc]->descripcion  .' P : '.$datos[$rc]->nropedido)));
                        $lc++;
                        $rc++;

                        if ($lc == 24) {
                            $pdf->AddPage();
                            $lc = 0;
                        }
                    }
                }

                $pdf->Ln(1);
                    
                    $pdf->Output($archivo,'F');
                    
                    return array("archivo"=>$archivo);

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function listarPedidos(){
            try {
                $salida = "";
                $sql = $this->db->connect()->prepare("SELECT
                                                        ibis.tb_costusu.id_cuser,
                                                        ibis.tb_costusu.ncodproy,
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
                                                        CONCAT_WS(' ',rrhh.tabla_aquarius.apellidos,rrhh.tabla_aquarius.nombres) AS nombres,
                                                        estados.cdescripcion AS estado,
                                                        atencion.cdescripcion AS atencion,
                                                        estados.cabrevia 
                                                    FROM
                                                        ibis.tb_costusu
                                                        INNER JOIN ibis.tb_pedidocab ON tb_costusu.ncodproy = tb_pedidocab.idcostos
                                                        INNER JOIN ibis.tb_proyectos ON tb_costusu.ncodproy = tb_proyectos.nidreg
                                                        INNER JOIN rrhh.tabla_aquarius ON ibis.tb_pedidocab.idsolicita = rrhh.tabla_aquarius.internal
                                                        INNER JOIN ibis.tb_parametros AS estados ON ibis.tb_pedidocab.estadodoc = estados.nidreg
                                                        INNER JOIN ibis.tb_parametros AS atencion ON ibis.tb_pedidocab.nivelAten = atencion.nidreg 
                                                    WHERE
                                                        tb_costusu.id_cuser = :user 
                                                        AND (tb_pedidocab.estadodoc = 54 OR tb_pedidocab.estadodoc = 51) 
                                                        AND tb_pedidocab.idtipomov = 37
                                                        AND tb_costusu.nflgactivo = 1
                                                    ORDER BY tb_pedidocab.emision DESC");
                $sql->execute(["user"=>$_SESSION['iduser']]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()) {
                        $tipo = $rs['idtipomov'] == 37 ? "B":"S";
                        $salida .='<tr class="pointer" data-indice="'.$rs['idreg'].'">
                                        <td class="textoCentro">'.str_pad($rs['nrodoc'],6,0,STR_PAD_LEFT).'</td>
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

        public function generarVariosArchivos($cabecera,$detalles){
            require_once "public/fpdf/fpdf.php";
            require_once("public/fpdi/src/autoload.php");

            $archivo1 = $this->generarPdfTransferencia($cabecera,$detalles,$cabecera['numero']."_1.pdf",'Cargo Almacén');
            $archivo2 = $this->generarPdfTransferencia($cabecera,$detalles,$cabecera['numero']."_2.pdf",'Cargo Logística SPC');
            $archivo3 = $this->generarPdfTransferencia($cabecera,$detalles,$cabecera['numero']."_3.pdf",'Cargo Logística PPC/ Seguridad de vigilancia');
            $archivo4 = $this->generarPdfTransferencia($cabecera,$detalles,$cabecera['numero']."_4.pdf",'Usuario');
            
            // define some files to concatenate
            //$ruta = "public/documentos/notas_transferencia/emitidas/";
            
            $files = array($archivo1,$archivo2,$archivo3,$archivo4);

            // initiate FPDI
            $pdf = new Fpdi();

            // iterate through the files
            foreach($files as $file){
                // get the page count
                $pageCount = $pdf->setSourceFile($file);
                // iterate through all pages
                for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                    // import a page
                    $templateId = $pdf->importPage($pageNo);
                   
                    // get the size of the imported page
                    $size = $pdf->getTemplateSize($templateId);

                    // add a page with the same orientation and size
                    $pdf->AddPage($size['orientation'], $size);

                    // use the imported page
                    $pdf->useTemplate($templateId);
                }
            }

            // Output the new PDF
            $filename = "public/documentos/notas_transferencia/emitidas/".$cabecera['numero'].".pdf";
            $pdf->Output($filename,'F');

            return $filename;
        }

        public function generarPdfTransferencia($cabecera,$detalles,$archivo,$tipo){
            require_once("public/formatos/transferencia.php");
            try {

                $datos = json_decode($detalles);
                $nreg = count($datos);

                $valor_maximo_lineas  = 24;
                $contador_linea = 0;

                $filename = "public/documentos/temp/".$archivo;

                $pdf = new PDF($cabecera['numero'],
                                $cabecera['corigen'],
                                $cabecera['cdestino'],
                                $cabecera['usuario_genera'],
                                $cabecera['motivo_transferencia'],
                                $cabecera['aprueba'],
                                $tipo);
                
                $pdf->AliasNbPages();
                $pdf->AddPage('P','A5');
                $pdf->SetFont('Arial','',6);

                $x = 4;
                $y = $pdf->GetY();
                $rc = 0;
                $item = 1;
                $pdf->SetFont('Arial','',5);

                for($i=1;$i<=$nreg;$i++){
                    if ( $datos[$rc]->cantidad > 0 ){
                        $pdf->SetX(4);
                        $pdf->Cell(10,13,str_pad($item++,3,0,STR_PAD_LEFT),'BL',0,'R');
                        $pdf->Cell(20,13,$datos[$rc]->codigo,'BLR',0,'C');
                        $y = $pdf->GetY();
                        $pdf->SetXY(35,$y+1);
                        $pdf->Multicell(88,2,utf8_decode($datos[$rc]->descripcion)." P:".$datos[$rc]->nropedido,0,'L');
                        $pdf->Line(34,$y+13,122,$y+13);
                        $pdf->SetXY(122,$y);
                        $pdf->Cell(10,13,$datos[$rc]->unidad,'BL',0,'C');
                        $pdf->Cell(13,13,$datos[$rc]->cantidad,'BRL',1,'R');

                        if ( $pdf->GetY() > 164 ) {
                            $pdf->AddPage('P','A5');
                        }
                    }

                    $rc++;
                    $contador_linea++;
                }

                if ( $contador_linea < $valor_maximo_lineas ){
                    $lineas_faltantes = $valor_maximo_lineas-$contador_linea;
        
                    for ($i=0; $i < $lineas_faltantes ; $i++) { 
                        $pdf->SetX(4);
                        $pdf->Cell(10,5,"",'BL',0,'R');
                        $pdf->Cell(20,5,"",'BLR',0,'C');
                        $y = $pdf->GetY();
                        $pdf->SetXY(35,$y+1);
                        $pdf->Multicell(88,2,"",0,'L');
                        $pdf->Line(34,$y+5,122,$y+5);
                        $pdf->SetXY(122,$y);
                        $pdf->Cell(10,5,"",'BL',0,'C');
                        $pdf->Cell(13,5,"",1,1,'R');
                    }  
                }
                
                $pdf->Output($filename,'F');
                
                return $filename;

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }    
        }
    }
?>