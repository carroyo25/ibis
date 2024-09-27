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
                    <input type="hidden" name="codigo_costos_origen" id="codigo_costos_origen">
                    <input type="hidden" name="codigo_costos_destino" id="codigo_costos_destino">  
                    <input type="hidden" name="codigo_area" id="codigo_area">
                    <input type="hidden" name="codigo_movimiento" id="codigo_movimiento">
                    <input type="hidden" name="codigo_aprueba" id="codigo_aprueba">
                    <input type="hidden" name="codigo_almacen_origen" id="codigo_almacen_origen">
                    <input type="hidden" name="codigo_almacen_destino" id="codigo_almacen_destino">
                    <input type="hidden" name="codigo_pedido" id="codigo_pedido">
                    <input type="hidden" name="codigo_orden" id="codigo_orden">
                    <input type="hidden" name="codigo_estado" id="codigo_estado">
                    <input type="hidden" name="codigo_ingreso" id="codigo_ingreso">
                    <input type="hidden" name="codigo_salida" id="codigo_salida">
                    <input type="hidden" name="codigo_transferencia" id="codigo_transferencia">
                    <input type="hidden" name="id_centi" id="id_centi">
                    <input type="hidden" name="guia" id="guia">
                    <input type="hidden" name="total_items" id="total_items">
                    <input type="hidden" name="items_atendidos" id="items_atendidos">
                    <input type="hidden" name="motivo_transferencia" id="motivo_transferencia">
                    <input type="hidden" name="usuario_genera" id="usuario_genera">
                    

                    <div class="barraOpciones primeraBarra" id="barra_notifica">
                        <span>Datos Generales</span>
                        <div>
                            <button type="button" id="importRequest" title="Importar Pedido" class="boton3">
                                <i class="fab fa-wpexplorer"></i> Importar Pedido
                            </button>
                            <button type="button" id="preview" title="Vista Previa" class="boton3">
                                <i class="far fa-file-pdf"></i> Vista Previa
                            </button>
                            <button type="button" id="saveRegister" title="Grabar Registro" class="boton3">
                                <i class="fas fa-save"></i> Grabar Registro
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
                        
                        <div class="seccion_medio">
                            <div class="column2">
                                <label for="corigen">CCostos Origen:</label>
                                <input type="text" name="corigen" id="corigen" class="mostrarLista busqueda" placeholder="Elija una opcion">
                                <div class="lista" id="listaCostosOrigen">
                                   <ul>
                                       <?php echo $this->listaCostos?>
                                   </ul> 
                                </div>
                            </div>
                            <div class="column2">
                                <label for="cdestino">CCostos Destino:</label>
                                <input type="text" name="cdestino" id="cdestino" class="mostrarLista busqueda" placeholder="Elija una opcion">
                                <div class="lista" id="listaCostosDestino">
                                   <ul>
                                       <?php echo $this->listaCostos?>
                                   </ul> 
                                </div>
                            </div>
                        </div>
                        <div class="seccion_derecha">
                            <div class="column2">
                                <label for="tipo">Tipo Mov.:</label>
                                <input type="text" name="tipo" id="tipo" class="mostrarLista busqueda" placeholder="Elija opción" readonly>
                                <div class="lista" id="listaMovimiento">
                                   <ul>
                                       <?php echo $this->listaMovimiento?>
                                   </ul> 
                                </div>
                            </div>
                            <div class="column4_55">
                                <div class="column2">
                                    <label for="movimiento">Mov Almacen:</label>
                                    <input type="text" name="movimiento" id="movimiento" class="mostrarLista busqueda" placeholder="Elija opción"  readonly>
                                    <div class="lista" id="listaCriterio">
                                        <ul>
                                            <?php echo $this->listaCriteriosAlmacen?>
                                        </ul> 
                                    </div>
                                </div>
                                <div class="column2">
                                    <label for="estado">Estado:</label>
                                    <input type="text" name="estado" id="estado" class="textoCentro estado w100por procesando" readonly value="EN PROCESO">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="barraOpciones">
                        <span>Detalles</span>
                        <div>
                            <button type="button" id="guiaRemision" title="Guia de Remision" class="cerrarLista boton3">
                                <i class="fas fa-upload"></i> Guia de Remision
                            </button>
                        </div>
                    </div>
                    <div class="tablaInterna mininoTablaInterna">
                        <table class="tabla" id="tablaDetalles">
                            <thead>
                                <tr class="stickytop">
                                    <th class="">Item</th>
                                    <th class="">Codigo</th>
                                    <th width="45%">Descripcion</th>
                                    <th class="">Unidad</th>
                                    <th width="7%" class="">Cantidad</br>Referida<br>Almacen</th>
                                    <th width="7%">Cantidad</br>Atendida</th>
                                    <th width="7%">Cantidad por</br>Atender</th>
                                    <th width="7%">Fecha</br>Vencimiento</th>
                                    <th width="7%">Condicion</th>
                                    <th width="7%">Stock</th>
                                    <th width="20%" class="">Observaciones</th>
                                    <th class="">Pedido</th>
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
    <div class="modal" id="vistaPedido">
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
                    <input type="hidden" name="codigo_usuario" id="codigo_usuario">

                    <div class="barraOpciones primeraBarra">
                        <span>Datos Generales</span>
                        <div>
                            <button type="button" id="closeProcessRequest" title="Cerrar" class="boton3">
                                <i class="fas fa-window-close"></i>
                            </button>
                        </div>
                    </div>
                    <div class="dataProceso_2">
                        <div class="seccion_izquierda">
                            <div class="column4_55">
                                <div class="column2_3457">
                                    <label for="numero">Número:</label>
                                    <input type="text" name="numero_pedido" id="numero_pedido" class="cerrarLista" readonly>
                                </div>
                                <div class="column2_46">
                                    <label for="emision">Emisión:</label>
                                    <input type="date" name="emision" id="emision" class="cerrarLista" value="<?php echo date("Y-m-d");?>">
                                </div>
                            </div>
                            <div class="column2">
                                <label for="costos">CCostos:</label>
                                <input type="text" name="costos" id="costos" class="mostrarLista busqueda" placeholder="Elija una opcion">
                            </div>
                            <div class="column2">
                                <label for="area">Partida:</label>
                                <input type="text" name="partida" id="partida" class="mostrarLista busqueda" placeholder="Elija una opcion">
                            </div>
                        </div>
                        <div class="seccion_medio">
                            <div class="column2">
                                <label for="area">Area:</label>
                                <input type="text" name="area" id="area" class="mostrarLista busqueda" placeholder="Elija una opcion">
                            </div>
                            <div class="column2">
                                <label for="concepto">Concepto:</label>
                                <input type="text" name="concepto" id="concepto" class="cerrarLista mayusculas">
                            </div>
                            <div class="column2">
                                <label for="solicitante">Solicitante:</label>
                                <input type="text" name="solicitante" id="solicitante" class="mostrarLista busqueda" placeholder="Elija una opcion">
                            </div>
                        </div>
                        <div class="seccion_derecha">
                            <div class="column4_55">
                                <div class="column2_3957">
                                    <label for="tipo">Tipo :</label>
                                    <input type="text" name="tipo_pedido" id="tipo_pedido" class="mostrarLista" placeholder="Elija una opcion">
                                </div>
                                <div class="column2_46">
                                    <label for="vence">Vence :</label>
                                    <input type="date" name="vence" id="vence" class="cerrarLista" value="<?php echo date("Y-m-d");?>">
                                </div>
                            </div>
                            <div class="column4_55">
                                <div class="column2_3957">
                                    <label for="pedidommto">Ped. MMTO:</label>
                                    <input type="text" name="pedidommto" id="pedidommto">
                                </div>
                                <div class="column2_46">
                                    <label for="transporte">Transporte:</label>
                                    <input type="text" name="transporte" id="transporte" class="mostrarLista" placeholder="Elija una opcion">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="textAreaEnter oculto">
                        <textarea name="espec_items" id="espec_items" rows="2" class="w100p" readonly></textarea>
                    </div>
                    <div class="barraOpciones">
                        <span>Detalles</span>
                    </div>
                    <div class="tablaInterna mininoTablaInterna">
                        <table class="tabla" id="tablaDetallesPedido">
                            <thead class="stickytop">
                                <tr>
                                    <th width="3%">...</th>
                                    <th width="5%">Item</th>
                                    <th width="8%">Codigo</th>
                                    <th>Descripcion</th>
                                    <th width="5%">Und.</th>
                                    <th width="6%">Cant.</th>
                                    <th width="30%">Especificaciones</th>
                                    <th>Nro. Parte</th>
                                    <th>Bien/Activo</th>
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
    <div class="modal" id="pedidos">
        <div class="ventanaBusqueda w50por">
            <div class="tituloVentana">
                <span id="tituloBusqueda">Pedidos</span>
                <div>
                    <a href="#"><i class="fas fa-window-close"></i></a>
                </div>
            </div>
            <div class="textoBusqueda">
                <input type="text" name="txtBuscarPedido" id="txtBuscarPedido" placeholder="Buscar Pedido">
            </div>
            <div class="tablaBusqueda">
                <table class="tabla " id="tablaPedidos">
                    <thead >
                        <tr class="stickytop">
                            <th width="10%">Pedido</th>
                            <th width="10%">Fecha</th>
                            <th>Descripcion</th>
                            <th>CC</th>
                            <th>Solicitante</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="modal" id="vistadocumento">
        <div class="ventanaDocumento">
            <form method="post" id="guiaremision">
                <input type="hidden" name="codigo_origen" id="codigo_origen">
                <input type="hidden" name="codigo_destino" id="codigo_destino">
                <input type="hidden" name="codigo_autoriza" id="codigo_autoriza">
                <input type="hidden" name="codigo_despacha" id="codigo_despacha">
                <input type="hidden" name="codigo_destinatario" id="codigo_destinatario">
                <input type="hidden" name="codigo_entidad_transporte" id="codigo_entidad_transporte">
                <input type="hidden" name="direccion_entidad_transporte" id="direccion_entidad_transporte">
                <input type="hidden" name="ruc_entidad_transporte" id="ruc_entidad_transporte">
                <input type="hidden" name="codigo_modalidad" id="codigo_modalidad">
                <input type="hidden" name="codigo_tipo" id="codigo_tipo">
                <input type="hidden" name="motivo_traslado" id="motivo_traslado">
                <input type="hidden" name="id_guia" id="id_guia" value=0>
                <input type="hidden" name="motivo_guia" id="motivo_guia" value="94">

                <div class="tituloDocumento">
                    <div>
                        <p class="titulo_seccion"><strong> Guia de Remision y despacho : </strong></p>
                        <input type="text" name="serie_guia" id="serie_guia" class="w10por" value="F001" readonly>
                        <input type="text" name="numero_guia" id="numero_guia" readonly>    
                    </div>
                    <div class="opciones_documento">
                        <a href="#" id="saveDocument" title="Grabar Guia"><i class="fas fa-save"></i></a>
                        <a href="#" id="printDocument" title="Imprimir Guia"><i class="fas fa-print"></i></a>
                        <a href="#" id="previewDocument" title="Vista previa"><i class="fas fa-eye"></i></a>
                        <a href="#" id="closeDocument" title="Cerrar Ventana"><i class="fas fa-window-close"></i></a>
                    </div>
                </div>
                <hr>
                <div class="dos_columnas">
                    <div class="columna_izquierda">
                        <div class="fechas">
                            <div>
                                <label for="fgemision">Fecha Emisión:</label>
                                <input type="date" name="fgemision" id="fgemision" value="<?php echo date("Y-m-d")?>">
                            </div>
                            <div>
                                <label for="ftraslado">Fecha Traslado:</label>
                                <input type="date" name="ftraslado" id="ftraslado">
                            </div>
                            <div>
                                <label for="tipo_documento">Tipo Guia:</label>
                                <select name="tipo_documento" id="tipo_documento">
                                    <option value="1">DESTINATARIO</option>
                                    <option value="2">REMITENTE</option>
                                    <option value="3">TRANSPORTISTA</option>
                                    <option value="4">SUNAT</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <p class="titulo_seccion"><strong>Datos del destinatario</strong></p>
                            <div class="dos_columnas_interna">
                                <label>R.U.C.</label>
                                <input type="text" name="destinatario_ruc" id="destinatario_ruc" value="20504898173" readonly>
                                <label>Razón Social :</label>
                                <input type="text" name="destinatario_razon" id="destinatario_razon" value="SERVICIOS PETROLEROS Y CONSTRUCCIONES SEPCON S.A.C" readonly>
                                <label>Dirección:</label>
                                <input type="text" name="destinatario_direccion" id="destinatario_direccion" value="AV. SAN BORJA NORTE N° 445 - SAN BORJA-LIMA-PERU." readonly>
                            </div>
                            <p><strong>Domicilio de partida</strong></p>
                            <div class="tres_columnas_interna">
                                <label for="almacen_origen">Almacen Origen: </label>
                                <input type="text" name="almacen_origen" id="almacen_origen" class="cerrarLista" >
                                <button type="button" id="btnAlmacenOrigen" class="btnCallMenu boton3">+</button>
                                <div class="lista" id="listaOrigen">
                                   <ul>
                                       <?php echo $this->listaAlmacen?>
                                   </ul> 
                                </div>
                                <label for="almacen_origen_direccion ">Dirección:</label>
                                <input type="text" name="almacen_origen_direccion" id="almacen_origen_direccion">
                            </div>
                            <p><strong>Domicilio de Llegada</strong></p>
                            <div class="tres_columnas_interna">
                                <label for="almacen_destino">Almacen Destino: </label>
                                <input type="text" name="almacen_destino" id="almacen_destino">
                                <button type="button" class="btnCallMenu boton3">+</button>
                                <div class="lista" id="listaDestino">
                                   <ul>
                                       <?php echo $this->listaAlmacen?>
                                   </ul> 
                                </div>
                                <label for="almacen_destino_direccion ">Dirección:</label>
                                <input type="text" name="almacen_destino_direccion" id="almacen_destino_direccion">
                            </div>
                            <p><strong>Empresa de Transporte</strong></p>
                            <div class="tres_columnas_interna">
                                <label for="empresa_transporte_razon">Razón Social</label>
                                <input type="text" name="empresa_transporte_razon" id="empresa_transporte_razon" class="buscaGuia">
                                <button type="button" class="btnCallMenu boton3">+</button>
                                <div class="lista" id="listaEntidad">
                                   <ul>
                                       <?php echo $this->listaEntidad?>
                                   </ul> 
                                </div>
                                <label for="direccion_proveedor">Dirección</label>
                                <input type="text" name="direccion_proveedor" id="direccion_proveedor" class="datosEntidad">
                                <span></span>
                                <label for="ruc_proveedor">R.U.C.</label>
                                <input type="text" name="ruc_proveedor" id="ruc_proveedor" class="datosEntidad">
                            </div>
                        </div>
                    </div>
                    <div class="columna_derecha">
                        <p><strong>Motivo</strong></p>
                        <div class="tres_columnas_interna">
                            <label for="modalidad_traslado" class="uno">Modalidad Traslado :</label>
                            <input type="text" name="modalidad_traslado" id="modalidad_traslado" class="dos mostrarListaInterna busqueda">
                            <button type="button" class="btnCallMenu boton3">+</button>
                            <div class="lista rowOne uno" id="listaModalidad">
                                <ul>
                                    <?php echo $this->listaModalidad?>
                                </ul> 
                            </div>
                            <label for="tipo_envio">Tipo Envio</label>
                            <input type="text" name="tipo_envio" id="tipo_envio" class="dos mostrarListaInterna busqueda" placeholder="Elija opción">
                            <button type="button" class="btnCallMenu boton3">+</button>
                            <div class="lista uno rowTwo" id="listaEnvio">
                                <ul>
                                    <?php echo $this->listaEnvio?>
                                </ul> 
                            </div>
                            <label for="autoriza">Autoriza:</label>
                            <input type="text" name="autoriza" id="autoriza" class="dos mostrarListaInterna busqueda" placeholder="Elija opción">
                            <button type="button" class="btnCallMenu boton3">+</button>
                            <div class="lista uno rowThree" id="listaAutoriza">
                                <ul>
                                    <?php echo $this->listaPersonal?>
                                </ul> 
                            </div>
                            <label for="destinatario">Destinatario:</label>
                            <input type="text" name="destinatario" id="destinatario" class="busqueda" placeholder="Elija opción">
                            <button type="button" class="btnCallMenu boton3">+</button>
                            <div class="lista uno rowFour" id="listaDestinatario">
                                <ul>
                                    <?php echo $this->listaPersonal?>
                                </ul> 
                            </div>
                            <label for="observaciones_guia">Observaciones:</label>
                            <textarea name="observaciones" id="observaciones" placeholder="Observaciones" class="dos"></textarea>
                        </div>
                        <p><strong>Datos del Conductor</strong></p>
                        <div class="tres_columnas_interna">
                            <label for="nombre_conductor">Nombre :</label>
                            <input type="text" name="nombre_conductor" id="nombre_conductor" class="cerrarLista">
                            <span></span>
                            <label for="licencia_conducir">N° Licencia :</label>
                            <input type="text" name="licencia_conducir" id="licencia_conducir" class="cerrarLista">
                        </div>
                        <p><strong>Datos del Vehiculo</strong></p>
                        <div class="tres_columnas_interna">
                            <label for="marca">Marca :</label>
                            <input type="text" name="marca" id="marca">
                            <span></span>
                            <label for="placa">Nro. Placa :</label>
                            <input type="text" name="placa" id="placa">
                        </div>
                        <p><strong>Datos Adicionales</strong></p>
                        <div class="tres_columnas_interna">
                            <label for="marca">Peso :</label>
                            <input type="text" name="peso" id="peso">
                            <span></span>
                            <label for="bultos">Nro. Bultos :</label>
                            <input type="text" name="bultos" id="bultos">
                        </div>
                    </div>
                </div>
            </form>
            
        </div>
    </div>
    <div class="modal" id="vistaprevia">
        <div class="ventanaVistaPrevia">
            <div class="tituloVista">
                <h3>Vista Previa</h3>
                <a href="#" id="closePreview" title="Cerrar Ventana"><i class="fas fa-window-close"></i></a>
            </div>
            <iframe src="" id="pdfPreview"></iframe>
        </div>
    </div>
    <div class="modal" id="vistaTransferencia">
        <div class="ventanaVistaPrevia">
            <div class="tituloVista">
                <h3>Vista Previa</h3>
                <a href="#" id="closePreviewNota" title="Cerrar Ventana"><i class="fas fa-window-close"></i></a>
            </div>
            <iframe src="" id="pdfPreview"></iframe>
        </div>
    </div>
    <div class="modal" id="imprimir">
        <div class="ventanaVistaPrevia">
            <div class="tituloVista">
                <h3>Vista Previa</h3>
                <a href="#" id="closePreviewPrinter" title="Cerrar Ventana"><i class="fas fa-window-close"></i></a>
            </div>
            <iframe src="" id="iFramePdf"></iframe>
        </div>
    </div>
    <div class="cabezaModulo">
        <h1>Nota de Transferencia</h1>
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
    <div>
        <div class="itemsTablaVertical">
            <h3>Pedidos por atender</h3>
            <div>
                <table id="tablaPrincipalPedidos">
                    <thead class="stickytop">
                        <tr>
                            <th data-idcol="0" class="datafiltro">Num.</th>
                            <th>Emision</th>
                            <th>Tipo</th>
                            <th data-idcol="3" class="datafiltro">Descripción</th>
                            <th data-idcol="4" class="datafiltro">Centro Costos</th>
                            <th data-idcol="6">Responsable</th>
                            <th>Estado</th>
                            <th>Atencion</th>
                            <th>...</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php echo $this->listaPedidos;?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="itemsTablaVertical">
            <h3>Transferencias</h3>
            <div>
                <table id="tablaPrincipal">
                    <thead class="stickytop">
                        <tr>
                            <th data-idcol="0">Nro. Nota</th>
                            <th>F.Emisión</th>
                            <th data-idcol="4">Centro de Costos Origen</th>
                            <th data-idcol="4">Centro de Costos Destino</th>
                            <th data-idcol="5">Nro. Guia</th>
                            <th data-idcol="5">Nro. Pedido</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php echo $this->listaAtencion;?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
   
    <script src="<?php echo constant('URL');?>public/js/jquery.js"></script>
    <script src="<?php echo constant('URL');?>public/js/funciones.js?<?php echo constant('VERSION')?>"></script>
    <script src="<?php echo constant('URL');?>public/js/transferencias.js?<?php echo constant('VERSION')?>"></script>
</body>
</html>