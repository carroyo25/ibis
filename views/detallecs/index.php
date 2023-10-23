<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
    <title>Document</title>
</head>
<body>
    <div class="mensaje">
        <p></p>
    </div>
    <div class="cabezaModulo">
        <h1>Exixtencias Items</h1>
        <div>
            <a href="#" id="excelFile"><i class="fas fa-file-excel"></i><p>Reporte</p></a>
            <a href="#" id="irInicio"><i class="fas fa-home"></i><p>Inicio</p></a>
        </div>
    </div>
    <div class="barraTrabajo">
        <div class="variasConsultas5campos">
            <input type="hidden" name="cut" id="cut">
            <input type="hidden" name="correo" id="correo">
            <div>
                <label for="costosSearch">Centro de Costos: </label>
                <select name="costosSearch" id="costosSearch" class="item4">
                    <?php echo $this->listaCostosSelect ?>
                </select>
            </div>
            <div>
                <label for="codSearch">Codigo : </label>
                <input type="text" id="codSearch" name="codSearch" class="ingreso">
            </div>
            <div>
                <label for="nameSearch">Nombre</label>
                <input type="text" id="nameSearch" name="nameSearch" class="ingreso">
            </div>
        </div>
    </div>
    <div class="itemsTabla">
        <table id="tablaPrincipal">
            <thead class="stickytop">
                <tr>
                    <th width="7%">Item</th>
                    <th width="15%">Codigo</th>
                    <th width="30%">Descripcion</th>
                    <th>UND.</th>
                    <th width="15%">Total Existencias</th>
                </tr>
            </thead>
        </table>
    </div>
    <script src="<?php echo constant('URL');?>public/js/jquery.js"></script>
    <script src="<?php echo constant('URL');?>public/js/detallecs.js"></script>
    <script src="<?php echo constant('URL');?>public/js/funciones.js?<?php echo constant('VERSION')?>"></script>
</body>
</html>
