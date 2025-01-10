<?php 
    date_default_timezone_set('America/Lima');

    require_once("c:/xampp/htdocs/ibis/public/cotizacion/connect.php");
	require_once("c:/xampp/htdocs/ibis/public/PHPMailer/PHPMailerAutoload.php");
	require_once("c:/xampp/htdocs/ibis/public/PHPExcel/PHPExcel.php");

	$proyectos = verProyectos($pdo);
	$token = "aK8izG1WEQwwB1X";

	foreach ($proyectos as $proyecto) {
		$vencimiento = intval(listarTieneVencimientos($pdo,$proyecto['cubica']));
		
		if ( $vencimiento > 0 ) {
			crearArchivos($pdo,$proyecto['cubica'],$proyecto['cabrevia']);
		}
	}

	enviarCorreos($pdo,$token);

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

	function listarCorreos($pdo){
		try {
            $sql = "SELECT
						tb_user.iduser,
						tb_user.cnameuser,
						tb_user.ccorreo,
						tb_proyectos.cubica,
						tb_proyectos.ccodproy,
						tb_costusu.ncodproy 
					FROM
						tb_user
						INNER JOIN tb_proyectos
						INNER JOIN tb_costusu ON tb_user.iduser = tb_costusu.id_cuser 
						AND tb_proyectos.nidreg = tb_costusu.ncodproy 
					WHERE
						tb_user.nflgactivo = 1 
						AND tb_costusu.nflgactivo = 1 
						AND tb_user.nflgvence = 1  
					GROUP BY
						tb_user.iduser";

            $statement 	= $pdo->query($sql);
            $statement 	-> execute();
            $result 	= $statement->fetchAll();
			            
            return $result;

        } catch (PDOException $th) {
            echo $th->getMessage();
            return false;
        }
	}

	function verificarAdjuntos($pdo,$user){
		try {
            $sql = "SELECT
						tb_costusu.id_cuser,
						tb_proyectos.cubica,
						tb_proyectos.cabrevia
					FROM
						tb_costusu
						INNER JOIN tb_proyectos ON tb_costusu.ncodproy = tb_proyectos.nidreg
						INNER JOIN tb_user ON tb_costusu.id_cuser = tb_user.iduser 
					WHERE
						tb_costusu.nflgactivo = 1 
						AND tb_costusu.id_cuser = ?
						AND tb_proyectos.cubica <> '' 
					GROUP BY
						tb_proyectos.cubica 
					ORDER BY
						tb_user.cnameuser ASC";

            $statement 	= $pdo->prepare($sql);
            $statement 	-> execute(array($user));
            $result 	= $statement->fetchAll();
			            
            return $result;

        } catch (PDOException $th) {
            echo $th->getMessage();
            return false;
        }
	}

	function crearArchivos($pdo,$proyecto,$sede){
		try {
			$sql = "SELECT
							alm_existencia.idreg,
							alm_existencia.codprod AS codigo,
							alm_existencia.freg,
							DATE_FORMAT( alm_existencia.vence, '%d/%m/%Y' ) AS vence,
							cm_producto.ccodprod,
							UPPER( cm_producto.cdesprod ) AS producto,
							DATEDIFF( NOW(), alm_existencia.vence ) AS pasados,
							alm_existencia.nguia,
							alm_cabexist.idcostos,
							tb_proyectos.ccodproy,
							tb_unimed.cabrevia,
							( SELECT SUM( alm_existencia.cant_ingr )  FROM alm_existencia WHERE alm_existencia.codprod = codigo ) AS cant_ingr,
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
                    $objWriter->save('c:/xampp/htdocs/ibis/public/documentos/reportes/'.$proyecto."-".$sede.'.xlsx');
    
                    return array("documento"=>'c:/xampp/htdocs/ibis/public/documentos/reportes/'.$proyecto."-".$sede.'.xlsx');
    
                    exit();
    
                    return $salida;
		} catch (PDOException $th) {
            echo $th->getMessage();
            return false;
        }
	}

	function enviarCorreos($pdo,$token){
		$mail = new PHPMailer;
        $mail->isSMTP();
        $mail->SMTPDebug = 0;
        $mail->Debugoutput = 'html';
        $mail->Host = 'mail.sepcon.net';
        $mail->SMTPAuth = true;
        $mail->Username = 'sistema_ibis@sepcon.net';
        $mail->Password = $token;
        $mail->Port = 465;
        $mail->SMTPSecure = "ssl";
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => false
            )
        );

		$mail->SetFrom('sistema_ibis@sepcon.net','Sical');

		$mail->Subject = "Reporte de vencimientos";
		$mail->AltBody = "Este texto es para los clientes que no tienen HTML";

		$subject    = utf8_decode("Reporte de vencimiento");
                

        $messaje= '<div style="width:100%;display: flex;flex-direction: column;justify-content: center;align-items: center;
                            font-family: Futura, Arial, sans-serif;">
                    <div style="width: 45%;border: 1px solid #c2c2c2;background: green">
                        <h1 style="text-align: center;color:#fff">Informe del Sistema</h1>
                    </div>
                    <div style="width: 45%;
                                border-left: 1px solid #c2c2c2;
                                border-right: 1px solid #c2c2c2;
                                border-bottom: 1px solid #c2c2c2;">
                        <p style="padding:.5rem"><strong style="font-style: italic;">Estimados:</strong></p>
                        <p style="padding:.5rem;line-height: 1rem;">El presente correo es para informar, el envio del los adjuntos de vencimientos de items.</p>
                        <p style="padding:.5rem">Fecha de Emision : '. date("d/m/Y h:i:s") .'</p>
                    </div>
                </div>';
		
		$mail->msgHTML(utf8_decode($messaje));

		$correos = listarCorreos($pdo);

		foreach ($correos as $correo) {
			$mail->AddAddress($correo['ccorreo'], $correo['cnameuser']);

			$ubigeos = verificarAdjuntos($pdo,$correo['iduser']);

			foreach ($ubigeos as $ubigeo) {
				
				$ruta 		= 'c:/xampp/htdocs/ibis/public/documentos/reportes/';
				$adjunto  	= $ubigeo['cubica']."-".$ubigeo['cabrevia'].'.xlsx';

				if ( file_exists($ruta.$adjunto))
					$mail->AddAttachment($ruta.$adjunto);
			}

			if(!$mail->Send()) {
				echo 'Error : ' . $mail->ErrorInfo;
			} else {
				echo 'Ok!!';
			}

			$mail->clearAddresses();
			$mail->clearAttachments();
		}
	}  
?>