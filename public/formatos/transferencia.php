<?php

 	require_once "public/fpdf/fpdf.php";
	 class PDF extends FPDF{
		public function __construct($numero) {
			parent::__construct();
			$this->numero = $numero;
		}

		function Header() {

			$this->Rect(4,5,21,12);
			$this->Rect(4,17,121,18);
			$this->Rect(125,17,20,24);

			$this->Image('public/img/logo.png',5,6,20);
			// Arial bold 15
			$this->SetXY(25,5);
			$this->SetFont('Arial','B',13);
			// Título
			$this->Cell(100,12,"NOTA DE TRANSFERENCIA",1,0,'C');
			// Salto de línea// Movernos a la derecha
			//$this->Cell(5);
			
			$this->SetFont('Arial','',13);
			$this->Cell(20,12,$this->numero,1,1,'C');

			$this->Ln(4);
			$this->SetFont('Arial','',6);
			$this->Cell(16,3,"DEVOLUCION",0,0,'L');
			$this->Cell(5,3,"",1,0,'L');
			$this->Cell(5);
			$this->Cell(15,3,"PRESTAMO",0,0,'L');
			$this->Cell(5,3,"",1,0,'L');
			$this->Cell(5);
			$this->Cell(25,3,"MATERIAL TERCEROS",0,0,'L');
			$this->Cell(5,3,"",1,0,'L');
			$this->Cell(5);
			$this->Cell(10,3,"DEBITO",0,0,'L');
			$this->Cell(5,3,"",1,1,'L');
			$this->Ln(4);
			$this->Cell(16,3,"PERSONALES",0,0,'L');
			$this->Cell(5,3,"",1,1,'L');
			$this->Ln(8);
		}

		function Footer(){
			 // Posición: a 1,5 cm del final
			$this->SetY(-15);
			// Arial italic 8
			$this->SetFont('Arial','I',8);
			// Número de página
			$this->Cell(0,10,"Prueba");
		}
	}
?>