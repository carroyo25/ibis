<?php
    require('connect.php');

    session_start();

    if(isset($_POST['funcion'])){
        if($_POST['funcion'] == "login"){
            echo json_encode(login($pdo, $_POST));
        }
    }

    function login($pdo, $datos){
        try{
            $sql = "SELECT 
                        cm_entidad.cnumdoc,
                        cm_entidad.cpassword,
                        cm_entidad.id_centi,
                        cm_entidad.cnumdoc,
                        cm_entidad.crazonsoc 
                    FROM cm_entidad 
                    WHERE cm_entidad.cnumdoc = :ruc
                    AND cm_entidad.nflgactivo = 7
                    AND cm_entidad.cpassword = :pass
                    LIMIT 1";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([':ruc' => $datos['ruc'],
                            ':pass' => SHA1($datos['clave'])]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $count = $stmt->rowCount();
            
            $ruc_exist = false;
            $messaje = 'Entidad no registrada o contraseña incorrecta';

            if ( $count > 0 ) {
                $_SESSION['ruc'] = $result[0]['cnumdoc'];
                $_SESSION['entidad'] = $result[0]['crazonsoc'];
                $_SESSION['log'] = true;
                $messaje = "Bienvenido al registro de documentos";
                $ruc_exist = true;
                
                return ['status' => 'success', 
                        'message' => $messaje,
                        'ruc_exist'=>$ruc_exist,
                        'ruc'=>$result[0]['cnumdoc'],
                        'entidad'=>$result[0]['crazonsoc'],
                        'id'=>$result[0]['id_centi']];
            }else{
                return ['status' => 'error', 'message' => $messaje,'ruc_exist'=>false];
            }
        }catch(PDOException $e){
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    } 
?>