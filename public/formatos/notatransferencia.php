<?php
	require_once "public/fpdf/mc_table.inc.php";

	class PDF extends PDF_MC_Table{
		public function __construct($ndoc,$condicion,$dia,$mes,$anio,$proyecto,$origen,
									$destino,$movimiento,$nguia,$nautoriza,$cautoriza,$fecha)
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
            $this->fecha        = $fecha;
        }
	// Cabecera de página
		function Header(){
		   
		    $this->Rect(10,10,30,16); //marco de la imagen
        	$this->Rect(10,10,190,16); //marco general
			$this->Rect(170,10,30,16); //marco del numero

			$this->Rect(10,26,190,18); //segundo marco

        	$this->SetFillColor(229, 229, 229);
        	$this->Image('public/img/logo.png',12,12,25);
	        $this->SetFont('Arial','B',12);
			$this->SetTextColor(0,0,0);
            $this->SetFont('Arial','B',9);
			$this->Cell(190,3,"",0,1,'C');
	        $this->Cell(190,7,utf8_decode('NOTA DE SALIDA DE EQUIPOS/ MATERIALES Y/O REPUESTOS'),0,1,'C');

	        $this->SetXY(160,10);
            $this->SetTextColor(233,61,59);
            $this->Cell(45,5,utf8_decode("N° ").$this->nguia,0,1,"C");//pasa dato*/
            $this->SetTextColor(0,0,0);

            $this->SetFont('Arial','B',10);

	        $this->SetXY(10,27);
	        $this->SetFont('Arial','B',5);
	        $this->Cell(20,4,"TRANSFERENCIA",0,0);
	        $this->Cell(5,4,"X",1,0); //pasa dato
			$this->Cell(11,4,"",0,0); //pasa dato
			$this->Cell(20,4,"DEVOLUCION",0,0);
	        $this->Cell(5,4,"",1,0); //pasa dato
			$this->Cell(11,4,"",0,0); //pasa dato
			$this->Cell(12,4,"OTROS",0,0);
	        $this->Cell(5,4,"",1,0); //pasa dato
			$this->Cell(11,4,"",0,0); //pasa dato
			$this->Cell(25,4,"MATERIAL DE TERCEROS",0,0);
	        $this->Cell(5,4,"",1,0); //pasa dato
			$this->Cell(20,4,"",0,0); //pasa dato
			$this->Cell(18,4,"No Documento",1,0);
	        $this->Cell(22,4,"",1,1); //pasa dato
			
			$this->Cell(150,4,"",0,0); //pasa dato
			$this->Cell(18,4,"Centro de Costos",1,0);
	        $this->Cell(22,4,"",1,1); //pasa dato
	        
			$this->Cell(20,4,"SALIDA",0,0);
	        $this->Cell(5,4,"",1,0); //pasa dato
			$this->Cell(11,4,"",0,0); //pasa dato
			$this->Cell(20,4,"PRESTAMO",0,0);
	        $this->Cell(5,4,"",1,0); //pasa dato
			$this->Cell(89,4,"",0,0);
			$this->Cell(18,4,"Area Solicitante",1,0);
	        $this->Cell(22,4,"",1,1); //pasa dato
			$this->Cell(150,4,"",0,0);
			$this->Cell(18,4,"Lugar",1,0);
	        $this->Cell(22,4,"",1,1); //pasa dato


	        // Salto de línea
    		$this->Ln(1);
    		$this->SetFont('Arial','B',6);
    		$this->Rect(10,44,190,3,"F"); //fondo de mensaje
    		$this->SetWidths(array(10,15,105,10,25,25));
    		$this->SetAligns(array("C","C","C","C","C","C"));
    		$this->Row(array('Item',utf8_decode('Código'),utf8_decode('Descripción'),
    				'Und.','Cant Solicitada','Cant.Despachada'));
    		
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