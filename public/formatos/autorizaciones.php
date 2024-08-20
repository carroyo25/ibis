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
			$this->SetXY(10,18);
			$this->SetX(135);
			$this->Cell(25,4,"Centro de Costo: ",0,0);
			$this->Cell(40,4,"3500 COMPRESION MIPAYA",0,1);
			$this->Cell(20,4,"TRANSFERENCIA",0,0);
			$this->Cell(5,4,$this->tipo == 253 ? "X":"",1,0,"C");
			$this->SetX(40);
			$this->Cell(10,4,"ORIGEN",0,0);
			$this->Cell(25,4,"MALVINAS",0,0);
			$this->Cell(18,4,"IDA Y VUELTA",0,0);
			$this->Cell(5,4,"SI",0,0);
			$this->Cell(5,4,"",1,0);
			$this->Cell(5,4,"NO",0,0);
			$this->Cell(5,4,"",1,0);
			$this->SetX(135);
			$this->Cell(25,4,"Area Solicitante:",0,0);
			$this->Cell(40,4,"MANTENIMIENTO DE EQUIPOS",0,1);
			$this->Cell(20,4,"REPARACION",0,0);
			$this->Cell(5,4,$this->tipo == 253 ? "": "X",1,0,"C");
			$this->SetX(40);
			$this->Cell(10,4,"DESTINO",0,0);
			$this->Cell(25,4,"PAGORENI B",0,0);
			$this->SetX(135);
			$this->Cell(25,4,"Persona asignada:",0,0);
			$this->Cell(40,4,"",0,1);

			$this->SetXY(5,35);
			$this->Cell(10,4,"ITEM",0,0);
			$this->Cell(10,4,"CODIGO",0,0);
			$this->Cell(10,4,"DESCRIPCION",0,0);
			$this->Cell(10,4,"UNIDAD",0,0);
			$this->Cell(10,4,"CANT.",0,0);
			$this->Cell(10,4,"SERIE/NRO.PARTE",0,0);
			$this->Cell(10,4,"PERSONA O EQUIPO DESTINO",0,0);
			$this->Cell(10,4,"OBSERVACIONES",0,0);


        }

        function Footer() {
            $this->SetFont('Arial','I',6);
        }
    }
?>