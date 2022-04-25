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
                    <input type="hidden" name="elabora" id="elabora">
                    <input type="hidden" name="aprobado" id="aprobado">

                    <div class="barraOpciones primeraBarra">
                        <span>Datos Generales</span>
                        <div>
                            <button type="button" id="preview" title="Vista Previa" class="boton3">
                                <i class="fab fa-wpexplorer"></i> Ver Pedido
                            </button>
                            <button type="button" id="requestAprob" data-estado="55" title="Solicitar Cotizacion" class="boton3">
                                <i class="fas fa-award"></i> Enviar Cotización
                            </button>
                            <button type="button" id="closeCotiz"  data-estado="56" title="Cerrar Proceso" class="boton3">
                                <i class="fas fa-door-closed"></i> Cerrar Cotizaciones
                            </button>
                            <button type="button" id="closeProcess" title="Cierra la ventana actual" class="boton3">
                                <i class="fas fa-window-close"></i>
                            </button>
                        </div>
                    </div>
                    <div class="dataProceso_2">
                        <div class="seccion_izquierda">
                            <div class="column4_55">
                                <div class="column2_3957">
                                    <label for="numero">Número:</label>
                                    <input type="text" name="numero" id="numero" readonly>
                                </div>
                                <div class="column2_46">
                                    <label for="emision">Emisión:</label>
                                    <input type="date" name="emision" id="emision" readonly>
                                </div>
                            </div>
                            <div class="column2">
                                <label for="costos">CCostos:</label>
                                <input type="text" name="costos" id="costos" readonly>
                            </div>
                            <div class="column2">
                                <label for="area">Area:</label>
                                <input type="text" name="area" id="area" readonly>
                            </div>
                        </div>
                        <div class="seccion_medio">
                            <div class="column2">
                                <label for="transporte">Transporte:</label>
                                <input type="text" name="transporte" id="transporte" readonly>
                            </div>
                            <div class="column2">
                                <label for="concepto">Concepto:</label>
                                <input type="text" name="concepto" id="concepto">
                            </div>
                            <div class="column2">
                                <label for="solicitante">Solicitante:</label>
                                <input type="text" name="solicitante" id="solicitante">
                            </div>
                        </div>
                        <div class="seccion_derecha">
                            <div class="column4_55">
                                <div class="column2_3957">
                                    <label for="tipo">Tipo :</label>
                                    <input type="text" name="tipo" id="tipo">
                                </div>
                                <div class="column2_46">
                                    <label for="vence">Vence :</label>
                                    <input type="date" name="vence" id="vence">
                                </div>
                            </div>
                            <div class="column2">
                                <label for="estado">Estado:</label>
                                <input type="text" name="estado" id="estado" class="textoCentro w35por estado" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="barraOpciones">
                        <span>Especificaciones Técnicas o Descripción del Pedido</span>
                    </div>
                    <div class="textAreaEnter">
                        <textarea name="espec_items" id="espec_items" rows="2" class="w100p" readonly></textarea>
                    </div>
                    <div class="barraOpciones">
                        <span>Detalles</span>
                    </div>
                    <div class="tablaInterna mininoTablaInterna">
                        <table class="tabla" id="tablaDetalles">
                            <thead>
                                <tr>
                                    <th>...</th>
                                    <th>Item</th>
                                    <th>Codigo</th>
                                    <th>Descripcion</th>
                                    <th>Und.</th>
                                    <th>Cant.</th>
                                    <th>Nro.</br>Parte</th>
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
    <div class="modal" id="espera">
        <div class="ventanaEspera">
            <img src="<?php echo constant('URL');?>public/img/sendmail.gif" alt="">
        </div>
    </div>
    <div class="cabezaModulo">
        <h1>Cotizar Bienes/Servicios</h1>
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
    <script src="<?php echo constant('URL');?>public/js/funciones.js"></script>
    <script src="<?php echo constant('URL');?>public/js/cotizacion.js?"></script>
</body>
</html>