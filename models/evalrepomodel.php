<?php
    class EvalrepoModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarEvaluaciones(){
            try {
                 $salida = "";
                 $sql = $this->db->connect()->prepare("SELECT
                                                            lg_ordencab.id_regmov,
                                                            UPPER(cm_entidad.crazonsoc) AS proveedor,
                                                            tb_proyectos.ccodproy,
                                                            UPPER( tb_pedidocab.concepto ) AS concepto,
                                                            lg_ordencab.ffechadoc,
	                                                        lg_ordencab.cnumero, 
                                                        IF ( ISNULL( c01.npuntaje ), 4, c01.npuntaje ) AS criterio01,
                                                        IF ( ISNULL( c02.npuntaje ), 4, c02.npuntaje ) AS criterio02,
                                                        IF ( ISNULL( c03.npuntaje ), 4, c03.npuntaje ) AS criterio03,
                                                        IF ( ISNULL( c04.npuntaje ), 4, c04.npuntaje ) AS criterio04,
                                                        IF ( ISNULL( c05.npuntaje ), 4, c05.npuntaje ) AS criterio05,
                                                        IF ( ISNULL( c06.npuntaje ), 4, c06.npuntaje ) AS criterio06,
                                                        IF ( ISNULL( c07.npuntaje ), 4, c07.npuntaje ) AS criterio07,
                                                        IF ( ISNULL( c08.npuntaje ), 4, c08.npuntaje ) AS criterio08,
                                                        IF ( ISNULL( c09.npuntaje ), 4, c09.npuntaje ) AS criterio09,
                                                        IF ( ISNULL( c10.npuntaje ), 4, c10.npuntaje ) AS criterio10,
                                                        IF ( ISNULL( c11.npuntaje ), 4, c11.npuntaje ) AS criterio11,
                                                        IF ( ISNULL( c12.npuntaje ), 4, c12.npuntaje ) AS criterio12,
                                                        IF ( ISNULL( c13.npuntaje ), 4, c13.npuntaje ) AS criterio13,
                                                        IF ( ISNULL( c14.npuntaje ), 4, c14.npuntaje ) AS criterio14,
                                                        IF ( ISNULL( c15.npuntaje ), 4, c15.npuntaje ) AS criterio15,
                                                        IF ( ISNULL( c16.npuntaje ), 4, c16.npuntaje ) AS criterio16,
                                                        IF ( ISNULL( c17.npuntaje ), 4, c17.npuntaje ) AS criterio17,
                                                        IF ( ISNULL( c18.npuntaje ), 4, c18.npuntaje ) AS criterio18,
                                                        IF ( ISNULL( c19.npuntaje ), 4, c19.npuntaje ) AS criterio19,
                                                        IF ( ISNULL( c20.npuntaje ), 4, c20.npuntaje ) AS criterio20,
                                                        IF ( ISNULL( c21.npuntaje ), 4, c21.npuntaje ) AS criterio21,
                                                        IF ( ISNULL( c22.npuntaje ), 4, c22.npuntaje ) AS criterio22,
                                                        IF ( ISNULL( c23.npuntaje ), 4, c23.npuntaje ) AS criterio23,
                                                        IF ( ISNULL( c24.npuntaje ), 4, c24.npuntaje ) AS criterio24
                                                        FROM
                                                            lg_ordencab
                                                            LEFT JOIN ( SELECT tb_califica.npuntaje, tb_califica.idorden FROM tb_califica WHERE tb_califica.idcriterio = 1 ) AS c01 ON c01.idorden = lg_ordencab.id_regmov
                                                            LEFT JOIN ( SELECT tb_califica.npuntaje, tb_califica.idorden FROM tb_califica WHERE tb_califica.idcriterio = 2 ) AS c02 ON c02.idorden = lg_ordencab.id_regmov
                                                            LEFT JOIN ( SELECT tb_califica.npuntaje, tb_califica.idorden FROM tb_califica WHERE tb_califica.idcriterio = 3 ) AS c03 ON c03.idorden = lg_ordencab.id_regmov
                                                            LEFT JOIN ( SELECT tb_califica.npuntaje, tb_califica.idorden FROM tb_califica WHERE tb_califica.idcriterio = 4 ) AS c04 ON c04.idorden = lg_ordencab.id_regmov
                                                            LEFT JOIN ( SELECT tb_califica.npuntaje, tb_califica.idorden FROM tb_califica WHERE tb_califica.idcriterio = 5 ) AS c05 ON c05.idorden = lg_ordencab.id_regmov
                                                            LEFT JOIN ( SELECT tb_califica.npuntaje, tb_califica.idorden FROM tb_califica WHERE tb_califica.idcriterio = 6 ) AS c06 ON c06.idorden = lg_ordencab.id_regmov
                                                            LEFT JOIN ( SELECT tb_califica.npuntaje, tb_califica.idorden FROM tb_califica WHERE tb_califica.idcriterio = 7 ) AS c07 ON c07.idorden = lg_ordencab.id_regmov
                                                            LEFT JOIN ( SELECT tb_califica.npuntaje, tb_califica.idorden FROM tb_califica WHERE tb_califica.idcriterio = 8 ) AS c08 ON c08.idorden = lg_ordencab.id_regmov
                                                            LEFT JOIN ( SELECT tb_califica.npuntaje, tb_califica.idorden FROM tb_califica WHERE tb_califica.idcriterio = 9 ) AS c09 ON c09.idorden = lg_ordencab.id_regmov
                                                            LEFT JOIN ( SELECT tb_califica.npuntaje, tb_califica.idorden FROM tb_califica WHERE tb_califica.idcriterio = 10 ) AS c10 ON c10.idorden = lg_ordencab.id_regmov
                                                            LEFT JOIN ( SELECT tb_califica.npuntaje, tb_califica.idorden FROM tb_califica WHERE tb_califica.idcriterio = 11 ) AS c11 ON c11.idorden = lg_ordencab.id_regmov
                                                            LEFT JOIN ( SELECT tb_califica.npuntaje, tb_califica.idorden FROM tb_califica WHERE tb_califica.idcriterio = 12 ) AS c12 ON c12.idorden = lg_ordencab.id_regmov
                                                            LEFT JOIN ( SELECT tb_califica.npuntaje, tb_califica.idorden FROM tb_califica WHERE tb_califica.idcriterio = 13 ) AS c13 ON c13.idorden = lg_ordencab.id_regmov
                                                            LEFT JOIN ( SELECT tb_califica.npuntaje, tb_califica.idorden FROM tb_califica WHERE tb_califica.idcriterio = 14 ) AS c14 ON c14.idorden = lg_ordencab.id_regmov
                                                            LEFT JOIN ( SELECT tb_califica.npuntaje, tb_califica.idorden FROM tb_califica WHERE tb_califica.idcriterio = 15 ) AS c15 ON c15.idorden = lg_ordencab.id_regmov
                                                            LEFT JOIN ( SELECT tb_califica.npuntaje, tb_califica.idorden FROM tb_califica WHERE tb_califica.idcriterio = 16 ) AS c16 ON c16.idorden = lg_ordencab.id_regmov
                                                            LEFT JOIN ( SELECT tb_califica.npuntaje, tb_califica.idorden FROM tb_califica WHERE tb_califica.idcriterio = 17 ) AS c17 ON c17.idorden = lg_ordencab.id_regmov
                                                            LEFT JOIN ( SELECT tb_califica.npuntaje, tb_califica.idorden FROM tb_califica WHERE tb_califica.idcriterio = 18 ) AS c18 ON c18.idorden = lg_ordencab.id_regmov
                                                            LEFT JOIN ( SELECT tb_califica.npuntaje, tb_califica.idorden FROM tb_califica WHERE tb_califica.idcriterio = 19 ) AS c19 ON c19.idorden = lg_ordencab.id_regmov
                                                            LEFT JOIN ( SELECT tb_califica.npuntaje, tb_califica.idorden FROM tb_califica WHERE tb_califica.idcriterio = 20 ) AS c20 ON c20.idorden = lg_ordencab.id_regmov
                                                            LEFT JOIN ( SELECT tb_califica.npuntaje, tb_califica.idorden FROM tb_califica WHERE tb_califica.idcriterio = 21 ) AS c21 ON c21.idorden = lg_ordencab.id_regmov
                                                            LEFT JOIN ( SELECT tb_califica.npuntaje, tb_califica.idorden FROM tb_califica WHERE tb_califica.idcriterio = 22 ) AS c22 ON c22.idorden = lg_ordencab.id_regmov
                                                            LEFT JOIN ( SELECT tb_califica.npuntaje, tb_califica.idorden FROM tb_califica WHERE tb_califica.idcriterio = 23 ) AS c23 ON c23.idorden = lg_ordencab.id_regmov
                                                            LEFT JOIN ( SELECT tb_califica.npuntaje, tb_califica.idorden FROM tb_califica WHERE tb_califica.idcriterio = 24 ) AS c24 ON c24.idorden = lg_ordencab.id_regmov
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
                                        <td class="textoCentro">4</td>
                                        <td class="textoCentro">4</td>
                                        <td class="textoCentro">4</td>
                                        <td class="textoCentro">4</td>
                                        <td class="textoCentro">4</td>
                                        <td class="textoCentro">4</td>
                                        <td class="textoCentro">4</td>
                                        <td class="textoCentro">4</td>
                                        <td class="textoCentro">4</td>
                                        <td class="textoCentro">'.$rs['criterio13'].'</td>
                                        <td class="textoCentro">'.$rs['criterio14'].'</td>
                                        <td class="textoCentro">'.$rs['criterio15'].'</td>
                                        <td class="textoCentro">'.$rs['criterio16'].'</td>
                                        <td class="textoCentro">'.$rs['criterio17'].'</td>
                                        <td class="textoCentro">'.$rs['criterio18'].'</td>
                                        <td class="textoCentro">'.$rs['criterio19'].'</td>
                                        <td class="textoCentro">'.$rs['criterio20'].'</td>
                                        <td class="textoCentro">'.$rs['criterio21'].'</td>
                                        <td class="textoCentro"><strong></strong></td>
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