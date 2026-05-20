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
    <div class="modal" id="vistaprevia">
        <div class="ventanaVistaPrevia">
            <div class="tituloVista">
                <h3>Vista Previa</h3>
                <a href="#" id="closePreview" title="Cerrar Ventana"><i class="fas fa-window-close cerrar_vista"></i></a>
            </div>
            <iframe src="" id="pdfPreview"></iframe>
        </div>
    </div>
    <div class="cabezaModulo">
        <h1>Reporte de Guias</h1>
        <div>
            <a href="#" id="nuevoRegistro"><i class="far fa-file"></i><p>Nuevo</p></a>
            <a href="#" id="irInicio"><i class="fas fa-home"></i><p>Inicio</p></a>
        </div>
    </div>
    <div class="barraTrabajo">
        <form action="#" id="formConsulta">
            <div class="variasConsultas">
                    <div>
                        <label for="tipo">Nro. Guia</label>
                        <input type="text" id="guiaSearch" name="">
                    </div>
                    <div>
                        <label for="costosSearch">Centro de Costos: </label>
                        <select name="costosSearch" id="costosSearch" class="item4" disabled>
                            <?php echo $this->listaCostosSelect ?>
                        </select>
                    </div>
                    <div>
                        <label for="mes">Guia Sunat</label>
                        <input type="text" id="guiaSunat" name="">
                    </div>
                    <div>
                        <label for="anio">Año :</label>
                        <input type="number" name="anioSearch" id="anioSearch" value="<?php echo date("Y")?>" class="textoCentro">
                    </div>
                    <button type="button" class="boton3" id="btnConsulta">Consultar</button> 
            </div>
        </form>
    </div>
    <div class="itemsTablaReporte">
        <table id="tablaPrincipal">
            <thead class="stickytop">
                <tr>
                    <th>Num. Guia</th>
                    <th>F. Envio</th>
                    <th>Año</th>
                    <th>Guia</br>Sunat</th>
                    <th>Tipo Transporte</th>
                    <th width="45%">Observaciones</th>
                </tr>
            </thead>
            <tbody id="tablaPrincipalCuerpo">

            </tbody>
        </table>
    </div>
    <div class="paginator_container">
        <div class="paginador_wrap" id="paginador">
            
        </div>
    </div>
    
    <script src="<?php echo constant('URL');?>public/js/jquery.js"></script>
    <script src="<?php echo constant('URL');?>public/js/funciones.js?<?php echo constant('VERSION')?>"></script>
    <script src="<?php echo constant('URL');?>public/js/reporteguias.js?<?php echo constant('VERSION')?>"></script>
</body>
</html>