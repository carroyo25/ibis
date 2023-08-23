<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div class="modal" id="proceso">
        <div class="ventanaProceso tamanioProceso">
            <div class="cabezaProceso">
                <form action="#" id="formProceso" autocomplete="off">
                    <input type="hidden" name="codigo_costos" id="codigo_costos"> 
                    <input type="hidden" name="codigo_area" id="codigo_area">
                    <input type="hidden" name="codigo_transporte" id="codigo_transporte" value="39">
                    <input type="hidden" name="codigo_solicitante" id="codigo_solicitante">
                    <input type="hidden" name="codigo_tipo" id="codigo_tipo">
                    <input type="hidden" name="codigo_partida" id="codigo_partida">
                    <input type="hidden" name="codigo_pedido" id="codigo_pedido">
                    <input type="hidden" name="codigo_estado" id="codigo_estado" value="49">
                    <input type="hidden" name="codigo_verificacion" id="codigo_verificacion">
                    <input type="hidden" name="dias_atencion" id="dias_atencion" value="10">
                    <input type="hidden" name="codigo_atencion" id="codigo_atencion" value="47">
                    <input type="hidden" name="vista_previa" id="vista_previa">
                    <input type="hidden" name="emitido" id="emitido">
                    <input type="hidden" name="transporte" id="transporte" value="">

                    <div class="barraOpciones primeraBarra">
                        <span>Datos Generales</span>
                        <div>
                            <button type="button" id="save" title="Grabar Cambios" class="boton3">
                                <i class="fas fa-save"></i> Grabar
                            </button>
                            <button type="button" id="preview" title="Ver Documento" class="boton3">
                                <i class="fab fa-wpexplorer"></i> Vista Documento
                            </button>
                            <button type="button" id="addAtach" title="Añadir Adjunto" class="boton3">
                                <i class="fas fa-paperclip"></i> Añadir Adjunto
                            </button>
                            <button type="button" id="closeProcess" title="Cerrar" class="boton3">
                                <i class="fas fa-window-close"></i>
                            </button>
                        </div>
                    </div>
                    <div class="dataProceso_2">
                        <div class="seccion_izquierda">
                            <div class="column4_55">
                                <div class="column2_3457">
                                    <label for="numero">Número:</label>
                                    <input type="text" name="numero" id="numero" class="cerrarLista" readonly>
                                </div>
                                <div class="column2_46">
                                    <label for="emision">Emisión:</label>
                                    <input type="date" name="emision" id="emision" class="cerrarLista" value="">
                                </div>
                            </div>
                            <div class="column2">
                                <label for="costos">CCostos:</label>
                                <input type="text" name="costos" id="costos" class="mostrarLista busqueda">
                            </div>
                            <div class="column2">
                                <label for="area">Partida:</label>
                                <input type="text" name="partida" id="partida" class="mostrarLista busqueda">
                            </div>
                        </div>
                        <div class="seccion_medio">
                            <div class="column2">
                                <label for="area">Area:</label>
                                <input type="text" name="area" id="area" class="mostrarLista busqueda">
                            </div>
                            <div class="column2">
                                <label for="concepto">Concepto:</label>
                                <input type="text" name="concepto" id="concepto" class="cerrarLista">
                            </div>
                            <div class="column2">
                                <label for="solicitante">Solicitante:</label>
                                <input type="text" name="solicitante" id="solicitante" class="mostrarLista" readonly>
                            </div>
                        </div>
                        <div class="seccion_derecha">
                            <div class="column4_55">
                                <div class="column2_3957">
                                    <label for="tipo">Tipo :</label>
                                    <input type="text" name="tipo" id="tipo" class="mostrarLista" readonly>
                                </div>
                                <div class="column2_46">
                                    <label for="vence">Vence :</label>
                                    <input type="date" name="vence" id="vence" readonly>
                                </div>
                            </div>
                            <div class="column4_55">
                                <div class="column2_3957">
                                    <label for="pedidommto">Ped. MMTO:</label>
                                    <input type="text" name="pedidommto" id="pedidommto">
                                </div>
                                <div class="column2_46">
                                    <label for="transporte">Transporte:</label>
                                    <input type="text" name="transporte" id="transporte">
                                </div>
                            </div>
                            <div class="column2">
                                <label for="estado">Estado:</label>
                                <input type="text" name="estado" id="estado" class="textoCentro w35por estado procesando" readonly value="EN PROCESO">
                            </div>
                        </div>
                    </div>
                    <div class="textAreaEnter oculto">
                        <textarea name="espec_items" id="espec_items" rows="2" class="w100p" readonly></textarea>
                    </div>
                    <div class="barraOpciones">
                        <span>Detalles</span>
                        <div>
                            <button type="button" id="btnAnular" title="Añadir Item" class="cerrarLista boton4">
                                <i class="fas fa-ban"></i> Anular
                            </button>
                            <button type="button" id="btnRetornar" title="Retornar Proceso" class="cerrarLista boton3">
                                <i class="fas fa-undo-alt"></i> Retornar Proceso
                            </button>
                            <button type="button" id="btnAgregar" title="Añadir Item" class="cerrarLista boton3">
                                <i class="fas fa-plus-square"></i> Añadir Item
                            </button>
                        </div>
                    </div>
                    <div class="tablaInterna mininoTablaInterna">
                        <table class="tabla" id="tablaDetalles">
                            <thead class="stickytop">
                                <tr>
                                    <th width="3%">...</th>
                                    <th width="5%">Item</th>
                                    <th width="8%">Codigo</th>
                                    <th>Descripcion</th>
                                    <th width="5%">Und.</th>
                                    <th width="6%">Cant.</th>
                                    <th width="20%">Especificaciones</th>
                                    <th>Nro. Parte</th>
                                    <th>Bien/Activo</th>
                                    <th width="6%">Atendido</br>Almacen</th>
                                    <th>...</th>
                                    <th>...</th>
                                    <th>...</th>
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
    <div class="modal" id="esperar">
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
    <div class="modal" id="preguntaAnula">
        <div class="ventanaPregunta">
            <h3>¿Anular el pedido?</h3>
            <div>
                <button type="button" id="btnAceptarAnula">Aceptar</button>
                <button type="button" id="btnCancelarAnula">Cancelar</button>
            </div>
        </div>
    </div>
    <div class="modal" id="preguntaProceso">
        <div class="ventanaPregunta">
            <h3>¿Restaurar el pedido?</h3>
            <div>
                <button type="button" id="btnAceptarProceso">Aceptar</button>
                <button type="button" id="btnCancelarProceso">Cancelar</button>
            </div>
        </div>
    </div>
    <div class="modal" id="preguntaItemBorra">
        <div class="ventanaPregunta">
            <h3>¿Eliminar el item?</h3>
            <div>
                <button type="button" id="btnAceptarEliminaItem">Aceptar</button>
                <button type="button" id="btnCancelarEliminaItem">Cancelar</button>
            </div>
        </div>
    </div>
    <div class="modal" id="preguntaItemLibera">
        <div class="ventanaPregunta">
            <h3>¿Liberar el item?</h3>
            <div>
                <button type="button" id="btnAceptarLiberaItem">Aceptar</button>
                <button type="button" id="btnCancelarLiberaItem" class="claseCancela">Cancelar</button>
            </div>
        </div>
    </div>
    <div class="modal" id="busqueda">
        <div class="ventanaBusqueda w50por">
            <div class="tituloVentana">
                <span id="tituloBusqueda">Catálogo Bienes/Servicios</span>
                <div>
                    <a href="#"><i class="fas fa-window-close"></i></a>
                </div>
            </div>
            <div class="textoBusqueda">
                <input type="text" name="txtBuscarCodigo" id="txtBuscarCodigo" placeholder="Buscar Codigo">
                <input type="text" name="txtBuscarDescrip" id="txtBuscarDescrip" placeholder="Buscar Descripción">
            </div>
            <div class="tablaBusqueda">
                <table class="tabla " id="tablaModulos">
                    <thead >
                        <tr class="stickytop">
                            <th width="10%">Codigo</th>
                            <th>Descripcion</th>
                            <th>Und.</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="cabezaModulo">
        <h1>Editar Pedidos(Administrador)</h1>
        <div>
            <a href="#" id="irInicio"><i class="fas fa-home"></i><p>Inicio</p></a>
        </div>
    </div>
    <div class="barraTrabajo">
        <form action="#" id="formConsulta">
            <div class="variasConsultas">
                    <div>
                        <label for="numeroSearch">Numero : </label>
                        <input type="text" name="numeroSearch" id="numeroSearch">
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
                    <button type="button" id="btnProceso">Procesar</button> 
            </div>
        </form>
    </div>
    <div class="itemsTabla">
        <table id="tablaPrincipal">
            <thead class="stickytop">
                <tr>
                    <th>Num.</th>
                    <th>Interno</th>
                    <th>Emisión</th>
                    <th>Tipo</th>
                    <th width="35%">Descripción</th>
                    <th>Centro Costos</th>
                    <th>Resposable</th>
                    <th>Estado</th>
                    <th>Atencion</th>
                </tr>
            </thead>
            <tbody>
                <?php echo $this->listaPedidos;?>
            </tbody>
        </table>
    </div>
    <script src="<?php echo constant('URL');?>public/js/jquery.js"></script>
    <script src="<?php echo constant('URL');?>public/js/funciones.js?<?php echo constant('VERSION')?>"></script>
    <script src="<?php echo constant('URL');?>public/js/pedidoedit.js?<?php echo constant('VERSION')?>"></script>
</body>
</html>