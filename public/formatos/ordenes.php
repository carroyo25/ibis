<?php
    require_once "public/fpdf/mc_table.inc.php";

    class PDF extends PDF_MC_Table{

        public function __construct($titulo,$condicion,$fecha,$moneda,$plazo,
                                    $lugar,$cotizacion,$fentrega,$pago,$importe,
                                    $info,$detalle,$usuario,$razon_social,
                                    $ruc,$direccion,$telefono,$correo,$retencion,
                                    $contacto,$tel_contacto,$cor_contacto)
        {
            parent::__construct();
            $this->titulo       = $titulo;
            $this->condicion    = $condicion;
            $this->fecha        = $fecha;
            $this->moneda       = $moneda;
            $this->plazo        = $plazo;
            $this->lugar        = $lugar;
            $this->cotizacion   = $cotizacion;
            $this->fentrega     = $fentrega;
            $this->pago         = $pago;
            $this->importe      = $importe;
            $this->info         = $info;
            $this->detalle      = $detalle;
            $this->razon_social = $razon_social;
            $this->ruc          = $ruc;
            $this->direccion    = $direccion;
            $this->telefono     = $telefono;
            $this->correo       = $correo;
            $this->retencion    = $retencion;
            $this->contacto     = $contacto;
            $this->tel_contacto = $tel_contacto;
            $this->cor_contacto = $cor_contacto;
            $this->usuario      = $usuario;
        }

        function header(){
            $this->Rect(10,10,30,20); //marco de la imagen
        	$this->Rect(40,10,130,20); //marco del titulo
        	$this->Rect(10,10,190,20); //marco general

            if ($this->condicion == 0) {
                $condicion = "VISTA PREVIA";
            }else {
                $condicion = "EMITIDO";
            }

            $fecha = explode("-",$this->fecha);

        	$this->SetFillColor(229, 229, 229);
        	$this->Rect(70,24,70,5,"F"); //fondo de mensaje
        	$this->Image('public/img/logo.png',12,12,25);
	        $this->SetFont('Arial','B',12);
			$this->SetTextColor(0,0,0);
	 		$this->SetFillColor(229, 229, 229);
	        $this->Cell(190,7,utf8_decode($this->titulo),0,1,'C'); //envia de parametro
	        $this->SetFont('Arial','B',8);
            $this->Cell(190,6,utf8_decode($this->info),0,1,'C'); //envia proyecto
	        $this->Cell(190,7,$condicion,0,0,'C');
	        $this->SetXY(170,11);
	        $this->SetFont('Arial','',6);
	        $this->MultiCell(30,5,utf8_decode('PSPC-410-X-PR-001-FR-002 Revisi??n: 0 Emisi??n: 06/05/2019 '),0,'L',false);

            $this->SetXY(170,32);
	        $this->Cell(10,4,utf8_decode("D??a"),1,0,"C");
	        $this->Cell(10,4,"Mes",1,0,"C");
	        $this->Cell(10,4,utf8_decode("A??o"),1,1,"C");
	        $this->SetXY(170,36);
	        $this->Cell(10,8,$fecha[0],1,0,"C"); //envia de parametro
	        $this->Cell(10,8,$fecha[1],1,0,"C"); //envia de parametro
	        $this->Cell(10,8,$fecha[2],1,1,"C"); //envia de parametro
	        
	        $this->SetXY(10,32);
	        $this->Cell(30,4,"Facturar a nombre de : ","TL",0);
	        $this->Cell(80,4,"SERVICIOS PETROLEROS Y CONSTRUCCIONES SEPCON S.A.C","T",0);
            $this->Cell(20,4,utf8_decode("RUC"),"T",0);
	        $this->Cell(30,4,utf8_decode("20504898173"),"TR",1);

	        $this->Cell(30,4,utf8_decode("Direcci??n Oficina Principal :"),"L",0);
	        $this->Cell(80,4,utf8_decode("AV. SAN BORJA NORTE N?? 445 - SAN BORJA - LIMA - PERU"),0,0);
	        $this->Cell(20,4,utf8_decode("MONEDA"),0,0);
	        $this->Cell(30,4,utf8_decode($this->moneda),"R",1);
            
            $this->Cell(30,4,"Lugar de entrega de bienes :","LB",0);
	        $this->Cell(130,4,utf8_decode($this->lugar),"BR",1); //envia de parametro

    		$this->Ln(1);

            $this->Cell(113,4,"1. DATOS DEL PROVEEDOR","1",0);
            $this->Cell(77,4,"2. CONDICIONES GENERALES","TRB",1);
            
            $this->Cell(13,3,utf8_decode("Se??or(es) :"),"L",0);
            $this->Cell(100,3,utf8_decode($this->razon_social),"R",0); //envia de parametro
            $this->Cell(20,3,utf8_decode("N??mero RUC:"),0); 
            $this->Cell(57,3,$this->ruc,"R",1); //envia de parametro

            $this->Cell(13,3,utf8_decode("Direcci??n :"),"L",0);
            $this->Cell(100,3,utf8_decode($this->direccion),0,0); //envia de parametro
            $this->Cell(20,3,utf8_decode("Forma de Pago: "),"L",0);
            $this->Cell(57,3,$this->pago,"R",1); //envia de parametro

            $this->Cell(113,3,"","L",0);
            $this->Cell(20,3,utf8_decode("Referencia Pago: "),"L",0);
            $this->Cell(57,3,"","R",1); //ver de donde sale

            $this->Cell(13,3,utf8_decode("Atenci??n :"),"L",0);
            $this->Cell(40,3,utf8_decode($this->contacto),0); //envia de parametro
            $this->Cell(13,3,utf8_decode("Tel??fono :"),0); 
            $this->Cell(47,3,utf8_decode($this->telefono),0); //envia de parametro
            $this->Cell(15,3,utf8_decode("N??.Cotizaci??n :"),"L",0);
            $this->Cell(15,3,utf8_decode($this->cotizacion),0); //envia de parametro
            $this->Cell(30,3,utf8_decode("N??. Contrato :"),0);
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
            $this->SetXY(10,64);
            $this->Cell(16,3,utf8_decode(""),"L",0);
            $this->Cell(10,3,utf8_decode("Contacto :"),"0",0);
            $this->Cell(40,3,utf8_decode($this->contacto),0,0); //envia de parametro
            $this->Cell(20,3,utf8_decode("Tel??fono :"),0,0);
            $this->Cell(27,3,utf8_decode($this->tel_contacto),0,0); //envia de parametro
            $this->Cell(13,3,utf8_decode("Observ :"),"L",0);

            $this->SetFillColor(255, 255, 0);
            $this->Cell(64,3,utf8_decode($this->detalle),"R",1,"L",true); //envia de parametro
            $this->Cell(16,3,utf8_decode(""),"BL",0);
            $this->Cell(13,3,utf8_decode("E-mail :"),"B",0);
            $this->Cell(84,3,utf8_decode($this->cor_contacto),"B",0); //envia de parametro
            $this->Cell(77,3,utf8_decode(""),"LBR",1);

        	$this->SetFillColor(229, 229, 229);

	        // Salto de l??nea
    		$this->Ln(3);
            $this->Rect(10,73,190,7,"F"); //fondo de mensaje
    		$this->SetWidths(array(10,15,15,10,95,17,13,15));
    		$this->SetAligns(array("C","C","C","C","C","C","C","C"));
    		$this->Row(array('Item',utf8_decode('C??digo'),'Cant.','Und.',utf8_decode('Descripci??n'),'Nro.Pedido','Precio Unitario','Valor Total'));
                    
        }

        function footer(){
            $this->Ln(3);
		    $this->SetFillColor(229, 229, 229);
            $this->SetY(-90);
            $this->SetFont('Arial',"","7");
		    $this->cell(30,4,"ELABORADO POR",1,0,"C",true);
            $this->cell(30,4,"IMPRESO POR",1,1,"C",true);
            $this->cell(30,4,utf8_decode($this->usuario),1,0,"C"); //envia de parametro
            $this->cell(30,4,"SYSTEM",1,1,"C");
            $this->cell(30,4,"EMITIDO",1,0,"C",true);
            $this->cell(30,4,"FOLIO",1,1,"C",true);
            $this->cell(30,4,"17/01/2021 01:39",1,0,"C");
            $this->Cell(30,4,utf8_decode('P??gina ').$this->PageNo()." de ".'/{nb}',1,1,"C");

            $this->setXY(70,207);

            if ($this->condicion == 0){
                $this->SetTextColor(170,218,245);
                $estado = chr(45); //envia de parametro
            }else if(($this->condicion == 1)){
                $this->SetTextColor(29,162,97);
                $estado = chr(45);
            }else if(($this->condicion == 2)){
                $this->SetTextColor(29,162,97);
                $estado = chr(51);
            }

            $this->SetFont('ZapfDingbats','',24);
            $this->cell(43,16,$estado,1,0,"C"); //envia de parametro
            $this->cell(43,16,$estado,1,0,"C"); //envia de parametro
            $this->cell(43,16,$estado,1,1,"C"); //envia de parametro

            $this->SetFont('Arial',"","7");
            $this->SetTextColor(0,0,0);
            $this->cell(60,4,"20210003024022021122904",1,0,"C"); //envia de parametro
            $this->cell(43,4,"OPERACIONES / G.GENERAL",1,0,"C");
            $this->cell(43,4,"FINANZAS / ADMINISTRACION",1,0,"C");
            $this->cell(43,4,"LOGISTICA",1,1,"C");

            $this->Ln(2);

            $this->MultiCell(130,3,utf8_decode('NOTA INFORMATIVA :
1. Se adjunta a la presente Orden los t??rminos y condiciones de compra.
2. Todo material recibido fuera de tiempo, SEPCON; se reserva los derechos de recepci??n y/o penalizaci??n sin               conocimiento previo del proveedor.
3. Al momento de hacer entrega de los materiales, el proveedor deber?? adjuntar cuando corresponda:                            Certificado de calidad, Hojas SDS, manuales de operaci??n y mantenimiento, certificado de calibraci??n,                        instrucciones de conservaci??n, etc.
4. Al momento de hacer la entrega de los materiales, el proveedor debe solicitar al almac??n "La Nota de                          Ingreso" debidamente sellada. 
5. Sirva adjuntar la Nota de Ingreso, Guia de Remisi??n y Factura, referente a una sola Orden de                                      Compra o Servicio
6. Consignar el n??mero de la Orden de Compra en la Guia de Remisi??n y Factura. Presentar Factura                              original, con copia Sunat.'),1);

            $this->setXY(144,229);
            $this->MultiCell(55,3.2,utf8_decode('***IMPORTANTE***
Es requisito indispensable que el proveedor ingrese en f??sico sus Facturas, Notas de Cr??dito y/o Notas de D??bito debidamente sustentadas, por el ??rea de Recepci??n de SEPCON (Av. San Borja Norte 445-San Borja), ??nico lugar
autorizado para recibir dichos documentos, de otra manera no podr??n ser ingresadas al proceso de pago. 
SEPCON no se responsabiliza por la p??rdida o extrav??o de documentos que no lleven su sello de recepci??n'),1);
            
        $this->Ln(2);
        $this->MultiCell(189,3.3,utf8_decode('En SEPCON contribuimos con la protecci??n, cuidado y conservaci??n del Medio Ambiente, por ello les alcanzamos algunas eco recomendaciones: 
(i) Use con responsabilidad y de forma racional los recursos no renovables. (ii) Reduzca el consumo de materiales desechables; busque, eval??e y proponga opciones ???eco-amigables??? y reutilice lo m??s posible. (iii) Si transporta materiales peligrosos, asegurarse de contar con permisos, plan de contingencia, hojas SDS, recursos y personal capacitado.'),1);

        $this->Ln(1);
        $this->Cell(189,6,utf8_decode("** SOMOS AGENTE DE RETENCI??N DEL IGV DE ACUERDO A R.S.219-2006 **"),0,0,"C");
        }
    }
?>