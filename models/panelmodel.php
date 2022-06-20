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
                                                        AND sysmenu.copcion	= '00'
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
                                                        tb_usermod.iduser = :user AND flgactivo = 1 
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

        public function listarPanelPedidos(){
            try {
                $valores = [];
                $salida ="";
                $proceso = 0;
                $consulta = 0;
                $atendido = 0;
                $aprobacion = 0;
                $aprobado = 0;
                $cotizando = 0;
                $etiquetas = ["Proceso", "Consulta", "Atendido", "Aprobacion", "Aprobado","Culminados"];


                $sql = $this->db->connect()->prepare("SELECT
                                                        tb_pedidocab.emision,
                                                        tb_pedidocab.estadodoc,
                                                        LPAD(tb_pedidocab.nrodoc, 6, 0 ) AS pedido,
                                                        UPPER(tb_pedidocab.concepto) AS concepto,
                                                        UPPER(tb_proyectos.cdesproy) AS proyecto,
                                                        tb_parametros.cdescripcion,
                                                        tb_parametros.cabrevia 
                                                    FROM
                                                        tb_pedidocab
                                                        INNER JOIN tb_proyectos ON tb_pedidocab.idcostos = tb_proyectos.nidreg
                                                        INNER JOIN tb_parametros ON tb_pedidocab.estadodoc = tb_parametros.nidreg 
                                                    WHERE
                                                        tb_pedidocab.usuario = :user");
                $sql->execute(["user"=>$_SESSION['iduser']]);
                $rowcount = $sql->rowcount();

                if ($rowcount > 0) {
                    while ($rs = $sql->fetch()) {
                        $salida .= '<tr>
                                        <td class="textoCentro">'.$rs['pedido'].'</td>
                                        <td class="pl20px">'.$rs['concepto'].'</td>
                                        <td class="textoCentro">'.$rs['emision'].'</td>
                                        <td class="pl20px">'.$rs['proyecto'].'</td>
                                        <td class="textoCentro '.$rs['cabrevia'].'">'.$rs['cdescripcion'].'</td>
                                    </tr>';
                        
                        if ($rs['estadodoc'] == 49){ //procesando
                            $proceso++;
                        }else if($rs['estadodoc'] == 51) {
                            $consulta++;
                        }else if($rs['estadodoc'] == 52) {
                            $atendido++;
                        }else if($rs['estadodoc'] == 53) {
                            $aprobacion++;
                        }else if($rs['estadodoc'] == 54) {
                            $aprobado++;
                        }else if($rs['estadodoc'] == 55) {
                            $cotizando++;
                        }
                        
                    }

                    array_push($valores,$proceso);
                    array_push($valores,$consulta);
                    array_push($valores,$atendido);
                    array_push($valores,$aprobacion);
                    array_push($valores,$aprobado);
                    array_push($valores,$cotizando);
                }

                return array("contenido"=>$salida,
                              "valores"=>$valores,
                              "etiquetas"=>$etiquetas);
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            } 

        }


    }
?>