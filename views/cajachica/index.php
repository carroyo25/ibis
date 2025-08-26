<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo constant('URL')?>public/css/ibis.css?<?php echo constant('VERSION')?>">
</head>
<body>
    <div class="mensaje">
        <p></p>
    </div>
    <div class="modal" id="esperar">
    </div>
    <div class="modal" id="pregunta">
        <div class="ventanaPregunta">
            <h3>Desea anular el registro?</h3>
            <div>
                <button type="button" id="btnAceptarPregunta">Aceptar</button>
                <button type="button" id="btnCancelarPregunta">Cancelar</button>
            </div>
        </div>
    </div>
    <div class="modal" id="anular">
        <div class="ventanaPregunta">
            <h3>¿Anular el requerimiento?</h3>
            <div>
                <button type="button" id="btnAceptarAnular">Aceptar</button>
                <button type="button" id="btnCancelarAnular">Cancelar</button>
            </div>
        </div>
    </div>
    <div class="modal" id="proceso">
        <div class="ventanaProceso">
            <div class="cabezaProceso">
                <form action="#" id="formProceso" autocomplete="off">
                    <input type="hidden" name="codigo_costos" id="codigo_costos"> 
                    <input type="hidden" name="codigo_area" id="codigo_area">
                    <input type="hidden" name="codigo_transporte" id="codigo_transporte" value="39">
                    <input type="hidden" name="codigo_solicitante" id="codigo_solicitante">
                    <input type="hidden" name="codigo_tipo" id="codigo_tipo" value="37">
                    <input type="hidden" name="codigo_partida" id="codigo_partida">
                    <input type="hidden" name="codigo_pedido" id="codigo_pedido">
                    <input type="hidden" name="codigo_estado" id="codigo_estado" value="49">
                    <input type="hidden" name="codigo_verificacion" id="codigo_verificacion">
                    <input type="hidden" name="dias_atencion" id="dias_atencion" value="10">
                    <input type="hidden" name="codigo_atencion" id="codigo_atencion" value="47">
                    <input type="hidden" name="vista_previa" id="vista_previa">
                    <input type="hidden" name="emitido" id="emitido">
                    <input type="hidden" name="codigo_usuario" id="codigo_usuario">
                    <input type="hidden" name="codigo_entidad" id="codigo_entidad">
                    <input type="hidden" name="total_numero" id="total_numero">
                    <input type="hidden" name="fecha_entrega" id="fecha_entrega">


                    <div class="barraOpciones primeraBarra">
                        <span>Datos Generales</span>
                        <div>
                            <button type="button" id="saveItem" title="Grabar Pedido" class="boton3">
                                <p><i class="far fa-save"></i> Grabar Pedido</p> 
                            </button>
                            <button type="button" id="upAttach" title="Importar Adjuntos" class="boton3">
                                <i class="fas fa-upload"></i> Adjuntar Archivos
                                <span class="button__atach cookie_info" id="atach_counter"></span>
                            </button>
                            <button type="button" id="preview" title="Vista Previa" class="boton3">
                                <i class="fab fa-wpexplorer"></i> Vista Previa
                            </button>
                            <button type="button" id="closeProcess" title="Cerrar" class="boton3">
                                <i class="fas fa-window-close"></i>
                            </button>
                        </div>
                    </div>
                    <div class="dataProceso_2">
                        <div class="seccion_izquierda_grid">
                            <label for="numero" data-etiqueta="numero">Número:</label>
                            <input type="text" name="numero" id="numero" class="cerrarLista" readonly>
                            <label for="emision" data-etiqueta="emision">Emisión:</label>
                            <input type="date" name="emision" id="emision" class="cerrarLista" value="<?php echo date("Y-m-d");?>">
                            <label for="costos" data-etiqueta="costos">CCostos:</label>
                            <div data-box="lista_costos">
                                <input type="text" name="costos" id="costos" class="mostrarLista busqueda" placeholder="Elija una opcion">
                                <div class="lista_grid" id="listaCostos">
                                    <ul>
                                        <?php echo $this->listaCostos?>
                                    </ul> 
                                </div>
                            </div>
                            <label for="partida" data-etiqueta="partida">Partida:</label>
                            <div data-box="lista_partidas">
                                <input type="text" name="partida" id="partida" class="mostrarLista busqueda" placeholder="Elija una opcion">
                                <div class="lista_grid" id="listaPartidas">
                                    <ul>
                                        
                                    </ul>
                                </div>
                            </div>
                            
                        </div>
                        <div class="seccion_medio_grid">
                            <label for="area" data-etiqueta="area">Area:</label>
                            <div data-box="lista_areas">
                                <input type="text" name="area" id="area" class="mostrarLista busqueda" placeholder="Elija una opcion">
                                <div class="lista_grid" id="listaAreas">
                                <ul>
                                    <?php echo $this->listaAreas?>
                                </ul>
                                </div>
                            </div>
                            <label for="concepto" data-etiqueta="concepto">Concepto:</label>
                            <input type="text" name="concepto" id="concepto" class="cerrarLista mayusculas">
                            
                            <label for="solicitante" data-etiqueta="solicitante">Solicitante:</label>
                            <div data-box="lista_solicitante">
                                <input type="text" name="solicitante" id="solicitante" class="mostrarLista busqueda" placeholder="Elija una opcion">
                                <div class="lista_grid" id="listaSolicitantes">
                                    <ul>
                                        <?php echo $this->listaAquarius?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="seccion_derecha_grid">
                            <label for="entidad" data-etiqueta="entidad">Entidad:</label>
                            <div data-box="lista_entidad">
                                <input type="text" name="entidad" id="entidad" class="mostrarLista" placeholder="Elija una opcion">
                                <div class="lista_grid" id="lista_entidad">
                                    <ul>
                                        <?php echo $this->listaEntidades?>
                                    </ul>
                                </div>
                            </div>
                                                    
                            <label for="total" data-etiqueta="total">Total</label>
                            <input type="text" name="total" id="total" class="cerrarLista">
                            
                            <label for="estado" data-etiqueta="estado">Estado:</label>
                            <input type="text" name="estado" id="estado" class="textoCentro estado procesando" readonly value="EN PROCESO">
                        </div>
                    </div>
                    <div class="textAreaEnter oculto">
                        <textarea name="espec_items" id="espec_items" rows="2" class="w100p" readonly></textarea>
                    </div>
                    <div class="barraOpciones">
                        <span>Detalles</span>
                        <button type="button" id="addItem" title="Añadir Item" class="cerrarLista boton3">
                            <i class="far fa-plus-square"></i> Agregar
                        </button>
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
                                    <th width="8%">Precio</th>
                                    <th width="8%">Total</th>
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
    <div class="modal" id="archivos">
        <div class="ventanaArchivos">
            <form action="#" id="fileAtachs" enctype='multipart/form-data'>
                <input type="hidden" name="nropedidoatach" id="nropedidoatach">
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
    <div class="modal" id="busqueda">
        <div class="ventanaBusqueda w50por">
            <div class="tituloVentana">
                <span id="tituloBusqueda">Catálogo Bienes/Servicios</span>
                <div>
                    <a href="#">
                        <i class="fas fa-window-close"></i>
                        <span>Cerrar</span>
                    </a>
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
    <div class="modal" id="vistaprevia">
        <div class="ventanaVistaPrevia">
            <div class="tituloVista">
                <h3>Vista Previa</h3>
                <a href="#" id="closePreview" title="Cerrar Ventana"><i class="fas fa-window-close"></i></a>
            </div>
            <iframe src=""></iframe>
        </div>
    </div>
    <div class="modal" id="sendMail">
        <form action="#" method="post" id="formMails">
            <input type="hidden" name="estadoPedido" id="estadoPedido">
            <div class="ventanaCorreo">
                    <input type="file" name="mailAtach[]" id="mailAtach" multiple class="oculto">
                    <div class="tituloCorreo">
                        <h3 class="w100por">Enviar Correo</h3>
                        <a href="#" id="closeMail" title="Cerrar Ventana"><i class="fas fa-window-close"></i></a>
                        <hr>
                    </div>
                    <div class="cuerpoCorreo">
                        <div class="correoIzq">
                            <div class="asunto">
                                <label for="subject">Asunto :</label>
                                <input type="text" name="subject" id="subject">
                            </div>
                            <div class="opciones">
                                <button class="boton3 js-boton" data-type="bold" type="button"><i class="fas fa-bold"></i></button>
                                <button class="boton3 js-boton" data-type="italic" type="button"><i class="fas fa-italic"></i></button>
                                <button class="boton3" id="btnAtach"><i class="fas fa-paperclip"></i></button>
                            </div>
                            <div class="messaje">
                                <div contenteditable="true" id="mailMessage">

                                </div>
                            </div>
                            <div>
                                <span>CC: <?php echo $_SESSION['correo']?></span>
                            </div>
                            <ul class="atachs">

                            </ul>
                            <div class="commands">
                                <button class="boton3" id="btnConfirmSend">Enviar</button>
                            </div>
                        </div>
                        <div class="correoDerch">
                            <h4>Correos</h4>
                            <table id="listaCorreos" class="tabla">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Correo</th>
                                        <th>...</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
            </div>
        </form>
    </div>
    <div class="cabezaModulo">
        <h1>Compras Caja Chica</h1>
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
                        <label for="tipo">Tipo : </label>
                        <select name="tipoSearch" id="tipoSearch">
                            <option value="37">Bienes</option>
                            <option value="38">Servicios</option>
                        </select>
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
                    <th>Descripción</th>
                    <th>Centro Costos</th>
                    <th>Resposable</th>
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
    <script src="<?php echo constant('URL');?>public/js/jquery.js"></script>
    <script src="<?php echo constant('URL');?>public/js/funciones.js?<?php echo constant('VERSION')?>"></script>
    <script src="<?php echo constant('URL');?>public/js/cajachica.js?<?php echo constant('VERSION')?>"></script>
</body>
</html>