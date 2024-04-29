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
            <section class="wrap__header">
                <img src="../img/logo.png" alt="logo_sepcon">
                <span>FORMULARIO DE REGISTRO DE PROVEEDORES</span>
                <a href="#">Grabar Datos</a>
            </section>
            <section class="seccion_pagina">
                <p class="nota1">Sirvase rellenar los datos en su totalidad</p>
                <p class="detalle">SECCION 1: Detalles de la Empresa e información general</p>
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
                <p class="detalle">SECCION 2: Detalles detalles del los representastes de la empresa</p>
            </section>
            <section class="seccion_pagina">

            </section>
            <section class="seccion_pagina">
                <p class="detalle">SECCION 3: Información Bancaria</p>
            </section>
        </form>
        
    </div>
</body>
</html>