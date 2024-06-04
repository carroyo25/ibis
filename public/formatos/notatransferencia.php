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
		   
		    $this->Rect(10,10,30,20); //marco de la imagen
        	$this->Rect(10,10,190,20); //marco general

        	$this->SetFillColor(229, 229, 229);
        	$this->Rect(70,24,70,5,"F"); //fondo de mensaje
        	$this->Image('public/img/logo.png',12,12,25);
	        $this->SetFont('Arial','B',12);
			$this->SetTextColor(0,0,0);

	 		$this->SetFillColor(229, 229, 229);
            $this->SetFont('Arial','B',9);
	        $this->Cell(190,7,utf8_decode('NOTA DE INGRESO DE EQUIPOS/ MATERIALES Y/O REPUESTOS'),0,1,'C');
	        $this->SetFont('Arial','B',10);
	        $this->Cell(190,7,'',0,1,'C'); //pasa dato condicion
	        $this->Cell(190,7,'EMITIDO',0,0,'C'); //pasa dato condicion

	        $this->SetXY(160,10);
	        $this->Cell(40,5,"","LR",1,"C");//pasa dato*/
            $this->SetXY(160,12);
            $this->SetFont('Arial','B',8);
            $this ->MultiCell(35,2.3,"PSPB-430-X-FR-010
                                    Revision: 2
            Emision:13/05/2015", 'L', 'L', 0);
            $this->SetXY(160,24);

            $this->SetTextColor(233,61,59);
            $this->Cell(40,5,utf8_decode("N° ").$this->nguia,"LR",1,"C");//pasa dato*/
            $this->SetTextColor(0,0,0);

            $this->SetFont('Arial','B',10);

	        $this->SetXY(10,32);
	        $this->SetFont('Arial','B',5);
	        $this->Cell(12,5,"Nro Registro",0,0);
	        $this->Cell(24,5,$this->nguia,0,0); //pasa dato
	        $this->Cell(12,5,"Fecha",0,0);
	        $this->Cell(24,5,utf8_decode($this->fecha),0,0); //pasa dato
	        $this->Cell(10,5,"Nro. Guia",0,0);
	        $this->Cell(24,5,"",0,0); //pasa dato
	        $this->Cell(15,5,utf8_decode("Proyecto"),0,0);
	        $this->Cell(24,5,"",0,0);
            $this->Cell(10,5,utf8_decode("Centro de Costo"),0,0);
	        $this->Cell(20,5,"",0,1);

            $this->Cell(20,5,utf8_decode("Tipo de Movimiento"),0,0);
	        $this->Cell(52,5,"TRANSFERENCIA DE ALMACEN",0,0);
            $this->Cell(20,5,utf8_decode("Pedido"),0,0);
	        $this->Cell(53,5,"",0,0);
            $this->Cell(20,5,utf8_decode("Area Solicitante"),0,0);
	        $this->Cell(60,5,"",0,1);

            $this->Cell(20,5,utf8_decode("Almacen Origen"),0,0);
	        $this->Cell(52,5,"",0,0);
            $this->Cell(20,5,utf8_decode("Nro. Compra"),0,0);
	        $this->Cell(50,5,"",0,1);

	        // Salto de línea
    		$this->Ln(1);
    		$this->SetFont('Arial','B',6);
    		$this->Rect(10,47,190,3,"F"); //fondo de mensaje
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