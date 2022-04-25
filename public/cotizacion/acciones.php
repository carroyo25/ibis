<?php
    require_once("connect.php");

    function parametros($pdo,$clase){
        try {
            $sql = "SELECT
                        tb_parametros.nidreg,
                        tb_parametros.cdescripcion,
                        tb_parametros.cabrevia 
                    FROM
                        tb_parametros 
                    WHERE
                        tb_parametros.cclase = ? 
                        AND tb_parametros.ccod <> '00'";
            $statement = $pdo->prepare($sql);
            $statement -> execute(array($clase));
            $result = $statement ->fetchAll();
            $rowaffect = $statement->rowCount($sql);
            $salida = '<option value="-1" class="oculto">Elija opcion</option>';

            if ($rowaffect > 0) {
                foreach ($result as $rs) {
                    $salida .= '<option value="'.$rs['nidreg'].'">'.$rs['cdescripcion'].'</option>';
                }
            }

            return $salida;
        } catch (PDOException $th) {
            echo $th->getMessage();
            return false;
        }
    }

    function nombre_entidad($pdo,$ruc){
        try {
            $sql = "SELECT
                         id_centi,UPPER(crazonsoc) AS nombre
                    FROM
                        cm_entidad 
                    WHERE
                        cm_entidad.cnumdoc = ? 
                        AND cm_entidad.nflgactivo = 7";
            $statement = $pdo->prepare($sql);
            $statement -> execute(array($ruc));
            $result = $statement ->fetchAll();
            $salida = $result[0]['nombre'];

            return $salida;
        } catch (PDOException $th) {
            echo $th->getMessage();
            return false;
        }
    }

    function itemsPedido($pdo,$pedido){
        try {
            $salida ="";
            $sql = "SELECT
                        lg_cotizadet.nitemcot,
                        lg_cotizadet.id_regmov,
                        lg_cotizadet.niddet,
                        lg_cotizadet.ncodmed,
                        lg_cotizadet.id_cprod,
                        lg_cotizadet.cantcoti,
                        lg_cotizadet.ccodcot,
                        cm_producto.ccodprod,
                        UPPER(cm_producto.cdesprod) AS cdesprod,
                        tb_unimed.cabrevia 
                    FROM
                        lg_cotizadet
                        INNER JOIN cm_producto ON lg_cotizadet.id_cprod = cm_producto.id_cprod
                        INNER JOIN tb_unimed ON lg_cotizadet.ncodmed = tb_unimed.ncodmed 
                    WHERE
                        lg_cotizadet.id_regmov = ?";
            $statement = $pdo->prepare($sql);
            $statement -> execute(array($pedido));
            $result = $statement ->fetchAll();
            $rowaffect = $statement->rowCount($sql);
            $filas = 1;

            if ($rowaffect > 0) {
                foreach ($result as $rs) {
                    $salida .= '<tr>
                                    <td class="textoCentro">'.str_pad($filas++,3,0,STR_PAD_LEFT).'</td>
                                    <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                    <td class="pl20px">'.$rs['cdesprod'].'</td>
                                    <td class="textoCentro">'.$rs['cabrevia'].'</td>
                                    <td class="textoDerecha pr5px">'.number_format($rs['cantcoti'], 2, '.', ',').'</td>
                                    <td>
                                        <input type="number" 
                                            step="any" 
                                            placeholder="0.00" 
                                            onchange="(function(el){el.value=parseFloat(el.value).toFixed(2);})(this)"
                                            class="textoDerecha pr5px w100por precio">
                                    </td>
                                    <td class="textoDerecha pr5px"></td>
                                    <td></td>
                                    <td><input type="text" class="w100por"></td>
                                    <td><input type="date" class="w90por"></td>
                                    <td class="textoCentro"><a href="'.$rs['nitemcot'].'"><i class="fas fa-paperclip"></i></a></td>
                                </tr>';
                }
            }
            
            return $salida;
        } catch (PDOException $th) {
            echo $th->getMessage();
            return false;
        }
    }
?>