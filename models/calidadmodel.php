<?php
    class CalidadModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarNotasCalidad(){
            try {
                $salida = "";
                $sql = $this->db->connect()->prepare("SELECT
                                                        tb_costusu.id_cuser,
                                                        tb_costusu.ncodproy,
                                                        alm_recepcab.id_regalm,
                                                        alm_recepcab.ncodmov,
                                                        alm_recepcab.nnromov,
                                                        alm_recepcab.nnronota,
                                                        alm_recepcab.cper,
                                                        alm_recepcab.cmes,
                                                        alm_recepcab.ncodalm1,
                                                        alm_recepcab.ffecdoc,
                                                        alm_recepcab.id_centi,
                                                        alm_recepcab.cnumguia,
                                                        alm_recepcab.ncodpry,
                                                        alm_recepcab.ncodarea,
                                                        alm_recepcab.ncodcos,
                                                        alm_recepcab.idref_pedi,
                                                        alm_recepcab.idref_abas,
                                                        alm_recepcab.nEstadoDoc,
                                                        alm_recepcab.nflgCalidad,
                                                        UPPER( tb_almacen.cdesalm ) AS almacen,
                                                        UPPER( tb_proyectos.cdesproy ) AS proyecto,
                                                        UPPER( tb_area.cdesarea ) AS area,
                                                        lg_ordencab.cnumero AS orden,
                                                        LPAD( tb_pedidocab.nrodoc, 6, 0 ) pedido 
                                                    FROM
                                                        tb_costusu
                                                        INNER JOIN alm_recepcab ON tb_costusu.ncodproy = alm_recepcab.ncodpry
                                                        INNER JOIN tb_almacen ON alm_recepcab.ncodalm1 = tb_almacen.ncodalm
                                                        INNER JOIN tb_proyectos ON alm_recepcab.ncodpry = tb_proyectos.nidreg
                                                        INNER JOIN tb_area ON alm_recepcab.ncodarea = tb_area.ncodarea
                                                        INNER JOIN lg_ordencab ON alm_recepcab.idref_abas = lg_ordencab.id_regmov
                                                        INNER JOIN tb_pedidocab ON alm_recepcab.idref_pedi = tb_pedidocab.idreg 
                                                    WHERE
                                                        tb_costusu.id_cuser = :usr 
                                                        AND tb_costusu.nflgactivo = 1
                                                        AND alm_recepcab.nEstadoDoc = 61");
                $sql->execute(["usr"=>$_SESSION['iduser']]);
                $rowCount = $sql->rowcount();
                if ($rowCount > 0){
                    while($rs = $sql->fetch()){
                        $salida.='<tr class="pointer" data-indice="'.$rs['id_regalm'].'">
                                    <td class="textoCentro">'.$rs['nnronota'].'</td>
                                    <td class="textoCentro">'.date("d/m/Y", strtotime($rs['ffecdoc'])).'</td>
                                    <td class="pl20px">'.$rs['almacen'].'</td>
                                    <td class="pl20px">'.$rs['proyecto'].'</td>
                                    <td class="pl20px">'.$rs['area'].'</td>
                                    <td class="textoCentro">'.$rs['orden'].'</td>
                                    <td class="textoCentro">'.$rs['pedido'].'</td>
                                </tr>';
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }

        public function grabarCalidad($detalles){
                $datos = json_decode($detalles);
                $nreg = count($datos);

                for ($i=0; $i < $nreg; $i++) { 
                    try {
                        $sql = $this->db->connect()->prepare("UPDATE alm_recepdet SET nestadoreg=:estado WHERE niddeta=:id");
                        $sql->execute(["estado"=>$datos[$i]->nestado,
                                        "id"=>$datos[$i]->iddetnota]);
                    } catch (PDOException $th) {
                        echo "Error: " . $th->getMessage();
                        return false;
                    }  
                }
        }

        public function liberar_nota($id,$estado,$detalles){
            try {
                $sql = $this->db->connect()->prepare("UPDATE alm_recepcab SET nEstadoDoc=:estado WHERE id_regalm = :id");
                $sql->execute(["estado"=>$estado,"id"=>$id]);
                $rowCount = $sql->rowCount();
                
                if ($rowCount > 0) {
                    $this->grabarCalidad($detalles);
                }

            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }
    }
?>