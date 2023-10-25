<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div class="modal" id="esperar">
        <div class="loadingio-spinner-spinner-5ulcsi06hlf">
            <div class="ldio-fifgg00y5y">
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
            </div>
        </div>
        <div class="modal" id="vistadocumento">
    </div>
        <div class="ventanaResumen tamanioProceso50">
            <div class="resumen">
                <div class="tituloResumen">
                    <div>
                        <p class="titulo_seccion"><strong> Detalle del Reporte : </strong></p>
                    </div>
                    <div>
                        <a href="#" id="closeDocument" title="Cerrar Ventana"><i class="fas fa-window-close"></i><span> Cerrar</span></a>
                    </div>
                </div>
                <hr>
                <div class="cuerpoResumen">
                   <div class="area1">
                        <label>Codigo</label>
                        <label>:</label>
                        <label id="codigo_item"></label>
                        <label>Descripción</label>
                        <label>:</label>
                        <label id="nombre_item"></label>
                        <hr>
                   </div>
                   <div>
                        <table id="listaVencimientos" class="tablaInfo">
                            <thead>
                                <th width="45%">Observaciones</th>
                                <th>Fecha</br>Compra</th>
                                <th>Numero</br>Orden</th>
                                <th>Fecha</br>Vencimiento</th>
                                <th>Cant.</br>Compra</th>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                   </div>
                </div>
            </div>   
        </div>
    </div>
    <div class="cabezaModulo">
        <h1>Reporte de Vencimiento</h1>
        <div>
            <a href="#" id="excelFile"><i class="fas fa-file-excel"></i><p>Exportar</p></a>
            <a href="#" id="irInicio"><i class="fas fa-home"></i><p>Inicio</p></a>
        </div>
    </div>
    <div class="barraTrabajo">
        <form action="#" id="formConsulta">
            <div class="variasConsultas4campos">
                    <div>
                        <label for="costosSearch">Centro Costos: </label>
                        <select name="costosSearch" id="costosSearch">
                            <?php echo $this->listaCostosSelect ?>
                        </select>
                    </div>
                    <div>
                        <label for="codigoBusqueda">Codigo : </label>
                        <input type="text" name="codigoBusqueda" id="codigoBusqueda">
                    </div>
                    <div>
                        <label for="descripcionSearch">Descripcion: </label>
                        <input type="text" name="descripcionSearch" id="descripcionSearch">
                    </div>
                    <div>
                    </div>
                    <button type="button" id="btnConsulta" class="boton3">Consultar</button> 
            </div>
        </form>
    </div>
    <div class="itemsTabla">
        <table id="tablaPrincipal">
            <thead class="stickytop">
                <tr>
                    <th rowspan="2">Item</th>
                    <th rowspan="2">Centro</br>Costos </th>
                    <th rowspan="2">Codigo</th>
                    <th rowspan="2" width="50%">Descripcion</th>
                    <th rowspan="2">Unidad</th>
                    <th rowspan="2">N° Orden</th>
                    <th rowspan="2">Origen</th>
                    <th>Fecha de </br> Vencimiento</th>
                    <th rowspan="2">Dias</br>Vencido</th>
                    <th>Cantidad</br> Ingresada</th>
                    <th>Cantidad</br> Consumida</th>
                    <th>Saldo</th>
                </tr>
            </thead>
            <tbody>
                <?php echo $this->listaVencimientos; ?>
            </tbody>
        </table>
    </div>
    <script src="<?php echo constant('URL');?>public/js/jquery.js"></script>
    <script src="<?php echo constant('URL');?>public/js/funciones.js?<?php echo constant('VERSION')?>"></script>
    <script src="<?php echo constant('URL');?>public/js/vence.js?<?php echo constant('VERSION')?>"></script>
</body>
</html>