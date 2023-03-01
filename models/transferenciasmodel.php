<?php
    class TransferenciasModel extends Model{

        public function __construct()
        {
            parent::__construct();
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

        public function listarPedidosAtencion(){
            try {
                $salida = "";
                $sql = $this->db->connect()->query("SELECT
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
                                                INNER JOIN rrhh.tabla_aquarius ON ibis.tb_pedidocab.idsolicita = rrhh.tabla_aquarius.internal
                                                INNER JOIN ibis.tb_parametros AS estados ON ibis.tb_pedidocab.estadodoc = estados.nidreg
                                                INNER JOIN ibis.tb_parametros AS atencion ON ibis.tb_pedidocab.nivelAten = atencion.nidreg
                                                INNER JOIN ibis.tb_proyectos ON ibis.tb_pedidocab.idcostos = ibis.tb_proyectos.nidreg 
                                            WHERE
                                                ibis.tb_pedidocab.estadodoc = 54
                                                AND ibis.tb_pedidocab.nflgactivo = 1
                                                AND ibis.tb_pedidocab.idtipomov = 37 
                                                AND ISNULL(ibis.tb_pedidocab.asigna)
                                            ORDER BY ibis.tb_pedidocab.emision DESC");
                $sql->execute();
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()) {
                        $tipo = $rs['idtipomov'] == 37 ? "B":"S";
                        $salida .='<tr class="pointer" data-indice="'.$rs['idreg'].'">
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
                                                    cm_producto.ccodprod,
                                                    UPPER(cm_producto.cdesprod) AS cdesprod,
                                                    tb_unimed.cabrevia,
                                                    LPAD(tb_pedidocab.nrodoc,6,0) AS nrodoc,
                                                    ( SELECT SUM( alm_existencia.cant_ingr ) FROM alm_existencia WHERE alm_existencia.idalm = :ingresos AND alm_existencia.codprod = cm_producto.id_cprod ) AS ingreso,
                                                    ( SELECT SUM( alm_inventariodet.cant_ingr ) FROM alm_inventariodet WHERE alm_inventariodet.idalm = :inventario AND alm_inventariodet.codprod = cm_producto.id_cprod ) AS inventario 
                                                FROM
                                                    tb_pedidodet
                                                    INNER JOIN cm_producto ON tb_pedidodet.idprod = cm_producto.id_cprod
                                                    INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed
                                                    INNER JOIN tb_pedidocab ON tb_pedidodet.idpedido = tb_pedidocab.idreg 
                                                WHERE
                                                    idpedido = :indice");
                $sql -> execute(['indice'=>$indice,"ingresos"=>$origen,"inventario"=>$origen]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while($rs = $sql->fetch()) {
                        $existencia = $rs['ingreso']+$rs['inventario'];
                        $salida .= '<tr>
                                        <td class="textoCentro"><a href="'.$rs['iditem'].'" title="Eliminar" data-accion="delete"><i class="fas fa-eraser"></i></a></td>
                                        <td class="textoCentro"><a href="'.$rs['iditem'].'" title="Cambiar" data-accion="change"><i class="fas fa-exchange-alt"></i></a></td>
                                        <td class="textoCentro">'.str_pad($item++,3,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                        <td class="pl20px">'.$rs['cdesprod'].'</td>
                                        <td class="textoCentro">'.$rs['cabrevia'].'</td>
                                        <td><input type="number"></td>
                                        <td class="textoDerecha">'.number_format($existencia,2).'</td>
                                        <td><input type="text"></td>
                                        <td  class="textoCentro">'.$rs['nrodoc'].'</td>
                                    </tr>';
                    }
                }

                return $salida;
            }catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function insertarTransferencia($cabecera,$detalles){
            try {
                $sql = $this->db->connect()->prepare("INSERT INTO alm_transfercab SET idcc,idaprueba,almorigen");

            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function insertarDetalles($indice,$detalles){
            $datos(json_decode($detalles));
            $nreg = count($detalles);
        }
    }
?>