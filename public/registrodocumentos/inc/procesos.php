<?php
    require('connect.php');

    session_start();

    if(isset($_POST['funcion'])){
        if($_POST['funcion'] == "login"){
            echo json_encode(login($pdo, $_POST));
        }
    }

    function login($pdo, $datos){
        try {
            var_dump($datos);
        }  catch(PDOException $e){
            echo "Error al guardar los datos: " . $e->getMessage();
            $pdo->rollBack();
        }
    }
?>