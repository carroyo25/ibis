<?php
    class ValItemModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function consultarItems($parametros) {
            try {
                $costos     = $parametros['costosSearch'] == -1 ? "%" : $parametros['costosSearch'];
                $codigo     = $parametros['codigoBusqueda'] == "" ? "%" : "%".$parametros['codigoBusqueda']."%";
                $concepto   = $parametros['descripcionSearch'] == "" ? "%" : "%".$parametros['descripcionSearch']."%";

                $salida = "";

                $sql = $this->db->connect()->prepare("SELECT
                                                        lg_ordendet.nunitario,
                                                        lg_ordendet.ncanti,
                                                        lg_ordencab.ffechadoc,
                                                        LPAD( lg_ordencab.cnumero, 6, 0 ) AS orden,
                                                        lg_ordencab.ntcambio,
                                                        cm_producto.ccodprod,
                                                        UPPER( CONCAT_WS( ' ', cm_producto.cdesprod, tb_pedidodet.observaciones ) ) AS descripcion,
                                                        tb_unimed.cabrevia AS unidad,
                                                        tb_parametros.cabrevia AS moneda,
                                                        lg_ordendet.ncodcos,
                                                        lg_ordencab.ncodmon 
                                                    FROM
                                                        lg_ordendet
                                                        INNER JOIN lg_ordencab ON lg_ordendet.id_regmov = lg_ordencab.id_regmov
                                                        INNER JOIN cm_producto ON lg_ordendet.id_cprod = cm_producto.id_cprod
                                                        INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed
                                                        INNER JOIN tb_pedidodet ON lg_ordendet.niddeta = tb_pedidodet.iditem
                                                        INNER JOIN tb_parametros ON lg_ordencab.ncodmon = tb_parametros.nidreg 
                                                    WHERE
                                                        lg_ordencab.ntipmov = 37 
                                                        AND lg_ordendet.ncodcos LIKE :costos 
                                                        AND cm_producto.ccodprod LIKE :codigo 
                                                    ORDER BY
                                                        lg_ordencab.id_regmov ASC");
                $sql->execute(["costos"=>$costos,"codigo"=>$codigo]);
                $rowCount = $sql->rowcount();
                $item = 1;


                $total_soles = 0;
                $total_dolares = 0;

                if ($rowCount > 0){
                    while($rs = $sql->fetch()){

                        if ($rs['ncodmon'] == 20) {
                            $precio_soles =  $rs['nunitario'] ;
                            $precio_dolares = $rs['nunitario'] / $rs['ntcambio'];
                        }else {
                            $precio_soles =   $rs['nunitario'] * $rs['ntcambio'];
                            $precio_dolares =  $rs['nunitario'] ;
                        }

                        $salida .='<tr class="pointer">
                                        <td class="textoCentro">'.str_pad($item++,3,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                        <td class="pl20px">'.$rs['descripcion'].'</td>
                                        <td class="textoCentro">'.$rs['unidad'].'</td>
                                        <td class="textoCentro">'.$rs['moneda'].'</td>
                                        <td class="textoDerecha">'.$rs['ntcambio'].'</td>
                                        <td class="textoCentro">'.$rs['ffechadoc'].'</td>
                                        <td class="textoCentro">'.$rs['orden'].'</td>
                                        <td class="textoDerecha">'.$rs['ncanti'].'</td>
                                        <td class="textoDerecha">'.number_format($precio_soles,2).'</td>
                                        <td class="textoDerecha">'.number_format($precio_dolares,2).'</td>
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