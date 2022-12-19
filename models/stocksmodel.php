<?php
    class StocksModel extends Model{

        public function __construct(){
            parent::__construct();
        }

        public function listarItems(){
            try {
                $salida = "";
                $sql = $this->db->connect()->query("SELECT
                                                        alm_existencia.idreg,
                                                        alm_existencia.idalm,
                                                        alm_existencia.idpedido,
                                                        alm_existencia.idorden,
                                                        alm_existencia.codprod,
                                                        alm_existencia.serie,
                                                        FORMAT(alm_existencia.cant_ingr,2) AS cantidad_ingreso,
                                                        alm_existencia.cant_sal,
                                                        cm_producto.ccodprod,
                                                        UPPER(cm_producto.cdesprod) AS descripcion,
                                                        alm_existencia.idprod,
                                                        tb_unimed.cabrevia,
                                                        SUM(alm_existencia.cant_ingr) AS ingreso	
                                                    FROM
                                                        alm_existencia
                                                        INNER JOIN cm_producto ON alm_existencia.codprod = cm_producto.id_cprod
                                                        INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed
                                                    GROUP BY alm_existencia.codprod
                                                    ORDER BY cm_producto.cdesprod");
                $sql->execute();
                $rowCount = $sql->rowCount();
                $item = 1;
                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()){
                        $salida.='<tr class="pointer" data-idprod="'.$rs['codprod'].'">
                                        <td class="textoCentro">'.str_pad($item++,4,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                        <td class="pl20px">'.$rs['descripcion'].'</td>
                                        <td class="textoCentro">'.$rs['cabrevia'].'</td>
                                        <td class="textoDerecha">'.$rs['cantidad_ingreso'].'</td>
                                        <td class="textoDerecha"></td>
                                        <td class="textoDerecha"></td>
                                  </tr>';
                    }
                }

                return $salida;

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage;
                return false;
            }
        }

        public function nuevoRegistro() {
            try {
                $sql = $this->db->connect()->query("SELECT MAX(idreg) AS numero FROM alm_cabexist");
                $sql->execute();

                $result = $sql->fetchAll();

                return array("numero"=>str_pad($result[0]['numero'],6,0,STR_PAD_LEFT));
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage;
                return false;
            }
        }

        public function grabarRegistro($cabecera,$detalles){
            try {
                $sql = $this->db->connect()->prepare("INSERT INTO alm_cabexist 
                                                    SET idcostos=:costo,ffechadoc=:fechadoc,ncodalm2=:almacen,ntipomov=:movimiento,
                                                        ffechaInv=:fechaInv");
                $sql->execute(["costo"=>$cabecera["codigo_costos"],
                                "fechadoc"=>$cabecera["fecha"],
                                "almacen"=>$cabecera["codigo_almacen"],
                                "movimiento"=>$cabecera["codigo_tipo"],
                                "fechaInv"=>$cabecera["fechaIngreso"]
                            ]);

                $rowCount = $sql->rowCount();

                if ($rowCount > 0){
                    $indice = $this->nuevoRegistro();
                    $this->grabarDetalles($detalles,$indice["numero"],$cabecera["codigo_tipo"],$cabecera["codigo_almacen"]);
                    $mensaje = "Registrado Correctamente";
                }
                else {
                    $mensaje = "Hubo un error en el registro";
                }

                return array("mensaje"=>$mensaje);
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function grabarDetalles($detalles,$indice,$movimiento,$almacen){
            try {
                $datos = json_decode($detalles);
                $nreg = count($datos);

                for ($i=0; $i < $nreg; $i++) {
                    $sql = $this->db->connect()->prepare("INSERT INTO alm_existencia 
                                                            SET idalm=:almacen,
                                                                idregistro=:indice,
                                                                codprod=:item,
                                                                cant_ingr=:cantidad,
                                                                observaciones=:observ,
                                                                ubicacion=:ubica,
                                                                ntipmov=:movimiento,
                                                                vence=:fecha_vence,
                                                                psi=:numpsi,
                                                                ncertcal=:certificado_calidad,
                                                                ffeccalibra=:fecha_calibracion,
                                                                ncertificado=:numero_certificado,
                                                                condicion=:condicion_item,
                                                                nroorden=:orden,
                                                                serie=:nroserie,
                                                                estado=:estado_item,
                                                                marca=:item_marca");
                                                            
                $sql->execute(["almacen"                =>$almacen, 
                                "indice"                =>$indice,
                                "item"                  =>$datos[$i]->idprod,
                                "cantidad"              =>$datos[$i]->cantidad,
                                "observ"                =>$datos[$i]->observaciones,
                                "ubica"                 =>$datos[$i]->contenedor.'-'.$datos[$i]->estante.'-'.$datos[$i]->fila,
                                "fecha_vence"           =>$datos[$i]->vence,
                                "movimiento"            =>$movimiento,
                                "numpsi"                =>$datos[$i]->psi,
                                "certificado_calidad"   =>$datos[$i]->ncertcal,
                                "fecha_calibracion"     =>$datos[$i]->feccal,
                                "numero_certificado"    =>$datos[$i]->ncert,
                                "condicion_item"        =>$datos[$i]->condicion,
                                "estado_item"           =>$datos[$i]->estado,
                                "nroserie"              =>$datos[$i]->serie,
                                "orden"                 =>$datos[$i]->orden,
                                "item_marca"            =>$datos[$i]->marca]);
                }
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function importFromXsl(){
            require_once('public/PHPExcel/PHPExcel.php');
            try {
                $temporal	 = $_FILES['fileUpload']['tmp_name'];

                $objPHPExcel = PHPExcel_IOFactory::load($temporal);
                $objHoja=$objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
                $fila = 1;
                $datos= "";

                foreach ($objHoja as $iIndice=>$objCelda) {
                    if ( $objCelda['B'] && $objCelda['B']!="CODIGO") {

                        $codigo = $this->establecerCodigos($objCelda['B']);
                        
                        $datos .='<tr data-grabado="0" data-idprod="'.$codigo['codigo'].'" data-codund="'.$codigo['unidad'].'" data-idx="-">
                                    <td class="textoCentro">'.str_pad($fila++,6,0,STR_PAD_LEFT).'</td>
                                    <td class="textoCentro">'.$objCelda['B'].'</td>
                                    <td class="pl20px">'.$objCelda['C'].'</td>
                                    <td class="textoCentro">'.$objCelda['D'].'</td>
                                    <td><input type="number" value="'.$objCelda['E'].'"></td>
                                    <td class="textoCentro"><input type="text" value="'.$objCelda['F'].'"></td>
                                    <td class="textoCentro"><input type="text" value="'.$objCelda['G'].'"></td>
                                    <td class="textoCentro"><input type="text" value="'.$objCelda['H'].'"></td>
                                    <td class="textoCentro"><input type="text" value="'.$objCelda['I'].'"></td>
                                    <td class="textoCentro"><input type="text" value="'.$objCelda['J'].'"></td>
                                    <td class="textoCentro"><input type="date" value="'.$objCelda['K'].'"></td>
                                    <td class="textoCentro"><input type="date" value="'.$objCelda['L'].'"></td>
                                    <td class="textoCentro"><input type="text" value="'.$objCelda['M'].'"></td>
                                    <td class="textoCentro"><input type="text" value="'.$objCelda['N'].'"></td>
                                    <td class="textoCentro"><input type="text" value="'.$objCelda['O'].'"></td>
                                    <td ><input type="text" class="textoCentro"  value="'.$objCelda['P'].'"></td>
                                    <td ><input type="text" class="textoCentro"  value="'.$objCelda['Q'].'"></td>
                                    <td ><input type="text" class="textoCentro"  value="'.$objCelda['R'].'"></td>
                                    <td><textarea>'.$objCelda['T'].'</textarea></td>
                                </tr>';
                    }
                }

                return array("datos"=>$datos);

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function establecerCodigos($codigo){
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                cm_producto.ccodprod,
                                                cm_producto.nund,
                                                cm_producto.id_cprod 
                                            FROM
                                                cm_producto 
                                            WHERE
                                                cm_producto.ccodprod = :codigo 
                                            LIMIT 1");
                $sql->execute(["codigo"=>$codigo]);

                $result = $sql->fetchAll();


                return array("codigo"=>$result[0]['id_cprod'],"unidad"=>$result[0]['nund']);

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function obtenerResumen($codigo){
            return  array("pedidos"=>$this->numeroPedidos($codigo),
                          "ordenes"=>$this->numeroOrdenes($codigo),
                          "inventario"=>$this->verIngresos($codigo,91),
                          "ingresos"=>$this->verIngresos($codigo,92),
                          "pendientes"=>$this->pendientesOC($codigo),
                          "precios"=>$this->listaPrecios($codigo),
                          "existencias"=>$this->listaExistencias($codigo));
        }

        private function numeroPedidos($codigo){
            try {
                $sql=$this->db->connect()->prepare("SELECT
                                                        COUNT( tb_pedidodet.idprod ) AS numero_pedidos 
                                                    FROM
                                                        tb_pedidodet 
                                                    WHERE
                                                        tb_pedidodet.idprod = :codigo 
                                                        AND tb_pedidodet.nflgActivo = 1 
                                                        AND tb_pedidodet.idpedido != 0");
                $sql->execute(["codigo"=>$codigo]);
                $result = $sql->fetchAll();

                return $result[0]['numero_pedidos'];

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function numeroOrdenes($codigo){
            try {
                $sql=$this->db->connect()->prepare("SELECT
                                                        COUNT( lg_ordendet.id_cprod ) AS numero_orden 
                                                    FROM
                                                        lg_ordendet 
                                                    WHERE
                                                        lg_ordendet.id_cprod = :codigo");
                $sql->execute(["codigo"=>$codigo]);
                $result = $sql->fetchAll();

                if ( empty($result[0]['numero_orden'] ) ) 
                    return 0;
                else
                    return $result[0]['numero_orden'];
                
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function verIngresos($codigo,$tipo){
            try {
                $sql=$this->db->connect()->prepare("SELECT
                                                    SUM( alm_existencia.cant_ingr ) AS ingresos 
                                                FROM
                                                    alm_existencia 
                                                WHERE
                                                    alm_existencia.codprod = :codigo
                                                    AND ntipmov =:movimiento");
                $sql->execute(["codigo"=>$codigo,"movimiento"=>$tipo]);
                $result = $sql->fetchAll();

                return $result[0]['ingresos'];
                
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function pendientesOC($codigo){
            try {
                $sql=$this->db->connect()->prepare("SELECT
                                                    SUM( lg_ordendet.ncanti ) AS cantidad_pendiente 
                                                FROM
                                                    lg_ordendet 
                                                WHERE
                                                    lg_ordendet.id_cprod = :codigo 
                                                    AND lg_ordendet.nEstadoReg = 60");
                $sql->execute(["codigo"=>$codigo]);
                $result = $sql->fetchAll();

                return $result[0]['cantidad_pendiente'];
                
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function listaPrecios($codigo){
            try {
                $salida = "";
                $sql=$this->db->connect()->prepare("SELECT
                                                        lg_ordendet.nunitario,
                                                        DATE_FORMAT( lg_ordencab.ffechadoc, '%d/%m/%Y' ) fecha,
                                                        tb_parametros.cabrevia,
                                                        lg_ordencab.ntcambio 
                                                    FROM
                                                        lg_ordendet
                                                        INNER JOIN lg_ordencab ON lg_ordendet.id_regmov = lg_ordencab.id_regmov
                                                        INNER JOIN tb_parametros ON lg_ordencab.ncodmon = tb_parametros.nidreg 
                                                    WHERE
                                                        lg_ordendet.id_cprod = :codigo 
                                                        AND lg_ordendet.id_orden IS NOT NULL");
                $sql->execute(["codigo"=>$codigo]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()){
                        $salida .='<tr class="pointer">
                                        <td class="textoCentro">'.$rs['fecha'].'</td>
                                        <td class="textoCentro">'.$rs['cabrevia'].'</td>
                                        <td class="textoDerecha">'.$rs['ntcambio'].'</td>
                                        <td class="textoDerecha">'.$rs['nunitario'].'</td>
                                    </tr>';
                    }
                }else {
                    $salida = '<tr class="textoCentro"><td colspan="4">Sin registros anteriores</td></tr>';
                }

                return $salida;

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function listaExistencias($codigo){
            try {
                $salida = "";
                $sql=$this->db->connect()->prepare("SELECT
                                                        FORMAT(alm_existencia.cant_ingr,2) AS cant_ingr,
                                                        UPPER( tb_almacen.cdesalm ) AS almacen,
                                                        tb_proyectos.ccodproy,
                                                        tb_unimed.cabrevia 
                                                    FROM
                                                        alm_existencia
                                                        INNER JOIN alm_cabexist ON alm_existencia.idregistro = alm_cabexist.idreg
                                                        INNER JOIN tb_almacen ON alm_existencia.idalm = tb_almacen.ncodalm
                                                        INNER JOIN tb_proyectos ON alm_cabexist.idcostos = tb_proyectos.nidreg
                                                        INNER JOIN cm_producto ON alm_existencia.codprod = cm_producto.id_cprod
                                                        INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed 
                                                    WHERE
                                                        alm_existencia.codprod = :codigo");
                $sql->execute(["codigo"=>$codigo]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()){
                        $salida .='<tr class="pointer">
                                        <td class="pl20px">'.$rs['ccodproy'].'</td>
                                        <td class="textoCentro">'.$rs['cabrevia'].'</td>
                                        <td class="textoDerecha">'.$rs['cant_ingr'].'</td>
                                        <td class="textoDerecha"></td>
                                        <td class="textoDerecha">'.$rs['cant_ingr'].'</td>
                                        <td class="textoDerecha">'.$rs['almacen'].'</td>
                                    </tr>';
                    }
                }else {
                    $salida = '<tr class="textoCentro"><td colspan="4">Sin registros anteriores</td></tr>';
                }

                return $salida;

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }
    }
?>