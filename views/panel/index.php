<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="<?php echo constant('URL')?>public/img/logo.png" />
    <link rel="stylesheet" href="<?php echo constant('URL')?>public/css/all.css">
    <link rel="stylesheet" href="<?php echo constant('URL')?>public/css/ibis.css?<?php echo constant('VERSION')?>">
    <link rel="stylesheet" href="<?php echo constant('URL');?>public/css/exceltable.css">
    <link rel="stylesheet" href="<?php echo constant('URL');?>public/css/pagination.css">
    <title>Sistema Ibis -- Control de Procesos</title>
</head>
<body>
    <div class="mensaje mensaje_correcto">
        <p></p>
    </div>
    <div class="modal" id="esperar">
        <div class="loadingio-spinner-spinner-5ulcsi06hlf">
            <div class="ldio-fifgg00y5y">
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
            </div>
        </div>
    </div>
    <div class="modal" id="cambio">
        <div class="ventanaCambio">
            <h3>Cambio de clave</h3>
            <div>
                <form method="post" id="form__clave">
                    <input type="password" name="nueva_clave" id="nueva_clave" placeholder="Ingrese su nueva clave" autocomplete>
                    <input type="password" name="nueva_clave_comfirm" id="nueva_clave_comfirm" placeholder="Reescriba la clave anterior" autocomplete>
                </form>
            </div>
            <div>
                <button type="button" id="btnAceptarCambio">Aceptar</button>
                <button type="button" id="btnCancelarCambio">Cancelar</button>
            </div>
        </div>
    </div>
    <div class="modal" id="preguntaVerifica">
        <div class="ventanaPregunta">
            <h3>¿Verificar Registro?</h3>
            <div>
                <button type="button" id="btnAceptarVerifica">Aceptar</button>
                <button type="button" id="btnCancelarVerifica">Cancelar</button>
            </div>
        </div>
    </div>
    <div class="wrap">
        <div class="cabecera">
            <img src="<?php echo constant('URL')?>public/img/ibis.png" alt="">
            <div id="cabecera_inicial">
                <h1 id="iniciales"><a href="#" id="cabecera_main_option"><?php echo $this->iniciales?></a> </h1>
                <ul id="cabecera_menu">
                    <li><a href="<?php echo $_SESSION['iduser']?>" id="changePass">Cambiar Contraseña</a></li>
                    <li><a href="#">Ir al inicio</a></li>
                    <hr>
                    <li><a href="<?php echo constant('URL')?>">Salir del Sistema</a></li>
                </ul>
                <input type="hidden" id="name_user" name="name_user" value="<?php echo $_SESSION['nombres']?>">
                <input type="hidden" id="id_user" name="id_user" value="<?php echo $_SESSION['iduser']?>">
                <input type="hidden" id="user_log" name="user_log" value="<?php echo $_SESSION['user']?>">
                <input type="hidden" id="mail_user" name="mail_user" value="<?php echo $_SESSION['correo']?>">
                <input type="hidden" id="rol_user" name="rol_user" value="<?php echo $_SESSION['rol']?>">
                <input type="hidden" id="almacen" name="almacen" value="<?php echo $_SESSION['almacen']?>">
                <input type="hidden" id="medicos" name="medicos" value="<?php echo $_SESSION['medicos']?>">
                <input type="hidden" id="mmtto" name="mmtto" value="<?php echo $_SESSION['mmtto']?>">
            </div>
        </div>
        <div class="areaTrabajo">
            <div class="menu">
                <div class="titulo_menu">
                    <h1>Menu</h1>
                    <a href="#"><i class="fas fa-bars"></i></a>
                </div>
                <div class="acordeon">
                    <?php echo $this->acordeon ?>
                </div>
            </div>
            <div class="ventana">
                <div class="cargaModulo">
                    <?php if ($_SESSION['rol'] == 2 || $_SESSION['rol'] == 9 || $_SESSION['rol'] == 110 || $_SESSION['rol'] == 299)
                                require 'views/adm.php'; 
                            else if ($_SESSION['rol'] == 5 )
                                require 'views/aprob.php';
                            else if ($_SESSION['rol'] == 3 || $_SESSION['rol'] == 228)
                                require 'views/aped.php';
                            else if ($_SESSION['rol'] == 68 )
                                require 'views/comp.php';
                            else if ($_SESSION['rol'] == 4 )
                                require 'views/alsed.php';
                            else if ($_SESSION['rol'] == 109 )
                                require 'views/alobr.php';  
                        ?>
                </div>
            </div>
        </div>
        <div class="pie">

        </div>
    </div>
    <script src="<?php echo constant('URL');?>public/code/highcharts.js"></script>
    <script src="<?php echo constant('URL');?>public/code/highcharts-more.js"></script>
    <script src="<?php echo constant('URL');?>public/code/modules/solid-gauge.js"></script>
    <script src="<?php echo constant('URL');?>public/code/modules/exporting.js"></script>
    <script src="<?php echo constant('URL');?>public/code/modules/export-data.js"></script>
    <script src="<?php echo constant('URL');?>public/code/modules/accessibility.js"></script>
    <script src="<?php echo constant('URL');?>public/js/jquery.js"></script>
    <script src="<?php echo constant('URL');?>public/js/funciones.js"></script>
    <script src="<?php echo constant('URL');?>public/js/panel.js?<?php echo constant('VERSION')?>"></script>
</body>
</html>