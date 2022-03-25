<?php
    class Panelmodel extends Model{
        public function __construct(){
            parent::__construct();
        }

        public function acordeon($user){
            $salida = "";
            $opcion = $this->acordeonUL($user);
            $item = $this->acordeonLi($user);

            foreach ($opcion as $op){
                $salida .= '<li>
                                <a class="link">
                                    <i class="'.$op['cicono'].'"></i><span>'.$op['cdescripcion'].'</span><i class="fa fa-chevron-down"></i>
                                </a>
                                <ul class="submenu">';
                                foreach ($item as $it) {
                                    if($it['cclasmenu'] == $op['cclasmenu']){
                                        $salida .= '<li>
                                                        <a href="'.constant('URL').$it['cruta'].'" class="opcion">'.$it['cdescripcion'].'</a>
                                                    </li>';
                                    }
                                }
                $salida.='</ul></li>';
            }

            return $salida;
        }

        private function acordeonUl($user){
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                        sysmenu.cdescripcion,
                                                        sysmenu.cicono,
                                                        sysmenu.cruta,
                                                        sysmenu.cclasmenu 
                                                    FROM
                                                        tb_usermod
                                                        INNER JOIN sysmenu ON tb_usermod.classmenu = sysmenu.cclasmenu 
                                                    WHERE
                                                        tb_usermod.iduser = :user 
                                                        AND ISNULL( sysmenu.cruta ) 
                                                    GROUP BY
                                                        sysmenu.cdescripcion 
                                                    ORDER BY
                                                        sysmenu.cdescripcion");
                $sql->execute(["user"=>$user]);
                $result = $sql->fetchAll();

                return $result;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function acordeonLi($user){
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                        sysmenu.cdescripcion,
                                                        sysmenu.cruta,
                                                        sysmenu.cclasmenu 
                                                    FROM
                                                        tb_usermod
                                                        INNER JOIN sysmenu ON tb_usermod.ncodmod = sysmenu.ncodmenu 
                                                    WHERE
                                                        tb_usermod.iduser = :user 
                                                    ORDER BY
                                                        sysmenu.cclasmenu ASC");
                $sql->execute(["user"=>$user]);
                $result = $sql->fetchAll();

                return $result;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }
    }
?>