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
                    <input type="hidden" name="codigo_tipo" id="codigo_tipo">
                    <input type="hidden" name="codigo_almacen" id="codigo_almacen">
                    <input type="hidden" name="codigo_pedido" id="codigo_pedido">
                    <input type="hidden" name="codigo_orden" id="codigo_orden">
                    <input type="hidden" name="codigo_estado" id="codigo_estado">
                    <input type="hidden" name="codigo_entidad" id="codigo_entidad">
                    <input type="hidden" name="codigo_moneda" id="codigo_moneda">
                    <input type="hidden" name="ruc_entidad" id="ruc_entidad">
                    <input type="hidden" name="direccion_entidad" id="direccion_entidad">
                    <input type="hidden" name="telefono_entidad" id="telefono_entidad">
                    <input type="hidden" name="correo_entidad" id="correo_entidad">
                    <input type="hidden" name="codigo_verificacion" id="codigo_verificacion">
                    <input type="hidden" name="telefono_contacto" id="telefono_contacto">
                    <input type="hidden" name="correo_contacto" id="correo_contacto">
                    <input type="hidden" name="vista_previa" id="vista_previa">
                    <input type="hidden" name="emitido" id="emitido">
                    <input type="hidden" name="pedidopdf" id="pedidopdf">
                    <input type="hidden" name="proforma" id="proforma">
                    <input type="hidden" name="retencion" id="retencion">
                    <input type="hidden" name="nivel_atencion" id="nivel_atencion">
                    <input type="hidden" name="codigo_pago" id="codigo_pago">
                    <input type="hidden" name="sw" id="sw" value="0">
                    <input type="hidden" name="detalle" id="detalle">

                    <div class="barraOpciones primeraBarra">
                        <span>Datos Generales</span>
                        <div>
                            <button type="button" id="addMessage" title="Comentarios" class="boton3">
                                <i class="far fa-comments"></i> Observaciones 
                            </button>
                            <button type="button" id="preview" title="Vista Previa" class="boton3">
                                <i class="far fa-file-pdf"></i> Vista Previa
                            </button>
                            <button type="button" id="requestAprob"  title="Solicitar Aprobacion" class="boton3" data-rol="5">
                                <i class="fas fa-signature"></i> Autorizar Proceso
                            </button>
                            <button type="button" id="verCotizacion" title="Ver cotizacion" class="boton3">
                                <i class="far fa-file-pdf"></i> Ver Cotización
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
                                    <label for="numero">Orden Nro:</label>
                                    <input type="text" name="numero" id="numero" class="cerrarLista" readonly>
                                </div>
                                <div class="column2_46">
                                    <label for="emision">Emisión:</label>
                                    <input type="date" name="emision" id="emision" class="cerrarLista" value="<?php echo date("Y-m-d");?>">
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
                            <div class="column2">
                                <label for="concepto">Concepto:</label>
                                <input type="text" name="concepto" id="concepto" class="cerrarLista" readonly>
                            </div>
                        </div>
                        <div class="seccion_medio">
                            <div class="column4_55">
                                <div class="column2_3957">
                                    <label for="moneda">Moneda :</label>
                                    <input type="text" name="moneda" id="moneda" class="cerrarLista" readonly>
                                </div>
                                <div class="column2_46">
                                    <label for="total">Total :</label>
                                    <input type="text" name="total" id="total" class="cerrarLista textoDerecha pr5px" readonly>
                                </div>
                            </div>
                            <div class="column4_55">
                                <div class="column2_3957">
                                    <label for="tipo">Tipo :</label>
                                    <input type="text" name="tipo" id="tipo" class="cerrarLista" readonly>
                                </div>
                                <div class="column2_46">
                                    <label for="fentrega">Fec.Entrega :</label>
                                    <input type="date" name="fentrega" id="fentrega" class="cerrarLista">
                                </div>
                            </div>
                            <div class="column4_55">
                                <div class="column2_3957">
                                    <label for="cpago">Cond.Pago :</label>
                                    <input type="text" name="cpago" id="cpago" class="cerrarLista" readonly>
                                </div>
                                <div class="column2_46">
                                    <label for="estado">Estado:</label>
                                    <input type="text" name="estado" id="estado" class="textoCentro estado procesando" readonly value="EN PROCESO">
                                </div>
                            </div>
                        </div>
                        <div class="seccion_derecha">
                            <div class="column2">
                                <label for="entidad">Entidad:</label>
                                <input type="text" name="entidad" id="entidad" readonly>
                            </div>
                            <div class="column2">
                                <label for="atencion">Atención:</label>
                                <input type="text" name="atencion" id="atencion" readonly>
                            </div>
                            <div class="column2">
                                <label for="transporte">Transporte:</label>
                                <input type="text" name="transporte" id="transporte" class="mostrarLista busqueda" placeholder="Elija una opcion"
                                    readonly>
                                <div class="lista" id="listaTransporte">
                                   <ul>
                                       <?php echo $this->listaTransportes?>
                                   </ul> 
                                </div>
                            </div>
                            <div class="column2">
                                <label for="lentrega">Lugar Entrega:</label>
                                <input type="text" name="lentrega" id="lentrega" class="mostrarLista busqueda" placeholder="Elija una opcion"
                                    readonly>
                                <div class="lista" id="listaAlmacen">
                                   <ul>
                                       <?php echo $this->listaAlmacenes?>
                                   </ul> 
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="barraOpciones">
                        <span>Detalles</span>
                    </div>
                    <div class="tablaInterna mininoTablaInterna">
                        <table class="tabla" id="tablaDetalles">
                            <thead>
                                <tr class="stickytop">
                                    <th>Item</th>
                                    <th>Codigo</th>
                                    <th>Descripcion</th>
                                    <th>Und.</th>
                                    <th>Cant.</th>
                                    <th>Precio</th>
                                    <th>Total</th>
                                    <th>Nro.</br>Parte</th>
                                    <th>Pedido</th>
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
    <div class="modal" id="comentarios">
        <div class="ventanaComentarios">
            <h3>Observaciones</h3>
            <hr>
            <div class="cuerpoComentarios">
                <table class="tabla" id="tablaComentarios">
                    <thead>
                         <tr>
                             <th>Usuario:</th>
                             <th>Fecha:</th>
                             <th>Comentarios</th>
                             <th>...</th>
                         </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <div>
                <button type="button" id="btnAceptarDialogo">Aceptar</button>
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
            <h3>¿Firmar la Orden?</h3>
            <div>
                <button type="button" id="btnAceptarPregunta">Aceptar</button>
                <button type="button" id="btnCancelarPregunta">Cancelar</button>
            </div>
        </div>
    </div>
    <div class="cabezaModulo">
        <h1>Aprobacion de Ordenes</h1>
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
                    <th rowspan="2">Num.</th>  
                    <th rowspan="2">Emision</th>
                    <th rowspan="2">Descripción</th>
                    <th rowspan="2">Centro Costos</th> 
                    <th rowspan="2">Area</th>
                    <th rowspan="2">Atencion</th>
                    <th colspan="3" width="16%">Firmas</th>
                    <tr>
                        <th>Logística</th>
                        <th>Operaciones</th>
                        <th>Finanzas</th>
                    </tr>
                    
                </tr>
            </thead>
            <tbody>
                <?php echo $this->listaOrdenes;?>
            </tbody>
        </table>
    </div>
    <script src="<?php echo constant('URL');?>public/js/jquery.js"></script>
    <script src="<?php echo constant('URL');?>public/js/funciones.js?<?php echo constant('VERSION')?>"></script>
    <script src="<?php echo constant('URL');?>public/js/firmas.js?<?php echo constant('VERSION')?>"></script>
</body>
</html>