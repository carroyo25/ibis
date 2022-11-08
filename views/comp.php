<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div class="modal" id="vistaprevia">
        <div class="ventanaVistaPrevia">
            <div class="tituloVista">
                <h3>Vista Previa</h3>
                <a href="#" id="closePreview" title="Cerrar Ventana"><i class="fas fa-window-close"></i></a>
            </div>
            <iframe src=""></iframe>
        </div>
    </div>
    <div class="dashBoard">
        <div class="area1">
            <div class="titulo">
                <p>Resumen General</p>
            </div>
            <div class="area1_description">
                <div class="area1_section">
                    <div>
                        <i class="fas fa-dolly"></i>
                    </div>
                    <div>
                        <p>Ordenes Emitidas</p>
                        <p id="pedidos_emitidos">1</p>
                        <p>ultimo emitido: 02/08/2022</p>
                    </div>
                </div>
                <div class="area1_section">
                    <div>
                        <span><i class="far fa-edit"></i></span>
                    </div>
                    <div>
                        <p>Ordenes Aprobadas</p>
                        <p>0</p>
                        <p>ultimo aprobado: -</p>
                    </div>
                </div>
                <div class="area1_section">
                    <div>
                        <span><i class="fas fa-file-signature"></i></span>
                    </div>
                    <div>
                        <p>Ordenes Culminados</p>
                        <p>0</p>
                        <p>ultimo culminada: 15/04/2022</p>
                    </div>
                </div>
            </div>
            <div class="area1_command">
                <button type="button"><i class="fas fa-list-ol"></i>  Ver Resumen</button>
            </div>
        </div>
        <div class="area2">
            <div id="dashboard_table">
                <table class="tabla" id="tablaPanelOrdenes">
                    <caption>Ordenes</caption>
                    <thead>
                        <tr>
                            <th>Nro</th>
                            <th>Concepto</th>
                            <th>Emision</th>
                            <th>Centro Costos</th>
                            <th>Estado</th>
                            <th>L</th>
                            <th>O</th>
                            <th>F</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="area3">
            <caption>Pedidos</caption>
            <div id="dashboard_table">
                <table class="tabla" id="tablaPanelPedidos">
                   <thead>
                       <tr>
                           <th>Nro</th>
                           <th>Concepto</th>
                           <th>Emision</th>
                           <th>Centro Costos</th>
                           <th>Estado</th>
                       </tr>
                   </thead>
                   <tbody>
                   </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="<?php echo constant('URL');?>public/js/jquery.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <script src="<?php echo constant('URL');?>public/js/chart.js"></script>

</body>
</html>