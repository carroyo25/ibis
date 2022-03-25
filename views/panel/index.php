<?php

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="<?php echo constant('URL')?>public/img/logo.png" />
    <link rel="stylesheet" href="<?php echo constant('URL')?>public/css/all.css">
    <link rel="stylesheet" href="<?php echo constant('URL')?>public/css/ibis.css?<?php echo constant('VERSION')?>">
    <title>Sistema Ibis -- Control de Procesos</title>
</head>
<body>
    <div class="mensaje mensaje_correcto">
        <p></p>
    </div>
    <div class="modal" id="ventanaEspera">

    </div>
    <div class="wrap">
        <div class="cabecera">
            <img src="<?php echo constant('URL')?>public/img/ibis.png" alt="">
            <div>
                <h1>CA</h1>
            </div>
        </div>
        <div class="areaTrabajo">
            <div class="menu">
                <div>
                    <h1>Menu</h1>
                    <a href="#"><i class="fas fa-bars"></i></a>
                </div>
                <div class="acordeon">
                    <?php echo $this->acordeon ?>
                </div>
            </div>
            <div class="ventana">
                <div class="cargaModulo"></div>
            </div>
        </div>
        <div class="pie">

        </div>
    </div>
    <script src="<?php echo constant('URL');?>public/js/jquery.js"></script>
    <script src="<?php echo constant('URL');?>public/js/funciones.js"></script>
    <script src="<?php echo constant('URL');?>public/js/panel.js?<?php echo constant('VERSION')?>"></script>
</body>
</html>