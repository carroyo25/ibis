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
                                    <a href="'.$row['nidreg'].'">'.$row['nidreg'].' '.$row['cdescripcion'].'</a>
                                 </li>';
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
                        $salida .= '<option value="'.$rs['nidreg'].'" data-abrevia"">'.$rs['cdescripcion'].'</option>';
                    }
                } 

                return $salida;
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
                        $salida .='<li><a href="'.$rs['ncodgrupo'].'">'.$rs['ccodcata'] .' - '.strtoupper($rs['cdescrip']).'</a></li>';
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
                        $salida .='<li><a href="'.$rs['ncodclase'].'" data-catalogo="'.$rs['ccodcata'] .'">'.$rs['ccodcata'] .' - '.strtoupper($rs['cdescrip']).'</a></li>';
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
                        $salida .='<tr class="pointer" data-id="'.$rs['ncodgrupo'].'">
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
                                                    AND tb_proyectos.nflgactivo = 1");
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

        //genera los numero de los docuentos
        public function generarNumero($id,$query){
            try {
                $sql = $this->db->connect()->prepare($query);
                $sql->execute(["cod"=>$id]);
                $result = $sql->fetchAll();

                return $salida = array("numero"=>str_pad($result[0]['numero'] + 1,6,0,STR_PAD_LEFT),
                                        "codigo"=>uniqid()); 
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

         //listado de productos
        public function listarProductos($tipo){
             try {
                 $salida = "";
                 $sql = $this->db->connect()->prepare("SELECT
                                                        cm_producto.id_cprod,
                                                        cm_producto.ccodprod,
                                                        cm_producto.cdesprod,
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

        public function buscarRol($rol,$cc){
            try {
                $salida = "";

                if ($rol != 3){
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
                                                        AND ibis.tb_costusu.ncodproy = :cc");
                    $sql->execute(["rol"=>$rol,
                    "cc"=>$cc]);
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
                    
                }
                
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
                                                        ibis.tb_user.cnombres
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
                }
                    

                return array("cabecera"=>$docData,
                            "detalles"=>$detalles);
            } catch (PDOException $th) {
                echo $th->getMessage();
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
                                        <td class="pl20px">'.$rs['cdesprod'].'</td>
                                        <td class="textoCentro">'.$rs['cabrevia'].'</td>
                                        <td>
                                            <input type="number" 
                                                        step="any" 
                                                        placeholder="0.00" 
                                                        onchange="(function(el){el.value=parseFloat(el.value).toFixed(2);})(this)"
                                                        onclick="this.select()" 
                                                        value="'.$rs['cant_pedida'].'">
                                        </td>
                                        <td></td>
                                        <td class="textoCentro"><input type="checkbox" '.$checked.'></td>
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
                        $salida .='<tr data-grabado="1" data-idprod="'.$rs['idprod'].'" data-codund="'.$rs['unid'].'" data-idx="'.$rs['iditem'].'">
                                        <td class="textoCentro"><a href="'.$rs['idprod'].'"><i class="far fa-eye"></i></a></td>
                                        <td class="textoCentro">'.str_pad($filas++,3,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                        <td class="pl20px">'.$rs['cdesprod'].'</td>
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
                        
                        $salida .='<tr data-grabado="1" data-idprod="'.$rs['idprod'].'" data-codund="'.$rs['unid'].'" data-idx="'.$rs['iditem'].'">
                                        <td class="textoCentro">'.str_pad($filas++,3,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                        <td class="pl20px">'.$rs['cdesprod'].'</td>
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
                        
                        $salida .='<tr data-grabado="1" data-idprod="'.$rs['idprod'].'" data-codund="'.$rs['unid'].'" data-idx="'.$rs['iditem'].'">
                                        <td class="textoCentro"><input type="checkbox" checked></td>
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
    }
?>