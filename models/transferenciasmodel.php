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
    }
?>