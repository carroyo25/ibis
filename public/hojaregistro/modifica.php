<?php
session_start();
require_once('consultas.php');

if (isset($_SESSION['loggedin'])) {
    $proveedor = getProveedorById($pdo, $_SESSION['id_centi']);
    $paises = getCountries($pdo);
    $pagos = getPaymentList($pdo);
    $actividades = getEconomicActivity($pdo);
    echo $proveedor['detalle'][0]['nentifinan'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alta Proveedores SEPCON - SICAL</title>
    <link rel="stylesheet" href="../css/hojaregistro.css?<?php echo $version = rand(0, 9999); ?>">
</head>
<body>
    <!-- <div class="wrap">
        <section class="wrap__header">
            <div>
                <img src="../img/logo.png" alt="logo_sepcon">
                <p>FORMULARIO DE REGISTRO DE PROVEEDORES</p>
                <a href="#">Grabar Datos</a>
            </div>
        </section>
        <section class="seccion_pagina">

        </section>
        <section class="seccion_pagina">

        </section>
        <section class="seccion_pagina">

        </section>
        <section class="seccion_pagina">

        </section>
    </div> -->
    <div class="wrap">
        <form id="datos_entidad" method="POST" enctype="multipart/form-data">
            <input type="file" class="oculto" name="uploadruc" id="uploadruc">
            <input type="file" class="oculto" name="uploadcatalogo" id="uploadcatalogo">

            <section class="wrap__header">
                <img src="../img/logo.png" alt="logo_sepcon">
                <span>FORMULARIO DE ACTUALIZACIÓN DE DATOS</span>
                <a href="#" class="btn btn-1" id="btn_guardar">Actualizar Datos</a>
                <a href="#" class="btn btn-1" style="margin-left: 1em;" id="btn_pass">Cambiar Contraseña</a>
            </section>
            <section class="seccion_pagina">
                <div class="contenedor_detalles">
                    <p class="detalle">SECCION 1: Detalles de la Empresa e información general</p>
                </div>
                <div class="seccion_data">
                    <label for="ruc">R.U.C/RUT</label>
                    <input type="text" name="ruc" id="ruc" class="requerido" value="<?php echo $proveedor['cnumdoc']?>">

                    <label for="razon_social">Razón Social</label>
                    <input type="text" name="razon_social" id="razon_social" class="requerido" value="<?php echo $proveedor['crazonsoc']?>">

                    <label for="direccion">Dirección</label>
                    <input type="text" name="direccion" id="direccion" class="requerido" value="<?php echo $proveedor['cviadireccion']?>">

                    <label for="correo_electronico">Correo Electrónico</label>
                    <input type="email" name="correo_electronico" id="correo_electronico" value="<?php echo $proveedor['cemail']?>">

                    <label for="pagina_web">Página Web</label>
                    <input type="text" name="pagina_web" id="pagina_web" value="<?php echo $proveedor['cwebpage']?>">

                    <label for="telefono">Teléfono</label>
                    <input type="text" name="telefono" id="telefono" class="requerido" value="<?php echo $proveedor['ctelefono']?>">

                    <label for="pais">Pais</label>
                    <select name="pais" id="pais">

                        <?php
                            foreach ($paises as $pais) { ?>
                                <option value="<?php echo $pais['ccodpais'] ?>" <?php if($pais['ccodpais'] == $proveedor['ncodpais']) {?>selected<?php }?> ><?php echo $pais['cdespais']?></option>
                        <?php } ?>
                    </select>

                    <label for="forma_pago">Forma de Pago</label>
                    <select name="forma_pago" id="forma_pago">
                        <?php
                            foreach ($pagos as $pago) { ?>
                                <option value="<?php echo $pago['nidreg'] ?>" <?php if($pago['nidreg'] == $proveedor['nformapago']) {?>selected<?php }?>><?php echo $pago['cdescripcion']?></option>
                        <?php } ?>
                    </select>
                    
                    <label for="actividad_economica">Actividad Económica</label>
                    <select name="actividad_economica" id="actividad_economica">
                        <?php
                            foreach ($actividades as $actividad) { ?>
                                <option value="<?php echo $actividad['nidreg'] ?>" <?php if($actividad['nidreg'] == $proveedor['nacteconomica']) {?>selected<?php }?>><?php echo $actividad['cdescripcion']?></option>
                        <?php } ?>
                    </select>
                </div>
            </section>
            <section class="seccion_pagina">
                <div class="contenedor_detalles">
                    <p class="detalle">SECCION 2: Detalles detalles del los representantes de la empresa</p>
                </div>      
                </br>
                <p class="detalle">Gerente Comercial</p>
                <div class="seccion_data">
                    <label for="gerente_comercial">Nombres</label>
                    <input type="text" name="gerente_comercial" id="gerente_comercial" value="<?php echo $proveedor['cnomgerentec']?>">
                    <label for="documento_gerente">Documento de Identidad</label>
                    <input type="text" name="documento_gerente" id="documento_gerente" value="<?php echo $proveedor['cnumdocgerentec']?>">
                    <label for="telefono_gerente">Telefono</label>
                    <input type="text" name="telefono_gerente" id="telefono_gerente" value="<?php echo $proveedor['ctelgerentec']?>">
                    <label for="correo_gerente">Correo Electrónico</label>
                    <input type="text" name="correo_gerente" id="correo_gerente" value="<?php echo $proveedor['cemailgerentec']?>">
                </div>
                <p class="detalle">Personal de contacto</p>
                <div class="seccion_data">
                    <label for="contacto">Nombres</label>
                    <input type="text" name="contacto" id="contacto" class="requerido" value="<?php echo $proveedor['cnomcontacto']?>">
                    <label for="documento_contacto">Documento de Identidad</label>
                    <input type="text" name="documento_contacto" id="documento_contacto" value="<?php echo $proveedor['cnumdoccontacto']?>">
                    <label for="telefono_contacto">Telefono</label>
                    <input type="text" name="telefono_contacto" id="telefono_contacto" class="requerido" value="<?php echo $proveedor['ctelcontacto']?>">
                    <label for="correo_contacto">Correo Electrónico</label>
                    <input type="text" name="correo_contacto" id="correo_contacto" class="requerido" value="<?php echo $proveedor['cemailcontacto']?>">
                </div>
            </section>
            <section class="seccion_pagina">
                <div class="contenedor_detalles">
                    <p class="detalle">SECCION 3: Información Bancaria</p>
                    <!-- <a href="#" class="btn btn-1" id="agregar_bancos">Agregar</a> -->
                </div>
                </br>
                <table id="tabla_bancos">
                    <thead>
                        <tr>
                            <th>Nombre Entidad Financieria</th>
                            <th>Moneda</th>
                            <th>Tipo de cuenta</th>
                            <th>N° de cuenta</th>
                            <!-- <th>...</th> -->
                        </tr>
                        
                    </thead>
                    <tbody id="tabla_bancos_body">
                    <?php
                            foreach($proveedor['detalle'] as $cuenta){
                        ?>
                        <tr>
                            <td>
                            <select name="banco_cuenta" id="banco_cuenta">
                                <option value="-1">Seleccione una opcion</option>
                                <option value="11" <?php if($cuenta['nentifinan'] == 11) {?> selected<?php } ?>>BANCO DE CREDITO</option>
                                <option value="12" <?php if($cuenta['nentifinan'] == 12) {?> selected<?php } ?>>INTERBANK</option>
                                <option value="13" <?php if($cuenta['nentifinan'] == 13) {?> selected<?php } ?>>SCOTIA BANK</option>
                                <option value="15" <?php if($cuenta['nentifinan'] == 15) {?> selected<?php } ?>>BANCO CONTINENTAL</option>
                            </select>
                            </td>
                            <td>
                            <select name="tipo_moneda" id="tipo_moneda">
                                <option value="-1">Seleccione una opcion</option>
                                <option value="20" <?php if($cuenta['ntipomoneda'] == 20) {?> selected<?php } ?>>SOLES</option>
                                <option value="21" <?php if($cuenta['ntipomoneda'] == 21) {?> selected<?php } ?>>DOLARES</option>
                            </select>
                            </td>
                            <td>
                            <select name="tipo_cuenta" id="tipo_cuenta">
                                <option value="-1">Seleccione una opcion</option>
                                <option value="01" <?php if($cuenta['ntipocuenta'] == 1) {?> selected<?php } ?>>AHORROS</option>
                                <option value="02" <?php if($cuenta['ntipocuenta'] == 2) {?> selected<?php } ?>>CUENTA CORRIENTE</option>
                                <option value="03" <?php if($cuenta['ntipomoneda'] == 3) {?> selected<?php } ?>>INTERBANCARIA</option>
                            </select>
                            </td>
                            <td><input type="text" name="num_cuenta" id="num_cuenta" value="<?php echo $cuenta['cnumcuenta'] ?>"></td>
                            <!-- <td><a onclick="eliminar(this)" data-grabado="0" data-idx=""><i class="fas fa-trash-alt"></i>Borrar</a></td> -->
                        </tr>
                        <?php  
                            } 
                        ?>
                    </tbody>
                </table>
                <div class="seccion_data">
                    <label for="cta_detracciones">N° de cuenta detracciones</label>
                    <input type="text" name="cta_detracciones" id="cta_detracciones" value="<?php echo $proveedor['ccuentadetrac']?>">
                </div>
            </section>
            <section class="seccion_pagina">
                <div class="contenedor_detalles">
                    <p class="detalle">SECCION 4: Adjuntos</p>
                </div>
                <div class="opciones">
                    <div class="container-input">
                        <input type="file" name="file_ruc" id="file_ruc" class="inputfile">
                        <label for="file_ruc" class="btn btn-1">
                            Cargar Ficha R.U.C
                        </label>
                        <!-- <a href="#" class="btn btn-1" id="ficha_ruc">Ficha R.U.C</a> -->
                         <?php
                         if(!empty($proveedor['cficharuc'])){?>
                         <div class="btn-container">
                            <!-- <button>Descargar Ficha</button> -->
                            <a class="btn-download" href="../documentos/ficharuc/<?php echo $proveedor['cficharuc'] ?>" download="<?php echo $proveedor['cficharuc'] ?>">Descargar Ficha Actual</a>
                         </div>
                         <?php }
                         ?>
                    </div>

                    <div class="container-input">
                        <input type="file" name="file_catalogo" id="file_catalogo" class="inputfile">
                        <label for="file_catalogo" class="btn btn-1">
                            Cargar Catálogo Productos
                        </label>
                        <?php
                         if(!empty($proveedor['ccatalogo'])){?>
                        <div class="btn-container">
                            <!-- <button>Descargar Ficha</button> -->
                            <a class="btn-download" href="../documentos/catalogoproducto/<?php echo $proveedor['ccatalogo'] ?>" download="<?php echo $proveedor['ccatalogo'] ?>">Descargar Catlálogo Actual</a>
                        </div>
                        <?php }
                        ?>
                    </div>
                    <!-- <a href="#" class="btn btn-1" id="ficha_ruc">Ficha R.U.C</a> -->
                    <!-- <a href="#" class="btn btn-1" id="catalogo">Catálogo Productos</a> -->
                </div>
            </section>
            <input id="id_centi" name="id_centi" type="hidden" value="<?php echo $proveedor['id_centi'] ?>" />
            <input id="cficharuc" name="cficharuc" type="hidden" <?php if($proveedor['cficharuc'] == ""){ echo 'value=""';} else {echo 'value='.$proveedor['cficharuc']; } ?> />
            <input id="ccatalogo" name="ccatalogo" type="hidden" <?php if($proveedor['ccatalogo'] == ""){ echo 'value=""'; }else {echo 'value='.$proveedor['ccatalogo']; }?> />
        </form>
    </div>
    <div id="myModal" class="modal">

    <!-- Modal content -->
    <div class="modal-content">
        <span class="close">&times;</span>
        <div class="container-form-pass">
            
            <form id="change_pass">
            <div class="password">
                <label for="ruc">Nueva Contraseña</label>
                <div class="sec-2">
                <ion-icon name="mail-outline"></ion-icon>
                <input type="text" name="password" id="password" placeholder="············"/>
                </div>
            </div>
            <div class="password">
                <label for="password">Confirmar Contraseña</label>
                <div class="sec-2">
                <ion-icon name="lock-closed-outline"></ion-icon>
                <input class="pas" type="password" name="confirm_password" id="confirm_password" placeholder="············"/>
                <ion-icon class="show-hide" name="eye-outline"></ion-icon>
                <input id="id_centi_pass" name="id_centi" type="hidden" value="<?php echo $proveedor['id_centi'] ?>" />
                </div>
            </div>
            <div class="buttons">
                <a href="#" class="change-pass" id="btn_cambiar">Guardar Cambios</a>
                <!-- <button class="login" id="btn_login">Ingresar</button> -->
            </div>
        </div>
        
        
        </form>
    </div>

</div>
</body>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const btn_guardar = document.getElementById("btn_guardar");
    const btn_cambiar = document.getElementById("btn_cambiar");
    btn_guardar.onclick = (e) => {
    e.preventDefault();
    try {

        let cuentas = [];
        const datos = new FormData(document.getElementById("datos_entidad"));
            datos.set("funcion","actualizar");
            datos.append("numCuentas", document.querySelectorAll("#tabla_bancos_body tr").length);

            const filas = document.querySelectorAll("#tabla_bancos_body tr");
            filas.forEach(fila => {
                const selects = fila.querySelectorAll('select');
                const input = fila.querySelector('input').value

                const banco = selects[0].value;
                const moneda = selects[1].value;
                const tipoCuenta = selects[2].value;

                cuentas.push({
                nombreBanco : banco,
                tipoMoneda : moneda,
                tipoCuenta: tipoCuenta,
                numeroCuenta: input
                })
            })

            datos.append("cuentas", JSON.stringify(cuentas))
            fetch("../hojaregistro/procesos.php",{
                method: 'POST',
                body: datos
            })
            .then(response => response.json())
            .then(data => {
                console.log(JSON.stringify(data));
                Swal.fire({
                icon: "success",
                title: "Actualización exitosa",
                text: "Se han cambiado los datos exitosamente"
                });
            }) 

    } catch (error) {
        console.log(error.message);
    }
    
    return false;
    
    }

    btn_cambiar.onclick = (e) => {
        e.preventDefault();
        let password = document.getElementById("password").value;
        let confirm_password = document.getElementById("confirm_password").value;
        if(password == confirm_password){
            const datos = new FormData(document.getElementById("change_pass"));
            console.log()
            datos.set("funcion", "cambiarPass")
            fetch('../hojaregistro/procesos.php',{
                method: 'POST',
                body: datos
            })
            .then(response => response.json())
            .then(data => {
                console.log(JSON.stringify(data));
                Swal.fire({
                icon: "success",
                title: "Actualización exitosa",
                text: "Se han cambiado los datos exitosamente"
                });
            }) 
        }else {
            Swal.fire({
                icon: "error",
                title: "Error al cambiar contraseña",
                text: "Verifique que las contraseñas digitadas coincidan en los campos"
            });
        }
        
    }
    var modal = document.getElementById("myModal");
    var btn = document.getElementById("btn_pass");
    var span = document.getElementsByClassName("close")[0];

    btn.onclick = function() {
    modal.style.display = "block";
    }

    span.onclick = function() {
    modal.style.display = "none";
    }

    window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
    }

</script>
</html>
<?php
}else {
    header('Location: index.php');
}
session_destroy()
?>