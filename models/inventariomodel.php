<?php
    class InventarioModel extends Model{

        public function __construct(){
            parent::__construct();
        }

        public function listarEntradas(){
            try {
                $salida = "";
                $sql = $this->db->connect()->query("SELECT
                                        tb_proyectos.cdesproy,
                                        alm_inventariocab.idreg,
                                        DATE_FORMAT( alm_inventariocab.ffechadoc, '%d/%m%/%Y' ) AS fecha_documento,
                                        DATE_FORMAT( alm_inventariocab.ffechaInv, '%d/%m%/%Y' ) AS fecha_inventario,
                                        tb_user.cnombres,
                                        UPPER( tb_almacen.cdesalm ) AS almacen,
                                        tb_proyectos.ccodproy 
                                    FROM
                                        alm_inventariocab
                                        INNER JOIN tb_proyectos ON alm_inventariocab.idcostos = tb_proyectos.nidreg
                                        INNER JOIN tb_user ON alm_inventariocab.idautoriza = tb_user.iduser
                                        INNER JOIN tb_almacen ON alm_inventariocab.ncodalm2 = tb_almacen.ncodalm");
                                        $sql->execute();
                $rowCount = $sql->rowCount();
                $item = 1;
                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()){
                        $salida.='<tr class="pointer" data-doc="'.$rs['idreg'].'">
                                        <td class="textoCentro">'.str_pad($rs['idreg'],4,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.$rs['fecha_documento'].'</td>
                                        <td class="textoCentro">'.$rs['fecha_inventario'].'</td>
                                        <td class="pl20px">'.$rs['cnombres'].'</td>
                                        <td class="pl20px">'.$rs['almacen'].'</td>
                                        <td class="pl20px">'.$rs['ccodproy'].'</td>
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
                $sql = $this->db->connect()->query("SELECT COUNT(idreg) AS numero FROM alm_inventariocab");
                $sql->execute();

                $result = $sql->fetchAll();

                $numero = $result[0]['numero'] != 0 ? $result[0]['numero'] : 1;

                return array("numero"=>str_pad($numero+1,6,0,STR_PAD_LEFT),"indice"=>$numero);
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function grabarRegistro($cabecera,$detalles){
            try {
                $sql = $this->db->connect()->prepare("INSERT INTO alm_inventariocab 
                                                    SET idcostos=:costo,ffechadoc=:fechadoc,ncodalm2=:almacen,ntipomov=:movimiento,
                                                        ffechaInv=:fechaInv,idautoriza=:autoriza");
                $sql->execute(["costo"=>$cabecera["codigo_costos"],
                                "fechadoc"=>$cabecera["fecha"],
                                "almacen"=>$cabecera["codigo_almacen"],
                                "movimiento"=>$cabecera["codigo_tipo"],
                                "fechaInv"=>$cabecera["fechaIngreso"],
                                "autoriza"=>$cabecera["codigo_autoriza"]
                            ]);

                $rowCount = $sql->rowCount();

                if ($rowCount > 0){
                    $indice = $this->nuevoRegistro();
                    $this->grabarDetalles($detalles,$indice["indice"],$cabecera["codigo_tipo"],$cabecera["codigo_almacen"]);
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

        public function actualizarInventario($detalles){
            try {
                $datos = json_decode($detalles);
                $nreg = count($datos);

                for ($i=0; $i < $nreg ; $i++) { 
                    $sql = $this->db->connect()->prepare("UPDATE alm_inventariodet 
                                                            SET codprod=:item,
                                                                cmarca=:item_marca,
                                                                cant_ingr=:item_cantidad,
                                                                nroorden=:item_nro_orden,
                                                                ncolada=:item_colada,
                                                                ntag=:item_tag,
                                                                cserie=:item_serie,
                                                                ncertificado=:item_ncertificado,
                                                                ffeccalibra=:item_calibra,
                                                                vence=:item_vence,
                                                                nreglib=:item_reglib,
                                                                estado=:item_estado,
                                                                condicion=:item_condicion,
                                                                ccontenedor=:item_contenedor,
                                                                cestante=:item_estante,
                                                                cfila=:item_fila,
                                                                observaciones=:item_observaciones
                                                            WHERE idreg =:item_registro");

                    $sql->execute([ "item"                  =>$datos[$i]->idprod,
                                    "item_marca"            =>$datos[$i]->marca,
                                    "item_cantidad"         =>$datos[$i]->cantidad,
                                    "item_nro_orden"        =>$datos[$i]->orden,
                                    "item_colada"           =>$datos[$i]->colada,
                                    "item_tag"              =>$datos[$i]->tag,
                                    "item_serie"            =>$datos[$i]->serie,
                                    "item_ncertificado"     =>$datos[$i]->ncertcal,
                                    "item_calibra"          =>$datos[$i]->feccal,
                                    "item_vence"            =>$datos[$i]->vence,
                                    "item_reglib"           =>$datos[$i]->reglib,
                                    "item_estado"           =>$datos[$i]->estado,
                                    "item_condicion"        =>$datos[$i]->condicion,
                                    "item_contenedor"       =>$datos[$i]->contenedor,
                                    "item_estante"          =>$datos[$i]->estante,
                                    "item_fila"             =>$datos[$i]->fila,
                                    "item_observaciones"    =>$datos[$i]->observaciones,
                                    "item_registro"         =>$datos[$i]->idreg]);
                }
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
                    $sql = $this->db->connect()->prepare("INSERT INTO alm_inventariodet 
                                                            SET idalm=:almacen,
                                                                idregistro=:indice,
                                                                codprod=:item,
                                                                cmarca=:item_marca,
                                                                cant_ingr=:item_cantidad,
                                                                nroorden=:item_nro_orden,
                                                                ncolada=:item_colada,
                                                                ntag=:item_tag,
                                                                cserie=:item_serie,
                                                                ncertificado=:item_ncertificado,
                                                                ffeccalibra=:item_calibra,
                                                                vence=:item_vence,
                                                                nreglib=:item_reglib,
                                                                estado=:item_estado,
                                                                condicion=:item_condicion,
                                                                ccontenedor=:item_contenedor,
                                                                cestante=:item_estante,
                                                                cfila=:item_fila,
                                                                observaciones=:item_observaciones");
                                                            
                $sql->execute(["almacen"                =>$almacen, 
                                "indice"                =>$indice,
                                "item"                  =>$datos[$i]->idprod,
                                "item_marca"            =>$datos[$i]->marca,
                                "item_cantidad"         =>$datos[$i]->cantidad,
                                "item_nro_orden"        =>$datos[$i]->orden,
                                "item_colada"           =>$datos[$i]->colada,
                                "item_tag"              =>$datos[$i]->tag,
                                "item_serie"            =>$datos[$i]->serie,
                                "item_ncertificado"     =>$datos[$i]->ncertcal,
                                "item_calibra"          =>$datos[$i]->feccal,
                                "item_vence"            =>$datos[$i]->vence,
                                "item_reglib"           =>$datos[$i]->reglib,
                                "item_estado"           =>$datos[$i]->estado,
                                "item_condicion"        =>$datos[$i]->condicion,
                                "item_contenedor"       =>$datos[$i]->contenedor,
                                "item_estante"          =>$datos[$i]->estante,
                                "item_fila"             =>$datos[$i]->fila,
                                "item_observaciones"    =>$datos[$i]->observaciones]);
                }
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function importFromXsl(){
            require_once('public/PHPExcel/PHPExcel.php');

            $archivo = './public/documentos/temp/temp.xlsx';
            $temporal	 = $_FILES['fileUpload']['tmp_name'];


            if (move_uploaded_file($temporal,$archivo)){
                $mensaje = "El archivo ha sido cargado correctamente.";

                $objPHPExcel = PHPExcel_IOFactory::load($archivo);
                $objHoja=$objPHPExcel->getActiveSheet()->toArray(null,true,true,true);

                $fila = 1;
                $datos= "";

                foreach ($objHoja as $iIndice=>$objCelda) {
                    if ( $objCelda['B'] && $objCelda['B']!="CODIGO") {

                        $codigo_sical = $this->compareCode(RTRIM($objCelda['B']));

                        if ( $codigo_sical == 0 ){
                            $codigo_sical = $this->compareDescription(TRIM($objCelda['C']));
                        }

                        $estado      = $codigo_sical  != 0 ? 1 : 0;
                        $fondo_fila  = $codigo_sical  != 0 ? "rgba(56,132,192,0.2)" : "rgba(255,0,57,0.2)";
                        $descripcion = $codigo_sical  != 0 ? $objCelda['C'] : '<a href="#">'.$objCelda['C'].'</a>';
                        $observaciones =  $codigo_sical  != 0 ? $objCelda['T']: $objCelda['C'];

                       $datos .='<tr data-grabado="0" 
                                        data-idprod="'.$codigo_sical.'" 
                                        data-codund="" 
                                        data-idx="-" 
                                        data-estado="'.$estado.'"
                                        data-registro="-"
                                        style = background:'.$fondo_fila.'>
                                    <td class="textoCentro">'.str_pad($fila++,6,0,STR_PAD_LEFT).'</td>
                                    <td class="textoCentro">'.$objCelda['B'].'</td>
                                    <td class="pl20px">'.$descripcion.'</td>
                                    <td class="textoCentro">'.$objCelda['D'].'</td>
                                    <td><input type="number" value="'.$objCelda['E'].'"></td>
                                    <td><input type="number" value="'.$objCelda['F'].'"></td>
                                    <td class="textoCentro"><input type="text" value="'.$objCelda['G'].'"></td>
                                    <td class="textoCentro"><input type="text" value="'.$objCelda['H'].'"></td>
                                    <td class="textoCentro"><input type="text" value="'.$objCelda['I'].'"></td>
                                    <td class="textoCentro"><input type="text" value="'.$objCelda['J'].'"></td>
                                    <td class="textoCentro"><input type="text" value="'.$objCelda['K'].'"></td>
                                    <td class="textoCentro"><input type="date" value="'.$objCelda['L'].'"></td>
                                    <td class="textoCentro"><input type="date" value="'.$objCelda['M'].'"></td>
                                    <td class="textoCentro"><input type="text" value="'.$objCelda['N'].'"></td>
                                    <td class="textoCentro"><input type="text" value="'.$objCelda['O'].'"></td>
                                    <td class="textoCentro"><input type="text"   value="'.$objCelda['P'].'"></td>
                                    <td ><input type="text" class="textoCentro"  value="'.$objCelda['Q'].'"></td>
                                    <td ><input type="text" class="textoCentro"  value="'.$objCelda['R'].'"></td>
                                    <td ><input type="text" class="textoCentro"  value="'.$objCelda['S'].'"></td>
                                    <td><textarea>'.$observaciones.'</textarea></td>
                                </tr>';
                    }
                }
            }else{
                $mensaje = "Ocurrió algún error al subir el fichero. No pudo guardarse.";
            }

            return array("datos"=>$datos);

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

                if(gettype($result) == NULL)
                    return array("codigo"=>"X","unidad"=>"X");
                else    
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

        private function compareCode($codigo){
            try {
                $sql=$this->db->connect()->prepare("SELECT
                                                       cm_producto.id_cprod AS codigo 
                                                    FROM
                                                        cm_producto 
                                                    WHERE
                                                        cm_producto.ccodprod = :codigo");
                $sql->execute(["codigo" => $codigo]);

                $result = $sql->fetchAll();

                $codigo = isset($result[0]['codigo'])  ? $result[0]['codigo'] : 0;

                return $codigo;

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function compareDescription($descripcion) {
            try {
                //$valor = strtok($descripcion);
                
                $sql=$this->db->connect()->prepare("SELECT
                                                        cm_producto.id_cprod AS codigo 
                                                    FROM
                                                        cm_producto 
                                                    WHERE
                                                        cm_producto.cdesprod LIKE :descripcion
                                                    LIMIT 1");
                $sql->execute(["descripcion" => "%".$descripcion."%"]);

                $result = $sql->fetchAll();

                $codigo = isset($result[0]['codigo'])  ? $result[0]['codigo'] : 0;

                return $codigo;

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function consultarInventario($idx){
            try {
                    $sql = $this->db->connect()->prepare("SELECT
                                                        tb_proyectos.cdesproy,
                                                        alm_inventariocab.idreg,
                                                        alm_inventariocab.ffechadoc,
                                                        alm_inventariocab.ffechaInv,
                                                        tb_user.cnombres,
                                                        UPPER( tb_almacen.cdesalm ) AS almacen,
                                                        tb_proyectos.ccodproy,
                                                        alm_inventariocab.idcostos,
                                                        alm_inventariocab.ncodalm2,
                                                        tb_user.iduser,
                                                        tb_almacen.ncodalm,
                                                        alm_inventariocab.ntipomov,
                                                        tb_parametros.cdescripcion 
                                                    FROM
                                                        alm_inventariocab
                                                        INNER JOIN tb_proyectos ON alm_inventariocab.idcostos = tb_proyectos.nidreg
                                                        INNER JOIN tb_user ON alm_inventariocab.idautoriza = tb_user.iduser
                                                        INNER JOIN tb_almacen ON alm_inventariocab.ncodalm2 = tb_almacen.ncodalm
                                                        INNER JOIN tb_parametros ON alm_inventariocab.ntipomov = tb_parametros.nidreg 
                                                    WHERE
                                                        alm_inventariocab.idreg = :idx");
                    $sql->execute(["idx" => $idx]);

                    $rowCount = $sql->rowcount();

                    if ($rowCount > 0) {
                        $docData = array();
                        while($row=$sql->fetch(PDO::FETCH_ASSOC)){
                            $docData[] = $row;
                        }
                    }

                    return array("cabecera" => $docData,
                                "detalles" =>$this->detallesInventario($idx));
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
           
        }

        private function detallesInventario($idx){
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                        alm_inventariodet.idreg,
                                                        alm_inventariodet.cant_ingr,
                                                        alm_inventariodet.nroorden,
                                                        alm_inventariodet.ncertcal,
                                                        alm_inventariodet.ffeccalibra,
                                                        alm_inventariodet.ncertificado,
                                                        alm_inventariodet.condicion,
                                                        alm_inventariodet.cmarca,
                                                        alm_inventariodet.estado,
                                                        alm_inventariodet.ncolada,
                                                        alm_inventariodet.ntag,
                                                        alm_inventariodet.cserie,
                                                        alm_inventariodet.ccontenedor,
                                                        alm_inventariodet.cestante,
                                                        alm_inventariodet.cfila,
                                                        alm_inventariodet.nreglib,
                                                        alm_inventariodet.cestado,
                                                        alm_inventariodet.codprod,
                                                        cm_producto.cdesprod,
                                                        cm_producto.ccodprod,
                                                        tb_unimed.cabrevia,
                                                        alm_inventariodet.idregistro,
                                                        alm_inventariodet.vence,
                                                        alm_inventariodet.observaciones
                                                    FROM
                                                        alm_inventariodet
                                                        LEFT JOIN cm_producto ON alm_inventariodet.codprod = cm_producto.id_cprod
                                                        LEFT JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed 
                                                    WHERE
                                                        alm_inventariodet.idregistro =:idx");
                $sql->execute(["idx" => $idx]);

                $rowCount = $sql->rowcount();
                $salida = "";
                $item = 1;

                if ($rowCount > 0) {
                    while($rs = $sql->fetch()){

                        $descripcion = is_null($rs['ccodprod']) ? '<a href="#">'.$rs['observaciones'].'</a>' : $rs['cdesprod'];
                        $fondo_fila  = is_null($rs['ccodprod']) ? "rgba(255,0,57,0.2)" : "rgba(56,132,192,0.2)";

                        $salida .= '<tr class="pointer" style=background:'.$fondo_fila.' 
                                        data-grabado="1" 
                                        data-idprod="'.$rs['codprod'].'"
                                        data-registro="'.$rs['idreg'].'">
                                        <td class="textoCentro">'.$item++.'</td>
                                        <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                        <td class="pl20px">'.$descripcion.'</td>
                                        <td class="textoCentro"> <input type="text" value="'.$rs['cabrevia'].'"</td>
                                        <td class="textoCentro"> <input type="text" value="'.$rs['cmarca'].'"</td>
                                        <td class="textoDerecha"> <input type="number" value="'.$rs['cant_ingr'].'"</td>
                                        <td class="textoCentro"> <input type="number" value="'.$rs['nroorden'].'"</td>
                                        <td class="textoCentro"> <input type="text" value="'.$rs['ncolada'].'"</td>
                                        <td class="textoCentro"> <input type="text" value="'.$rs['ntag'].'"</td>
                                        <td class="textoCentro"> <input type="text" value="'.$rs['cserie'].'"</td>
                                        <td class="textoCentro"> <input type="text" value="'.$rs['ncertificado'].'"</td>
                                        <td class="textoCentro"> <input type="date" value="'.$rs['ffeccalibra'].'"</td>
                                        <td class="textoCentro"> <input type="date" value="'.$rs['vence'].'"</td>
                                        <td class="textoCentro"> <input type="number" value="'.$rs['nreglib'].'"</td>
                                        <td class="textoCentro"> <input type="text" value="'.$rs['cestado'].'"</td>
                                        <td class="textoCentro"> <input type="text" value="'.$rs['condicion'].'"</td>
                                        <td class="textoCentro"> <input type="text" value="'.$rs['ccontenedor'].'"</td>
                                        <td class="textoCentro"> <input type="text" value="'.$rs['cestante'].'"</td>
                                        <td class="textoCentro"> <input type="text" value="'.$rs['cfila'].'"</td>
                                        <td><textarea>'.$rs['observaciones'].'</textarea></td>
                                    </tr>';
                    }
                }

                return $salida;

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function exportar($detalles){
            require_once('public/PHPExcel/PHPExcel.php');
            try {

                $datos = json_decode($detalles);
                $nreg = count($datos);

                $objPHPExcel = new PHPExcel();
                $objPHPExcel->getProperties()
                    ->setCreator("Sical")
                    ->setLastModifiedBy("Sical")
                    ->setTitle("Registro Inventario")
                    ->setSubject("Template excel")
                    ->setDescription("Registro Inventario")
                    ->setKeywords("Template excel");

                $cuerpo = array(
                    'font'  => array(
                    'bold'  => false,
                    'size'  => 7,
                ));

                $objWorkSheet = $objPHPExcel->createSheet(1);

                $objPHPExcel->setActiveSheetIndex(0);
                $objPHPExcel->getActiveSheet()->setTitle("Registro Inventario");

                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');

                $objPHPExcel->getActiveSheet()->mergeCells('A1:T1');
                $objPHPExcel->getActiveSheet()->setCellValue('A1','Registro de Inventario -- Productos sin códigos');

                $objPHPExcel->getActiveSheet()->getStyle('A1:T2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('A1:T2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $objPHPExcel->getActiveSheet()->getStyle('A2:T2')->getAlignment()->setWrapText(true);

                $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(12);
                $objPHPExcel->getActiveSheet()->getColumnDimension("B")->setWidth(12);
                $objPHPExcel->getActiveSheet()->getColumnDimension("C")->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension("D")->setWidth(24);
                $objPHPExcel->getActiveSheet()->getColumnDimension("E")->setWidth(12);;
                $objPHPExcel->getActiveSheet()->getColumnDimension("F")->setWidth(12);;
                $objPHPExcel->getActiveSheet()->getColumnDimension("G")->setWidth(12);;
                $objPHPExcel->getActiveSheet()->getColumnDimension("H")->setWidth(12);;
                $objPHPExcel->getActiveSheet()->getColumnDimension("I")->setWidth(12);;
                $objPHPExcel->getActiveSheet()->getColumnDimension("J")->setWidth(12);;
                $objPHPExcel->getActiveSheet()->getColumnDimension("K")->setWidth(12);;
                $objPHPExcel->getActiveSheet()->getColumnDimension("L")->setWidth(12);;
                $objPHPExcel->getActiveSheet()->getColumnDimension("M")->setWidth(12);;
                $objPHPExcel->getActiveSheet()->getColumnDimension("N")->setWidth(12);;
                $objPHPExcel->getActiveSheet()->getColumnDimension("O")->setWidth(12);;
                $objPHPExcel->getActiveSheet()->getColumnDimension("P")->setWidth(12);;
                $objPHPExcel->getActiveSheet()->getColumnDimension("Q")->setWidth(12);;
                $objPHPExcel->getActiveSheet()->getColumnDimension("R")->setWidth(12);;
                $objPHPExcel->getActiveSheet()->getColumnDimension("S")->setWidth(12);;
                $objPHPExcel->getActiveSheet()->getColumnDimension("T")->setAutoSize(true);
                 
                $objPHPExcel->getActiveSheet()
                            ->getStyle('A2:T2')
                            ->getFill()
                            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB('BFCDDB');

                $objPHPExcel->getActiveSheet()->setCellValue('A2','Item'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('B2','Código'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('C2','Descripción'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('D2','Unidad'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('E2','Marca'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('F2','Cantidad'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('G2','Orden'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('H2','N° Colada Lote'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('I2','N° TAG'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('J2','Serie'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('K2','N° Cert. Calidad'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('L2','Fecha Calibración'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('M2','Fecha Vencimiento'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('N2','N°. Registro Liberacion'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('O2','Estado'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('P2','Condicion'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('Q2','Contenedor'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('R2','Estante'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('S2','Fila/Col'); // esto cambia
                $objPHPExcel->getActiveSheet()->setCellValue('T2','Observaciones'); // esto cambia

                $fila = 3;
                $item = 1;

                for ($i=0; $i < $nreg; $i++) {
                    if ($datos[$i]->idprod == 0){
                        $objPHPExcel->getActiveSheet()->setCellValue('A'.$fila,str_pad($item++,3,0,STR_PAD_LEFT));
                        $objPHPExcel->getActiveSheet()->setCellValue('B'.$fila,$datos[$i]->codigo);
                        $objPHPExcel->getActiveSheet()->setCellValue('C'.$fila,$datos[$i]->descripcion);
                        $objPHPExcel->getActiveSheet()->setCellValue('D'.$fila,$datos[$i]->unidad);
                        $objPHPExcel->getActiveSheet()->setCellValue('E'.$fila,$datos[$i]->marca);
                        $objPHPExcel->getActiveSheet()->setCellValue('F'.$fila,$datos[$i]->cantidad);
                        $objPHPExcel->getActiveSheet()->setCellValue('G'.$fila,$datos[$i]->orden);
                        $objPHPExcel->getActiveSheet()->setCellValue('H'.$fila,$datos[$i]->colada);
                        $objPHPExcel->getActiveSheet()->setCellValue('I'.$fila,$datos[$i]->tag);
                        $objPHPExcel->getActiveSheet()->setCellValue('J'.$fila,$datos[$i]->serie);
                        $objPHPExcel->getActiveSheet()->setCellValue('K'.$fila,$datos[$i]->ncertcal);
                        $objPHPExcel->getActiveSheet()->setCellValue('L'.$fila,$datos[$i]->feccal);
                        $objPHPExcel->getActiveSheet()->setCellValue('M'.$fila,$datos[$i]->vence);
                        $objPHPExcel->getActiveSheet()->setCellValue('N'.$fila,$datos[$i]->reglib);
                        $objPHPExcel->getActiveSheet()->setCellValue('O'.$fila,$datos[$i]->estado);
                        $objPHPExcel->getActiveSheet()->setCellValue('P'.$fila,$datos[$i]->condicion);
                        $objPHPExcel->getActiveSheet()->setCellValue('Q'.$fila,$datos[$i]->contenedor);
                        $objPHPExcel->getActiveSheet()->setCellValue('R'.$fila,$datos[$i]->estante);
                        $objPHPExcel->getActiveSheet()->setCellValue('S'.$fila,$datos[$i]->fila);
                        $objPHPExcel->getActiveSheet()->setCellValue('T'.$fila,$datos[$i]->observaciones);

                        $fila++;
                    } 
                }
                
                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');
                $objWriter->save('public/documentos/reportes/inventario.xlsx');

                return array("documento"=>'public/documentos/reportes/inventario.xlsx');

                exit();

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }
    }
?>