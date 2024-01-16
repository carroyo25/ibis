<?php
    require_once "public/fpdf/mc_table.inc.php";

    class PDF extends PDF_MC_Table{

        public function __construct($titulo,$condicion,$fecha,$moneda,$plazo,
                                    $lugar,$cotizacion,$fentrega,$pago,$importe,
                                    $info,$detalle,$usuario,$razon_social,
                                    $ruc,$direccion,$telefono,$correo,$retencion,
                                    $contacto,$tel_contacto,$cor_contacto,$direccion_almacen,$referencia,
                                    $procura,$finanzas,$operaciones,$condiciones)
        {
            parent::__construct();
            $this->titulo           = $titulo;
            $this->condicion        = $condicion;
            $this->fecha            = $fecha;
            $this->moneda           = $moneda;
            $this->plazo            = "SEGÚN ANEXOS";
            $this->lugar            = $lugar;
            $this->cotizacion       = $cotizacion;
            $this->fentrega         = $fentrega;
            $this->pago             = "SEGÚN ANEXOS";
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
            $this->condiciones      = $condiciones;
        }

        function header(){
            $this->Rect(10,10,30,20); //marco de la imagen
        	$this->Rect(40,10,130,20); //marco del titulo
        	$this->Rect(10,10,190,20); //marco general

            if ($this->condicion == 0) {
                $condicion = "VISTA PREVIA";
            }else if($this->condicion == 1){
                $condicion = "EMITIDO";
            }else if($this->condicion == 2){
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
	        $this->MultiCell(30,5,utf8_decode('PSPC-410-X-PR-001-FR-002 Revisión: 0 Emisión: 06/05/2019 '),0,'L',false);

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
	        $this->Cell(130,4,utf8_decode(strtoupper($this->lugar)),"BR",1); //envia de parametro

    		$this->Ln(1);

            $this->Cell(113,4,"1. DATOS DEL PROVEEDOR","1",0);
            $this->Cell(77,4,"2. CONDICIONES GENERALES","TRB",1);
            
            $this->Cell(13,4,utf8_decode("Señor(es) :"),"L",0);
            $this->Cell(100,4,utf8_decode($this->razon_social),"R",0); //envia de parametro
            $this->Cell(20,4,utf8_decode("Número RUC:"),0); 
            $this->Cell(57,4,$this->ruc,"R",1); //envia de parametro
            $this->Cell(13,4,utf8_decode("Dirección :"),"L",0);
            $this->MultiCell(95,3.5,utf8_decode($this->direccion),0); //envia de parametro
            $this->SetXY(123,52);
            $this->Cell(20,4,utf8_decode("Forma de Pago: "),"L",0);
            $this->Cell(57,4,utf8_decode($this->pago),"R",1); //envia de parametro
            $this->Cell(13,4,utf8_decode("Atención :"),"L",0);
            $this->Cell(100,4,utf8_decode($this->contacto),0); //envia de parametro
            $this->Cell(20,4,utf8_decode("Referencia: "),"L",0);
            $this->Cell(57,4,utf8_decode($this->referencia),"R",1);
            $this->Cell(13,4,utf8_decode("E-mail :"),"L",0);
            $this->Cell(100,4,utf8_decode($this->correo),"R",0); //envia de parametro
            $this->Cell(15,4,utf8_decode("N°.Cotización :"),0,0);
            $this->Cell(62,4,utf8_decode($this->cotizacion),"R",1); //envia de parametro
            $this->Cell(13,4,utf8_decode("Teléfono :"),"BL",0); 
            $this->Cell(100,4,utf8_decode($this->telefono),"B",0); //envia de parametro
            $this->Cell(20,4,utf8_decode("Fecha Entrega :"),"BL",0);
            $this->Cell(15,4,date("d/m/Y", strtotime($this->fentrega)),"B",0); //envia de parametro
            $this->Cell(20,4,utf8_decode("Plazo de entrega :"),"B",0);
            $this->Cell(22,4,utf8_decode($this->plazo),"BR",1); //envia de parametro
  
            $this->SetFillColor(229, 229, 229);

	        // Salto de línea
    		$this->Ln(5);
            $this->Rect(10,73,190,7,"F"); //fondo de mensaje
    		$this->SetWidths(array(10,15,15,15,10,80,15,15,15));
    		$this->SetAligns(array("C","C","C","C","C","C","C","C","C"));
    		$this->Row(array('Item',utf8_decode('Código'),'Payment Basis','Cant.','Und.',utf8_decode('Descripción'),'Nro.Pedido','Precio Unitario','Valor Total'));
                    
        }

        function footer(){
            $this->setXY(10,145);
            //$this->Ln(3);
            if ( $this->condiciones != "" )
                $this->MultiCell(190,3,utf8_decode($this->condiciones),"TLRB");

            $this->setXY(10,278);
            $this->SetFillColor(229, 229, 229);
            $this->SetFont('Arial',"","7");
            $this->cell(30,4,"ELABORADO POR",1,0,"C",true);
            $this->cell(30,4,"IMPRESO POR",1,1,"C",true);
            $this->SetFont('Arial',"","4");
            $this->cell(30,4,utf8_decode($this->usuario),1,0,"C"); //envia de parametro
            $this->cell(30,4,"SYSTEM",1,1,"C");
            $this->SetFont('Arial',"","7");
            $this->cell(30,4,"EMITIDO",1,0,"C",true);
            $this->cell(30,4,"FOLIO",1,1,"C",true);
            $this->cell(30,4,date("d-m-Y h:i:s"),1,0,"C");
            $this->Cell(30,4,utf8_decode('Página ').$this->PageNo()." de ".'/{nb}',1,1,"C");

            $this->setXY(80,-18);
            $this->cell(35,4,"","LTR",0);
            $this->cell(7,4,"",0,0); //espacio de separacion
            $this->cell(35,4,"","LTR",0);
            $this->cell(7,4,"",0,0); //espacio de separacion
            $this->cell(35,4,"","LTR",1);

            $this->setXY(80,-14);
            $this->cell(35,4,"","LR",0);
            $this->cell(7,4,"",0,0); //espacio de separacion
            $this->cell(35,4,"","LR",0);
            $this->cell(7,4,"",0,0); //espacio de separacion
            $this->cell(35,4,"","LR",1);

            $this->setXY(80,-10);
            $this->cell(35,4,"","LR",0);
            $this->cell(7,4,"",0,0); //espacio de separacion
            $this->cell(35,4,"","LR",0);
            $this->cell(7,4,"",0,0); //espacio de separacion
            $this->cell(35,4,"","LR",1);

            $this->setXY(80,-6);
            $this->cell(35,4,"PROCURA INTERNACIONAL",1,0,"C");
            $this->cell(7,4,"",0,0); //espacio de separacion
            $this->cell(35,4,"OPERACIONES",1,0,"C");
            $this->cell(7,4,"",0,0); //espacio de separacion
            $this->cell(35,4,"GERENTE FINANZAS",1,1,"C");
        }
    }
?>