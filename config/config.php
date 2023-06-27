<?php
    
    $url = $_SERVER['HTTP_HOST'];

    if ($url === "localhost")
        define('URL','http://localhost/ibis/');
    else if ($url === "192.168.110.16")
        define('URL','//192.168.110.16/ibis/');
    else if ($url === "200.41.86.58")
        define('URL','http://200.41.86.58/ibis/');
    else if ($url === "sicalsepcon.net")
        define('URL','http://sicalsepcon.net/ibis/');
    else if ($url === "200.115.23.164")
        define('URL','http://200.115.23.164/ibis/');
    
    define('HOST','localhost');
    //define('HOST1','localhost');
    //define('HOST','192.168.1.30');
    define('HOST1','192.168.1.30');
    
    
    define('DB','rrhh');
    define('DB2','ibis');
    define('DB3','documentos');
    define('MAILPASSWORD','aK8izG1WEQwwB1X');
    define('MAILUSER','sistema_ibis@sepcon.net');
    define('CHARSET','UTF8');
    define('USER','remoto');
    define('PASSWORD','s3pc0n2020');
    
    //define('USER','root');
    //define('PASSWORD','zBELTUAKpNQvCOl6'); 

    define('VERSION',rand(0, 15000));
?>