<?php
    require_once "public/fpdf/prn_table.inc.php";

    class PDF extends PDF_MC_Table{
        // Cabecera de página
            public function __construct($nguia,$fecha_emision,$ruc,$razondest,$direccdest,$raztransp,$ructransp,$dirtransp,
                                        $vianomorg,$nroorg,$distorg,$zonaorg,$feenttrans,$modtras,$vianomodest,$nrodest,$zondest,$depdest,
                                        $marca,$placa,$detcond,$licencia,$tipoEnvio,$referido,$origen,$anio,$observaciones,$atencion){
                parent::__construct();
                $this->nguia = $nguia;
                $this->fecha_emision = $fecha_emision;
                $this->ruc = $ruc;
                $this->razondest = $razondest;
                $this->direccdest = $direccdest;
                $this->raztransp = $raztransp;
                $this->ructransp = $ructransp;
                $this->dirtransp = $dirtransp;
                $this->vianomorg = $vianomorg;
                $this->nroorg = $nroorg;
                $this->distorg = $distorg;
                $this->zonaorg = $zonaorg;
                $this->vianomodest = $vianomodest;
                $this->nrodest = $nrodest;
                $this->zondest = $zondest;
                $this->depdest = $depdest;
                $this->marca = $marca;
                $this->placa = $placa;
                $this->detcond = $detcond;
                $this->licencia = $licencia;
                $this->feenttrans = $feenttrans;
                $this->modtras = $modtras;
                $this->tipoEnvio = $tipoEnvio;
                $this->referido = $referido;
                $this->origen = $origen;
                $this->anio = $anio;
                $this->observaciones = $observaciones;
                $this->atencion = $atencion;
            }

            function Header(){
                $this->SetFillColor(229, 229, 229);
                
                $this->SetFont('Arial','B',11);
                $this->SetXY(53,15);
                $this->SetFont('Arial','B',12);
                $this->SetXY(135,18);
                $this->SetXY(135,28);
                $this->SetFont('Arial','B',10);
                $this->SetXY(135,38);
                $this->SetFont('Arial','B',12);
                $this->SetFont('Times',"",7);


                $this->SetXY(13,32);
                $this->SetFont('Arial',"",8);
                $this->Cell(15,6,"ORIGEN :",0,0);
                $this->Cell(10,6,$this->origen,0,1);
                
                $this->SetXY(150,40);
                $this->SetFont('Arial',"",7);
                $this->Cell(35,6,$this->anio.' 001 - '.$this->nguia,0,0);
                $this->Cell(5,6,"R.S:",0,0);
                $this->Cell(5,6,str_pad($this->referido,5,0,STR_PAD_LEFT),0,1);
                $this->SetFont('Arial',"",9);

                $this->SetXY(11,40);
                $this->Cell(30,6,"",0,0);
                $this->Cell(30,6,$this->fecha_emision,0,0);
                $this->Cell(10,6,"Envio",0,0);
                $this->SetFont('Arial',"",12);
                $this->Cell(80,6,$this->tipoEnvio,0,1);
                //fin de cabecera
                $this->SetFont('Arial','',8);
                $this->SetXY(10,50);
                $this->Cell(92,5,"",0,0,"C",false);
                $this->SetXY(14,53);
                $this->Cell(12,5,utf8_decode($this->razondest),0,1);
                $this->SetX(10);
                $this->Cell(5,5,"",0,0);
                $this->Cell(12,5,utf8_decode($this->direccdest),0,1);
                $this->SetX(10);
                $this->Cell(5,5,"",0,0);
                $this->Cell(12,5,$this->ruc,0,0);
                $this->SetXY(108,50);
                $this->Cell(92,5,"",0,1,"C",false);
                $this->SetXY(115,52);
                $this->Cell(25,5,"",0,0);
                $this->Cell(15,5,utf8_decode($this->raztransp),0,1);
                $this->SetX(120);
                $this->Cell(12,5,"",0,0);
                $this->MultiCell(75,3,utf8_decode($this->dirtransp),0,1);
                $this->SetX(125);
                $this->Cell(15,5,"",0,0);
                $this->Cell(15,5, $this->ructransp,0,1);
                //$this->RoundedRect(13, 72, 92, 20, 1, '1234', 'D');

                //punto de partida-punto de llegada
                $this->SetXY(16, 70);
                $this->SetFont('Arial','',9);
                $this->Cell(94,5,"",0,1,"C",false);
                $this->SetX(15);
                $this->MultiCell(80,4,utf8_decode($this->vianomorg),0,1);
    
                //$this->RoundedRect(108, 72, 92, 20, 1, '1234', 'D');
                $this->SetXY(108,70);
                $this->Cell(94,5,"",0,1,"C",false);
                $this->SetX(110);
                $this->MultiCell(80,4,utf8_decode($this->vianomodest ." - ". $this->depdest),0,1);
    
                //$this->RoundedRect(13, 94, 187, 20, 1, '1234', 'D');
                //$this->SetXY(13,80);
                //$this->Cell(187,5,"UNIDAD DE TRANSPORTE Y CONDUCTOR",1,1,"C",true);
                $this->SetXY(12,90);
                $this->Cell(35,5,"",0,0);
                $this->Cell(45,5,utf8_decode($this->marca . "-" .$this->placa),0,0);
                $this->Cell(60,5,"",0,0);
                
                if ($this->feenttrans =="")
                    $this->Cell(92,5,"",0,1);
                else
                    $this->Cell(92,5,$this->feenttrans,0,1);

                $this->SetX(10);
                $this->Cell(35,5,"",0,0);
                $this->Cell(45,5,utf8_decode($this->detcond),0,0);
                $this->Cell(62,5,"",0,0);
                $this->Cell(92,5,utf8_decode($this->licencia),0,1);
    
                // Salto de línea
                $this->Ln(1);
                $this->SetFillColor(0, 0, 0);
                $this->SetTextColor(255,255,255);
                $this->SetXY(3,110); //detalle del documento
                $this->SetFont('Arial','',7);
                $this->SetWidths(array(10,15,25,147));
                $this->SetAligns(array("C","C","C","C"));
                $this->SetCellHeight(3);
                $this->SetFill(true);
                $this->Row(array('ITEM',utf8_decode('CANTIDAD'),'UNIDAD',utf8_decode('DESCRIPCION')));
            }
    
            function Footer(){
                //$this->SetFillColor(229, 229, 229);
                $this->SetY(-60);
                //$this->RoundedRect(13, 250, 187, 30, 1, '1234', 'D');
                $this->SetXY(13,250);
                $this->SetFont('Arial','',9);
                $this->Cell(140,6,"",0,0,"C");
                $this->SetFont('Arial','',6);
                $this->SetXY(15,256);
                $this->Cell(15,4,"",0,0);
                $this->Cell(5,4,"",0,0,"C");
                $this->SetXY(15,261);
                $this->Cell(15,4,"",0,0);
                $this->Cell(5,4,"",0,0,"C");
                $this->SetXY(15,263);
                $this->Cell(15,4,"",0,1);
                $this->SetXY(15,266);
                $this->Cell(15,4,"",0,0);
                $this->Cell(5,4,"",0,0,"C");
                $this->SetXY(15,271);
                $this->Cell(15,4,"",0,0);
                $this->Cell(5,4,"",0,0,"C");
    
                $this->SetXY(40,225);
                $this->SetFont('Arial','',9);
                $this->Cell(90,4,"ATENCION".$this->atencion,0,1);
                $this->SetX(35);
                $this->Cell(90,4,$this->observaciones,0,1);
                $this->SetXY(40,261);
                $this->Cell(25,4,"",0,0);
                $this->Cell(5,4,"",0,0,"C");
                $this->SetXY(37,266);
                $this->Cell(25,4,"",0,0);
                $this->Cell(5,4,"",0,0,"C");
                $this->SetXY(37,268);
                $this->Cell(25,4,"",0,0);
                $this->SetXY(35,272);
                $this->Cell(15,4,"",0,0);
                $this->Cell(90,4,utf8_decode($this->modtras),0,0);
    
                $this->SetXY(70,256);
                $this->Cell(25,4,"",0,0);
                $this->Cell(5,4,"",0,0,"C");
                $this->SetXY(70,261);
                $this->Cell(25,4,"",0,1);
                $this->SetXY(70,263);
                $this->Cell(25,4,"",0,0);
                $this->Cell(5,4,"",0,0,"C");
                $this->SetXY(70,268);
                $this->Cell(25,4,"",0,0);
                $this->Cell(5,4,"",0,0,"C");
    
                $this->SetXY(105,256);
                $this->Cell(20,4,"",0,0);
                $this->Cell(5,4,"",0,0,"C");
                $this->SetXY(105,261);
                $this->Cell(20,4,"",0,0);
                $this->Cell(5,4,"",0,0,"C");
                $this->SetXY(105,267);
                $this->Cell(20,4,"",0,0);
                $this->Cell(5,4,"",0,0,"C");
    
                //$this->Line(145,250,145,280);
                //$this->SetXY(147,275);
                $this->Cell(51,6,"",0,0,"C");
    
                $this->SetXY(13,280); //detalle del documento
                $this->SetFont('Arial','',11);
                $this->Cell(187,6,"",0,0,"C");
            }
        }
    

?>