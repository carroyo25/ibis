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
                $salida = '<option value="0">Seleccionar</option>';

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
                $salida = '<option value="0">Seleccionar</option>';
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

        public function mesActual() {
            $salida = null;
            $mes  = ['Todos','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Setiembre','Octubre','Noviembre','Diciembre'];

            foreach ($mes as $key =>$m) {
                $salida .= '<option value = '.$key.'>'.$m.'</option>';
            }

            return $salida;
        }

        public function tablaFamilias($grupo,$clase){
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                        UPPER(tb_familia.cdescrip) AS name,
                                                        SUM(lg_ordendet.ncanti) AS cantidad,
                                                        cm_producto.ngrupo,
                                                        cm_producto.nclase,
                                                        cm_producto.nfam,
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
               

                if ($rowCount > 0) {
                    $docData = array();
                    
                    while($row=$sql->fetch(PDO::FETCH_ASSOC)){
                        array_push( $docData,array("name"=>$row['name'],"y"=>$row['cantidad']));
                    }
                }

                return $docData;

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function tablaItems($grupo,$clase,$familia){
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                        SUM(lg_ordendet.ncanti) AS cantidad,
                                                        IF
                                                        ( lg_ordencab.ncodmon = 20, SUM( lg_ordendet.ntotal )*1, SUM( lg_ordendet.ntotal )* lg_ordencab.ntcambio ) AS total,
                                                        lg_ordendet.id_cprod,
                                                        cm_producto.ccodprod,
                                                        UPPER(cm_producto.cdesprod) AS cdesprod,
                                                        cm_producto.ngrupo,
                                                        cm_producto.nclase,
                                                        cm_producto.nfam 
                                                    FROM
                                                        lg_ordendet
                                                        INNER JOIN cm_producto ON lg_ordendet.id_cprod = cm_producto.id_cprod
                                                        INNER JOIN lg_ordencab ON lg_ordendet.id_regmov = lg_ordencab.id_regmov 
                                                    WHERE
                                                        lg_ordendet.nestado = 1 
                                                        AND cm_producto.ngrupo =:grupo 
                                                        AND cm_producto.nclase =:clase 
                                                        AND cm_producto.nfam =:familia
                                                    GROUP BY cm_producto.id_cprod");

                $sql->execute(["grupo"=>$grupo,"clase"=>$clase,"familia"=>$familia]);
                $rowCount = $sql->rowCount();

                $total_cantidad = 0;
                $total_dinero = 0;
                $salida = "";

                
                /*$salida = "<thead class='stickytop'>
                                <tr >
                                    <th>Tipo</th>
                                    <th>suma<br/>Cantidad</th>
                                    <th>Suma<br/>Total</th>
                                </tr>
                            </thead>
                            <tbody>";
            
                if ($rowCount > 0) {
                    while($rs = $sql->fetch()){
                        $salida .='<tr data-grupo="'.$rs['ngrupo'].'" 
                                        data-clase="'.$rs['nclase'].'" 
                                        data-familia="'.$rs['nfam'].'">
                                        <td width="50%">'.$rs['cdesprod'].'</td>
                                        <td class="textoDerecha"> '.number_format($rs['cantidad'],2).'</td>
                                        <td class="textoDerecha">S/. '.number_format($rs['total'],2).'</td>
                                    </tr>';

                        $total_cantidad = $rs['cantidad'] + $total_cantidad;
                        $total_dinero = $rs['total'] + $total_dinero;
                    }
                }

                $salida.='</tbody>';

                $salida.='<tfoot>
                            <tr>
                                <th scope="row">Total</th>
                                <td class="textoDerecha">'.number_format($total_cantidad,2).'</td>
                                <td class="textoDerecha">S/. '.number_format($total_dinero,2).'</td>
                            </tr>
                        </tfoot>';*/

                return $salida;
                                    
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }
    }
?>