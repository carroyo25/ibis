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
    <div class="modal" id="dialogo">
        <div class="ventanaPregunta">
            <h3>Ingrese Codigo</h3>
            <div>
                <input type="text" name="codigoSearch" id="codigoSearch">
            </div>
            <div>
                <button type="button" id="btnAceptarDialogo">Aceptar</button>
                <button type="button" id="btnCancelarDialogo">Cancelar</button>
            </div>
        </div>
    </div>
    <div class="cabezaModulo">
        <h1>Reporte de Productos - Serie</h1>
        <div>
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
                <label for="serie">N°. Serie </label>
                <input type="text" id="serie" name="serie" class="ingreso">
            </div>
            <div>
                <label for="descripcion">Descripción </label>
                <input type="text" id="descripcion" name="descripcion" class="ingreso">
            </div>
        </div>
    </div>
    <div class="itemsTabla">
        <table id="tablaPrincipal">
            <thead class="stickytop">
                <tr>
                    <th>Item</th>
                    <th>CCs</th>
                    <th>Codigo</th>
                    <th width="30%">Descripcion</th>
                    <th>UND.</th>
                    <th>Cant.</th>
                    <th>Fecha</br>Salida</th>
                    <th>Fecha</br>Devolución</th>  
                    <th>N° Hoja</th>
                    <th>Isometricos</th>
                    <th>Observaciones</th>
                    <th>Serie</th>
                    <th>Patrimonio</th>
                    <th>Estado</th>
                    <th width="20px">Firma</th>
                </tr>
            </thead>
        </table>
    </div>
    <script src="<?php echo constant('URL');?>public/js/jquery.js"></script>
    <script src="<?php echo constant('URL');?>public/js/series.js"></script>
    <script src="<?php echo constant('URL');?>public/js/funciones.js?<?php echo constant('VERSION')?>"></script>

</body>
</html>
