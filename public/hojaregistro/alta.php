<?php
    require_once('consultas.php');

    $paises         = getCountries($pdo);
    $pagos          = getPaymentList($pdo);
    $actividades    = getEconomicActivity($pdo);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alta Proveedores SEPCON - SICAL</title>
    <link rel="shortcut icon" type="image/png" href="../img/logo.png" />
    <link rel="stylesheet" href="../css/hojaregistro.css?<?php echo $version = rand(0, 9999); ?>">
    <link rel="stylesheet" href="../css/all.css">
    <link rel="stylesheet" href="../css/notify.css">
</head>
<body>
    <div class="floating">
        <a href="#" id="floatUp" class="floatingOptions"><i class="fas fa-arrow-alt-circle-up"></i></a>
        <a href="#" id="cancelReg" class="floatingOptions"><i class="fas fa-ban"></i></a>
        <a href="#" id="floatSave" class="floatingOptions btnSave"><i class="fas fa-save"></i></a>
    </div>
    <div class="wrap" id="wrap">
        <form id="datos_entidad" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="ubigeo" id="ubigeo">

            <section class="wrap__header">
                <img src="../img/logo.png" alt="logo_sepcon">
                <span>FORMULARIO DE REGISTRO DE PROVEEDORES</span>
                <a href="#" class="btn btn-1 btnSave" id="btn_guardar"><p>Grabar Datos</p></a>
            </section>
            <section class="seccion_pagina">
                <div class="contenedor_detalles">
                    <p class="detalle">SECCION 1: Detalles de la Empresa e información general</p>
                </div>
                <div class="seccion_data">
                    <label for="ruc">R.U.C/RUT</label>
                    <input type="text" name="ruc" id="ruc" class="requerido" placeholder="registre un RUC válido">

                    <label for="razon_social">Razón Social</label>
                    <input type="text" name="razon_social" id="razon_social" class="requerido">

                    <label for="direccion">Dirección</label>
                    <input type="text" name="direccion" id="direccion" class="requerido">

                    <label for="correo_electronico">Correo Electrónico</label>
                    <input type="email" name="correo_electronico" id="correo_electronico" class="correo">

                    <label for="pagina_web">Página Web</label>
                    <input type="text" name="pagina_web" id="pagina_web">

                    <label for="telefono">Teléfono</label>
                    <input type="text" name="telefono" id="telefono" class="requerido">

                    <label for="pais">Pais</label>
                    <select name="pais" id="pais">
                        <?php
                            foreach ($paises as $pais) { ?>
                                <option value="<?php echo $pais['ccodpais'] ?>"><?php echo $pais['cdespais']?></option>
                        <?php } ?>
                    </select>
                    <label for="actividad_economica">Actividad Económica</label>
                    <select name="actividad_economica" id="actividad_economica">
                        <?php
                            foreach ($actividades as $actividad) { ?>
                                <option value="<?php echo $actividad['nidreg'] ?>"><?php echo $actividad['cdescripcion']?></option>
                        <?php } ?>
                    </select>

                    <label for="registro_mtc">Registro MTC</label>
                    <input type="text" name="registro_mtc" id="registro_mtc" placeholder="Solo si la actividad económica es TRANSPORTE">
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
                    <input type="text" name="gerente_comercial" id="gerente_comercial">
                    <label for="telefono_gerente">Telefono</label>
                    <input type="text" name="telefono_gerente" id="telefono_gerente">
                    <label for="correo_gerente">Correo Electrónico</label>
                    <input type="text" name="correo_gerente" id="correo_gerente" class="correo">
                </div>
                <p class="detalle">Personal de contacto</p>
                <div class="seccion_data">
                    <label for="contacto">Nombres</label>
                    <input type="text" name="contacto" id="contacto" class="requerido">
                    <label for="telefono_contacto">Telefono</label>
                    <input type="text" name="telefono_contacto" id="telefono_contacto" class="requerido">
                    <label for="correo_contacto">Correo Electrónico</label>
                    <input type="text" name="correo_contacto" id="correo_contacto" class="requerido correo">
                </div>
                <p class="detalle">Contacto Personal Retención</p>
                <div class="seccion_data">
                    <label for="contacto_detraccion">Nombres</label>
                    <input type="text" name="contacto_detraccion" id="contacto_detraccion">
                    <label for="telefono_contacto">Telefono</label>
                    <input type="text" name="telefono_contacto_detraccion" id="telefono_contacto_detraccion" >
                    <label for="correo_contacto_detraccion">Correo Electrónico</label>
                    <input type="text" name="correo_contacto_detraccion" id="correo_contacto_detraccion" class="correo">
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
                            <th>N° de cuenta</th>
                            <th>N° CCI</th>
                            <th>...</th>
                        </tr>
                    </thead>
                    <tbody id="tabla_bancos_body">

                    </tbody>
                </table>
                <div class="seccion_data">
                    <label for="cta_detracciones">N° de cuenta detracciones</label>
                    <input type="text" name="cta_detracciones" id="cta_detracciones">
                </div>
            </section>
            <section class="seccion_pagina">
                <div class="contenedor_detalles">
                    <p class="detalle">SECCION 4: Adjuntos</p>
                </div>
                <div class="opciones">
                    <div class="container-input">
                        <input type="file" name="file_ruc" id="file_ruc" class="inputfile">
                        <label for="file_ruc" class="btn btn-1">
                            FICHA R.U.C
                        </label>
                    </div>
                    <div class="container-input">
                        <input type="file" name="file_catalogo" id="file_catalogo" class="inputfile">
                        <label for="file_catalogo" class="btn btn-1">
                            Catálogo Productos
                        </label>
                    </div>
                </div>
        </section>
        </form>
       
    </div>
    
    <script src="../js/index.var.js"></script>
    <script src="../js/hojaregistro.js?<?php echo $version = rand(0, 9999); ?>"></script>
</body>
</html>