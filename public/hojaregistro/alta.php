<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alta Proveedores SEPCON - SICAL</title>
    <link rel="stylesheet" href="../css/hojaregistro.css?<?php echo $version = rand(0, 9999); ?>">
</head>
<body>
    <div class="wrap">
        <form id="datos_entidad">
            <input type="file" class="oculto" name="uploadruc" id="uploadruc">
            <input type="file" class="oculto" name="uploadcatalogo" id="uploadcatalogo">

            <section class="wrap__header">
                <img src="../img/logo.png" alt="logo_sepcon">
                <span>FORMULARIO DE REGISTRO DE PROVEEDORES</span>
                <a href="#" class="btn btn-1">Grabar Datos</a>
            </section>
            <section class="seccion_pagina">
                <p class="nota1">Sirvase rellenar los datos en su totalidad</p>
                <div class="contenedor_detalles">
                    <p class="detalle">SECCION 1: Detalles de la Empresa e información general</p>
                </div>
                <div class="seccion_data">
                    <label for="ruc">R.U.C</label>
                    <input type="text" name="ruc" id="ruc">

                    <label for="nombre_empresa">Nombre de la empresa</label>
                    <input type="text" name="nombre_empresa" id="nombre_empresa">

                    <label for="direccion">Dirección</label>
                    <input type="text" name="direccion" id="direccion">

                    <label for="correo_electronico">Correo Electrónico</label>
                    <input type="email" name="correo_electronico" id="correo_electronico">

                    <label for="pagina_web">Página Web</label>
                    <input type="text" name="pagina_web" id="pagina_web">

                    <label for="telefono">Teléfono</label>
                    <input type="text" name="telefono" id="telefono">

                    <label for="pais">Pais</label>
                    <select name="pais" id="pais">

                    </select>

                    <label for="forma_pago">Forma de Pago</label>
                    <select name="forma_pago" id="forma_pago"></select>
                    
                    <label for="actividad_economica">Actividad Económica</label>
                    <select name="actividad_economica" id="actividad_economica">

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
                    <input type="text" name="gerente_comercial" id="gerente_comercial">
                    <label for="documento_gerente">Documento de Identidad</label>
                    <input type="text" name="documento_gerente" id="documento_gerente">
                    <label for="telefono_gerente">Telefono</label>
                    <input type="text" name="telefono_gerente" id="telefono_gerente">
                    <label for="correo_gerente">Correo Electrónico</label>
                    <input type="text" name="correo_gerente" id="correo_gerente">
                </div>
                <p class="detalle">Personal de contacto</p>
                <div class="seccion_data">
                    <label for="contacto">Nombres</label>
                    <input type="text" name="contacto" id="contacto">
                    <label for="documento_contacto">Documento de Identidad</label>
                    <input type="text" name="documento_contacto" id="documento_contacto">
                    <label for="telefono_contacto">Telefono</label>
                    <input type="text" name="telefono_contacto" id="telefono_contacto">
                    <label for="correo_contacto">Correo Electrónico</label>
                    <input type="text" name="correo_contacto" id="correo_contacto">
                </div>
            </section>
            <section class="seccion_pagina">
                <div class="contenedor_detalles">
                    <p class="detalle">SECCION 3: Información Bancaria</p>
                    <a href="#" class="btn btn-1">Agregar</a>
                </div>
                </br>
                <table>
                    <thead>
                        <tr>
                            <th>Nombre Entidad Financieria</th>
                            <th>Moneda</th>
                            <th>Tipo de cuenta</th>
                            <th>N° de cuenta</th>
                        </tr>
                    </thead>
                    <tbody>

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
                    <a href="#" class="btn btn-1">Ficha R.U.C</a>
                    <a href="#" class="btn btn-1">Catálogo Productos</a>
                </div>
            </section>
        </form>
    </div>
    <script src="../js/hojaregistro.js"></script>
</body>
</html>