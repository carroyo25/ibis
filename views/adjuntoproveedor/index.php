<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div class="mensaje">
        <p></p>
    </div>
    <div class="modal" id="esperar">
    </div>
   <div class="modal" id="vistaAdjuntos">
        <div class="ventanaAdjuntos">
            <div class="tituloAdjuntos">
                <h3>Adjuntos Orden</h3>
                <a href="#" id="closeAtach" title="Cerrar Ventana"><i class="fas fa-window-close"></i></a>
            </div>
            <ul id="listaAdjuntos">

            </ul>
            <iframe src=""></iframe>
        </div>
    </div>
    <!--Ventana Princpal-->
    <div class="cabezaModulo">
        <h2>Reporte de Documento de Proveedores</h2>
        
        <div style="text-align: center;">
            <a href="#" id="irInicio"><i class="fas fa-home"></i><p>Inicio</p></a>
        </div>
    </div>
    <div class="barraTrabajo">
        <div class="unaConsulta">
            <label for="nameSearch">Nombre : </label>
            <input type="text" name="nameSearch" id="nameSearch">
        </div>
    </div>
    <div class="itemsTabla">
        <table id="tablaPrincipal" class="tablaBusqueda">
            <thead class="stickytop">
                <tr>
                    <th>Item</th>
                    <th width="10%" data-filtro="filtro">Nro. Documento/RUC</th>
                    <th data-filtro="filtro">Razón Social</th>
                    <th width="10%">Teléfono</th>
                    <th>Correo</th>
                </tr>
            </thead>
            <tbody id="tablaPrincipalCuerpo">
                
            </tbody>
        </table>
    </div>
    <div class="paginadorWrap"></div>
    <script src="<?php echo constant('URL');?>public/js/jquery.js"></script>
    <script src="<?php echo constant('URL');?>public/js/funciones.js"></script>
    <script src="<?php echo constant('URL');?>public/js/adjuntoproveedor.js?<?php echo constant('VERSION')?>"></script>
</body>
</html>