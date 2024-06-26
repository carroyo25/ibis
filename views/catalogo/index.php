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
    <div class="cabezaModulo">
        <h1>Catálogo Bienes/Servicios</h1>
        <div>
            <a href="#" id="excelFile"><i class="fas fa-file-excel"></i><p>Exportar</p></a>
            <a href="#" id="irInicio"><i class="fas fa-home"></i><p>Inicio</p></a>
        </div>
    </div>
    <div class="barraTrabajo">
        <div class="dosConsultas">
            <label for="consulta">Codigo : </label>
            <input type="text" name="codigo" id="codigo">
            <label for="consulta">Nombre : </label>
            <input type="text" name="descripcion" id="descripcion">
        </div>
    </div>
    <div class="itemsTabla" id="contenedor">
        <table id="tablaPrincipal">
            <thead class="stickytop">
                <tr>
                    <th width="10%">Codigo</th>
                    <th>Tipo</th>
                    <th>Denominación</th>
                    <th>Unidad</th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table>
    </div>
    <script src="<?php echo constant('URL');?>public/js/jquery.js"></script>
    <script src="<?php echo constant('URL');?>public/js/funciones.js?<?php echo constant('VERSION')?>"></script>
    <script src="<?php echo constant('URL');?>public/js/catalogo.js?<?php echo constant('VERSION')?>"></script>
</body>
</html>