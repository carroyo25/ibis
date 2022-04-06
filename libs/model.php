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
                $query = $this->db->connectrrhh()->query("SELECT dni, CONCAT(apellidos,', ',nombres) AS nombres, internal,ccargo,dcargo FROM tabla_aquarius 
                                                        WHERE estado = 'AC' ORDER BY apellidos ASC");
                $query->execute();
                $rowcount = $query->rowcount();

                if ($rowcount > 0) {
                    while ($row = $query->fetch()) {
                        $salida.='<li>
                                    <a href="'.$row['internal'].'" data-ccargo="'.$row['ccargo'].'" data-dcargo="'.$row['dcargo'].'">'.$row['nombres'].'</a>
                                 </li>';
                    }
                }
                return $salida;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function actualizarCabecera($tabla,$valor,$id){

        }

        public function actualizarDetalles($tabla,$valor,$detalles,$id){
            
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
                $sql = $this->db->connect()->prepare("SELECT ncodclase,ccodcata,cdescrip 
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
                                                        ORDER BY cdescrip DESC");
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
    }
?>