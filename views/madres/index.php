<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guias Madre</title>
</head>
<body>
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
                    <input type="hidden" name="codigo_moneda" id="codigo_moneda">
                    <input type="hidden" name="codigo_ingreso" id="codigo_ingreso">
                    <input type="hidden" name="codigo_salida" id="codigo_salida">
                    <input type="hidden" name="codigo_transferencia" id="codigo_transferencia">
                    <input type="hidden" name="id_centi" id="id_centi">
                    <input type="hidden" name="guia" id="guia">
                    <input type="hidden" name="total_items" id="total_items">
                    <input type="hidden" name="items_atendidos" id="items_atendidos">

                    <div class="barraOpciones primeraBarra">
                        <span>Datos Generales</span>
                        <div>
                            <button type="button" id="guiaRemision" title="Guia de Remision" class="cerrarLista boton3">
                                <i class="fas fa-upload"></i> Guia de Remisión
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
                                <label for="almacen_origen_despacho">Origen:</label>
                                <input type="text" name="almacen_origen_despacho" id="almacen_origen_despacho" class="mostrarLista busqueda">
                                <div class="lista" id="listaOrigenCabecera">
                                   <ul>
                                       <?php echo $this->listaEntidad?>
                                   </ul> 
                                </div>
                            </div>
                            <div class="column2">
                                <label for="almacen_destino_despacho">Destino:</label>
                                <input type="text" name="almacen_destino_despacho" id="almacen_destino_despacho" class="mostrarLista busqueda">
                                <div class="lista" id="listaDestinoCabecera">
                                   <ul>
                                       <?php echo $this->listaEntidad?>
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
                            <div class="column2">
                                <label for="corigen">CCostos Origen:</label>
                                <input type="text" name="corigen" id="corigen" class="mostrarLista busqueda" placeholder="Elija una opcion">
                                <div class="lista" id="listaCostosDestinoCabecera">
                                   <ul>
                                       <?php echo $this->listaCostos?>
                                   </ul> 
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="barraOpciones">
                        <span>Detalles</span>
                        <div>
                            <button type="button" id="importData" title="Importar Ingresos" class="boton3">
                                <i class="fab fa-wpexplorer"></i> Importar Guias
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
                                    <th width="7%">Cantidad</th>
                                    <th width="7%">Nro.Guia</th>
                                    <th width="7%">Nro. Paquete<br/>Lurin</th>
                                    <th width="7%">PUCP</th>
                                    <th width="7%">...</th>
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
    <div class="modal" id="guias">
        <div class="ventanaBusqueda w50por">
            <div class="tituloVentana">
                <span id="tituloBusqueda">Pedidos</span>
                <div>
                    <a href="#"><i class="fas fa-window-close"></i></a>
                </div>
            </div>
            <div class="textoBusqueda">
                <input type="text" name="txtBuscarGuia" id="txtBuscarGuia" placeholder="Ingrese Nro. Guia">
            </div>
            <div class="tablaBusqueda">
                <table class="tabla " id="tablaGuias">
                    <thead >
                        <tr class="stickytop">
                            <th width="10%">Nro. Guia</th>
                            <th width="10%">Fecha</th>
                            <th>Centro </br>de Costos</th>
                            <th>...</th>
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
                <input type="hidden" name="codigo_transporte" id="codigo_transporte">
                <input type="hidden" name="codigo_tipo" id="codigo_tipo">
                <input type="hidden" name="motivo_traslado" id="motivo_traslado">
                <input type="hidden" name="id_guia" id="id_guia" value=0>
                <input type="hidden" name="ubig_origen" id="ubig_origen">
                <input type="hidden" name="ubig_destino" id="ubig_destino">
                <input type="hidden" name="cso" id="cso">
                <input type="hidden" name="csd" id="csd">
                <input type="hidden" name="codigo_ubigeo" id="codigo_ubigeo">
                <input type="hidden" name="nombre_entidad_origen" id="nombre_entidad_origen">
                <input type="hidden" name="ruc_entidad_origen" id="ruc_entidad_origen">
                <input type="hidden" name="nombre_entidad_destino" id="nombre_entidad_destino">
                <input type="hidden" name="ruc_entidad_destino" id="ruc_entidad_destino">
                <input type="hidden" name="ticket_sunat" id="ticket_sunat">
                <input type="hidden" name="bultos" id="bultos">
                <div class="tituloDocumento">
                    <div>
                        <p class="titulo_seccion"><strong> Guia de Remision Interna : </strong></p>
                        <input type="text" name="serie_guia" id="serie_guia" class="w10por" value="T001" readonly>
                        <input type="text" name="numero_guia" id="numero_guia" readonly>
                        <p class="titulo_seccion"><strong> Guia de Remision Sunat : </strong></p>
                        <input type="text" name="serie_guia_sunat" id="serie_guia_sunat" class="w10por" value="T001" readonly>
                        <input type="text" name="numero_guia_sunat" id="numero_guia_sunat" readonly>
                    </div>
                    <div class="opciones_modal">
                        <a href="#" id="saveDocument" title="Grabar Guia"><i class="fas fa-save"></i><p>Grabar</p></a>
                        <a href="#" id="previewDocument" title="Vista previa"><i class="fas fa-eye"></i><p>Vista Previa</p></a>
                        <a href="#" id="guiaSunat" title="Guia Sunat" class="oculto"><i class="fas fa-shipping-fast"></i><p>Sunat</p></a>
                        <a href="#" id="closeDocument" title="Cerrar Ventana"><i class="fas fa-window-close"></i><p>Cerrar</p></a>
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
                                <input type="date" name="ftraslado" id="ftraslado" value="<?php echo date("Y-m-d")?>" min="<?php echo date("Y-m-d")?>">
                            </div>
                            <div>
                                <label for="tipo_documento">Tipo Guia:</label>
                                <select name="tipo_documento" id="tipo_documento">
                                    <option value="1">REMITENTE</option>
                                    <option value="2" style="display:none;">REMITENTE</option>
                                    <option value="3">TRANSPORTISTA</option>
                                    <option value="4" style="display:none;">SUNAT</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <p class="titulo_seccion"><strong>Datos del Remitente</strong></p>
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
                                <br/>
                                <label for="ubigeo_origen">Ubigeo :</label>
                                <input type="text" name="ubigeo_origen_guia" id="ubigeo_origen_guia">
                                <button type="button" class="btnCallDialog boton3" id="ubigeoBtnOrigen">+</button>
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
                                <br/>
                                <label for="ubigeo_destino">Ubigeo :</label>
                                <input type="text" name="ubigeo_destino_guia" id="ubigeo_destino_guia">
                                <button type="button" class="btnCallDialog boton3" id="ubigeoBtnDestino">+</button>
                            </div>
                            <p><strong>Empresa de Transporte</strong></p>
                            <div class="tres_columnas_interna">
                                <label for="empresa_transporte_razon">Razón Social</label>
                                <input type="text" name="empresa_transporte_razon" id="empresa_transporte_razon" class="buscaGuia">
                                <button type="button" class="btnCallMenu boton3" id="ubigeoBtnDestino">+</button>
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
                                <span></span>
                                <label for="registro_mtc">Registro MTC</label>
                                <input type="text" name="registro_mtc" id="registro_mtc" class="datosEntidad">
                            </div>
                        </div>
                    </div>
                    <div class="columna_derecha">
                        
                        <p><strong>Motivo</strong></p>
                        <div class="tres_columnas_interna">
                            <label for="tipo_transporte" class="uno">Tipo Transporte :</label>
                            <input type="text" name="tipo_transporte" id="tipo_transporte" class="dos mostrarListaInterna busqueda">
                            <button type="button" class="btnCallMenu boton3">+</button>
                            <div class="lista rowOne uno" id="listaTransporte">
                                <ul>
                                    <?php echo $this->listaTransporte?>
                                </ul> 
                            </div>
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
                            <button type="button" id="btnConductor" class="btnCallMenu boton3">+</button>
                            <div class="lista" id="listaConductores">
                                <ul>
                                    <?php echo $this->listaConductores?>
                                </ul> 
                            </div>
                            <label for="licencia_conducir">N° Licencia :</label>
                            <input type="text" name="licencia_conducir" id="licencia_conducir" class="cerrarLista">
                            <span></span>
                            <label for="coductor_dni">DNI conductor :</label>
                            <input type="text" name="conductor_dni" id="conductor_dni" class="cerrarLista">
                        </div>
                        <p><strong>Datos del Vehiculo</strong></p>
                        <div class="tres_columnas_interna">
                            <label for="placa">Nro. Placa :</label>
                            <input type="text" name="placa" id="placa">
                            <button type="button" id="btnPlaca" class="btnCallMenu boton3">+</button>
                            <div class="lista" id="listaPlacas">
                                <ul>
                                    <?php echo $this->listaPlacas?>
                                </ul> 
                            </div>
                            <label for="marca" class="oculto">Marca :</label>
                            <input type="text" name="marca" id="marca" class="oculto">
                        </div>
                        <p><strong>Datos Adicionales</strong></p>
                        <div class="tres_columnas_interna">
                            <label for="marca">Peso :</label>
                            <input type="text" name="peso" id="peso">
                            <span></span>
                            <label for="fecha_embarque">Fecha de Embarque :</label>
                            <input type="date" name="fecha_embarque" id="fecha_embarque">
                            <span></span>
                            <label for="nombre_embarque">Nombre Embarcación :</label>
                            <input type="text" name="nombre_embarque" id="nombre_embarque">
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
    <div class="modal" id="aviso">
        <div class="ventanaAdvertencia">
            <h3>AVISO</h3>
            <div>
                <span>Estimado Usuario, la emisión de la Guia de Remisión SUNAT
                    es un proceso, irreversible y constituye un documento de 
                    legal, por favor verificar los que los datos indicados 
                    sean correctos.
                </span> 
            </div>
            <div class="btnOptions">
                <button type="button" id="btnAceptarAdvierte">Aceptar</button>
                <button type="button" id="btnCancelarAdvierte">Cancelar</button>
            </div>
        </div>
    </div>
    <div class="modal" id="ubigeo">
        <div class="ventanaDialogo">
            <div class="selectDiv">
                <label for="dpto">Departamento</label>
                <select name="dpto" id="dpto">
                    <?php foreach($this->listaDepartamento['datos'] as $dpto) { ?>
                       <option value="<?php echo $dpto['ccubigeo'];?>"><?php echo $dpto['cdubigeo'];?></option> 
                    <?php };?>
                </select>
                <label for="prov">Provincia</label>
                <select name="prov" id="prov">
                    
                </select>
                <label for="dist">Provincia</label>
                <select name="dist" id="dist">
                    
                </select>
            </div>
            <div class="btnOptions">
                <button type="button3" id="btnAceptarUbigeo">Aceptar</button>
                <button type="button3" id="btnCancelarUbigeo">Cancelar</button>
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
    <div class="cabezaModulo">
        <h1>Guias de Remision SUNAT</h1>
        <div>
            <a href="#" id="nuevoRegistro"><i class="far fa-file"></i><p>Nuevo</p></a>
            <a href="#" id="irInicio"><i class="fas fa-home"></i><p>Inicio</p></a>
        </div>
    </div>
    <div class="barraTrabajo">
        <form action="#" id="formConsulta">
            <div class="variasConsultas">
                    <div>
                        <label for="tipo">N° Guia : </label>
                        <input type="text" name="ordenSearch" id="ordenSearch">
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
                            <option value="-1">Seleccionar Todos</option>
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
                    <th>Num.</th>  
                    <th>Emision</th>
                    <th>Fecha Traslado</th> 
                    <th>Almacen Origen</th>
                    <th>Almacen Destino</th>
                    <th>N° Guia</th>
                    <th>N° Guia Sunat</th>
                    <th>Estado Sunat</th>
                </tr>
            </thead>
            <tbody>
                <?php echo $this->listaGuias ?>
            </tbody>
        </table>
    </div>
    <script src="<?php echo constant('URL');?>public/js/jquery.js"></script>
    <script src="<?php echo constant('URL');?>public/js/funciones.js?<?php echo constant('VERSION')?>"></script>
    <script src="<?php echo constant('URL');?>public/js/madres.js?<?php echo constant('VERSION')?>"></script>
</body>
</html>