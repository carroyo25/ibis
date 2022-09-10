<?php
    class CargoPlannerModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarCargoPlan(){
            try {
                $salida="";
                $sql = $this->db->connect()->prepare("");
                $sql->execute();
                $rowCount = $sql->rowCount();
                $item = 1;

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()) {
                        $salida .='<tr class="pointer">
                                        <td class="textoCentro">'.str_pad($item++,6,0,STR_PAD_LEFT).'</td>
                                        <td></td>
                                        <td>'.$rs['ccodproy'].'</td>
                                        <td>'.$rs['area'].'</td>
                                        <td></td>
                                        <td>'.$rs['pedido'].'</td>
                                        <td>'.$rs['faprueba'].'</td>
                                        <td>'.$rs['ccodprod'].'</td>
                                        <td>'.$rs['unidad'].'</td>
                                        <td width="20%">'.$rs['descripcion'].'</td>
                                        <td>'.$rs['cant_pedida'].'</td>
                                        <td>'.$rs['orden'].'</td>
                                        <td>'.$rs['ffechadoc'].'</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>';
                    }
                }

                return $salida;
                
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }
    }
?>