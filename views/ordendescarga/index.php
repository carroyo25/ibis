<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div class="cabezaModulo">
        <h1>Descargar de Ordenes</h1>
        <div>
            <a href="#" id="upload_file"><i class="fas fa-upload"></i><p>Cargar Archivo</p></a>
            <a href="#" id="donload_pdf"><i class="fas fa-file-download"></i><p>Descargar Ordendes</p></a>
            <a href="#" id="irInicio"><i class="fas fa-home"></i><p>Inicio</p></a>
            <input type="file" id="excelInput" accept=".xlsx, .xls" class="oculto">
        </div>
    </div>
    
    <div class="table-wrapper">
        <table id="tablaPrincipal" class="tablaNuevoFormato">
            <thead class="stickytop">
                <tr>
                    <th>✅</th>
                    <th>Item.</th>
                    <th>Descripcion</th>
                    <th>OC/OS</th>
                    <th>PDF</th>
                </tr>
            </thead>
            <tbody id="tableBody">
               
            </tbody>
        </table>
    </div>
    <script src="<?php echo constant('URL');?>public/js/jquery.js"></script>
    <script src="<?php echo constant('URL');?>public/js/xlsx.mini.min.js"></script>
    <script src="<?php echo constant('URL');?>public/js/funciones.js?<?php echo constant('VERSION')?>"></script>
    <script src="<?php echo constant('URL');?>public/js/ordendescarga.js?<?php echo constant('VERSION')?>"></script>
</body>
</html>