<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <div class="mensaje">
        <p></p>
    </div>
    <div class="modal" id="esperar">
    </div>
    <div class="modal" id="proceso">
        <div class="ventanaProceso tamanioProceso">
            <div class="cabezaProceso">
                <form action="#" id="formProceso" autocomplete="off">
                    <input type="hidden" name="codigo_costos_origen" id="codigo_costos_origen">
                    <input type="hidden" name="codigo_costos_destino" id="codigo_costos_destino"> 
                    <input type="hidden" name="codigo_area" id="codigo_area">
                    <input type="hidden" name="codigo_tipo" id="codigo_tipo">
                    <input type="hidden" name="codigo_tipo_transferencia" id="codigo_tipo_transferencia">
                    <input type="hidden" name="codigo_solicitante" id="codigo_solicitante">
                    <input type="hidden" name="codigo_origen" id="codigo_origen">
                    <input type="hidden" name="codigo_destino" id="codigo_destino">
                    <input type="hidden" name="vista_previa" id="vista_previa">
                    <input type="hidden" name="emitido" id="emitido">
                    <input type="hidden" name="codigo_usuario" id="codigo_usuario">
                    <input type="hidden" name="codigo_autoriza" id="codigo_autoriza">
                    <input type="hidden" name="codigo_traslado" id="codigo_traslado">
                    <input type="hidden" name="correo_usuario" id="correo_usuario">
                    <input type="hidden" name="codigo_estado" id="codigo_estado">
                    <input type="hidden" name="estado_autorizacion" id="estado_autorizacion">
                    <input type="hidden" name="codigo_traslado" id="codigo_traslado">
                    <input type="hidden" name="motivo_guia" id="motivo_guia" value="95">

                    <div class="barraOpciones primeraBarra">
                        <span>Datos Generales</span>
                        <div>
                            
                            <button type="button" id="authorizeDocument" title="Autorizar Traslado" class="boton3">
                                <p><i class="far fa-save"></i> Autorizar Traslado</p> 
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
                                    <label for="numero">Transferencia:</label>
                                    <input type="text" name="numero" id="numero" class="cerrarLista" readonly>
                                </div>
                                <div class="column2_46">
                                    <label for="emision">Emisión:</label>
                                    <input type="date" name="emision" id="emision" class="cerrarLista" value="<?php echo date("Y-m-d");?>">
                                </div>
                            </div>
                            <div class="column2">
                                <label for="costosOrigen">CCostos Origen:</label>
                                <input type="text" name="costosOrigen" id="costosOrigen" class="mostrarLista busqueda" placeholder="Elija una opcion">
                                <div class="lista" id="listaCostosOrigen">
                                   <ul>
                                       <?php echo $this->listaCostos?>
                                   </ul> 
                                </div>
                            </div>
                            <div class="column2">
                                <label for="costosDestino">CCostos Destino:</label>
                                <input type="text" name="costosDestino" id="costosDestino" class="mostrarLista busqueda" placeholder="Elija una opcion">
                                <div class="lista" id="listaCostosDestino">
                                   <ul>
                                       <?php echo $this->listaCostos?>
                                   </ul> 
                                </div>
                            </div>
                            <div class="column2">
                                <label for="area">Area:</label>
                                <input type="text" name="area" id="area" class="mostrarLista busqueda" placeholder="Elija una opcion">
                                <div class="lista" id="listaAreas">
                                   <ul>
                                       <?php echo $this->listaAreas?>
                                   </ul>
                                </div>
                            </div>
                        </div>
                        <div class="seccion_medio">
                            <div class="column2">
                                    <label for="origen">Almacen Origen:</label>
                                    <input type="text" name="origen" id="origen" class="mostrarLista busqueda" placeholder="Elija una opcion">
                                    <div class="lista" id="listaOrigen">
                                        <ul>
                                            <?php echo $this->listaAlmacen?>
                                        </ul>
                                   </div>
                            </div>
                            <div class="column2">
                                <label for="destino">Almacen Destino:</label>
                                <input type="text" name="destino" id="destino" class="mostrarLista busqueda" placeholder="Elija una opcion">
                                <div class="lista" id="listaDestino">
                                    <ul>
                                        <?php echo $this->listaAlmacen?>
                                    </ul>
                                </div>
                            </div>
                            <div class="column2">
                                <label for="solicitante">Solicitante:</label>
                                <input type="text" name="solicitante" id="solicitante">
                            </div>
                            <div class="column2">
                                <label for="autoriza">Autoriza:</label>
                                <input type="text" name="autoriza" id="autoriza" class="mostrarLista" placeholder="Elija una opcion">
                                <div class="lista" id="listaAutoriza">
                                   <ul>
                                        <?php echo $this->listaPersonal?>
                                   </ul>
                                </div>
                            </div>
                        </div>
                        <div class="seccion_derecha">
                            <div class="column4_55">
                                <div class="column2_3457">
                                    <label for="tipo">Transferencia:</label>
                                    <input type="text" name="transferencia" id="transferencia" class="mostrarLista busqueda" placeholder="Elija una opcion">
                                    <div class="lista" id="listaTiposTransferencia">
                                        <ul>
                                        <?php echo $this->listaTiposTransferencia?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="column4_55">
                                <div class="column2_3457">
                                    <label for="tipo">Tipo:</label>
                                    <input type="text" name="tipo" id="tipo" class="mostrarLista busqueda" placeholder="Elija una opcion">
                                    <div class="lista" id="listaTipos">
                                        <ul>
                                        <?php echo $this->listaTipos?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="column2">
                                <label for="observaciones">Observa:</label>
                                <input type="text" name="observaciones" id="observaciones">
                            </div>
                        </div>
                    </div>
                    <div class="textAreaEnter oculto">
                        <textarea name="espec_items" id="espec_items" rows="2" class="w100p" readonly></textarea>
                    </div>
                    <div class="barraOpciones">
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
                                    <th width="6%">Serie.</th>
                                    <th width="6%">Destino</th>
                                    <th width="10%">Nro. Parte</th>
                                    <th width="30%">Observaciones</th>
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
    <div class="modal" id="status">
        <div class="ventanaInformes">
            <div class="title__informe">
                <h3>Estado del Traslado</h3>
                <a href="#" id="closeInform"><i class="far fa-window-close"></i></a>
            </div>
            <div class="statusTraslado">
                <div class="estados">
                    <div class="etapas" id="etapa1">
                        <div>
                            <p class="descripcionEtapa">Recepción Almacén</p>
                            <p id="fecha1"></p>
                        </div>
                        <div class="circle etapa_falta" id="circle1">
                            <p class="faltante"><i class="fas fa-times"></i></p>
                        </div>
                    </div>
                    <div class="etapas" id="etapa2">
                        <div>
                            <p class="descripcionEtapa">Entrega Logística</p>
                            <p id="fecha2"</p>
                        </div>
                        <div class="circle etapa_falta" id="circle2">
                            <p class="faltante"><i class="fas fa-times"></i></p>
                        </div>
                    </div>
                    <div class="etapas" id="etapa3">
                        <div>
                            <p class="descripcionEtapa">Recepción Logística</p>
                            <p id="fecha3"></p>
                        </div>
                        <div class="circle etapa_falta" id="circle3">
                            <p class="faltante"><i class="fas fa-times"></i></p>
                        </div>
                    </div>
                    <div class="etapas" id="etapa4">
                        <div>
                            <p class="descripcionEtapa">Entrega Usuario</p>
                            <p id="fecha4"></p>
                        </div>
                        <div class="circle etapa_falta" id="circle4">
                            <p class="faltante"><i class="fas fa-times"></i></p>
                        </div>
                    </div>
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
    <div class="cabezaModulo">
        <h1>Aprobación de Traslados</h1>
        <div>
            <a href="#" id="nuevoRegistro"><i class="far fa-file"></i><p>Nuevo</p></a>
            <a href="#" id="irInicio"><i class="fas fa-home"></i><p>Inicio</p></a>
            <a href="#" id="closeSession" class="oculto">xxx<p>Inicio</p></a>
        </div>
    </div>
    <div class="barraTrabajo">
        <form action="#" id="formConsulta">
            <div class="variasConsultas">
                    <div>
                        <label for="numberSearch">Numero : </label>
                        <input type="text" name="numberSearch" id="numberSearch">
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
                    <button type="button" id="btnConsulta" class="boton3">Consultar</button> 
            </div>
        </form>
    </div>
    <div class="itemsTabla">
        <table id="tablaPrincipal">
            <thead class="stickytop">
                <tr>
                    <th>Item.</th>
                    <th>Emision</th>
                    <th>Tipo</th>
                    <th>Centro Costos</th>
                    <th>Origen</th>
                    <th>Destino</th>
                    <th>Area</th>
                    <th>Asigna</th>
                    <th>Estado</th>
                    <th>...</th>
                    <th>...</th>
                </tr>
            </thead>
            <tbody>
                <?php echo $this->listaTraslados;?>
            </tbody>
        </table>
    </div>
    <script src="<?php echo constant('URL');?>public/js/jquery.js"></script>
    <script src="<?php echo constant('URL');?>public/js/funciones.js?<?php echo constant('VERSION')?>"></script>
    <script src="<?php echo constant('URL');?>public/js/autorizatraslado.js?<?php echo constant('VERSION')?>"></script>
</body>
</html>