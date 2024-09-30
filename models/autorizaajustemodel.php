<?php
    class AutorizaAjusteModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function autorizarAjuste($datos){
            try {
                $salida = false;
                $mensaje = "Error al autorizar";

                $sql = $this->db->connect()->prepare("UPDATE alm_ajustecab
                                                    SET alm_ajustecab.idrecepciona =:user
                                                    WHERE  alm_ajustecab.idreg =:id
                                                    LIMIT 1");
                $sql->execute(["user"=>$datos['user'],"id"=>$datos['id']]);

                if( $sql->rowCount() > 0){
                    $salida = true;
                    $mensaje = "Ajuste autorizado";
                }

                return array("salida"=>$salida,"mensaje"=>$mensaje);

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }
    }
?>