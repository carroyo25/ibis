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
    <div class="modal" id="vistraTrazable">
        <div class="ventanaBusqueda w50por">
            <div class="tituloVentana">
                    <span id="tituloBusqueda">Seguimiento</span>
                    <div>
                        <a href="#" id="closeTrazable">
                            <i class="fas fa-window-close"></i>
                        <span>Cerrar</span>
                    </a>
                </div>
            </div>
            <div class="datosResumen">
                <table id="tbl_pedidos" class="tabla oculto">
                    <thead>
                        <tr>
                            <th>Nro.</th>
                            <th>Cant.Aprobada</th>
                            <th>Cant.Pedida</th>
                            <th>Elaborado</th>
                            <th>Aprobado</th>
                            <th>Area</th>
                            <th>Fecha Emision</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
                <table id="tbl_ordenes" class="tabla oculto">
                    <thead>
                        <tr>
                            <th>Numero</th>
                            <th>Fecha Emisión</th>
                            <th>Cantidad</th>
                            <th>Area</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                <table id="tbl_ingresos" class="tabla oculto">
                    <thead>
                        <tr>
                            <th>Nro.Nota</th>
                            <th>Guia. Proveedor</th>
                            <th>Proveedor</th>
                            <th>Cantidad</th>
                            <th>Emisión</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
                <table id="tbl_despachos" class="tabla oculto">
                    <thead>
                        <tr>
                            <th>Nro.Nota</th>
                            <th>Emisión</th>
                            <th>Envio</th>
                            <th>Guia.Interna</th>
                            <th>Guia.Sunat</th>
                            <th>Cantidad</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
                <table id="tbl_almacen" class="tabla oculto">
                    <thead>
                        <tr>
                            <th>Nro.Nota</th>
                            <th>Emisión</th>
                            <th>Guia.Interna/Nota Transferencia</th>
                            <th>Recepciona</th>
                            <th>Cantidad</th>
                            <th>Guia.Sunat</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
                <table id="tbl_consumos" class="tabla oculto">
                    <thead>
                        <tr class="stickytop">
                            <th>Nro.Kardex</th>
                            <th>Emitido</th>
                            <th>Cantidad</th>
                            <th>Despacho</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
                <table id="tbl_devolucion" class="tabla oculto">
                    <thead>
                        <tr>
                            <th>Nro.Kardex</th>
                            <th>Emitido</th>
                            <th>Cantidad</th>
                            <th>Despacho</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
                <table id="tbl_inventario" class="tabla oculto">
                    <thead>
                        <tr>
                            <th>Nro. Operacion</th>
                            <th>Emitido</th>
                            <th>Cantidad</th>
                            <th>Registrado</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
                <table id="tbl_transferencias" class="tabla oculto">
                    <thead>
                        <tr>
                            <th>Nota Transferencia</th>
                            <th>Guia Interna</th>
                            <th>Origen</th>
                            <th>Destino</th>
                            <th>Cantidad</th>
                            <th>...</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="modal" id="registroStock">
        <div class="ventanaPregunta">
            <h3>Registrar Stock Minimo</h3>
            <div> 
                <input type="number" id="stockMin">
                <hr>
            </div>
            <div>
                <button type="button" id="btnAceptarStock">Aceptar</button>
                <button type="button" id="btnCancelarStock">Cancelar</button>
            </div>
        </div>
    </div>
    <div class="cabezaModulo">
        <h1>Control de Activos</h1>
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