<?php
    setlocale(LC_MONETARY, 'en_US');
    class Model{
        function __construct()
        {
            $this->db = new Database();
        }

        //funcion para convertir numeros
        public function unidad($numuero){
            switch ($numuero)
            {
                case 9:
                {
                    $numu = "NUEVE";
                    break;
                }
                case 8:
                {
                    $numu = "OCHO";
                    break;
                }
                case 7:
                {
                    $numu = "SIETE";
                    break;
                }
                case 6:
                {
                    $numu = "SEIS";
                    break;
                }
                case 5:
                {
                    $numu = "CINCO";
                    break;
                }
                case 4:
                {
                    $numu = "CUATRO";
                    break;
                }
                case 3:
                {
                    $numu = "TRES";
                    break;
                }
                case 2:
                {
                    $numu = "DOS";
                    break;
                }
                case 1:
                {
                    $numu = "UNO";
                    break;
                }
                case 0:
                {
                    $numu = "";
                    break;
                }
            }
            return $numu;
        }
         
        public function decena($numdero){
         
                if ($numdero >= 90 && $numdero <= 99)
                {
                    $numd = "NOVENTA ";
                    if ($numdero > 90)
                        $numd = $numd."Y ".($this->unidad($numdero - 90));
                }
                else if ($numdero >= 80 && $numdero <= 89)
                {
                    $numd = "OCHENTA ";
                    if ($numdero > 80)
                        $numd = $numd."Y ".($this->unidad($numdero - 80));
                }
                else if ($numdero >= 70 && $numdero <= 79)
                {
                    $numd = "SETENTA ";
                    if ($numdero > 70)
                        $numd = $numd."Y ".($this->unidad($numdero - 70));
                }
                else if ($numdero >= 60 && $numdero <= 69)
                {
                    $numd = "SESENTA ";
                    if ($numdero > 60)
                        $numd = $numd."Y ".($this->unidad($numdero - 60));
                }
                else if ($numdero >= 50 && $numdero <= 59)
                {
                    $numd = "CINCUENTA ";
                    if ($numdero > 50)
                        $numd = $numd."Y ".($this->unidad($numdero - 50));
                }
                else if ($numdero >= 40 && $numdero <= 49)
                {
                    $numd = "CUARENTA ";
                    if ($numdero > 40)
                        $numd = $numd."Y ".($this->unidad($numdero - 40));
                }
                else if ($numdero >= 30 && $numdero <= 39)
                {
                    $numd = "TREINTA ";
                    if ($numdero > 30)
                        $numd = $numd."Y ".($this->unidad($numdero - 30));
                }
                else if ($numdero >= 20 && $numdero <= 29)
                {
                    if ($numdero == 20)
                        $numd = "VEINTE ";
                    else
                        $numd = "VEINTI".($this->unidad($numdero - 20));
                }
                else if ($numdero >= 10 && $numdero <= 19)
                {
                    switch ($numdero){
                    case 10:
                    {
                        $numd = "DIEZ ";
                        break;
                    }
                    case 11:
                    {
                        $numd = "ONCE ";
                        break;
                    }
                    case 12:
                    {
                        $numd = "DOCE ";
                        break;
                    }
                    case 13:
                    {
                        $numd = "TRECE ";
                        break;
                    }
                    case 14:
                    {
                        $numd = "CATORCE ";
                        break;
                    }
                    case 15:
                    {
                        $numd = "QUINCE ";
                        break;
                    }
                    case 16:
                    {
                        $numd = "DIECISEIS ";
                        break;
                    }
                    case 17:
                    {
                        $numd = "DIECISIETE ";
                        break;
                    }
                    case 18:
                    {
                        $numd = "DIECIOCHO ";
                        break;
                    }
                    case 19:
                    {
                        $numd = "DIECINUEVE ";
                        break;
                    }
                    }
                }
                else
                    $numd = $this->unidad($numdero);
            return $numd;
        }
         
        public function centena($numc){
                if ($numc >= 100)
                {
                    if ($numc >= 900 && $numc <= 999)
                    {
                        $numce = "NOVECIENTOS ";
                        if ($numc > 900)
                            $numce = $numce.($this->decena($numc - 900));
                    }
                    else if ($numc >= 800 && $numc <= 899)
                    {
                        $numce = "OCHOCIENTOS ";
                        if ($numc > 800)
                            $numce = $numce.($this->decena($numc - 800));
                    }
                    else if ($numc >= 700 && $numc <= 799)
                    {
                        $numce = "SETECIENTOS ";
                        if ($numc > 700)
                            $numce = $numce.($this->decena($numc - 700));
                    }
                    else if ($numc >= 600 && $numc <= 699)
                    {
                        $numce = "SEISCIENTOS ";
                        if ($numc > 600)
                            $numce = $numce.($this->decena($numc - 600));
                    }
                    else if ($numc >= 500 && $numc <= 599)
                    {
                        $numce = "QUINIENTOS ";
                        if ($numc > 500)
                            $numce = $numce.($this->decena($numc - 500));
                    }
                    else if ($numc >= 400 && $numc <= 499)
                    {
                        $numce = "CUATROCIENTOS ";
                        if ($numc > 400)
                            $numce = $numce.($this->decena($numc - 400));
                    }
                    else if ($numc >= 300 && $numc <= 399)
                    {
                        $numce = "TRESCIENTOS ";
                        if ($numc > 300)
                            $numce = $numce.($this->decena($numc - 300));
                    }
                    else if ($numc >= 200 && $numc <= 299)
                    {
                        $numce = "DOSCIENTOS ";
                        if ($numc > 200)
                            $numce = $numce.($this->decena($numc - 200));
                    }
                    else if ($numc >= 100 && $numc <= 199)
                    {
                        if ($numc == 100)
                            $numce = "CIEN ";
                        else
                            $numce = "CIENTO ".($this->decena($numc - 100));
                    }
                }
                else
                    $numce = $this->decena($numc);
         
                return $numce;
        }
         
        public function miles($nummero){
                if ($nummero >= 1000 && $nummero < 2000){
                    $numm = "MIL ".($this->centena($nummero%1000));
                }
                if ($nummero >= 2000 && $nummero <10000){
                    $numm = $this->unidad(Floor($nummero/1000))." MIL ".($this->centena($nummero%1000));
                }
                if ($nummero < 1000)
                    $numm = $this->centena($nummero);
         
                return $numm;
            }
         
        public function decmiles($numdmero){
                if ($numdmero == 10000)
                    $numde = "DIEZ MIL";
                if ($numdmero > 10000 && $numdmero <20000){
                    $numde = $this->decena(Floor($numdmero/1000))."MIL ".($this->centena($numdmero%1000));
                }
                if ($numdmero >= 20000 && $numdmero <100000){
                    $numde = $this->decena(Floor($numdmero/1000))." MIL ".($this->miles($numdmero%1000));
                }
                if ($numdmero < 10000)
                    $numde = $this->miles($numdmero);
         
                return $numde;
            }
         
        public function cienmiles($numcmero){
                if ($numcmero == 100000)
                    $num_letracm = "CIEN MIL";
                if ($numcmero >= 100000 && $numcmero <1000000){
                    $num_letracm = $this->centena(Floor($numcmero/1000))." MIL ".($this->centena($numcmero%1000));
                }
                if ($numcmero < 100000)
                    $num_letracm = $this->decmiles($numcmero);
                return $num_letracm;
            }
         
        public function millon($nummiero){
                if ($nummiero >= 1000000 && $nummiero <2000000){
                    $num_letramm = "UN MILLON ".($this->cienmiles($nummiero%1000000));
                }
                if ($nummiero >= 2000000 && $nummiero <10000000){
                    $num_letramm = $this->unidad(Floor($nummiero/1000000))." MILLONES ".($this->cienmiles($nummiero%1000000));
                }
                if ($nummiero < 1000000)
                    $num_letramm = $this->cienmiles($nummiero);
         
                return $num_letramm;
        }
         
        public function decmillon($numerodm){
                if ($numerodm == 10000000)
                    $num_letradmm = "DIEZ MILLONES";
                if ($numerodm > 10000000 && $numerodm <20000000){
                    $num_letradmm = $this->decena(Floor($numerodm/1000000))."MILLONES ".($this->cienmiles($numerodm%1000000));
                }
                if ($numerodm >= 20000000 && $numerodm <100000000){
                    $num_letradmm = $this->decena(Floor($numerodm/1000000))." MILLONES ".($this->millon($numerodm%1000000));
                }
                if ($numerodm < 10000000)
                    $num_letradmm = $this->millon($numerodm);
         
                return $num_letradmm;
            }
         
        public function cienmillon($numcmeros){
                if ($numcmeros == 100000000)
                    $num_letracms = "CIEN MILLONES";
                if ($numcmeros >= 100000000 && $numcmeros <1000000000){
                    $num_letracms = $this->centena(Floor($numcmeros/1000000))." MILLONES ".($this->millon($numcmeros%1000000));
                }
                if ($numcmeros < 100000000)
                    $num_letracms = $this->decmillon($numcmeros);
                return $num_letracms;
            }
         
        public function milmillon($nummierod){
                if ($nummierod >= 1000000000 && $nummierod <2000000000){
                    $num_letrammd = "MIL ".($this->cienmillon($nummierod%1000000000));
                }
                if ($nummierod >= 2000000000 && $nummierod <10000000000){
                    $num_letrammd = $this->unidad(Floor($nummierod/1000000000))." MIL ".($this->cienmillon($nummierod%1000000000));
                }
                if ($nummierod < 1000000000)
                    $num_letrammd = $this->cienmillon($nummierod);
         
                return $num_letrammd;
        }
         
         
        public function convertir($numero){
            $num = str_replace(",","",$numero);
            $num = number_format($num,2,'.','');
            $cents = substr($num,strlen($num)-2,strlen($num)-1);
            $num = (int)$num;

            $numf = $this->milmillon($num);
         
            return $numf." y ".$cents."/100";
        }

        //grabar las acciones de los modulos
        public function saveAction($accion,$codigo,$modulo,$user){
            try {
                $query = $this->db->connect()->prepare("INSERT INTO lg_seguimiento SET cmodulo=:cmod,id_regmov=:cod,cproceso=:acc,id_cuser=:usr");
                $query->execute(["cmod" => $modulo,
                                 "cod"  => $codigo,
                                 "acc"  => $accion,
                                 "usr"  => $user]);
            } catch (PDOException $e) {
                echo $e->getMessage();
                return false;
            }
        }

        //obtener el ultimo id creado
        public function lastInsertId($query) {
            try {
                $sql = $this->db->connect()->query($query);
                $sql->execute();
                $result = $sql->fetchAll();
                
                return $result[0]['id'];
            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;

            }
        }

        public function encryptPass($password){
            $sSalt = '20adeb83e85f03cfc84d0fb7e5f4d290';
            $sSalt = substr(hash('sha256', $sSalt, true), 0, 32);
            $method = 'aes-256-cbc';
        
            $iv = chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0);
        
            $encrypted = base64_encode(openssl_encrypt($password, $method, $sSalt, OPENSSL_RAW_DATA, $iv));
            return $encrypted;
        }

        public function decryptPass($password){
            $sSalt = '20adeb83e85f03cfc84d0fb7e5f4d290';
            $sSalt = substr(hash('sha256', $sSalt, true), 0, 32);
            $method = 'aes-256-cbc';
        
            $iv = chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0);
        
            $decrypted = openssl_decrypt(base64_decode($password), $method, $sSalt, OPENSSL_RAW_DATA, $iv);
            return $decrypted;
        }

        //llamar valores de la tabla general al pasar un parametro
        public function listarParametros($clase) {
            try {
                $salida = "";
                $query = $this->db->connect()->prepare("SELECT nidreg,cdescripcion 
                                                        FROM tb_parametros
                                                        WHERE cclase=:clase
                                                        AND ccod != '00' 
                                                        ORDER BY cdescripcion");
                $query->execute(["clase" => $clase]);
                $rowcount = $query->rowcount();

                if ($rowcount > 0) {
                    while ($row = $query->fetch()) {
                        $salida.='<li>
                                    <a href="'.$row['nidreg'].'">'.$row['cdescripcion'].'</a>
                                 </li>';
                    }
                }
                return $salida;
            } catch (PDOException $e) {
                echo $e->getMessage();
                return false;
            }
        }

        public function rrhhCargo($codigo){
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                        ibis.tb_user.iduser, 
                                                        rrhh.tabla_aquarius.dcargo, 
                                                        rrhh.tabla_aquarius.ccargo 
                                                    FROM
                                                        ibis.tb_user
                                                        INNER JOIN
                                                        rrhh.tabla_aquarius
                                                        ON 
                                                            ibis.tb_user.ncodper = rrhh.tabla_aquarius.internal
                                                    WHERE
                                                        ibis.tb_user.iduser =:codigo");
                $sql->execute(["codigo"=>$codigo]);
                $result = $sql->fetchAll();

                return $result[0]['dcargo'];
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function listarSelect($clase,$activo) {
            try {
                $salida = "";
                $query = $this->db->connect()->prepare("SELECT nidreg,cdescripcion,cobservacion 
                                                        FROM tb_parametros
                                                        WHERE cclase=:clase
                                                        AND ccod != '00' 
                                                        ORDER BY cdescripcion");
                $query->execute(["clase" => $clase]);
                $rowcount = $query->rowcount();

                if ($rowcount > 0) {
                    while ($row = $query->fetch()) {
                        $selected = $activo == $row['nidreg'] ? "selected" : "";
                        
                        $salida.='<option value="'.$row['nidreg'].'" '.$selected.'>'.$row['cdescripcion'].'</option>';
                    }
                }
                return $salida;
            } catch (PDOException $e) {
                echo $e->getMessage();
                return false;
            }
        } 

        public function listarPais() {
            try {
                $salida = "";
                $query = $this->db->connect()->query("SELECT ncodpais,cdespais 
                                                        FROM tb_pais
                                                        ORDER BY cdespais");
                $query->execute();
                $rowcount = $query->rowcount();

                if ($rowcount > 0) {
                    while ($row = $query->fetch()) {
                        $salida.='<li>
                                    <a href="'.$row['ncodpais'].'">'.$row['cdespais'].'</a>
                                 </li>';
                    }
                }
                return $salida;
            } catch (PDOException $e) {
                echo $e->getMessage();
                return false;
            }
        }

        public function listarAquarius(){
            try {
                $salida = "";
                $query = $this->db->connectrrhh()->query("SELECT dni, CONCAT(nombres,' ',apellidos) AS nombres, internal,ccargo,dcargo,
                                                        nombres AS nomb,
                                                        apellidos AS apell
                                                        FROM tabla_aquarius 
                                                        WHERE estado = 'AC' ORDER BY apellidos ASC");
                $query->execute();
                $rowcount = $query->rowcount();

                if ($rowcount > 0) {
                    while ($row = $query->fetch()) {
                        $salida.='<li>
                                    <a href="'.$row['internal'].'" 
                                                data-ccargo="'.$row['ccargo'].'" 
                                                data-dcargo="'.$row['dcargo'].'"
                                                data-nom="'.$row['nomb'].'"
                                                data-apell="'.$row['apell'].'">'.$row['nombres'].'</a>
                                 </li>';
                    }
                }
                return $salida;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function actualizarCabecera($tabla,$valor,$id,$emitido,$aprueba){
            try {
                $sql = $this->db->connect()->prepare("UPDATE $tabla SET estadodoc=:est,docPdfEmit=:emit,aprueba=:aut WHERE idreg=:id");
                $sql->execute(["est"=>$valor,
                                "id"=>$id,
                                "emit"=>$emitido,
                                "aut"=>$aprueba]);
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function actualizarDetalles($tabla,$valor,$detalles){
            $datos = json_decode($detalles);
            $nreg =  count($datos);

            try {
                for ($i=0; $i < $nreg; $i++) { 

                    //esta linea es para cambiar los items 52 -- atendido en su total por almacen
                    $estado = floatval($datos[$i]->cantidad) - floatval($datos[$i]->atendida) == 0 ? 52: $valor;
                    $resto = floatval($datos[$i]->cantidad) - floatval($datos[$i]->atendida);

                    $sql = $this->db->connect()->prepare("UPDATE $tabla SET estadoItem=:est,observAlmacen=:obs,
                                                                            cant_atend=:aten,
                                                                            cant_resto=:resto
                                                                        WHERE iditem=:id");
                    $sql->execute(["est"=>$estado,
                                    "id"=>$datos[$i]->itempedido,
                                    "obs"=>$datos[$i]->observac,
                                    "aten"=>$datos[$i]->atendida,
                                    "resto"=>$resto]);
                }
                
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function crearMenu($user){
           $salida = "";
           try {
               $ul = ['CATALOGOS',];
           } catch (PDOException $th) {
               echo $th->getMessage();
               return false;
           }
        }

        public function llamarParametrosSelect($clase){
            try {
                $salida = '<option value="-1" class="oculto">Elija una opción</option>';
                $sql = $this->db->connect()->prepare("SELECT nidreg,cclase,ccod,cdescripcion,cabrevia
                                                        FROM tb_parametros
                                                        WHERE cclase=:clase 
                                                        AND nactivo = 1
                                                        AND ccod !='00' 
                                                        ORDER BY cdescripcion");
                $sql->execute(["clase"=>$clase]);
                $rowcount = $sql->rowCount();

                if($rowcount > 0){
                    while ($rs = $sql->fetch()) {
                        $salida .= '<option value="'.$rs['nidreg'].'" data-abrevia="">'.$rs['cdescripcion'].'</option>';
                    }
                } 

                return $salida;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function selectAlmacen(){
            try {
                $salida = '<option value="-1" class="oculto">Elija una opción</option>';
                $sql = $this->db->connect()->query("SELECT ncodalm,UPPER(cdesalm) AS almacen FROM tb_almacen WHERE nflgactivo = 1");
                $sql->execute();
                $rowCount = $sql->rowCount();

                if ($rowCount > 0){
                    while ($rs = $sql->fetch()){
                        $salida .= '<option value="'.$rs['ncodalm'].'" data-abrevia="">'.$rs['almacen'].'</option>';
                    }

                    return $salida;
                } 
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function selectCostos(){
            try {
                $salida = "";

                $sql = $this->db->connect()->prepare("SELECT
                                                    UPPER( tb_proyectos.ccodproy ) AS codigo_costos,
                                                    UPPER( tb_proyectos.cdesproy ) AS descripcion_costos,
                                                    tb_proyectos.veralm,
                                                    tb_costusu.ncodproy 
                                                FROM
                                                    tb_costusu
                                                    INNER JOIN tb_proyectos ON tb_costusu.ncodproy = tb_proyectos.nidreg 
                                                WHERE
                                                    tb_costusu.id_cuser = :id 
                                                    AND tb_proyectos.nflgactivo = 1
                                                    AND tb_costusu.nflgactivo = 1");
                $sql->execute(["id"=>$_SESSION['iduser']]);
                $rowCount = $sql->rowCount(); 

                if ($rowCount > 0){
                    while ($rs = $sql->fetch()){
                        $salida .= '<option value="'.$rs['ncodproy'].'" data-abrevia="">'.$rs['codigo_costos']." ".$rs['descripcion_costos'].'</option>';
                    }

                    return $salida;
                }
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function obtenerUnidades(){
            try {
                $salida = '';
                $sql = $this->db->connect()->query("SELECT ncodmed,ccodmed,cdesmed,cabrevia,nfactor
                                                    FROM tb_unimed");
                $sql->execute();
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while ( $rs = $sql->fetch()){
                        $salida .='<li><a href="'.$rs['ncodmed'].'">'.$rs['ccodmed'] .' - '.strtoupper($rs['cdesmed']).'</a></li>';
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function obtenerGrupos(){
            $salida = "";
            try {
                $sql = $this->db->connect()->query("SELECT ncodgrupo,ccodcata,cdescrip 
                                                    FROM tb_grupo 
                                                    WHERE nflgactivo=1 
                                                    ORDER BY cdescrip ASC");
                $sql->execute();
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while ( $rs = $sql->fetch()){
                        $salida .='<li><a href="'.$rs['ncodgrupo'].'" data-grupo="'.$rs['ccodcata'].'">'.$rs['ccodcata'] .' - '.strtoupper($rs['cdescrip']).'</a></li>';
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function obtenerClases($grupo) {
            $salida = "";
            try {
                $sql = $this->db->connect()->prepare("SELECT ncodgrupo,ncodclase,ccodcata,cdescrip 
                                                    FROM tb_clase 
                                                    WHERE nflgactivo=1 AND ncodgrupo = :grupo 
                                                    ORDER BY cdescrip ASC");
                $sql->execute(["grupo"=>$grupo]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while ( $rs = $sql->fetch()){
                        $salida .='<li><a href="'.$rs['ncodclase'].'" data-catalogo="'.$rs['ccodcata'] .'">'.$rs['ccodcata'] .' - '.strtoupper($rs['cdescrip']).'</a></li>';
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function crearCodigoClase($grupo,$clase){
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                            LPAD( COUNT(tb_familia.ncodfamilia ) + 1, 4, 0 ) AS numero 
                                                        FROM
                                                            tb_familia 
                                                        WHERE
                                                            tb_familia.ncodgrupo =:grupo 
                                                            AND tb_familia.ncodclase =:clase");
                $sql->execute(['grupo' =>$grupo,'clase'=>$clase]);
                $result=$sql->fetchAll();

                return str_pad($result[0]['numero'],4,0,STR_PAD_LEFT);
                
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function obtenerFamilias($grupo,$clase) {
            $salida = "";
            try {
                $sql = $this->db->connect()->prepare("SELECT ncodgrupo,ncodclase,ncodfamilia,ccodcata,cdescrip 
                                                    FROM tb_familia 
                                                    WHERE nflgactivo=1 
                                                    AND ncodgrupo = :grupo
                                                    AND ncodclase = :clase 
                                                    ORDER BY cdescrip ASC");
                $sql->execute(["grupo"=>$grupo,
                                "clase"=>$clase]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while ( $rs = $sql->fetch()){
                        $salida .='<li><a href="'.$rs['ncodfamilia'].'" data-catalogo="'.$rs['ccodcata'] .'">'.$rs['ccodcata'] .' - '.strtoupper($rs['cdescrip']).'</a></li>';
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }
      
        public function listarClases($grupo){
            try {
                $salida = "";
                $sql = $this->db->connect()->prepare("SELECT ncodclase,ccodcata,UPPER(cdescrip) AS cdescrip 
                                                        FROM tb_clase
                                                        WHERE ncodgrupo =:grupo AND nflgactivo = 1
                                                        ORDER BY cdescrip DESC");
                $sql->execute(['grupo'=>$grupo]);
                $rowCount = $sql->rowCount();

                if($rowCount > 0) {
                    while($rs = $sql->fetch()){
                        $salida .='<tr class="pointer" data-id="'.$rs['ncodclase'].'">
                                        <td class="textoCentro">'.$rs['ccodcata'].'</td>
                                        <td class="pl20px">'.$rs['cdescrip'].'</td>
                                        <td class="textoCentro"><a href="'.$rs['ncodclase'].'"><i class="fas fa-trash-alt"></i></a></td>
                                    </tr>';

                    }
                }
                
                return $salida;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function listarGrupos(){
            try {
                $salida = "";
                $sql = $this->db->connect()->query("SELECT ncodgrupo,ccodcata,cdescrip 
                                                        FROM tb_grupo
                                                        WHERE nflgactivo = 1
                                                        ORDER BY ccodcata DESC");
                $sql->execute();
                $rowCount = $sql->rowCount();

                if($rowCount > 0) {
                    while($rs = $sql->fetch()){
                        $salida .='<tr class="pointer" data-id="'.$rs['ncodgrupo'].'" data-grupo="'.$rs['ccodcata'].'">
                                        <td class="textoCentro">'.$rs['ccodcata'].'</td>
                                        <td class="pl20px">'.$rs['cdescrip'].'</td>
                                        <td class="textoCentro"><a href="'.$rs['ccodcata'].'"><i class="fas fa-trash-alt"></i></a></td>
                                    </tr>';

                    }
                }
                
                return $salida;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function listarAlmacen(){
            try {
                $salida = "";
                $sql = $this->db->connect()->query("SELECT ncodalm,UPPER(cdesalm) AS almacen FROM tb_almacen WHERE nflgactivo = 1");
                $sql->execute();
                $rowCount = $sql->rowCount();

                if ($rowCount > 0){
                    while ($rs = $sql->fetch()){
                        $salida .='<li><a href="'.$rs['ncodalm'].'" >'.$rs['almacen'].'</a></li>';
                    }

                    return $salida;
                } 
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function listarEntidades(){
            try {
                $salida = "";
                $sql = $this->db->connect()->query("SELECT
                                                    cm_entidad.id_centi, 
                                                    UPPER(cm_entidad.crazonsoc) AS crazonsoc, 
                                                    cm_entidad.cnumdoc,
                                                    cm_entidad.cviadireccion
                                                FROM
                                                    cm_entidad
                                                WHERE
                                                    cm_entidad.nflgactivo = 7");
                $sql->execute();
                $rowCount = $sql->rowCount();

                if ($rowCount > 0){
                    while ($rs = $sql->fetch()){
                        $salida .='<li><a href="'.$rs['id_centi'].'" data-direccion="'.$rs['cviadireccion'].'" data-ruc="'.$rs['cnumdoc'].'">'.$rs['crazonsoc'].'</a></li>';
                    }

                    return $salida;
                } 
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function listarAlmacenGuia(){
            try {
                $salida = "";
                $sql = $this->db->connect()->query("SELECT
                                                        ncodalm,
                                                        UPPER( cdesalm ) AS almacen,
                                                        UPPER(
                                                        CONCAT_WS( ' ', cdesvia, cnrovia )) AS direccion,
                                                        distritos.cdubigeo AS dist,
                                                        provincias.cdubigeo AS prov,
                                                        dptos.cdubigeo AS dpto 
                                                    FROM
                                                        tb_almacen
                                                        LEFT JOIN tb_ubigeo AS distritos ON tb_almacen.ncubigeo = distritos.ccubigeo
                                                        LEFT JOIN tb_ubigeo AS provincias ON SUBSTR( tb_almacen.ncubigeo, 1, 4 ) = provincias.ccubigeo
                                                        LEFT JOIN tb_ubigeo AS dptos ON SUBSTR( tb_almacen.ncubigeo, 1, 2 ) = dptos.ccubigeo 
                                                    WHERE
                                                        nflgactivo = 1");
                $sql->execute();
                $rowCount = $sql->rowCount();

                if ($rowCount > 0){
                    while ($rs = $sql->fetch()){
                        $salida .='<li><a href="'.$rs['ncodalm'].'" 
                                        data-direccion="'.$rs['direccion'].'"
                                        data-dpto="'.$rs['dpto'].'"
                                        data-prov="'.$rs['prov'].'"
                                        data-dist="'.$rs['dist'].'">'.$rs['almacen'].'</a></li>';
                    }

                    return $salida;
                } 
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function listarPersonalRol($nrol){
            try {
                $salida = "";
                $sql = $this->db->connect()->prepare("SELECT
                                            tb_user.iduser,
                                            tb_user.cnombres 
                                        FROM
                                            tb_user 
                                        WHERE
                                            nrol =:rol 
                                            AND nflgactivo = 1 
                                            AND nestado = 7");
                $sql->execute(['rol'=>4]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0){
                    while ($rs = $sql->fetch()){
                        $salida .='<li><a href="'.$rs['iduser'].'" >'.$rs['cnombres'].'</a></li>';
                    }

                    return $salida;
                } 
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        //centro de costos por usuario
        public function costosPorUsuario($id){
            try {
                $salida = "";

                $sql = $this->db->connect()->prepare("SELECT
                                                    UPPER( tb_proyectos.ccodproy ) AS codigo_costos,
                                                    UPPER( tb_proyectos.cdesproy ) AS descripcion_costos,
                                                    tb_proyectos.veralm,
                                                    tb_costusu.ncodproy 
                                                FROM
                                                    tb_costusu
                                                    INNER JOIN tb_proyectos ON tb_costusu.ncodproy = tb_proyectos.nidreg 
                                                WHERE
                                                    tb_costusu.id_cuser = :id 
                                                    AND tb_proyectos.nflgactivo = 1
                                                    AND tb_costusu.nflgactivo = 1");
                $sql->execute(["id"=>$id]);
                $rowCount = $sql->rowCount(); 

                if ($rowCount > 0){
                    while ($rs = $sql->fetch()){
                        $salida .='<li><a href="'.$rs['ncodproy'].'" data-aprobacion="'.$rs['veralm'].'">'.$rs['codigo_costos']." ".$rs['descripcion_costos'].'</a></li>';
                    }

                    return $salida;
                }
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function obtenerAreas(){
            try {
                $salida = "";

                $sql = $this->db->connect()->query("SELECT ncodarea,ccodarea,UPPER(cdesarea) AS cdesarea 
                                                    FROM tb_area 
                                                    WHERE nflgactivo = 1");
                $sql->execute();
                $rowCount = $sql->rowCount(); 

                if ($rowCount > 0){
                    while ($rs = $sql->fetch()){
                        $salida .='<li><a href="'.$rs['ncodarea'].'">'.$rs['ccodarea']." ".$rs['cdesarea'].'</a></li>';
                    }

                    return $salida;
                }
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        //genera los numero de los documentos
        public function generarNumero($id,$query){
            try {
                $sql = $this->db->connect()->prepare($query);
                $sql->execute(["cod"=>$id]);
                $result = $sql->fetchAll();

                return $salida = array("numero"=>str_pad($result[0]['numero'] + 1,6,0,STR_PAD_LEFT),
                                        "codigo"=>uniqid(),
                                        "movimiento"=>str_pad($this->genNumberIngresos($id)+1,6,0,STR_PAD_LEFT)); 
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage;
                return false;
            }
        }
        

        public function generarNumeroPedido($id,$query){
            try {
                $sql = $this->db->connect()->prepare($query);
                $sql->execute(["cod"=>$id]);
                $result = $sql->fetchAll();

                return $salida = array("numero"=>str_pad($result[0]['numero'] + 1,6,0,STR_PAD_LEFT),
                                        "codigo"=>uniqid(),
                                        "movimiento"=>str_pad($this->genNumberIngresos($id)+1,6,0,STR_PAD_LEFT),
                                        "partidas"=>$this->listarPartidas($id)); 
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage;
                return false;
            }
        }

        public function obtenerIndice($codigo,$query){
            try {
                $sql = $this->db->connect()->prepare("$query");
                $sql->execute(["id"=>$codigo]);
                $result = $sql->fetchAll();

                return $result[0]['numero'];
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function listarPartidas($codigo) {
            try {
                $salida = "";
                $sql = $this->db->connect()->prepare("SELECT
                                                        tb_partidas.idreg,
                                                        tb_partidas.idcc,
                                                        tb_partidas.ccodigo,
                                                        UPPER(tb_partidas.cdescripcion) AS cdescripcion
                                                    FROM
                                                        tb_partidas
                                                    WHERE
                                                        tb_partidas.idcc = :costo
                                                    AND tb_partidas.nflgactivo = 1");
                $sql->execute(["costo" => $codigo]);
                $rowCount = $sql->rowCount(); 

                if ($rowCount > 0){
                    while ($rs = $sql->fetch()){
                        $salida .='<li><a href="'.$rs['idreg'].'">'.$rs['ccodigo']." ".$rs['cdescripcion'].'</a></li>';
                    }
                }

                return $salida;

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

         //listado de productos
        public function listarProductos($tipo){
             try {
                 $salida = "";
                 $sql = $this->db->connect()->prepare("SELECT
                                                        cm_producto.id_cprod,
                                                        cm_producto.ccodprod,
                                                        UPPER(cm_producto.cdesprod) AS cdesprod,
                                                        tb_unimed.ncodmed,
                                                        tb_unimed.cabrevia,
                                                        tb_unimed.nfactor,
                                                        tb_parametros.cdescripcion 
                                                    FROM
                                                        cm_producto
                                                        INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed
                                                        INNER JOIN tb_parametros ON cm_producto.ntipo = tb_parametros.nidreg
                                                    WHERE ntipo = :tipo
                                                    LIMIT 20");
                $sql->execute(["tipo"=>$tipo]);
                $rowCount = $sql->rowCount();
                if ($rowCount > 0){
                    while ($rs = $sql->fetch()) {
                        $salida .='<tr class="pointer" data-idprod="'.$rs['id_cprod'].'" data-ncomed="'.$rs['ncodmed'].'" data-unidad="'.$rs['cabrevia'].'">
                                        <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                        <td class="pl20px">'.$rs['cdesprod'].'</td>
                                        <td class="textoCentro">'.$rs['cabrevia'].'</td>
                                    </tr>';
                    }
                }

                return $salida;
             } catch (PDOException $th) {
                echo "Error: ".$th->getMessage;
                return false;
            }
        }

         //codigo de ubigeo
        public function getUbigeo($nivel,$prefijo){
            try {
                $salida = "";
                $query= $this->db->connect()->prepare("SELECT
                    tb_ubigeo.ccubigeo,
                    tb_ubigeo.cdubigeo
                FROM
                    tb_ubigeo
                WHERE
                    tb_ubigeo.nnivel = :nivel AND
                    tb_ubigeo.ccubigeo LIKE :prefijo");
                $query->execute(['nivel'=>$nivel,'prefijo'=>$prefijo]);
                $rowcount = $query->rowCount();
                if ($rowcount > 0 ){
                    while ($row = $query->fetch()) {
                        $salida.='<li><a href="'.$row['ccubigeo'].'">'.$row['cdubigeo'].'</a></li>';
                    }
                }

                return $salida;
                
            } catch (PDOException $e) {
                echo $e->getMessage();
                return false;
            }
        }

        //filtrar par que nop vean los correso deben poner le centro de costos
        public function buscarRol($rol,$cc){
            try {
                $salida = "";

                /*if ($rol != 3){
                   
                }   
                else {
                    $sql = $this->db->connect()->prepare("SELECT
                                                        ibis.tb_user.ccorreo AS correo,
                                                        ibis.tb_user.nrol,
                                                        rrhh.tabla_aquarius.nombres, 
                                                        rrhh.tabla_aquarius.apellidos 
                                                    FROM
                                                        ibis.tb_user
                                                        INNER JOIN rrhh.tabla_aquarius ON ibis.tb_user.ncodper = rrhh.tabla_aquarius.internal 
                                                    WHERE
                                                        tb_user.nrol =:rol");
                    $sql->execute(["rol"=>$rol]);
                    
                }*/

                $sql = $this->db->connect()->prepare("SELECT
                                                        ibis.tb_costusu.ncodcos,
                                                        ibis.tb_costusu.ncodproy,
                                                        ibis.tb_costusu.id_cuser,
                                                        ibis.tb_user.ccorreo AS correo,
                                                        rrhh.tabla_aquarius.apellidos,
                                                        rrhh.tabla_aquarius.nombres,
                                                        ibis.tb_proyectos.cdesproy 
                                                    FROM
                                                        ibis.tb_costusu
                                                        INNER JOIN ibis.tb_user ON tb_costusu.id_cuser = tb_user.iduser
                                                        INNER JOIN rrhh.tabla_aquarius ON ibis.tb_user.ncodper = rrhh.tabla_aquarius.internal
                                                        INNER JOIN ibis.tb_proyectos ON ibis.tb_costusu.ncodproy = ibis.tb_proyectos.nidreg 
                                                    WHERE
                                                        ibis.tb_user.nrol = :rol 
                                                        AND ibis.tb_costusu.ncodproy = :cc
                                                        AND ibis.tb_costusu.nflgactivo = 1");
                $sql->execute(["rol"=>$rol,"cc"=>$cc]);
                
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while($rs = $sql->fetch()){
                    $nom = $this->primerosNombres($rs['nombres'],$rs['apellidos']);

                        $salida .='<tr>
                                    <td class="pl10px">'.$nom.'</td>
                                    <td class="pl10px">'.$rs['correo'].'</td>
                                    <td class="textoCentro"><input type="checkbox"></td>
                                </tr>';
                    }
                     
                }

                return $salida;

            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function buscarFirmas($rol){
            try {
                $salida = "";
                $sql = $this->db->connect()->prepare("SELECT
                                                        ibis.tb_user.ccorreo AS correo,
                                                        ibis.tb_user.nrol,
                                                        rrhh.tabla_aquarius.nombres, 
                                                        rrhh.tabla_aquarius.apellidos 
                                                    FROM
                                                        ibis.tb_user
                                                        INNER JOIN rrhh.tabla_aquarius ON ibis.tb_user.ncodper = rrhh.tabla_aquarius.internal 
                                                    WHERE
                                                        tb_user.nrol =:rol");
                    $sql->execute(["rol"=>$rol]);
                
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while($rs = $sql->fetch()){
                    $nom = $this->primerosNombres($rs['nombres'],$rs['apellidos']);

                        $salida .='<tr>
                                    <td class="pl10px">'.$nom.'</td>
                                    <td class="pl10px">'.$rs['correo'].'</td>
                                    <td class="textoCentro"><input type="checkbox"></td>
                                </tr>';
                    }
                     
                }

                return $salida;

            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function primerosNombres($nombres,$apellidos){
            $nombre_array = explode(" ",$nombres);
            $apellido_array= explode(" ",$apellidos);
            
            $nnom = count($nombre_array);
            $napell = count($apellido_array);
             
            if( $nnom  <= 2){
                $nom  = $nombre_array[0];
            }
            
            if( $napell == 2){
                $apell = $apellido_array[0];
            }else {
                $apell = $apellido_array[0] . " " . $apellido_array[1];
            }
            
            return $nom . " " . $apell;
        }

        //subir los adjuntos de los Correos
        public function subirAdjuntoCorreo($archivos){
            $countfiles = count( $archivos['name'] );
            $upload = false;

            for($i=0;$i<$countfiles;$i++){
                try {
                    // Upload file
                    if (move_uploaded_file($archivos['tmp_name'][$i],'public/documentos/correos/adjuntos/'.$archivos['name'][$i])){
                        $upload = true;
                    }
                } catch (PDOException $th) {
                    echo "Error: ".$th->getMessage();
                    return false;
                }
            }

            return $upload;
        }

        //consultas pedidos
        public function consultarReqId($id,$min,$max,$proceso){
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                        ibis.tb_pedidocab.idreg, 
                                                        ibis.tb_pedidocab.idcostos, 
                                                        ibis.tb_pedidocab.idarea, 
                                                        ibis.tb_pedidocab.idtrans, 
                                                        ibis.tb_pedidocab.idsolicita, 
                                                        ibis.tb_pedidocab.idtipomov, 
                                                        ibis.tb_pedidocab.emision, 
                                                        ibis.tb_pedidocab.vence, 
                                                        ibis.tb_pedidocab.estadodoc, 
                                                        ibis.tb_pedidocab.nrodoc, 
                                                        ibis.tb_pedidocab.usuario, 
                                                        ibis.tb_pedidocab.concepto, 
                                                        ibis.tb_pedidocab.detalle, 
                                                        ibis.tb_pedidocab.nivelAten, 
                                                        ibis.tb_pedidocab.docfPdfPrev, 
                                                        ibis.tb_pedidocab.docPdfEmit, 
                                                        ibis.tb_pedidocab.docPdfAprob, 
                                                        ibis.tb_pedidocab.verificacion, 
                                                        CONCAT( rrhh.tabla_aquarius.apellidos, ' ', rrhh.tabla_aquarius.nombres ) AS nombres, 
                                                        UPPER(
                                                        CONCAT( ibis.tb_proyectos.ccodproy, ' ', ibis.tb_proyectos.cdesproy )) AS proyecto, 
                                                        UPPER(
                                                        CONCAT( ibis.tb_area.ccodarea, ' ', ibis.tb_area.cdesarea )) AS area, 
                                                        UPPER(
                                                        CONCAT( ibis.tb_parametros.nidreg, ' ', ibis.tb_parametros.cdescripcion )) AS transporte, 
                                                        estados.cdescripcion AS estado, 
                                                        estados.cabrevia, 
                                                        UPPER(
                                                        CONCAT_WS( ' ', tipos.nidreg, tipos.cdescripcion )) AS tipo, 
                                                        ibis.tb_proyectos.veralm, 
                                                        ibis.tb_user.cnombres,
                                                        ibis.tb_partidas.cdescripcion,
                                                        ibis.tb_pedidocab.idpartida
                                                    FROM
                                                        ibis.tb_pedidocab
                                                        INNER JOIN
                                                        rrhh.tabla_aquarius
                                                        ON 
                                                            ibis.tb_pedidocab.idsolicita = rrhh.tabla_aquarius.internal
                                                        INNER JOIN
                                                    ibis.tb_proyectos ON ibis.tb_pedidocab.idcostos = ibis.tb_proyectos.nidreg
                                                    INNER JOIN ibis.tb_area ON ibis.tb_pedidocab.idarea = ibis.tb_area.ncodarea
                                                    INNER JOIN ibis.tb_parametros ON ibis.tb_pedidocab.idtrans = ibis.tb_parametros.nidreg
                                                    INNER JOIN ibis.tb_parametros AS transportes ON ibis.tb_pedidocab.idtrans = transportes.nidreg
                                                    INNER JOIN ibis.tb_parametros AS estados ON ibis.tb_pedidocab.estadodoc = estados.nidreg
                                                    INNER JOIN ibis.tb_parametros AS tipos ON ibis.tb_pedidocab.idtipomov = tipos.nidreg
                                                    INNER JOIN ibis.tb_user ON ibis.tb_pedidocab.usuario = ibis.tb_user.iduser
                                                    LEFT JOIN ibis.tb_partidas ON ibis.tb_pedidocab.idpartida = ibis.tb_partidas.idreg 
                                                    WHERE
                                                        tb_pedidocab.idreg = :id 
                                                        AND tb_pedidocab.estadodoc BETWEEN :min 
                                                        AND :max");
                $sql->execute(['id'=>$id, 'min'=>$min, 'max'=>$max]);
                $rowCount = $sql->rowCount();
                
                if ($rowCount > 0) {
                    $docData = array();
                    while($row=$sql->fetch(PDO::FETCH_ASSOC)){
                        $docData[] = $row;
                    }
                }

                if ( $proceso == 49){
                    $detalles = $this->consultarDetallesProceso($id);
                }else if ( $proceso == 51){
                    $detalles = $this->consultarDetallesStock($id);
                }else if ( $proceso == 53 ){
                    $detalles = $this->consultarDetallesAprobacion($id);
                }else if ( $proceso == 54 ){
                    $detalles = $this->consultarDetallesCotizacion($id);
                }else if ( $proceso == 56 ){
                    $detalles = $this->obtenerProformas($id);
                }else if ($proceso ==57) {
                    $detalles = $this->consultarDetallesConformidad($id);
                }
                    

                return array("cabecera"=>$docData,
                            "detalles"=>$detalles);
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function buscarSeries($idprod,$ingreso,$almacen){
            try {
                $salida = "";

                $sql = $this->db->connect()->prepare("SELECT cdesserie FROM alm_recepserie 
                                                         WHERE id_cprod = :producto
                                                            AND idref_movi = :salida
                                                            AND idref_alma = :almacen");
                $sql ->execute(["producto"=>$idprod,"salida"=>$ingreso,"almacen"=>$almacen]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()) {
                        $salida .= $rs['cdesserie']." "; 
                    }
                }
                
                return $salida;
                
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function consultarDetallesProceso($id){
            try {
                $salida ="";

                $sql=$this->db->connect()->prepare("SELECT
                                                    tb_pedidodet.iditem,
                                                    tb_pedidodet.idpedido,
                                                    tb_pedidodet.idprod,
                                                    tb_pedidodet.idtipo,
                                                    tb_pedidodet.nroparte,
                                                    tb_pedidodet.unid,
                                                    tb_pedidodet.observaciones,
                                                    FORMAT(tb_pedidodet.cant_pedida,2) AS cant_pedida,
                                                    tb_pedidodet.estadoItem,
                                                    cm_producto.ccodprod,
                                                    cm_producto.cdesprod,
                                                    tb_unimed.cabrevia,
                                                    tb_pedidodet.nflgqaqc 
                                                FROM
                                                    tb_pedidodet
                                                    INNER JOIN cm_producto ON tb_pedidodet.idprod = cm_producto.id_cprod
                                                    INNER JOIN tb_unimed ON tb_pedidodet.unid = tb_unimed.ncodmed 
                                                WHERE
                                                    tb_pedidodet.idpedido = :id");
                $sql->execute(["id"=>$id]);
                $rowCount = $sql->rowCount();
                
                if ($rowCount > 0){
                    $filas = 1;
                    while ($rs = $sql->fetch()) {

                        $checked = $rs['nflgqaqc'] == 1 ? "checked ": " ";
                        
                        $salida .='<tr data-grabado="1" data-idprod="'.$rs['idprod'].'" data-codund="'.$rs['unid'].'" data-idx="'.$rs['iditem'].'">
                                        <td class="textoCentro"><a href="#"><i class="fas fa-eraser"></i></a></td>
                                        <td class="textoCentro">'.str_pad($filas++,3,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                        <td class="pl20px">'.strtoupper($rs['cdesprod']).'</td>
                                        <td class="textoCentro">'.$rs['cabrevia'].'</td>
                                        <td>
                                            <input type="number" 
                                                        step="any" 
                                                        placeholder="0.00" 
                                                        onchange="(function(el){el.value=parseFloat(el.value).toFixed(2);})(this)"
                                                        onclick="this.select()" 
                                                        value="'.$rs['cant_pedida'].'">
                                        </td>
                                        <td class="pl20px"><textarea>'.$rs['observaciones'].'</textarea></td>
                                        <td class="textoCentro"></td>
                                    </tr>';
                    }
                }
                
                return $salida;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function consultarDetallesStock($id){
            try {
                $salida ="";

                $sql=$this->db->connect()->prepare("SELECT
                                                    tb_pedidodet.iditem, 
                                                    tb_pedidodet.idpedido, 
                                                    tb_pedidodet.idprod, 
                                                    tb_pedidodet.idtipo, 
                                                    tb_pedidodet.nroparte, 
                                                    tb_pedidodet.unid, 
                                                    FORMAT(tb_pedidodet.cant_pedida,2) AS cant_pedida, 
                                                    tb_pedidodet.estadoItem, 
                                                    cm_producto.ccodprod, 
                                                    CONCAT_WS(' ',cm_producto.cdesprod,tb_pedidodet.observaciones) AS cdesprod, 
                                                    tb_unimed.cabrevia, 
                                                    tb_pedidodet.nflgqaqc, 
                                                    tb_pedidodet.especificaciones 
                                                FROM
                                                    tb_pedidodet
                                                    INNER JOIN
                                                    cm_producto
                                                    ON 
                                                        tb_pedidodet.idprod = cm_producto.id_cprod
                                                    INNER JOIN
                                                    tb_unimed
                                                    ON 
                                                        tb_pedidodet.unid = tb_unimed.ncodmed
                                                WHERE
                                                    tb_pedidodet.idpedido = :id");
                $sql->execute(["id"=>$id]);
                $rowCount = $sql->rowCount();
                
                if ($rowCount > 0){
                    $filas = 1;
                    while ($rs = $sql->fetch()) {
                        $salida .='<tr data-grabado="1" data-idprod="'.$rs['idprod'].'" data-codund="'.$rs['unid'].'" data-idx="'.$rs['iditem'].'">
                                        <td class="textoCentro"><a href="'.$rs['idprod'].'"><i class="far fa-eye"></i></a></td>
                                        <td class="textoCentro">'.str_pad($filas++,3,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                        <td class="pl20px">'.strtoupper($rs['cdesprod']).'</td>
                                        <td class="textoCentro">'.$rs['cabrevia'].'</td>
                                        <td class="textoCentro">'.$rs['cant_pedida'].'</td>
                                        <td>
                                            <input type="number" 
                                                        step="any" 
                                                        placeholder="0.00" 
                                                        onchange="(function(el){el.value=parseFloat(el.value).toFixed(2);})(this)"
                                                        onclick="this.select()" 
                                                        value=""
                                                        class="valorAtendido">
                                        </td>
                                        <td></td>
                                        <td class="textoCentro"><input type="text"></td>
                                    </tr>';
                    }
                }
                
                return $salida;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function consultarDetallesAprobacion($id){
            try {
                $salida ="";

                $sql=$this->db->connect()->prepare("SELECT
                                                    tb_pedidodet.iditem, 
                                                    tb_pedidodet.idpedido, 
                                                    tb_pedidodet.idprod, 
                                                    tb_pedidodet.idtipo, 
                                                    tb_pedidodet.nroparte, 
                                                    tb_pedidodet.unid, 
                                                    FORMAT(tb_pedidodet.cant_pedida,2) AS cant_pedida,
                                                    FORMAT(tb_pedidodet.cant_atend,2) AS cant_atendida,
                                                    FORMAT(tb_pedidodet.cant_resto,2) AS cant_pendiente, 
                                                    tb_pedidodet.estadoItem, 
                                                    cm_producto.ccodprod, 
                                                    CONCAT_WS(' ',cm_producto.cdesprod,tb_pedidodet.observaciones) AS cdesprod, 
                                                    tb_unimed.cabrevia, 
                                                    tb_pedidodet.nflgqaqc, 
                                                    tb_pedidodet.especificaciones 
                                                FROM
                                                    tb_pedidodet
                                                    INNER JOIN
                                                    cm_producto
                                                    ON 
                                                        tb_pedidodet.idprod = cm_producto.id_cprod
                                                    INNER JOIN
                                                    tb_unimed
                                                    ON 
                                                        tb_pedidodet.unid = tb_unimed.ncodmed
                                                WHERE
                                                    tb_pedidodet.idpedido = :id 
                                                    AND tb_pedidodet.cant_resto != 0");
                $sql->execute(["id"=>$id]);
                $rowCount = $sql->rowCount();
                
                if ($rowCount > 0){
                    $filas = 1;
                    while ($rs = $sql->fetch()) {
                        
                        $salida .='<tr data-grabado="1" data-idprod="'.$rs['idprod'].'" data-codund="'.$rs['unid'].'" data-idx="'.$rs['iditem'].'">
                                        <td class="textoCentro">'.str_pad($filas++,3,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                        <td class="pl20px">'.strtoupper($rs['cdesprod']).'</td>
                                        <td class="textoCentro">'.$rs['cabrevia'].'</td>
                                        <td class="textoCentro">'.$rs['cant_pedida'].'</td>
                                        <td class="textoCentro">'.$rs['cant_atendida'].'</td>
                                        <td>
                                            <input type="number" 
                                                        step="any" 
                                                        placeholder="0.00" 
                                                        onchange="(function(el){el.value=parseFloat(el.value).toFixed(2);})(this)"
                                                        onclick="this.select()" 
                                                        value="'.$rs['cant_pendiente'].'"
                                                        class="valorAtendido">
                                        </td>
                                        <td></td>
                                        <td class="textoCentro"><input type="text"></td>
                                        <td class="textoCentro"><input type="checkbox" checked></td>
                                    </tr>';
                    }
                }
                
                return $salida;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function consultarDetallesCotizacion($id){
            try {
                $salida ="";

                $sql=$this->db->connect()->prepare("SELECT
                                                    tb_pedidodet.iditem,
                                                    tb_pedidodet.idpedido,
                                                    tb_pedidodet.idprod,
                                                    tb_pedidodet.idtipo,
                                                    tb_pedidodet.nroparte,
                                                    tb_pedidodet.unid,
                                                    FORMAT(tb_pedidodet.cant_pedida,2) AS cant_pedida,
                                                    FORMAT(tb_pedidodet.cant_atend,2) AS cant_atendida,
                                                    FORMAT(tb_pedidodet.cant_resto,2) AS cant_pendiente,
                                                    FORMAT(tb_pedidodet.cant_aprob,2) AS cant_aprobada,
                                                    tb_pedidodet.estadoItem,
                                                    cm_producto.ccodprod,
                                                    UPPER(CONCAT_WS(' ',cm_producto.cdesprod, tb_pedidodet.observaciones,lg_proformadet.cdetalle)) AS cdesprod,
                                                    tb_unimed.cabrevia,
                                                    tb_pedidodet.nflgqaqc 
                                                FROM
                                                    tb_pedidodet
                                                    INNER JOIN cm_producto ON tb_pedidodet.idprod = cm_producto.id_cprod
                                                    INNER JOIN tb_unimed ON tb_pedidodet.unid = tb_unimed.ncodmed
                                                    LEFT JOIN lg_proformadet ON tb_pedidodet.iditem = lg_proformadet.niddet 
                                                WHERE
                                                    tb_pedidodet.idpedido = :id 
                                                AND tb_pedidodet.cant_resto > 0");
                $sql->execute(["id"=>$id]);
                $rowCount = $sql->rowCount();
                
                if ($rowCount > 0){
                    $filas = 1;
                    while ($rs = $sql->fetch()) {
                        
                        $salida .='<tr data-grabado="1" data-idprod="'.$rs['idprod'].'" data-codund="'.$rs['unid'].'" data-idx="'.$rs['iditem'].'">
                                        <td><input type="checkbox" checked="checked"></td>    
                                        <td class="textoCentro">'.str_pad($filas++,3,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                        <td class="pl20px">'.$rs['cdesprod'].'</td>
                                        <td class="textoCentro">'.$rs['cabrevia'].'</td>
                                        <td class="textoCentro">'.$rs['cant_aprobada'].'</td>
                                        <td class="textoCentro"></td>
                                        <td class="textoCentro"><input type="text"></td>
                                    </tr>';
                    }
                }
                
                return $salida;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function obtenerProformas($id){
            try {
                $proveedores = 0;
                $codpr = [];
                $salida ='<thead>
                            <tr>
                                <th rowspan="2" width="3%">ITEM</th>
                                <th rowspan="2" width="35%">DESCRIPCION</th>
                                <th rowspan="2" width="3%">UND</th>';
                $sql = $this->db->connect()->prepare("SELECT
                                                            lg_proformacab.id_regmov,
                                                            lg_proformacab.id_centi,
                                                            cm_entidad.crazonsoc,
                                                            cm_entidad.id_centi AS id_proveedor 
                                                        FROM
                                                            lg_proformacab
                                                            INNER JOIN cm_entidad ON lg_proformacab.id_centi = cm_entidad.cnumdoc 
                                                        WHERE
                                                            lg_proformacab.id_regmov = :id");
                $sql->execute(["id"=>$id]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0){
                    while ($rs = $sql->fetch()){
                        $salida .= '<th colspan="5">'.$rs['crazonsoc'].'</th>';
                        $codpr[$proveedores] = $rs['id_centi'];
                        $proveedores++;
                    }
                }

                $salida .= '</tr><tr>';

                for ($i=0; $i < $proveedores ; $i++) { 
                    $salida .= '<th width="6%">Precio Unit</th>
					            <th width="6%">F.Entrega</th>
					            <th width="3%">Dias</th>
					            <th width="3%">Adj.</th>
                                <th width="3%">...</th>';
                }

                $salida .= '</tr></thead>';

                $cuerpo = $this->detalleProductosProforma($id,$codpr);

                return $salida.$cuerpo;

            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }
        
        private function detalleProductosProforma($id,$codpr){
            try {
                $detalle = "<tbody>";
                $item=1;
                $linea = 1;

                $sql = $this->db->connect()->prepare("SELECT
                                                        tb_pedidodet.iditem,
                                                        tb_pedidodet.idprod,
                                                        UPPER(
                                                        CONCAT_WS(' ', cm_producto.cdesprod, tb_pedidodet.observaciones )) AS producto,
                                                        tb_pedidodet.cant_aprob,
                                                        tb_unimed.cabrevia 
                                                    FROM
                                                        tb_pedidodet
                                                        INNER JOIN cm_producto ON tb_pedidodet.idprod = cm_producto.id_cprod
                                                        INNER JOIN tb_unimed ON tb_pedidodet.unid = tb_unimed.ncodmed 
                                                    WHERE
                                                        tb_pedidodet.idpedido = :id 
                                                        AND cant_aprob > 0");
                $sql->execute(["id"=>$id]);
                $rowcount = $sql->rowcount();

                if ($rowcount > 0){
                    while ($rs = $sql->fetch()) {
                        $detalle .= '<tr data-fila="'.$item.'">
                                        <td class="textoCentro">'.str_pad($linea++,3,"0",STR_PAD_LEFT).'</td>
                                        <td class="pl10px">'.$rs['producto'].'</td>
                                        <td class="textoCentro">'.$rs['cabrevia'].'</td> '.$this->preciosProforma($id,$codpr,$rs['iditem']);
                        $item++;
                    }
                }

                $detalle.="</tr></tbody>";
                

                return $detalle;

            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }

        private function preciosProforma($id,$codpr,$item){
            $nreg = count($codpr);
            $precios = "";
            $opc = 1;

           for ($i=0; $i < $nreg; $i++) { 
                $sql = $this->db->connect()->prepare("SELECT
                                                        lg_proformadet.id_regmov,
                                                        lg_proformadet.niddet,
                                                        lg_proformadet.id_centi,
                                                        lg_proformadet.cantcoti,
                                                        lg_proformadet.id_cprod,
                                                        lg_proformadet.ffechaent,
                                                        lg_proformadet.precunit,
                                                        lg_proformadet.nitemprof,
                                                        lg_proformadet.cdetalle,
                                                        lg_proformadet.cotref,
                                                        DATE_FORMAT( lg_proformadet.fregsys, '%Y-%m-%d' ) AS emitido,
                                                        DATEDIFF(
                                                            lg_proformadet.ffechaent,
                                                        DATE_FORMAT( lg_proformadet.fregsys, '%Y-%m-%d' )) AS dias,
                                                        lg_proformadet.cdocPDF,
                                                        lg_proformadet.total,
                                                        tb_parametros.cabrevia 
                                                    FROM
                                                        lg_proformadet
                                                        INNER JOIN tb_parametros ON lg_proformadet.ncodmon = tb_parametros.nidreg 
                                                    WHERE
                                                        lg_proformadet.id_regmov = :cod 
                                                        AND lg_proformadet.id_centi = :ent 
                                                        AND lg_proformadet.niddet = :item");

                $sql->execute(["cod"=>$id,"ent"=>$codpr[$i],"item"=>$item]);
                $rs = $sql->fetchAll();
                $url = "public/documentos/pedidos/especificaciones/";

                //$adjunto = $rs[0]["cdocPDF"] == "" ? "": '<a href="'.$url.$rs[0]['cdocPDF'].'"><i class="far fa-sticky-note"></i></a>';
                $adjunto = "";
                    
                $precios .= '<td class="textoDerecha pr20px '.$codpr[$i].'">'.$rs[0]['cabrevia']." ".number_format($rs[0]['precunit'], 2, '.', ',').'</td>
                             <td class="textoCentro '.$codpr[$i].'">'.date("d/m/Y", strtotime($rs[0]['ffechaent'])).'</td>
                             <td class="textoDerecha pr20px '.$codpr[$i].'">'.$rs[0]['dias'].'</td>
                             <td class="textoCentro '.$codpr[$i].'">'.$adjunto.'</td>
                             <td class="textoCentro '.$codpr[$i].'" data-position="'.$i.'"
                                                         data-pedido="'.$id.'"
                                                         data-entidad="'.$codpr[$i].'"
                                                         data-detprof="'.$rs[0]["nitemprof"].'"
                                                         data-total="'.$rs[0]['total'].'"
                                                         data-precio="'.$rs[0]['precunit'].'"
                                                         data-detped="'.$rs[0]['niddet'].'"
                                                         data-entrega="'.$rs[0]['ffechaent'].'"
                                                         data-dias="'.$rs[0]['dias'].'"
                                                         data-detalle="'.$rs[0]['cdetalle'].'"
                                                         data-idproforma="'.$rs[0]['cotref'].'"
                                                         data-espec="'.$rs[0]['cdocPDF'].'"><input type="checkbox" name="opcion'.$nreg.'" class="chkVerificado"</td>';
           }

           return $precios;
        }


        private function consultarDetallesConformidad($id){
            try {
                $salida ="";

                $sql=$this->db->connect()->prepare("SELECT
                                                    tb_pedidodet.iditem,
                                                    tb_pedidodet.idpedido,
                                                    tb_pedidodet.idprod,
                                                    tb_pedidodet.idtipo,
                                                    tb_pedidodet.nroparte,
                                                    tb_pedidodet.unid,
                                                    UPPER(tb_pedidodet.docEspec) AS docEspec,
                                                    FORMAT(tb_pedidodet.cant_aprob,2) AS cant_aprob,
                                                    FORMAT(tb_pedidodet.cant_atend,2) AS cant_atendida,
                                                    FORMAT(tb_pedidodet.cant_resto,2) AS cant_pendiente,
                                                    tb_pedidodet.estadoItem,
                                                    tb_pedidodet.docEspec,
                                                    cm_producto.ccodprod,
                                                    UPPER(CONCAT_WS(' ',cm_producto.cdesprod, tb_pedidodet.observaciones)) AS cdesprod,
                                                    tb_unimed.cabrevia,
                                                    tb_pedidodet.nflgqaqc 
                                                FROM
                                                    tb_pedidodet
                                                    INNER JOIN cm_producto ON tb_pedidodet.idprod = cm_producto.id_cprod
                                                    INNER JOIN tb_unimed ON tb_pedidodet.unid = tb_unimed.ncodmed 
                                                WHERE
                                                    tb_pedidodet.idpedido = :id 
                                                AND tb_pedidodet.nflgAdjudicado  = 1");
                $sql->execute(["id"=>$id]);
                $rowCount = $sql->rowCount();
                
                if ($rowCount > 0){
                    $filas = 1;
                    while ($rs = $sql->fetch()) {
                        
                        $salida .='<tr data-grabado="1" data-idprod="'.$rs['idprod'].'" data-codund="'.$rs['unid'].'" data-idx="'.$rs['iditem'].'">
                                        <td class="textoCentro">'.str_pad($filas++,3,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                        <td class="pl20px">'.$rs['cdesprod'].'</td>
                                        <td class="textoCentro">'.$rs['cabrevia'].'</td>
                                        <td class="numeroTabla">'.$rs['cant_aprob'].'</td>
                                        <td></td>
                                        <td class="pl20px">'.$rs['docEspec'].'</td>
                                        <td class="textoCentro"><input type="text"></td>
                                        <td class="textoCentro"><input type="checkbox" checked></td>
                                        <td class="textoCentro"><a href="'.$rs['docEspec'].'"><i class="far fa-sticky-note"></i</a></td>
                                    </tr>';
                    }
                }
                
                return $salida;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        //ordenes
        public function consultarOrdenId($id){
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                    lg_ordencab.id_regmov,
                                                    lg_ordencab.cnumero,
                                                    lg_ordencab.ffechadoc,
                                                    lg_ordencab.ncodcos,
                                                    lg_ordencab.ncodarea,
                                                    lg_ordencab.id_centi,
                                                    lg_ordencab.ctiptransp,
                                                    lg_ordencab.ncodpago,
                                                    lg_ordencab.nplazo,
                                                    lg_ordencab.ncodcot,
                                                    lg_ordencab.cnumcot,
                                                    lg_ordencab.nEstadoDoc,
                                                    lg_ordencab.id_refpedi,
                                                    lg_ordencab.ntcambio,
                                                    lg_ordencab.cnumcot,
                                                    UPPER(tb_pedidocab.concepto) AS concepto,
                                                    UPPER(tb_pedidocab.detalle) AS detalle,
                                                    UPPER(CONCAT_WS(' ',tb_proyectos.ccodproy,tb_proyectos.cdesproy)) AS costos,
                                                    lg_ordencab.ncodpry,
                                                    lg_ordencab.ncodalm,
                                                    UPPER(CONCAT_WS(' ',tb_area.ccodarea,tb_area.cdesarea)) AS area,
                                                    lg_ordencab.ncodmon,
                                                    monedas.cdescripcion AS nombre_moneda,
                                                    monedas.cabrevia AS abrevia_moneda,
                                                    lg_ordencab.ntipmov,
                                                    tipos.cdescripcion AS tipo,
                                                    pagos.cdescripcion AS pagos,
                                                    lg_ordencab.ffechaent,
                                                    estados.cabrevia AS estado,
                                                    estados.cdescripcion AS descripcion_estado,
                                                    cm_entidad.crazonsoc,
                                                    cm_entidad.cnumdoc,
                                                    UPPER(cm_entidadcon.cnombres) as cnombres,
                                                    cm_entidadcon.cemail,
                                                    cm_entidadcon.ctelefono1,
                                                    transportes.cdescripcion AS transporte,
                                                    UPPER(tb_almacen.cdesalm) AS cdesalm,
                                                    cm_entidad.cviadireccion,
	                                                cm_entidad.cemail AS mail_entidad,
                                                    cm_entidad.nagenret,
                                                    lg_ordencab.cverificacion,
                                                    lg_ordencab.ntotal,
	                                                tb_pedidocab.nivelAten  
                                                FROM
                                                    lg_ordencab
                                                    INNER JOIN tb_pedidocab ON lg_ordencab.id_refpedi = tb_pedidocab.idreg
                                                    INNER JOIN tb_proyectos ON lg_ordencab.ncodcos = tb_proyectos.nidreg
                                                    INNER JOIN tb_area ON lg_ordencab.ncodarea = tb_area.ncodarea
                                                    INNER JOIN tb_parametros AS monedas ON lg_ordencab.ncodmon = monedas.nidreg
                                                    INNER JOIN tb_parametros AS tipos ON lg_ordencab.ntipmov = tipos.nidreg
                                                    INNER JOIN tb_parametros AS pagos ON lg_ordencab.ncodpago = pagos.nidreg
                                                    INNER JOIN tb_parametros AS estados ON lg_ordencab.nEstadoDoc = estados.nidreg
                                                    INNER JOIN cm_entidad ON lg_ordencab.id_centi = cm_entidad.id_centi
                                                    INNER JOIN cm_entidadcon ON cm_entidad.id_centi = cm_entidadcon.id_centi
                                                    INNER JOIN tb_parametros AS transportes ON lg_ordencab.ctiptransp = transportes.nidreg
                                                    INNER JOIN tb_almacen ON lg_ordencab.ncodalm = tb_almacen.ncodalm 
                                                WHERE
                                                    lg_ordencab.id_regmov =:id 
                                                    AND lg_ordencab.nflgactivo = 1");
                $sql->execute(["id"=>$id]);
                $rowCount = $sql->rowCount();
                
                if ($rowCount > 0) {
                    $docData = array();
                    while($row=$sql->fetch(PDO::FETCH_ASSOC)){
                        $docData[] = $row;
                    }
                }

                $detalles = $this->consultarDetallesOrden($id);
                $comentarios = $this->consultarComentarios($id);
                $total = $this->calculaTotalOrden($id);
                $ncomentarios = $this->consultarTotalComentarios($id);
                $adjuntos = $this->verAdjuntosOrden($id);

                return array("cabecera"=>$docData,
                            "detalles"=>$detalles,
                            "comentarios"=>$comentarios,
                            "total"=>$total,
                            "bocadillo"=>$ncomentarios,
                            "adjuntos"=>$adjuntos);

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function grabarComentarios($codigo,$comentarios) {
            try {
                $indice = $this->obtenerIndice($codigo,"SELECT id_regmov AS numero FROM lg_ordencab WHERE lg_ordencab.cverificacion =:id");
                $datos = json_decode($comentarios);
                $nreg = count($datos);

                for ($i=0; $i < $nreg; $i++) { 
                    $sql = $this->db->connect()->prepare("INSERT INTO lg_ordencomenta 
                                                        SET id_regmov=:id,id_cuser=:usr,ffecha=:fecha,ccomenta=:comentario");
                    $sql->execute(["id"=>$indice,
                                    "usr"=>$datos[$i]->usuario,
                                    "fecha"=>$datos[$i]->fecha,
                                    "comentario"=>$datos[$i]->comentario]);
                }

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function consultarDetallesOrden($id){
            try {
                $salida = "";
                $sql=$this->db->connect()->prepare("SELECT
                                                    lg_ordendet.nitemord,
                                                    lg_ordendet.id_regmov,
                                                    lg_ordendet.niddeta,
                                                    lg_ordendet.nidpedi,
                                                    lg_ordendet.id_cprod,
                                                    FORMAT( lg_ordendet.ncanti, 2 ) AS ncanti,
                                                    lg_ordendet.nunitario AS nunitario,
                                                    FORMAT( lg_ordendet.nigv, 2 ) AS nigv,
                                                    FORMAT( tb_pedidodet.total - lg_ordendet.nigv,2) AS subtotal,
                                                    FORMAT( lg_ordendet.ntotal,2) as ntotal,
                                                    cm_producto.ccodprod,
                                                    UPPER(CONCAT_WS(' ',cm_producto.cdesprod,tb_pedidodet.observaciones,tb_pedidodet.docEspec)) AS cdesprod,
                                                    cm_producto.nund,
                                                    tb_unimed.cabrevia,
                                                    FORMAT( tb_pedidodet.total, 2 ) AS total,
                                                    tb_pedidodet.idpedido,
                                                    tb_pedidodet.nroparte,
                                                    monedas.cabrevia AS moneda,
                                                    tb_pedidodet.total AS total_numero 
                                                FROM
                                                    lg_ordendet
                                                    INNER JOIN cm_producto ON lg_ordendet.id_cprod = cm_producto.id_cprod
                                                    INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed
                                                    INNER JOIN tb_pedidodet ON lg_ordendet.niddeta = tb_pedidodet.iditem
                                                    INNER JOIN tb_parametros AS monedas ON lg_ordendet.nmonref = monedas.nidreg 
                                                WHERE
                                                    lg_ordendet.id_regmov = :id");
                $sql->execute(["id"=>$id]);
                $rowCount = $sql->rowCount();
                $item = 1;
                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()){
                        $salida.='<tr data-grabado="1" 
                                        data-total="'.$rs['total_numero'].'" 
                                        data-codprod="'.$rs['id_cprod'].'" 
                                        data-itPed="'.$rs['niddeta'].'">
                                    <td class="textoCentro"><i class="fas fa-ban"></i></td>
                                    <td class="textoCentro">'.str_pad($item++,3,0,STR_PAD_LEFT).'</td>
                                    <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                    <td class="pl20px">'.$rs['cdesprod'].'</td>
                                    <td class="textoCentro">'.$rs['cabrevia'].'</td>
                                    <td class="textoDerecha pr5px"><input type="number" 
                                                                    step="any" 
                                                                    placeholder="0.00" 
                                                                    onchange="(function(el){el.value=parseFloat(el.value).toFixed(2);})(this)"
                                                                    onclick="this.select()"
                                                                    value='.$rs['ncanti'].'>
                                    </td>
                                    <td class="textoDerecha pr5px">
                                    <input type="number"
                                        step="any" 
                                        placeholder="0.00" 
                                        onclick="this.select()"
                                        onchange="(function(el){el.value=parseFloat(el.value).toFixed(2);})(this)"
                                        value='.$rs['nunitario'].'
                                        class="textoDerecha">
                                    </td>
                                    <td class="textoDerecha pr5px">'.$rs['ntotal'].'</td>
                                    <td class="textoCentro">'.$rs['nroparte'].'</td>
                                    <td class="textoCentro">'.str_pad($rs['idpedido'],6,0,STR_PAD_LEFT).'</td>
                                </tr>';
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function consultarComentarios($id){
            try {
                $salida="";
                $sql = $this->db->connect()->prepare("SELECT
                                                    UPPER(lg_ordencomenta.ccomenta) AS ccomenta,
                                                    lg_ordencomenta.ffecha,
                                                    tb_user.cnombres 
                                                FROM
                                                    lg_ordencomenta
                                                    INNER JOIN tb_user ON lg_ordencomenta.id_cuser = tb_user.iduser 
                                                WHERE
                                                    lg_ordencomenta.id_regmov = :id
                                                ORDER BY fregsys DESC");
                $sql->execute(["id"=>$id]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()){
                        $salida.='<tr data-grabar="1">
                                    <td >'.$rs['cnombres'].'</td>
                                    <td><input type="date" value="'.$rs['ffecha'].'" readonly></td>
                                    <td>'.$rs['ccomenta'].'</td>
                                    <td class="con_borde centro"><a href="#"><i class="far fa-trash-alt"></i></a></td>
                                </tr>';
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function consultarTotalComentarios($id){
            try {
                $salida="";
                $sql = $this->db->connect()->prepare("SELECT
                                                COUNT(id_regmov) AS comments__number
                                            FROM
                                                lg_ordencomenta
                                            WHERE
                                                lg_ordencomenta.id_regmov = :id");
                $sql->execute(["id"=>$id]);
                $result = $sql->fetchAll();

                return $result[0]["comments__number"];
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function calculaTotalOrden($id){
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                        FORMAT( SUM( lg_ordendet.ncanti * lg_ordendet.nunitario ) + lg_ordendet.nigv, 2 ) AS total 
                                                    FROM
                                                        lg_ordendet 
                                                    WHERE
                                                        lg_ordendet.id_regmov = :id");
                $sql->execute(["id"=>$id]);
                $result = $sql->fetchAll();

                return $result[0]["total"];
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function verAdjuntosOrden($id){
            try {
                $salida = "";
                $sql = $this->db->connect()->prepare("SELECT creferencia,cdocumento 
                                                        FROM lg_regdocumento 
                                                        WHERE nidrefer=:id
                                                        AND cmodulo='ORD'");
                $sql->execute(['id'=>$id]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()) {
                        $salida .= '<li><a href="'.$rs['creferencia'].'" data-archivo="'.$rs['creferencia'].'"><i class="far fa-file"></i><p>'.$rs['cdocumento'].'</p></a></li>';
                    }
                }
                
                $ret = array("adjuntos"=>$salida,
                            "archivos"=>$rowCount);

                return $ret;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        //recepcion
        public function apruebaRecepción(){
            try {
                $salida = "";
                $sql = $this->db->connect()->query("SELECT
                                                tb_user.iduser,
                                                tb_user.cnombres 
                                            FROM
                                                tb_user 
                                            WHERE
                                                tb_user.nrol = 4");
                $sql->execute();
                $rowCount = $sql->rowCount();

                if ($rowCount > 0){
                    while ($rs = $sql->fetch()){
                        $salida .='<li><a href="'.$rs['iduser'].'" >'.$rs['cnombres'].'</a></li>';
                    }

                    return $salida;
                } 
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function consultarNotaID($indice,$clase){
            try {
                $sql=$this->db->connect()->prepare("SELECT
                                                        ibis.alm_recepcab.id_regalm,
                                                        ibis.alm_recepcab.ctipmov,
                                                        ibis.alm_recepcab.ncodmov,
                                                        ibis.alm_recepcab.nnronota,
                                                        ibis.alm_recepcab.cper,
                                                        ibis.alm_recepcab.cmes,
                                                        ibis.alm_recepcab.ncodalm1,
                                                        ibis.alm_recepcab.ffecdoc,
                                                        ibis.alm_recepcab.cnumguia,
                                                        ibis.alm_recepcab.ncodpry,
                                                        ibis.alm_recepcab.ncodarea,
                                                        ibis.alm_recepcab.ncodcos,
                                                        ibis.alm_recepcab.idref_pedi,
                                                        ibis.alm_recepcab.idref_abas,
                                                        ibis.alm_recepcab.id_userAprob,
                                                        ibis.alm_recepcab.nEstadoDoc,
                                                        ibis.tb_proyectos.ccodproy,
                                                        UPPER( ibis.tb_proyectos.cdesproy ) AS proyecto,
                                                        ibis.tb_area.ccodarea,
                                                        UPPER ( ibis.tb_area.cdesarea ) AS area,
                                                        ibis.tb_user.cnombres,
                                                        CONCAT_WS( ' ', rrhh.tabla_aquarius.nombres, rrhh.tabla_aquarius.apellidos ) AS nombres,
                                                        ibis.tb_pedidocab.idsolicita,
                                                        ibis.tb_almacen.ccodalm,
                                                        UPPER( ibis.tb_almacen.cdesalm ) AS almacen,
                                                        ibis.alm_recepcab.nnromov,
                                                        ibis.tb_parametros.cdescripcion,
                                                        ibis.cm_entidad.id_centi,
                                                        ibis.cm_entidad.cnumdoc,
                                                        ibis.cm_entidad.crazonsoc,
                                                        LPAD( ibis.tb_pedidocab.nrodoc, 6, 0 ) AS pedido,
                                                        ibis.lg_ordencab.cnumero AS orden,
                                                        UPPER( ibis.tb_pedidocab.concepto ) AS concepto,
                                                        UPPER( ibis.tb_pedidocab.detalle ) AS detalle,
                                                        estados.cabrevia AS estado,
	                                                    ibis.alm_recepcab.nflgCalidad 
                                                    FROM
                                                        ibis.alm_recepcab
                                                        INNER JOIN ibis.tb_proyectos ON alm_recepcab.ncodpry = tb_proyectos.nidreg
                                                        INNER JOIN ibis.tb_area ON alm_recepcab.ncodarea = tb_area.ncodarea
                                                        INNER JOIN ibis.tb_user ON alm_recepcab.id_userAprob = tb_user.iduser
                                                        INNER JOIN ibis.tb_pedidocab ON ibis.alm_recepcab.idref_pedi = ibis.tb_pedidocab.idreg
                                                        INNER JOIN rrhh.tabla_aquarius ON ibis.tb_pedidocab.idsolicita = rrhh.tabla_aquarius.internal
                                                        INNER JOIN ibis.tb_almacen ON ibis.alm_recepcab.ncodalm1 = ibis.tb_almacen.ncodalm
                                                        INNER JOIN ibis.tb_parametros ON ibis.alm_recepcab.ncodmov = ibis.tb_parametros.nidreg
                                                        INNER JOIN ibis.cm_entidad ON ibis.alm_recepcab.id_centi = ibis.cm_entidad.id_centi
                                                        INNER JOIN ibis.lg_ordencab ON ibis.alm_recepcab.idref_abas = ibis.lg_ordencab.id_regmov
                                                        INNER JOIN ibis.tb_parametros AS estados ON ibis.alm_recepcab.nEstadoDoc = estados.nidreg 
                                                    WHERE
                                                        alm_recepcab.id_regalm = :id 
                                                        LIMIT 1");
                $sql->execute(["id"=>$indice]);

                $rowCount = $sql->rowCount();
                
                if ($rowCount > 0) {
                    $docData = array();
                    while($row=$sql->fetch(PDO::FETCH_ASSOC)){
                        $docData[] = $row;
                    }
                }

                return array("cabecera"=>$docData,
                            "detalles"=>$this->detallesNota($indice,$clase),
                            "series"=>$this->seriesNota($indice),
                            "adjuntos"=>$this->adjuntosNota($indice));

            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }

        private function detallesNota($indice,$clase){
            try {
                $salida='<tr><td colspan="12">No hay registros</td></tr>';
                $sql=$this->db->connect()->prepare("SELECT
                                                    alm_recepdet.niddeta,
                                                    alm_recepdet.id_regalm,
                                                    alm_recepdet.ncodalm1,
                                                    alm_recepdet.id_cprod,
                                                    FORMAT(alm_recepdet.ncantidad, 2) AS ncantidad,
                                                    alm_recepdet.niddetaPed,
                                                    alm_recepdet.niddetaOrd,
                                                    alm_recepdet.nestadoreg,
                                                    cm_producto.ccodprod,
                                                    UPPER(
                                                        CONCAT_WS(
                                                            ' ',
                                                            cm_producto.cdesprod,
                                                            tb_pedidodet.observaciones,
                                                            tb_pedidodet.docEspec
                                                        )
                                                    ) AS cdesprod,
                                                    FORMAT(lg_ordendet.ncanti, 2) AS cantidad,
                                                    tb_unimed.cabrevia,
                                                    alm_recepdet.cobserva,
                                                    alm_recepdet.fvence
                                                FROM
                                                    alm_recepdet
                                                INNER JOIN tb_pedidodet ON alm_recepdet.niddetaPed = tb_pedidodet.iditem
                                                INNER JOIN cm_producto ON alm_recepdet.id_cprod = cm_producto.id_cprod
                                                INNER JOIN lg_ordendet ON alm_recepdet.niddetaOrd = lg_ordendet.nitemord
                                                INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed
                                                WHERE
                                                    alm_recepdet.id_regalm = :id");
                $sql->execute(['id'=>$indice]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    $salida="";
                    while($rs = $sql->fetch()){
                        $item = 1;
                        
                        $fecha = $rs['fvence'] == "" ? date("d/m/Y", strtotime($rs['fvence'])) : "";
                        
                        $salida .= '<tr data-detorden="'.$rs['niddetaOrd'].'" 
                                        data-idprod="'.$rs['id_cprod'].'"
                                        data-iddetped="'.$rs['niddetaPed'].'"
                                        data-iddetnota="'.$rs['niddeta'].'">
                                        <td class="textoCentro"><a href="'.$rs['id_regalm'].'"><i class="fas fa-barcode"></i></a></td>
                                        <td class="textoCentro">'.str_pad($item++,3,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                        <td class="pl20px">'.$rs['cdesprod'].'</td>
                                        <td class="textoCentro">'.$rs['cabrevia'].'</td>
                                        <td class="pr20px textoDerecha">'.$rs['cantidad'].'</td>
                                        <td class="pr20px textoDerecha"><input type="text" value="'.$rs['ncantidad'].'" readonly></td>
                                        <td class="pr20px textoDerecha"></td>
                                        <td><input type="text" value="'.$rs['cobserva'].'"></td>
                                        <td></td>
                                    </tr>';
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }

        private function seriesNota($indice){
            try {
                $salida="";
                $item = 1;
                $sql=$this->db->connect()->prepare("SELECT
                                                    alm_recepserie.ncodserie,
                                                    alm_recepserie.id_cprod,
                                                    alm_recepserie.idref_movi,
                                                    alm_recepserie.idref_alma,
                                                    alm_recepserie.cdesserie,
                                                    alm_recepserie.cdetalle,
                                                    cm_producto.cdesprod 
                                                FROM
                                                    alm_recepserie
                                                    INNER JOIN cm_producto ON alm_recepserie.id_cprod = cm_producto.id_cprod 
                                                WHERE
                                                    alm_recepserie.idref_movi = :id");
                $sql->execute(['id'=>$indice]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while($rs = $sql->fetch()){
                        $salida .= '<tr>
                                        <td>'.$item++.'</td>
                                        <td>'.$rs['cdesserie'].'</td>
                                    </tr>';
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }

        private function adjuntosNota($indice){
            try {
                $salida="";
                $sql=$this->db->connect()->prepare("SELECT
                                                    lg_regdocumento.id_regmov,
                                                    lg_regdocumento.nidrefer,
                                                    lg_regdocumento.creferencia,
                                                    lg_regdocumento.cdocumento,
                                                    lg_regdocumento.cmodulo 
                                                FROM
                                                    lg_regdocumento 
                                                WHERE
                                                    lg_regdocumento.nidrefer = :id 
                                                    AND lg_regdocumento.cmodulo = 'NI'");
                $sql->execute(['id'=>$indice]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while($rs = $sql->fetch()){
                        $salida .= '<li><p><i class="far fa-file"></i></p>
                                    <p>'.$rs['cdocumento'].'</p></li>';
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }

        public function genNumberSalidas($cod){
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                            COUNT(alm_despachocab.nnromov) AS nummov 
                                                        FROM
                                                            alm_despachocab 
                                                        WHERE
                                                            alm_despachocab.ncodalm1 = :cod");
                $sql->execute(["cod"=>$cod]);

                $row = $sql->fetchAll();

                return $row[0]['nummov'];

            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function genNumberIngresos($cod){
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                            COUNT(alm_recepcab.nnromov) AS nummov 
                                                        FROM
                                                            alm_recepcab 
                                                        WHERE
                                                            alm_recepcab.ncodalm1 = :cod");
                $sql->execute(["cod"=>$cod]);

                $row = $sql->fetchAll();

                return $row[0]['nummov'];

            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }
    }
?>