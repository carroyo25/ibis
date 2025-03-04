<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro Documentario Sepcon</title>
    <link rel="shortcut icon" type="image/png" href="../img/logo.png" />
    <link rel="stylesheet" href="../css/registrodocumentosIndex.css?<?php echo $version = rand(0, 9999); ?>">
    <link rel="stylesheet" href="../css/notify.css">
    <link rel="stylesheet" href="../css/all.css">
    <link rel="shortcut icon" type="image/png" href="../img/logo.png" />
</head>
<body>
    <div class="wrap">
        <div class="wrap_header">
            <div class="wrap_header_logo">
                    
            </div>
            <div class="wrap_header_title">
                <span>Registro Documentario Proveedores - Sepcon</span>
            </div> 
        </div>
        <div class="wrap_body">
            <div class="login_container">
                <h1 class="login_title">Iniciar Registro</h1>
                <form action="">
                <div class="group">      
                        <input type="text" id="entidad_ruc" name="entidad_ruc" class="login_input" required>
                        <span class="highlight"></span>
                        <span class="bar"></span>
                        <label class="place_label">R.U.C</label>
                    </div>
                    <div class="group">      
                        <input type="password" id="entidad_clave" name="entidad_clave" class="login_input" required autocomplete>
                        <span class="highlight"></span>
                        <span class="bar"></span>
                        <label class="place_label">Contraseña</label>
                        <a href="#" class="icon_login" id="password_eye">
                            <i class="far fa-eye-slash"></i>
                        </a>
                    </div>
                    
                    <button id="boton_login" class="button">Ingresar</button>
                </form>
            </div>
        </div>
        <div class="wrap_footer">
            <h4>RR.HH. Sepcon 2024</h4>
        </div>
    </div>
    <script src="../js/index.var.js"></script>
    <script src="../js/registrodocumentoindex.js" type="module"></script>
</body>
</html>