<?php 
    date_default_timezone_set('America/Lima');

    require_once("c:/xampp/htdocs/ibis/public/cotizacion/connect.php");
	require_once("c:/xampp/htdocs/ibis/public/PHPMailer/PHPMailerAutoload.php");
	require_once("c:/xampp/htdocs/ibis/public/PHPExcel/PHPExcel.php");

	$proyectos = verProyectos($pdo);

	foreach ($proyectos as $proyecto) {
		$vencimiento = intval(listarTieneVencimientos($pdo,$proyecto['cubica']));
		
		if ( $vencimiento > 0 ) {
			crearArchivos($pdo,$proyecto['cubica']);
		}
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

	function crearArchivos($pdo,$proyecto){
		try {
			$sql = "SELECT
							alm_existencia.idreg,
							alm_existencia.codprod,
							alm_existencia.freg,
							DATE_FORMAT( alm_existencia.vence, '%d/%m/%Y' ) AS vence,
							cm_producto.ccodprod,
							UPPER( cm_producto.cdesprod ) AS producto,
							DATEDIFF( NOW(), alm_existencia.vence ) AS pasados,
							alm_existencia.nguia,
							alm_cabexist.idcostos,
							tb_proyectos.ccodproy,
							tb_unimed.cabrevia,
							SUM( alm_existencia.cant_ingr ) AS cant_ingr,
							alm_existencia.cant_ord,
							s.consumo 
						FROM
							alm_existencia
							LEFT JOIN cm_producto ON alm_existencia.codprod = cm_producto.id_cprod
							LEFT JOIN alm_cabexist ON alm_existencia.idregistro = alm_cabexist.idreg
							INNER JOIN tb_proyectos ON alm_cabexist.idcostos = tb_proyectos.nidreg
							INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed
							LEFT JOIN (
							SELECT
								SUM( alm_consumo.cantsalida ) AS consumo,
								alm_consumo.idprod,
								alm_consumo.ncostos 
							FROM
								alm_consumo 
							WHERE
								alm_consumo.flgactivo = 1 
							GROUP BY
								alm_consumo.idprod 
							) AS s ON s.idprod = alm_existencia.codprod 
						WHERE
							alm_existencia.vence <> '' 
							AND alm_existencia.nflgActivo = 1 
							AND tb_proyectos.cubica = ?
							AND cm_producto.ntipo = 37
						GROUP BY
							alm_existencia.codprod 
						ORDER BY
							cm_producto.cdesprod ASC";

			$statement = $pdo->prepare($sql);
			$statement -> execute(array($proyecto));

			$objPHPExcel = new PHPExcel();
                $objPHPExcel->getProperties()
                    ->setCreator("Sical")
                    ->setLastModifiedBy("Sical")
                    ->setTitle("Cargo Plan")
                    ->setSubject("Template excel")
                    ->setDescription("Reporte Vencimientos")
                    ->setKeywords("Template excel");

                    $objWorkSheet = $objPHPExcel->createSheet(1);

                    $objPHPExcel->setActiveSheetIndex(0);
                    $objPHPExcel->getActiveSheet()->setTitle("Vencimientos");
    
    
                    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');
                    $objPHPExcel->getActiveSheet()->mergeCells('A1:I1');
                    $objPHPExcel->getActiveSheet()->setCellValue('A1','VENCIMIENTOS DE PRODUCTOS');
    
                    $objPHPExcel->getActiveSheet()->getStyle('A1:AP2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $objPHPExcel->getActiveSheet()->getStyle('A1:AP2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    
                    $objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(60);

                    $objPHPExcel->getActiveSheet()->getColumnDimension("A")->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension("B")->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension("C")->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension("D")->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension("E")->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension("F")->setAutoSize(true);

                    $objPHPExcel->getActiveSheet()->setCellValue('A2','Items'); // esto cambia
                    $objPHPExcel->getActiveSheet()->setCellValue('B2','Centro de Costos'); // esto cambia
                    $objPHPExcel->getActiveSheet()->setCellValue('C2','Descripcion'); // esto cambia
                    $objPHPExcel->getActiveSheet()->setCellValue('D2','Codigo'); // esto cambia
                    $objPHPExcel->getActiveSheet()->setCellValue('E2','Unidad'); // esto cambia
                    $objPHPExcel->getActiveSheet()->setCellValue('F2','Fecha Vencimiento'); // esto cambia
                    $objPHPExcel->getActiveSheet()->setCellValue('G2','Cantidad'); // esto cambia
					$objPHPExcel->getActiveSheet()->setCellValue('H2','Consumo'); // esto cambia
					$objPHPExcel->getActiveSheet()->setCellValue('I2','Saldo'); // esto cambia

                    $objPHPExcel->getActiveSheet()
                            ->getStyle('A2:I2')
                            ->getFill()
                            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB('BFCDDB');

					$rowCount = $statement->rowCount();
					$item = 1;
					$fila = 3;

				 	if($rowCount > 0) {
						while($rs = $statement->fetch()){

							$saldo     = $rs['cant_ingr'] - $rs['consumo'];

							if (  $rs['consumo'] < $rs['cant_ingr'] ) {
								$objPHPExcel->getActiveSheet()->setCellValue('A'.$fila,$item++);
								$objPHPExcel->getActiveSheet()->setCellValue('B'.$fila,$rs['ccodproy']);
								$objPHPExcel->getActiveSheet()->setCellValue('D'.$fila,$rs['producto']);
								$objPHPExcel->getActiveSheet()->setCellValue('C'.$fila,$rs['ccodprod']);
								$objPHPExcel->getActiveSheet()->setCellValue('E'.$fila,$rs['cabrevia']);
								$objPHPExcel->getActiveSheet()->setCellValue('F'.$fila,$rs['vence']);
								$objPHPExcel->getActiveSheet()->setCellValue('G'.$fila,$rs['cant_ingr']);
								$objPHPExcel->getActiveSheet()->setCellValue('H'.$fila,$rs['consumo']);
								$objPHPExcel->getActiveSheet()->setCellValue('I'.$fila,$saldo);

								$fila++;
							}
						}
					}


                    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');
                    $objWriter->save('c:/xampp/htdocs/ibis/public/documentos/reportes/auto'.$proyecto.'.xlsx');
    
                    return array("documento"=>'c:/xampp/htdocs/ibis/public/documentos/reportes/auto'.$proyecto.'.xlsx');
    
                    exit();
    
                    return $salida;
		} catch (PDOException $th) {
            echo $th->getMessage();
            return false;
        }
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