<?php
    require_once("acciones.php");

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
                    <button class="btn anim-bottom to-left">Ingresar</button>
                </div>
            </div>
            <div class="options">
                <i class="fas fa-tools"></i>
                <a href="#">Actualizar datos proveedor</a>
                <div class="option">
                    <button class="btn anim-bottom to-left">Ingresar</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>