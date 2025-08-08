<?php
    require_once('consultas.php');

    session_start();

    if ($_SESSION['log'] == true) {
        $paises = getCountries($pdo);
        $pagos = getPaymentList($pdo);
        $actividades = getEconomicActivity($pdo);
        $proveedor = getEntiByRuc($pdo,$_SESSION['ruc']);
        $detalles = getDetailsById($pdo,$proveedor[0]["id_centi"]);
        $bancos = getEntityBancs($pdo,$proveedor[0]["id_centi"]);
    }else{
        header("Location: http://localhost/ibis/public/hojaregistro/");
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hoja de actualización de Datos - SEPCON - SICAL</title>
    <link rel="stylesheet" href="../css/hojaregistro.css?<?php echo $version = rand(0, 9999); ?>">
    <link rel="stylesheet" href="../css/notify.css">
    <link rel="stylesheet" href="../css/all.css">
    <link rel="shortcut icon" type="image/png" href="../img/logo.png" />
</head>
<body>
    <div class="floating">
        <a href="#" id="floatUp" class="floatingOptions"><i class="fas fa-arrow-alt-circle-up"></i></a>
        <a href="#" id="cancelReg" class="floatingOptions"><i class="fas fa-ban"></i></a>
        <a href="#" id="floatSave" class="floatingOptions btnSave" data-accion="modify"><i class="fas fa-save"></i></a>
    </div>
    <div class="wrap" id="wrap">
        <form id="datos_entidad" method="POST" enctype="multipart/form-data">
            <input type="file" class="oculto" name="uploadruc" id="uploadruc">
            <input type="file" class="oculto" name="uploadcatalogo" id="uploadcatalogo">
            <input type="file" class="oculto" name="uploadeclaracion" id="uploadeclaracion">

            <input type="hidden" name="actualiza" id="actualiza" value="<?php echo $proveedor[0]["nflgactualizado"]?>">
            <input type="hidden" name="id" id="id" value="<?php echo $proveedor[0]["id_centi"]?>">

            <section class="wrap__header">
                <img src="../img/logo.png" alt="logo_sepcon">
                <span>ACTUALIZACION DE DATOS DE PROVEEDORES</span>
                <a href="#" class="btn btn-1 btnSave" id="btn_guardar" data-accion="modify"><span>Grabar Datos</span></a>
            </section>
            <section class="seccion_pagina">
                <div class="contenedor_detalles">
                    <p class="detalle">SECCION 1: Detalles de la Empresa e información general</p>
                </div>
                <div class="seccion_data">
                    <label for="ruc">R.U.C/RUT</label>
                    <input type="text" name="ruc" id="ruc" class="requerido" value="<?php echo $proveedor[0]["cnumdoc"]?>">

                    <label for="razon_social">Razón Social</label>
                    <input type="text" name="razon_social" id="razon_social" class="requerido" value="<?php echo $proveedor[0]["crazonsoc"]?>">

                    <label for="direccion">Dirección</label>
                    <input type="text" name="direccion" id="direccion" class="requerido" value="<?php echo $proveedor[0]["cviadireccion"]?>">

                    <label for="correo_electronico">Correo Electrónico</label>
                    <input type="email" name="correo_electronico" id="correo_electronico" value="<?php echo $proveedor[0]["cemail"]?>">

                    <label for="pagina_web">Página Web</label>
                    <input type="text" name="pagina_web" id="pagina_web">

                    <label for="telefono">Teléfono</label>
                    <input type="text" name="telefono" id="telefono" class="requerido" value="<?php echo $proveedor[0]["ctelefono"]?>">

                    <label for="pais">Pais</label>
                    <select name="pais" id="pais">
                        <?php foreach ($paises as $pais) { 
                            $selected = $pais['ncodpais'] == $proveedor[0]['ncodpais'] ? 'selected':'';
                        ?>
                            <option value="<?php echo $pais['ncodpais']?>" <?php echo $selected ?>><?php echo $pais['cdespais']?></option>
                        <?php } ?>
                    </select>

                    <label for="actividad_economica">Actividad Económica</label>
                    <select name="actividad_economica" id="actividad_economica">
                        <?php
                            foreach ($actividades as $actividad) { 
                                $selected = $actividad['nidreg'] == $proveedor[0]['nrubro'] ? 'selected':'';
                            ?>
                                <option value="<?php echo $actividad['nidreg'] ?>" <?php echo $selected ?>><?php echo $actividad['cdescripcion']?></option>
                        <?php } ?>
                    </select>
                </div>
            </section>
            <section class="seccion_pagina">
                <div class="contenedor_detalles">
                    <p class="detalle">SECCION 2: Detalles detalles del los representantes de la empresa</p>
                </div>      
                </br>
                <p class="detalle">Gerente Comercial</p>
                <div class="seccion_data">
                    <label for="gerente_comercial">Nombres</label>
                    <input type="text" name="gerente_comercial" id="gerente_comercial" value="<?php echo isset($detalles['nomgercomer']) ? $detalles['nomgercomer']:"" ?>">
                    <label for="telefono_gerente">Telefono</label>
                    <input type="text" name="telefono_gerente" id="telefono_gerente" value="<?php echo isset($detalles['telgercomer']) ? $detalles['telgercomer']:""?>">
                    <label for="correo_gerente">Correo Electrónico</label>
                    <input type="text" name="correo_gerente" id="correo_gerente" value="<?php echo isset($detalles['corgercomer']) ? $detalles['corgercomer']:""?>">
                </div>
                <p class="detalle">Personal de contacto</p>
                <div class="seccion_data">
                    <label for="contacto">Nombres</label>
                    <input type="text" name="contacto" id="contacto" class="requerido" value="<?php echo isset($detalles['nomcontacto']) ? $detalles['nomcontacto'] : ""?>">
                    <label for="telefono_contacto">Telefono</label>
                    <input type="text" name="telefono_contacto" id="telefono_contacto" class="requerido" value="<?php echo isset($detalles['telcontacto']) ? $detalles['telcontacto'] : "" ?>">
                    <label for="correo_contacto">Correo Electrónico</label>
                    <input type="text" name="correo_contacto" id="correo_contacto" class="requerido" value="<?php echo isset($detalles['corcontacto']) ? $detalles['corcontacto'] :"" ?>">
                </div>
                <p class="detalle">Contacto Personal Retención</p>
                <div class="seccion_data">
                    <label for="contacto_detraccion">Nombres</label>
                    <input type="text" name="contacto_detraccion" id="contacto_detraccion" value="<?php echo isset($detalles['nomperdetra']) ? $detalles['nomperdetra'] :""?>">
                    <label for="telefono_contacto">Telefono</label>
                    <input type="text" name="telefono_contacto_detraccion" id="telefono_contacto_detraccion" value="<?php echo isset($detalles['telperdetra']) ? $detalles['telperdetra'] :""?>">
                    <label for="correo_contacto_detraccion">Correo Electrónico</label>
                    <input type="text" name="correo_contacto_detraccion" id="correo_contacto_detraccion" value="<?php echo isset($detalles['corperdetra']) ? $detalles['corperdetra'] :""?>">
                </div>
            </section>
            <section class="seccion_pagina">
                <div class="contenedor_detalles">
                    <p class="detalle">SECCION 3: Información Bancaria</p>
                    <a href="#" class="btn btn-1" id="agregar_bancos">Agregar</a>
                </div>
                </br>
                <table id="tabla_bancos">
                    <thead>
                        <tr>
                            <th>Nombre Entidad Financieria</th>
                            <th>Moneda</th>
                            <th>Tipo de cuenta</th>
                            <th>N° CTA</th>
                            <th>N° CCI</th>
                            <th>...</th>
                        </tr>
                    </thead>
                    <tbody id="tabla_bancos_body">
                        <?php foreach ($bancos as $banco) {?>
                            <tr data-grabado="1">
                                <td><input type="text" value="<?php echo $banco['banco']?>"></td>
                                <td><input type="text" value="<?php echo $banco['moneda']?>"></td>
                                <td><input type="text" value="<?php echo $banco['cuenta']?>"></td>
                                <td><input type="text" value="<?php echo $banco['cnrocta']?>"></td>
                                <td><input type="text" value="<?php echo $banco['cnrocci']?>"></td>
                                <td><a href="<?php echo $banco['nitem']?>" data-grabado="1" data-idx=""><i class="fas fa-trash-alt lnkTrash"></i></a></td>
                            </tr>
                        <?php }?>
                    </tbody>
                </table>
                <div class="seccion_data">
                    <label for="cta_detracciones">N° de cuenta detracciones</label>
                    <input type="text" name="cta_detracciones" id="cta_detracciones" value="<?php echo isset($detalles['nctadetrac']) ? $detalles['nctadetrac'] : ""?>">
                </div>
            </section>
            <section class="seccion_pagina">
                <div class="contenedor_detalles">
                    <p class="detalle">SECCION 4: Adjuntos</p>
                    
                </div>
                <div class="opciones">
                    <table id="adjuntos">
                        <thead>
                            <tr>
                                <th width="50%">Nombre Adjunto</th>
                                <th width="15%">Descargar</th>
                                <th width="15%">Subir</th>
                                <th>Archivo Adjunto</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>FICHA RUC</td>
                                <td></td>
                                <td style="text-align: center;line-height: 3rem;"><a href="#" ><i class="fas fa-upload btn_upload" data-file="ficha"></i></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>CATÁLOGO</td>
                                <td></td>
                                <td style="text-align: center;line-height: 3rem;"><a href="#" ><i class="fas fa-upload btn_upload" data-file="catalogo"></i></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>DECLARACIÓN JURADA DE CUMPLIMIENTO DEL SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO</td>
                                <td style="text-align: center;"><a href="../documentos/proveedores/formatos/declaracionjurada.docx" download><i class="fas fa-download"></i></a></td>
                                <td style="text-align: center;line-height: 3rem;"><a href="#" ><i class="fas fa-upload btn_upload" data-file="declaracion"></i></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </form>
    </div>
    <script src="../js/index.var.js"></script>
    <script src="../js/hojaregistro.js?<?php echo $version = rand(0, 9999); ?>"></script>
</body>
</html>