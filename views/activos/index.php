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
        <div class="ventanaResumen">
            <div class="resumen">
                <div class="tituloResumen">
                    <div>
                        <p class="titulo_seccion"><strong> Detalle Almacen : </strong></p>
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
                        <label id="descripcion_item"></label>
                   </div>
                   <div class="action_tab">
                        <button type="button" class="tab_button" data-tab="tab1">Detalles</button>
                        <button type="button" class="tab_button tab_inactivo" data-tab="tab2">Stocks Minimos</button>
                        <button type="button" class="tab_button tab_inactivo" data-tab="tab3">Precios</button>
                        <button type="button" class="tab_button tab_inactivo" data-tab="tab4">Kardex</button>
                   </div>
                   <div class="body_tab">
                        <div class="tab" id="tab1">
                            <div class="info_tab1">
                                <table id="tabla1_tab1">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>N°</th>
                                            <th>Cantidad</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr data-categoria="pedidos" class="report_process">
                                            <td>Pedidos</td>
                                            <td class="textoDerecha"></td>
                                            <td class="textoDerecha"></td>
                                        </tr>
                                        <tr data-categoria="ordenes" class="report_process">
                                            <td>Ordenes</td>
                                            <td class="textoDerecha">0</td>
                                            <td class="textoDerecha">0</td>
                                        </tr>
                                        <tr data-categoria="ingresos" class="report_process">
                                            <td>Ingresos</td>
                                            <td class="textoDerecha">0</td>
                                            <td class="textoDerecha">0</td>
                                        </tr>
                                        <tr data-categoria="despachos" class="report_process">
                                            <td>Despachos</td>
                                            <td class="textoDerecha">0</td>
                                            <td class="textoDerecha">0</td>
                                        </tr>
                                        <tr data-categoria="registros" class="report_process">
                                            <td>Registro Obra</td>
                                            <td class="textoDerecha">0</td>
                                            <td class="textoDerecha">0</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="info_tab1">
                                <table id="tabla2_tab1">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>N°</th>
                                            <th>Cantidad</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr data-categoria="consumos" class="report_process">
                                            <td>Consumos</td>
                                            <td class="textoDerecha"></td>
                                            <td class="textoDerecha"></td>
                                        </tr>
                                        <tr data-categoria="devoluciones" class="report_process">
                                            <td>Devoluciones</td>
                                            <td class="textoDerecha"></td>
                                            <td class="textoDerecha"></td>
                                        </tr>
                                        <tr data-categoria="inventarios" class="report_process">
                                            <td>Registro Inventario</td>
                                            <td class="textoDerecha"></td>
                                            <td class="textoDerecha"></td>
                                        </tr>
                                        <tr data-categoria="transferencias" class="report_process">
                                            <td>Transferencias</td>
                                            <td class="textoDerecha"></td>
                                            <td class="textoDerecha"></td>
                                        </tr>
                                        <tr>
                                            <!--<td colspan="2"><strong>Saldo</strong></td>
                                            <td class="textoDerecha"></td>-->
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab oculto" id="tab2">
                            <table id="tabla1_tab2">
                                <thead>
                                    <tr class="stickytop">
                                        <th>Fecha</th>
                                        <th>Cantidad</th>
                                        <th>Usuario</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                        <div class="tab oculto" id="tab3">
                            <table id="tabla1_tab3">
                                <thead>
                                    <tr class="stickytop">
                                        <th>Fecha</th>
                                        <th>Moneda</th>
                                        <th>Tipo Cambio</th>
                                        <th>Precio</th>
                                        <th>Orden</th>
                                        <th>CC</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                        <div class="tab oculto" id="tab4">

                        </div>
                   </div>
                </div>
            </div>   
        </div>
    </div>
    <div class="modal" id="windowUpdate">
        <div class="ventanaPregunta">
            <h1>Actualizar Registro</h1>
            <div class="windowUpdateBody">
                
            </div>
        </div>
    </div>
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
    </div>
    <div class="modal" id="dialogo_registro">
        <div class="ventanaActivos">
            <h3>Registrar Activos/Equipos</h3>
            <div class="contenedor">
                <div class="cabecera_activo">
                    <div class="container_flex">
                        <label for="centro_costos">Centro de Costos</label>
                        <select name="centro_costos" id="centro_costos" placeholder="Seleccione un centro de costos">
                        </select>
                    </div>
                    <div class="container_grid">
                        <label for="codigoSearch">Codigo</label>
                        <input type="text" name="codigoSearch" id="codigoSearch" placeholder="Ingrese codigo">
                        <label for="descripSearch">Descripcion</label>
                        <input type="text" name="descripSearch" id="descripSearch" placeholder="Ingrese Descripcion">
                    </div>
                </div>
                <div class="opciones_dialogo">
                    <button type="button" id="btnAddItem"><i class="fas fa-tasks"></i> Agregar Item</button>
                    <button type="button" id="btnSearchIn"><i class="fas fa-stream"></i> Buscar Inventario</button>
                </div>
                <div class="tabla_dialogo">
                    <table id="tabla_detalles_activos" class="tabla">
                        <thead class="stickytop">
                            <tr>
                                <th>Codigo</th>
                                <th>Descripcion</th>
                                <th>Und.</th>
                                <th>Cant.</th>
                                <th>Registro/Inventario</th>
                                <th>Estado</th>
                                <th>Asignado</th>
                                <th>Fecha Calibracion</th>
                                <th>Vencimiento</th>
                                <th>Observaciones</th>
                                <th>...</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
                <div class="opciones_dialogo botones_derecha">
                    <button type="button" id="btnGrabarDialogoActivos"><i class="fas fa-save"></i> Grabar</button>
                    <button type="button" id="btnCancelarDialogoKardex"><i class="fas fa-window-close"></i> Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="cabezaModulo">
        <h1>Control de Activos</h1>
        <div>
            <a href="#" id="nuevoRegistro"><i class="far fa-file"></i><p>Nuevo</p></a>
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
                    <th rowspan="2">Codigo</th>
                    <th rowspan="2" width="50%">Descripcion</th>
                    <th rowspan="2">Unidad</th>
                    <th rowspan="2">Cantidad<br>Ingreso</th>
                    <th rowspan="2">Ingreso<br>Inventario</th>
                    <th rowspan="2">Cantidad<br>Salida</th>
                    <th rowspan="2">Cantidad<br>Devolucion</th>
                    <th rowspan="2">Salida Transfer.</th>
                    <th rowspan="2">Ajuste</th>
                    
                    <th rowspan="2">Cantidad<br>Minima</th>
                    <th rowspan="2">Saldo</th>
                    <th colspan="9">Condicion</th>
                </tr>
                <tr>
                    <th>1A</th>
                    <th>1B</th>
                    <th>2A</th>
                    <th>2B</th>
                    <th>3A</th>
                    <th>3B</th>
                    <th>3C</th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table>
    </div>
    <script src="<?php echo constant('URL');?>public/js/jquery.js"></script>
    <script src="<?php echo constant('URL');?>public/js/funciones.js?<?php echo constant('VERSION')?>"></script>
    <script src="<?php echo constant('URL');?>public/js/activos.js?<?php echo constant('VERSION')?>"></script>
</body>
</html>