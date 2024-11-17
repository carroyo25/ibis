<?php
    class RegistrosModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarDespachos($guia){
            $salida = "";

            $nguia = $guia == "" ? "%":$guia;
            
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                        alm_despachocab.id_regalm,
                                                        YEAR ( alm_despachocab.ffecdoc ) AS anio,
                                                        DATE_FORMAT( alm_despachocab.ffecdoc, '%d/%m/%Y' ) AS ffecdoc,
                                                        alm_despachocab.nnronota,
                                                        alm_despachocab.nReferido,
                                                        UPPER( origen.cdesalm ) AS origen,
                                                        UPPER( destino.cdesalm ) AS destino,
                                                        UPPER( CONCAT_WS( ' ', tb_proyectos.ccodproy, tb_proyectos.cdesproy ) ) AS costos,
                                                        tb_parametros.cdescripcion,
                                                        tb_parametros.cabrevia,
                                                        alm_despachocab.nEstadoDoc,
                                                        alm_despachocab.cnumguia,
                                                        tb_costusu.id_cuser/*,
                                                        d.despachos,
                                                        i.ingresos*/ 
                                                    FROM
                                                        alm_despachocab
                                                        INNER JOIN tb_almacen AS origen ON alm_despachocab.ncodalm1 = origen.ncodalm
                                                        INNER JOIN tb_almacen AS destino ON alm_despachocab.ncodalm2 = destino.ncodalm
                                                        INNER JOIN tb_proyectos ON alm_despachocab.ncodpry = tb_proyectos.nidreg
                                                        INNER JOIN tb_parametros ON alm_despachocab.nEstadoDoc = tb_parametros.nidreg
                                                        INNER JOIN tb_costusu ON alm_despachocab.ncodpry = tb_costusu.ncodproy
                                                        /*LEFT JOIN ( SELECT SUM( alm_despachodet.ndespacho ) AS despachos, alm_despachodet.id_regalm FROM alm_despachodet GROUP BY alm_despachodet.id_regalm ) AS d ON alm_despachocab.id_regalm = d.id_regalm
                                                        LEFT JOIN ( SELECT SUM( alm_existencia.cant_ingr ) AS ingresos, alm_existencia.iddespacho FROM alm_existencia GROUP BY alm_existencia.iddespacho ) AS i ON alm_despachocab.id_regalm = i.iddespacho */
                                                    WHERE
                                                        alm_despachocab.nflgactivo = 1 
                                                        AND tb_costusu.nflgactivo = 1 
                                                        AND tb_costusu.id_cuser = :usr
                                                        AND alm_despachocab.nEstadoDoc = 62 
                                                        AND alm_despachocab.cnumguia LIKE :guia 
                                                    ORDER BY
                                                        alm_despachocab.ffecdoc DESC");
                $sql->execute(["usr"=>$_SESSION['iduser'],"guia"=>$nguia]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()){
                        //$resto  = $rs['despachos'] - $rs['ingresos'];
                        
                        //if ( $resto != 0 ) {
                            $salida .='<tr data-indice="'.$rs['id_regalm'].'" class="pointer" data-guia="'.$rs['cnumguia'].'">                                        
                                    <td class="textoCentro">'.str_pad($rs['id_regalm'],6,0,STR_PAD_LEFT).'</td>
                                    <td class="pl20px">'.$rs['ffecdoc'].'</td>
                                    <td class="pl20px">'.$rs['origen'].'</td>
                                    <td class="pl20px">'.$rs['destino'].'</td>
                                    <td class="pl20px">'.$rs['costos'].'</td>
                                    <td class="textoCentro">'.$rs['anio'].'</td>
                                    <td class="textoCentro">'.$rs['cnumguia'].'</td>
                                    <td class="textoCentro">'.$rs['nReferido'].'</td>
                                    <td></td>
                                </tr>';
                        //}
                       
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        /*importa desde las guias de Lurin*/
        public function importarDespachoSalidas($indice){
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                        alm_despachocab.id_regalm,
                                                        alm_despachocab.ncodpry,
                                                        alm_despachocab.ffecenvio,
                                                        alm_despachocab.ffecdoc,
                                                        alm_despachocab.cnumguia,
                                                        alm_despachocab.nReferido,
                                                        UPPER( origen.cdesalm ) AS origen,
                                                        UPPER( destino.cdesalm ) AS destino,
                                                        alm_despachocab.ncodalm1,
                                                        alm_despachocab.ncodalm2,
                                                        UPPER( CONCAT_WS( ' ', tb_proyectos.ccodproy, tb_proyectos.cdesproy ) ) AS costos
                                                        
                                                    FROM
                                                        alm_despachocab
                                                        INNER JOIN tb_almacen AS origen ON alm_despachocab.ncodalm1 = origen.ncodalm
                                                        INNER JOIN tb_almacen AS destino ON alm_despachocab.ncodalm2 = destino.ncodalm
                                                        INNER JOIN tb_proyectos ON alm_despachocab.ncodpry = tb_proyectos.nidreg 
                                                    WHERE
                                                        alm_despachocab.id_regalm = :indice");
                $sql->execute(["indice"=>$indice]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    $docData = array();
                    while($row=$sql->fetch(PDO::FETCH_ASSOC)){
                        $docData[] = $row;
                    }

                    $detalles = $this->detallesDespachoSalidas($indice);
                }
                
                $indice = $this->ultimoIndice() + 1;
                return array("cabecera"=>$docData,
                             "numero"=>str_pad($indice,6,0,STR_PAD_LEFT),
                             "detalles"=>$detalles);
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function detallesDespachoSalidas($indice){
            try {
                $salida = "";
                $item = 1;
                $sql = $this->db->connect()->prepare("SELECT
                                                        ibis.alm_despachodet.niddeta,
                                                        ibis.alm_despachodet.id_regalm,
                                                        ibis.alm_despachodet.ncodalm1,
                                                        ibis.alm_despachodet.ncodalm2,
                                                        ibis.alm_despachodet.id_cprod,
                                                        ibis.alm_despachodet.ncantidad,
                                                        ibis.alm_despachodet.nsaldo,
                                                        ibis.alm_despachodet.ndespacho,
                                                        ibis.alm_despachodet.nfactor,
                                                        LPAD(ibis.alm_despachodet.nroorden,6,0) AS pedido,
                                                        LPAD(ibis.alm_despachodet.nropedido,6,0) AS orden,
                                                        UPPER(
                                                        CONCAT_WS( ' ', cm_producto.cdesprod, tb_pedidodet.observaciones )) AS descripcion,
                                                        ibis.cm_producto.id_cprod,
                                                        ibis.cm_producto.ccodprod,
                                                        ibis.tb_unimed.cabrevia,
                                                        ibis.alm_despachodet.niddetaPed,
                                                        UPPER(ibis.tb_area.cdesarea) AS cdesarea,
                                                        ibis.tb_area.ncodarea,
                                                        rrhh.tabla_aquarius.apellidos,
                                                        rrhh.tabla_aquarius.nombres,
                                                        ibis.tb_pedidocab.idcostos,
                                                        ibis.alm_despachocab.cnumguia  
                                                    FROM
                                                        ibis.alm_despachodet
                                                        INNER JOIN ibis.cm_producto ON alm_despachodet.id_cprod = cm_producto.id_cprod
                                                        INNER JOIN ibis.tb_unimed ON cm_producto.nund = tb_unimed.ncodmed
                                                        INNER JOIN ibis.tb_pedidodet ON alm_despachodet.niddetaPed = tb_pedidodet.iditem
                                                        INNER JOIN ibis.tb_pedidocab ON tb_pedidodet.idpedido = tb_pedidocab.idreg
                                                        INNER JOIN ibis.tb_area ON tb_pedidocab.idarea = tb_area.ncodarea
                                                        INNER JOIN rrhh.tabla_aquarius ON ibis.tb_pedidocab.idsolicita = rrhh.tabla_aquarius.internal
                                                        INNER JOIN ibis.alm_despachocab ON ibis.alm_despachodet.id_regalm = ibis.alm_despachocab.id_regalm
                                                    WHERE
                                                        alm_despachodet.id_regalm = :indice
                                                        AND alm_despachodet.nflgactivo = 1");
                $sql->execute(["indice"=>$indice]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0){
                    while ($rs = $sql->fetch()){
                        $salida .='<tr class="pointer" data-idpet="'.$rs['niddetaPed'].'"
                                                        data-area="'.$rs['ncodarea'].'"
                                                        data-almacen = "'.$rs['ncodalm2'].'"
                                                        data-codprod = "'.$rs['id_cprod'].'"
                                                        data-costos = "'.$rs['idcostos'].'">
                                        <td class="textoCentro">'.str_pad($item++,3,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro"> '.$rs['ccodprod'].'</td>
                                        <td class="pl20px">'.$rs['descripcion'].'</td>
                                        <td class="textoCentro">'.$rs['cabrevia'].'</td>
                                        <td class="textoDerecha pr5px">'.$rs['ndespacho'].'</td>
                                        <td class="textoDerecha"><input type="number" min="1" value="'.$rs['ndespacho'].'"></td>
                                        <td><input type="text"></td>
                                        <td class="pl20px">'.$rs['cdesarea'].'</td>
                                        <td><input type="date"></td>
                                        <td><input type="text"></td>
                                        <td><input type="text"></td>
                                        <td class="textoCentro">'.$rs['pedido'].'</td>
                                        <td class="textoCentro">'.$rs['orden'].'</td>
                                        <td class="textoCentro">'.$rs['cnumguia'].'</td>
                                        <td class="textoCentro"><a href="'.$rs['niddetaPed'].'" ><i class="fas fa-paperclip"></i></a></td>
                                    </tr>';
                    } 
                }
                return $salida;
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        /*importa desde las guias madre */
        public function importarDespachoMadres($indice){
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                        alm_madrescab.id_regalm,
                                                        alm_madrescab.ncodcos,
                                                        alm_madrescab.cnumguia,
                                                        UPPER( CONCAT_WS( ' ', tb_proyectos.ccodproy, tb_proyectos.cdesproy ) ) AS costos,
                                                        lg_guias.corigen AS origen,
                                                        lg_guias.cdestino AS destino,
                                                        origen.ncodalm AS ncodalm1,
                                                        destino.ncodalm AS ncodalm2 
                                                    FROM
                                                        alm_madrescab
                                                        INNER JOIN tb_proyectos ON alm_madrescab.ncodcos = tb_proyectos.nidreg
                                                        INNER JOIN lg_guias ON alm_madrescab.cnumguia = lg_guias.cnumguia
                                                        INNER JOIN tb_almacen AS origen ON lg_guias.corigen = origen.cdesalm
                                                        INNER JOIN tb_almacen AS destino ON lg_guias.cdestino = destino.cdesalm 
                                                    WHERE
                                                        alm_madrescab.id_regalm =:indice");
                $sql->execute(["indice"=>$indice]);

                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    $docData = array();
                    while($row=$sql->fetch(PDO::FETCH_ASSOC)){
                        $docData[] = $row;
                    }

                    $detalles = $this->detallesDespachosMadres($indice);
                }
                
                $indice = $this->ultimoIndice() + 1;
                return array("cabecera"=>$docData,
                             "numero"=>str_pad($indice,6,0,STR_PAD_LEFT),
                             "detalles"=>$detalles);
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function detallesDespachosMadres($indice){
            try {
                $salida = "";
                $item = 1;
                $sql = $this->db->connect()->prepare("SELECT
                                                        alm_madresdet.niddeta,
                                                        alm_madresdet.id_regalm,
                                                        alm_madresdet.id_cprod,
                                                        alm_madresdet.ncantidad,
                                                        cm_producto.ccodprod,
                                                        tb_unimed.cabrevia,
                                                        guias.id_regalm AS registro_despacho,
                                                        UPPER(
                                                        CONCAT_WS( ' ', cm_producto.cdesprod )) AS descripcion,
                                                        alm_despachodet.niddetaPed,
                                                        tb_pedidocab.nrodoc AS pedido,
                                                        tb_pedidocab.idreg,
                                                        lg_ordencab.cnumero AS orden,
                                                        lg_ordencab.ncodcos AS idcostos,
                                                        tb_area.ncodarea,
                                                        tb_pedidocab.idarea,
                                                        UPPER(tb_area.cdesarea) AS cdesarea,
                                                        madres.cdestino,
                                                        madres.cnumguia,
                                                        tb_almacen.ncodalm AS ncodalm2
                                                    FROM
                                                        alm_madresdet
                                                        LEFT JOIN alm_madrescab ON alm_madresdet.id_regalm = alm_madrescab.id_regalm
                                                        LEFT JOIN cm_producto ON alm_madresdet.id_cprod = cm_producto.id_cprod
                                                        LEFT JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed
                                                        LEFT JOIN lg_guias AS guias ON alm_madresdet.nGuia = guias.cnumguia
                                                        LEFT JOIN alm_despachodet ON alm_despachodet.id_regalm = guias.id_regalm 
                                                        AND cm_producto.id_cprod = alm_despachodet.id_cprod
                                                        LEFT JOIN tb_pedidodet ON alm_despachodet.niddetaPed = tb_pedidodet.iditem
                                                        LEFT JOIN tb_pedidocab ON tb_pedidodet.idpedido = tb_pedidocab.idreg
                                                        LEFT JOIN lg_ordendet ON alm_despachodet.niddetaPed = lg_ordendet.niddeta
                                                        LEFT JOIN lg_ordencab ON lg_ordendet.id_orden = lg_ordencab.id_regmov
                                                        LEFT JOIN tb_area ON tb_pedidocab.idarea = tb_area.ncodarea
                                                        LEFT JOIN lg_guias AS madres ON alm_madresdet.nGuiaMadre = madres.cnumguia
                                                        LEFT JOIN tb_almacen ON tb_almacen.cdesalm = madres.cdestino
                                                    WHERE
                                                        alm_madrescab.id_regalm = :indice
                                                    GROUP BY alm_despachodet.niddetaPed");
                
                $sql->execute(["indice"=>$indice]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0){
                    while ($rs = $sql->fetch()){
                        $salida .='<tr class="pointer" data-idpet="'.$rs['niddetaPed'].'"
                                                        data-area="'.$rs['ncodarea'].'"
                                                        data-almacen = "'.$rs['ncodalm2'].'"
                                                        data-codprod = "'.$rs['id_cprod'].'"
                                                        data-costos = "'.$rs['idcostos'].'">
                                        <td class="textoCentro">'.str_pad($item++,3,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro"> '.$rs['ccodprod'].'</td>
                                        <td class="pl20px">'.$rs['descripcion'].'</td>
                                        <td class="textoCentro">'.$rs['cabrevia'].'</td>
                                        <td class="textoDerecha pr5px">'.$rs['ncantidad'].'</td>
                                        <td class="textoDerecha"><input type="number" min="1" value="'.$rs['ncantidad'].'"></td>
                                        <td><input type="text"></td>
                                        <td class="pl20px">'.$rs['cdesarea'].'</td>
                                        <td><input type="date"></td>
                                        <td><input type="text"></td>
                                        <td><input type="text"></td>
                                        <td class="textoCentro">'.$rs['pedido'].'</td>
                                        <td class="textoCentro">'.$rs['orden'].'</td>
                                        <td class="textoCentro">'.$rs['cnumguia'].'</td>
                                        <td class="textoCentro"><a href="'.$rs['niddetaPed'].'" ><i class="fas fa-paperclip"></i></a></td>
                                    </tr>';
                    } 
                }

                return $salida;
                
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function ultimoIndice(){
            $indice = $this->lastInsertId("SELECT MAX(idreg) AS id FROM alm_cabexist");
            return $indice;
        }

        public function grabarRegistros($cabecera,$detalles,$tipo) {
            try {   
                $sql = $this->db->connect()->prepare("INSERT INTO alm_cabexist SET idcostos=:costos,
                                                                                    iddespacho=:despacho,
                                                                                    ffechadoc=:fecha,
                                                                                    idautoriza=:autoriza,
                                                                                    idrecepciona=:recepciona,
                                                                                    numguia=:guia,
                                                                                    nreferido=:referido,
                                                                                    ncodalm1=:origen,
                                                                                    ncodalm2=:destino");
                $sql->execute(["costos" =>$cabecera['codigo_costos'],
                                "despacho"=>$cabecera['codigo_despacho'],
                                "fecha"=>$cabecera['fecha'],
                                "autoriza"=>$cabecera['codigo_autoriza'],
                                "recepciona"=>$cabecera['codigo_recepcion'],
                                "guia"=>$cabecera['cnumguia'],
                                "referido"=>$cabecera['referido'],
                                "origen"=>$cabecera['codigo_almacen_origen'],
                                "destino"=>$cabecera['codigo_almacen_destino']]);
                $rowCount = $sql->rowCount();
                $indice = $this->ultimoIndice();

                if ($rowCount > 0) {
                    $this->grabarDetalllesIngreso($indice,$detalles,$cabecera['codigo_despacho'],$cabecera['cnumguia'],$tipo);
                    return array("estado"=>true);
                }else{
                    return array("estado"=>false);
                }
                
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function grabarDetalllesIngreso($indice,$detalles,$despacho,$guia,$tipo){
            try {
                $datos = json_decode($detalles);
                $nreg = count($datos);

                for ($i=0; $i < $nreg; $i++) {
                    $sql = $this->db->connect()->prepare("INSERT INTO alm_existencia 
                                                            SET idalm=:almacen,
                                                                idregistro=:indice,
                                                                iddespacho=:despacho,
                                                                codprod=:item,
                                                                tipo=:tipoMovimiento,
                                                                cant_ingr=:cantidad,
                                                                nguia=:guia,
                                                                observaciones=:observ,
                                                                ubicacion=:ubica,
                                                                area_solicita=:areapedido,
                                                                cant_ord=:cant_orden,
                                                                vence=:fecha_vence,
                                                                idpedido=:itempedido,
                                                                nropedido=:pedido,
                                                                nroorden=:orden");
                    $sql->execute(["almacen" =>$datos[$i]->almacen, 
                                    "indice" =>$indice,
                                    "despacho"=>$despacho,
                                    "item"=>$datos[$i]->codprod,
                                    "cantidad"=>$datos[$i]->cantrecep,
                                    "guia"=>$guia,
                                    "observ"=>$datos[$i]->observac,
                                    "ubica"=>$datos[$i]->ubica,
                                    "areapedido"=>$datos[$i]->area,
                                    "cant_orden"=>$datos[$i]->cantenv,
                                    "fecha_vence"=>$datos[$i]->vence,
                                    "itempedido"=>$datos[$i]->iddepet,
                                    "pedido"=>$datos[$i]->pedido,
                                    "orden"=>$datos[$i]->orden,
                                    "tipoMovimiento"=>$tipo]);
                }

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function listarIngresos($parametros) {
            try {
                $salida = "";
                $item = 1;

                $guia = "%";
                $cc   = "%";
                $mes  = "%";

                if ($parametros != "" ) {
                    $guia = $parametros['guiaSearch'] == "" ? "%" : "%".$parametros['guiaSearch']."%";
                    $cc = $parametros['costosSearch'] == -1 ? "%" : "%".$parametros['costosSearch']."%";
                }

                $sql = $this->db->connect()->prepare("SELECT
                                                        alm_cabexist.idreg,
                                                        alm_cabexist.idcostos,
                                                        alm_cabexist.iddespacho,
                                                        DATE_FORMAT(alm_cabexist.ffechadoc,'%d/%m/%Y') AS ffechadoc,
                                                        alm_cabexist.idautoriza,
                                                        alm_cabexist.idrecepciona,
                                                        alm_cabexist.numguia,
                                                        alm_cabexist.nreferido,
                                                        alm_cabexist.ncodalm1,
                                                        alm_cabexist.ncodalm2,
                                                        UPPER( origen.cdesalm ) AS origen,
                                                        UPPER( destino.cdesalm ) AS destino,
                                                        UPPER(CONCAT_WS(' ',tb_proyectos.ccodproy,tb_proyectos.cdesproy)) AS costos
                                                    FROM
                                                        tb_costusu
                                                        INNER JOIN alm_cabexist ON tb_costusu.ncodproy = alm_cabexist.idcostos
                                                        INNER JOIN tb_almacen AS origen ON alm_cabexist.ncodalm1 = origen.ncodalm
                                                        INNER JOIN tb_almacen AS destino ON alm_cabexist.ncodalm2 = destino.ncodalm
                                                        INNER JOIN tb_proyectos ON alm_cabexist.idcostos = tb_proyectos.nidreg 
                                                    WHERE
                                                        tb_costusu.id_cuser = :usr 
                                                        AND tb_costusu.nflgactivo = 1
                                                        AND alm_cabexist.numguia LIKE :guia
                                                        AND alm_cabexist.idcostos LIKE :cc
                                                    ORDER BY  alm_cabexist.idreg DESC
                                                    LIMIT 0,50");
                $sql->execute(["usr"=>$_SESSION["iduser"],
                                "guia"=>$guia,
                                "cc"=>$cc]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()){
                        $salida.='<tr class="pointer" data-indice="'.$rs['idreg'].'">
                                    <td class="textoCentro">'.str_pad($rs['idreg'],6,0,STR_PAD_LEFT).'</td>
                                    <td class="textoCentro">'.$rs['ffechadoc'].'</td>
                                    <td class="pl20px">'.$rs['origen'].'</td>
                                    <td class="pl20px">'.$rs['destino'].'</td>
                                    <td class="pl20px">'.$rs['costos'].'</td>
                                    <td class="textoCentro">'.$rs['numguia'].'</td>
                                    <td class="textoCentro">'.$rs['nreferido'].'</td>
                                </tr>';
                    }

                    return $salida;
                }
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        /*consulta de la tabla principal */

        public function consultarID($indice){
            try {
               $sql = $this->db->connect()->prepare("SELECT
                                                    alm_cabexist.ffechadoc,
                                                    alm_cabexist.idautoriza,
                                                    alm_cabexist.idreg,
                                                    UPPER( origen.cdesalm ) AS origen,
                                                    UPPER(
                                                    CONCAT_WS( ' ', tb_proyectos.ccodproy, tb_proyectos.cdesproy )) AS costos,
                                                    alm_cabexist.numguia,
                                                    alm_cabexist.nreferido,
                                                    alm_cabexist.idcostos,
                                                    alm_cabexist.iddespacho,
                                                    alm_cabexist.ncodalm1,
                                                    alm_cabexist.ncodalm2,
                                                    tb_proyectos.nidreg,
                                                    LPAD(alm_cabexist.idreg,6,0) AS numero,
                                                    UPPER( destino.cdesalm ) AS destino,
                                                    alm_cabexist.idrecepciona,
                                                    tb_user.cnombres  
                                                FROM
                                                    alm_cabexist
                                                    INNER JOIN tb_almacen AS origen ON alm_cabexist.ncodalm1 = origen.ncodalm
                                                    INNER JOIN tb_almacen AS destino ON alm_cabexist.ncodalm2 = destino.ncodalm
                                                    INNER JOIN tb_proyectos ON alm_cabexist.idcostos = tb_proyectos.nidreg
                                                    INNER JOIN tb_user ON alm_cabexist.idautoriza = tb_user.iduser 
                                                WHERE
                                                    alm_cabexist.idreg = :id");
                $sql->execute(["id"=>$indice]);

                $docData = array();
                while($row=$sql->fetch(PDO::FETCH_ASSOC)){
                    $docData[] = $row;
                }

                return array("cabecera"=>$docData,
                            "detalles"=>$this->registroDetalles($indice),
                            "total_adjuntos"=>$this->contarAdjuntos($indice,"GA"));
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function registroDetalles($indice){
            try {
                $salida = "";
                $item=1;
                $sql = $this->db->connect()->prepare("SELECT
                                                        LPAD(alm_existencia.nropedido,6,0) AS pedido,
                                                        LPAD(alm_existencia.nroorden,6,0) AS orden,
                                                        FORMAT(alm_existencia.cant_ingr,2) AS cant_ingr,
                                                        FORMAT(alm_existencia.cant_ord,2) AS cant_ord,
                                                        cm_producto.ccodprod,
                                                        UPPER(
                                                        CONCAT_WS( ' ', cm_producto.cdesprod, tb_pedidodet.observaciones )) AS descripcion,
                                                        tb_unimed.cabrevia,
                                                        UPPER( tb_area.cdesarea ) AS area_solicita,
                                                        alm_existencia.observaciones,
                                                        alm_existencia.ubicacion,
                                                        alm_existencia.condicion,
                                                        alm_existencia.vence,
                                                        alm_existencia.idreg,
                                                        tb_pedidodet.docEspec/*,
	                                                    lg_ordencab.cnumero */
                                                    FROM
                                                        alm_existencia
                                                        LEFT JOIN cm_producto ON alm_existencia.codprod = cm_producto.id_cprod
                                                        LEFT JOIN tb_pedidodet ON alm_existencia.idpedido = tb_pedidodet.iditem
                                                        LEFT JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed
                                                        LEFT JOIN tb_area ON alm_existencia.area_solicita = tb_area.ncodarea
                                                        /*LEFT JOIN lg_ordendet ON tb_pedidodet.iditem = lg_ordendet.niddeta
                                                        LEFT JOIN lg_ordencab ON lg_ordendet.id_regmov = lg_ordencab.id_regmov */  
                                                    WHERE
                                                        alm_existencia.idregistro = :id
                                                        /*AND (ISNULL(lg_ordendet.nEstadoReg) OR lg_ordendet.nEstadoReg != 105)*/");
                $sql->execute(["id"=>$indice]);

                $rowCount = $sql->rowCount();

                if ($rowCount > 0){
                    while ($rs = $sql->fetch()){

                        $adjunto    = $rs['docEspec'] == NULL ? '#' : $rs['docEspec'];
                        $icono      = $rs['docEspec'] == NULL ? '<i class="fas fa-paperclip"></i>' : '<i class="far fa-file"></i>';

                        $salida .= '<tr data-idreg="'.$rs['idreg'].'">
                                        <td class="textoCentro">'.str_pad($item++,3,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                        <td class="pl20px">'.$rs['descripcion'].'</td>
                                        <td class="textoCentro">'.$rs['cabrevia'].'</td>
                                        <td class="textoDerecha pr5px">'.$rs['cant_ord'].'</td>
                                        <td class="textoDerecha pr5px">'.$rs['cant_ingr'].'</td>
                                        <td class="pl20px">'.$rs['observaciones'].'</td>
                                        <td class="pl20px">'.$rs['area_solicita'].'</td>
                                        <td class="textoCentro">'.$rs['vence'].'</td>
                                        <td class="textoCentro">'.$rs['condicion'].'</td>
                                        <td class="pl20px">'.$rs['ubicacion'].'</td>
                                        <td class="textoCentro">'.$rs['pedido'].'</td>
                                        <td class="textoCentro"></td>
                                        <td class="textoCentro"></td>
                                        <td class="textoCentro"><a href="'.$rs['docEspec'].'">'.$icono.'</a></td>
                                    </tr>';
                    }
                }
                return $salida;

             } catch (PDOException $th) {
                 echo "Error: ".$th->getMessage();
                 return false;
             }
        }

        /*----*/

        public function listarTransferencias($nt){
            try {
                $salida = "";

                $sql = $this->db->connect()->prepare("SELECT
                                                        alm_transfercab.idreg,
                                                        alm_transfercab.cnumguia,
                                                        LPAD( alm_transfercab.idreg, 5, 0 ) AS nro_nota,
                                                        UPPER( origen.cdesalm ) AS almacen_origen,
                                                        UPPER( destino.cdesalm ) AS almacen_destino,
                                                        DATE_FORMAT(alm_transfercab.ftraslado,'%d/%m/%Y') AS ftraslado,
                                                        UPPER(tb_proyectos.cdesproy ) AS cc_origen,
                                                        YEAR(alm_transfercab.ftraslado) as anio
                                                    FROM
                                                        tb_almausu
                                                        INNER JOIN alm_transfercab ON tb_almausu.nalmacen = alm_transfercab.almdestino
                                                        INNER JOIN tb_almacen AS origen ON alm_transfercab.almorigen = origen.ncodalm
                                                        INNER JOIN tb_almacen AS destino ON alm_transfercab.almdestino = destino.ncodalm
                                                        INNER JOIN tb_proyectos ON alm_transfercab.idcc = tb_proyectos.nidreg 
                                                    WHERE
                                                        tb_almausu.nflgactivo = 1 
                                                        AND tb_almausu.id_cuser = :id
                                                    ORDER BY alm_transfercab.idreg DESC");
                $sql->execute(['id'=>$_SESSION["iduser"]]);
                $rowCount = $sql->rowCount();

                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()){
                        $salida.='<tr class="pointer" data-indice="'.$rs['idreg'].'">
                                    <td class="textoCentro">'.$rs['nro_nota'].'</td>
                                    <td class="textoCentro">'.$rs['cnumguia'].'</td>
                                    <td class="textoCentro">'.$rs['ftraslado'].'</td>
                                    <td class="pl20px">'.$rs['almacen_origen'].'</td>
                                    <td class="pl20px">'.$rs['almacen_destino'].'</td>
                                    <td class="pl20px">'.$rs['cc_origen'].'</td>
                                    <td class="textoCentro">'.$rs['anio'].'</td>
                                </tr>';
                    }

                    return $salida;
                }
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function consultarTransferenciaID($id){
            try {
                $sql=$this->db->connect()->prepare("SELECT
                                                    alm_transfercab.almorigen,
                                                    alm_transfercab.almdestino,
                                                    alm_transfercab.idcc,
                                                    UPPER( almacen_origen.cdesalm ) AS descripcion_origen,
                                                    UPPER( almacen_destino.cdesalm ) AS descripcion_destino,
                                                    almacen_origen.ncodalm AS codigo_almacen_origen,
                                                    almacen_destino.ncodalm AS codigo_almacen_destino,
                                                    alm_transfercab.idreg,
                                                    alm_transfercab.ftraslado,
                                                    alm_transfercab.cnumguia  
                                                FROM
                                                    alm_transfercab
                                                    INNER JOIN tb_almacen AS almacen_origen ON alm_transfercab.almorigen = almacen_origen.ncodalm
                                                    INNER JOIN tb_almacen AS almacen_destino ON alm_transfercab.almdestino = almacen_destino.ncodalm 
                                                WHERE
                                                    alm_transfercab.idreg = :idx 
                                                    AND alm_transfercab.nflgactivo = 1");
                $sql->execute(["idx"=>$id]);

                $docData = array();

                while($row=$sql->fetch(PDO::FETCH_ASSOC)){
                    $docData[] = $row;
                }
               
                $indice = $this->ultimoIndice() + 1;

                return array("cabecera"=>$docData,
                             "detalles"=>$this->detallesTransferencias($id,$docData[0]['almdestino']),
                             "numero"=>str_pad($indice,6,0,STR_PAD_LEFT),);
                
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function detallesTransferencias($indice,$destino){
            try {
                $salida = "";
                $item=1;

                $sql = $this->db->connect()->prepare("SELECT
                                                    alm_transferdet.iditem,
                                                    alm_transferdet.idtransfer,
                                                    alm_transferdet.iddetped,
                                                    alm_transferdet.idPedido,
                                                    alm_transferdet.idcprod,
                                                    alm_transferdet.idcostos,
                                                    alm_transferdet.ncanti,
                                                    alm_transferdet.cobserva,
                                                    cm_producto.ccodprod,
                                                    UPPER( cm_producto.cdesprod ) AS cdesprod,
                                                    tb_unimed.cabrevia,
                                                    tb_pedidocab.nrodoc,
                                                    tb_area.ncodarea, 
	                                                UPPER(tb_area.cdesarea) AS cdesarea 
                                                FROM
                                                    alm_transferdet
                                                    INNER JOIN cm_producto ON alm_transferdet.idcprod = cm_producto.id_cprod
                                                    INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed
                                                    INNER JOIN tb_pedidocab ON alm_transferdet.idPedido = tb_pedidocab.idreg
                                                    INNER JOIN tb_area ON tb_pedidocab.idarea = tb_area.ncodarea  
                                                WHERE
                                                    alm_transferdet.nflgactivo = 1 
                                                    AND alm_transferdet.idtransfer =:idx");
                $sql->execute(["idx"=>$indice]);

                $rowCount = $sql->rowCount();

                if ($rowCount > 0){
                    while ($rs= $sql->fetch()){
                        if ( $rs['ncanti'] > 0) {

                            //<i class="far fa-file-pdf"></i> 

                            $salida .='<tr class="pointer" data-idpet="'.$rs['iddetped'].'"
                                                        data-area="'.$rs['ncodarea'].'"
                                                        data-almacen = "'.$destino.'"
                                                        data-codprod = "'.$rs['idcprod'].'"
                                                        data-costos = "'.$rs['idcostos'].'">
                                        <td class="textoCentro">'.str_pad($item++,3,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro"> '.$rs['ccodprod'].'</td>
                                        <td class="pl20px">'.$rs['cdesprod'].'</td>
                                        <td class="textoCentro">'.$rs['cabrevia'].'</td>
                                        <td class="textoDerecha pr5px">'.$rs['ncanti'].'</td>
                                        <td class="textoDerecha"><input type="number" min="1" value="'.$rs['ncanti'].'"></td>
                                        <td><input type="text">'.$rs['cobserva'].'</td>
                                        <td class="pl20px">'.$rs['cdesarea'].'</td>
                                        <td><input type="date"></td>
                                        <td><input type="text"></td>
                                        <td><input type="text"></td>
                                        <td class="textoCentro">'.$rs['nrodoc'].'</td>
                                        <td class="textoCentro"></td>
                                        <td class="textoCentro">'.$rs['idtransfer'].'</td>
                                        <td class="textoCentro"><a href="'.$rs['iddetped'].'" ><i class="fas fa-paperclip"></i></a></td>
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

        public function subirAdjuntos($codigo,$adjuntos){
            $countfiles = count( $adjuntos );

            for($i=0;$i<$countfiles;$i++){
                try {
                    $file = "file-".$i;
                    $ext = explode('.',$adjuntos[$file]['name']);
                    $filename = uniqid().".".end($ext);
                    // Upload file
                    if (move_uploaded_file($adjuntos[$file]['tmp_name'],'public/documentos/almacen/adjuntos/'.$filename)){
                        $sql= $this->db->connect()->prepare("INSERT INTO lg_regdocumento 
                                                                    SET nidrefer=:cod,cmodulo=:mod,cdocumento=:doc,
                                                                        creferencia=:ref,nflgactivo=:est");
                        $sql->execute(["cod"=>$codigo,
                                        "mod"=>"GA",
                                        "ref"=>$filename,
                                        "doc"=>$adjuntos[$file]['name'],
                                        "est"=>1]);
                    }

                    

                } catch (PDOException $th) {
                    echo "Error: ".$th->getMessage();
                    return false;
                }
            }

            //$this->actualizarDetallePedido($codigo,$filename);
            return array("total_adjuntos"=>$countfiles);
        }

        private function actualizarDetallePedido($codigo,$filename){
            try {
                $sql = $this->db->connect()->prepare("UPDATE tb_pedidodet SET tb_pedidodet.docEspec = :archivo WHERE iditem = :item");
                $sql->execute(["archivo"=>$filename,"item"=>$codigo]);
            }catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function buscarGuiaTotal($guia){
            try {

                $docData = [];

                $sql = $this->db->connect()->prepare("SELECT
                                                            lg_guias.cnumguia,
                                                            lg_guias.corigen,
                                                            lg_guias.cdestino,
                                                            despachos.id_regalm AS salida,
                                                            madres.id_regalm AS madre,
                                                            YEAR ( lg_guias.freg ) AS anio,
                                                        IF
                                                            ( ISNULL( despachos.id_regalm ), madres.id_regalm, despachos.id_regalm ) AS iddespacho,
                                                        IF
                                                            ( ISNULL( despachos.cdesproy ), madres.cdesproy, despachos.cdesproy ) AS proyectoGuias,
                                                            madres.id_regalm AS indice_madre,
                                                            despachos.id_regalm AS indice_despacho,
                                                            lg_guias.freg,
                                                            DATE( lg_guias.freg ) AS fecha,
                                                        IF ( ISNULL(despachos.nReferido),'-',despachos.nReferido ) AS referido 
                                                        FROM
                                                            lg_guias
                                                            LEFT JOIN (
                                                            SELECT
                                                                alm_despachocab.id_regalm,
                                                                alm_despachocab.cnumguia,
                                                                alm_despachocab.nReferido,
                                                                CONCAT_WS( '', tb_proyectos.ccodproy, tb_proyectos.cdesproy ) AS cdesproy 
                                                            FROM
                                                                alm_despachocab
                                                                LEFT JOIN tb_proyectos ON tb_proyectos.nidreg = alm_despachocab.ncodpry 
                                                            ) AS despachos ON lg_guias.cnumguia = despachos.cnumguia
                                                            LEFT JOIN (
                                                            SELECT
                                                                alm_madrescab.id_regalm,
                                                                alm_madrescab.cnumguia,
                                                                CONCAT_WS( ' ', tb_proyectos.ccodproy, tb_proyectos.cdesproy ) AS cdesproy 
                                                            FROM
                                                                alm_madrescab
                                                                LEFT JOIN tb_proyectos ON tb_proyectos.nidreg = alm_madrescab.ncodcos 
                                                            ) AS madres ON lg_guias.cnumguia = madres.cnumguia 
                                                        WHERE
                                                            lg_guias.cnumguia = :guia");
                                                                        
                
                $sql->execute(["guia"=>$guia]);

                $docData = array();

                while($row=$sql->fetch(PDO::FETCH_ASSOC)){
                    $docData[] = $row;
                }
                
                return array("cabecera"=>$docData);
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function numeroRegistros(){
            try {
                $sql = $this->db->connect()->query("SELECT COUNT(*) AS records FROM alm_cabexist WHERE ISNULL(alm_cabexist.flgActivo)");
                $sql->execute();

                $records = $sql->fetchAll();

                return $records[0]['records'];

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }
    }
?>