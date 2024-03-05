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
                                                            lg_ordencab.id_regmov DESC
                                                        LIMIT 10");
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
                                        <td class="textoCentro">4</td>
                                        <td class="textoCentro">4</td>
                                        <td class="textoCentro">4</td>
                                        <td class="textoCentro">4</td>
                                        <td class="textoCentro">4</td>
                                        <td class="textoCentro">4</td>
                                        <td class="textoCentro">4</td>
                                        <td class="textoCentro">4</td>
                                        <td class="textoCentro">4</td>
                                        <td class="textoCentro">4</td>
                                        <td class="textoCentro">4</td>
                                        <td class="textoCentro">4</td>
                                        <td class="textoCentro">4</td>
                                        <td class="textoCentro">4</td>
                                        <td class="textoCentro">4</td>
                                        <td class="textoCentro">4</td>
                                        <td class="textoCentro"></td>
                                        <td class="textoCentro"></td>
                                        <td class="textoCentro"></td>
                                        <td class="textoCentro"></td>
                                        <td class="textoCentro"></td>
                                        <td class="textoCentro"></td>
                                        <td class="textoCentro"></td>
                                        <td class="textoCentro"></td>
                                        <td class="textoCentro"></td>
                                        <td class="textoCentro"></td>
                                        <td class="textoCentro"></td>
                                        <td class="textoCentro"></td>
                                        <td class="textoCentro"></td>
                                        <td class="textoCentro"></td>
                                        <td class="textoCentro"></td>
                                        <td class="textoCentro"></td>
                                        <td class="textoCentro"></td>
                                        <td class="textoCentro"></td>
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

        public function crearReporte($parametros){
            $tipo = $parametros["tipoSearch"];
            $costos = $parametros["costosSearch"] == "-1" ? "%" : $parametros["costosSearch"];
            $mes = $parametros["mesSearch"] == "-1" ? "%" : $parametros["mesSearch"];
            $anio = $parametros["anioSearch"] == "" ? "2024" : $parametros["anioSearch"];
            $salida = "";

            if ($tipo == 1) {
                try {
                    
                    $result = $this->compras($anio,$mes);

                    foreach ($result as $rs) {
                        $total = $rs['c1']+$rs['c2']+$rs['c3']+$rs['c4']+$rs['c5']+$rs['c6']+$rs['c7']+$rs['c8']+$rs['c9']+$rs['c10']+$rs['c11']+$rs['c12']+
                                    $rs['c13']+$rs['c14']+$rs['c15']+$rs['c16']+$rs['c17']+$rs['c18']+$rs['c19']+$rs['c20']+$rs['c39']+$rs['c41']+$rs['c42']+$rs['c43']+
                                    $rs['c44'];
                        
                        $salida .='<tr class="pointer" data-indice="'.$rs['id_regmov'].'"">
                                    <td class="textoCentro">'.str_pad($rs['cnumero'],4,0,STR_PAD_LEFT).'</td>
                                    <td class="textoCentro">'.date("d/m/Y", strtotime($rs['ffechadoc'])).'</td>
                                    <td class="pl20px">'.$rs['concepto'].'</td>
                                    <td class="pl20px">'.$rs['ccodproy'].'</td>
                                    <td class="pl20px">'.$rs['proveedor'].'</td>

                                    <td class="textoCentro">'.$rs['c1'].'</td>
                                    <td class="textoCentro">'.$rs['c2'].'</td>
                                    <td class="textoCentro">'.$rs['c3'].'</td>
                                    <td class="textoCentro">'.$rs['c4'].'</td>
                                    <td class="textoCentro">'.$rs['c5'].'</td>

                                    <td class="textoCentro">'.$rs['c6'].'</td>
                                    <td class="textoCentro">'.$rs['c7'].'</td>
                                    <td class="textoCentro">'.$rs['c8'].'</td>
                                    <td class="textoCentro">'.$rs['c9'].'</td>
                                    <td class="textoCentro">'.$rs['c10'].'</td>
                                    <td class="textoCentro">'.$rs['c11'].'</td>
                                    <td class="textoCentro">'.$rs['c12'].'</td>

                                    <td class="textoCentro">'.$rs['c13'].'</td>
                                    <td class="textoCentro">'.$rs['c14'].'</td>
                                    <td class="textoCentro">'.$rs['c15'].'</td>
                                    <td class="textoCentro">'.$rs['c16'].'</td>
                                    <td class="textoCentro">'.$rs['c17'].'</td>

                                    <td class="textoCentro">'.$rs['c18'].'</td>
                                    <td class="textoCentro">'.$rs['c19'].'</td>
                                    <td class="textoCentro">'.$rs['c20'].'</td>
                                    <td class="textoCentro">'.$rs['c39'].'</td>
                                    <td class="textoCentro">'.$rs['c41'].'</td>
                                    <td class="textoCentro">'.$rs['c42'].'</td>

                                    <td class="textoCentro">'.$rs['c43'].'</td>
                                    <td class="textoCentro">'.$rs['c44'].'</td>
                                    

                                    <td class="textoCentro"><strong>'.$total.'</strong></td>
                                 </tr>';
                    }

                    return $salida;

                } catch (PDOException $th) {
                    echo "Error: " . $th->getMessage();
                    return false;
                }
                
            }else{
                try {
                    
                    $result = $this->servicios($anio,$mes);

                    foreach ($result as $rs) {
                        $total = $rs['c25']+$rs['c26']+$rs['c27']+$rs['c28']+$rs['c29']+$rs['c30']+$rs['c31']+$rs['c32']+$rs['c33']+$rs['c34']
                                    +$rs['c45']+$rs['c46']+$rs['c47']+$rs['c48']+$rs['c49']+$rs['c50']+$rs['c51']+$rs['c52'];
                        
                        $salida .='<tr class="pointer" data-indice="'.$rs['id_regmov'].'"">
                                    <td class="textoCentro">'.str_pad($rs['cnumero'],4,0,STR_PAD_LEFT).'</td>
                                    <td class="textoCentro">'.date("d/m/Y", strtotime($rs['ffechadoc'])).'</td>
                                    <td class="pl20px">'.$rs['concepto'].'</td>
                                    <td class="pl20px">'.$rs['ccodproy'].'</td>
                                    <td class="pl20px">'.$rs['proveedor'].'</td>

                                    <td class="textoCentro">'.$rs['c25'].'</td>
                                    <td class="textoCentro">'.$rs['c26'].'</td>
                                    <td class="textoCentro">'.$rs['c27'].'</td>
                                    <td class="textoCentro">'.$rs['c28'].'</td>
                                    <td class="textoCentro">'.$rs['c29'].'</td>

                                    <td class="textoCentro">'.$rs['c30'].'</td>
                                    <td class="textoCentro">'.$rs['c31'].'</td>
                                    <td class="textoCentro">'.$rs['c32'].'</td>
                                    <td class="textoCentro">'.$rs['c33'].'</td>
                                    <td class="textoCentro">'.$rs['c34'].'</td>

                                    <td class="textoCentro">'.$rs['c45'].'</td>
                                    <td class="textoCentro">'.$rs['c46'].'</td>
                                    <td class="textoCentro">'.$rs['c47'].'</td>

                                    <td class="textoCentro">'.$rs['c48'].'</td>
                                    <td class="textoCentro">'.$rs['c49'].'</td>
                                    <td class="textoCentro">'.$rs['c50'].'</td>
                                    <td class="textoCentro">'.$rs['c51'].'</td>
                                    <td class="textoCentro">'.$rs['c52'].'</td>
                                    
                                    <td class="textoCentro"><strong>'.$total.'</strong></td>
                                 </tr>';
                    }

                    return $salida;
                } catch (PDOException $th) {
                    echo "Error: " . $th->getMessage();
                    return false;
                }
            }
        }

        public function crearExcel($parametros) {
            $tipo = $parametros["tipoSearch"];
            $costos = $parametros["costosSearch"] == "-1" ? "%" : $parametros["costosSearch"];
            $mes = $parametros["mesSearch"] == "-1" ? "%" : $parametros["mesSearch"];
            $anio = $parametros["anioSearch"] == "" ? "2024" : $parametros["anioSearch"];

            if ($tipo == 1){
                $this->exportarCompras($anio, $mes);
            }
            else{
                $this->exportarServicios($anio, $mes);
            }

            return array("documento"=>'public/documentos/reportes/evaluacion.xlsx');
        }

        private function servicios($anio,$mes){
                $sql = $this->db->connect()->prepare("SELECT
                                                            lg_ordencab.id_regmov,
                                                            UPPER( cm_entidad.crazonsoc ) AS proveedor,
                                                            tb_proyectos.ccodproy,
                                                            UPPER( tb_pedidocab.concepto ) AS concepto,
                                                            lg_ordencab.ffechadoc,
                                                            lg_ordencab.cnumero,
                                                            lg_ordencab.ncodcos,
                                                            IFNULL( c25.npuntaje, 4 ) AS c25,
                                                            IFNULL( c26.npuntaje, 4 ) AS c26,
                                                            IFNULL( c27.npuntaje, 4 ) AS c27,
                                                            IFNULL( c28.npuntaje, 4 ) AS c28,
                                                            IFNULL( c29.npuntaje, 4 ) AS c29,
                                                            IFNULL( c30.npuntaje, 4 ) AS c30,
                                                            IFNULL( c31.npuntaje, 4 ) AS c31,
                                                            IFNULL( c32.npuntaje, 4 ) AS c32,
                                                            IFNULL( c33.npuntaje, 4 ) AS c33,
                                                            IFNULL( c34.npuntaje, 4 ) AS c34,
                                                            IFNULL( c45.npuntaje, 4 ) AS c45,
                                                            IFNULL( c46.npuntaje, 4 ) AS c46,
                                                            IFNULL( c47.npuntaje, 4 ) AS c47,
                                                            IFNULL( c48.npuntaje, 4 ) AS c48,
                                                            IFNULL( c49.npuntaje, 4 ) AS c49,
                                                            IFNULL( c50.npuntaje, 4 ) AS c50,
                                                            IFNULL( c51.npuntaje, 4 ) AS c51,
                                                            IFNULL( c52.npuntaje, 4 ) AS c52 
                                                        FROM
                                                            lg_ordencab
                                                            INNER JOIN cm_entidad ON lg_ordencab.id_centi = cm_entidad.id_centi
                                                            INNER JOIN tb_proyectos ON lg_ordencab.ncodcos = tb_proyectos.nidreg
                                                            INNER JOIN tb_pedidocab ON lg_ordencab.id_refpedi = tb_pedidocab.idreg
                                                            LEFT JOIN ( SELECT tb_califica.npuntaje, tb_califica.idorden FROM tb_califica WHERE tb_califica.idcriterio = 25 ) AS c25 ON c25.idorden = lg_ordencab.id_regmov
                                                            LEFT JOIN ( SELECT tb_califica.npuntaje, tb_califica.idorden FROM tb_califica WHERE tb_califica.idcriterio = 26 ) AS c26 ON c26.idorden = lg_ordencab.id_regmov
                                                            LEFT JOIN ( SELECT tb_califica.npuntaje, tb_califica.idorden FROM tb_califica WHERE tb_califica.idcriterio = 27 ) AS c27 ON c27.idorden = lg_ordencab.id_regmov
                                                            LEFT JOIN ( SELECT tb_califica.npuntaje, tb_califica.idorden FROM tb_califica WHERE tb_califica.idcriterio = 28 ) AS c28 ON c28.idorden = lg_ordencab.id_regmov
                                                            LEFT JOIN ( SELECT tb_califica.npuntaje, tb_califica.idorden FROM tb_califica WHERE tb_califica.idcriterio = 29 ) AS c29 ON c29.idorden = lg_ordencab.id_regmov
                                                            LEFT JOIN ( SELECT tb_califica.npuntaje, tb_califica.idorden FROM tb_califica WHERE tb_califica.idcriterio = 30 ) AS c30 ON c30.idorden = lg_ordencab.id_regmov
                                                            LEFT JOIN ( SELECT tb_califica.npuntaje, tb_califica.idorden FROM tb_califica WHERE tb_califica.idcriterio = 31 ) AS c31 ON c31.idorden = lg_ordencab.id_regmov
                                                            LEFT JOIN ( SELECT tb_califica.npuntaje, tb_califica.idorden FROM tb_califica WHERE tb_califica.idcriterio = 32 ) AS c32 ON c32.idorden = lg_ordencab.id_regmov
                                                            LEFT JOIN ( SELECT tb_califica.npuntaje, tb_califica.idorden FROM tb_califica WHERE tb_califica.idcriterio = 33 ) AS c33 ON c33.idorden = lg_ordencab.id_regmov
                                                            LEFT JOIN ( SELECT tb_califica.npuntaje, tb_califica.idorden FROM tb_califica WHERE tb_califica.idcriterio = 34 ) AS c34 ON c34.idorden = lg_ordencab.id_regmov
                                                            LEFT JOIN ( SELECT tb_califica.npuntaje, tb_califica.idorden FROM tb_califica WHERE tb_califica.idcriterio = 45 ) AS c45 ON c45.idorden = lg_ordencab.id_regmov
                                                            LEFT JOIN ( SELECT tb_califica.npuntaje, tb_califica.idorden FROM tb_califica WHERE tb_califica.idcriterio = 46 ) AS c46 ON c46.idorden = lg_ordencab.id_regmov
                                                            LEFT JOIN ( SELECT tb_califica.npuntaje, tb_califica.idorden FROM tb_califica WHERE tb_califica.idcriterio = 47 ) AS c47 ON c47.idorden = lg_ordencab.id_regmov
                                                            LEFT JOIN ( SELECT tb_califica.npuntaje, tb_califica.idorden FROM tb_califica WHERE tb_califica.idcriterio = 48 ) AS c48 ON c48.idorden = lg_ordencab.id_regmov
                                                            LEFT JOIN ( SELECT tb_califica.npuntaje, tb_califica.idorden FROM tb_califica WHERE tb_califica.idcriterio = 49 ) AS c49 ON c49.idorden = lg_ordencab.id_regmov
                                                            LEFT JOIN ( SELECT tb_califica.npuntaje, tb_califica.idorden FROM tb_califica WHERE tb_califica.idcriterio = 50 ) AS c50 ON c50.idorden = lg_ordencab.id_regmov
                                                            LEFT JOIN ( SELECT tb_califica.npuntaje, tb_califica.idorden FROM tb_califica WHERE tb_califica.idcriterio = 51 ) AS c51 ON c51.idorden = lg_ordencab.id_regmov
                                                            LEFT JOIN ( SELECT tb_califica.npuntaje, tb_califica.idorden FROM tb_califica WHERE tb_califica.idcriterio = 52 ) AS c52 ON c52.idorden = lg_ordencab.id_regmov 
                                                            WHERE
                                                                lg_ordencab.cper = :anio 
                                                                AND lg_ordencab.ntipmov = 38
                                                                AND lg_ordencab.nEstadoDoc !=105 
                                                            ORDER BY
                                                                lg_ordencab.id_regmov DESC");
                $sql->execute(["anio"=>$anio]);
                $rowCount = $sql->rowCount();

                return $servicios = $sql->fetchAll(PDO::FETCH_ASSOC);
        }   

        private function compras($anio,$mes){
                $sql = $this->db->connect()->prepare("SELECT
                                                        lg_ordencab.id_regmov,
                                                        UPPER( cm_entidad.crazonsoc ) AS proveedor,
                                                        tb_proyectos.ccodproy,
                                                        UPPER( tb_pedidocab.concepto ) AS concepto,
                                                        lg_ordencab.ffechadoc,
                                                        lg_ordencab.cnumero,
                                                        lg_ordencab.ncodcos,
                                                        IFNULL( c1.npuntaje, 4 ) AS c1,
                                                        IFNULL( c2.npuntaje, 4 ) AS c2,
                                                        IFNULL( c3.npuntaje, 4 ) AS c3,
                                                        IFNULL( c4.npuntaje, 4 ) AS c4,
                                                        IFNULL( c5.npuntaje, 4 ) AS c5,
                                                        IFNULL( c6.npuntaje, 4 ) AS c6,
                                                        IFNULL( c7.npuntaje, 4 ) AS c7,
                                                        IFNULL( c8.npuntaje, 4 ) AS c8,
                                                        IFNULL( c9.npuntaje, 4 ) AS c9,
                                                        IFNULL( c10.npuntaje, 4 ) AS c10,
                                                        IFNULL( c11.npuntaje, 4 ) AS c11,
                                                        IFNULL( c12.npuntaje, 4 ) AS c12,
                                                        IFNULL( c13.npuntaje, 4 ) AS c13,
                                                        IFNULL( c14.npuntaje, 4 ) AS c14,
                                                        IFNULL( c15.npuntaje, 4 ) AS c15,
                                                        IFNULL( c16.npuntaje, 4 ) AS c16,
                                                        IFNULL( c17.npuntaje, 4 ) AS c17,
                                                        IFNULL( c18.npuntaje, 4 ) AS c18,
                                                        IFNULL( c19.npuntaje, 4 ) AS c19,
                                                        IFNULL( c20.npuntaje, 4 ) AS c20,
                                                        IFNULL( c39.npuntaje, 4 ) AS c39,
                                                        IFNULL( c41.npuntaje, 4 ) AS c41,
                                                        IFNULL( c42.npuntaje, 4 ) AS c42,
                                                        IFNULL( c43.npuntaje, 4 ) AS c43,
                                                        IFNULL( c44.npuntaje, 4 ) AS c44,
                                                        IFNULL( c53.npuntaje, 4 ) AS c53,
                                                        IFNULL( c54.npuntaje, 4 ) AS c54,
                                                        IFNULL( c55.npuntaje, 4 ) AS c55 
                                                    FROM
                                                        lg_ordencab
                                                        INNER JOIN cm_entidad ON lg_ordencab.id_centi = cm_entidad.id_centi
                                                        INNER JOIN tb_proyectos ON lg_ordencab.ncodcos = tb_proyectos.nidreg
                                                        INNER JOIN tb_pedidocab ON lg_ordencab.id_refpedi = tb_pedidocab.idreg
                                                        LEFT JOIN ( SELECT tb_califica.npuntaje, tb_califica.idorden FROM tb_califica WHERE tb_califica.idcriterio = 1 ) AS c1 ON c1.idorden = lg_ordencab.id_regmov
                                                        LEFT JOIN ( SELECT tb_califica.npuntaje, tb_califica.idorden FROM tb_califica WHERE tb_califica.idcriterio = 2 ) AS c2 ON c2.idorden = lg_ordencab.id_regmov
                                                        LEFT JOIN ( SELECT tb_califica.npuntaje, tb_califica.idorden FROM tb_califica WHERE tb_califica.idcriterio = 3 ) AS c3 ON c3.idorden = lg_ordencab.id_regmov
                                                        LEFT JOIN ( SELECT tb_califica.npuntaje, tb_califica.idorden FROM tb_califica WHERE tb_califica.idcriterio = 4 ) AS c4 ON c4.idorden = lg_ordencab.id_regmov
                                                        LEFT JOIN ( SELECT tb_califica.npuntaje, tb_califica.idorden FROM tb_califica WHERE tb_califica.idcriterio = 5 ) AS c5 ON c5.idorden = lg_ordencab.id_regmov
                                                        LEFT JOIN ( SELECT tb_califica.npuntaje, tb_califica.idorden FROM tb_califica WHERE tb_califica.idcriterio = 6 ) AS c6 ON c6.idorden = lg_ordencab.id_regmov
                                                        LEFT JOIN ( SELECT tb_califica.npuntaje, tb_califica.idorden FROM tb_califica WHERE tb_califica.idcriterio = 7 ) AS c7 ON c7.idorden = lg_ordencab.id_regmov
                                                        LEFT JOIN ( SELECT tb_califica.npuntaje, tb_califica.idorden FROM tb_califica WHERE tb_califica.idcriterio = 8 ) AS c8 ON c7.idorden = lg_ordencab.id_regmov
                                                        LEFT JOIN ( SELECT tb_califica.npuntaje, tb_califica.idorden FROM tb_califica WHERE tb_califica.idcriterio = 9 ) AS c9 ON c9.idorden = lg_ordencab.id_regmov
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
                                                        LEFT JOIN ( SELECT tb_califica.npuntaje, tb_califica.idorden FROM tb_califica WHERE tb_califica.idcriterio = 39 ) AS c39 ON c39.idorden = lg_ordencab.id_regmov
                                                        LEFT JOIN ( SELECT tb_califica.npuntaje, tb_califica.idorden FROM tb_califica WHERE tb_califica.idcriterio = 41 ) AS c41 ON c41.idorden = lg_ordencab.id_regmov
                                                        LEFT JOIN ( SELECT tb_califica.npuntaje, tb_califica.idorden FROM tb_califica WHERE tb_califica.idcriterio = 42 ) AS c42 ON c42.idorden = lg_ordencab.id_regmov
                                                        LEFT JOIN ( SELECT tb_califica.npuntaje, tb_califica.idorden FROM tb_califica WHERE tb_califica.idcriterio = 43 ) AS c43 ON c43.idorden = lg_ordencab.id_regmov
                                                        LEFT JOIN ( SELECT tb_califica.npuntaje, tb_califica.idorden FROM tb_califica WHERE tb_califica.idcriterio = 44 ) AS c44 ON c44.idorden = lg_ordencab.id_regmov
                                                        LEFT JOIN ( SELECT tb_califica.npuntaje, tb_califica.idorden FROM tb_califica WHERE tb_califica.idcriterio = 53 ) AS c53 ON c53.idorden = lg_ordencab.id_regmov
                                                        LEFT JOIN ( SELECT tb_califica.npuntaje, tb_califica.idorden FROM tb_califica WHERE tb_califica.idcriterio = 54 ) AS c54 ON c54.idorden = lg_ordencab.id_regmov
                                                        LEFT JOIN ( SELECT tb_califica.npuntaje, tb_califica.idorden FROM tb_califica WHERE tb_califica.idcriterio = 55 ) AS c55 ON c55.idorden = lg_ordencab.id_regmov 
                                                    WHERE
                                                        lg_ordencab.cper = :anio 
                                                        AND lg_ordencab.ntipmov = 37
                                                        AND lg_ordencab.nEstadoDoc !=105 
                                                    ORDER BY
                                                        lg_ordencab.id_regmov DESC");
                $sql->execute(["anio"=>$anio]);
                $rowCount = $sql->rowCount();

                return $compras = $sql->fetchAll(PDO::FETCH_ASSOC);
        }

        private function exportarCompras($anio,$mes){
            try {
                require_once('public/PHPExcel/PHPExcel.php');

                $result = $this->compras($anio,$mes);

                $objPHPExcel = new PHPExcel();
                $objPHPExcel->getProperties()
                    ->setCreator("Sical")
                    ->setLastModifiedBy("Sical")
                    ->setTitle("Cargo Plan")
                    ->setSubject("Template excel")
                    ->setDescription("Reporte de Evaluación de Proveedores")
                    ->setKeywords("Template excel");

                $objWorkSheet = $objPHPExcel->createSheet(1);

                $objPHPExcel->setActiveSheetIndex(0);
                $objPHPExcel->getActiveSheet()->setTitle("Reporte Evaluación - Compras");

                $objPHPExcel->getActiveSheet()->mergeCells('A1:AQ1');
                $objPHPExcel->getActiveSheet()->mergeCells('F2:J2');
                $objPHPExcel->getActiveSheet()->mergeCells('K2:Q2');
                $objPHPExcel->getActiveSheet()->mergeCells('R2:V2');
                $objPHPExcel->getActiveSheet()->mergeCells('W2:AD2');
                $objPHPExcel->getActiveSheet()->mergeCells('AE2:AG2');

                $objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(60);
                $objPHPExcel->getActiveSheet()->getRowDimension('3')->setRowHeight(60);

                $objPHPExcel->getActiveSheet()->getStyle('F3:AH3')->getAlignment()->setWrapText(true);

                $objPHPExcel->getActiveSheet()->setCellValue('A1','Reporte de Evaluación de Proveedores - Compras');

                $objPHPExcel->getActiveSheet()->getStyle('A1:AH2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('A1:AH2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $objPHPExcel->getActiveSheet()->getStyle('A3:AH3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('A3:AH3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $objPHPExcel->getActiveSheet()->setCellValue('A2','N°.ORDEN');
                $objPHPExcel->getActiveSheet()->setCellValue('B2','EMISION');
                $objPHPExcel->getActiveSheet()->setCellValue('C2','DESCRIPCION');
                $objPHPExcel->getActiveSheet()->setCellValue('D2','CC');
                $objPHPExcel->getActiveSheet()->setCellValue('E2','PROVEEDOR');
                $objPHPExcel->getActiveSheet()->setCellValue('F2','ALMACEN RECEPCION');
                $objPHPExcel->getActiveSheet()->setCellValue('K2','QA/QC');
                $objPHPExcel->getActiveSheet()->setCellValue('R2','ALMACEN OBRA');
                $objPHPExcel->getActiveSheet()->setCellValue('W2','COMPRAS');

                $objPHPExcel->getActiveSheet()->setCellValue('F3','Fecha de Entrega');
                $objPHPExcel->getActiveSheet()->setCellValue('G3','Condiciones de Llegada');
                $objPHPExcel->getActiveSheet()->setCellValue('H3','Embalaje de Proveedor');
                $objPHPExcel->getActiveSheet()->setCellValue('I3','Cantidad Entregada');
                $objPHPExcel->getActiveSheet()->setCellValue('J3','Documentación');

                $objPHPExcel->getActiveSheet()->setCellValue('K3','Cumplimiento Técnico');
                $objPHPExcel->getActiveSheet()->setCellValue('L3','Documentación');
                $objPHPExcel->getActiveSheet()->setCellValue('M3','Inspeccion Visual');
                $objPHPExcel->getActiveSheet()->setCellValue('N3','Trazabilidad');
                $objPHPExcel->getActiveSheet()->setCellValue('O3','Comunicación');
                $objPHPExcel->getActiveSheet()->setCellValue('P3','Aceptación de Reclamos');
                $objPHPExcel->getActiveSheet()->setCellValue('Q3','Almacenamiento y Preservacion');

                $objPHPExcel->getActiveSheet()->setCellValue('R3','Cantidad Entregada');
                $objPHPExcel->getActiveSheet()->setCellValue('S3','Condiciones de Llegada');
                $objPHPExcel->getActiveSheet()->setCellValue('T3','Embalaje');
                $objPHPExcel->getActiveSheet()->setCellValue('U3','Garantía del Material');
                $objPHPExcel->getActiveSheet()->setCellValue('V3','Documentación');

                $objPHPExcel->getActiveSheet()->setCellValue('W3','Precio Competitivo');
                $objPHPExcel->getActiveSheet()->setCellValue('X3','Descuento');
                $objPHPExcel->getActiveSheet()->setCellValue('Y3','Delivery');
                $objPHPExcel->getActiveSheet()->setCellValue('Z3','Aceptación de Reclamos');
                $objPHPExcel->getActiveSheet()->setCellValue('AA3','Forma de Pago');
                $objPHPExcel->getActiveSheet()->setCellValue('AB3','Comunicación');
                $objPHPExcel->getActiveSheet()->setCellValue('AC3','Seriedad');
                $objPHPExcel->getActiveSheet()->setCellValue('AD3','Capacitación');
               
                $objPHPExcel->getActiveSheet()->setCellValue('AE3','TOTAL');

                $fila = 4;

                foreach ($result as $rs) {
                    $objPHPExcel->getActiveSheet()->setCellValue('A'.$fila,str_pad($rs['cnumero'],4,0,STR_PAD_LEFT));
                    $objPHPExcel->getActiveSheet()->setCellValue('B'.$fila,date("d/m/Y", strtotime($rs['ffechadoc'])));
                    $objPHPExcel->getActiveSheet()->setCellValue('C'.$fila,$rs['concepto']);
                    $objPHPExcel->getActiveSheet()->setCellValue('D'.$fila,$rs['ccodproy']);
                    $objPHPExcel->getActiveSheet()->setCellValue('E'.$fila,$rs['proveedor']);
                    
                    $objPHPExcel->getActiveSheet()->setCellValue('F'.$fila,$rs['c1']);
                    $objPHPExcel->getActiveSheet()->setCellValue('G'.$fila,$rs['c2']);
                    $objPHPExcel->getActiveSheet()->setCellValue('H'.$fila,$rs['c3']);
                    $objPHPExcel->getActiveSheet()->setCellValue('I'.$fila,$rs['c4']);
                    $objPHPExcel->getActiveSheet()->setCellValue('J'.$fila,$rs['c5']);

                    $objPHPExcel->getActiveSheet()->setCellValue('K'.$fila,$rs['c6']);
                    $objPHPExcel->getActiveSheet()->setCellValue('L'.$fila,$rs['c7']);
                    $objPHPExcel->getActiveSheet()->setCellValue('M'.$fila,$rs['c8']);
                    $objPHPExcel->getActiveSheet()->setCellValue('N'.$fila,$rs['c9']);
                    $objPHPExcel->getActiveSheet()->setCellValue('O'.$fila,$rs['c10']);
                    $objPHPExcel->getActiveSheet()->setCellValue('P'.$fila,$rs['c11']);
                    $objPHPExcel->getActiveSheet()->setCellValue('Q'.$fila,$rs['c12']);

                    $objPHPExcel->getActiveSheet()->setCellValue('R'.$fila,$rs['c13']);
                    $objPHPExcel->getActiveSheet()->setCellValue('S'.$fila,$rs['c14']);
                    $objPHPExcel->getActiveSheet()->setCellValue('T'.$fila,$rs['c15']);
                    $objPHPExcel->getActiveSheet()->setCellValue('U'.$fila,$rs['c16']);
                    $objPHPExcel->getActiveSheet()->setCellValue('V'.$fila,$rs['c17']);

                    $objPHPExcel->getActiveSheet()->setCellValue('W'.$fila,$rs['c18']);
                    $objPHPExcel->getActiveSheet()->setCellValue('X'.$fila,$rs['c19']);
                    $objPHPExcel->getActiveSheet()->setCellValue('Y'.$fila,$rs['c20']);
                    $objPHPExcel->getActiveSheet()->setCellValue('Z'.$fila,$rs['c39']);
                    $objPHPExcel->getActiveSheet()->setCellValue('AA'.$fila,$rs['c41']);
                    $objPHPExcel->getActiveSheet()->setCellValue('AB'.$fila,$rs['c42']);
                    $objPHPExcel->getActiveSheet()->setCellValue('AC'.$fila,$rs['c43']);
                    $objPHPExcel->getActiveSheet()->setCellValue('AD'.$fila,$rs['c44']);

                    $fila++;
                }

                $objPHPExcel->getActiveSheet()->getColumnDimension("B")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("C")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("E")->setAutoSize(true);
                
                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');
                $objWriter->save('public/documentos/reportes/evaluacion.xlsx');

                return array("documento"=>'public/documentos/reportes/evaluacion.xlsx');

                exit();
            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }

        private function exportarServicios($anio,$mes){
            try {
                require_once('public/PHPExcel/PHPExcel.php');

                $result = $this->servicios($anio,$mes);

                $objPHPExcel = new PHPExcel();
                $objPHPExcel->getProperties()
                    ->setCreator("Sical")
                    ->setLastModifiedBy("Sical")
                    ->setTitle("Cargo Plan")
                    ->setSubject("Template excel")
                    ->setDescription("Reporte de Evaluación de Proveedores")
                    ->setKeywords("Template excel");

                    $objWorkSheet = $objPHPExcel->createSheet(1);

                    $objPHPExcel->setActiveSheetIndex(0);
                    $objPHPExcel->getActiveSheet()->setTitle("Reporte Evaluación - Servicios");
    
                    $objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(60);
                    $objPHPExcel->getActiveSheet()->getRowDimension('3')->setRowHeight(60);
    
                    $objPHPExcel->getActiveSheet()->getStyle('F3:AH3')->getAlignment()->setWrapText(true);
    
                    $objPHPExcel->getActiveSheet()->setCellValue('A1','Reporte de Evaluación de Proveedores - Servicios');
    
                    $objPHPExcel->getActiveSheet()->getStyle('A1:W2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $objPHPExcel->getActiveSheet()->getStyle('A1:W2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    
                    $objPHPExcel->getActiveSheet()->getStyle('A3:W3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $objPHPExcel->getActiveSheet()->getStyle('A3:W3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                    $objPHPExcel->getActiveSheet()->mergeCells('A1:W1');
                    $objPHPExcel->getActiveSheet()->mergeCells('F2:M2');
                    $objPHPExcel->getActiveSheet()->mergeCells('N2:T2');
                    $objPHPExcel->getActiveSheet()->mergeCells('U2:W2');
    
                    $objPHPExcel->getActiveSheet()->setCellValue('A2','N°.ORDEN');
                    $objPHPExcel->getActiveSheet()->setCellValue('B2','EMISION');
                    $objPHPExcel->getActiveSheet()->setCellValue('C2','DESCRIPCION');
                    $objPHPExcel->getActiveSheet()->setCellValue('D2','CC');
                    $objPHPExcel->getActiveSheet()->setCellValue('E2','PROVEEDOR');
                    $objPHPExcel->getActiveSheet()->setCellValue('F2','QA/QC');
                    $objPHPExcel->getActiveSheet()->setCellValue('N2','COMPRAS');
                    $objPHPExcel->getActiveSheet()->setCellValue('U2','USUARIO/GERENTE DE PROYECTO');
                    $objPHPExcel->getActiveSheet()->setCellValue('W2','TOTAL');

                    $objPHPExcel->getActiveSheet()->setCellValue('F3','Plan de Gestion');
                    $objPHPExcel->getActiveSheet()->setCellValue('G3','Plan de Puntos');
                    $objPHPExcel->getActiveSheet()->setCellValue('H3','Procedimientos');
                    $objPHPExcel->getActiveSheet()->setCellValue('I3','Requisitos');
                    $objPHPExcel->getActiveSheet()->setCellValue('J3','Aceptación de Reclamos');
                    $objPHPExcel->getActiveSheet()->setCellValue('K3','Comunicación Oportuna');
                    $objPHPExcel->getActiveSheet()->setCellValue('L3','Dossier');
                    $objPHPExcel->getActiveSheet()->setCellValue('M3','Avisa oportunamente');

                    $objPHPExcel->getActiveSheet()->setCellValue('N3','Precio');
                    $objPHPExcel->getActiveSheet()->setCellValue('O3','Descuento');
                    $objPHPExcel->getActiveSheet()->setCellValue('P3','Notificaciones');
                    $objPHPExcel->getActiveSheet()->setCellValue('Q3','Aceptación de Reclamos');
                    $objPHPExcel->getActiveSheet()->setCellValue('R3','Forma de Pago');
                    $objPHPExcel->getActiveSheet()->setCellValue('S3','Comunicación');
                    $objPHPExcel->getActiveSheet()->setCellValue('T3','Seriedad');

                    $objPHPExcel->getActiveSheet()->setCellValue('U3','Fecha de Entrega');
                    $objPHPExcel->getActiveSheet()->setCellValue('V3','Calidad');
                    $objPHPExcel->getActiveSheet()->setCellValue('W3','Cantidad de Procesos');

                    $fila = 4;

                foreach ($result as $rs) {
                    $objPHPExcel->getActiveSheet()->setCellValue('A'.$fila,str_pad($rs['cnumero'],4,0,STR_PAD_LEFT));
                    $objPHPExcel->getActiveSheet()->setCellValue('B'.$fila,date("d/m/Y", strtotime($rs['ffechadoc'])));
                    $objPHPExcel->getActiveSheet()->setCellValue('C'.$fila,$rs['concepto']);
                    $objPHPExcel->getActiveSheet()->setCellValue('D'.$fila,$rs['ccodproy']);
                    $objPHPExcel->getActiveSheet()->setCellValue('E'.$fila,$rs['proveedor']);
                    
                    $objPHPExcel->getActiveSheet()->setCellValue('F'.$fila,$rs['c45']);
                    $objPHPExcel->getActiveSheet()->setCellValue('G'.$fila,$rs['c46']);
                    $objPHPExcel->getActiveSheet()->setCellValue('H'.$fila,$rs['c47']);
                    $objPHPExcel->getActiveSheet()->setCellValue('I'.$fila,$rs['c48']);
                    $objPHPExcel->getActiveSheet()->setCellValue('J'.$fila,$rs['c49']);
                    $objPHPExcel->getActiveSheet()->setCellValue('K'.$fila,$rs['c50']);
                    $objPHPExcel->getActiveSheet()->setCellValue('L'.$fila,$rs['c51']);
                    $objPHPExcel->getActiveSheet()->setCellValue('M'.$fila,$rs['c52']);

                    $objPHPExcel->getActiveSheet()->setCellValue('N'.$fila,$rs['c28']);
                    $objPHPExcel->getActiveSheet()->setCellValue('O'.$fila,$rs['c29']);
                    $objPHPExcel->getActiveSheet()->setCellValue('P'.$fila,$rs['c30']);
                    $objPHPExcel->getActiveSheet()->setCellValue('Q'.$fila,$rs['c31']);
                    $objPHPExcel->getActiveSheet()->setCellValue('R'.$fila,$rs['c32']);
                    $objPHPExcel->getActiveSheet()->setCellValue('S'.$fila,$rs['c33']);
                    $objPHPExcel->getActiveSheet()->setCellValue('T'.$fila,$rs['c34']);

                    $objPHPExcel->getActiveSheet()->setCellValue('U'.$fila,$rs['c50']);
                    $objPHPExcel->getActiveSheet()->setCellValue('V'.$fila,$rs['c51']);
                    $objPHPExcel->getActiveSheet()->setCellValue('W'.$fila,$rs['c52']);

                    $fila++;
                }

                $objPHPExcel->getActiveSheet()->getColumnDimension("B")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("C")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("E")->setAutoSize(true);

                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');
                $objWriter->save('public/documentos/reportes/evaluacion.xlsx');

                return array("documento"=>'public/documentos/reportes/evaluacion.xlsx');

                exit();
            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }
    }
?>