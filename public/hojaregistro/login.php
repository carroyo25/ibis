<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión Proveedor</title>
    <link rel="stylesheet" href="../css/loginProveedor.css">
</head>
<body>   
    <div class="screen-1">
    <img class="logo" src="../img/logo.png" alt="logo_sepcon">
    <h1 class="title">Ingreso de Proveedores</h1>
    <form id="proveedor_login">
        <div class="email">
            <label for="ruc">RUC</label>
            <div class="sec-2">
            <ion-icon name="mail-outline"></ion-icon>
            <input type="text" name="ruc" placeholder="Ejm: 20503644968"/>
            </div>
        </div>
        <div class="password">
            <label for="password">Contraseña</label>
            <div class="sec-2">
            <ion-icon name="lock-closed-outline"></ion-icon>
            <input class="pas" type="password" name="password" placeholder="********"/>
            <ion-icon class="show-hide" name="eye-outline"></ion-icon>
            </div>
        </div>
        <div class="buttons">
            <a href="#" class="login" id="btn_login">Ingresar</a>
            <!-- <button class="login" id="btn_login">Ingresar</button> -->
        </div>
        
    </form>    
    <!-- <div class="footer"><span>Sign up</span><span>Forgot Password?</span></div> -->
    </div>
    <script src="../js/loginproveedor.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>