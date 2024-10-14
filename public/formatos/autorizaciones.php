<?php 
    require_once "public/fpdf/fpdf.php";

    class PDF extends FPDF{
        public function __construct($numero,$costos,$area,$solicitante,$origen,$destino,$tipo,$autoriza,$observaciones,$emision) {
			parent::__construct();
			$this->numero = $numero;
			$this->costos = $costos;
            $this->area = $area;
            $this->solicitante = $solicitante;
			$this->origen = $origen;
			$this->destino = $destino;
			$this->tipo = $tipo;
			$this->autoriza = $autoriza;
			$this->observaciones = $observaciones;
            $this->observaciones = $emision;
        }

        function Header() {
            $this->Rect(4,5,21,12);
			$this->Rect(4,17,200,15);

            $this->Image('public/img/logo.png',5,6,20);
			// Arial bold 15
			$this->SetXY(25,5);
			$this->SetFont('Arial','B',12);
			// Título
			$this->Cell(179,12,"AUTORIZACION DE TRASLADO DE EQUIPOS / MATERIALES",'TR',0,'C');
			// Salto de línea// Movernos a la derecha
			$this->SetFont('Arial','B',6);
			$this->SetXY(10,17);
			$this->SetX(135);
			$this->Cell(25,5,"Centro de Costo: ",'LBR',0);
			$this->Cell(44,5,utf8_decode($this->costos),'B',1);
			$this->Cell(20,4,"TRANSFERENCIA",0,0);
			$this->Cell(5,4,$this->tipo == 274 ? "X":"",1,0,"C");
			$this->SetX(40);
			$this->Cell(10,4,"ORIGEN",0,0);
			$this->Cell(25,4,utf8_decode($this->origen),0,0);
			$this->Cell(18,4,"IDA Y VUELTA",0,0);
			$this->Cell(5,4,"SI",0,0);
			$this->Cell(5,4,"",1,0);
			$this->Cell(5,4,"NO",0,0);
			$this->Cell(5,4,"",1,0);
			$this->SetX(135);
			$this->Cell(25,5,"Area Solicitante:",'LRB',0);
			$this->Cell(44,5,utf8_decode($this->area),'B',1);
			$this->Cell(20,4,"REPARACION",0,0);
			$this->Cell(5,4,$this->tipo == 273 ? "": "X",1,0,"C");
			$this->SetX(40);
			$this->Cell(10,4,"DESTINO",0,0);
			$this->Cell(25,4,utf8_decode($this->destino),0,0);
			$this->SetX(135);
			$this->Cell(25,5,"Persona asignada:",'LRB',0);
			$this->Cell(44,5,utf8_decode($this->solicitante),'B',1);

			$this->SetXY(4,35);
			$this->Cell(8,6,"ITEM",1,0,'C');
			$this->Cell(15,6,"CODIGO",1,0,'C');
			$this->Cell(70,6,"DESCRIPCION",1,0,'C');
			$this->Cell(10,6,"UND.",1,0,'C');
			$this->Cell(15,6,"CANT.",1,0,'C');
			$this->Cell(22,6,"SERIE/NRO.PARTE",1,0,'C');
			$this->Multicell(25,3,"PERSONA O EQUIPO DESTINO",1,'C');
			$this->SetXY(169,35);
			$this->Multicell(35,6,"OBSERVACIONES",1,'C');
        }

        // Pie de página
		function Footer(){
		    $this->SetXY(4,-60);
			
			$this->Line(5, 236, 204, 236);
			$this->Line(5, 247, 204, 247);

			$this->Cell(20,4,"OBSERVACIONES :",0,0,"L");
			$this->Cell(100,4,utf8_decode($this->observaciones),0,1,"L");

			$this->SetXY(4,-50);
			$this->Cell(20,8,"AUTORIZADO POR :",1,0,"L");
			$this->SetXY(24,-50);
			$this->Cell(80,4,"NOMBRE :",'LRB',0,"L");
			$this->Cell(100,4,"FIRMA :","LR",0);
			$this->SetXY(24,-46);
			$this->Cell(80,4,"CARGO :",1,0,"L");
			$this->Cell(100,4,"","LRB",1);
			$this->SetX(4);
			$this->Cell(67,4,"RECEPCION ORIGEN :",1,0,"C");
			$this->Cell(67,4,"TRANSPORTADO POR :",1,0,"C");
			$this->Cell(66,4,"RECEPCION EN DESTINO :",1,1,"C");
			$this->SetX(4);
			$this->Cell(67,4,"FIRMA :",'LBR',0,"L");
			$this->Cell(67,4,"FIRMA :",'LBR',0,"L");
			$this->Cell(66,4,"FIRMA :",'LBR',1,"L");
			$this->SetX(4);
			$this->Cell(67,4,"NOMBRE :",'LBR',0,"L");
			$this->Cell(67,4,"NOMBRE :",'LBR',0,"L");
			$this->Cell(66,4,"NOMBRE :",'LBR',1,"L");
			$this->SetX(4);
			$this->Cell(67,4,"FECHA :",'LBR',0,"L");
			$this->Cell(67,4,"FECHA :",'LBR',0,"L");
			$this->Cell(66,4,"FECHA :",'LBR',1,"L");
		}
    }
?>