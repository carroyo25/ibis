<?php
    class UsuariosModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarUsuarios() {
            try {
                $salida = "";
                $sql = $this->db->connect()->query("SELECT
                                                    ibis.tb_user.iduser,
                                                    ibis.tb_user.cnameuser,
                                                    ibis.tb_user.cnombres,
                                                    ibis.tb_user.cclave,
                                                    ibis.tb_user.ncodper,
                                                    ibis.tb_user.nrol,
                                                    ibis.tb_user.ccorreo,
                                                    ibis.tb_user.fvigdesde,
                                                    ibis.tb_user.fvighasta,
                                                    ibis.tb_user.cinicial,
                                                    ibis.tb_user.nestado,
                                                    ibis.tb_user.freg,
                                                    ibis.tb_user.ccargo,
                                                    tiporol.cdescripcion AS nivel,
                                                    estados.cdescripcion AS estado,
                                                    rrhh.tabla_aquarius.apellidos,
                                                    rrhh.tabla_aquarius.nombres 
                                                FROM
                                                    ibis.tb_user
                                                    INNER JOIN ibis.tb_parametros AS tiporol ON tb_user.nrol = tiporol.nidreg
                                                    INNER JOIN ibis.tb_parametros AS estados ON tb_user.nestado = estados.nidreg
                                                    INNER JOIN rrhh.tabla_aquarius ON ibis.tb_user.ncodper = rrhh.tabla_aquarius.internal 
                                                WHERE
                                                    tiporol.cclase = '00' 
                                                    AND estados.cclase = '01'
                                                    AND ibis.tb_user.nflgactivo = 1
                                                ORDER BY tb_user.cnameuser");
                $sql->execute();
                $rc = $sql->rowcount();
                $c = 1;

                if ($rc > 0){
                    while ($rs = $sql->fetch()) {
                        $salida.='<tr data-user="'.$rs['iduser'].'" class="pointer">
                                    <td class="textoCentro">'.str_pad($c++,3,0,STR_PAD_LEFT).'</td>
                                    <td class="pl10px">'.strtoupper($rs['cnameuser']).'</td>
                                    <td class="pl10px">'.strtoupper($rs['nombres'].' '.$rs['apellidos']).'</td>
                                    <td class="pl10px">'.$rs['nivel'].'</td>
                                    <td class="textoCentro '.strtolower($rs['estado']).'">'.$rs['estado'].'</td>
                                    <td class="textoCentro">'.$rs['fvigdesde'].'</td>
                                    <td class="textoCentro">'.$rs['fvighasta'].'</td>
                                    <td class="textoCentro"><a href="'.$rs['iduser'].'" data-action="s"><i class="fas fa-eye"></i></a></td>
                                    <td class="textoCentro"><a href="'.$rs['iduser'].'" data-action="u"><i class="far fa-edit"></i></a></td>
                                    <td class="textoCentro"><a href="'.$rs['iduser'].'" data-action="d"><i class="far fa-trash-alt"></i></a></td>
                                 </tr>';
                    }
                    
                }else{
                    $salida = '<tr><td colspan="10" class="textoCentro">No se registraron usuarios</td></tr>';
                }

                return $salida;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function listarModulos() {
            try {
                $sql= $this->db->connect()->query("SELECT ncodmenu,cclasmenu,copcion,cdescripcion,cicono FROM sysmenu");
                $sql->execute();
                $rc = $sql->rowcount();
                $salida = "";
                $item = 1;

                if ($rc > 0) {
                    while ($rs = $sql->fetch()) {

                        $separador = $rs['copcion'] == '00' ? 'separadorItem': 'tablaPointer' ;

                        $salida .='<tr data-ncodmenu="'.$rs['ncodmenu'].'" class="'.$separador.'" data-tipo="'.$rs['cclasmenu'].'" data-opcion="'.$rs['copcion'].'">
                                        <td class="textoCentro">'.str_pad($item++,3,0,STR_PAD_LEFT).'</td>
                                        <td class="pl10px">'.$rs['cdescripcion'].'</td>
                                    </tr>'; 
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function listarCostos() {
            try {
                $salida = "";
                $sql= $this->db->connect()->query("SELECT nidreg,ccodproy,cdesproy FROM tb_proyectos WHERE nflgactivo = 1");
                $sql->execute();

                if ($sql->rowcount() > 0){
                    while ($rs = $sql->fetch()) {
                        $salida .='<tr data-id="'.$rs['nidreg'].'" class="tablaPointer">
                                    <td class="textoCentro">'.$rs['ccodproy'].'</td>
                                    <td class="pl10px">'.strtoupper(utf8_encode($rs['cdesproy'])).'</td>
                                 </tr>';
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function listarAlmacen() {
            try {
                $salida = "";
                $sql= $this->db->connect()->query("SELECT ncodalm,cdesalm FROM tb_almacen WHERE nflgactivo = 1");
                $sql->execute();

                if ($sql->rowcount() > 0){
                    while ($rs = $sql->fetch()) {
                        $salida .='<tr data-id="'.$rs['ncodalm'].'" class="tablaPointer">
                                    <td class="textoCentro">'.str_pad($rs['ncodalm'],2,0,STR_PAD_LEFT).'</td>
                                    <td class="pl10px">'.strtoupper($rs['cdesalm']).'</td>
                                 </tr>';
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function verificarUsuario($usuario){
            try {
                $sql = $this->db->connect()->prepare("SELECT iduser FROM tb_user WHERE cnameuser=:user");
                $sql->execute(["user"=>$usuario]);
                $rowcount = $sql->rowcount();

                if ($rowcount > 0) {
                    return true;
                }

            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }
        
        public function insertarUsuario($cabecera,$modulos,$costos,$almacenes){
            $salida = false;
            try {

                if ( $this->verificarUsuario($cabecera['usuario'])) {
                    $salida = array("respuesta"=>false,
                                    "mensaje"=>"El usuario ya existe");
                }else {
                    $id = uniqid();

                    $sql = $this->db->connect()->prepare("INSERT INTO tb_user 
                                                        SET iduser=:id,
                                                            cnameuser=:user,
                                                            cnombres=:nombres,
                                                            cclave=:clave,
                                                            ncodper=:internal,
                                                            nrol=:rol,
                                                            ccorreo=:correo,
                                                            fvigdesde=:fdesde,
                                                            fvighasta=:fhasta,
                                                            cinicial=:iniciales,
                                                            nestado=:estado");
                    $sql->execute(["id"=>$id,
                                    "user"=>$cabecera['usuario'],
                                    "clave"=>$this->encryptPass($cabecera['clave']),
                                    "internal"=>$cabecera['cod_resp'],
                                    "rol"=>$cabecera['cod_niv'],
                                    "correo"=>$cabecera['correo'],
                                    "fdesde"=>$cabecera['desde'],
                                    "fhasta"=>$cabecera['hasta'],
                                    "estado"=>$cabecera['cod_est'],
                                    "iniciales"=>$cabecera['user_inic'],
                                    "nombres"=>$cabecera['nombre']]);
                    $rowcount = $sql->rowcount();
    
                    if ($rowcount) {
                        
                        $this->grabarModulos($id,$modulos);
                        $this->grabarCostos($id,$costos);
                        $this->grabarAlmacenes($id,$almacenes);
                        
                        $salida = array("respuesta"=>true,
                                        "mensaje"=>"Usuario creado correctammente");
                    }else {
                        $salida = array("respuesta"=>false,
                                        "mensaje"=>"Error.. al crear el usuario");
                    }
                };
                return $salida;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function consultarUsuario($id){
            
            try {
                $sql = $this->db->connect()->prepare("SELECT ibis.tb_user.iduser,
                                                    ibis.tb_user.cnameuser,
                                                    ibis.tb_user.cclave,
                                                    ibis.tb_user.ncodper,
                                                    ibis.tb_user.nrol,
                                                    ibis.tb_user.ccorreo,
                                                    ibis.tb_user.fvigdesde,
                                                    ibis.tb_user.fvighasta,
                                                    ibis.tb_user.cinicial,
                                                    ibis.tb_user.nestado,
                                                    ibis.tb_user.freg,
                                                    ibis.tb_user.ccargo,
                                                    tiporol.cdescripcion AS nivel,
                                                    estados.cdescripcion AS estado,
                                                    rrhh.tabla_aquarius.apellidos,
                                                    rrhh.tabla_aquarius.nombres,
                                                    rrhh.tabla_aquarius.dcargo, 
	                                                rrhh.tabla_aquarius.ccargo 
                                                FROM
                                                    ibis.tb_user
                                                    INNER JOIN ibis.tb_parametros AS tiporol ON tb_user.nrol = tiporol.nidreg
                                                    INNER JOIN ibis.tb_parametros AS estados ON tb_user.nestado = estados.nidreg
                                                    INNER JOIN rrhh.tabla_aquarius ON ibis.tb_user.ncodper = rrhh.tabla_aquarius.internal 
                                                WHERE
                                                    tiporol.cclase = '00' 
                                                    AND estados.cclase = '01' 
                                                    AND ibis.tb_user.iduser = :id");
                $sql->execute(["id"=>$id]);
                $rowCount = $sql->rowCount();
                
                if ($rowCount > 0) {
                    $docData = array();
                    while($row=$sql->fetch(PDO::FETCH_ASSOC)){
                        $docData[] = $row;
                    }
                }

                return array("cabecera"=>$docData,
                            "modulos"=>$this->listarModulosUsuario($id),
                            "almacen"=>$this->listarAlmacenUsuario($id),
                            "costos"=>$this->listarCostosUsuario($id));
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function actualizarUsuario($cabecera,$modulos,$costos,$almacenes){
            try {
                if ( $cabecera['old_pass']  == $cabecera['clave']){
                    $clave_actual = $cabecera['clave'];
                }else{
                    $clave_actual = $this->encryptPass($cabecera['clave']);
                }

                $sql = $this->db->connect()->prepare("UPDATE tb_user 
                                                        SET cnameuser=:user,
                                                            cclave=:clave,
                                                            ncodper=:internal,
                                                            nrol=:rol,
                                                            ccorreo=:correo,
                                                            fvigdesde=:fdesde,
                                                            fvighasta=:fhasta,
                                                            cinicial=:iniciales,
                                                            nestado=:estado
                                                        WHERE iduser=:id ");
                
                $sql->execute(["id"=>$cabecera['cod_user'],
                                "user"=>$cabecera['usuario'],
                                "clave"=>$clave_actual,
                                "internal"=>$cabecera['cod_resp'],
                                "rol"=>$cabecera['cod_niv'],
                                "correo"=>$cabecera['correo'],
                                "fdesde"=>$cabecera['desde'],
                                "fhasta"=>$cabecera['hasta'],
                                "estado"=>$cabecera['cod_est'],
                                "iniciales"=>$cabecera['user_inic']]);

                $this->grabarModulos($cabecera['cod_user'],$modulos);
                $this->grabarCostos($cabecera['cod_user'],$costos);
                $this->grabarAlmacenes($cabecera['cod_user'],$almacenes);

                $salida = array("respuesta"=>true,
                                "mensaje"=>"Usuario modificado");
                
                return $salida;

            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function quitarItem($id,$modulo,$usuario){
            try {
                if ($modulo == 1){
                    $query = "UPDATE tb_usermod SET flgactivo  = 0 WHERE idreg =:id AND iduser=:usr";
                }else if ($modulo == 2){
                    $query = "UPDATE tb_costusu SET nflgactivo = 0 WHERE ncodcos =:id AND id_cuser=:usr";
                }else if ($modulo == 3){
                    $query = "UPDATE tb_almausu SET nflgactivo = 0 WHERE ncodalm =:id AND id_cuser=:usr";
                }
                $sql = $this->db->connect()->prepare($query);
                $sql->execute(["id"=>$id,"usr"=>$usuario]);
                $rowCount=$sql->fetchAll();

                if ($rowCount > 0){
                    echo "borrado";
                }else {
                    echo 'Huy problemas';
                }
                
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function mostrarClave($id){
            try {
                $sql = $this->db->connect()->prepare("SELECT cclave FROM tb_user WHERE iduser=:id LIMIT 1");
                $sql->execute(["id"=>$id]);
                
                $result = $sql->fetchAll();

                $clave = $this->decryptPass($result[0]['cclave']);

                return $clave;

            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function grabarModulos($id,$modulos){
            $data = json_decode($modulos);

            for ($i=0; $i < count($data); $i++) { 
                try {
                    $select = "SELECT COUNT( * ) AS contador FROM tb_usermod WHERE ncodmod = :id AND iduser = :usr AND flgactivo = 1";

                    $sw = $this->verificaExisteItem($data[$i]->codm,$data[$i]->iduser,$select);

                    if ($sw == 0) {
                        $sql = $this->db->connect()->prepare("INSERT INTO tb_usermod 
                                                                SET iduser=:id,
                                                                    ncodmod=:codigo,
                                                                    classmenu=:clase,
                                                                    copcion=:opcion");
                        $sql->execute(["id"=>$id,
                            "codigo"=>$data[$i]->codm,
                            "clase"=>$data[$i]->clas,
                            "opcion"=>$data[$i]->opci]);
                    }

                   
                } catch (PDOException $th) {
                    echo $th->getMessage();
                    return false;
                }
            }
        }

        private function grabarCostos($id,$costos){
            $data = json_decode($costos);
            try {
                for ($i=0; $i < count($data); $i++) { 
                    
                    $select = "SELECT COUNT( * ) AS contador FROM tb_costusu WHERE ncodproy = :id AND id_cuser = :usr AND nflgactivo = 1";
                    $sw = $this->verificaExisteItem($data[$i]->codpr,$data[$i]->iduser,$select);
                    
                    if ($sw == 0){
                        $sql = $this->db->connect()->prepare("INSERT INTO tb_costusu SET ncodproy=:cod,id_cuser=:usr,nflgactivo=:est");
                        $sql->execute(["cod"=>$data[$i]->codpr,
                                        "usr"=>$id,
                                        "est"=>1]);
                    }
                   
                }
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function grabarAlmacenes($id,$almacenes){
            $data = json_decode($almacenes);
            
            try {
                for ($i=0; $i < count($data); $i++) { 

                    $select = "SELECT COUNT( ncodalm ) AS contador FROM tb_almausu WHERE nalmacen = :id AND id_cuser = :usr AND nflgactivo = 1";
                    $sw = $this->verificaExisteItem($data[$i]->codalm,$data[$i]->iduser,$select);

                    if ($sw == 0) {
                        $sql = $this->db->connect()->prepare("INSERT INTO tb_almausu SET nalmacen=:cod,id_cuser=:usr,nflgactivo=:est");
                        $sql->execute(["cod"=>$data[$i]->codalm,
                                    "usr"=>$id,
                                    "est"=>1]);
                    }
                    
                }
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function listarAlmacenUsuario($codigo) {
            $salida = "";
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                        tb_almausu.ncodalm,
                                                        tb_almausu.nalmacen,
                                                        tb_almausu.id_cuser,
                                                        tb_almausu.nflgactivo,
                                                        UPPER(tb_almacen.cdesalm) AS descripcion 
                                                    FROM
                                                        tb_almausu
                                                        INNER JOIN tb_almacen ON tb_almausu.nalmacen = tb_almacen.ncodalm 
                                                    WHERE
                                                        tb_almausu.id_cuser = :id 
                                                        AND tb_almausu.nflgactivo = 1");
                $sql->execute(["id"=>$codigo]);
                $rowCount = $sql->rowCount();
                $filas = 1;

                if($rowCount > 0) {
                    while ($rs = $sql->fetch()) {
                        $salida .= '<tr data-grabado="1" data-codigo="'.$rs['nalmacen'].'">
                                            <td class="textoCentro"><a href="'.$rs['ncodalm'].'"><i class="fas fa-eraser"></i></a></td>
                                            <td class="textoCentro">'.str_pad($filas++,2,0,STR_PAD_LEFT).'</td>
                                            <td class="pl10px">'.$rs['descripcion'].'</td>
                                    </tr>';
                    }
                }
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
            return $salida;
        }

        private function listarModulosUsuario($codigo) {
            $salida = "";
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                        tb_usermod.idreg,
                                                        tb_usermod.ncodmod,
                                                        tb_usermod.classmenu,
                                                        tb_usermod.copcion,
                                                        tb_usermod.agrega,
                                                        tb_usermod.modifica,
                                                        tb_usermod.elimina,
                                                        tb_usermod.imprime,
                                                        tb_usermod.procesa,
                                                        tb_usermod.visible,
                                                        tb_usermod.todos,
                                                        sysmenu.cdescripcion AS modulo 
                                                    FROM
                                                        tb_usermod
                                                        INNER JOIN sysmenu ON tb_usermod.ncodmod = sysmenu.ncodmenu 
                                                    WHERE
                                                        iduser = :id
                                                    AND flgactivo = 1");
                $sql->execute(["id"=>$codigo]);
                $rowCount = $sql->rowCount();
                $filas = 1;

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()) {

                        $add = $rs['agrega'] == 1 ? "checked":"";
                        $mod = $rs['modifica'] == 1 ? "checked":"";
                        $del = $rs['elimina'] == 1 ? "checked":"";
                        $prn = $rs['imprime'] == 1 ? "checked":"";
                        $pro = $rs['procesa'] == 1 ? "checked":"";
                        $ver = $rs['visible'] == 1 ? "checked":"";
                        $all = $rs['todos'] == 1 ? "checked":"";

                        $salida .= '<tr data-grabado="1" data-codigo="'.$rs['ncodmod'].'" data-clase="'.$rs['classmenu'].'" data-opcion="'.$rs['copcion'].'">
                                        <td class="textoCentro"><a href="'.$rs['idreg'].'"><i class="fas fa-eraser"></i></a> </td>
                                        <td class="textoCentro">'.str_pad($filas++,2,0,STR_PAD_LEFT).'</td>
                                        <td class="pl10px">'.$rs['modulo'].'</td>
                                        <td class="textoCentro"><input type="checkbox" '.$add.'></td>
                                        <td class="textoCentro"><input type="checkbox" '.$mod.'></td>
                                        <td class="textoCentro"><input type="checkbox" '.$del.'></td>
                                        <td class="textoCentro"><input type="checkbox" '.$prn.'></td>
                                        <td class="textoCentro"><input type="checkbox" '.$pro.'></td>
                                        <td class="textoCentro"><input type="checkbox" '.$ver.'></td>
                                        <td class="textoCentro"><input type="checkbox" '.$all.'></td>
                                    </tr>';
                    }
                }
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
            return $salida;
        }

        private function listarCostosUsuario($codigo) {
            $salida = "";
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                        tb_costusu.ncodcos,
                                                        UPPER( tb_proyectos.cdesproy ) AS descripcion,
                                                        tb_proyectos.ccodproy 
                                                    FROM
                                                        tb_costusu
                                                        INNER JOIN tb_proyectos ON tb_costusu.ncodproy = tb_proyectos.nidreg 
                                                    WHERE
                                                        tb_costusu.id_cuser = :id
                                                    AND tb_costusu.nflgactivo = 1");
                $sql->execute(["id"=>$codigo]);
                $rowCount = $sql->rowCount();
                $filas = 1;

                if($rowCount > 0) {
                    while ($rs = $sql->fetch()) {
                        $salida .= '<tr data-grabado="1" data-codigo="'.$rs['ncodcos'].'">
                                            <td class="textoCentro"><a href="'.$rs['ncodcos'].'"><i class="fas fa-eraser"></i></a> </td>
                                            <td class="textoCentro">'.$rs['ccodproy'].'</td>
                                            <td class="pl10px">'.$rs['descripcion'].'</td>
                                    </tr>';
                    }
                }
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }

            return $salida;
        }

        private function verificaExisteItem($id,$user,$query) {
            try {
                $sql = $this->db->connect()->prepare($query);
                $sql->execute(["id"=>$id,"usr"=>$user]);
                $result = $sql->fetchAll();

                return $result[0]['contador'];

            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }
    }
?>