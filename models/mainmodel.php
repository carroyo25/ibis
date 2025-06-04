<?php 
    class MainModel extends Model {
        public function __construct()
        {
            parent::__construct();
        }

        public function ingresarSistema($user,$clave){
            try {
                $respuesta = false;
                $pass = $this->encryptPass($clave);

                $sql = $this->db->connect()->prepare("SELECT tb_user.iduser,
                                                             tb_user.cnameuser,
                                                             tb_user.cnombres,
                                                             tb_user.ccorreo,
                                                             tb_user.ccargo,
                                                             tb_user.cinicial,
                                                             tb_user.nrol,
                                                             tb_user.fusrmmtto,
                                                             tb_user.fusrmedica,
                                                             tb_user.fusralmacen
                                                        FROM tb_user 
                                                        WHERE 
                                                           tb_user.cnameuser=:user 
                                                            AND tb_user.cclave=:pass
                                                            AND tb_user.nestado=7");
                $sql->execute(["user"=>$user,"pass"=>$pass]);
                $rc = $sql->rowcount();
                $rq = $sql->fetchAll();

                if ($rc > 0){
                    $respuesta = true;
                    $_SESSION['iduser']     = $rq[0]['iduser'];
                    $_SESSION['user']       = $rq[0]['cnameuser'];
                    $_SESSION['nombres']    = $rq[0]['cnombres'];
                    $_SESSION['correo']     = $rq[0]['ccorreo'];
                    $_SESSION['cargo']      = $rq[0]['ccargo'];
                    $_SESSION['inicial']    = $rq[0]['cinicial'];
                    $_SESSION['rol']        = $rq[0]['nrol'];
                    $_SESSION['almacen']    = $rq[0]['fusralmacen'];
                    $_SESSION['medicos']    = $rq[0]['fusrmedica'];
                    $_SESSION['mmtto']      = $rq[0]['fusrmmtto'];
                    $_SESSION['password']   = "aK8izG1WEQwwB1X";
                }else{
                    $respuesta = false;
                    session_destroy();
                }

                return array("respuesta"=>$respuesta);

            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }


    }
?>
