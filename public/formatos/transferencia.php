<?php

 	require_once "public/fpdf/fpdf.php";
	 class PDF extends FPDF{
		public function __construct($numero,$origen,$destino,$reponsable,$documento) {
			parent::__construct();
			$this->numero = $numero;
			$this->origen = $origen;
			$this->destino = $destino;
			$this->responsable = $responsable;
			$this->documento = $documento;

		}

		function Header() {

			$this->Rect(4,5,21,12);
			$this->Rect(4,17,118,18);
			$this->Rect(122,17,23,23);

			$this->Image('public/img/logo.png',5,6,20);
			// Arial bold 15
			$this->SetXY(25,5);
			$this->SetFont('Arial','B',13);
			// Título
			$this->Cell(97,12,"NOTA DE TRANSFERENCIA",'T',0,'C');
			// Salto de línea// Movernos a la derecha
			//$this->Cell(5);
			
			$this->SetFont('Arial','',13);
			$this->Cell(23,12,$this->numero,'TLR',1,'C');

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
			$this->Ln(4);
			$this->SetX(4);
			$this->SetFont('Arial','',5);
			$this->Cell(18,5,"ALMACEN ORIGEN",'BL',0,'L');
			$this->Cell(36,5,"",'L',0,'L');
			$this->Cell(20,5,"ALMACEN DESTINO",'BL',0,'L');
			$this->Cell(36,5,"",'L',0,'L');

			$this->SetFont('Arial','',4);
			$this->SetXY(22,36);
			$this->Multicell(36,1.6,utf8_decode($this->origen));
			$this->SetXY(78,36);
			$this->Multicell(44,1.6,utf8_decode($this->destino));

			$this->SetFont('Arial','',5);
			$this->SetXY(122,17);
			$this->Cell(13,9,"CC:",'B',0,'L');
			$this->Cell(10,9,"",'B',1,'L');
			$this->SetX(122);
			$this->Cell(10,9,"FASE:",'B',0,'L');
			$this->Cell(13,9,"",'B',1,'L');
			$this->SetX(122);
			$this->Cell(10,6,"USUARIO:",0,0,'L');
			$this->Cell(13,6,"",0,1,'L');
			
			$this->SetFont('Arial','',6);
			$this->SetFillColor(0,92,132);
			$this->SetTextColor(255);

			$this->SetXY(4,40);
			$this->Cell(10,4,"IT",1,0,'C',1);
			$this->Cell(20,4,"COD.SICAL",1,0,'C',1);
			$this->Cell(88,4,"DESCRIPCION DEL MATERIAL",1,0,'C',1);
			$this->Cell(10,4,"UM",1,0,'C',1);
			$this->Cell(13,4,"CANT",1,1,'C',1);
		}

		function Footer(){
			 // Posición: a 1,5 cm del final
			//$this->SetY(-26);
			// Arial italic 8
			$this->SetFont('Arial','I',6);
			// Número de página
			$this->setX(4);
			$this->Cell(141,6,"OBSERVACIONES :",1,1);

			$this->setX(4);
			$this->Cell(47,6,"",'LR',0);
			$this->Cell(47,6,"",'LR',0);
			$this->Cell(47,6,"",'LR',1);
			
			$this->SetFont('Arial','',6);

			$this->setX(4);
			$this->Cell(3,3,"",'LB',0);
			$this->Cell(41,3,"REPONSABLE ALMACEN",'TB',0,'C');
			$this->Cell(3,3,"",'RB',0);


			$this->Cell(3,3,"",'LB',0);
			$this->Cell(41,3,"REPONSABLE RECEPCION",'TB',0,'C');
			$this->Cell(3,3,"",'RB',0);
			
			$this->Cell(3,3,"",'LB',0);
			$this->Cell(41,3,"REPONSABLE DE DESPACHO",'TB',0,'C');
			$this->Cell(3,3,"",'RB',1);

			$this->setX(4);
			$this->SetFont('Arial','',5);
			$this->Cell(141,4,$this->documento,1,1,'R');

		}
	}
?>