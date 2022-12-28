<?php
    class PedidoMttoModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarEquipos(){
            try {
                $salida=array();

                $sql = $this->db->connect()->query("SELECT
                                                    tb_equipmtto.idreg,
                                                    CONCAT_WS(' / ',tb_equipmtto.cregistro,tb_equipmtto.cdescripcion) AS registro
                                                FROM
                                                    tb_equipmtto 
                                                WHERE
                                                    tb_equipmtto.nflgactivo = 1");
                $sql->execute();
                $rowCount = $sql->rowCount();


                if ($rowCount > 0){
                    while ($rs = $sql->fetch()) {
                        $item['valor']    =$rs['idreg'];
                        $item['registro'] =$rs['registro'];

                        array_push($salida,$item);

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