<?php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="<?php echo constant('URL')?>public/img/logo.png" />
    <link rel="stylesheet" href="<?php echo constant('URL')?>public/css/all.css">
    <link rel="stylesheet" href="<?php echo constant('URL')?>public/css/login.css?<?php echo constant('VERSION')?>">
    <title>Sical -- Sistema Integrado de Compras, Almacenes y Logística</title>
</head>
<body>
    <div class="mensaje mensaje_correcto">
        <p>Aca ira el mensaje</p>
    </div>
    <div class="modal" id="ventanaEspera">

    </div>
    <div class="wrap">
        <div class="cabecera">

        </div>
        <div class="landZone">
            <div class="presentacion">
                <h1>Sical  - Sepcon</h1>
                <p>Sistema Integrado de Compras, Almacenes y Logística</p>
                <div class="circulos">
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                </div>
                <p>2022</p>
            </div>
            <div class="ingreso">
                <div>
                    <form method="post">
                        <h1>Acceso</h1>
                        <div class="entradaTexto">
                            <i class="fas fa-user"></i>
                            <input type="text" name="usuario" id="usuario" placeholder="usuario">
                        </div>
                        <div class="entradaTexto">
                            <i class="fas fa-lock"></i>
                            <input type="password" name="clave" id="clave" placeholder="contraseña" autocomplete>
                        </div>
                        <div class="entradaCheck">
                            <input type="checkbox" name="verclave" id="verclave">
                            <span>Mostrar Clave</span>
                        </div>
                        <div class="opciones">
                            <button id="btnAcceso">Ingresar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="pie">

        </div>
    </div>
    <script>
            (function() {
                let lastModified = {};
                
                function checkForChanges() {
                    fetch('public/css/modulo.css', {cache: 'no-store'})
                    .then(res => res.headers.get('last-modified'))
                    .then(modified => {
                        if(lastModified['modulo.css'] && lastModified['modulo.css'] !== modified) {
                        reloadStyles();
                        }
                        lastModified['modulo.css'] = modified;
                    });
                }
                
                function reloadStyles() {
                    const links = document.querySelectorAll('link[rel="stylesheet"]');
                    links.forEach(link => {
                    const url = new URL(link.href);
                    url.searchParams.set('forceReload', Date.now());
                    link.href = url.toString();
                    });
                }
                
                // Verificar cambios cada 500ms
                setInterval(checkForChanges, 3600);
            })();
    </script>
    <script src="<?php echo constant('URL');?>public/js/jquery.js"></script>
    <script src="<?php echo constant('URL');?>public/js/funciones.js"></script>
    <script src="<?php echo constant('URL');?>public/js/main.js?<?php echo constant('VERSION')?>"></script>
</body>
</html>