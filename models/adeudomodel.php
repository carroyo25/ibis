
<?php
    class AdeudoModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function buscarDatosAdeudo($doc,$cc) {
            $registrado = false;
            $url = "http://sicalsepcon.net/api/activesapi.php?documento=".$doc;
            $api = file_get_contents($url);
            
            $datos =  json_decode($api);
            $nreg = count($datos);

            $registrado = $nreg > 0 ? true: false;

            return array("datos" => $datos,
                        "registrado"=>$registrado,
                        "anteriores"=>$this->buscarAdeudo($doc,$cc));
        }

        private function buscarAdeudo($d,$c){
            try {
                $salida = "";
                $sql = $this->db->connect()->prepare("SELECT
                                                        alm_consumo.idreg,
                                                        alm_consumo.reguser,
                                                        alm_consumo.idprod,
                                                        alm_consumo.cantsalida,
                                                        alm_consumo.cantdevolucion,
                                                        DATE_FORMAT(alm_consumo.fechasalida,'%d/%m/%Y') AS fechasalida,
                                                        alm_consumo.nhoja,
                                                        alm_consumo.cisometrico,
                                                        alm_consumo.cobserentrega,
                                                        DATE_FORMAT(alm_consumo.fechadevolucion,'%d/%m/%Y') AS fechadevolucion,
                                                        alm_consumo.cobserdevuelto,
                                                        alm_consumo.cestado,
                                                        alm_consumo.ncondicion,
                                                        alm_consumo.flgdevolver,
                                                        alm_consumo.cfirma,
                                                        alm_consumo.reguserdevol,
                                                        alm_consumo.cserie,
                                                        alm_consumo.calmacen,
                                                        cm_producto.ccodprod,
                                                        UPPER(cm_producto.cdesprod) AS cdesprod,
                                                        tb_unimed.cabrevia 
                                                    FROM
                                                        alm_consumo
                                                        INNER JOIN cm_producto ON alm_consumo.idprod = cm_producto.id_cprod
                                                        INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed 
                                                    WHERE
                                                            alm_consumo.nrodoc = :documento 
                                                        AND alm_consumo.ncostos = :cc
                                                        AND alm_consumo.flgdevolver = 1
                                                    ORDER BY alm_consumo.freg DESC"
                                                    );
                $sql->execute(["documento"=>$d,"cc"=>$c]);
                $rowCount = $sql->rowCount();
                $item = 1;
                $salida ="No hay registros";

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()){

                        $marcado = $rs['flgdevolver'] == 1 ? "checked" : "";
                        $firma =   "public/documentos/firmas/".$rs['cfirma'].".png";
                        $almacen = "public/documentos/almacen/".$rs['calmacen'].".png";
                        $fecha = date("Y-m-d");

                        if ($rs['ncondicion'] == 1) {
                            $salida .= '<tr class="pointer" data-grabado="1" data-item="'.$rs['idreg'].'" data-condicion="'.$rs['ncondicion'].'">
                                        <td class="textoDerecha">'.str_pad($item++,3,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                        <td class="pl5px">'.$rs['cdesprod'].'</td>
                                        <td class="textoCentro">'.$rs['cabrevia'].'</td>
                                        <td class="textoDerecha">'.$rs['cantsalida'].'</td>
                                        <td class="textoDerecha" >'.$rs['cantdevolucion'].'</td>
                                        <td class="textoCentro">'.$rs['fechasalida'].'</td>
                                        <td class="textoCentro">'.$rs['fechadevolucion'].'</td>
                                        <td class="textoCentro">'.$rs['nhoja'].'</td>
                                        <td class="pl5px">'.$rs['cisometrico'].'</td>
                                        <td class="pl5px">'.$rs['cobserdevuelto'].'</td>
                                        <td class="pl5px">'.$rs['cserie'].'</td>
                                        <td class="textoCentro"><input type="checkbox" '.$marcado.' readonly></td>
                                        <td class="pl5px">'.$rs['cestado'].'</td>
                                        <td class="textoCentro">
                                            <div style ="width:110px !important; text-align:center">
                                                <img src = '.$firma.' style ="width:100% !important">
                                            </div>
                                        </td>
                                        <td class="textoCentro">
                                            <div style ="width:110px !important; text-align:center">
                                                <img src = '.$almacen.' style ="width:100% !important">
                                            </div>
                                        </td>
                                    </tr>';
                        }else {
                            $salida .= '<tr class="pointer" data-grabado="0" data-item="'.$rs['idreg'].'" data-condicion="'.$rs['ncondicion'].'">
                                        <td class="textoDerecha">'.str_pad($item++,3,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                        <td class="pl5px">'.$rs['cdesprod'].'</td>
                                        <td class="textoCentro">'.$rs['cabrevia'].'</td>
                                        <td class="textoDerecha">'.$rs['cantsalida'].'</td>
                                        <td ><input type="number" class="textoDerecha" value="'.$rs['cantdevolucion'].'"></td>
                                        <td class="textoCentro">'.$rs['fechasalida'].'</td>
                                        <td class="textoCentro"><input type="date" value="'.$fecha.'"></td>
                                        <td class="textoCentro">'.$rs['nhoja'].'</td>
                                        <td class="pl5px">'.$rs['cisometrico'].'</td>
                                        <td class="pl5px"><input type="text" value="'.$rs['cobserdevuelto'].'"></td>
                                        <td class="pl5px">'.$rs['cserie'].'</td>
                                        <td class="textoCentro"><input type="checkbox" '.$marcado.'></td>
                                        <td class="pl5px"><input type="text" value="'.$rs['cestado'].'"></td>
                                        <td class="textoCentro">
                                            <div style ="width:110px !important; text-align:center">
                                                <img src = '.$firma.' style ="width:100% !important">
                                            </div>
                                        </td>
                                        <td class="textoCentro">
                                            <div style ="width:110px !important; text-align:center">
                                                <img src = '.$almacen.' style ="width:100% !important">
                                            </div>
                                        </td>
                                    </tr>';
                        }
                    }
                }

                return $salida;

            }catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }  
        }

        public function buscarProductos($codigo){
            try {
                $salida = "";
                $sql = $this->db->connect()->prepare("SELECT
                                                        cm_producto.id_cprod,
                                                        cm_producto.ccodprod,
                                                        UPPER(cm_producto.cdesprod) AS cdesprod,
                                                        tb_unimed.cabrevia,
                                                        NOW() AS fecha
                                                    FROM
                                                        cm_producto
                                                        INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed 
                                                    WHERE
                                                        cm_producto.flgActivo = 1 
                                                        AND cm_producto.ccodprod = :codigo 
                                                        AND cm_producto.ntipo = 37");
                $sql->execute(["codigo"=>$codigo]);

                $rowCount = $sql->rowCount();
                $result = $sql->fetchAll();

                if ($rowCount > 0) {
                    $respuesta = array("descripcion"=>$result[0]['cdesprod'],
                                        "codigo"=>$result[0]['ccodprod'],
                                        "unidad"=>$result[0]['cabrevia'],
                                        "idprod"=>$result[0]['id_cprod'],
                                        "fecha"=>$result[0]['fecha'],
                                        "registrado"=>true);
                }else{
                    $respuesta = array("registrado"=>false); 
                }

                return $respuesta;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function subirFirmaAlmacen($detalles) {
            if (array_key_exists('img',$_REQUEST)) {
                // convierte la imagen recibida en base64
                // Eliminamos los 22 primeros caracteres, que 
                // contienen el substring "data:image/png;base64,"
                $imgData = base64_decode(substr($_REQUEST['img'],22));
            
                // Path en donde se va a guardar la imagen
                
                $fechaActual = date('Y-m-d');
                $respuesta = false;
        
                $namefile = uniqid();
        
                $file = 'public/documentos/almacen/'.$namefile.'.png';
            
                // borrar primero la imagen si existía previamente
                if (file_exists($file)) { unlink($file); }
            
                // guarda en el fichero la imagen contenida en $imgData
                $fp = fopen($file, 'w');
                fwrite($fp, $imgData);
                fclose($fp);
                
                if (file_exists($file)){
                    $respuesta = true;

                    $datos = json_decode($detalles);
                    $nreg = count($datos);
                    $kardex = $this->norepite();

                    for ($i=0; $i<$nreg; $i++){
                        if ($datos[$i]->devuelto > 0) {
                            $sql = $this->db->connect()->prepare("UPDATE alm_consumo 
                                                                    SET alm_consumo.cantdevolucion=:devolucion,
                                                                        alm_consumo.fechadevolucion=:salida,
                                                                        alm_consumo.cobserdevuelto=:observaciones,
                                                                        alm_consumo.cestado=:estado,
                                                                        alm_consumo.ncondicion = 1,
                                                                        alm_consumo.reguserdevol =:user,
                                                                        alm_consumo.calmacen =:firma
                                                                    WHERE idreg=:item
                                                                    LIMIT 1");

                            $sql->execute(["item"=>$datos[$i]->idreg,
                                            "devolucion"=>$datos[$i]->devuelto,
                                            "salida"=>$datos[$i]->fdevuelto,
                                            "observaciones"=>$datos[$i]->observac,
                                            "estado"=>$datos[$i]->estado,
                                            "firma"=>$namefile,
                                            "user"=>$_SESSION['iduser']]);
                        }
                       
                    }
                }      
            }
        
            return  $respuesta;
        }

        public function buscarConsumoPersonal($cod,$d,$cc){
            try {
                $salida = "";
                $sql = $this->db->connect()->prepare("SELECT
                                                        alm_consumo.idreg,
                                                        alm_consumo.reguser,
                                                        alm_consumo.idprod,
                                                        alm_consumo.cantsalida,
                                                        DATE_FORMAT(alm_consumo.fechasalida,'%d/%m/%Y') AS fechasalida,
                                                        alm_consumo.nhoja,
                                                        alm_consumo.cisometrico,
                                                        alm_consumo.cobserentrega,
                                                        alm_consumo.cobserdevuelto,
                                                        alm_consumo.cestado,
                                                        alm_consumo.flgdevolver,
                                                        alm_consumo.cfirma,
                                                        cm_producto.ccodprod,
                                                        cm_producto.cdesprod,
                                                        tb_unimed.cabrevia,
                                                        DATEDIFF(alm_consumo.fechasalida,NOW()) AS  dias_ultima_entrega
                                                    FROM
                                                        alm_consumo
                                                        INNER JOIN cm_producto ON alm_consumo.idprod = cm_producto.id_cprod
                                                        INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed 
                                                    WHERE
                                                        nrodoc = :documento 
                                                        AND ncostos = :cc
                                                        AND cm_producto.ccodprod =:codigo
                                                    ORDER BY alm_consumo.freg DESC");

                $sql->execute(["documento"=>$d,"cc"=>$cc,"codigo"=>$cod]);
                $rowCount = $sql->rowCount();
                $item = 1;
                $salida ="No hay registros";
                $numero_item = $this->cantidadItems($d,$cc);

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()){

                        $marcado = $rs['flgdevolver'] == 1 ? "checked" : "";
                        $firma = "public/documentos/firmas/".$rs['cfirma'].".png";

                        $alerta = $rs['dias_ultima_entrega'] < 15 ? "inactivo" : "";

                        $salida .= '<tr class="pointer" data-grabado="1">
                                        <td class="textoDerecha">'.$numero_item--.'</td>
                                        <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                        <td class="pl5px">'.$rs['cdesprod'].'</td>
                                        <td class="textoCentro">'.$rs['cabrevia'].'</td>
                                        <td class="textoDerecha">'.$rs['cantsalida'].'</td>
                                        <td class="textoCentro '.$alerta.'">'.$rs['fechasalida'].'</td>
                                        <td class="textoCentro">'.$rs['nhoja'].'</td>
                                        <td class="pl5px">'.$rs['cisometrico'].'</td>
                                        <td class="pl5px">'.$rs['cobserentrega'].'</td>
                                        <td class="textoCentro"><input type="checkbox" '.$marcado.'></td>
                                        <td class="pl5px">'.$rs['cestado'].'</td>
                                        <td class="textoCentro">
                                            <div style ="width:110px !important; text-align:center">
                                                <img src = '.$firma.' style ="width:100% !important">
                                            </div>
                                        </td>
                                        <td></td>
                                    </tr>';
                    }
                }

                return $salida;

            }catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }  
        }

        public function generarAdeudo($parametros){
            require_once("public/formatos/libreadeudo.php");

            $costo  = $parametros['cc'];
            $doc    = $parametros['doc'];
            $nombre = $parametros['nombre'];
            $almacen= "";
            $fecha = "";

            $file = uniqid();

            $pdf = new PDF($doc,$nombre,$almacen,$costo,$fecha);

            $pdf->AddPage();
            $pdf->AliasNbPages();
            $pdf->SetWidths(array(15,25,110,20,20));
            $pdf->SetFont('Arial','',5);

            $lc = 0;

            $detalle = $this->itemsAdeudo($costo,$doc);
            $nreg = count($detalle);
            $items_devueltos = 0;
            $estado = "PENDIENTE";

            for ($i=0; $i < $nreg; $i++) { 
                $estado = "PENDIENTE";
                
                if ( $detalle[$i]['ncondicion'] === 1) {
                    $estado = "DEVUELTO";
                    $items_devueltos++;
                }

                $pdf->SetAligns(array("C","C","L","C","R"));
                $pdf->Row(array(str_pad($lc++,3,0,STR_PAD_LEFT),
                                $detalle[$i]['ccodprod'],
                                $detalle[$i]['cdesprod'].' '.strtoupper($detalle[$i]['cserie']),
                                $estado,
                                $detalle[$i]['cantdevolucion']));
                    $lc++;

                    if ($pdf->getY() >= 185) {
                        $pdf->AddPage();
                        $lc = 0;
                    }
            }

            $pdf->ln(2);

            $pdf->SetFont('Arial','',8);
            $pdf->Cell(190,6,"Cantidad de items entregados: ".str_pad($i,2,0,STR_PAD_LEFT),0,1,'L');
            $pdf->Cell(190,6,"Cantidad de items devueltos :  ".str_pad($items_devueltos,2,0,STR_PAD_LEFT),0,1,'L');

            $pdf->SetFont('Arial','B',8);

            if ( $i === $items_devueltos ) {
                $pdf->Cell(15,6,"Estado :",'T',0,'L');
                $pdf->SetTextColor(0,118,184);
                $pdf->Cell(175,6,"Sin pendientes de entrega",'T',1,'L');
            }else {
                $pdf->Cell(15,6,"Estado :",'T',0,'L');
                $pdf->SetTextColor(252,65,54);
                $pdf->Cell(175,6,"Tiene pendientes de entrega",'T',1,'L');
            }

            $pdf->SetFont('Arial','',8);
            $pdf->SetTextColor(0,0,0);

            $filename = "public/documentos/adeudos/".$file;

            $pdf->Output($filename,'F');

            return $file;

        }

        private function itemsAdeudo($cc,$doc) {
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                        alm_consumo.idreg,
                                                        alm_consumo.reguser,
                                                        alm_consumo.nrodoc,
                                                        FORMAT(alm_consumo.cantdevolucion,2) AS cantdevolucion,
                                                        alm_consumo.fechasalida,
                                                        alm_consumo.calmacen,
                                                        alm_consumo.cfirma,
                                                        alm_consumo.cserie,
                                                        alm_consumo.flgdevolver,
                                                        cm_producto.ccodprod,
                                                        UPPER(cm_producto.cdesprod) AS cdesprod,
                                                        tb_unimed.cabrevia,
                                                        alm_consumo.ncostos,
                                                        alm_consumo.ncondicion,
                                                        alm_consumo.flgdevolver 
                                                    FROM
                                                        alm_consumo
                                                        INNER JOIN cm_producto ON alm_consumo.idprod = cm_producto.id_cprod
                                                        INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed 
                                                    WHERE
                                                        alm_consumo.nrodoc = :doc 
                                                        AND alm_consumo.ncostos = :cc 
                                                        AND alm_consumo.flgdevolver = 1");
                $sql->execute(["doc"=>$doc,"cc"=>$cc]);
                $result = $sql->fetchAll();

                return $result;

            }catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            } 
        }
    }
?>
