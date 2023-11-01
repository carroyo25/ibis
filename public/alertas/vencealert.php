<?php 
    date_default_timezone_set('America/Lima');

    require_once("c:/xampp/htdocs/ibis/public/cotizacion/connect.php");
	require_once("c:/xampp/htdocs/ibis/public/PHPMailer/PHPMailerAutoload.php");

	$proyectos = verProyectos($pdo);

	foreach ($proyectos as $proyecto) {
		//echo $proyecto['cubica']."</br>";
		echo listarTieneVencimientos($pdo,$proyecto['cubica'])."</br>";
	}


	//funciones para seleccionar el ubigeo por proyecto
	function verProyectos($pdo){
		try {
            $sql = "SELECT
						tb_proyectos.cubica,
						tb_proyectos.cabrevia 
					FROM
						tb_proyectos 
					WHERE
						tb_proyectos.nflgactivo = 1 
						AND tb_proyectos.cubica != '' 
					GROUP BY
						tb_proyectos.cubica";

            $statement = $pdo->query($sql);
            $statement -> execute();
            $result = $statement ->fetchAll();
            
            return $result;

        } catch (PDOException $th) {
            echo $th->getMessage();
            return false;
        }
	}

	function listarTieneVencimientos($pdo,$sede){
		try {
            $sql = "SELECT
						COUNT(alm_existencia.vence) AS contador 
					FROM
						alm_existencia
						INNER JOIN alm_cabexist ON alm_existencia.idregistro = alm_cabexist.idreg
						INNER JOIN tb_proyectos ON alm_cabexist.idcostos = tb_proyectos.nidreg 
					WHERE
						alm_existencia.nflgActivo = 1 
						AND alm_existencia.vence <> ''
						AND tb_proyectos.cubica = ?";

            $statement = $pdo->prepare($sql);
            $statement -> execute(array($sede));
            $result = $statement ->fetchAll();
			$salida = $result[0]['contador'];
            
            return $salida;

        } catch (PDOException $th) {
            echo $th->getMessage();
            return false;
        }
	}

	function listarCorreos($proyecto,$rol){

	}

	function crearArchivos($proyecto,$Adjunto){

	}

	function enviarCorreos($listaCorreos,$Adjunto){
		$mail = new PHPMailer();
		$mail->IsSMTP(true);
		$mail->Host = 'mail.sepcon.net'; // not ssl://smtp.gmail.com
		$mail->SMTPAuth= true;
		$mail->Username='sistema_ibis@sepcon.net';
		$mail->Password='';
		$mail->Port = 587; // not 587 for ssl 
		$mail->SMTPDebug = 0; 
		$mail->SMTPSecure = 'tsl';
		$mail->SetFrom('carroyo@sepcon.net', 'Cesar');
		$mail->AddAddress('carroyo@sepcon.net', 'Cesar');
    	//$mail->AddAddress('acruz@sepcon.net', 'Cesar');
		$mail->Subject = 'Subject';
		$mail->Subject = "Correo de prueba automatica";
		$mail->Body    = "Este es un mensaje de envio automatico <b>".date("H:i:s")."</b>";
		$mail->AltBody = "Este texto es para los clientes que no tienen HTML";
	
		if(!$mail->Send()) {
			echo 'Error : ' . $mail->ErrorInfo;
		} else {
			echo 'Ok!!';
		}
	}

	

    
?>