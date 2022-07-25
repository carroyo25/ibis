<?php
    
    $url = $_SERVER['HTTP_HOST'];

    /*if ($url === "localhost"){
        define('URL','http://localhost/ibis/');
        define('PASSWORD','s3pc0n2020');
    }else {
        define('URL','http://200.41.86.61:3000/ibis/');
        define('PASSWORD','odigo72');
    }*/

    if ($url === "localhost"){
        define('URL','http://localhost/ibis/');
        define('PASSWORD','s3pc0n2020');
    }else {
        define('URL','http://200.41.86.61:3000/ibis/');
        define('PASSWORD','s3pc0n2020');
    }
        
    define('HOST','192.168.1.30');
    define('DB','ibis');
    define('DB2','rrhh');
    define('USER','remoto');
    define('MAILPASSWORD','aK8izG1WEQwwB1X');
    define('MAILUSER','sistema_ibis@sepcon.net');
    define('CHARSET','utf8mb4'); 


    define('VERSION',rand(0, 15000));
?>