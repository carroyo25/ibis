<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div class="dashBoard">
        <div class="area1">
            <div class="titulo">
                <p>Resumen General</p>
            </div>
            <div class="area1_description">
                <div class="area1_section">
                    <div>
                        <span><i class="far fa-edit"></i></span>
                    </div>
                    <div>
                        <p>Pedidos Aprobados</p>
                        <p id="pedidos_aprobados"></p>
                        <p>ultimo aprobado: <?php echo date("d/m/Y") ?></p>
                    </div>
                </div>
                <div class="area1_section">
                    <div>
                        <span><i class="fas fa-file-signature"></i></span>
                    </div>
                    <div>
                        <p>Pedidos Pendientes</p>
                        <p id="pedidos_pendientes"></p>
                        <p>ultimo emitido: 15/04/2022</p>
                    </div>
                </div>
            </div>
            <div class="area1_command">
                <button type="button"><i class="fas fa-list-ol"></i>  Ver Resumen</button>
            </div>
        </div>
        <div class="area2">
            <div class="titulo">
            </div>
            <div id="containerPie" class="container"></div>
        </div>
        <div class="area3">
            <div id="dashboard_table">
                <table class="tabla" id="tablaPanel">
                    
                   <thead class="stickytop">
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
    <script src="<?php echo constant('URL');?>public/js/chart.js"></script>
    <script src="<?php echo constant('URL');?>public/code/highcharts.js"></script>
    <script src="<?php echo constant('URL');?>public/code/highcharts-3d.js"></script>
</body>
</html>