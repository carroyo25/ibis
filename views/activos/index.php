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
            <form id="activos_form">
                <input type="hidden" name="codigo_interno" id="codigo_interno">
                <input type="hidden" name="codigo_unidad" id="codigo_unidad">
                <input type="hidden" name="codigo_usuario" id="codigo_usuario">

                <fieldset class="container">
                    <legend>  Datos Generales  </legend>
                    <div class="container_flex_column">
                        <label for="centro_costos">Centro de Costos</label>
                        <select name="centro_costos" id="centro_costos" placeholder="Seleccione un centro de costos">
                             <?php echo $this->listaCostosSelect ?>
                        </select>
                        <div class="item_information">
                            <label for="codigoSearch">Codigo</label>
                            <input type="text" name="codigoSearch" id="codigoSearch" placeholder="Ingrese codigo" value="B030600020003">
                            <label for="descripSearch">Descripcion</label>
                            <input type="text" name="descripSearch" id="descripSearch" readonly>
                        </div>
                    </div>
                </fieldset>

                <fieldset>
                    <legend>Datos de Registro</legend>
                    <div class="container_grid">
                        <div class="form_group">
                            <label for="unidad">Unidad:</label>
                            <input type="text" name="unidad" id="unidad" readonly >
                        </div>
                        <div class="form_group">
                            <label for="cantidad">Cantidad:</label>
                            <input type="text" name="cantidad" id="cantidad" readonly value ="1">
                        </div>
                        <div class="form_group">
                            <label for="serie">Serie:</label>
                            <input type="text" name="serie" id="serie">
                        </div>
                        <div class="form_group">
                            <label for="marca">Marca:</label>
                            <input type="text" name="marca" id="marca">
                        </div>
                        <div class="form_group">
                            <label for="modelo">Modelo:</label>
                            <input type="text" name="modelo" id="modelo">
                        </div>
                    </div>
                </fieldset>
                <fieldset>
                    <legend>Asignación</legend>
                    <div class="container_grid">
                        <div class="form_group">
                            <label for="dni">DNI:</label>
                            <input type="text" name="dni" id="dni" readonly >
                        </div>
                        <div class="form_group">
                            <label for="nombres">Nombres:</label>
                            <input type="text" name="nombres" id="nombres" readonly >
                        </div>
                        <div class="form_group">
                            <label for="cargo">Cargo:</label>
                            <input type="text" name="cargo" id="cargo" readonly >
                        </div>
                        <div class="form_group">
                            <label for="area">Area:</label>
                            <input type="text" name="area" id="area">
                        </div>
                        <div class="form_group">
                            <label for="fecha_asigna">Fecha Asignacion:</label>
                            <input type="date" name="fecha_asigna" id="fecha_asigna">
                        </div>
                    </div> 
                </fieldset>
                 <fieldset>
                    <legend>Calibracion</legend>
                    <div class="container_grid">
                        <div class="form_group">
                            <label for="frecuencia">Frecuencia:</label>
                            <select name="frecuencia" id="frecuencia">
                                <option value="anual">Anual</option>
                                <option value="semestral">Semestral</option>
                            </select>
                        </div>
                        <div class="form_group">
                            <label for="fecha_calibra">Fecha Calibracion:</label>
                            <input type="date" name="fecha_calibra" id="fecha_calibra">
                        </div>
                        <div class="form_group">
                            <label for="vence_calibra">Vmto. Calibracion:</label>
                            <input type="date" name="vence_calibra" id="vence_calibra">
                        </div>
                        <div class="form_group">
                            <label for="estado_actual">Estado Actual:</label>
                            <input type="text" name="estado_actual" id="estado_actual" readonly >
                        </div>
                        <div class="form_group">
                            <label for="observa_estado">Observaciones:</label>
                            <input type="text" name="observa_estado" id="observa_estado" readonly >
                        </div>
                    </div>
                </fieldset>
                 <fieldset>
                    <legend>Envio/Recepción</legend>
                     <div class="container_grid">
                        <div class="form_group">
                            <label for="guia_envio">Guia Envio:</label>
                            <input type="text" name="guia_envio" id="guia_envio">
                        </div>
                        <div class="form_group">
                            <label for="fecha_envio">Fecha Envio:</label>
                            <input type="date" name="fecha_envio" id="fecha_envio">
                        </div>
                        <div class="form_group">
                            <label for="guia_recepcion">Guia Recepción:</label>
                            <input type="text" name="guia_recepcion" id="guia_recepcion">
                        </div>
                        <div class="form_group">
                            <label for="fecha_recepcion">Fecha Recepción:</label>
                            <input type="date" name="fecha_recepcion" id="fecha_recepcion">
                        </div>
                        <div class="form_group">
                            <label for="ubicacion">Ubicación Actual:</label>
                            <input type="text" name="ubicacion" id="ubicacion">
                        </div>
                    </div>
                </fieldset>
                <fieldset>
                    <legend>Ubicacion</legend>
                    <div class="container_grid">
                        <div class="form_group">
                            <label for="contenedor">Contenedor:</label>
                            <input type="text" name="contenedor" id="contenedor">
                        </div>
                        <div class="form_group">
                            <label for="estante">Estante:</label>
                            <input type="text" name="estante" id="estante">
                        </div>
                        <div class="form_group">
                            <label for="letra">Letra :</label>
                            <input type="text" name="letra" id="letra">
                        </div>
                        <div class="form_group">
                            <label for="columna">Columna:</label>
                            <input type="text" name="columna" id="columna">
                        </div>
                    </div>
                </fieldset>

                <div class="opciones_dialogo botones_derecha">
                    <button type="button" id="btQrDialogoActivos"><i class="fas fa-qrcode"></i> Crear QR</button>
                    <button type="button" id="btnAtachDialogoActivos"><i class="far fa-images"></i> Adjuntar Fotos</button>
                    <button type="button" id="btnGrabarDialogoActivos"><i class="fas fa-save"></i> Grabar</button>
                    <button type="button" id="btnCancelarDialogoActivos"><i class="fas fa-window-close"></i> Cancelar</button>
                </div>
            </form>
        </div>
    </div>
    <div class="modal" id="registros">
        <div class="ventanaBusqueda w50por">
            <div class="tituloVentana">
                <span id="tituloBusqueda">Ingreso de Productos(Registros)</span>
                <div>
                    <a href="#" id="closeSearch" class="closeModal"><i class="fas fa-window-close"></i></a>
                </div>
            </div>
            <div class="textoBusqueda">
                <input type="text" name="txtBuscar" id="txtBuscar" placeholder="Buscar" class="w90por">
                <button type="button" class="boton3 closeModal" id="btnAceptItems">Aceptar</button>
            </div>
            <div class="tablaBusqueda">
                <table class="tablaWrap" id="ingresos">
                    <thead>
                        <tr class="stickytop" >
                            <th>Nro. Ingreso</th>
                            <th>Costos/Proyecto</th>
                            <th>Cantidad</th>
                            <th>Pedido</th>
                        </tr>
                    </thead>
                    <tbody id="cuerpo_ingresos">

                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="modal" id="inventarios">
        <div class="ventanaBusqueda w50por">
            <div class="tituloVentana">
                <span id="tituloBusqueda">Ingreso de Productos(Inventarios)</span>
                <div>
                    <a href="#" id="closeSearch" class="closeModal"><i class="fas fa-window-close"></i></a>
                </div>
            </div>
            <div class="textoBusqueda">
                <input type="text" name="txtBuscar" id="txtBuscar" placeholder="Buscar" class="w90por">
                <button type="button" class="boton3 closeModal" id="btnAceptItems">Aceptar</button>
            </div>
            <div class="tablaBusqueda">
                <table class="tablaWrap" id="ingresos">
                    <thead>
                        <tr class="stickytop" >
                            <th>Nro. Inventario</th>
                            <th>Fecha Inventario</th>
                            <th>Costos/Proyecto</th>
                            <th>Cantidad</th>
                            <th>Serie</th>
                            <th>Ubicacion</th>
                        </tr>
                    </thead>
                    <tbody id="cuerpo_inventarios">

                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="cabezaModulo">
        <h1>Control de Activos</h1>
        <div>
            <a href="#" id="nuevoRegistro"><i class="far fa-file"></i><p>Nuevo</p></a>
            <a href="#" id="importXls"><i class="fas fa-file-import"></i><p>Importar</p></a>
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