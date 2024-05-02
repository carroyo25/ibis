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
?>