<?php 
	$dsn = "mysql:dbname=ibis;host=localhost";
	$user = "root";
	$password = "zBELTUAKpNQvCOl6";
	$errorDbConexion = true;

	try {
		$pdo = new PDO($dsn,$user,$password);
		$errorDbConexion = false;
	}
	catch ( PDOException $e) {
		echo 'Error al conectarnos ' . $e->getMessage();
	}

	$pdo->exec("SET CHARACTER SET utf8"); // <--utf8
?>