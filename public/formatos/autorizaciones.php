<?php 
    require_once "public/fpdf/fpdf.php";

    class PDF extends FPDF{
        public function __construct($numero,$costos,$area,$solicitante,$origen,$destino,$tipo,$autoriza,$observaciones) {
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


            function header() {

            }

            function footer() {
                
            }
		}

    }
?>