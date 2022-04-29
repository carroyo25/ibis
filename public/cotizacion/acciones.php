<?php
    require_once("connect.php");

    if (isset($_POST['funcion'])) {
        if ($_POST['funcion'] == "subirAdjunto"){
            echo subirAdjunto($_FILES['file']);
        }else if($_POST['funcion'] == "enviarProforma"){
            echo json_encode(enviarProforma($pdo,$_POST['ruc'],
                                $_POST['detalles'],
                                $_FILES['cotizacion'],
                                $_POST['pedido'],
                                $_POST['fecha_emision'],
                                $_POST['fecha_vence'],
                                $_POST['nro_cot'],
                                $_POST['moneda'],
                                $_POST['cond_pago'],
                                $_POST['radioIgv'],
                                $_POST['st'],
                                $_POST['si'],
                                $_POST['to'],
                                $_POST['observaciones']));
        }
    }

    function enviarProforma($pdo,$ruc,$detalles,$archivo,$pedido,$emision,$vence,$numero,$moneda,$pago,$igv,$stotal,$tigv,$total,$obs){
        try {
            $fileTmpPath = $archivo['tmp_name'];
            $fileName = $archivo['name'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;

            $uploadFileDir = '../documentos/cotizaciones/';
            $dest_path = $uploadFileDir . $newFileName;
            
            if(move_uploaded_file($fileTmpPath, $dest_path))
            {
                $docrefer = uniqid();

                $sql = "INSERT INTO lg_proformacab SET id_regmov=?,id_centi=?,ffechadoc=?,ffechaven=?,cnumero=?,
                                                        ccondpago=?,cnameprof=?,crefdocprof=?,ncodmon=?,nsubtot=?,
                                                        nigv=?,ntotal=?,cobserva=?,cotref=?,nflgactivo=?";
                $statement = $pdo->prepare($sql);
                $statement -> execute(array($pedido,$ruc,$emision,$vence,$numero,
                                            $pago,$archivo['name'],$newFileName,$moneda,$stotal,
                                            $tigv,$total,$obs,$docrefer,1));
                $rowaffect = $statement->rowCount($sql);

                if ($rowaffect > 0) {
                    $message = 'Se registro correctamente';
                    $respuesta = true;
                    $error = "";
                    grabarDetalles($pdo,$detalles,$pedido,$docrefer,$moneda,$ruc);
                }  
            }
            else
            {
                $message = 'Hubo un error en el registro';
                $respuesta = false;
                $error = $archivo['error'];
            }

            return array("mensaje"=>$message,
                        "respuesta"=>$respuesta,
                        "error"=>$error);

        } catch (PDOException $th) {
            echo "Error: ".$th->getMessage();
            return false;
        }
    }

    function grabarDetalles($pdo,$detalles,$pedido,$cotref,$moneda,$ruc){
        try {
            $datos = json_decode($detalles);
            $nreg = count($datos);

            for ($i=0; $i < $nreg ; $i++) { 
                $sql = "INSERT INTO lg_proformadet SET id_regmov=?,niddet=?,ncodmed=?,id_centi=?,id_cprod=?,
                                                    cantcoti=?,ncodmon=?,precunit=?,total=?,ffechaent=?,cdetalle=?,
                                                    cdocPDF=?,cotref=?,nflgactivo=?";
                $statement = $pdo->prepare($sql);
                $statement -> execute(array($pedido,
                                            $datos[$i]->idpedet,
                                            $datos[$i]->unidad,
                                            $ruc,
                                            $datos[$i]->codprod,
                                            $datos[$i]->cantidad,
                                            $moneda,
                                            $datos[$i]->precio,
                                            $datos[$i]->precio*$datos[$i]->cantidad,
                                            $datos[$i]->entrega,
                                            $datos[$i]->observa,
                                            $datos[$i]->adjunto,
                                            $cotref,
                                            1));    
            }

        } catch (PDOException $th) {
            echo "Error: ".$th->getMessage();
            return false;
        }
    }

    function verificaParticipa($pdo,$pedido,$proveedor){
        try {
            $ret = false;
            $sql = "SELECT id_regmov FROM lg_proformacab WHERE id_regmov=? AND id_centi=?";
            $statement = $pdo->prepare($sql);
		    $statement -> execute(array($pedido,$proveedor));
		    $result = $statement ->fetchAll();
		    $rowaffect = $statement->rowCount($sql);

            if ($rowaffect > 0) {
                $ret = true;
            }

            return $ret;

        } catch (PDOException $th) {
            echo $th->getMessage();
            return false;
        }
    }
    
    function parametros($pdo,$clase){
        try {
            $sql = "SELECT
                        tb_parametros.nidreg,
                        tb_parametros.cdescripcion,
                        tb_parametros.cabrevia 
                    FROM
                        tb_parametros 
                    WHERE
                        tb_parametros.cclase = ? 
                        AND tb_parametros.ccod <> '00'";
            $statement = $pdo->prepare($sql);
            $statement -> execute(array($clase));
            $result = $statement ->fetchAll();
            $rowaffect = $statement->rowCount($sql);
            $salida = '<option value="-1" class="oculto">Elija opcion</option>';

            if ($rowaffect > 0) {
                foreach ($result as $rs) {
                    $salida .= '<option value="'.$rs['nidreg'].'">'.$rs['cdescripcion'].'</option>';
                }
            }

            return $salida;
        } catch (PDOException $th) {
            echo $th->getMessage();
            return false;
        }
    }

    function nombre_entidad($pdo,$ruc){
        try {
            $sql = "SELECT
                         id_centi,UPPER(crazonsoc) AS nombre
                    FROM
                        cm_entidad 
                    WHERE
                        cm_entidad.cnumdoc = ? 
                        AND cm_entidad.nflgactivo = 7";
            $statement = $pdo->prepare($sql);
            $statement -> execute(array($ruc));
            $result = $statement ->fetchAll();
            $salida = $result[0]['nombre'];

            return $salida;
        } catch (PDOException $th) {
            echo $th->getMessage();
            return false;
        }
    }

    function itemsPedido($pdo,$pedido,$ruc){
        try {
            $salida ="";
            $sql = "SELECT
                        lg_cotizadet.nitemcot,
                        lg_cotizadet.id_regmov,
                        lg_cotizadet.niddet,
                        lg_cotizadet.ncodmed,
                        lg_cotizadet.id_cprod,
                        lg_cotizadet.cantcoti,
                        lg_cotizadet.ccodcot,
                        cm_producto.ccodprod,
                        UPPER(cm_producto.cdesprod) AS cdesprod,
                        tb_unimed.cabrevia 
                    FROM
                        lg_cotizadet
                        INNER JOIN cm_producto ON lg_cotizadet.id_cprod = cm_producto.id_cprod
                        INNER JOIN tb_unimed ON lg_cotizadet.ncodmed = tb_unimed.ncodmed 
                    WHERE
                        lg_cotizadet.id_regmov = ?
                        AND lg_cotizadet.id_centi = ?";
            $statement = $pdo->prepare($sql);
            $statement -> execute(array($pedido,$ruc));
            $result = $statement ->fetchAll();
            $rowaffect = $statement->rowCount($sql);
            $filas = 1;
            $row = 0;

            if ($rowaffect > 0) {
                foreach ($result as $rs) {
                    $salida .= '<tr data-codprod="'.$rs['id_cprod'].'" data-iddetped="'.$rs['niddet'].'" data-adjunto="" data-und="'.$rs['ncodmed'].'">
                                    <td class="textoCentro">'.str_pad($filas++,3,0,STR_PAD_LEFT).'</td>
                                    <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                    <td class="pl20px">'.$rs['cdesprod'].'</td>
                                    <td class="textoCentro">'.$rs['cabrevia'].'</td>
                                    <td class="textoDerecha pr5px">'.number_format($rs['cantcoti'], 2, '.', ',').'</td>
                                    <td>
                                        <input type="number" 
                                            step="any" 
                                            placeholder="0.00" 
                                            onchange="(function(el){el.value=parseFloat(el.value).toFixed(2);})(this)"
                                            class="textoDerecha pr5px w100por precio">
                                    </td>
                                    <td class="textoDerecha pr5px"></td>
                                    <td></td>
                                    <td><input type="text" class="w100por"></td>
                                    <td><input type="date" class="w90por"></td>
                                    <td class="textoCentro"><a href="'.$rs['nitemcot'].'" data-row="'.$row++.'"><i class="fas fa-paperclip"></i></a></td>
                                </tr>';
                }
            }
            
            return $salida;
        } catch (PDOException $th) {
            echo $th->getMessage();
            return false;
        }
    }

    function subirAdjunto($archivo){
        try {
            // directory in which the uploaded file will be moved
            // get details of the uploaded file
            $message = "nothing to do";
            $fileTmpPath = $archivo['tmp_name'];
            $fileName = $archivo['name'];
            $fileSize = $archivo['size'];
            $fileType = $archivo['type'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;

            
            $uploadFileDir = '../documentos/pedidos/especificaciones/';
            $dest_path = $uploadFileDir . $newFileName;
            
            if(move_uploaded_file($fileTmpPath, $dest_path))
            {
                $message = $newFileName;
            }
            else
            {
                $message = 'There was some error moving the file to upload directory. Please make sure the upload directory is writable by web server.';
            }

            return $message;
        } catch (PDOException $th) {
            echo $th->getMessage();
            return false;
        }
    }
?>