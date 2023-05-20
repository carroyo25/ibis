<?php
    class RepoagerModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarClasesReporte() {
            try {
                $sql = $this->db->connect()->query("SELECT
                                                        tb_clase.ncodclase,
                                                        tb_clase.ncodgrupo,
                                                        UPPER(tb_grupo.cdescrip) AS cdescrip,
                                                        tb_grupo.ccodcata,
                                                        COUNT( tb_clase.ncodgrupo ) AS repeticiones
                                                    FROM
                                                        tb_clase
                                                        INNER JOIN tb_grupo ON tb_clase.ncodgrupo = tb_grupo.ncodgrupo
                                                    WHERE
                                                        tb_clase.nflgactivo = 1
                                                    GROUP BY tb_grupo.cdescrip
                                                    HAVING COUNT( tb_clase.ncodgrupo ) > 0
                                                    ORDER BY tb_grupo.cdescrip");
                $sql->execute();
                $rowCount = $sql->rowCount();
                $salida = "";

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()) {
                        $salida .= '<option value ="'.$rs['ncodgrupo'].'">'.$rs['cdescrip'].'</option>';
                    }  
                }

                return $salida;
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function listarTipos($id) {
            try {
                $sql=$this->db->connect()->prepare("SELECT
                                                    tb_clase.ncodclase, 
                                                    tb_clase.ccodcata, 
                                                    tb_clase.cdescrip, 
                                                    tb_clase.ncodgrupo
                                                FROM
                                                    tb_clase
                                                WHERE
                                                    tb_clase.nflgactivo = 1
                                                    AND tb_clase.ncodgrupo = :id");
                
                $sql->execute(['id'=>$id]);
                $salida = "";
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()) {
                        $salida .= '<option value ="'.$rs['ncodclase'].'">'.$rs['cdescrip'].'</option>';
                    }  
                }

                return $salida;

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function tablaFamilias($grupo,$clase){
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                        UPPER(tb_familia.cdescrip) AS cdescrip,
                                                        SUM(lg_ordendet.ncanti) AS cantidad,
                                                    IF
                                                        ( lg_ordencab.ncodmon = 20, SUM( lg_ordendet.ntotal )*1, SUM( lg_ordendet.ntotal )* lg_ordencab.ntcambio ) AS total
                                                    FROM
                                                        lg_ordendet
                                                        INNER JOIN cm_producto ON lg_ordendet.id_cprod = cm_producto.id_cprod
                                                        INNER JOIN tb_grupo ON cm_producto.ngrupo = tb_grupo.ncodgrupo
                                                        INNER JOIN tb_familia ON cm_producto.nfam = tb_familia.ncodfamilia
                                                        INNER JOIN lg_ordencab ON lg_ordendet.id_regmov = lg_ordencab.id_regmov 
                                                    WHERE
                                                        cm_producto.ngrupo = :grupo 
                                                        AND cm_producto.nclase = :clase
                                                        AND lg_ordendet.nestado = 1 
                                                    GROUP BY
                                                        cm_producto.nfam");
                $sql->execute(["grupo"=>$grupo,"clase"=>$clase]);

                $rowCount = $sql->rowCount();
                $salida = "<tbody>";
                $total_cantidad = 0;
                $total_dinero = 0;
            
                if ($rowCount > 0) {
                    while($rs = $sql->fetch()){
                        $salida .='<tr>
                                        <td>'.$rs['cdescrip'].'</td>
                                        <td class="textoDerecha"> '.$rs['cantidad'].'</td>
                                        <td class="textoDerecha">S/. '.$rs['total'].'</td>
                                    </tr>';

                        $total_cantidad = $rs['cantidad']++;
                        $total_dinero = $rs['total']++;
                    }
                }

                $salida.='</tbody>';

                $salida.='<tfoot>
                            <tr>
                                <th scope="row">Totals</th>
                                <td class="textoDerecha">'.$total_cantidad.'</td>
                                <td class="textoDerecha">S/. '.$total_dinero.'</td>
                            </tr>
                        </tfoot>';

                return $salida;

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }
    }
?>