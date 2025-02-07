<?php

    date_default_timezone_set('America/Lima');

    require_once('connect.php');
    
    if ( isset($_POST) ) {
        if ( isset($_POST['funcion']) && $_POST['funcion'] === "getEntiByRuc") {
            echo json_encode(getEntiByRuc($pdo,$_POST['ruc'])); 
        }
    }

    function getCountries($pdo) {
        $datos = [];

        try{
            $sql = "SELECT tb_pais.ccodpais,tb_pais.cdespais,tb_pais.ncodpais FROM tb_pais ORDER BY tb_pais.cdespais";
            $statement = $pdo->query($sql);
            $rowaffect = $statement->rowCount($sql);

            while($row = $statement->fetch(PDO::FETCH_ASSOC)){
                $docData[] = $row;
            }

            return $docData;

        }catch(PDOException $e){
            echo $th->getMessage();
            return false;
        }
    }

    function getPaymentList($pdo){
        try {
            $sql = "SELECT
                        tb_parametros.nidreg,
                        tb_parametros.cdescripcion 
                    FROM
                        tb_parametros
                    WHERE
                        tb_parametros.cclase = 11
                        AND tb_parametros.ccod != '00'
                    ORDER BY
                        tb_parametros.cdescripcion";
                        $statement = $pdo->query($sql);
                        $rowaffect = $statement->rowCount($sql);

            while($row = $statement->fetch(PDO::FETCH_ASSOC)){
                $docData[] = $row;
            }

            return $docData;
        } catch(PDOException $e){
            echo $th->getMessage();
            return false;
        }
    }

    function getEconomicActivity($pdo){
        try {
            $sql = "SELECT
                        tb_parametros.nidreg,
                        tb_parametros.cdescripcion 
                    FROM
                        tb_parametros
                    WHERE
                        tb_parametros.cclase = 15
                        AND tb_parametros.ccod != '00'
                    ORDER BY
                        tb_parametros.cdescripcion";
                        $statement = $pdo->query($sql);
                        $rowaffect = $statement->rowCount($sql);

            while($row = $statement->fetch(PDO::FETCH_ASSOC)){
                $docData[] = $row;
            }

            return $docData;
        } catch(PDOException $e){
            echo $th->getMessage();
            return false;
        }
    }

    function registerProveedor($cabecera){
        try {
            $sql = $this->db->connect()->prepare("INSERT INTO cm_entidad (cnumdoc, crazonsoc, cviadireccion, cemail, ctelefono, ncodpais) 
            VALUES (:numdoc, :razonsocial, :direccion, :email, :telefono, :codpais");
            $sql->execute(["numdoc"=>$cabecera["codigo_costos"],
                            "razonsocial"=>$cabecera["fecha"],
                            "direccion"=>$cabecera["codigo_almacen"],
                            "email"=>$cabecera["codigo_tipo"],
                            "telefono"=>$cabecera["fechaIngreso"],
                            "codpais"=>$cabecera["codigo_autoriza"]
                        ]);

            /* $rowCount = $sql->rowCount();

            if ($rowCount > 0){
                $indice = $this->nuevoRegistro();
                $this->grabarDetallesAjustes($detalles,$indice["indice"],$cabecera["codigo_tipo"],$cabecera["codigo_almacen"]);
                $mensaje = "Registrado Correctamente";
            }
            else {
                $mensaje = "Hubo un error en el registro";
            }

            return array("mensaje"=>$mensaje); */
        } catch (PDOException $th) {
            echo "Error: ".$th->getMessage();
            return false;
        }
    }

    function getEntiByRuc($pdo,$ruc){
        try {
            $sql  = "SELECT cm_entidad.cnumdoc,
                            cm_entidad.crazonsoc,
                            cm_entidad.cpassword,
                            cm_entidad.cviadireccion,
                            cm_entidad.ctelefono,
                            cm_entidad.cemail,
                            cm_entidad.ncodpais,
                            cm_entidad.ncondpag,
                            cm_entidad.nrubro,
                            cm_entidad.id_centi,
                            cm_entidad.nflgactualizado
                        FROM cm_entidad 
                        WHERE cm_entidad.cnumdoc = :ruc";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([':ruc' => $ruc]);
            $rowdetalle = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $rowdetalle;

        } catch(PDOException $e){
            echo $e->getMessage();
            return false;
        }
    }

    function getDetailsById($pdo,$id){
        try {

            $rowdetalle = [];

            $sql = "SELECT cm_detallenti.nomgercomer,
                            cm_detallenti.telgercomer,
                            cm_detallenti.corgercomer,
                            cm_detallenti.nomcontacto,
                            cm_detallenti.telcontacto,
                            cm_detallenti.corcontacto,
                            cm_detallenti.nomperdetra,
                            cm_detallenti.telperdetra,
                            cm_detallenti.corperdetra,
                            cm_detallenti.nctadetrac
                    FROM
                        cm_detallenti
                    WHERE 
                        cm_detallenti.idcenti = :id
                        AND cm_detallenti.nflgActivo = 1";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            $rowdetalle = $stmt->fetch(PDO::FETCH_ASSOC);

            return $rowdetalle;

        } catch(PDOException $e){
            echo $th->getMessage();
            return false;
        }
    }

    function getEntityBancs($pdo,$id){
        try {

            $bancos = [];

            $sql = "SELECT
                    cm_entidadbco.nitem,
                    cm_entidadbco.ncodbco,
                    cm_entidadbco.cnrocta,
                    cm_entidadbco.cmoneda,
                    cm_entidadbco.ctipcta,
                    bancos.cdescripcion AS banco,
                    monedas.cdescripcion AS moneda,
                    cuentas.cdescripcion AS cuenta
                FROM
                    cm_entidadbco
                    LEFT JOIN tb_parametros AS bancos ON bancos.nidreg = cm_entidadbco.ncodbco
                    LEFT JOIN tb_parametros AS monedas ON monedas.nidreg = cm_entidadbco.cmoneda
                    LEFT JOIN tb_parametros AS cuentas ON cuentas.nidreg = cm_entidadbco.ntipcta
                WHERE
                    cm_entidadbco.nflgactivo = 7 
                    AND cm_entidadbco.id_centi = :id
                    AND NOT ISNULL(cm_entidadbco.ntipcta)";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $id]);

            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                $bancos[] = $row;
            }

            return $bancos;
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }
?>