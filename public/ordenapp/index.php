<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/app.css?v<?php echo rand(0,900)?>">
    <title>Document</title>
</head>
<body>
    
    <div class="modal" id="loader">

    </div>
    
    <div class="wrap">
        <div class="header__wrap">
            <h1>Sical</h1>
        </div>
        <div class="body__wrap">
            <figure>
                <img src="../img/sical.png" alt="">
            </figure>
            <p>Sistema Integrado de Compras y Logística</p>
            <div class="login__body__wrap">
                <form action="panel.php" method="POST">
                    <h3>Acceso</h3>
                    <input type="text" name="user" id="user" placeholder="Usuario" required>
                    <input type="password" name="password" id="password" placeholder="Clave" required>

                    <button type="submit" name="login" id="login">Ingresar</button>
                </form>
            </div>
        </div>
    </div>
    <script src="../js/jquery.js"></script>
    <script src="../js/funciones.js"></script>
    <script src="../js/appord.js?v<?php echo rand(0,900)?>"></script>
</body>
</html>