<?php 
    require_once "public/fpdf/mc_table.inc.php";

    class PDF extends PDF_MC_Table{
        public function __construct($ndoc,$nombre,$almacenero,$proyecto,$fechaDevuelto,$cctext)
        {
            parent::__construct();
            $this->ndoc         = $ndoc;
            $this->nombre       = $nombre;
            $this->almacenero   = $almacenero;
            $this->proyecto     = $proyecto;
            $this->fechaDevuelto= $fechaDevuelto;
			$this->cctext		= $cctext;
        }

        function Header(){
			$almacen = $_SESSION['nombres'];

            $this->Rect(10,10,30,20); //marco de la imagen
        	$this->Rect(10,10,190,20); //marco general

        	$this->SetFillColor(229, 229, 229);
        	//$this->Rect(70,24,70,5,"F"); //fondo de mensaje
        	$this->Image('public/img/logo.png',12,12,25);
	        $this->SetFont('Arial','B',12);
			$this->SetTextColor(0,0,0);

           // $this->SetFillColor(229, 229, 229);
	        $this->Cell(190,7,"CONSTANCIA DE LIBRE ADEUDO",0,1,'C');
	        $this->SetFont('Arial','B',10);
            $this->SetX(50);
	        $this->Cell(190,6,'Nombre del Trabajador: '.utf8_decode($this->nombre),0,1,'L'); //pasa dato
            $this->SetX(50);
	        $this->Cell(190,7,'Responsable del Almacen : '.utf8_decode($almacen),0,1,'L'); //pasa dato condicion
            
			$this->ln(15);

			$this->Cell(190,6,'Fecha de Emision : '.date('d/m/Y H:i:s'),0,1,'L'); //pasa dato
			$this->Cell(190,6,'Proyecto                : '.utf8_decode($this->cctext),0,1,'L'); //pasa dato

			$this->ln(3);

            $this->SetFont('Arial','B',6);
    		//$this->Rect(10,44,190,3,"F"); //fondo de mensaje
    		$this->SetWidths(array(15,25,110,20,20));
    		$this->SetAligns(array("C","C","C","C","C"));
    		$this->Row(array('Item',utf8_decode('Código'),utf8_decode('Descripción'),'Estado','Cantidad'));
        }

        // Pie de página
		function Footer(){
		    $this->SetY(-70);
		    //$this->Ln(20);
		    $this->Line(20, 225, 65, 225);
		    $this->Line(80, 225, 130, 225);
		    $this->Line(150, 225, 190, 225);

			$firma = $_SESSION['iduser'];

			$firma = './public/img/'.$firma.'.jpg';

			if ( file_exists($firma) ){
				$this->Image($firma,160,205,25);
			}

		   	$this->Cell(64,4,"FIRMA JEFE DE OBRA",0,0,"C");
		    $this->Cell(64,4,"FIRMA DEL TRABAJADOR",0,0,"C");
		    $this->Cell(64,4,"FIRMA JEFE DE ALMACEN",0,1,"C");

			
		}
    }
?>