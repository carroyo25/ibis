<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ingreso Almacen</title>
</head>
<body>
    <div class="modal" id="proceso">
        <div class="ventanaProceso tamanioProceso">
            <div class="cabezaProceso">
                <form action="#" id="formProceso" autocomplete="off">
                    <input type="hidden" name="codigo_costos" id="codigo_costos"> 
                    <input type="hidden" name="codigo_area" id="codigo_area">
                    <input type="hidden" name="codigo_almacen_origen" id="codigo_almacen_origen">
                    <input type="hidden" name="codigo_almacen_destino" id="codigo_almacen_destino">
                    <input type="hidden" name="codigo_estado" id="codigo_estado">
                    <input type="hidden" name="codigo_despacho" id="codigo_despacho">
                    <input type="hidden" name="codigo_autoriza" id="codigo_autoriza">
                    <input type="hidden" name="codigo_ingreso" id="codigo_ingreso">
                    <input type="hidden" name="codigo_recepcion" id="codigo_recepcion" value="<?php echo $_SESSION['iduser']?>">
                    <div class="barraOpciones primeraBarra">
                        <span>Datos Generales</span>
                        <div>
                            <button type="button" id="atachDocs" title="Adjuntar documentos" class="boton3">
                                <i class="fas fa-paperclip"></i> Adjuntar Documentos
                            </button>
                            <button type="button" id="printLabels" title="Imprimir etiquetas" class="boton3 oculto">
                                <i class="fas fa-tags"></i> Imprimir etiquetas
                            </button>
                            <button type="button" id="updateDocument" title="Grabar Ingreso" class="boton3">
                                <i class="far fa-save"></i> Grabar Ingreso
                            </button>
                            <button type="button" id="cancelDocument" title="Anular Ingreso" class="boton4">
                                <i class="fas fa-ban"></i> Anular Ingreso
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
                                    <label for="numero">Numero Registro:</label>
                                    <input type="text" name="numero" id="numero" class="cerrarLista textoDerecha pr20px" readonly>
                                </div>
                            </div>
                            <div class="column2">
                                <label for="costos">CCostos:</label>
                                <input type="text" name="costos" id="costos" class="mostrarLista busqueda" placeholder="Elija una opcion">
                                <div class="lista" id="listaCostos">
                                   <ul>
                                       <?php echo $this->listaCostos?>
                                   </ul> 
                                </div>
                            </div>
                        </div>
                        <div class="seccion_medio">
                            <div class="column2">
                                <label for="almacen_origen_despacho">Almacen Origen:</label>
                                <input type="text" name="almacen_origen_ingreso" id="almacen_origen_ingreso" class="busqueda" readonly>
                            </div>
                            <div class="column2">
                                <label for="almacen_destino_despacho">Almacen Destino:</label>
                                <input type="text" name="almacen_destino_ingreso" id="almacen_destino_ingreso" class="mostrarLista busqueda" readonly>
                            </div>
                            
                        </div>
                        <div class="seccion_derecha">
                            <div class="column2">
                                <label for="recepciona">Autoriza:</label>
                                <input type="text" name="autoriza" id="autoriza" class="mostrarLista busqueda" placeholder="Elija opción" readonly>
                                <div class="lista uno rowFive" id="listaRecepciona">
                                    <ul>
                                        <?php echo $this->listaRecepciona?>
                                    </ul> 
                                </div>
                            </div>
                            <div class="column4_55">
                                <div class="column2_3957">
                                    <label for="guia">Guia/Trans/Comp.:</label>
                                    <input type="text" name="cnumguia" id="guia">
                                </div>
                                <div class="column2_46">
                                    <label for="RS :">R.S.:</label>
                                    <input type="text" name="referido" id="referido">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="barraOpciones">
                        <span>Detalles</span>
                        <div>
                            <button type="button" id="previewDocs" title="Documentos Adjuntos" class="cerrarLista boton3">
                                <i class="fas fa-upload"></i> Ver Adjuntos
                                <span class="button__atach cookie_info" id="atach_counter"></span>
                            </button>
                            <button type="button" id="itemsImport" title="Importar Guias de Remision" class="cerrarLista boton3">
                                <i class="fas fa-upload"></i> Guias de Remisión
                            </button>
                            <button type="button" id="itemsTransfer" title="Importar Transferencias" class="cerrarLista boton3">
                                <i class="fas fa-upload"></i> Notas de Transferencia
                            </button>
                            <button type="button" id="itemsLocals" title="Importar compras Locales" class="cerrarLista boton3">
                                <i class="fas fa-upload"></i> Compras Locales
                            </button>
                        </div>
                    </div>
                    <div class="tablaInterna mininoTablaInterna">
                        <table class="tabla" id="tablaDetalles">
                            <thead>
                                <tr class="stickytop">
                                        <th class="">Item</th>
                                        <th class="">Codigo</th>
                                        <th class="">Descripcion</th>
                                        <th class="">Unidad</th>
                                        <th width="7%">Cantidad <br/> Enviada</th>
                                        <th width="7%">Cantidad <br/> Recep.</th>
                                        <th class="">Observaciones</th>
                                        <th class="">Area</th>
                                        <th class="">Fecha </br> Vencimiento</th>
                                        <th class="">Condicion</th>
                                        <th class="">Ubicación</th>
                                        <th class="">Pedido</th>
                                        <th class="">Orden</th>
                                        <th class="">G. Remision<br/>Nr.Trans.</th>
                                        <th class="">Adjunto</th>
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
                <span id="tituloBusqueda">Guias de Remision</span>
                <div>
                    <a href="#" id="closeSearch"><i class="fas fa-window-close"></i></a>
                </div>
            </div>
            <div class="textoBusqueda">
                <input type="text" name="txtBuscar" id="txtBuscar" placeholder="Buscar" class="w90por">
                <button type="button" class="boton3" id="btnAceptItems">Aceptar</button>
            </div>
            <div class="tablaBusqueda">
                <table class="tablaWrap" id="despachos">
                    <thead>
                        <tr class="stickytop" >
                            <th>Despacho</th>
                            <th>Fecha</th>
                            <th>Origen</th>
                            <th>Destino</th>
                            <th>Costos/Proyecto</th>
                            <th>Año</th>
                            <th>Guia</th>
                            <th>RS</th>
                            <th>Orden</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="modal" id="transferencias">
        <div class="ventanaBusqueda w75por">
            <div class="tituloVentana">
                <span id="tituloBusqueda">Notas de Transferencia</span>
                <div>
                    <a href="#" id="closeSearchTrans"><i class="fas fa-window-close"></i></a>
                </div>
            </div>
            <div class="textoBusqueda">
                <input type="text" name="txtBuscarTrans" id="txtBuscarTrans" placeholder="Buscar" class="w90por">
                <button type="button" class="boton3" id="btnAceptItemsTrans">Aceptar</button>
            </div>
            <div class="tablaBusqueda">
                <table class="tablaWrap" id="transferencias">
                    <thead>
                        <tr class="stickytop" >
                            <th>Nro</th>
                            <th>Guia</th>
                            <th>Fecha</th>
                            <th>Origen</th>
                            <th>Destino</th>
                            <th>Costos/Proyecto Origen</th>
                            <th>Año</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="modal" id="archivos">
        <div class="ventanaArchivos">
            <form action="#" id="fileAtachs" enctype='multipart/form-data'>
                <input type="hidden" name="certificadoAtach" id="certificadoAtach">
                <input type="file" name="uploadAtach" id="uploadAtach" multiple class="oculto">
                <div class="tituloArchivos">
                    <h3>Adjuntar Archivos</h3>
                    <a href="#" id="openArch" title="Adjuntar Archivos"><i class="fas fa-file-medical"></i><p>Añadir</p></a>
                </div>            
                <ul class="listaArchivos" ondrop="dropHandler(event);" ondragover="dragOverHandler(event);">
                </ul>
                <div class="opcionesArchivos">
                    <button type="button" class="boton3" id="btnConfirmAtach">Aceptar</button>
                    <button type="button" class="boton3" id="btnCancelAtach">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
    <div class="modal" id="vistaCertificado">
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
                <h3>Adjuntos</h3>
                <a href="#" id="closeAtach" title="Cerrar Ventana"><i class="fas fa-window-close"></i></a>
            </div>
            <ul id="listaAdjuntos">

            </ul>
            <iframe src=""></iframe>
        </div>
    </div>
    <div class="cabezaModulo">
        <h1>Registro Almacen</h1>
        <div>
            <a href="#" id="nuevoRegistro"><i class="far fa-file"></i><p>Nuevo</p></a>
            <a href="#" id="irInicio"><i class="fas fa-home"></i><p>Inicio</p></a>
        </div>
    </div>
    <div class="barraTrabajo">
        <form action="#" id="formConsulta">
            <div class="variasConsultas">
                    <div>
                        <label for="tipo">Guia N°. </label>
                        <input type="text" id="guiaSearch" name="guiaSearch">
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
                    <th>Item</th>
                    <th>F.Emisión</th>
                    <th>Almacen Origen</th>
                    <th>Almacen Destino</th>
                    <th>Centro de Costos</th>
                    <th>Guia</br>Remision</th>
                    <th>R.S.</th>
                </tr>
            </thead>
            <tbody>
                <?php echo $this->listaIngresos;?>
            </tbody>
        </table>
    </div>
    <div class="paginador oculto">
        <div class="botonesPaginador">
            <button><i class="fas fa-step-backward"></i></button>
            <button><i class="fas fa-caret-left"></i></button>
            <div class="paginas">
                <?php $inicial= 0;for ($i = 1; $i < intval($this->registros/50)+1 ; $i++) { ?>
                    <button class="boton_pagina" data-limite="<?php echo $inicial?>"><?php echo $i; ?></button>
                <?php $inicial+=50;} ?>
            </div>
            <button><i class="fas fa-caret-right"></i></button>
            <button><i class="fas fa-step-forward"></i></button>
        </div>
    </div>
    <script src="<?php echo constant('URL');?>public/js/jquery.js"></script>
    <script src="<?php echo constant('URL');?>public/js/funciones.js?<?php echo constant('VERSION')?>"></script>
    <script src="<?php echo constant('URL');?>public/js/registros.js?<?php echo constant('VERSION')?>"></script>
</body>
</html>