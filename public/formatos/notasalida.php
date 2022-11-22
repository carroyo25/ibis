<?php
	require_once "public/fpdf/mc_table.inc.php";

	class PDF extends PDF_MC_Table{
		public function __construct($ndoc,$condicion,$dia,$mes,$anio,$proyecto,$origen,
									$destino,$movimiento,$nguia,$nautoriza,$cautoriza)
        {
            parent::__construct();
            $this->ndoc         = $ndoc;
            $this->condicion    = $condicion;
            $this->dia          = $dia;
            $this->mes          = $mes;
            $this->anio         = $anio;
            $this->proyecto     = $proyecto;
            $this->origen       = $origen;
			$this->destino		= $destino;
            $this->movimiento   = $movimiento;
            $this->nguia        = $nguia;
            $this->nautoriza    = $nautoriza;
            $this->cautoriza    = $cautoriza;
        }
	// Cabecera de página
		function Header(){
		   
		    $this->Rect(10,10,30,20); //marco de la imagen
        	$this->Rect(10,10,190,20); //marco general

        	$this->SetFillColor(229, 229, 229);
        	$this->Rect(70,24,70,5,"F"); //fondo de mensaje
        	$this->Image('public/img/logo.png',12,12,25);
	        $this->SetFont('Arial','B',12);
			$this->SetTextColor(0,0,0);

	 		$this->SetFillColor(229, 229, 229);
	        $this->Cell(190,7,utf8_decode('REGISTRO DE SALIDA N° ').$this->ndoc,0,1,'C');
	        $this->SetFont('Arial','B',10);
	        $this->Cell(190,7,'',0,1,'C'); //pasa dato condicion
	        $this->Cell(190,7,'VISTA PREVIA',0,0,'C'); //pasa dato condicion

	        $this->SetXY(160,10);
	        $this->SetFont('Arial','B',8);
	        $this->Cell(13,5,utf8_decode("Día"),1,0,"C",1);
	        $this->Cell(13,5,"Mes",1,0,"C",1);
	        $this->Cell(14,5,utf8_decode("Año"),1,1,"C",1);
	        $this->SetXY(160,15);
	        $this->Cell(13,5,$this->dia,1,0,"C");//pasa dato
	        $this->Cell(13,5,$this->mes,1,0,"C");//pasa dato
	        $this->Cell(14,5,$this->anio,1,1,"C");//pasa dato
	        $this->SetXY(160,20);
	        $this->Cell(13,5,utf8_decode("OC"),1,0,"C",1);
	        $this->Cell(13,5,"PE",1,0,"C",1);
	        $this->Cell(14,5,utf8_decode("Guia"),1,1,"C",1);
	        $this->SetXY(160,25);
	        $this->Cell(13,5,"",1,0,"C");//pasa dato
	        $this->Cell(13,5,"",1,0,"C");//pasa dato
	        $this->SetFont('Arial','B',6);
	        $this->Cell(14,5,$this->nguia,1,1,"C");//pasa dato

	        $this->SetXY(10,32);
	        $this->SetFont('Arial','B',5);
	        $this->Cell(20,5,"Proyecto",1,0);
	        $this->Cell(90,5,utf8_decode($this->proyecto),1,0); //pasa dato
	        $this->Cell(20,5,utf8_decode("Almacén Origen"),1,0);
	        $this->Cell(60,5,utf8_decode($this->origen),1,1); //pasa dato
	        $this->Cell(20,5,"Tipo Movimiento",1,0);
	        $this->Cell(90,5,$this->movimiento,1,0); //pasa dato
	        $this->Cell(20,5,utf8_decode("Almacén Destino"),1,0);
	        $this->Cell(60,5,utf8_decode($this->destino),1,1); //pasa dato

	        // Salto de línea
    		$this->Ln(1);
    		$this->SetFont('Arial','B',6);
    		$this->Rect(10,43,190,3,"F"); //fondo de mensaje
    		$this->SetWidths(array(10,15,70,8,10,30,17,15,15));
    		$this->SetAligns(array("C","C","C","C","C","C","C","C","C"));
    		$this->Row(array('Item',utf8_decode('Código'),utf8_decode('Descripción'),
    				'Und.','Cant.','Observ. Item','Proveedor','Estado',
    				'Ubica Fisica'));
    		
		}

		// Pie de página
		function Footer(){
		    $this->SetY(-70);
		    //$this->Ln(20);
		    $this->Line(20, 225, 65, 225);
		    $this->Line(80, 225, 130, 225);
		    $this->Line(150, 225, 190, 225);

		    $this->SetFont('Arial','B',8);
		    $this->Cell(64,4,$this->nautoriza,0,0,"C"); //pasa dato
		    $this->Cell(64,4,"",0,0,"C"); //pasa dato
		    $this->Cell(64,4,"",0,1,"C"); // pasa dato

		    $this->SetFont('Arial','',6);
		    $this->Cell(64,2,$this->cautoriza,0,0,"C"); //pasa dato
		    $this->Cell(64,2,"",0,0,"C"); //pasa dato
		    $this->Cell(64,2,"",0,1,"C"); //pasa dato
		   
		   	$this->Cell(64,4,"Autorizado",0,0,"C");
		    $this->Cell(64,4,"Recibido",0,0,"C");
		    $this->Cell(64,4,"Expeditado",0,1,"C");


		}
	}
?>