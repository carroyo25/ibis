<?php
    class AjustesModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function importFromXslAjustes(){
            require_once "public/PHPExcel/PHPExcel/IOFactory.php";
    
            $archivo  = './public/documentos/temp/temp.xlsx';
            $temporal = $_FILES['fileUpload']['tmp_name'];
            $datos    = "";
            $nombre = $_FILES['fileUpload']['name'];
    
            if ( !$this->buscarArchivoProcesado($nombre) ){
                if ( move_uploaded_file($temporal,$archivo) ) {
                    $document = PHPExcel_IOFactory::load($archivo);
                    $activeSheetData = $document->getActiveSheet()->toArray(null, true, true, true);
                    
                    $rows = 8;
                    $nregs = count($activeSheetData);
                    $fila = 1;
    
                    for ($i=8; $i < 300; $i++) { 
                        if ( $activeSheetData[$i]["B"] && $activeSheetData[$i]["B"]!="CODIGO"){

                            $codigo_sical = $this->compareCode(RTRIM($activeSheetData[$i]["B"]));
                            $estado = 1;

                            if ( $codigo_sical == 0 ){
                                $codigo_sical = 0;
                            }
                            
                            $fondo_fila     = $codigo_sical  != 0 ? "rgba(56,132,192,0.2)" : "rgba(255,0,57,0.2)";
                            $descripcion    = $codigo_sical  != 0 ? $activeSheetData[$i]["C"] : '<a href="#">'.$activeSheetData[$i]["C"].'</a>';
                            $observaciones  =  $codigo_sical  != 0 ? $activeSheetData[$i]["U"]: $activeSheetData[$i]["C"];
    
                            $datos .='<tr data-grabado="0" 
                                            data-idprod="'.$codigo_sical.'" 
                                            data-codund="" 
                                            data-idx="-" 
                                            data-estado="'.$estado.'"
                                            data-registro="-"
                                            style = background:'.$fondo_fila.'>
                                        <td class="textoCentro">'.str_pad($fila++,6,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.$activeSheetData[$i]["B"].'</td>
                                        <td class="pl20px">'.$descripcion.'</td>
                                        <td class="textoCentro">'.$activeSheetData[$i]["D"].'</td>
                                        <td><input type="text" value="'.$activeSheetData[$i]["E"].'"></td>
                                        <td><input type="number" value="'.$activeSheetData[$i]["F"].'"></td>
                                        <td class="textoCentro"><input type="text" value="'.$activeSheetData[$i]["G"].'"></td>
                                        <td class="textoCentro"><input type="text" value="'.$activeSheetData[$i]["H"].'"></td>
                                        <td class="textoCentro"><input type="text" value="'.$activeSheetData[$i]["I"].'"></td>
                                        <td class="textoCentro"><input type="text" value="'.$activeSheetData[$i]["J"].'"></td>
                                        <td class="textoCentro"><input type="date" value="'.$activeSheetData[$i]["K"].'"></td>
                                        <td class="textoCentro"><input type="date" value="'.$activeSheetData[$i]["L"].'"></td>
                                        <td class="textoCentro"><input type="text" value="'.$activeSheetData[$i]["M"].'"></td>
                                        <td class="textoCentro"><input type="text" value="'.$activeSheetData[$i]["N"].'"></td>
                                        <td class="textoCentro"><input type="text"   value="'.$activeSheetData[$i]["O"].'"></td>
                                        <td ><input type="text" class="textoCentro"  value="'.$activeSheetData[$i]["P"].'"></td>
                                        <td ><input type="text" class="textoCentro"  value="'.$activeSheetData[$i]["Q"].'"></td>
                                        <td ><input type="text" class="textoCentro"  value="'.$activeSheetData[$i]["R"].'"></td>
                                        <td ><input type="text" class="textoCentro"  value="'.$activeSheetData[$i]["S"].'"></td>
                                        <td><textarea>'.$observaciones.'</textarea></td>
                                    </tr>';
                        }
                    }
                    $mensaje = "Items procesados --- ".$fila;
                }else {
                    $mensaje = "Ocurrió algún error al subir el fichero. No pudo guardarse.";
                }
            }else {
                $mensaje = "El Archivo ya fue procesado";
            }
    
            return array("datos"=>$datos, 
                        "mensaje"=>$mensaje);
        }
    
        public function buscarArchivoProcesado($archivo){
            try {
                $sql = $this->db->connect()->prepare("SELECT COUNT(alm_ajustecab.idreg) AS archivos FROM  alm_ajustecab 
                                                    WHERE alm_ajustecab.observaciones=:archivo");
                $sql->execute(["archivo"=>$archivo]);
                $result = $sql->fetchAll();
    
                return $result[0]['archivos'];
    
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function grabarRegistroAjustes($cabecera,$detalles){
            try {
                $sql = $this->db->connect()->prepare("INSERT INTO alm_ajustecab 
                                                    SET idcostos=:costo,ffechadoc=:fechadoc,ncodalm2=:almacen,ntipomov=:movimiento,
                                                        ffechaInv=:fechaInv,idautoriza=:autoriza,observaciones=:archivo");
                $sql->execute(["costo"=>$cabecera["codigo_costos"],
                                "fechadoc"=>$cabecera["fecha"],
                                "almacen"=>$cabecera["codigo_almacen"],
                                "movimiento"=>$cabecera["codigo_tipo"],
                                "fechaInv"=>$cabecera["fechaIngreso"],
                                "autoriza"=>$cabecera["codigo_autoriza"],
                                "archivo"=>$cabecera["archivo"]
                            ]);

                $rowCount = $sql->rowCount();

                if ($rowCount > 0){
                    $indice = $this->nuevoRegistro();
                    $this->grabarDetallesAjustes($detalles,$indice["indice"],$cabecera["codigo_tipo"],$cabecera["codigo_almacen"]);
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

        private function grabarDetallesAjustes($detalles,$indice,$movimiento,$almacen){
            try {
                $datos = json_decode($detalles);
                $nreg = count($datos);


                for ($i=0; $i < $nreg; $i++) {
                    $sql = $this->db->connect()->prepare("INSERT INTO alm_ajustedet 
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

        public function nuevoRegistro() {
            try {
                $sql = $this->db->connect()->query("SELECT COUNT(idreg) AS numero FROM alm_ajustecab");
                $sql->execute();

                $result = $sql->fetchAll();

                $numero = $result[0]['numero'] != 0 ? $result[0]['numero'] : 1;

                return array("numero"=>str_pad($numero+1,6,0,STR_PAD_LEFT),"indice"=>$numero);
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function consultarAjuste($idx){
            try {
                    $sql = $this->db->connect()->prepare("SELECT
                                                        tb_proyectos.cdesproy,
                                                        alm_ajustecab.idreg,
                                                        alm_ajustecab.ffechadoc,
                                                        alm_ajustecab.ffechaInv,
                                                        tb_user.cnombres,
                                                        UPPER( tb_almacen.cdesalm ) AS almacen,
                                                        tb_proyectos.ccodproy,
                                                        alm_ajustecab.idcostos,
                                                        alm_ajustecab.ncodalm2,
                                                        tb_user.iduser,
                                                        tb_almacen.ncodalm,
                                                        alm_ajustecab.ntipomov,
                                                        tb_parametros.cdescripcion 
                                                    FROM
                                                        alm_ajustecab
                                                        INNER JOIN tb_proyectos ON alm_ajustecab.idcostos = tb_proyectos.nidreg
                                                        INNER JOIN tb_user ON alm_ajustecab.idautoriza = tb_user.iduser
                                                        INNER JOIN tb_almacen ON alm_ajustecab.ncodalm2 = tb_almacen.ncodalm
                                                        INNER JOIN tb_parametros ON alm_ajustecab.ntipomov = tb_parametros.nidreg 
                                                    WHERE
                                                        alm_ajustecab.idreg = :idx");
                    $sql->execute(["idx" => $idx]);

                    $rowCount = $sql->rowcount();

                    if ($rowCount > 0) {
                        $docData = array();
                        while($row=$sql->fetch(PDO::FETCH_ASSOC)){
                            $docData[] = $row;
                        }
                    }

                    return array("cabecera" => $docData,
                                "detalles" =>$this->detallesAjuste($idx));
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
           
        }

        private function detallesAjuste($idx){
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                        alm_ajustedet.idreg,
                                                        alm_ajustedet.cant_ingr,
                                                        alm_ajustedet.nroorden,
                                                        alm_ajustedet.ncertcal,
                                                        alm_ajustedet.ffeccalibra,
                                                        alm_ajustedet.ncertificado,
                                                        alm_ajustedet.condicion,
                                                        alm_ajustedet.cmarca,
                                                        alm_ajustedet.estado,
                                                        alm_ajustedet.ncolada,
                                                        alm_ajustedet.ntag,
                                                        alm_ajustedet.cserie,
                                                        alm_ajustedet.ccontenedor,
                                                        alm_ajustedet.cestante,
                                                        alm_ajustedet.cfila,
                                                        alm_ajustedet.nreglib,
                                                        alm_ajustedet.cestado,
                                                        alm_ajustedet.codprod,
                                                        UPPER(cm_producto.cdesprod) AS cdesprod,
                                                        cm_producto.ccodprod,
                                                        tb_unimed.cabrevia,
                                                        alm_ajustedet.idregistro,
                                                        alm_ajustedet.vence,
                                                        alm_ajustedet.observaciones
                                                    FROM
                                                        alm_ajustedet
                                                        LEFT JOIN cm_producto ON alm_ajustedet.codprod = cm_producto.id_cprod
                                                        LEFT JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed 
                                                    WHERE
                                                        alm_ajustedet.idregistro =:idx");
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
                                        <td class="textoCentro"> <input type="text" value="'.$rs['cabrevia'].'"></td>
                                        <td class="textoCentro"> <input type="text" value="'.$rs['cmarca'].'"></td>
                                        <td class="textoDerecha"> <input type="number" value="'.$rs['cant_ingr'].'"></td>
                                        <td class="textoCentro"> <input type="number" value="'.$rs['nroorden'].'"></td>
                                        <td class="textoCentro"> <input type="text" value="'.$rs['ncolada'].'"></td>
                                        <td class="textoCentro"> <input type="text" value="'.$rs['ntag'].'"></td>
                                        <td class="textoCentro"> <input type="text" value="'.$rs['cserie'].'"></td>
                                        <td class="textoCentro"> <input type="text" value="'.$rs['ncertificado'].'"></td>
                                        <td class="textoCentro"> <input type="date" value="'.$rs['ffeccalibra'].'"></td>
                                        <td class="textoCentro"> <input type="date" value="'.$rs['vence'].'"></td>
                                        <td class="textoCentro"> <input type="number" value="'.$rs['nreglib'].'"></td>
                                        <td class="textoCentro"> <input type="text" value="'.$rs['cestado'].'"></td>
                                        <td class="textoCentro"> <input type="text" value="'.$rs['condicion'].'"></td>
                                        <td class="textoCentro"> <input type="text" value="'.$rs['ccontenedor'].'"></td>
                                        <td class="textoCentro"> <input type="text" value="'.$rs['cestante'].'"></td>
                                        <td class="textoCentro"> <input type="text" value="'.$rs['cfila'].'"></td>
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
    }

    
?>