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
            <p></p>
        </div>
    </div>
</body>
</html>