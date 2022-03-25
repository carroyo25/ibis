<?php
    
    $url = $_SERVER['HTTP_HOST'];

    if ($url === "localhost"){
        define('URL','http://localhost/ibis/');
        define('PASSWORD','zBELTUAKpNQvCOl6');
    }else {
        define('URL','http://200.41.86.61:3000/ibis/');
        define('PASSWORD','odigo72');
    }
        
    define('HOST','localhost');
    define('DB','ibis');
    define('DB2','rrhh');
    define('USER','root');
    define('MAILPASSWORD','aK8izG1WEQwwB1X');
    define('MAILUSER','sistema_ibis@sepcon.net');
    define('CHARSET','utf8mb4'); 


    define('VERSION',rand(0, 15000));
?>