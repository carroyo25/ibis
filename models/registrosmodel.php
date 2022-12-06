<?php
    class RegistrosModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarDespachos(){
            $salida = "";
            try {
                $sql = $this->db->connect()->prepare("SELECT  alm_despachocab.id_regalm,
                                                        YEAR(ffecdoc) AS anio,
                                                        alm_despachocab.nnronota,
                                                        alm_despachocab.nReferido,
                                                        DATE_FORMAT( alm_despachocab.ffecdoc, '%d/%m/%Y' ) AS ffecdoc,
                                                        UPPER( origen.cdesalm ) AS origen,
                                                        UPPER( destino.cdesalm ) AS destino,
                                                        UPPER( CONCAT_WS( ' ', tb_proyectos.ccodproy, tb_proyectos.cdesproy ) ) AS costos,
                                                        tb_parametros.cdescripcion,
                                                        tb_parametros.cabrevia,
                                                        alm_despachocab.nEstadoDoc,
                                                        alm_despachocab.cnumguia,
                                                        alm_despachocab.nReferido 
                                                    FROM
                                                        tb_costusu
                                                        INNER JOIN alm_despachocab ON tb_costusu.ncodproy = alm_despachocab.ncodpry
                                                        INNER JOIN tb_almacen AS origen ON alm_despachocab.ncodalm1 = origen.ncodalm
                                                        INNER JOIN tb_almacen AS destino ON alm_despachocab.ncodalm2 = destino.ncodalm
                                                        INNER JOIN tb_proyectos ON alm_despachocab.ncodpry = tb_proyectos.nidreg
                                                        INNER JOIN tb_parametros ON alm_despachocab.nEstadoDoc = tb_parametros.nidreg 
                                                    WHERE
                                                        tb_costusu.id_cuser = :usr
                                                        AND tb_costusu.nflgactivo = 1 
                                                        AND alm_despachocab.nEstadoDoc = 62 
                                                    ORDER BY
                                                        alm_despachocab.ffecdoc ASC");
                $sql->execute(["usr"=>$_SESSION['iduser']]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()){
                        $salida .='<tr data-indice="'.$rs['id_regalm'].'" class="pointer">                                        
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
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function importarDespacho($indice){
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

                    $detalles = $this->detallesDespacho($indice);
                }

                return array("cabecera"=>$docData,
                             "numero"=>$this->ultimoIndice(),
                             "detalles"=>$detalles);
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function detallesDespacho($indice){
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
                                                        ibis.tb_pedidocab.idcostos 
                                                    FROM
                                                        ibis.alm_despachodet
                                                        INNER JOIN ibis.cm_producto ON alm_despachodet.id_cprod = cm_producto.id_cprod
                                                        INNER JOIN ibis.tb_unimed ON cm_producto.nund = tb_unimed.ncodmed
                                                        INNER JOIN ibis.tb_pedidodet ON alm_despachodet.niddetaPed = tb_pedidodet.iditem
                                                        INNER JOIN ibis.tb_pedidocab ON tb_pedidodet.idpedido = tb_pedidocab.idreg
                                                        INNER JOIN ibis.tb_area ON tb_pedidocab.idarea = tb_area.ncodarea
                                                        INNER JOIN rrhh.tabla_aquarius ON ibis.tb_pedidocab.idsolicita = rrhh.tabla_aquarius.internal 
                                                    WHERE
                                                        alm_despachodet.id_regalm = :indice");
                $sql->execute(["indice"=>$indice]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0){
                    while ($rs= $sql->fetch()){
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
                                        <td class="textoCentro">'.$rs['pedido'].'</td>
                                        <td class="textoCentro">'.$rs['orden'].'</td>
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
            $indice = $indice  + 1;
            return str_pad($indice,6,0,STR_PAD_LEFT);
        }

        public function grabarRegistros($cabecera,$detalles) {
            try {
                $indice = $this->ultimoIndice();
                $estado = false;
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

                if ($rowCount > 0) {
                    $this->grabarDetalllesIngreso($indice,$detalles,$cabecera['codigo_despacho'],$cabecera['cnumguia']);
                    return array("estado"=>true);
                }else{
                    return array("estado"=>false);
                }
                
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function listarIngresos() {
            try {
                $salida = "";
                $item = 1;
                $sql = $this->db->connect()->prepare("SELECT
                                                        alm_cabexist.idcostos,
                                                        alm_cabexist.iddespacho,
                                                        alm_cabexist.ffechadoc,
                                                        alm_cabexist.idautoriza,
                                                        alm_cabexist.idrecepciona,
                                                        alm_cabexist.numguia,
                                                        alm_cabexist.nreferido,
                                                        alm_cabexist.ncodalm1,
                                                        alm_cabexist.ncodalm2,
                                                        UPPER( origen.cdesalm ) AS origen,
                                                        UPPER( destino.cdesalm ) AS destino,
                                                        CONCAT_WS(' ',tb_proyectos.ccodproy,tb_proyectos.cdesproy) AS costos
                                                    FROM
                                                        tb_costusu
                                                        INNER JOIN alm_cabexist ON tb_costusu.ncodproy = alm_cabexist.idcostos
                                                        INNER JOIN tb_almacen AS origen ON alm_cabexist.ncodalm1 = origen.ncodalm
                                                        INNER JOIN tb_almacen AS destino ON alm_cabexist.ncodalm2 = destino.ncodalm
                                                        INNER JOIN tb_proyectos ON alm_cabexist.idcostos = tb_proyectos.nidreg 
                                                    WHERE
                                                        tb_costusu.id_cuser = :usr 
                                                        AND tb_costusu.nflgactivo = 1");
                $sql->execute(["usr"=>$_SESSION["iduser"]]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()){
                        $salida.='<tr class="pointer">
                                    <td class="textoCentro">'.str_pad($item++,6,0,STR_PAD_LEFT).'</td>
                                    <td class="textoCentro">'.$rs['ffechadoc'].'</td>
                                    <td class="pl20px">'.$rs['origen'].'</td>
                                    <td class="pl20px">'.$rs['destino'].'</td>
                                    <td class="pl20px">'.$rs['costos'].'</td>
                                    <td>'.$rs['numguia'].'</td>
                                    <td>'.$rs['nreferido'].'</td>
                                </tr>';
                    }

                    return $salida;
                }
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        

        private function grabarDetalllesIngreso($indice,$detalles,$despacho,$guia){
            try {
                $datos = json_decode($detalles);
                $nreg = count($datos);

        
                $datos = json_decode($detalles);
                $nreg = count($datos);

                for ($i=0; $i < $nreg; $i++) {
                    $sql = $this->db->connect()->prepare("INSERT INTO alm_existencia 
                                                            SET idalm=:almacen,
                                                                idregistro=:indice,
                                                                iddespacho=:despacho,
                                                                codprod=:item,
                                                                tipo=1,
                                                                cant_ingr=:cantidad,
                                                                nguia=:guia,
                                                                observaciones=:observ,
                                                                ubicacion=:ubica,
                                                                area=:areapedido");
                    $sql->execute(["almacen" =>$datos[$i]->almacen, 
                                    "indice" =>$indice,
                                    "despacho"=>$despacho,
                                    "item"=>$datos[$i]->codprod,
                                    "cantidad"=>$datos[$i]->cantrecep,
                                    "guia"=>$guia,
                                    "observ"=>$datos[$i]->observac,
                                    "ubica"=>$datos[$i]->ubica,
                                    "areapedido"=>$datos[$i]->area]);
                }

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }
    }
?>