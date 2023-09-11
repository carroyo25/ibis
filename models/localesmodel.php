<?php
    class LocalesModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarCompras($parametros) {
            try {
                $salida = "";
                $item = 1;

                $cc   = "%";
                $mes  = "%";

                if ($parametros != "" ) {
                    $cc = $parametros['costosSearch'] == -1 ? "%" : "%".$parametros['costosSearch']."%";
                }

                $sql = $this->db->connect()->prepare("SELECT
                                                    alm_cabexist.idreg,
                                                    alm_cabexist.idcostos,
                                                    alm_cabexist.iddespacho,
                                                    DATE_FORMAT( alm_cabexist.ffechadoc, '%d/%m/%Y' ) AS ffechadoc,
                                                    alm_cabexist.idautoriza,
                                                    alm_cabexist.idrecepciona,
                                                    UPPER(
                                                    CONCAT_WS( ' ', tb_proyectos.ccodproy, tb_proyectos.cdesproy )) AS costos,
                                                    alm_cabexist.cnrodoc,
                                                    tb_parametros.cdescripcion,
                                                    tb_pedidocab.nrodoc 
                                                FROM
                                                    tb_costusu
                                                    INNER JOIN alm_cabexist ON tb_costusu.ncodproy = alm_cabexist.idcostos
                                                    LEFT JOIN tb_almacen AS origen ON alm_cabexist.ncodalm1 = origen.ncodalm
                                                    LEFT JOIN tb_almacen AS destino ON alm_cabexist.ncodalm2 = destino.ncodalm
                                                    INNER JOIN tb_proyectos ON alm_cabexist.idcostos = tb_proyectos.nidreg
                                                    INNER JOIN tb_parametros ON alm_cabexist.ntipodoc = tb_parametros.nidreg
                                                    LEFT JOIN tb_pedidocab ON alm_cabexist.idped = tb_pedidocab.idreg 
                                                WHERE
                                                    tb_costusu.id_cuser = :usr 
                                                    AND tb_costusu.nflgactivo = 1 
                                                    AND alm_cabexist.ntipomov = 230 
                                                    AND alm_cabexist.idcostos LIKE :cc 
                                                ORDER BY
                                                    alm_cabexist.idreg DESC");

                $sql->execute(["usr"=>$_SESSION["iduser"],"cc"=>$cc]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()){
                        $salida.='<tr class="pointer" data-indice="'.$rs['idreg'].'">
                                    <td class="textoCentro">'.str_pad($rs['idreg'],6,0,STR_PAD_LEFT).'</td>
                                    <td class="textoCentro">'.$rs['ffechadoc'].'</td>
                                    <td class="pl20px">'.$rs['costos'].'</td>
                                    <td class="textoCentro">'.$rs['nrodoc'].'</td>
                                    <td class="pl20px">'.$rs['cnrodoc'].'</td>
                                </tr>';
                    }

                    return $salida;
                }
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function listarPedidosComprasLocales($cc,$pedido){
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
                                                        ibis.tb_proyectos.cdesproy 
                                                    FROM
                                                        ibis.tb_pedidocab
                                                        LEFT JOIN rrhh.tabla_aquarius ON ibis.tb_pedidocab.idsolicita = rrhh.tabla_aquarius.internal
                                                        LEFT JOIN ibis.tb_parametros AS estados ON ibis.tb_pedidocab.estadodoc = estados.nidreg
                                                        LEFT JOIN ibis.tb_parametros AS atencion ON ibis.tb_pedidocab.nivelAten = atencion.nidreg
                                                        LEFT JOIN ibis.tb_proyectos ON ibis.tb_pedidocab.idcostos = ibis.tb_proyectos.nidreg 
                                                    WHERE
                                                        ibis.tb_pedidocab.estadodoc = 54 
                                                        AND ibis.tb_pedidocab.nflgactivo = 1 
                                                        AND ibis.tb_pedidocab.idtipomov = 37 
                                                        AND tb_pedidocab.nrodoc LIKE :pedido
                                                        AND tb_pedidocab.idcostos = :cc
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

        public function itemsCompra($indice,$origen){
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
                                                        AND tb_pedidodet.cant_orden <> tb_pedidodet.cant_aprob 
                                                        AND tb_pedidodet.estadoItem = 54
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
                                        <td class="textoCentro"><a href="'.$rs['iditem'].'" title="Eliminar" data-accion="delete"><i class="fas fa-eraser"></i></a></td>
                                        <td class="textoCentro"><a href="'.$rs['iditem'].'" title="Cambiar" data-accion="change"><i class="fas fa-exchange-alt"></i></a></td>
                                        <td class="textoCentro">'.str_pad($item++,3,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                        <td class="pl20px">'.$rs['cdesprod'].'</td>
                                        <td class="textoCentro">'.$rs['cabrevia'].'</td>
                                        <td class="textoDerecha">'.$rs['cant_aprob'].'</td>
                                        <td><input type="text"></td>
                                        <td  class="textoCentro">'.$rs['nrodoc'].'</td>
                                    </tr>';
                    }
                }

                return array("items"=>$salida,
                            "total_items"=>$this->cantidadItemsPedido($indice));


            }catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function cantidadItemsPedido($pedido){
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

        public function insertarCompra($cabecera,$detalles,$pedido,$atendidos){

            try {
                $mensaje = "Error al grabar el registro";
                $sw = false;
                $tipomov = 230;

                $sql = $this->db->connect()->prepare("INSERT INTO alm_cabexist SET idcostos=:costos,
                                                                                   ffechadoc=:fecha,
                                                                                   idautoriza=:aprueba,
                                                                                   ntipomov=:tipo,
                                                                                   flgActivo=:estado,
                                                                                   ntipodoc=:comprobante,
                                                                                   cnrodoc=:nro_comp,
                                                                                   idped=:pedido,
                                                                                   idrecepciona=:registra");
                
                $sql->execute([
                    "costos"=>$cabecera['codigo_costos'],
                    "aprueba"=>$cabecera['codigo_autoriza'],
                    "fecha"=>$cabecera['fecha'],
                    "tipo"=>$cabecera['codigo_movimiento'],
                    "estado"=>1,
                    "comprobante"=>$cabecera['codigo_comprobante'],
                    "nro_comp"=>$cabecera['nrodoc'],
                    "pedido"=>$pedido,
                    "registra"=>$cabecera['codigo_registra']]);

                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    $mensaje = "Registro insertado";
                    $sw = true;

                    $indice = $this->lastInsertId("SELECT MAX(idreg) AS id FROM alm_cabexist");
                    
                    $this->insertarDetalles($indice,$detalles);

                    $estado = 230;

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

                    $sql = $this->db->connect()->prepare("INSERT INTO alm_existencia SET idregistro=:compra,
                                                                                        idpedido=:iditem,
                                                                                        tipo=:estado,
                                                                                        codprod=:producto,
                                                                                        cant_ingr=:cantidad,
                                                                                        observaciones=:observa,
                                                                                        nflgActivo=:activo,
                                                                                        nropedido=:pedido");
                    
                    $sql->execute(["compra"=>$indice,
                        "iditem"=>$datos[$i]->iditem,
                        "producto"=>$datos[$i]->idprod,
                        "cantidad"=>$datos[$i]->cantidad,
                        "activo"=>1,
                        "estado"=>3,
                        "observa"=>$datos[$i]->obser,
                        "pedido"=>$datos[$i]->pedido]);

                    if ( $datos[$i]->cantidad != 0 ){
                        $this->actualizarDetallesPedido($datos[$i]->iditem,$datos[$i]->cantidad);
                    }
                } catch (PDOException $th) {
                    echo $th->getMessage();
                    return false;
                }
            }
        }

        private function actualizarDetallesPedido($item,$cantidad){
            try {
                $estado = 230;

                $sql = $this->db->connect()->prepare("UPDATE tb_pedidodet 
                                                        SET tb_pedidodet.estadoItem =:estado,
                                                            tb_pedidodet.cant_atend =:cantidad
                                                        WHERE tb_pedidodet.iditem =:item");
                $sql->execute(["item"=>$item,
                                "cantidad"=>$cantidad,
                                "estado"=>$estado]);

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

        public function consultarCompra($id){
            try {
                $result = [];

                $sql = $this->db->connect()->prepare("SELECT
                                                        alm_cabexist.idreg,
                                                        alm_cabexist.idautoriza,
                                                        alm_cabexist.idrecepciona,
                                                        alm_cabexist.ntipodoc,
                                                        alm_cabexist.cnrodoc,
                                                        alm_cabexist.ffechadoc,
                                                        autorizacion.cnombres AS autoriza,
                                                        registro.cnombres AS recepciona,
                                                        documentos.cdescripcion AS comprobante,
                                                        tb_proyectos.cdesproy AS costos,
                                                        movimientos.cdescripcion AS tipoMov
                                                    FROM
                                                        alm_cabexist
                                                        INNER JOIN tb_user AS autorizacion ON alm_cabexist.idautoriza = autorizacion.iduser
                                                        INNER JOIN tb_user AS registro ON alm_cabexist.idrecepciona = registro.iduser
                                                        INNER JOIN tb_parametros AS documentos ON alm_cabexist.ntipodoc = documentos.nidreg
                                                        INNER JOIN tb_proyectos ON alm_cabexist.idcostos = tb_proyectos.nidreg
                                                        INNER JOIN tb_parametros AS movimientos ON alm_cabexist.ntipomov = movimientos.nidreg 
                                                    WHERE
                                                        alm_cabexist.idreg = :compra");

                $sql->execute(["compra"=>$id]);

                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    $docData = array();
                    while($row=$sql->fetch(PDO::FETCH_ASSOC)){
                        $docData[] = $row;
                    }
                }

                return array("cabecera"=>$docData,
                            "detalles"=>$this->detallesCompra($id));

            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function detallesCompra($id){
            try {
                $salida = "";

                $sql = $this->db->connect()->prepare("SELECT
                                                            alm_existencia.idreg,
                                                            alm_existencia.idalm,
                                                            alm_existencia.idpedido,
                                                            FORMAT(alm_existencia.cant_ingr,2) AS cant_ingr,
                                                            UPPER( alm_existencia.observaciones ) AS observaciones,
                                                            alm_existencia.nropedido,
                                                            alm_existencia.nroorden,
                                                            alm_existencia.codprod,
                                                            cm_producto.ccodprod,
                                                            UPPER( cm_producto.cdesprod ) AS descripcion,
                                                            LPAD(tb_pedidocab.nrodoc,3,0) AS nrodoc,
                                                            tb_unimed.cabrevia 
                                                        FROM
                                                            alm_existencia
                                                            INNER JOIN cm_producto ON alm_existencia.codprod = cm_producto.id_cprod
                                                            INNER JOIN tb_pedidocab ON alm_existencia.nropedido = tb_pedidocab.idreg
                                                            INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed 
                                                        WHERE
                                                            alm_existencia.idregistro = :id 
                                                            AND alm_existencia.nflgActivo = 1");
                $sql->execute(["id"=>$id]);

                $rowCount = $sql->rowCount();
                $item = 1;

                if ($rowCount > 0) {
                    while ( $rs = $sql->fetch()){
                        $salida .= '<tr class="pointer"
                                            data-grabado="1"
                                            data-itempedido="'.$rs['idpedido'].'"
                                            data-idprod="'.$rs['ccodprod'].'" 
                                            data-codund="" 
                                            data-idx="'.$rs['idreg'].'">
                                        <td class="textoCentro"><a href="'.$rs['idreg'].'"><i class="fas fa-eraser"></i></a></td>
                                        <td class="textoCentro"><a href="'.$rs['idreg'].'"><i class="fas fa-exchange-alt"></i></a></td>
                                        <td class="textoCentro">'.str_pad($item++,3,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                        <td class="pl20px">'.$rs['descripcion'].'</td>
                                        <td class="textoCentro">'.$rs['cabrevia'].'</td>
                                        <td class="textoDerecha">'.$rs['cant_ingr'].'</td>
                                        <td>'.$rs['observaciones'].'</td>
                                        <td class="textoCentro">'.$rs['nrodoc'].'</td>
                                    </tr>';
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }
    }
?>