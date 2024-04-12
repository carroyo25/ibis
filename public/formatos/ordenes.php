<?php
    require_once "public/fpdf/mc_table.inc.php";

    class PDF extends PDF_MC_Table{

        public function __construct($titulo,$condicion,$fecha,$moneda,$plazo,
                                    $lugar,$cotizacion,$fentrega,$pago,$importe,
                                    $info,$detalle,$usuario,$razon_social,
                                    $ruc,$direccion,$telefono,$correo,$retencion,
                                    $contacto,$tel_contacto,$cor_contacto,$direccion_almacen,$referencia,
                                    $procura,$finanzas,$operaciones,$tipo)
        {
            parent::__construct();
            $this->titulo           = $titulo;
            $this->condicion        = $condicion;
            $this->fecha            = $fecha;
            $this->moneda           = $moneda;
            $this->plazo            = $plazo;
            $this->lugar            = $lugar;
            $this->cotizacion       = $cotizacion;
            $this->fentrega         = $fentrega;
            $this->pago             = $pago;
            $this->importe          = $importe;
            $this->info             = $info;
            $this->detalle          = $detalle;
            $this->razon_social     = $razon_social;
            $this->ruc              = $ruc;
            $this->direccion        = $direccion;
            $this->telefono         = $telefono;
            $this->correo           = $correo;
            $this->retencion        = $retencion;
            $this->contacto         = $contacto;
            $this->tel_contacto     = $tel_contacto;
            $this->cor_contacto     = $cor_contacto;
            $this->usuario          = $usuario;
            $this->direccion_almacen= $direccion_almacen;
            $this->referencia       = $referencia;
            $this->procura          = $procura;
            $this->finanzas         = $finanzas;
            $this->operaciones      = $operaciones;
            $this->tipo             = $tipo;
        }

        function header(){
            $this->Rect(10,10,30,20); //marco de la imagen
        	$this->Rect(40,10,130,20); //marco del titulo
        	$this->Rect(10,10,190,20); //marco general

            if ($this->condicion == 0) {
                $condicion = "VISTA PREVIA - NO APTO PARA PAGO";
            }else if($this->condicion == 1){
                $condicion = "EMITIDO";
            }else if($this->condicion == 2){
                $condicion = "APROBADO";
            }else if($this->condicion == 3){
                $condicion = "APROBADO";
            }else if($this->condicion == 4){
                $condicion = "APROBADO";
            }

            $fecha = explode("-",$this->fecha);

        	$this->SetFillColor(229, 229, 229);
        	$this->Rect(70,24,70,5,"F"); //fondo de mensaje
        	$this->Image('public/img/logo.png',12,12,25);
	        $this->SetFont('Arial','B',11);
			$this->SetTextColor(0,0,0);
	 		$this->SetFillColor(229, 229, 229);
	        $this->Cell(190,7,utf8_decode($this->titulo),0,1,'C'); //envia de parametro
	        $this->SetFont('Arial','B',7);
            $this->Cell(190,6,utf8_decode($this->info),0,1,'C'); //envia proyecto
	        $this->Cell(190,7,$condicion,0,0,'C');
	        $this->SetXY(170,11);
	        $this->SetFont('Arial','',6);
	        $this->MultiCell(30,5,utf8_decode('PSPC-410-X-PR-001-FR-002 Revisión: 01 Emisión: 12/04/2024 '),0,'L',false);

            $this->SetXY(170,32);
	        $this->Cell(10,4,utf8_decode("Día"),1,0,"C");
	        $this->Cell(10,4,"Mes",1,0,"C");
	        $this->Cell(10,4,utf8_decode("Año"),1,1,"C");
	        $this->SetXY(170,36);
	        $this->Cell(10,8,$fecha[2],1,0,"C"); //envia de parametro
	        $this->Cell(10,8,$fecha[1],1,0,"C"); //envia de parametro
	        $this->Cell(10,8,$fecha[0],1,1,"C"); //envia de parametro
	        
	        $this->SetXY(10,32);
	        $this->Cell(30,4,"Facturar a nombre de : ","TL",0);
	        $this->Cell(80,4,"SERVICIOS PETROLEROS Y CONSTRUCCIONES SEPCON S.A.C","T",0);
            $this->Cell(20,4,utf8_decode("RUC"),"T",0);
	        $this->Cell(30,4,utf8_decode("20504898173"),"TR",1);

	        $this->Cell(30,4,utf8_decode("Dirección Oficina Principal :"),"L",0);
	        $this->Cell(80,4,utf8_decode("AV. SAN BORJA NORTE N° 445 - SAN BORJA - LIMA - PERU"),0,0);
	        $this->Cell(20,4,utf8_decode("MONEDA"),0,0);
	        $this->Cell(30,4,utf8_decode($this->moneda),"R",1);
            
            $this->Cell(30,4,"Lugar de entrega de bienes :","LB",0);
	        $this->Cell(130,4,utf8_decode($this->lugar . " ". $this->direccion_almacen),"BR",1); //envia de parametro

    		$this->Ln(1);

            $this->Cell(113,4,"1. DATOS DEL PROVEEDOR","1",0);
            $this->Cell(77,4,"2. CONDICIONES GENERALES","TRB",1);
            
            $this->Cell(13,3,utf8_decode("Señor(es) :"),"L",0);
            $this->Cell(100,3,utf8_decode($this->razon_social),"R",0); //envia de parametro
            $this->Cell(20,3,utf8_decode("Número RUC:"),0); 
            $this->Cell(57,3,$this->ruc,"R",1); //envia de parametro
           
            $this->Cell(13,3,utf8_decode("Dirección :"),"L",0);
            $this->MultiCell(95,3,utf8_decode($this->direccion),0); //envia de parametro
            $this->SetXY(123,52);
            
            $this->Cell(20,3,utf8_decode("Forma de Pago: "),"L",0);
            $this->Cell(57,3,$this->pago,"R",1); //envia de parametro

            $this->Cell(113,3,"","L",0);
            $this->Cell(20,3,utf8_decode("Referencia Pago: "),"L",0);
            $this->SetFont('Arial','',4.5);
            $this->MultiCell(57,2,strtoupper(utf8_decode($this->referencia)),0); //ver de donde sale
            
            $this->Line(200,50,200,80); //Lineas de caja
            $this->Line(10,50,10,80); //Lineas de caja
            $this->Line(123,50,123,80); //Lineas de caja*/


            $this->SetY(61);
            $this->SetFont('Arial','',6);
            $this->Cell(13,3,utf8_decode("Atención :"),"L",0);
            $this->Cell(40,3,utf8_decode($this->contacto),0); //envia de parametro
            $this->Cell(13,3,utf8_decode("Teléfono :"),0); 
            $this->Cell(47,3,utf8_decode($this->telefono),0); //envia de parametro
            $this->SetFont('Arial','',6);
            $this->Cell(15,3,utf8_decode("N°.Cotización :"),"L",0);
            $this->Cell(20,3,utf8_decode($this->cotizacion),0); //envia de parametro
            $this->Cell(25,3,utf8_decode("N°. Contrato :"),0);
            $this->Cell(17,3,utf8_decode(""),"R",1); //envia de parametro

            $this->Cell(13,3,utf8_decode("E-mail :"),"BL",0);
            $this->Cell(100,3,utf8_decode($this->correo),"B",0); //envia de parametro
            $this->Cell(15,3,utf8_decode("Fecha Entrega :"),"BL",0);
            $this->Cell(15,3,date("d/m/Y", strtotime($this->fentrega)),"B",0); //envia de parametro
            $this->Cell(20,3,utf8_decode("Plazo de entrega :"),"B",0);
            $this->Cell(27,3,utf8_decode($this->plazo),"BR",1); //envia de parametro
            
            if ($this->retencion == 2) {
                $this->SetFillColor(0, 0, 128);
                $this->SetTextColor(255,255,255);
                $this->MultiCell(15,3,utf8_decode('AGENTE DE RETENCION'),1,'L',true); //envia de parametro
            }
            
            $this->SetTextColor(0,0,0);
            $this->SetXY(10,67);
            $this->Cell(16,3,utf8_decode(""),"L",0);
            $this->Cell(10,3,utf8_decode("Contacto :"),"0",0);
            $this->Cell(40,3,utf8_decode($this->contacto),0,0); //envia de parametro
            $this->Cell(20,3,utf8_decode("Teléfono :"),0,0);
            $this->Cell(27,3,utf8_decode($this->tel_contacto),0,0); //envia de parametro
            $this->Cell(77,3,utf8_decode("Observ :"),"LR",0);

            $this->SetFillColor(255, 255, 0);
            $this->SetFont('Arial','',5);
            $this->SetXY(133,67.5);
            $this ->MultiCell(64,2,utf8_decode($this->detalle), 0, 'L', 1);
            $this->SetY(69);
            $this->SetFont('Arial','',6);
            $this->Cell(16,3,utf8_decode(""),"BL",0);
            $this->Cell(13,3,utf8_decode("E-mail :"),"B",0);
            $this->Cell(84,3,utf8_decode($this->cor_contacto),"B",0); //envia de parametro
            $this->Cell(77,3,utf8_decode(""),"LBR",1);

        	$this->SetFillColor(229, 229, 229);

	        // Salto de línea
    		$this->Ln(3);
            $this->Rect(10,73,190,7,"F"); //fondo de mensaje
    		$this->SetWidths(array(10,15,15,10,93,17,15,15));
    		$this->SetAligns(array("C","C","C","C","C","C","C","C"));
    		$this->Row(array('Item',utf8_decode('Código'),'Cant.','Und.',utf8_decode('Descripción'),'Nro.Pedido','Precio Unitario','Valor Total'));
                    
        }

        function footer(){
            $this->Ln(3);
		    $this->SetFillColor(229, 229, 229);
            $this->SetY(-90);
            $this->SetFont('Arial',"","7");
		    $this->cell(30,4,"ELABORADO POR",1,0,"C",true);
            $this->cell(30,4,"IMPRESO POR",1,1,"C",true);
            $this->SetFont('Arial',"","5");
            $this->cell(30,4,utf8_decode($this->usuario),1,0,"C"); //envia de parametro
            $this->cell(30,4,"SYSTEM",1,1,"C");
            $this->SetFont('Arial',"","7");
            $this->cell(30,4,"EMITIDO",1,0,"C",true);
            $this->cell(30,4,"FOLIO",1,1,"C",true);
            $this->cell(30,4,date("d-m-Y h:i:s"),1,0,"C");
            $this->Cell(30,4,utf8_decode('Página ').$this->PageNo()." de ".'/{nb}',1,1,"C");

            $this->setXY(70,207);

            $estado1 = chr(45);
            $estado2 = chr(45);
            $estado3 = chr(45);

            if ($this->condicion == 0){
                $this->SetTextColor(170,218,245);
                $estado1 = chr(45);
                $estado2 = chr(45);
                $estado3 = chr(45); //envia de parametro
            }else if(($this->condicion == 1)){
                $this->SetTextColor(29,162,97);
                $estado1 = chr(45);
                $estado2 = chr(45);
                $estado3 = chr(45);
            }else if(($this->condicion == 2)){
                $this->SetTextColor(29,162,97);
                $estado1 = $this->procura == 1 ? chr(51) : chr(45);
                $estado2 = $this->finanzas == 1 ? chr(51) : chr(45);
                $estado3 = $this->operaciones == 1 ? chr(51) : chr(45);
            }else if(($this->condicion == 3)){
                $this->SetTextColor(29,162,97);
                $estado1 = chr(51);
                $estado2 = chr(51);
                $estado3 = chr(51); //envia de parametro
            }else if(($this->condicion == 4)){
                $this->SetTextColor(29,162,97);
                $estado1 = $this->procura == 1 ? chr(51) : chr(45);
                $estado2 = $this->finanzas == 1 ? chr(51) : chr(45);
                $estado3 = $this->operaciones == 1 ? chr(51) : chr(45);
            }

            $this->SetFont('ZapfDingbats','',24);
            $this->cell(43,16,$estado3,1,0,"C"); //envia de parametro
            $this->cell(43,16,$estado2,1,0,"C"); //envia de parametro
            $this->cell(43,16,$estado1,1,1,"C"); //envia de parametro

            $this->SetFont('Arial',"","6.5");
            $this->SetTextColor(0,0,0);
            $this->cell(60,4,"20210003024022021122904",1,0,"C"); //envia de parametro
            $this->cell(43,4,"OPERACIONES / G.GENERAL",1,0,"C");
            $this->cell(43,4,"FINANZAS / ADMINISTRACION",1,0,"C");
            $this->cell(43,4,"JEFE DE SUMINISTROS",1,1,"C");

            $this->Ln(2);

            if ( $this->tipo == "37" )
            
            $this->MultiCell(130,3.18,utf8_decode('NOTA INFORMATIVA :
            
1. Se adjunta a la presente Orden los términos y condiciones de compra.
2. SEPCON se reserva los derechos de recepción y/o penalización sin conocimiento previo del proveedor por material                recibido fuera de tiempo.
3. Al hacer entrega de los materiales, el proveedor deberá adjuntar cuando corresponda: Certificado de calidad, Hojas SDS,        manuales de operación y mantenimiento, certificado de calibración, instrucciones de conservación, etc.
4. Una vez realizada la entrega de los materiales, el proveedor debe solicitar al almacén "La Nota de Ingreso" debidamente        sellada. 
5. Para la presentación de la factura:Sirva adjuntar la Nota de Ingreso, Guia de Remision y Factura, referente a una sola             Orden de Compra. Se debe consignar en la Guía de remisión el número de Orden de Compra.'),1);
            else 

            $this->MultiCell(130,3,utf8_decode('NOTA INFORMATIVA :
1. Se adjunta a la presente Orden los términos y condiciones de Compra y la Cartilla de Lineamientos de SSMMA para                Contratistas PSPC-100-X-IN-005.
2. SEPCON se reserva los derechos de penalización sin conocimiento previo del proveedor por un servicio que no cumpla            con las condiciones pactadas.
3. Una vez realizado el servicio, el proveedor debe solicitar la aprobación de su valorización.
4. Para presentación de factura: Sirva adjuntar la Valorización Aprobada y su respectiva Orden de Servicio. Para el caso de        servicios por suscripción o de naturaleza electrónica/digital se podrá reemplazar la Valorización Aprobada con una                   comunicación oficial escrita dando conformidad por el servicio.   
5. Para aquellos servicios que intervengan con el objeto principal de la organización , se debe completar el Formulario de            Evaluación de Proveedores de Servicio SST PSPC-410-X-PR-002-FR-001, siendo condición de pago la debida                        presentación de dicho formulario'),1);
            


            $this->setXY(141,229);
            $this->MultiCell(58,3.2,utf8_decode(
                '***IMPORTANTE***

Es requisito indispensable que el proveedor envíe sus facturas debidamente sustentadas en un solo archivo PDF, al correo: recepcion_factelec@sepcon.net, para su validación y registro, así mismo adjuntar los archivos XML y CDR de ser el caso, en el horario establecido:

Martes y Jueves (De 08:30am a 12:40pm y 02:00pm hasta 03:30pm'),1);
            
        $this->Ln(2);
        $this->MultiCell(189,3.3,utf8_decode('En SEPCON contribuimos con la protección, cuidado y conservación del Medio Ambiente y mitigación del cambio climático, por ello les alcanzamos algunas eco recomendaciones: 
(i) Promueva el uso de energías renovables y use con responsabilidad y de forma racional los recursos no renovables (agua,papel,electricidad,etc). 
(ii) Reduzca el consumo de materiales desechables; busque, evalúe y proponga opciones "eco-amigables" y reutilice lo más posible. 
(iii) Si transporta materiales peligrosos, asegurarse de contar con permisos, plan de contingencia, hojas SDS, recursos y personal capacitado.'),1);

        $this->Ln(1);
        $this->Cell(189,6,utf8_decode("** SOMOS AGENTE DE RETENCIÓN DEL IGV DE ACUERDO A R.S.219-2006 **"),0,0,"C");
        }
    }
?>