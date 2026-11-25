<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <!-- CSS del Módulo -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo constant('URL'); ?>public/css/bienes.css">
    <link rel="stylesheet" href="<?php echo constant('URL'); ?>public/css/cargoplan.css">
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
            <a href="#" id="repoExcel"><i class="fas fa-file-excel"></i><p>Exportar</p></a>
            <a href="#" id="irInicio"><i class="fas fa-home"></i><p>Inicio</p></a>
        </div>
    </div>
    <div class="barraTrabajo">
        <form action="#" id="formConsulta">
            <div class="filtros-container">
                <div class="filtro-grupo">
                    <label for="nroGuia">Nro. Guía</label>
                    <input type="text" id="guiaSearch" class="form-control" placeholder="Ej. 0157854">
                </div>
                
                <div class="filtro-grupo">
                    <label for="guiaSunat">Guía Sunat</label>
                    <input type="text" id="guiaSunat" class="form-control" placeholder="Ej. 13467">
                </div>
                
                <div class="filtro-grupo">
                    <label for="anio">Año :</label>
                    <input type="number" id="anioSearch" class="form-control" value="2026">
                </div>
                
                <div class="filtro-accion">
                    <button type="button" class="btn-consultar" id="btnConsulta">Consultar</button>
                </div>
            </div>
        </form>
    </div>
    <div class="itemsTabla">
        <table id="tablaPrincipal">
            <thead class="stickytop">
                <tr>
                    <th data-titulo="guia">Num. Guia</th>
                    <th data-titulo="envio" width="50px">F. Envio</th>
                    <th data-titulo="anio" width="50px">Año</th>
                    <th data-titulo="observaciones" width="45%">Observaciones</th>
                    <th data-titulo="sunat" width="50px">Guia Sunat</th>
                    <th data-titulo="transporte" width="50px">Tipo Transporte</th>
                </tr>
            </thead>
            <tbody id="tablaPrincipalCuerpo">

            </tbody>
        </table>
    </div>
    <div class="paginador_cp">
        <div class="pagination">
        
        </div>
    </div>
    <script src="<?php echo constant('URL');?>public/js/jquery.js"></script>
    <script src="<?php echo constant('URL');?>public/js/funciones.js?<?php echo constant('VERSION')?>"></script>
    <script src="<?php echo constant('URL');?>public/js/reporteguias.js?<?php echo constant('VERSION')?>"></script>
</body>
</html>