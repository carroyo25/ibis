<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Guias de Transporte</title>
</head>
<body>
    <div class="mensaje">
        <p></p>
    </div>
    <div class="modal" id="vistaAdjuntos">
        <div class="ventanaAdjuntos">
            <div class="tituloAdjuntos">
                <h3>Adjuntos Orden</h3>
                <a href="#" id="closeAtach" title="Cerrar Ventana"><i class="fas fa-window-close" style="pointer-events:none;"></i></a>
            </div>
            <ul id="listaAdjuntos">

            </ul>
            <iframe src="" id="pdfPreview"></iframe>
        </div>
    </div>
    <div class="cabezaModulo">
        <h1>Reporte Guias de Transporte</h1>
        <div>
            <a href="#" id="reporteExcel"><i class="fas fa-file-excel" style="pointer-events:none"></i><p style="pointer-events:none">Reporte</p></a>
            <a href="#" id="irInicio"><i class="fas fa-home" style="pointer-events:none"></i><p>Inicio</p style="pointer-events:none"></a>
        </div>
    </div>
    <div class="barraTrabajo">
        <form action="#" id="formConsulta">
            <div class="variasConsultas5items">
                    <div>
                        <label for="tipo">Nro. Orden</label>
                        <input type="text" id="ordenSearch" name="ordenSearch">
                    </div>
                    <div>
                        <label for="costosSearch">Centro de Costos: </label>
                        <select name="costosSearch" id="costosSearch">
                            <?php echo $this->listaCostosSelect ?>
                        </select>
                    </div>
                    <div>
                        <label for="descripSearch">Descripcion: </label>
                        <input type="text" id="descripSearch" name="descripSearch">
                    </div>
                    <div>
                        <label for="nroPedido">Pedido</label>
                        <input type="text" id="nroPedido" name="nroPedido">
                    </div>
                    <div>
                        <label for="anio">Año :</label>
                        <input type="number" name="anioSearch" id="anioSearch" value="<?php echo date("Y")?>" class="textoCentro">
                    </div>
                    <button type="button" class="boton3" id="btnConsulta">Consultar</button> 
            </div>
        </form>
    </div>
    <div class="itemsTabla">
        <table id="tablaPrincipal">
            <thead class="stickytop">
                <tr>
                    <th>N° Orden</th>
                    <th>N° Pedido</th>
                    <th>Año</th>
                    <th>Codigo</th>
                    <th>Codigo Costos</th>
                    <th>Descripcion</th>
                </tr>
            </thead>
            <tbody id="tablaPrincipalCuerpo">

            </tbody>
        </table>
    </div>
    <script src="<?php echo constant('URL');?>public/js/jquery.js"></script>
    <script src="<?php echo constant('URL');?>public/js/exceljs.min.js"></script>
    <script src="<?php echo constant('URL');?>public/js/funciones.js?<?php echo constant('VERSION')?>"></script>
    <script src="<?php echo constant('URL');?>public/js/repotransporte.js?<?php echo constant('VERSION')?>"></script>
</body>
</html>