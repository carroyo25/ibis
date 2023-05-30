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
    <div class="modal" id="hojakardex">
        <div class="ventanaVistaPrevia">
            <div class="tituloVista">
                <h3>Kardex</h3>
                <a href="#" id="closePreview" title="Cerrar Ventana"><i class="fas fa-window-close"></i></a>
            </div>
            <iframe src=""></iframe>
        </div>
    </div>
    <div class="cabezaModulo">
        <h1>Kardex Personal</h1>
        <div>
            <a href="#" id="btnKardex"><i class="fas fa-file-pdf"></i><p>Kardex</p></a>
            <a href="#" id="excelFile"><i class="fas fa-file-excel"></i><p>Reporte</p></a>
            <a href="#" id="btnBuscar"><i class="fas fa-search-location"></i><p>Buscar</p></a>
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
                <label for="docident">N°. Documento </label>
                <input type="text" id="docident" name="docident" class="ingreso">
            </div>
            <div>
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" class="ingreso">
            </div>
            <div>
                <label for="cargo">Cargo</label>
                <input type="cargo" id="cargo" name="cargo" class="ingreso">
            </div>
            <div>
                <img src="" id="vistafirma">
            </div>
        </div>
    </div>
    <div class="itemsTabla">
        <table id="tablaPrincipal">
            <thead class="stickytop">
                <tr>
                    <th>Item</th>
                    <th>Codigo</th>
                    <th>Descripcion</th>
                    <th>UND.</th>
                    <th>Cant.</th>
                    <th>Fecha</br>Salida</th>
                    <th>N° Hoja</th>
                    <th>Isometricos</th>
                    <th>Observaciones</th>
                    <th>Serie</th>
                    <th>Patrimonio</th>
                    <th>Estado</th>
                    <th width="20px">Firma</th>
                    <th>...</th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table>
    </div>

    <script src="<?php echo constant('URL');?>public/js/jquery.js"></script>
    <script src="<?php echo constant('URL');?>public/js/repoperso.js"></script>
    <script src="<?php echo constant('URL');?>public/js/funciones.js?<?php echo constant('VERSION')?>"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.4.456/pdf.min.js"></script>
</body>
</html>
