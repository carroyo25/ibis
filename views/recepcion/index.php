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
                    <input type="hidden" name="codigo_entidad" id="codigo_entidad">
                    <input type="hidden" name="codigo_moneda" id="codigo_moneda">
                    <input type="hidden" name="correo_entidad" id="correo_entidad">
                    <input type="hidden" name="codigo_verificacion" id="codigo_verificacion">
                    <input type="hidden" name="codigo_recepcion" id="codigo_recepcion">
                    <input type="hidden" name="codigo_ingreso" id="codigo_ingreso" value="1">
                    <input type="hidden" name="detalle" id="detalle">

                    <div class="barraOpciones primeraBarra">
                        <span>Datos Generales</span>
                        <div>
                            <button type="button" id="cancelRegister" title="Anular Nota de Ingreso" class="boton4">
                                <i class="fas fa-ban"></i> Anular Nota
                            </button>
                            <button type="button" id="importData" title="Importar Orden" class="boton3">
                                <i class="fab fa-wpexplorer"></i> Importar Orden
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
                            <div class="column2">
                                <label for="almacen">Almacen:</label>
                                <input type="text" name="almacen" id="almacen" class="mostrarLista busqueda" placeholder="Elija opción"
                                    readonly>
                            </div>
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
                        </div>
                        <div class="seccion_medio">
                            <div class="column4_55">
                                <div class="column2_3957">
                                    <label for="orden">Nro.Orden :</label>
                                    <input type="text" name="orden" id="orden" class="cerrarLista" readonly>
                                </div>
                                <div class="column2_46">
                                    <label for="pedido">Nro.Pedido :</label>
                                    <input type="text" name="pedido" id="pedido" class="cerrarLista pr5px" readonly>
                                </div>
                            </div>
                            <div class="column4_55">
                                <div class="column2_3957">
                                    <label for="ruc">Nro. Ruc :</label>
                                    <input type="text" name="ruc" id="ruc" class="cerrarLista" readonly>
                                </div>
                                <div class="column2_46">
                                    <label for="guia">Guia Remisión :</label>
                                    <input type="text" name="guia" id="guia" class="cerrarLista">
                                </div>
                            </div>
                            <div class="column2">
                                <label for="razon">Razón Social:</label>
                                <input type="text" name="razon" id="razon" readonly>
                            </div>
                            <div class="column2">
                                <label for="concepto">Concepto:</label>
                                <input type="text" name="concepto" id="concepto" readonly>
                            </div>
                            <div class="column2">
                                    <label for="aprueba">Aprueba:</label>
                                    <input type="text" name="aprueba" id="aprueba" class="mostrarLista busqueda" placeholder="Elija opción"
                                        readonly>
                                    <div class="lista" id="listaAprueba">
                                    <ul>
                                        <?php echo $this->listaAprueba?>
                                    </ul> 
                                    </div>
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
                                    <label for="items">Nro. Items :</label>
                                    <input type="text" name="items" id="items" class="cerrarLista">
                                </div>
                            </div>
                            <div class="column4_55">
                                <div class="column2">
                                    <label for="movimiento">Mov Almacen:</label>
                                    <input type="text" name="movimiento" id="movimiento" class="w100por" readonly>
                                </div>
                                <div class="column2">
                                    <input type="checkbox" name="qaqc" id="qaqc" class="cerrarLista ml60px">
                                    <label for="qaqc">Verificar Calidad</label>
                                </div>
                            </div>
                            <div class="opcionesProceso">
                                <a id="btnPendientes" class="boton3"><i class="far fa-square"></i> Grabar Pendientes</a>
                                <a id="btnTotales" class="boton3"><i class="far fa-check-square"></i> Grabar Marcadas</a>
                            </div>
                        </div>
                    </div>
                    <div class="barraOpciones">
                        <span>Detalles</span>
                        <div>
                            <button type="button" id="atachDocs" title="Documentos Adjuntos" class="cerrarLista boton3">
                                <i class="fas fa-upload"></i> Ver Adjuntos
                            </button>
                        </div>
                    </div>
                    <div class="tablaInterna mininoTablaInterna">
                        <table class="tabla" id="tablaDetalles">
                            <thead>
                                <tr class="stickytop">
                                        <th width="5%"> - </th>
                                        <th width="5%"> ... </th>
                                        <th class="">Item</th>
                                        <th class="">Codigo</th>
                                        <th class="">Descripcion</th>
                                        <th class="">Unidad</th>
                                        <th width="7%">Cantidad</br>Orden</th>
                                        <th width="7%">Cantidad</br>Pendiente</th>
                                        <th class="">Observaciones</th>
                                        <th class="">N° Parte</th>
                                        <th width="5%">...</th>
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
        <div class="ventanaBusqueda w60por">
            <div class="tituloVentana">
                <span id="tituloBusqueda">Ordenes</span>
                <div>
                    <a href="#" id="closeSearch"><i class="fas fa-window-close"></i></a>
                </div>
            </div>
            <div class="textoBusquedaGrid">
                <input type="text" name="ordenSearch" id="ordenSearch" placeholder="Orden">
                <select name="itemCostos" id="itemCostos">
                    <?php echo $this->listaCostosSelect ?>
                </select>
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
                            <th>Proveedor</th>
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
    <div class="modal" id="pregunta">
        <div class="ventanaPregunta">
            <h3>¿Desea eliminar el registro?</h3>
            <div>
                <button type="button" id="btnAceptarPregunta">Aceptar</button>
                <button type="button" id="btnCancelarPregunta">Cancelar</button>
            </div>
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
    <div class="cabezaModulo">
        <h1>Notas Ingreso</h1>
        <div>
            <a href="#" id="nuevoRegistro"><i class="far fa-file"></i><p>Nuevo</p></a>
            <a href="#" id="irInicio"><i class="fas fa-home"></i><p>Inicio</p></a>
        </div>
    </div>
    <div class="barraTrabajo">
        <form action="#" id="formConsulta">
            <div class="variasConsultas">
                    <div>
                        <label for="tipo">N°. Orden</label>
                        <input type="text" id="ordenSearch" name="ordenSearch">
                    </div>
                    <div>
                        <label for="costosSearch">Centro de Costos: </label>
                        <select name="costosSearch" id="costosSearch" class="item4">
                            <?php echo $this->listaCostosSelect ?>
                        </select>
                    </div>
                    <div>
                        <label for="mes">Mes</label>
                        <select name="mesSearch" id="mesSearch">
                            <option value="-1">Mes</option>
                            <option value="1">Enero</option>
                            <option value="2">Febrero</option>
                            <option value="3">Marzo</option>
                            <option value="4">Abril</option>
                            <option value="5">Mayo</option>
                            <option value="6">Junio</option>
                            <option value="7">Julio</option>
                            <option value="8">Agosto</option>
                            <option value="9">Setiembre</option>
                            <option value="10">Octubre</option>
                            <option value="11">Noviembre</option>
                            <option value="12">Diciembre</option>
                        </select>
                    </div>
                    <div>
                        <label for="anio">Año :</label>
                        <input type="number" name="anioSearch" id="anioSearch" value="<?php echo date("Y")?>" class="textoCentro">
                    </div>
                    <button type="button" class="boton3" id="btnConsulta">Consultar</button> 
            </div>
        </form>
    </div>
    <div class="itemsTabla">
        <table id="tablaPrincipal">
            <thead class="stickytop">
                <tr>
                    <th>Num. Guia</th>
                    <th>F.Emisión</th>
                    <th>Nro. Ingreso</th>
                    <th>Almacen</th>
                    <th>Proyecto/Sede/Costos</th>
                    <th>Area</th>
                    <th>Proveedor</th>
                    <th>Orden</th>
                    <th>Pedido</th>
                </tr>
            </thead>
            <tbody>
                <?php echo $this->listaNotasIngreso;?>
            </tbody>
        </table>
    </div>
    <div class="modal" id="series">
        <div class="ventanaArchivos">
            <table id="tablaSeries" class="tabla">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Serie</th>
                    </tr>
                </thead>
                <tbody>
                    
                </tbody>
            </table>
            </br>
            <div class="opcionesArchivos">
                <button type="button" class="boton3" id="btnConfirmSeries">Aceptar</button>
                <button type="button" class="boton3" id="btnCancelSeries">Cancelar</button>
            </div>
        </div>
    </div>
    <script src="<?php echo constant('URL');?>public/js/jquery.js"></script>
    <script src="<?php echo constant('URL');?>public/js/funciones.js?<?php echo constant('VERSION')?>"></script>
    <script src="<?php echo constant('URL');?>public/js/recepcion.js?<?php echo constant('VERSION')?>"></script>
</body>
</html>