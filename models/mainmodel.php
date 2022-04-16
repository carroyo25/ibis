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

                $sql = $this->db->connect()->prepare("SELECT iduser,cnameuser,cnombres,ccorreo,ccargo,cinicial 
                                                        FROM tb_user 
                                                        WHERE cnameuser=:user AND cclave=:pass");
                $sql->execute(["user"=>$user,"pass"=>$pass]);
                $rc = $sql->rowcount();
                $rq = $sql->fetchAll();

                if ($rc > 0){
                    $respuesta = true;
                    $_SESSION['iduser']     = $rq[0]['iduser'];
                    $_SESSION['user']       = $rq[0]['cnameuser'];
                    $_SESSION['nombres']    = $rq[0]['cnombres'];
                    $_SESSION['correo']     = $rq[0]['ccorreo'];
                    $_SESSION['cargo']     = $rq[0]['ccargo'];
                    $_SESSION['inicial']     = $rq[0]['cinicial'];
                    $_SESSION['password']   = "aK8izG1WEQwwB1X";
                }

                return $respuesta;

            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }


    }
?>
