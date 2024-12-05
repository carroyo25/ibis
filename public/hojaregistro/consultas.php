<?php
    require_once('connect.php');
    

    function getCountries($pdo) {
        $datos = [];

        try{
            $sql = "SELECT tb_pais.ccodpais,tb_pais.cdespais FROM tb_pais ORDER BY tb_pais.cdespais";
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

    function getProveedorById($pdo, $id){
        try {
            $sql = "SELECT
                        *
                    FROM
                        cm_entidad
                    WHERE
                        id_centi = :id";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            $sqldetalle = "SELECT
                        *
                    FROM
                        cm_detalle_entidad
                    WHERE
                        id_entidad = :id";

            $stmtd = $pdo->prepare($sqldetalle);
            $stmtd->execute([':id' => $id]);
            $rowdetalle = $stmtd->fetchAll(PDO::FETCH_ASSOC);
            /* while($rowdetalle = $stmtd->fetch(PDO::FETCH_ASSOC)){
                $detalle[] = $row;
            } */

            $proveedor = [
                'id_centi' => $row['id_centi'],
                'cnumdoc' => $row['cnumdoc'],
                'crazonsoc' => $row['crazonsoc'],
                'cviadireccion' => $row['cviadireccion'],
                'cemail' => $row['cemail'],
                'ctelefono' => $row['ctelefono'],
                'ncodpais' => $row['ncodpais'],
                'cwebpage' => $rowdetalle[0]['cwebpage'],
                'cnomgerentec' => $rowdetalle[0]['cnomgerentec'],
                'cnumdocgerentec' => $rowdetalle[0]['cnumdocgerentec'],
                'ctelgerentec' => $rowdetalle[0]['ctelgerentec'],
                'cemailgerentec' => $rowdetalle[0]['cemailgerentec'],
                'cnomcontacto' => $rowdetalle[0]['cnomcontacto'],
                'cnumdoccontacto' => $rowdetalle[0]['cnumdoccontacto'],
                'ctelcontacto' => $rowdetalle[0]['ctelcontacto'],
                'cemailcontacto' => $rowdetalle[0]['cemailcontacto'],
                'ccuentadetrac' => $rowdetalle[0]['ccuentadetrac'],
                'nformapago' => $rowdetalle[0]['nformapago'],
                'nacteconomica' => $rowdetalle[0]['nacteconomica'],
                'nnumcuentas' => count($rowdetalle),
                'cficharuc' => $row['cficharuc'],
                'ccatalogo' => $row['ccatalogo'],
                'detalle' => $rowdetalle
                /* 'cnomgerentec' => rowdetalle[0]['cnomgerentec'],
                'cnumdocgerentec' => rowdetalle[0]['cnumdocgerentec'],
                'ctelgerentec' => rowdetalle[0]['ctelgerentec'], */
                /* 'cemailgerentec' => rowdetalle[0]['cemailgerentec'],
                'cnomcontacto' => rowdetalle[0]['cnomcontacto'],
                'cnumdoccontacto' => rowdetalle[0]['cnumdoccontacto'],
                'ctelcontacto' => rowdetalle[0]['ctelcontacto'],
                'cemailcontacto' => rowdetalle[0]['cemailcontacto'] */
            ];


            return $proveedor;
        } catch(PDOException $e){
            echo $th->getMessage();
            return false;
        }
    }

    function getEntiByRuc($pdo,$ruc){
        try {
            $sql  = "SELECT cm_entidad.cnumdoc,
                            cm_entidad.crazonsoc,
                            cm_entidad.cviadireccion,
                            cm_entidad.ctelefono,
                            cm_entidad.cemail
                        FROM cm_entidad 
                        WHERE cm_entidad.cnumdoc = :ruc";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([':ruc' => $ruc]);
            $rowdetalle = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $rowdetalle;

        } catch(PDOException $e){
            echo $th->getMessage();
            return false;
        }
    }
?>