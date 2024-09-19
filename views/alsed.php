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
    <div class="dashBoardJefeAlmacen">
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
                        <p>Ordenes Aprobadas</p>
                        <p id="pedidos_emitidos">0</p>
                        <p>ultimo emitido: 02/08/2022</p>
                    </div>
                </div>
                <div class="area1_section">
                    <div>
                        <span><i class="far fa-edit"></i></span>
                    </div>
                    <div>
                        <p>Transferencia</p>
                        <p>0</p>
                        <p>ultimo registrada: 3</p>
                    </div>
                </div>
                <div class="area1_section">
                    <div>
                        <span><i class="fas fa-file-signature"></i></span>
                    </div>
                    <div>
                        <p>Registros de Ingreso</p>
                        <p>0</p>
                        <p>ultimo emitida: 15/04/2022</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="area2">
            <div class="titulo">
                <p>Equipos Asignados</p>
            </div>
            <div class="dashboard_table">
                <table class="tabla" id="tablaPanelAsignaciones">
                   <thead>
                       <tr>
                           <th>Item</th>
                           <th>Proyecto</th>
                           <th>Nro.Documento</th>
                           <th>Equipo</th>
                           <th>Serie</th>
                           <th>Fecha Salida</th>
                           <th>...</th>
                       </tr>
                   </thead>
                   <tbody>
                   </tbody>
                </table>
            </div>
        </div>
        <div class="area3">
            <div class="titulo">
                <p>Listado Ordenes</p>
            </div>
            <div class="dashboard_table">
                <table class="tabla" id="tablaPanelOrdenes">
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
        <div class="area4">
            <div class="titulo">
                <p>Listado Pedidos</p>
            </div>
            <div class="dashboard_table">
                <table class="tabla" id="tablaPanelPedidos">
                   <thead class="stickytop">
                       <tr>
                           <th>Nro. Pedido</th>
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

</body>
</html>