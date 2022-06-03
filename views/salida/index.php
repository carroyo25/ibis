<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div class="mensaje">
        <p></p>
    </div>
    <div class="modal" id="pregunta">
        <div class="ventanaPregunta">
            <h3>Desea eliminar el registro?</h3>
            <div>
                <button type="button" id="btnAceptarPregunta">Aceptar</button>
                <button type="button" id="btnCancelarPregunta">Cancelar</button>
            </div>
        </div>
    </div>
    <div class="modal" id="proceso">
        <div class="ventanaProceso tamanioProceso">
            <div class="cabezaProceso">
                <form action="#" id="formProceso" autocomplete="off">
                    <input type="hidden" name="codigo_costos" id="codigo_costos"> 
                    <input type="hidden" name="codigo_area" id="codigo_area">
                    <input type="hidden" name="codigo_movimiento" id="codigo_movimiento">
                    <input type="hidden" name="codigo_aprueba" id="codigo_aprueba">
                    <input type="hidden" name="codigo_almacen" id="codigo_almacen">
                    <input type="hidden" name="codigo_pedido" id="codigo_pedido">
                    <input type="hidden" name="codigo_orden" id="codigo_orden">
                    <input type="hidden" name="codigo_estado" id="codigo_estado">
                    <input type="hidden" name="codigo_moneda" id="codigo_moneda">
                    <input type="hidden" name="correo_entidad" id="correo_entidad">
                    <input type="hidden" name="codigo_ingreso" id="codigo_ingreso">
                    <input type="hidden" name="codigo_salida" id="codigo_salida" value="1">

                    <div class="barraOpciones primeraBarra">
                        <span>Datos Generales</span>
                        <div>
                            <button type="button" id="saveOrden" title="Grabar Nota" class="boton3">
                                <span><i class="far fa-save"></i> Grabar </span> 
                            </button>
                            <button type="button" id="importData" title="Importar Orden" class="boton3">
                                <i class="fab fa-wpexplorer"></i> Importar Nota
                            </button>
                            <button type="button" id="closeDocument" title="Cerrar Nota" class="boton3">
                                <i class="far fa-comments"></i> Cerrar Nota
                            </button>
                            <button type="button" id="preview" title="Vista Previa" class="boton3">
                                <i class="far fa-file-pdf"></i> Vista Previa
                            </button>
                            <button type="button" id="closeProcess" title="Cerrar" class="boton3">
                                <i class="fas fa-window-close"></i>
                            </button>
                        </div>
                    </div>
                    <div class="dataProceso_2">
                        <div class="seccion_izquierda">
                            <div class="column4_55">
                                <div class="column2_3957">
                                    <label for="Fecha Emite">Fecha :</label>
                                    <input type="date" name="fecha" id="fecha" class="cerrarLista" value="<?php echo date("Y-m-d");?>" readonly>
                                </div>
                                <div class="column2_46">
                                    <label for="numero">Numero :</label>
                                    <input type="text" name="numero" id="numero" class="cerrarLista textoDerecha pr20px" readonly>
                                </div>
                            </div>
                            <div class="column2">
                                <label for="proyecto">Proyecto:</label>
                                <input type="text" name="proyecto" id="proyecto" readonly>
                            </div>
                            <div class="column2">
                                <label for="area">Area:</label>
                                <input type="text" name="area" id="area" readonly>
                            </div>
                            <div class="column2">
                                <label for="solicita">Solicita:</label>
                                <input type="text" name="solicita" id="solicita" class="cerrarLista" readonly>
                            </div>
                            <div class="column2">
                                <label for="aprueba">Aprueba:</label>
                                <input type="text" name="aprueba" id="aprueba" class="mostrarLista busqueda" placeholder="Elija opción" readonly>
                            </div>
                        </div>
                        <div class="seccion_medio">
                            <div class="column2">
                                <label for="almacen">Almacen:</label>
                                <input type="text" name="almacen" id="almacen" class="mostrarLista busqueda" placeholder="Elija opción" readonly>
                            </div>
                            <div class="column4_55">
                                <div class="column2_3957">
                                    <label for="pedido">Nro. Pedido :</label>
                                    <input type="text" name="pedido" id="pedido" class="cerrarLista" readonly>
                                </div>
                                <div class="column2_46">
                                    <label for="fecha_pedido">Fecha Doc. :</label>
                                    <input type="date" name="fecha_pedido" id="fecha_pedido" class="cerrarLista pr5px" readonly>
                                </div>
                            </div>
                            <div class="column4_55">
                                <div class="column2_3957">
                                    <label for="orden">Nro. Orden :</label>
                                    <input type="text" name="orden" id="orden" class="cerrarLista" readonly>
                                </div>
                                <div class="column2_46">
                                    <label for="fecha_orden">Fecha Doc. :</label>
                                    <input type="date" name="fecha_orden" id="fecha_orden" class="cerrarLista" readonly>
                                </div>
                            </div>
                            <div class="column2">
                                <label for="concepto">Concepto:</label>
                                <input type="text" name="concepto" id="concepto" readonly>
                            </div>
                            <div class="column2">
                                <label for="detalle">Detalle:</label>
                                <input type="text" name="detalle" id="detalle" readonly>
                            </div>
                        </div>
                        <div class="seccion_derecha">
                            
                            <div class="column2">
                                <label for="tipo">Tipo Mov.:</label>
                                <input type="text" name="tipo" id="tipo" class="mostrarLista busqueda" placeholder="Elija opción"
                                    readonly>
                                <div class="lista" id="listaMovimiento">
                                   <ul>
                                       <?php echo $this->listaMovimiento?>
                                   </ul> 
                                </div>
                            </div>
                            <div class="column4_55">
                                <div class="column2">
                                    <label for="estado">Estado:</label>
                                    <input type="text" name="estado" id="estado" class="textoCentro estado w100por procesando" readonly value="EN PROCESO">
                                </div>
                                <div class="column2_46">
                                    <label for="items">Nro.Guia :</label>
                                    <input type="text" name="guia" id="guia" class="cerrarLista">
                                </div>
                            </div>
                            <div class="column4_55">
                                <div class="column2">
                                    <label for="movimiento">Mov Almacen:</label>
                                    <input type="text" name="movimiento" id="movimiento" class="w100por" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="barraOpciones">
                        <span>Detalles</span>
                        <div>
                            <button type="button" id="atachDocs" title="Documentos Adjuntos" class="cerrarLista boton3">
                                <i class="fas fa-upload"></i> Documentos Adjuntos
                            </button>
                            <button type="button" id="asocOrd" title="Orden de Compra Asociada" class="cerrarLista boton3">
                                <i class="fas fa-upload"></i> Orden Asociada
                            </button>
                            <button type="button" id="atachDocs" title="Orden de Compra Asociada" class="cerrarLista boton3">
                                <i class="fas fa-upload"></i> Generar Guia Remisión
                            </button>
                        </div>
                    </div>
                    <div class="tablaInterna mininoTablaInterna">
                        <table class="tabla" id="tablaDetalles">
                            <thead>
                                <tr class="stickytop">
                                        <th class="">...</th>
                                        <th class="">Item</th>
                                        <th class="">Codigo</th>
                                        <th class="">Descripcion</th>
                                        <th class="">Unidad</th>
                                        <th width="7%">Cantidad</th>
                                        <th class="">Observaciones</th>
                                        <th class="">Serie</th>
                                        <th class="">Fecha </br> Vencimiento</th>
                                        <th class="">Estado</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal" id="busqueda">
        <div class="ventanaBusqueda w75por">
            <div class="tituloVentana">
                <span id="tituloBusqueda">Pedidos</span>
                <div>
                    <a href="#" id="closeSearch"><i class="fas fa-window-close"></i></a>
                </div>
            </div>
            <div class="textoBusqueda">
                <input type="text" name="txtBuscar" id="txtBuscar" placeholder="Buscar" class="w90por">
                <button type="button" class="boton3" id="btnAceptItems">Aceptar</button>
            </div>
            <div class="tablaBusqueda">
                <table class="tablaWrap" id="ordenes">
                    <thead>
                        <tr class="stickytop" >
                            <th>Orden</th>
                            <th>Emisión</th>
                            <th>Area</th>
                            <th>Centro de Costos</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
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
    <div class="cabezaModulo">
        <h1>Notas de Salida</h1>
        <div>
            <a href="#" id="nuevoRegistro"><i class="far fa-file"></i></a>
            <a href="#" id="irInicio"><i class="fas fa-home"></i></a>
        </div>
    </div>
    <div class="barraTrabajo">
        <form action="#" id="formConsulta">
            <div class="variasConsultas">
                    <div>
                        <label for="tipo">Tipo : </label>
                        <select name="tipoSearch" id="tipoSearch">
                            <option value="37">Bienes</option>
                            <option value="38">Servicios</option>
                        </select>
                    </div>
                    <div>
                        <label for="costosSearch">Centro de Costos</label>
                        <input type="text" name="costosSearch" id="costosSearch">
                    </div>
                    <div>
                        <label for="mes">Mes</label>
                        <input type="number" name="mesSearch" id="mesSearch" value="<?php echo date("m")?>" class="textoCentro">
                    </div>
                    <div>
                        <label for="anio">Año :</label>
                        <input type="number" name="anioSearch" id="anioSearch" value="<?php echo date("Y")?>" class="textoCentro">
                    </div>
                    <button type="button">Procesar</button> 
            </div>
        </form>
    </div>
    <div class="itemsTabla">
        <table id="tablaPrincipal">
            <thead>
                <tr>
                    <th>Num. Nota</th>
                    <th>F.Emisión</th>
                    <th>Registro </br> Mov.Almacen</th>
                    <th>Almacen</th>
                    <th>Centro de Costos</th>
                    <th>Año</th>
                    <th>Orden</th>
                    <th>Guia</br>Proveedor</th>
                    <th>Pedido</th>
                    <th>Guia</br>Remision</th>
                    <th>Detalle</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php echo $this->listaNotasSalidas;?>
            </tbody>
        </table>
    </div>
    <script src="<?php echo constant('URL');?>public/js/jquery.js"></script>
    <script src="<?php echo constant('URL');?>public/js/funciones.js?<?php echo constant('VERSION')?>"></script>
    <script src="<?php echo constant('URL');?>public/js/salida.js?<?php echo constant('VERSION')?>"></script>
</body>
</html>