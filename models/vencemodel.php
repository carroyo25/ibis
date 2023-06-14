<?php
    class VenceModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function mostrarvencimento($cc,$codigo) {
            $codigo = '%';
            $salida = "";

            $sql = $this->db->connect()->prepare("SELECT
                                                alm_existencia.codprod,
                                                DATE_FORMAT(date_add(alm_existencia.vence, interval 190 day),'%d/%m/%Y') AS vence,
                                                UPPER( cm_producto.cdesprod ) AS descripcion,
                                                cm_producto.ccodprod,
                                                lg_ordencab.cmes,
                                                lg_ordencab.cnumero,
                                                lg_ordencab.ffechadoc,
                                                lg_ordencab.ncodcos,
                                                tb_unimed.cabrevia,
                                                alm_existencia.cant_ingr  
                                            FROM
                                                alm_existencia
                                                INNER JOIN cm_producto ON alm_existencia.codprod = cm_producto.id_cprod
                                                INNER JOIN lg_ordencab ON alm_existencia.nropedido = lg_ordencab.id_regmov
                                                INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed  
                                            WHERE
                                                alm_existencia.vence <> '' 
                                                AND lg_ordencab.ncodcos =:costo
                                                AND cm_producto.ccodprod LIKE :codigo
                                            ORDER BY
                                                alm_existencia.vence ASC");
            $sql->execute(['costo'=>$cc,'codigo'=>$codigo]);
            $rowcount = $sql->rowcount();
            $item = 1;

            if ($rowcount > 0) {
                while ($rs = $sql->fetch()) {
                    $salida .='<tr class="pointer">
                                    <td>'.str_pad($item++,3,0,STR_PAD_LEFT).'</td>
                                    <td class="textocentro">'.$rs['ccodprod'].'</td>
                                    <td class="pl20px">'.$rs['descripcion'].'</td>
                                    <td class="textoCentro">'.$rs['cabrevia'].'</td>
                                    <td class="textoDerecha">'.$rs['cant_ingr'].'</td>
                                    <td class="textoCentro">'.str_pad($rs['cnumero'],6,0,STR_PAD_LEFT).'</td>
                                    <td></td>
                                    <td class="textoCentro">'.$rs['vence'].'</td>
                                    <td></td>
                                    <td></td>
                                </tr>';
                }
            }

            return $salida;

        }
    }
?>