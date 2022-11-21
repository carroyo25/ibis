<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div class="modal" id="resumen">
        <div class="resumen">

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
                        <p id="ordenes_emitidas"></p>
                        <p>Año: <?php echo date("Y") ?></p>
                    </div>
                </div>
                <div class="area1_section">
                    <div>
                        <span><i class="far fa-edit"></i></span>
                    </div>
                    <div>
                        <p>Ordenes Aprobadas</p>
                        <p id="ordenes_aprobadas">0</p>
                        <p>Año: <?php echo date("Y") ?></p>
                    </div>
                </div>
                <div class="area1_section">
                    <div>
                        <span><i class="fas fa-file-signature"></i></span>
                    </div>
                    <div>
                        <p>Ordenes Pendientes</p>
                        <p id="ordenes_pendientes">0</p>
                        <p>Año: <?php echo date("Y") ?></p>
                    </div>
                </div>
            </div>
            <div class="area1_command">
                <button type="button"><i class="fas fa-list-ol"></i>  Ver Resumen</button>
            </div>
        </div>
        <div class="area2">
            <div class="titulo">
                <div id="container"></div>
            </div>
        </div>
        <div class="area3">
            <div class="titulo">
                <p>Ordenes pendientes de firma</p>
            </div>
            <div id="dashboard_table">
                <div>
                    <table class="tabla" id="tablaPanel">
                    <thead class="stickytop">
                        <tr>
                            <th>N° Orden</th>
                            <th>Concepto</th>
                            <th>Emision</th>
                            <th>Centro Costos</th>
                            <th>Proveedor</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script src="<?php echo constant('URL');?>public/js/jquery.js"></script>
    <script src="<?php echo constant('URL');?>public/js/chart.js?<?php echo constant('VERSION')?>"></script>
    <script src="<?php echo constant('URL');?>public/code/highcharts.js"></script>
    <script src="<?php echo constant('URL');?>public/code/highcharts-3d.js"></script>
    <script src="<?php echo constant('URL');?>public/code/modules/accessibility.js"></script>
</body>
</html>