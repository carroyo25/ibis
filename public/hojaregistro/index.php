<?php
    require_once("acciones.php");

    session_start();

    $_SESSION['ruc'] = "";

    $version = rand(0, 9999);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sepcon - Registro de Proveedores</title>
    <link rel="stylesheet" href="../css/all.css">
    <link rel="stylesheet" href="../css/registro.css?<?php echo $version?>">
</head>
<body>
    <div class="wrap">
        <div class="wrap__header">
            <section class="logo">
                <img src="../img/logo.png" alt="logo_sepcon">
            </section>
        </div>
        <div class="wrap__body">
            <p>Bienvenidos!</p>
            <p>Registro de proveedores SEPCON.</p>
        </div>
        <div class="wrap__options">
            <div class="options">
                <i class="fas fa-luggage-cart"></i>
                <a href="#">Registrar nuevo proveedor</a>
                <div class="option">
                    <a href="http://localhost/ibis/public/hojaregistro/alta.php">
                        <button class="btn anim-bottom to-left">Ingresar</button>
                    </a>
                </div>
            </div>
            <div class="options">
                <i class="fas fa-tools"></i>
                <a href="#">Actualizar datos proveedor</a>
                <form id="formEntData" method="POST">
                    <div class="entdata">
                        <label for="entruc">R.U.C :</label>
                        <input type="text" name="entruc" id="entruc">

                        <label for="entpass">Clave :</label>
                        <input type="password" name="entpass" id="entpass">
                    </div>
                </form>
                <div class="option">
                    <a href="#" id="hojaActualiza"><button class="btn anim-bottom to-left">Ingresar</button></a>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../js/alta.js"></script>
</body>
</html>