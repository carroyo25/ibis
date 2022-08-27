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
                    <input type="hidden" name="codigo_almacen_destino" id="codigo_almacen_destino">
                    <input type="hidden" name="codigo_pedido" id="codigo_pedido">
                    <input type="hidden" name="codigo_orden" id="codigo_orden">
                    <input type="hidden" name="codigo_estado" id="codigo_estado">
                    <input type="hidden" name="codigo_moneda" id="codigo_moneda">
                    <input type="hidden" name="codigo_ingreso" id="codigo_ingreso">
                    <input type="hidden" name="codigo_salida" id="codigo_salida">
                    <input type="hidden" name="id_centi" id="id_centi">

                    <div class="barraOpciones primeraBarra">
                        <span>Datos Generales</span>
                        <div>
                            <button type="button" id="saveDoc" title="Grabar Nota" class="boton3">
                                <p><i class="far fa-save"></i> Grabar </p> 
                            </button>
                            <button type="button" id="importData" title="Importar Orden" class="boton3">
                                <i class="fab fa-wpexplorer"></i> Importar Nota
                            </button>
                            <button type="button" id="updateDocument" title="Cerrar Salida" class="boton3">
                                <i class="far fa-comments"></i> Cerrar Salida
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
                                <label for="costos">Ccostos:</label>
                                <input type="text" name="costos" id="costos" readonly>
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
                                <label for="almacen_origen_despacho">Almacen Origen:</label>
                                <input type="text" name="almacen_origen_despacho" id="almacen_origen_despacho" class="busqueda" readonly>
                            </div>
                            <div class="column2">
                                <label for="almacen_destino_despacho">Almacen Destino:</label>
                                <input type="text" name="almacen_destino_despacho" id="almacen_destino_despacho" class="mostrarLista busqueda" readonly>
                                <div class="lista" id="listaAlmacenDestino">
                                   <ul>
                                       <?php echo $this->listaAlmacen?>
                                   </ul> 
                                </div>
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
                                <i class="fas fa-paperclip"></i> Documentos Adjuntos
                            </button>
                            <button type="button" id="asocOrd" title="Orden de Compra Asociada" class="cerrarLista boton3">
                                <i class="far fa-file-pdf"></i> Orden Asociada
                            </button>
                            <button type="button" id="guiaRemision" title="Guia de Remision" class="cerrarLista boton3">
                                <i class="fas fa-upload"></i> Guia de Remision
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
                <span id="tituloBusqueda">Notas de Ingreso</span>
                <div>
                    <a href="#" id="closeSearch"><i class="fas fa-window-close"></i></a>
                </div>
            </div>
            <div class="textoBusqueda">
                <input type="text" name="txtBuscar" id="txtBuscar" placeholder="Buscar" class="w90por">
                <button type="button" class="boton3" id="btnAceptItems">Aceptar</button>
            </div>
            <div class="tablaBusqueda">
                <table class="tablaWrap" id="notas">
                    <thead>
                        <tr class="stickytop" >
                            <th>Número</th>
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
                <iframe id="iFramePdf" src="" class="oculto"></iframe>

                <div class="tituloDocumento">
                    <div>
                        <p class="titulo_seccion"><strong> Guia de Remision y despacho : </strong></p>
                        <input type="text" name="serie_guia" id="serie_guia" class="w10por" value="0001" readonly>
                        <input type="text" name="numero_guia" id="numero_guia">    
                    </div>
                    <div>
                        <a href="#" id="printDocument" title="Imprimir Guia"><i class="fas fa-print"></i></a>
                        <a href="#" id="closeDocument" title="Cerrar Ventana"><i class="fas fa-window-close"></i></a>
                    </div>
                </div>
                <hr>
                <div class="dos_columnas">
                    <div class="columna_izquierda">
                        <div >
                            <label for="fgemision">Fecha Emisión:</label>
                            <input type="date" name="fgemision" id="fgemision" value="<?php echo date("Y-m-d")?>">
                            <label for="ftransporte">Fecha Ent.Transpor.:</label>
                            <input type="date" name="ftransporte" id="ftransporte">
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
                            <div class="dos_columnas_interna">
                                <label for="almacen_origen">Almacen Origen: </label>
                                <input type="text" name="almacen_origen" id="almacen_origen" class="mostrarListaInterna busqueda" placeholder="Elija opción"
                                    readonly>
                                <div class="lista" id="listaOrigen">
                                   <ul>
                                       <?php echo $this->listaAlmacen?>
                                   </ul> 
                                </div>
                                <label for="almacen_origen_direccion ">Dirección:</label>
                                <input type="text" name="almacen_origen_direccion" id="almacen_origen_direccion">
                                <label for="almacen_origen_dpto">Departamento :</label>
                                <input type="text" name="almacen_origen_dpto" id="almacen_origen_dpto">
                                <label for="almacen_origen_prov">Provincia:</label>
                                <input type="text" name="almacen_origen_prov" id="almacen_origen_prov">
                                <label for="almacen_origen_dist">Distrito:</label>
                                <input type="text" name="almacen_origen_dist" id="almacen_origen_dist">
                            </div>
                            <p><strong>Domicilio de Llegada</strong></p>
                            <div class="dos_columnas_interna">
                                <label for="almacen_destino">Almacen Destino: </label>
                                <input type="text" name="almacen_destino" id="almacen_origen" class="mostrarListaInterna busqueda" placeholder="Elija opción"
                                    readonly>
                                <div class="lista" id="listaDestino">
                                   <ul>
                                       <?php echo $this->listaAlmacen?>
                                   </ul> 
                                </div>    
                                <label for="almacen_destino_direccion ">Dirección:</label>
                                <input type="text" name="almacen_destino_direccion" id="almacen_destino_direccion">
                                <label for="almacen_destino_dpto">Departamento :</label>
                                <input type="text" name="almacen_destino_dpto" id="almacen_destino_dpto">
                                <label for="almacen_destino_prov">Provincia:</label>
                                <input type="text" name="almacen_destino_prov" id="almacen_destino_prov">
                                <label for="almacen_destino_dist">Distrito:</label>
                                <input type="text" name="almacen_destino_dist" id="almacen_destino_dist">
                            </div>
                            <p><strong>Empresa de Transporte</strong></p>
                            <div class="dos_columnas_interna">
                                <label for="empresa_transporte_razon">Razón Social</label>
                                <input type="text" name="empresa_transporte_razon" id="empresa_transporte_razon" class="mostrarListaInterna busqueda" 
                                    placeholder="Elija opción" readonly>
                                <div class="lista" id="listaEntidad">
                                   <ul>
                                       <?php echo $this->listaEntidad?>
                                   </ul> 
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="columna_derecha">
                        <p><strong>Motivo</strong></p>
                        <div class="cuatro_columnas_interna">
                            <label for="motivo_traslado" class="uno">Motivo Traslado :</label>
                            <input type="text" name="motivo_traslado" id="motivo_traslado" class="dos">
                            <label for="modalidad_traslado" class="uno">Modalidad Traslado :</label>
                            <input type="text" name="modalidad_traslado" id="modalidad_traslado" class="dos mostrarListaInterna busqueda" placeholder="Elija opción" readonly>
                            <div class="lista rowTwo uno" id="listaModalidad">
                                <ul>
                                    <?php echo $this->listaModalidad?>
                                </ul> 
                            </div>
                            <label for="tipo_envio">Tipo Envio</label>
                            <input type="text" name="tipo_envio" id="tipo_envio" class="dos mostrarListaInterna busqueda" placeholder="Elija opción" readonly>
                            <div class="lista uno rowThree" id="listaEnvio">
                                <ul>
                                    <?php echo $this->listaEnvio?>
                                </ul> 
                            </div>
                            <label for="nro_bultos">N° Bultos/Palets</label>
                            <input type="text" name="nro_bultos" id="nro_bultos" class="tres">
                            <label for="peso_bruto">Peso Bruto:</label>
                            <input type="text" name="peso_bruto" id="peso_bruto" placeholder="Kg.">
                            <label for="autoriza">Autoriza:</label>
                            <input type="text" name="autoriza" id="autoriza" class="dos mostrarListaInterna busqueda" placeholder="Elija opción" readonly>
                            <div class="lista uno rowFive" id="listaAutoriza">
                                <ul>
                                    <?php echo $this->listaPersonal?>
                                </ul> 
                            </div>
                            <label for="despacha">Despacha:</label>
                            <input type="text" name="despacha" id="despacha"  class="dos mostrarListaInterna busqueda" placeholder="Elija opción" readonly>
                            <div class="lista uno rowSix" id="listaDespacha">
                                <ul>
                                    <?php echo $this->listaPersonal?>
                                </ul> 
                            </div>
                            <label for="destinatario">Destinatario:</label>
                            <input type="text" name="destinatario" id="destinatario" class="dos mostrarListaInterna busqueda" placeholder="Elija opción" readonly>
                            <div class="lista uno rowSeven" id="listaDestinatario">
                                <ul>
                                    <?php echo $this->listaPersonal?>
                                </ul> 
                            </div>
                            <label for="observaciones_guia">Observaciones:</label>
                            <textarea name="observaciones" id="observaciones" placeholder="Observaciones" class="dos"></textarea>    
                        </div>
                        <p><strong>Datos del Conductor</strong></p>
                        <div class="dos_columnas_interna">
                            <label for="dni_conductor">DNI :</label>
                            <input type="text" name="dni_conductor" id="dni_conductor">
                            <label for="nombre_conductor">Nombre :</label>
                            <input type="text" name="nombre_conductor" id="nombre_conductor">
                            <label for="licencia_conducir">N° Licencia :</label>
                            <input type="text" name="licencia_conducir" id="licencia_conducir">
                            <label for="nro_certificado">N°.Cert.Inscrip.:</label>
                            <input type="text" name="nro_certificado" id="nro_certificado">
                        </div>
                        <p><strong>Datos del Vehiculo</strong></p>
                        <div class="dos_columnas_interna">
                            <label for="marca">Marca :</label>
                            <input type="text" name="marca" id="marca">
                            <label for="placa">Nro. Placa :</label>
                            <input type="text" name="placa" id="placa">
                            <label for="configuracion">Conf. Vehicular :</label>
                            <input type="text" name="configuracion" id="configuracion">
                        </div>
                    </div>
                </div>
            </form>
            
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