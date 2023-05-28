<?php
    class EvalrepoModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarEvaluaciones(){
            try {
                 $salida = "";
                 $sql = $this->db->connect()->query("SELECT
                                                            lg_ordencab.id_regmov,
                                                            UPPER(cm_entidad.crazonsoc) AS proveedor,
                                                            tb_proyectos.ccodproy,
                                                            UPPER( tb_pedidocab.concepto ) AS concepto,
                                                            lg_ordencab.ffechadoc,
	                                                        lg_ordencab.cnumero, 
                                                        IF ( ISNULL( c01.npuntaje ), 5, c01.npuntaje ) AS criterio01,
                                                        IF ( ISNULL( c02.npuntaje ), 5, c02.npuntaje ) AS criterio02,
                                                        IF ( ISNULL( c03.npuntaje ), 5, c03.npuntaje ) AS criterio03,
                                                        IF ( ISNULL( c13.npuntaje ), 5, c13.npuntaje ) AS criterio13 
                                                        FROM
                                                            lg_ordencab
                                                            LEFT JOIN ( SELECT tb_califica.npuntaje, tb_califica.idorden FROM tb_califica WHERE tb_califica.idcriterio = 1 ) AS c01 ON c01.idorden = lg_ordencab.id_regmov
                                                            LEFT JOIN ( SELECT tb_califica.npuntaje, tb_califica.idorden FROM tb_califica WHERE tb_califica.idcriterio = 2 ) AS c02 ON c02.idorden = lg_ordencab.id_regmov
                                                            LEFT JOIN ( SELECT tb_califica.npuntaje, tb_califica.idorden FROM tb_califica WHERE tb_califica.idcriterio = 3 ) AS c03 ON c03.idorden = lg_ordencab.id_regmov
                                                            LEFT JOIN ( SELECT tb_califica.npuntaje, tb_califica.idorden FROM tb_califica WHERE tb_califica.idcriterio = 13 ) AS c13 ON c13.idorden = lg_ordencab.id_regmov
                                                            INNER JOIN cm_entidad ON lg_ordencab.id_centi = cm_entidad.id_centi
                                                            INNER JOIN tb_proyectos ON lg_ordencab.ncodcos = tb_proyectos.nidreg
                                                            INNER JOIN tb_pedidocab ON lg_ordencab.id_refpedi = tb_pedidocab.idreg 
                                                        ORDER BY
                                                            lg_ordencab.id_regmov DESC");
                 $sql->execute();
                 $rowCount = $sql->rowCount();
 
                 if ($rowCount > 0){
                     while ($rs = $sql->fetch()) {
 
                        $salida .='<tr class="pointer" data-indice="'.$rs['id_regmov'].'"">
                                        <td class="textoCentro">'.str_pad($rs['cnumero'],4,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.date("d/m/Y", strtotime($rs['ffechadoc'])).'</td>
                                        <td class="pl20px">'.$rs['concepto'].'</td>
                                        <td class="pl20px">'.$rs['ccodproy'].'</td>
                                        <td class="pl20px">'.$rs['proveedor'].'</td>
                                        <td class="textoCentro">5</td>
                                        <td class="textoCentro">5</td>
                                        <td class="textoCentro">5</td>
                                        <td class="textoCentro">5</td>
                                        <td class="textoCentro">5</td>
                                        <td class="textoCentro">5</td>
                                        <td class="textoCentro">5</td>
                                        <td class="textoCentro">5</td>
                                        <td class="textoCentro">5</td>
                                        <td class="textoCentro">5</td>
                                        <td class="textoCentro">5</td>
                                        <td class="textoCentro">5</td>
                                        <td class="textoCentro">5</td>
                                        <td class="textoCentro">5</td>
                                        <td class="textoCentro">5</td>
                                        <td class="textoCentro">5</td>
                                        <td class="textoCentro">5</td>
                                        <td class="textoCentro">5</td>
                                        <td class="textoCentro"><strong>85</strong></td>
                                     </tr>';
                     }
                 }
 
                 return $salida;                    
            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }

        private function calulaPuntaje($orden){

        }

        public function reportEval($detalles){
            
        }
    }
?>