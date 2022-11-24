<?php
    class SalidaModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarNotasDespacho(){
            $salida = "";
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                        alm_despachocab.id_regalm,
                                                        alm_despachocab.cmes,
                                                        DATE_FORMAT(
                                                            alm_despachocab.ffecdoc,
                                                            '%d/%m/%Y'
                                                        ) AS ffecdoc,
                                                        YEAR(ffecdoc) AS anio,
                                                        alm_despachocab.ncodpry,
                                                        UPPER(origen.cdesalm) AS origen,
                                                        alm_despachocab.nEstadoDoc,
                                                        alm_despachocab.cnumguia,
                                                        UPPER(destino.cdesalm) AS destino,
                                                        UPPER(
                                                            CONCAT_WS(
                                                                ' ',
                                                                tb_proyectos.cdesproy,
                                                                tb_proyectos.ccodproy
                                                            )
                                                        ) AS costos,
                                                        tb_parametros.cdescripcion,
                                                        tb_parametros.cabrevia
                                                    FROM
                                                        tb_costusu
                                                    INNER JOIN alm_despachocab ON tb_costusu.ncodproy = alm_despachocab.ncodpry
                                                    INNER JOIN tb_almacen AS origen ON alm_despachocab.ncodalm1 = origen.ncodalm
                                                    INNER JOIN tb_almacen AS destino ON alm_despachocab.ncodalm2 = destino.ncodalm
                                                    INNER JOIN tb_proyectos ON alm_despachocab.ncodpry = tb_proyectos.nidreg
                                                    INNER JOIN tb_parametros ON alm_despachocab.nEstadoDoc = tb_parametros.nidreg
                                                    WHERE
                                                        tb_costusu.nflgactivo = 1
                                                    AND tb_costusu.id_cuser = :usr
                                                    AND alm_despachocab.nEstadoDoc = 62
                                                    ORDER BY alm_despachocab.ffecdoc ASC");
                $sql->execute(["usr"=>$_SESSION['iduser']]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()){
                        $salida .='<tr data-indice="'.$rs['id_regalm'].'" class="pointer">
                                        <td class="textoCentro">'.str_pad($rs['id_regalm'],6,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.$rs['ffecdoc'].'</td>
                                        <td class="textoCentro">'.$rs['origen'].'</td>
                                        <td class="pl20px">'.$rs['destino'].'</td>
                                        <td class="pl20px">'.$rs['costos'].'</td>
                                        <td class="textoCentro">'.$rs['anio'].'</td>
                                        <td class="textoCentro">'.$rs['cnumguia'].'</td>
                                        <td class="textoCentro '.$rs['cabrevia'].'">'.$rs['cdescripcion'].'</td>
                                    </tr>';
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        //esto se usara para todos los documentos
        private function ultimoIndice(){
            $indice = $this->model->lastInsertId("SELECT MAX(id_regalm) AS id FROM alm_despachocab"); 
            $indice = gettype($indice) == "NULL" ? 1 : $indice;

            echo str_pad($indice,6,0,STR_PAD_LEFT);
        }

        public function listarIngresos(){
            try {
                $salida = "";
                $sql = $this->db->connect()->prepare("SELECT
                                                        alm_recepcab.id_regalm,
                                                        alm_recepcab.nnronota,
                                                        alm_recepcab.cnumguia,
                                                        tb_proyectos.nidreg,
                                                        alm_recepcab.idref_pedi AS pedido,
                                                         alm_recepcab.idref_abas AS orden,
                                                        CONCAT_WS(' ',tb_proyectos.ccodproy,tb_proyectos.cdesproy) AS proyecto,
                                                        tb_parametros.cdescripcion,
                                                        UPPER( tb_almacen.cdesalm ) AS almacen,
                                                        UPPER( tb_area.cdesarea ) AS area,
                                                        DATE_FORMAT(alm_recepcab.ffecdoc,'%d/%m/%Y') AS fecha  
                                                    FROM
                                                        tb_costusu
                                                        INNER JOIN alm_recepcab ON tb_costusu.ncodproy = alm_recepcab.ncodpry
                                                        INNER JOIN tb_proyectos ON alm_recepcab.ncodpry = tb_proyectos.nidreg
                                                        INNER JOIN tb_parametros ON alm_recepcab.ncodmov = tb_parametros.nidreg
                                                        INNER JOIN tb_almacen ON alm_recepcab.ncodalm1 = tb_almacen.ncodalm
                                                        INNER JOIN tb_area ON alm_recepcab.ncodarea = tb_area.ncodarea 
                                                    WHERE
                                                        tb_costusu.id_cuser = :user 
                                                        AND tb_costusu.nflgactivo = 1 
                                                        AND alm_recepcab.nEstadoDoc = 60
                                                    ORDER BY tb_proyectos.ccodproy");
                $sql->execute(["user"=>$_SESSION['iduser']]);
                $rowCount = $sql->rowCount();

                $item = 1;

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()) {
                        $salida .= '<tr class="pointer" data-pedido="'.$rs['pedido'].'"
                                                        data-orden="'.$rs['orden'].'"
                                                        data-ingreso="'.$rs['id_regalm'].'"
                                                        data-costos="'.$rs['nidreg'].'">
                                        <td class="textoCentro"><input type="checkbox"></td>
                                        <td class="textoCentro">'.$rs['fecha'].'</td>
                                        <td class="pl20px">'.$rs['proyecto'].'</td>
                                        <td class="textoCentro">'.str_pad($rs['pedido'],6,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.str_pad($rs['orden'],6,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.$rs['nnronota'].'</td>
                                        <td class="pl20px">'.$rs['area'].'</td>
                                        <td class="pl20px">'.$rs['cdescripcion'].'</td>
                                        <td class="pl20px">'.$rs['almacen'].'</td>
                                    </tr>';
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }
        
        public function filtrarIngresos($id) {
            $salida = "";
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                        alm_recepcab.id_regalm,
                                                        alm_recepcab.nnronota,
                                                        alm_recepcab.cnumguia,
                                                        alm_recepcab.idref_pedi AS pedido,
                                                        alm_recepcab.idref_abas AS orden,
                                                        tb_proyectos.nidreg,
                                                        CONCAT_WS(' ',tb_proyectos.ccodproy,tb_proyectos.cdesproy) AS proyecto,
                                                        tb_parametros.cdescripcion,
                                                        UPPER( tb_almacen.cdesalm ) AS almacen,
                                                        UPPER( tb_area.cdesarea ) AS area,
                                                        DATE_FORMAT(alm_recepcab.ffecdoc,'%d/%m/%Y') AS fecha  
                                                    FROM
                                                        tb_costusu
                                                        INNER JOIN alm_recepcab ON tb_costusu.ncodproy = alm_recepcab.ncodpry
                                                        INNER JOIN tb_proyectos ON alm_recepcab.ncodpry = tb_proyectos.nidreg
                                                        INNER JOIN tb_parametros ON alm_recepcab.ncodmov = tb_parametros.nidreg
                                                        INNER JOIN tb_almacen ON alm_recepcab.ncodalm1 = tb_almacen.ncodalm
                                                        INNER JOIN tb_area ON alm_recepcab.ncodarea = tb_area.ncodarea 
                                                    WHERE
                                                        tb_costusu.id_cuser = :usr 
                                                        AND tb_costusu.nflgactivo = 1 
                                                        AND alm_recepcab.nEstadoDoc = 62
                                                        AND alm_recepcab.id_regalm = :id
                                                    ORDER BY tb_proyectos.ccodproy");
                $sql->execute(["usr"=>$_SESSION['iduser'],'id'=>$id]);
                $rowCount = $sql->rowCount();
                $item = 1;

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()){
                        $salida .= '<tr class="pointer" data-pedido="'.$rs['pedido'].'"
                                                       data-orden="'.$rs['orden'].'"
                                                       data-ingreso="'.$rs['ingreso'].'"
                                                       data-costos="'.$rs['nidreg'].'">
                                        <td class="textoCentro"><input type="checkbox"></td>
                                        <td class="textoCentro">'.$rs['fecha'].'</td>
                                        <td class="pl20px">'.$rs['proyecto'].'</td>
                                        <td class="textoCentro">'.str_pad($rs['pedido'],6,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.str_pad($rs['orden'],6,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.$rs['nnronota'].'</td>
                                        <td class="pl20px">'.$rs['area'].'</td>
                                        <td class="pl20px">'.$rs['cdescripcion'].'</td>
                                        <td class="pl20px">'.$rs['almacen'].'</td>
                                    </tr>';
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function importarItems($data){
            $indices = implode($data);
            $indices = str_replace("[","(",$indices);
            $indices = str_replace("]",")",$indices);

            $salida = "";
            $qry = "SELECT
                        alm_recepdet.niddeta,
                        cm_producto.ccodprod,
                        UPPER(cm_producto.cdesprod) AS descripcion,
                        tb_unimed.cabrevia AS unidad,
                        tb_pedidodet.observaciones,
                        tb_pedidodet.iditem AS iditem,
                        LPAD(tb_pedidocab.nrodoc, 6, 0) AS pedido,
                        REPLACE (
                            FORMAT(lg_ordendet.ncanti, 2),
                            '',
                            ','
                        ) AS cantidad,
                        tb_almacen.cdesalm,
                        alm_recepcab.nnronota AS ingreso,
                        lg_ordencab.id_regmov AS idorden,
                        LPAD(lg_ordencab.cnumero, 6, 0) AS orden
                    FROM
                        alm_recepdet
                    INNER JOIN cm_producto ON alm_recepdet.id_cprod = cm_producto.id_cprod
                    INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed
                    INNER JOIN tb_pedidodet ON alm_recepdet.niddetaPed = tb_pedidodet.iditem
                    INNER JOIN tb_pedidocab ON tb_pedidodet.idpedido = tb_pedidocab.idreg
                    INNER JOIN lg_ordendet ON alm_recepdet.niddetaOrd = lg_ordendet.nitemord
                    INNER JOIN tb_almacen ON alm_recepdet.ncodalm1 = tb_almacen.ncodalm
                    INNER JOIN alm_recepcab ON alm_recepdet.id_regalm = alm_recepcab.id_regalm
                    INNER JOIN lg_ordencab ON tb_pedidocab.idorden = lg_ordencab.id_regmov
                    WHERE
                        alm_recepdet.id_regalm IN $indices";

            try {
                $sql = $this->db->connect()->query($qry);
                $sql->execute();
                $rowCount = $sql->rowCount();
                $item=1;

                if ($rowCount > 0){
                    while ($rs = $sql->fetch()) {
                        $salida .= '<tr class="pointer">
                                    <td class="textoCentro"><a href="'.$rs['niddeta'].'" data-accion="deleteItem" class="eliminarItem"><i class="fas fa-minus"></i></a></td>
                                    <td class="textoCentro">'.str_pad($item++,3,0,STR_PAD_LEFT).'</td>
                                    <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                    <td class="pl20px">'.$rs['descripcion'].'</td>
                                    <td class="textoCentro">'.$rs['unidad'].'</td>
                                    <td class="textoDerecha pr5px">'.$rs['cantidad'].'</td>
                                    <td><input type="number"></td>
                                    <td><input type="text"></td>
                                    <td class="textoCentro">'.$rs['pedido'].'</td>
                                    <td class="textoCentro">'.$rs['orden'].'</td>
                                    <td class="textoCentro">'.$rs['ingreso'].'</td>
                                </tr>';

                        
                    }
                    
                }

                return array("items" => $salida);
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }
    } 
?>