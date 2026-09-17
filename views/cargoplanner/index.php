<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="content-type" content="application/vnd.ms-excel; charset=UTF-8">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="<?php echo constant('URL'); ?>public/css/cargoplan.css">
    <title>Document</title>
    <style>
        :root {
            /* Primary Colors */
            --color-primary-main: #5A3FE1;
            --color-primary-hover: #3314C7;
            --color-primary-pressed: #200F70;
            --color-primary-focus: #AC9FF0;
            --color-primary-border: #C4BAF5;
            --color-primary-secondary: #EFEDFA;
        
            /* Gray Colors */
            --color-gray-100: #ECECEC;
            --color-gray-200: #D8D8D8;
            --color-gray-300: #C5C5C5;
            --color-gray-400: #B1B1B1;
            --color-gray-500: #9E9E9E;
            --color-gray-600: #7E7E7E;
            --color-gray-700: #5F5F5F;
            --color-gray-800: #3F3F3F;
            --color-gray-900: #202020;
        }
        .pagination {
            text-align: center;
            margin-top: 20px;
            margin-bottom: 20px;
        }
        
        .pagination button {
            padding: 5px 10px;
            margin: 5px 5px;
            cursor: pointer;
            outline: 1px solid var(--color-primary-main);
            color: var(--color-primary-main);
            border-radius: 4px;
            border: none;
            background-color: var(--color-primary-secondary);
        }
        
        .hidden {
            clip: rect(0 0 0 0);
            clip-path: inset(50%);
            height: 1px;
            overflow: hidden;
            position: absolute;
            white-space: nowrap;
            width: 1px;
        }
        
        .pagination button.active {
            background-color: var(--color-primary-main);
            color: white;
        }
    </style>
</head>
<body>
    <div class="mensaje">
        <p></p>
    </div>
    <div class="modal" id="esperarCargo">
        <div class="ventanaEsperaCargoPlan">
            <div id="display">00:00:00</div>
            <h2 id="waitMessage">Espere... Procesando consulta</h2>
            <progress id="excelProcces" value="0">50%</progress>
        </div>
    </div>
    <div class="cp-modal-overlay" id="cpModal">
        <div class="cp-modal-container">
            <!-- HEADER -->
            <div class="cp-modal-header">
                <h3><i class="fas fa-clipboard-list"></i> Detalle Cargo Plan</h3>
                <button class="cp-modal-close" id="cpCerrar">&times;</button>
            </div>

            <!-- BODY -->
            <div class="cp-modal-body">
                <!-- ===== INFO PRODUCTO ===== -->
                <div class="cp-info-card">
                    <div class="cp-card-title">
                        <i class="fas fa-box"></i> Información del Producto
                    </div>
                    <div class="cp-grid cp-grid-producto">
                        <div class="cp-field">
                            <label>Código</label>
                            <div class="cp-value" id="codigo"></div>
                        </div>
                        <div class="cp-field">
                            <label>Descripción</label>
                            <div class="cp-value" id="producto"></div>
                        </div>
                        <div class="cp-field">
                            <label>Unidad</label>
                            <div class="cp-value" id="unidad"></div>
                        </div>
                        <div class="cp-field">
                            <label>Cant. Solicitada</label>
                            <div class="cp-value" id="cantidad"></div>
                        </div>
                        <div class="cp-field">
                            <label>Estado</label>
                            <div class="cp-value">
                                <span class="cp-badge cp-badge-30" id="estado"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ===== INFO PEDIDO ===== -->
                <div class="cp-info-card">
                    <div class="cp-card-title">
                        <i class="fas fa-file-invoice"></i> Información del Pedido
                    </div>
                    <div class="cp-grid cp-grid-pedido">
                        <div class="cp-field">
                            <label>N° Pedido</label>
                            <div class="cp-value" id="nropedido"></div>
                        </div>
                        <div class="cp-field">
                            <label>Tipo</label>
                            <div class="cp-value" id="tipo_pedido"></div>
                        </div>
                        <div class="cp-field">
                            <label>Fecha Emisión</label>
                            <div class="cp-value" id="emision_pedido"></div>
                        </div>
                        <div class="cp-field">
                            <label>Fecha Aprobación</label>
                            <div class="cp-value" id="aprobacion_pedido"></div>
                        </div>
                        <div class="cp-field">
                            <label>Aprobado por</label>
                            <div class="cp-value" id="aprobado_por"></div>
                        </div>
                    </div>
                    <button class="cp-pdf-pedido" title="Ver documento del pedido">
                        <i class="fas fa-file-pdf" id=""></i>
                    </button>
                </div>

                <!-- ===== SECCIÓN: ORDENES ===== -->
                <div class="cp-seccion">
                    <div class="cp-seccion-header">
                        <span class="cp-seccion-titulo">
                            <i class="fas fa-shopping-cart"></i> Órdenes
                        </span>
                        <span class="cp-seccion-count">1</span>
                    </div>
                    <table class="cp-tabla">
                        <thead>
                            <tr>
                                <th>Nro. Orden</th>
                                <th>Fecha</th>
                                <th>Proveedor</th>
                                <th>CC</th>
                                <th width="50">PDF</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="5" class="cp-vacio">Sin registros</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- ===== SECCIÓN: INGRESOS ===== -->
                <div class="cp-seccion">
                    <div class="cp-seccion-header">
                        <span class="cp-seccion-titulo">
                            <i class="fas fa-arrow-down"></i> Ingresos
                        </span>
                        <span class="cp-seccion-count">0</span>
                    </div>
                    <table class="cp-tabla">
                        <thead>
                            <tr>
                                <th>Nro. Ingreso</th>
                                <th>Fecha Ingreso</th>
                                <th>Guía Proveedor</th>
                                <th width="50">PDF</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="4" class="cp-vacio">Sin registros</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- ===== SECCIÓN: DESPACHOS ===== -->
                <div class="cp-seccion">
                    <div class="cp-seccion-header">
                        <span class="cp-seccion-titulo">
                            <i class="fas fa-truck"></i> Despachos
                        </span>
                        <span class="cp-seccion-count">0</span>
                    </div>
                    <table class="cp-tabla">
                        <thead>
                            <tr>
                                <th>Nro. Salida</th>
                                <th>Fecha Salida</th>
                                <th>Nro. Guía</th>
                                <th>Nro. Referido</th>
                                <th width="50">PDF</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="5" class="cp-vacio">Sin registros</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- ===== SECCIÓN: REGISTROS OBRA ===== -->
                <div class="cp-seccion">
                    <div class="cp-seccion-header">
                        <span class="cp-seccion-titulo">
                            <i class="fas fa-hard-hat"></i> Registros Obra
                        </span>
                        <span class="cp-seccion-count">0</span>
                    </div>
                    <table class="cp-tabla">
                        <thead>
                            <tr>
                                <th>Nro. Nota</th>
                                <th>Fecha Ingreso</th>
                                <th width="50">PDF</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="3" class="cp-vacio">Sin registros</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="modal" id="filtros">
        <div class="ventanaFiltros">
            <div   div class="tituloDocumento">
                <div>
                    <p class="titulo_seccion"><strong> Filtros Avanzados : </strong></p>
                </div>
                <div>
                    <a href="#" id="closeFilters" title="Cerrar Ventana"><i class="fas fa-window-close"></i></a>
                </div>
            </div>
            <hr>
            <div class="cuerpoDocumento">
                <div class="proyectos">
                    <ul class="listaCostos" id="costos">
                        
                    </ul>
                </div>
                <div class="fechas">
                    <div>
                        <label for="desde">Fecha Inicio:</label>
                        <input type="date" name="fecha_inicio" id="fecha_inicio">
                    </div>
                    <div>
                        <label for="desde">Fecha Final:</label>
                        <input type="date" name="fecha_final" id="fecha_final">
                    </div>
                </div>
                <div class="porcentajes">
                </div>
                <div class="opciones">
                    <button type="button" id="btnAceptarFiltro" class="boton5">Aceptar</button>
                    <button type="button" id="btnCancelarFiltro" class="boton5">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal" id="vistaprevia">
        <div class="ventanaVistaPrevia">
            <div class="tituloVista">
                <h3>Vista Previa</h3>
                <a href="#" id="closePreview" title="Cerrar Ventana"><i class="fas fa-window-close"></i></a>
            </div>
            <iframe src=""></iframe>
        </div>
    </div>
    <div class="modal" id="vistaAdjuntos">
        <div class="ventanaAdjuntos">
            <div class="tituloAdjuntos">
                <h3>Adjuntos Orden</h3>
                <a href="#" id="closeAtach" title="Cerrar Ventana"><i class="fas fa-window-close"></i></a>
            </div>
            <ul id="listaAdjuntos">

            </ul>
            <iframe src=""></iframe>
        </div>
    </div>
    <div class="leyenda-modal-overlay" id="leyendaModal">
        <div class="leyenda-modal-container">
            <!-- HEADER -->
            <div class="leyenda-modal-header">
                <h3><i class="fas fa-palette"></i> Leyenda de Estados</h3>
                <button class="leyenda-modal-close" id="leyendaCerrar">&times;</button>
            </div>

            <!-- BODY -->
            <div class="leyenda-modal-body">
                <div class="leyenda-grid" id="leyendaGrid">

                    <div class="leyenda-item" data-estado="0">
                        <span class="leyenda-color" style="background:#D3D3D3;"></span>
                        <span class="leyenda-texto">0% - Anulado</span>
                    </div>

                    <div class="leyenda-item" data-estado="10">
                        <span class="leyenda-color" style="background:#F5DCC0;"></span>
                        <span class="leyenda-texto">10% - Creado</span>
                    </div>

                    <div class="leyenda-item" data-estado="15">
                        <span class="leyenda-color" style="background:#FF0000;"></span>
                        <span class="leyenda-texto">15% - Aprob.</span>
                    </div>

                    <div class="leyenda-item" data-estado="20">
                        <span class="leyenda-color" style="background:#B0C4DE;"></span>
                        <span class="leyenda-texto">20% - Stock</span>
                    </div>

                    <div class="leyenda-item leyenda-alerta" data-estado="25">
                        <span class="leyenda-color" style="background:#FFFF00;"></span>
                        <span class="leyenda-texto">25% - OC/OS</span>
                        <i class="fas fa-exclamation-triangle leyenda-icono"></i>
                    </div>

                    <div class="leyenda-item" data-estado="30">
                        <span class="leyenda-color" style="background:#D4E8D4;"></span>
                        <span class="leyenda-texto">30% - Enviado</span>
                    </div>

                    <div class="leyenda-item" data-estado="40">
                        <span class="leyenda-color" style="background:#B4D4B4;"></span>
                        <span class="leyenda-texto">40% - Ing. Parcial</span>
                    </div>

                    <div class="leyenda-item" data-estado="50">
                        <span class="leyenda-color" style="background:#96C896;"></span>
                        <span class="leyenda-texto">50% - At. Total</span>
                    </div>

                    <div class="leyenda-item" data-estado="60">
                        <span class="leyenda-color" style="background:#FF00FF;"></span>
                        <span class="leyenda-texto">60% - Com. Local</span>
                    </div>

                    <div class="leyenda-item" data-estado="70">
                        <span class="leyenda-color" style="background:#FFA500;"></span>
                        <span class="leyenda-texto">70% - P. Gerencia</span>
                    </div>

                    <div class="leyenda-item" data-estado="75">
                        <span class="leyenda-color" style="background:#00FFFF;"></span>
                        <span class="leyenda-texto">75% - Transito</span>
                    </div>

                    <div class="leyenda-item" data-estado="85">
                        <span class="leyenda-color" style="background:#F5F5DC;"></span>
                        <span class="leyenda-texto">85% - Rec. Parcial</span>
                    </div>

                    <div class="leyenda-item" data-estado="100">
                        <span class="leyenda-color" style="background:#00FF00;"></span>
                        <span class="leyenda-texto">100% - Obra</span>
                    </div>

                </div>
            </div>

            <!-- FOOTER -->
            <div class="leyenda-modal-footer">
                <button class="leyenda-btn leyenda-btn-secondary" id="leyendaCerrarBtn">Cerrar</button>
            </div>

        </div>
    </div>
    <div class="cabezaModulo">
        <h1>Cargo Plan</h1>
        <div>
            <a href="#" id="filtrosAvanzados"><i class="fab fa-searchengin"></i><p>Filtros</p></a>
            <a href="1" id="excelFile" class="exportReport"><i class="fas fa-file-excel"></i><p>Exportar Excel</p></a>
            <a href="2" id="csvFile" class="exportReport oculto"><i class="fas fa-file-csv"></i><p>Exportar CSV</p></a>
            <a href="3" id="excelJS" class="exportFast oculto"><i class="fas fa-file-excel"></i><p>Exportar Total Rapido</p></a>
            <a href="#" id="verAyuda"><i class="far fa-question-circle"></i><p>Mostrar Ayuda</p></a> 
            <a href="#" id="irInicio"><i class="fas fa-home"></i><p>Inicio</p></a>
        </div>
    </div>
    <div class="barraTrabajo">
        <form action="#" id="formConsulta">
            <input type="hidden" name="estado_item" id="estado_item">
            <div class="variasConsultasColumna">
                <div class="datosConsultaCargoPlan">
                    <div class="parametrosConsulta">
                        <div>
                            <label for="tipo">Tipo : </label>
                            <select name="tipoSearch" id="tipoSearch">
                                <option value="-1">Seleccione una opcion</option>
                                <option value="37">Bienes</option>
                                <option value="38">Servicios</option>
                            </select>    
                        </div>
                        <div>
                            <label for="costosSearch">Centro de Costos </label>
                            <select name="costosSearch" id="costosSearch">
                                <?php echo $this->listaCostos ?>
                            </select>
                        </div>
                        <div>
                            <label for="codigo">Codigo:</label>
                            <input type="text" name="codigoSearch" id="codigoSearch" class="textoCentro">
                        </div>
                        <div>
                            <label for="ordenSearch">N° Orden :</label>
                            <input type="text" name="ordenSearch" id="ordenSearch">
                        </div>
                        <div>
                            <label for="ordenSearch">N° Pedido :</label>
                            <input type="text" name="pedidoSearch" id="pedidoSearch">
                        </div>
                        <div>
                            <label for="descripSearch">Descripción Item:</label>
                            <input type="text" name="descripSearch" id="descripSearch">
                        </div>
                        <div>
                            <label for="conceptoSearch">Concepto : </label>
                            <input type="text" name="conceptoSearch" id="conceptoSearch">
                        </div>
                         <div>
                            <label for="anioSearch">Año : </label>
                            <input type="text" name="anioSearch" id="anioSearch">
                        </div>
                    </div>
                    
                </div>
                <div class="botonesConsulta">
                    <button type="button" id="btnProcesa">Procesar</button>
                    <button type="button" id="btnExporta">Exportar</button>
                </div>
            </div>
        </form>
    </div>
    <div class="itemsCargoPlanner" id="demo" style="overflow: scroll;">
        <table id="cargoPlanDescrip">
            <thead>
                <tr class="stickytop">
                    <th width="30px">Items</th>
                    <th style="background:#40D1FB; color:#000; position:relative" data-idcol="1" class="datafiltro">Estado</br>Actual</th>
                    <th style="background:#40D1FB; color:#000">Codigo</br>Proyecto</th>
                    <th style="background:#40D1FB; color:#000" data-idcol="3" class="datafiltro">Area</th>
                    <th style="background:#40D1FB; color:#000" data-idcol="4" class="datafiltro">Partida</th>
                    <th style="background:#40D1FB; color:#000">Atencion</th>
                    <th style="background:#40D1FB; color:#000" data-idcol="6" class="datafiltro">Tipo</th>
                    <th style="background:#FBD341; color:#000">Año</br> Pedido</th>
                    <th style="background:#FBD341; color:#000" data-idcol="8" class="datafiltro">N°</br>Pedido</th>
                    <th style="background:#FBD341; color:#000" width="80px">Creación</br>Pedido</th>
                    <th style="background:#FBD341; color:#000" width="80px">Aprobación</br>Pedido</th>
                    <th style="background:#FBD341; color:#000">Cantidad</br>Pedida</th>
                    <th style="background:#FBD341; color:#000">Cantidad</br>Aprobada</th>
                    <th style="background:#FBD341; color:#000">Cantidad</br>para compra</th>
                    <th style="background:#A6CAF0; color:#000" data-idcol="12" class="datafiltro">Codigo del</br>Bien/Servicio</th>
                    <th style="background:#A6CAF0; color:#000">Unidad</br>Medida</th>
                    <th style="background:#A6CAF0; color:#000" width="10%" data-idcol="14" class="datafiltro">Descripcion del Bien/Servicio</th>
                    <th style="background:#AAFFAA; color:#000" width="40px">Tipo</br>Orden</th>
                    <th style="background:#AAFFAA; color:#000" width="50px">Año</br>Orden</th>
                    <th style="background:#AAFFAA; color:#000" data-idcol="17" class="datafiltro">N°</br>Orden</th>
                    <th style="background:#AAFFAA; color:#000">Fecha</br>Orden</th>
                    <th style="background:#AAFFAA; color:#000">Cantidad</br>Orden</th>
                    <th style="background:#AAFFAA; color:#000">Item</br>Orden</th>
                    <th style="background:#AAFFAA; color:#000">Fecha</br>Autorización</th>
                    <th>Atencion</br>Almacen</th>
                    <th style="background:#AB7FAB; color:#fff" width="10%" data-idcol="22" class="datafiltro">Descripcion Proveedor</th>
                    <th>Fecha Entrega</br>Proveedor</th>
                    <th width="50px">Cantidad</br>Recibida</th>
                    <th width="50px" data-idcol="25" class="datafiltro">Nota</br>Ingreso</th>
                    <th width="50px">Fecha</br>Recepcion Proveedor</th>
                    <th>Saldo por</br>Recibir</th>
                    <th width="50px">Días</br>Entrega</th>
                    <th>Días</br>Atrazo</th>
                    <th>Semaforo</th>
                    <th style="background:#25AFF3; color:#000">Cantidad</br>Enviada</th>
                    <th style="background:#25AFF3; color:#000" data-idcol="31" class="datafiltro">Nro. Guia</th>
                    <th style="background:#25AFF3; color:#000" data-idcol="32" class="datafiltro">Nro. Guia Sunat</th>
                    <th style="background:#25AFF3; color:#000" data-idcol="33" class="datafiltro">Fecha Envio</th>
                    <th style="background:#127BDD; color:#000">N°. Nota</br>Transferencia</th>
                    <th style="background:#127BDD; color:#000">Fecha</br>Traslado</th>
                    <th style="background:#DA500B; color:#000">Registro</br>Almacen</th>
                    <th style="background:#DA500B; color:#000">Fecha</br>Ingreso Almacen</th>
                    <th style="background:#DA500B; color:#000">Cantidad</br>Recibida</br>Obra</th>
                    <th>Estado</br>Pedido</th>
                    <th>Estado</br>Item</th>
                    <th data-idcol="41" class="datafiltro">N°</br>Parte</th>
                    <th width="150px" data-idcol="37" class="datafiltro">Codigo</br>Activo</th>
                    <th>Operador</br>Logístico</th>
                    <th>Tipo</br>Transporte</th>
                    <th data-idcol="42" class="datafiltro">Observaciones/Concepto</th>
                    <th data-idcol="43" class="datafiltro">Solicitante</th>
                    <th data-idcol="44" class="datafiltro" style="background:#819830; color:#000">Pedido Asignado</th>
                    <th data-idcol="45" style="background:#819830; color:#000">Fecha Descarga </br>Orden</th>
                    <th data-idcol="45" style="background:#C7CBD1; color:#000">INCOTERM</th>
                    <th data-idcol="45" style="background:#C7CBD1; color:#000">PUNTO DE ENTREGA</th>
                    <th data-idcol="45" style="background:#C7CBD1; color:#000">FECHA DE COMPROMISO</th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table>
    </div>
    <script src="<?php echo constant('URL');?>public/js/exceljs.min.js"></script>
    <script src="<?php echo constant('URL');?>public/js/jquery.js"></script>
    <script src="<?php echo constant('URL');?>public/js/funciones.js?<?php echo constant('VERSION')?>"></script>
    <script src="<?php echo constant('URL');?>public/js/cargoplanner.js?<?php echo constant('VERSION')?>"></script>

</body>
</html>