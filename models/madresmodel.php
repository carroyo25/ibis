<?php
    class MadresModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function importarGuias($cc,$guia){
            try {
                $salida = "";

                $g = $guia == "" ? "%": "%".$guia."%";

                $sql = $this->db->connect()->prepare("SELECT
                                                lg_guias.cnumguia,
                                                alm_despachocab.ncodpry,
                                                tb_proyectos.ccodproy,
                                                UPPER(tb_proyectos.cdesproy) AS cdesproy,
	                                            lg_guias.id_regalm,
                                                DATE_FORMAT(alm_despachocab.ffecdoc,'%d/%m/%Y') AS ffecdoc  
                                            FROM
                                                lg_guias
                                                LEFT JOIN alm_despachocab ON lg_guias.id_regalm = alm_despachocab.id_regalm
                                                INNER JOIN tb_proyectos ON alm_despachocab.ncodpry = tb_proyectos.nidreg 
                                            WHERE
                                                lg_guias.cnumguia <> ''
                                                AND alm_despachocab.nflgactivo = 1 
                                                AND lg_guias.cnumguia LIKE :guia 
                                                AND alm_despachocab.ncodpry LIKE :costos 
                                                AND lg_guias.flgmadre = 0");
                
                $sql->execute(["guia"=>$g,"costos"=>$cc]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()) {
                        $salida .='<tr data-despacho="'.$rs['id_regalm'].'">
                                        <td class="textoCentro">'.$rs['cnumguia'].'</td>
                                        <td class="textoCentro">'.$rs['ffecdoc'].'</td>
                                        <td class="pl10px">'.$rs['cdesproy'].'</td>
                                        <td class="textoCentro"><button>Seleccionar</button></td>
                                    </tr>';
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function importarItemsDespacho($idx){
            try {
                $salida = "";

                $sql = $this->db->connect()->prepare("SELECT
                                            UPPER(cm_producto.ccodprod) AS cccodprod,
                                            UPPER(cm_producto.cdesprod) AS cdesprod,
                                            alm_despachodet.ncantidad,
                                            tb_unimed.cabrevia,
	                                        alm_despachodet.id_regalm,
                                            alm_despachodet.niddeta  
                                        FROM
                                            alm_despachodet
                                            INNER JOIN cm_producto ON alm_despachodet.id_cprod = cm_producto.id_cprod
                                            INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed 
                                        WHERE
                                            alm_despachodet.id_regalm =:id 
                                            AND alm_despachodet.nflgactivo = 1");
                
                $sql->execute(["id"=>$idx]);
                $rowCount = $sql->rowCount();

                $item = 0;

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()) {
                        $salida .='<tr data-despacho="'.$rs['id_regalm'].'" data-itemdespacho="'.$rs['niddeta'].'">
                                        <td class="textoCentro">'.str_pad($item++,3,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.$rs['cccodprod'].'</td>
                                        <td class="pl10px">'.$rs['cdesprod'].'</td>
                                        <td class="textoCentro">'.$rs['cabrevia'].'</td>
                                        <td class="textoDerecha">'.$rs['ncantidad'].'</td>
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