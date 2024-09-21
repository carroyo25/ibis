<?php
    class AutorizaTrasladoModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function aprobarTraslado($id,$user){
            try {
                $respuesta = false;
                $mensaje = "No se pudo autorizar";

                $sql = $this->db->connect()->prepare("UPDATE alm_autorizacab 
                                                    SET alm_autorizacab.uautoriza = :user,
                                                        alm_autorizacab.nflgautoriza = :flag
                                                    WHERE 
                                                        alm_autorizacab.idreg = :indice");

                $sql->execute(["user"=>$user,"indice"=>$id,"flag"=>1]);

                if ($sql->rowCount() > 0) {
                    $respuesta = true;
                    $mensaje = "Traslado autorizado";
                }

                return array("respuesta"=>$respuesta, "mensaje"=>$mensaje);

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }  
        }
    }
?>