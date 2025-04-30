<?php
    class TimmttoModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarMantenimientos($costos,$serie){

            $cc = $costos != -1 ? $costos : "%";
            $serie = $serie != "" ? $serie : "%";

            try {
                $docData = [];

                $sql = $this->db->connect()->prepare("SELECT
                                                        ibis.ti_mmttos.idreg,
                                                        ibis.ti_mmttos.fentrega AS entrega,
                                                        UPPER( ibis.cm_producto.cdesprod ) AS cdesprod,
                                                        ibis.tb_proyectos.ccodproy,
                                                        ibis.tb_proyectos.nidreg,
                                                        UPPER( ibis.ti_mmttos.cserie ) AS cserie,
                                                        ibis.ti_mmttos.nrodoc,
                                                        DATEDIFF(ibis.ti_mmttos.fmtto,NOW()) AS periodo,
                                                        DATE_FORMAT( ibis.ti_mmttos.fmtto, '%d/%m/%Y' ) AS fmtto1,
                                                        DATE_FORMAT( ibis.ti_mmttos.fentrega, '%d/%m/%Y' ) AS fentrega,
                                                        ibis.ti_mmttos.flgestado AS est1,
                                                        DATE_FORMAT( m2.fmtto, '%d/%m/%Y' ) AS fmtto2,m2.flgestado AS est2,
                                                        DATEDIFF(m2.fmtto,NOW()) AS periodo2,
                                                        DATE_FORMAT( m3.fmtto, '%d/%m/%Y' ) AS fmtto3,
                                                        m3.flgestado AS est3,
                                                        DATEDIFF(m3.fmtto,NOW()) AS periodo3,
                                                        DATE_FORMAT( m4.fmtto, '%d/%m/%Y' ) AS fmtto4,
                                                        m4.flgestado AS est4,
                                                        DATEDIFF(m4.fmtto,NOW()) AS periodo4,
                                                        ibis.tb_tiespec.cprocesador,
                                                        ibis.tb_tiespec.cram,
                                                        ibis.tb_tiespec.chdd,
                                                        ibis.tb_tiespec.totros
                                                    FROM
                                                        ibis.ti_mmttos
                                                        LEFT JOIN ibis.cm_producto ON ti_mmttos.idprod = cm_producto.id_cprod
                                                        LEFT JOIN ibis.tb_proyectos ON ibis.ti_mmttos.idcostos = ibis.tb_proyectos.nidreg
                                                        LEFT JOIN ( SELECT ti_mmttos.fmtto, ti_mmttos.flgestado, ti_mmttos.cserie FROM ti_mmttos WHERE ti_mmttos.nmtto = 2 AND ti_mmttos.flgactivo = 1) AS m2 ON m2.cserie = ti_mmttos.cserie
                                                        LEFT JOIN ( SELECT ti_mmttos.fmtto, ti_mmttos.flgestado, ti_mmttos.cserie FROM ti_mmttos WHERE ti_mmttos.nmtto = 3 AND ti_mmttos.flgactivo = 1) AS m3 ON m3.cserie = ti_mmttos.cserie
                                                        LEFT JOIN ( SELECT ti_mmttos.fmtto, ti_mmttos.flgestado, ti_mmttos.cserie FROM ti_mmttos WHERE ti_mmttos.nmtto = 4 AND ti_mmttos.flgactivo = 1) AS m4 ON m4.cserie = ti_mmttos.cserie
                                                        LEFT JOIN ibis.tb_tiespec ON ibis.tb_tiespec.cserie = ibis.ti_mmttos.cserie COLLATE utf8_unicode_ci
                                                    WHERE
                                                        ibis.ti_mmttos.flgactivo = 1 
                                                        AND ibis.tb_proyectos.nidreg LIKE :costos 
                                                        AND ibis.ti_mmttos.cserie LIKE :serie 
                                                    GROUP BY
                                                        ibis.ti_mmttos.nrodoc,
                                                        ibis.ti_mmttos.cserie");
                                                    
                $sql->execute(["costos" =>$cc,
                                "serie" =>$serie]);
                $rowCount = $sql->rowCount();
                
                if ($rowCount) {
                    $respuesta = true;
                    $i = 0;
                    
                    while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                        $docData[] = $row;
                    }
                }

                return array("datos"=>$docData,"usuarios"=>$this->usuariosAquarius());

                
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function registrarMmtto($parametros){
            try {
                $docData = [];
                $respuesta = false;
                $mensaje = "el equipo ya esta registrado";

                if ( !$this->existeSerie($parametros['serie_producto']) ) {
                    $this->grabarEspecificaciones($parametros);

                    $mensaje = "Equipo registrado";
                    $respuesta = true;
                }

                if ($parametros['tipo_mmtto'] === "1"){

                    $respuesta = "mantenimiento programado";
                    $sql = $this->db->connect()->prepare("UPDATE ti_mmttos 
                                                        SET ti_mmttos.frelmtto =:fecha,
                                                            ti_mmttos.flgestado =:estado,
                                                            ti_mmttos.iduser =:user,
                                                            ti_mmttos.cobserva =:observa 
                                                        WHERE ti_mmttos.idreg =:id
                                                            LIMIT 1");
                    $sql->execute(["fecha"      =>$parametros['fmmto'],
                                    "estado"    =>1,
                                    "user"      =>$parametros['user'],
                                    "observa"   =>$parametros['observa'],
                                    "id"        =>$parametros['lastMmtto']]);
                    if ( $sql->rowCount() > 0){
                        $respuesta = true;

                       /* $this->envio_correo_mantenimiento($parametros['correo'],
                                                        $parametros['tecnico'],
                                                        $parametros['correo_tecnico'],
                                                        $parametros['observa'],
                                                        $parametros['fmmto'],
                                                        $parametros['asignado']);*/
                    }
                }else {
                    $respuesta = "otro mantenimiento";
                    $sql = $this->db->connect()->prepare("INSERT ti_mmttos 
                                                            SET ti_mmttos.nrodoc =:documento,
                                                                ti_mmttos.idprod =:producto,
                                                                ti_mmttos.cserie  =:serie,
                                                                ti_mmttos.cobserva =:observa,
                                                                ti_mmttos.ntipo =:tipo,
                                                                ti_mmttos.idcostos =:costos,
                                                                ti_mmttos.iduser =:usuario,
                                                                ti_mmttos.frelmtto =:fecha,
                                                                ti_mmttos.flgestado =:estado");

                    $sql->execute(["documento"  =>$parametros['documento_usuario'],
                                    "producto"  =>$parametros['codigo_producto'],
                                    "serie"     =>$parametros['serie_producto'],
                                    "observa"   =>$parametros['observa'],
                                    "tipo"      =>$parametros['tipo_mmtto'],
                                    "costos"    =>$parametros['codigo_costos'],
                                    "usuario"   =>$parametros['user'],
                                    "fecha"     =>$parametros['fmmto'],
                                    "estado"    =>1]);

                    if ( $sql->rowCount() > 0){
                        $respuesta = true;

                        /*$this->envio_correo_mantenimiento($parametros['correo'],
                                            $parametros['tecnico'],
                                            $parametros['correo_tecnico'],
                                            $parametros['observa'],
                                            $parametros['fmmto'],
                                            $parametros['asignado']);*/
                    }
                }

                return array("respuesta"=>$respuesta);

            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function mantenimientosAnteriores($parametros){
            $docData = [];

            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                    DATE_FORMAT(ti_mmttos.frelmtto,'%d/%m/%Y') AS frelmtto,
                                                    UPPER( ti_mmttos.cobserva ) AS cobserva,
                                                    tb_user.cnombres AS tecnico 
                                                FROM
                                                    ti_mmttos
                                                    LEFT JOIN tb_user ON ti_mmttos.iduser = tb_user.iduser COLLATE utf8_unicode_ci 
                                                WHERE
                                                    ti_mmttos.nrodoc =:documento 
                                                    AND ti_mmttos.cserie =:serie
                                                AND ti_mmttos.flgestado = 1");

                $sql->execute(["documento"=>$parametros['documento'],
                                "serie"=>$parametros['serie']]);
                $rowCount = $sql->rowCount();
                
                if ($rowCount) {
                    $respuesta = true;
                    
                    while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                        $docData[] = $row;
                    }
                }

                $pendientes = $this->mmttoUltimoPendiente($parametros['serie'],$parametros['documento']);
                $detallesEquipos = $this->detallesEquipos($parametros['serie']);
        
                return array("mmttos" =>$docData,"lastmmttos" =>$pendientes, "especificaciones" => $detallesEquipos);

            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function detallesEquipos($serie){
            try {
                $docData = [];

                $sql = $this->db->connect()->prepare("SELECT tb_tiespec.cprocesador, 
                                                            tb_tiespec.cram, 
                                                            tb_tiespec.chdd, 
                                                            tb_tiespec.nestado
                                                        FROM tb_tiespec
                                                        WHERE  tb_tiespec.cserie = :serie");

                $sql->execute(["serie"=>$serie]);

                $rowCount = $sql->rowCount();
                
                if ($rowCount) {
                    $respuesta = true;
                    
                    while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                        $docData[] = $row;
                    }
                }

                return $docData;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function envio_correo_mantenimiento($correo,$tecnico,$correo_tecnico,$observa,$fecha,$asignado){
            try {
                require_once("public/PHPMailer/PHPMailerAutoload.php");
                $subject    = utf8_decode("Mantenimiento de equipo");

                $messaje= '<div style="width:100%;display: flex;flex-direction: column;justify-content: center;align-items: center;
                                    font-family: Futura, Arial, sans-serif;">
                            <div style="width: 45%;border: 1px solid #c2c2c2;background: #0078D4">
                                <h1 style="text-align: center;font-size:24px">Mantenimento de Equipo</h1>
                            </div>
                            <div style="width: 45%;
                                        border-left: 1px solid #c2c2c2;
                                        border-right: 1px solid #c2c2c2;
                                        border-bottom: 1px solid #c2c2c2;">
                                <p style="padding:.5rem"><strong style="font-style: italic;">Estimado(a):</strong></p>
                                <p style="padding:.5rem;line-height: 1rem;">El presente correo es para informar que se ha realizado el mantenimiento de su equipo</p>
                                <p style="padding:.5rem">Realizado el dia : '. $fecha.'</p>
                                <br><br>
                                <p style="padding:.5rem">Atte: '. $tecnico .'</p>
                            </div>
                        </div>';
                
                $origen = $correo_tecnico;
                $nombre_envio = $tecnico;

                $mail = new PHPMailer;
                $mail->isSMTP();
                $mail->SMTPDebug = 0;
                $mail->Debugoutput = 'html';
                $mail->Host = 'mail.sepcon.net';
                $mail->SMTPAuth = true;
                $mail->Username = 'sistema_ibis@sepcon.net';
                $mail->Password = $_SESSION['password'];
                $mail->Port = 465;
                $mail->SMTPSecure = "ssl";
                $mail->SMTPOptions = array(
                    'ssl' => array(
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => false
                    )
                );

                $mail->setFrom($origen,$nombre_envio);
                $mail->addAddress($correo,$asignado);
                $mail->addAddress($origen,$nombre_envio);

                $mail->Subject = $subject;
                $mail->msgHTML(utf8_decode($messaje));
   
                if (!$mail->send()) {
                    return array("mensaje"=>"Hubo un error, en el envio",
                                "clase"=>"mensaje_error");
                }
                        
                $mail->clearAddresses();

            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function enviarNotificacion($parametros){
            try {
                require_once("public/PHPMailer/PHPMailerAutoload.php");

                $respuesta = false;
                $fechaActual = date('Y-m-d');

                $subject    = utf8_decode("Notificación de Mantenimiento de Preventivo");

                $messaje= '<div style="width:80%;display: flex;flex-direction: column;justify-content: center;align-items: center;
                                    font-family: Futura, Arial, sans-serif;margin: 0 auto">
                                <div style="width: 70%;border: 1px solid #c2c2c2;background: #0078D4; padding:15px">
                                    <h3 style="text-align: center;font-size:12px">MANTENIMIENTO PREVENTIVO DE EQUIPOS INFORMÁTICOS</h3>
                                </div>
                                <div style="width: 70%;
                                            border-left: 1px solid #c2c2c2;
                                            border-right: 1px solid #c2c2c2;
                                            border-bottom: 1px solid #c2c2c2;
                                            padding:15px">
                                    <p style="padding:5px"><strong style="font-style: italic;">Estimado(a) : </strong>'.$parametros['usuario'].'</p>
                                    <p style="padding:5px"><strong style="font-style: italic;">Fecha de Mantenimiento programado : </strong>'.$parametros['fecha'].'</p>

                                    <p style="padding:5px;line-height: 1rem;">Acorde a la programación semestral de mantenimientos preventivos, su equipo asignado debe ser puesto a disposición del área de T&I para su respectiva atención. </p>
                                    <p style="padding:5px">Recordar que es responsabilidad del usuario conservar en buen estado las herramientas, el equipo de oficina, útiles y demás bienes de la organización- En caso de que la pérdida o deterioro de tales bienes hubiera sido causada por negligencia debidamente comprobada de parte del trabajador, este deberá reponerlos, sin perjuicio de las sanciones disciplinarias que puedan corresponder. (PSPC-900-X-RG-002 Reglamento Interno de Trabajo).</p>
                                    <p>Se agradece su colaboración</p>
                                    <p>Saludos Cordiales.</p>
                                </div>
                            </div>';
                

                $mail = new PHPMailer;
                $mail->isSMTP();
                $mail->SMTPDebug = 0;
                $mail->Debugoutput = 'html';
                $mail->Host = 'mail.sepcon.net';
                $mail->SMTPAuth = true;
                $mail->Username = 'sistema_ibis@sepcon.net';
                $mail->Password = $_SESSION['password'];
                $mail->Port = 465;
                $mail->SMTPSecure = "ssl";
                $mail->SMTPOptions = array(
                    'ssl' => array(
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => false
                    )
                );

                $mail->setFrom("ti@sepcon.net",utf8_encode("Dpto. Tecnologia Informatica"));
                $mail->addAddress($parametros['correo'],$parametros['usuario']);
                $mail->addAddress($parametros['correo_tecnico'],$parametros['tecnico']);
                $mail->addAddress('fichas@sepcon.net',utf8_decode('Correo de Notificación de Mantenimiento'));

                $mail->Subject = $subject;
                $mail->msgHTML(utf8_decode($messaje));
   
                if (!$mail->send()) {
                    return array("mensaje"=>"Hubo un error, en el envio",
                                "clase"=>"mensaje_error");
                }else{
                    $sql = $this->db->connect()->prepare("UPDATE ti_mmttos
                                                            SET ti_mmttos.fnotify =:fechaActual
                                                            WHERE ti_mmttos.idreg =:id");

                    $sql->execute(["fechaActual"=>$fechaActual,"id"=>$parametros['id']]);

                    $respuesta = true;
                }
                        
                $mail->clearAddresses();

                return array("respuesta"=>$respuesta);

            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function crearExcel($detalles){
            require_once('public/PHPExcel/PHPExcel.php');
            try {
                $objPHPExcel = new PHPExcel();
                
                $objPHPExcel->getProperties()
                    ->setCreator("Sical")
                    ->setLastModifiedBy("Sical")
                    ->setTitle("Reporte MMTTO")
                    ->setSubject("Template excel")
                    ->setDescription("Cargo Plan")
                    ->setKeywords("Template excel");

                $cuerpo = array(
                    'font'  => array(
                    'bold'  => false,
                    'size'  => 7,
                ));

                $objWorkSheet = $objPHPExcel->createSheet(1);

                $objPHPExcel->setActiveSheetIndex(0);
                $objPHPExcel->getActiveSheet()->setTitle("Reporte MMTTO");

                $objWorkSheet = $objPHPExcel->createSheet(1);

                $objPHPExcel->getActiveSheet()
                            ->getStyle('A2:N2')
                            ->getFill()
                            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB('BFCDDB');

                $objPHPExcel->getActiveSheet()->mergeCells('A1:N1');
                $objPHPExcel->getActiveSheet()->setCellValue('A1','REPORTE DE MANTENIMIENTO');

                $objPHPExcel->getActiveSheet()->getStyle('A1:AN2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('A1:AN2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(60);

                $objPHPExcel->getActiveSheet()->getColumnDimension("B")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("C")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("D")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("E")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("F")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("G")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("H")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("I")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("J")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("K")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("L")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("M")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("N")->setAutoSize(true);


                $objPHPExcel->getActiveSheet()->getStyle('A1:AN2')->getAlignment()->setWrapText(true);

                $objPHPExcel->getActiveSheet()->setCellValue('A2','Items'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('B2','Descripcion'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('C2','Usuario'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('D2','Serie'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('E2','Fecha Entrega'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('F2','Centro Costos'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('G2','1er MMTTO'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('H2','Estado'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('I2','2do MMTTO'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('J2','Estado'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('K2','3er MMTTO'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('L2','Estado'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('M2','4to MMTTO'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('N2','Estado'); // esto cambia

                $datos = json_decode($detalles);
                $fila = 3;
                $color_estado1 = "#FFD700";
                $color2 = "#FFD700";
                $color3 = "#FFD700";
                $color4 = "#FFD700";

                forEach($datos AS $dato){
                    
                    /*if ($dato->estado1 == 'Pendiente'){
                        $color_estado1 = "#FFD700";
                    }else if($dato->estado1 == 'Realizado'){
                        $color_estado1 = "#36DC2E";
                    }else if($dato->estado1 == 'Vencido'){
                        $color_estado1 = "#DC362E";
                    }

                    $color1 = array(
                        'fill' => array(
                            'type' => PHPExcel_Style_Fill::FILL_SOLID,
                            'startcolor' => array(
                                'argb' => $color_estado1,
                            ),
                            'endcolor' => array(
                                'argb' => $color_estado1,
                            ),
                        ),
                    );*/


                    $objPHPExcel->getActiveSheet()->setCellValue('A'.$fila,$dato->item);
                    $objPHPExcel->getActiveSheet()->setCellValue('B'.$fila,$dato->descripcion);
                    $objPHPExcel->getActiveSheet()->setCellValue('C'.$fila,$dato->usuario);
                    $objPHPExcel->getActiveSheet()->setCellValue('D'.$fila,$dato->serie);
                    $objPHPExcel->getActiveSheet()->setCellValue('E'.$fila,$dato->entrega);
                    $objPHPExcel->getActiveSheet()->setCellValue('F'.$fila,$dato->costos);
                    $objPHPExcel->getActiveSheet()->setCellValue('G'.$fila,$dato->mmtto1);
                    $objPHPExcel->getActiveSheet()->setCellValue('H'.$fila,$dato->estado1);
                    //$objPHPExcel->getActiveSheet()->getStyle('H'.$fila)->applyFromArray($color1);
                    $objPHPExcel->getActiveSheet()->setCellValue('I'.$fila,$dato->mmtto2);
                    $objPHPExcel->getActiveSheet()->setCellValue('J'.$fila,$dato->estado2);
                    //$objPHPExcel->getActiveSheet()->getStyle('J'.$fila)->applyFromArray($color2);
                    $objPHPExcel->getActiveSheet()->setCellValue('K'.$fila,$dato->mmtto3);
                    $objPHPExcel->getActiveSheet()->setCellValue('L'.$fila,$dato->estado3);
                    //$objPHPExcel->getActiveSheet()->getStyle('L'.$fila)->applyFromArray($color3);
                    $objPHPExcel->getActiveSheet()->setCellValue('M'.$fila,$dato->mmtto3);
                    $objPHPExcel->getActiveSheet()->setCellValue('N'.$fila,$dato->estado4);
                    //$objPHPExcel->getActiveSheet()->getStyle('N'.$fila)->applyFromArray($color4);

                    $fila++;
                }

                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');
                $objWriter->save('public/documentos/reportes/repommtto.xlsx');

                return array("documento"=>'public/documentos/reportes/repommtto.xlsx');

                exit();

                
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function existeSerie($serie){
            try {
                $respuesta = false;

                $sql = $this->db->connect()->prepare("SELECT 
                                                        tb_tiespec.idreg 
                                                    FROM  
                                                        tb_tiespec 
                                                    WHERE 
                                                        tb_tiespec.cserie =:serie");
                $sql->execute(["serie"=>$serie]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0){
                    $respuesta = true;
                }

                return $respuesta;

            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function grabarEspecificaciones($parametros){
            try {
                $respuesta = false;

                $sql = $this->db->connect()->prepare("INSERT INTO 
                                                        tb_tiespec 
                                                      SET 
                                                        tb_tiespec.idkardex =:kardex,
                                                        tb_tiespec.cserie =:serie,
                                                        tb_tiespec.cprocesador =:procesador,
                                                        tb_tiespec.cram =:ram,
                                                        tb_tiespec.chDd =:hdd,
                                                        tb_tiespec.totros =:otros,
                                                        tb_tiespec.nestado =:estado");

                $sql->execute(["kardex" =>$parametros['id'],
                                "serie"=>$parametros['serie_producto'],
                                "procesador"=>$parametros['procesador'],
                                "ram"=>$parametros['ram'],
                                "hdd"=>$parametros['hdd'],
                                "otros"=>$parametros['otros'],
                                "estado"=>$parametros['estado']]);

                if( $sql->rowCount() > 0){
                    $respuesta = true;
                }

                return $respuesta;

            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function actualizarFechas($parametros){
            try {
                $fecha = $parametros['fecha'];
                $serie = $parametros['serie'];
                $documento = $parametros['documento'];
                $nuevas_fechas = [];
                $fmmtto = 1;

                $lapso = array("+6 month","+12 month","+18 month","+24 month");

                for ($i = 0; $i < 4 ; $i++) {
                    $nuevas_fechas[$i] = $this->calcularProximos($fecha,$lapso[$i]);

                    $sql = $this->db->connect()->prepare("UPDATE ti_mmttos 
                                                         SET ti_mmttos.fmtto =:fecha,
                                                             ti_mmttos.fentrega =:entrega
                                                         WHERE ti_mmttos.nrodoc =:documento 
                                                                AND ti_mmttos.cserie =:serie
                                                                AND ti_mmttos.nmtto =:mmtto
                                                        LIMIT 1");
                    
                    $sql->execute(["fecha"=>$nuevas_fechas[$i],
                                    "entrega"=>$fecha,
                                    "documento"=>$documento,
                                    "serie"=>$serie,
                                    "mmtto"=>$fmmtto++]);

                } 

                return array("nuevas_fechas"=>$nuevas_fechas);
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function calcularProximos($fecha,$meses){
            $nuevafecha = date("Y-m-d",strtotime($fecha.$meses));
	 
		    return $nuevafecha;
        }

        private function mmttoUltimoPendiente($serie,$documento){
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                        ti_mmttos.idreg,
                                                        ti_mmttos.cserie,
                                                        ti_mmttos.nrodoc,
                                                        DATE_FORMAT(MIN(ti_mmttos.fmtto),'%d/%m/%Y') as fecha_proxima 
                                                    FROM
                                                        ti_mmttos 
                                                    WHERE
                                                        ti_mmttos.cserie = :serie 
                                                        AND ti_mmttos.nrodoc = :documento 
                                                        AND ti_mmttos.ntipo = 1
                                                        AND ti_mmttos.flgactivo = 1
                                                        AND ti_mmttos.flgestado = 0");
                
                $sql->execute(["serie"=>$serie,"documento"=>$documento]);
                $return = $sql->fetchAll();

                return array("serie"=>$return[0]['cserie'],
                            "id"=>$return[0]['idreg'],
                            "fecha_proxima"=>$return[0]['fecha_proxima']);

            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }
    }
?>