<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div class="modal" id="vistadocumento">
        <div class="ventanaResumen tamanioProceso">
                <div class="tituloDocumento">
                    <div>
                        <p class="titulo_seccion"><strong> Detalle Almacen : </strong></p>
                    </div>
                    <div>
                        <a href="#" id="closeDocument" title="Cerrar Ventana"><i class="fas fa-window-close"></i></a>
                    </div>
                </div>
                <hr>
                <div class="cuerpoResumem">
                   <div class="area1">
                        <label>Codigo</label>
                        <label>:</label>
                        <label id="codigo_item"></label>
                        <label>Descripción del Material</label>
                        <label>:</label>
                        <label id="descripcion_item"></label>
                   </div>
                   <div class="area2">
                        <div>
                            <label>Pedidos Solicitados</label>
                            <label>:</label>
                            <label id="numero_pedidos"></label>
                            <label>Ordenes Solicitadas</label>
                            <label>:</label>
                            <label id="numero_ordenes"></label>
                        </div>
                        <div>
                            <label>Ingreso Inventario</label>
                            <label>:</label>
                            <label id="inventario"></label>
                            <label>Ingresos Almacen</label>
                            <label>:</label>
                            <label id="ingresos"></label>
                            <label>Salidas Cosumo</label>
                            <label>:</label>
                            <label id="consumo"></label>
                            <label>Saldo Actual</label>
                            <label>:</label>
                            <label id="saldo"></label>
                        </div>
                        <div>
                            <label>Pendiente Recibir</label>
                            <label>:</label>
                            <label id="pendiente_compra"></label>
                        </div>
                   </div>
                   <div class="area3">
                        <div>
                            <h4>Precios</h4>
                            <table id="tabla_precios">
                                <thead class="stickytop">
                                    <tr class="pointer">
                                        <th>Fecha</th>
                                        <th>Moneda</th>
                                        <th>T.C</th>
                                        <th>Precio</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                        <div>
                            <h4>Existencias Centro de costos</h4>
                            <table id="tabla_existencias">
                                <thead class="stickytop">
                                    <tr class="pointer">
                                        <th>Centro de Costos</th>
                                        <th>unidad</th>
                                        <th>Ingresos</th>
                                        <th>Salidas</th>
                                        <th>Saldo</th>
                                        <th>Almacen</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                </tbody>
                            </table>
                        </div>
                   </div>
                   <div class="area4">
                        <div>
                            <h4>Ingresos</h4>
                        </div>
                        <div>
                            <h4>Salidas</h4>
                        </div>
                        <div>
                            <h4>Saldos</h4>
                        </div>
                   </div>
                </div>
        </div>
    </div>
    <div class="cabezaModulo">
        <h1>Detalle Valorizado Item</h1>
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
            <thead>
                <tr class="stickytop">
                    <th data-titulo="item">Item</th>
                    <th data-titulo="codigo">Codigo</th>
                    <th data-titulo="descripcion" width="50%">Descripcion</th>
                    <th data-titulo="unidad">Unidad</th>
                    <th data-titulo="moneda">Moneda</th>
                    <th data-titulo="cambio">Tipo<br>Cambio</th>
                    <th data-titulo="fecha">Fecha Orden</th>
                    <th data-titulo="orden">Nro. Orden</th>
                    <th data-titulo="proveedor">Proveedor</th>
                    <th data-titulo="cantidad">Cantidad</th>
                    <th data-titulo="soles">Precio<br/>Unitario S/.</th>
                    <th data-titulo="dolares">Precio<br/>Unitario $</th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table>
    </div>
    <script src="<?php echo constant('URL');?>public/js/jquery.js"></script>
    <script src="<?php echo constant('URL');?>public/js/funciones.js?<?php echo constant('VERSION')?>"></script>
    <script src="<?php echo constant('URL');?>public/js/valitem.js?<?php echo constant('VERSION')?>"></script>
</body>
</html>