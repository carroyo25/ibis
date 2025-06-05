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
                    <input type="hidden" name="codigo_transporte" id="codigo_transporte">
                    <input type="hidden" name="codigo_solicitante" id="codigo_solicitante">
                    <input type="hidden" name="codigo_tipo" id="codigo_tipo">
                    <input type="hidden" name="codigo_pedido" id="codigo_pedido">
                    <input type="hidden" name="codigo_estado" id="codigo_estado">
                    <input type="hidden" name="codigo_atencion" id="codigo_atencion">
                    <input type="hidden" name="emitido" id="emitido">
                    <input type="hidden" name="espec_items" id ="espec_items">

                    <div class="barraOpciones primeraBarra">
                        <span>Datos Generales</span>
                        <div>
                            <button type="button" id="closeReq" title="Atender todos el pedido por almacen" class="boton3 oculto">
                                <p><i class="far fa-save"></i> Culminar Pedido</p> 
                            </button>
                            <button type="button" id="preview" title="Vista Previa" class="boton3">
                                <i class="fab fa-wpexplorer"></i> Ver Pedido
                            </button>
                            <button type="button" id="requestAprob"  data-rol="3" data-estado="53" title="Solicitar Aprobacion" class="boton3">
                                <i class="fas fa-award"></i> Solicitar Aprobacion
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
                            <input type="date" name="emision" id="emision" class="cerrarLista" readonly>
                            <label for="costos" data-etiqueta="costos">CCostos:</label>
                            <div data-box="lista_costos">
                                <input type="text" name="costos" id="costos" data-box="lista_costos"  readonly>
                            </div>
                            <label for="area" data-etiqueta="partida">Partida:</label>
                            <div data-box="lista_partidas">
                                <input type="text" name="partida" id="partida" data-box="lista_partidas"  readonly>
                            </div>
                        </div>
                        <div class="seccion_medio_grid">
                            <label for="area" data-etiqueta="area">Area:</label>
                            <div data-box="lista_areas">
                                <input type="text" name="area" id="area" readonly>
                            </div>
                            <label for="concepto" data-etiqueta="concepto">Concepto:</label>
                            <input type="text" name="concepto" id="concepto" readonly>
                            <label for="solicitante" data-etiqueta="solicitante">Solicitante:</label>
                            <div data-box="lista_solicitante">
                                <input type="text" name="solicitante" id="solicitante" readonly>
                            </div>
                        </div>
                        <div class="seccion_derecha_grid">
                            <label for="tipo" data-etiqueta="tipo">Tipo Pedido:</label>
                            <div data-box="lista_tipo">
                                <input type="text" name="tipo" id="tipo" readonly>
                            </div>
                            <label for="fecha_entrega" data-etiqueta="fecha_entrega">Fecha<br/>Entrega :</label>
                            <input type="date" name="fecha_entrega" id="fecha_entrega" class="cerrarLista">
                            
                            <label for="estado_consulta" data-etiqueta="label_estado_consulta">Estado:</label>
                            <input type="text" name="estado_consulta" id="estado_consulta" class="textoCentro estado procesando" data-etiqueta="estado_consulta" readonly>
                        </div>
                    </div>
                    <div class="barraOpciones">
                        <span>Detalles</span>
                    </div>
                    <div class="tablaInterna mininoTablaInterna">
                        <table class="tabla" id="tablaDetalles">
                            <thead>
                                <tr>
                                    <th width="3%">...</th>
                                    <th width="5%">Item</th>
                                    <th width="10%">Codigo</th>
                                    <th>Descripcion</th>
                                    <th width="5%">Und.</th>
                                    <th width="7%">Cant.</br>Pedida</th>
                                    <th width="7%">Cant.</br>Atendida</th>
                                    <th width="7%">Nro.</br>Parte</th>
                                    <th>Observaciones</th>
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
            <table id="tablaExistencias" class="tabla">
                <thead>
                    <tr>
                        <th>Almacen</th>
                        <th>Cantidad</th>
                    </tr>
                </thead>
                <tbody id="tablaExistencias_body">
                    
                </tbody>
            </table>
            <div class="opcionesArchivos">
                <button type="button" class="boton3" id="btnConfirmAtach">Aceptar</button>
            </div>
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
                                <button class="boton3 oculto" id="btnAtach"><i class="fas fa-paperclip" ></i></button>
                            </div>
                            <div class="messaje">
                                <div contenteditable="true">

                                </div>
                            </div>
                            <ul class="atachs oculto">

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
    <div class="modal" id="pregunta">
        <div class="ventanaPregunta">
            <h3>Desea culminar el pedido?</h3>
            <div>
                <button type="button" id="btnAceptarPregunta">Aceptar</button>
                <button type="button" id="btnCancelarPregunta">Cancelar</button>
            </div>
        </div>
    </div>
    <div class="cabezaModulo">
        <h1>Atención de Pedidos (Almacen)</h1>
        <div>
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
                    <th>Num.</th>
                    <th>Emision</th>
                    <th>Vencimiento</th>
                    <th>Descripción</th>
                    <th>Centro Costos</th>
                    <th>Responsable</th>
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
    <div class="modal" id="vistaprevia">
        <div class="ventanaVistaPrevia">
            <div class="tituloVista">
                <h3>Vista Previa</h3>
                <a href="#" id="closePreview" title="Cerrar Ventana"><i class="fas fa-window-close"></i></a>
            </div>
            <iframe src=""></iframe>
        </div>
    </div>
    <script src="<?php echo constant('URL');?>public/js/jquery.js"></script>
    <script src="<?php echo constant('URL');?>public/js/funciones.js?<?php echo constant('VERSION')?>"></script>
    <script src="<?php echo constant('URL');?>public/js/atencion.js?<?php echo constant('VERSION')?>"></script>
</body>
</html>