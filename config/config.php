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

        
        
    //define('HOST','localhost');
    //define('HOST1','localhost');
    define('HOST','192.168.1.30');
    define('HOST1','192.168.1.30');
    define('DB','rrhh');
    define('DB2','ibis');
    define('USER','root');
    define('MAILPASSWORD','aK8izG1WEQwwB1X');
    define('MAILUSER','sistema_ibis@sepcon.net');
    define('CHARSET','utf8mb4');
    define('PASSWORD','zBELTUAKpNQvCOl6'); 


    define('VERSION',rand(0, 15000));
?>