<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Documentos</title>
    <link rel="stylesheet" href="../../css/registrodocumentospanel.css?<?php echo $version = rand(0, 9999); ?>">
    <link rel="stylesheet" href="../../css/all.css">
</head>
<body>
    <div class="wrap">
        <div class="wrap_header">
            <div class="logo">

            </div>
            <div class="entidad_datos">
                <p>AR CONDUCTORES S.A.C.-ARCSAC</p>
                <p>20509655627</p>
            </div>
        </div>
        <nav class="wrap_nav">
            <div class="acciones_archivo">
                <a href="#" class="botones__click_accion"><i class="fas fa-upload"></i> <p>Subir Documentos</p></a>
                <a href="#" class="botones__click_accion"><i class="fas fa-download"></i><p>Descargar Archivos</p></a>
                <a href="#" class="botones__click_accion"><i class="fas fa-mail-bulk"></i><p>Enviar Archivos</p></a>
            </div>
            <div class="acciones_sistema">
                <a href="#" class="botones__click_accion"><i class="fas fa-sign-out-alt"></i><p>Cerrar Session</p></a>
            </div>
        </nav>
        <div class="wrap_orders">
            <h2>Registro de Ordenes</h2>
            <input type="search" placeholder="buscar N° de Orden">
            <div class="contenedor_ordenes">
                <ul id="listaOrdenes" class="lista_ul">
                    <?php
                        $numero_inicial=320;

                        for ($i=0; $i < 13; $i++) { ?>
                            <li><a href="#"><i class="far fa-file-alt"></i><p>OC-2025-<?php echo $numero_inicial++?></p></a></li>
                    <?php   }
                    ?>
                </ul>
            </div>
            
        </div>
        <div class="wrap_atachs">
            <h2>Adjuntos</h2>
            <div class="contenedor_adjuntos">
                <ul id="listaAdjuntos" class="lista_ul">
                    <?php
                        $numero_inicial=320;

                        for ($i=0; $i < 3; $i++) { ?>
                            <li><a href="#"><i class="fas fa-file-pdf"></i><p>FACTURA-2025-<?php echo $numero_inicial++?></p></a></li>
                    <?php   }
                    ?>
                </ul>
            </div>
        </div>
        <div class="wrap_status">
            <h2>Estado del Documento</h2>
            <div class="contenedor_estado">
                <p>Estado Documento</p>
                <p>:</p>
                <p class="enviado">Enviado</p>
                <p>Fecha Envio</p>
                <p>:</p>
                <p>04/03/2025</p>
                <p>Fecha Recepcion</p>
                <p>:</p>
                <p>04/03/2025</p>
                <p>Fecha Ingreso</p>
                <p>:</p>
                <p>04/03/2025</p>
            </div>
        </div>
        <div class="wrap_footer">
            <h5>Sepcon - Derechos Reservados</h5>
        </div>
    </div>
    <link rel="stylesheet"href="../../css/registrodocumentospanel.js?<?php echo $version = rand(0, 9999); ?>" type="module">
</body>
</html>