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
            //$this->Rect(4,5,21,12);
			//$this->Rect(4,17,118,18);
			//$this->Rect(122,17,23,23);

            $this->Image('public/img/logo.png',5,6,20);
			// Arial bold 15
			$this->SetXY(25,5);
			$this->SetFont('Arial','B',13);
			// Título
			$this->Cell(97,12,"AUTORIZACION DE TRASLADO DE EQUIPOS / MATERIALES",'T',0,'C');
			// Salto de línea// Movernos a la derecha
			//$this->Cell(5);
        }

        function Footer() {
            $this->SetFont('Arial','I',6);
        }
    }
?>