<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
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
        <div class="ventanaResumen">
            <form method="post" id="cargoplan">
                <div class="tituloDocumento">
                    <div>
                        <p class="titulo_seccion"><strong> Detalle Cargo Plan : </strong></p>
                    </div>
                    <div>
                        <a href="#" id="closeDocument" title="Cerrar Ventana"><i class="fas fa-window-close"></i></a>
                    </div>
                </div>
                <hr>
                <div class="cuerpoDocumento">
                    <section class="seccion1">
                        <div>
                            <label for="codigo">Código</label>
                            <input type="text" name="codigo" id="codigo" readonly class="pl10">
                        </div>
                        <div>
                            <label for="producto">Descripción</label>
                            <input type="text" name="producto" id="producto" readonly class="pl10">
                        </div>
                        <div>
                            <label for="unidad">Unidad</label>
                            <input type="text" name="unidad" id="unidad" class="drch pr10" readonly>
                        </div>
                        <div>
                            <label for="cantidad">Cant. </br> Solicitada</label>
                            <input type="text" name="cantidad" id="cantidad" class="textoDerecha pr10px" readonly>
                        </div>
                        <div>
                            <label for="estado">Estado:</label>
                            <input type="text" name="estado" id="estado" class="centro destino" readonly>
                        </div>
                    </section>
                    <section class="seccion2">
                        <div>
                            <label for="nropedido">N° Pedido:</label>
                            <input type="text" name="nropedido" id="nropedido" class="drch pr10" readonly>
                        </div>
                        <div>
                            <label for="tipo_pedido">Tipo</label>
                            <input type="text" name="tipo_pedido" id="tipo_pedido" class="centro" readonly>
                        </div>
                        <div>
                            <label for="emision_pedido">Fecha</br>Emisión</label>
                            <input type="date" name="emision_pedido" id="emision_pedido" class="centro unstyled" readonly>
                        </div>
                        <div>
                            <label for="aprobacion_pedido">Fecha</br>Aprobación</label>
                            <input type="date" name="aprobacion_pedido" id="aprobacion_pedido" class="centro unstyled" readonly>
                        </div>
                        <div>
                            <label for="aprobado_por">Aprobado por:</label>
                            <input type="text" name="aprobado_por" id="aprobado_por" class="pl10" readonly>
                        </div>
                        <div>
                            <div></div>
                            <a href="#" id="pdfpedido" class="callpreview"><i class="far fa-file-pdf"></i></a>
                        </div>
                    </section>
                    <section class="seccion3">
                        <div>
                            <label for="orden">N° Orden:</label>
                            <input type="text" name="nroorden" id="nroorden" class="drch pr10" readonly>
                        </div>
                        <div>
                            <label for="emision_orden">Fecha</br>Emisión</label>
                            <input type="date" name="emision_orden" id="emision_orden" class="centro unstyled" readonly>
                        </div>
                        <div>
                            <label for="aprobacion_logistica">Fec. Aprob.</br>Logistica</label>
                            <input type="date" name="aprobacion_logistica" id="aprobacion_logistica" class="centro unstyled" readonly>
                        </div>
                        <div>
                            <label for="aprobacion_operaciones">Fec. Aprob.</br>Operaciones</label>
                            <input type="date" name="aprobacion_operaciones" id="aprobacion_operaciones" class="centro unstyled" readonly>
                        </div>
                        <div>
                            <label for="aprobacion_finanzas">Fec. Aprob.</br>Finanzas</label>
                            <input type="date" name="aprobacion_finanzas" id="aprobacion_finanzas" class="centro unstyled" readonly>
                        </div>
                        <div>
                            <div></div>
                            <a href="#" id="pdforden" class="callpreview"><i class="far fa-file-pdf"></i></a>
                        </div>
                    </section>
                    <section class="seccion4">
                        <div>
                            <label for="ingreso">N° Ingreso:</label>
                            <input type="text" name="ingreso" id="ingreso" class="drch pr5" readonly>
                        </div>
                        <div>
                            <label for="fecha_ingreso">Fecha Ingreso</label>
                            <input type="date" name="fecha_ingreso" id="fecha_ingreso" class="centro unstyled" readonly>
                        </div>
                        <div>
                            <label for="ingresada">Cantidad</br>Ingreso</label>
                            <input type="text" name="ingresada" id="ingresada" class="textoDerecha pr10px" readonly>
                        </div>
                        <div>
                            <label for="enviada">Porcentaje</br>Recibido</label>
                            <div class="porcentaje textoCentro" id="ingresado_porcentaje"><span id="porcentaje_ingresado">0%</span></div>
                        </div>
                        <div></div>
                        <div>
                            <div></div>
                            <a href="#" id="pdfingreso" class="callpreview"><i class="far fa-file-pdf"></i></a>
                        </div>
                    </section>
                    <section class="seccion5">
                        <div>
                            <label for="despacho">N°. Despacho:</label>
                            <input type="text" name="despacho" id="despacho" readonly>
                        </div>
                        <div>
                            <label for="fecha_salida">Fecha Salida</label>
                            <input type="date" name="fecha_salida" id="fecha_salida" class="centro unstyled" readonly>
                        </div>
                        <div>
                            <label for="enviada">Cantidad</br>Despachada</label>
                            <input type="text" name="enviada" id="enviada" class="textoDerecha pr10px" readonly>
                        </div>
                        <div>
                            <label for="enviada">Porcentaje</br>Enviado</label>
                            <div class="porcentaje textoCentro" id="enviado_porcentaje"><span id="porcentaje_despacho">0%</span></div>
                        </div>
                        <div></div>
                        <div>
                            <div></div>
                            <a href="#" id="pdfsalida" class="callpreview"><i class="far fa-file-pdf"></i></a>
                        </div>
                    </section>
                </div>
            </form>
        </div>
    </div>
    <div class="cabezaModulo">
        <h1>Cargo Plan</h1>
        <div>
            <a href="#" id="irInicio"><i class="fas fa-home"></i></a>
        </div>
    </div>
    <div class="barraTrabajo">
        <form action="#" id="formConsulta">
            <div class="variasConsultasColumna">
                <div class="datosConsultaCargoPlan">
                        <div class="w5por">
                            <label for="tipo">Tipo : </label>
                            <select name="tipoSearch" id="tipoSearch">
                                <option value="37">Bienes</option>
                                <option value="38">Servicios</option>
                            </select>    
                        </div>
                        <div>
                            <label for="costosSearch">Centro de Costos </label>
                            <select name="costosSearch" id="costosSearch">
                                <?php echo $this->listaCostos ?>
                            </select>
                        </div>
                        <!-- 
                             value="<?php echo date("m")?>"
                              value="<?php echo date("m")?>"
                        
                        <div class="w5por">
                            <label for="mes" class="item5">Mes Orden:</label>
                            <input type="number" name="mesSearch" id="mesSearch" class="textoCentro">
                        </div>-->
                        <div>
                            <label for="codigo">Codigo:</label>
                            <input type="text" name="codigoSearch" id="codigoSearch" class="textoCentro">
                        </div>
                        <div  class="w5por">
                            <label for="ordenSearch">Orden :</label>
                            <input type="text" name="ordenSearch" id="ordenSearch">
                        </div>
                        <div  class="w5por">
                            <label for="ordenSearch">Pedido :</label>
                            <input type="text" name="pedidoSearch" id="pedidoSearch">
                        </div>
                        <div class="w25por">
                            <label for="conceptoSearch">Concepto : </label>
                            <input type="text" name="conceptoSearch" id="conceptoSearch">
                        </div>
                        <div class="procesos">
                            <div class="procesando"><a href="#">10</a></div>
                            <div class="emitido"><a href="#">20</div>
                            <div class="consulta"><a href="#">30</a></div>
                            <div class="aprobacion"><a href="#">40</a></div>
                            <div class="aprobado"><a href="#">50</a></div>
                            <div class="orden"><a href="#">60</a></div>
                            <div class="firmas"><a href="#">70</a></div>
                            <div class="recepcion"><a href="#">80</a></div>
                            <div class="despacho"><a href="#">90</a></div>
                            <div class="culminado"><a href="#">100</a></div>
                        </div>
                </div>
                <div class="botonesConsulta">
                        <button type="button" id="btnProcesa">Procesar</button>
                        <button type="button" id="btnExporta">Exportar</button>
                    </div>
            </div>
        </form>
    </div>
    <div class="itemsCargoPlanner">
        <table>
            <thead>
                <tr >
                    <th>Items</th>
                    <th>Estado</br>Actual</th>
                    <th>Proyecto</th>
                    <th>Area</th>
                    <th>Partida</th>
                    <th>tipo</th>
                    <th>Pedido</th>
                    <th>Emisión</br>Pedido</th>
                    <th>Aprobacion</br>Pedido</th>
                    <th>Concepto</th>
                    <th>Codigo</th>
                    <th>UND</th>
                    <th>Descripcion</th>
                    <th>Cant</br>Sol.</th>
                    <th>Orden</th>
                    <th>Fecha</br>Orden</th>
                    <th>Cant.</br>Orden</th>
                    <th>Proveedor</th>
                    <th>Fecha</br>Entrega</br>Proveedor</th>
                    <th>Dias</br>Entrega</th>
                    <th>Dias</br>Atraso</th>
                    <th>Semaforo</th>
                    <th>Cant.</br>Recibida</th>
                    <th>Saldo</br>Recibir</th>
                    <th>Nota</br>Ingreso</th>
                    <th>Guia</br>Ingreso</th>
                    <th>Fecha</br>Ingreso</th>
                    <th>Nota</br>Salida</th>
                    <th>Guia</br>Remisión</th>
                    <th>Fecha</br>Guia</br>Remisión</th>
                    <th>Cant</br>Rec.</br>Obra</th>
                    <th>Nota</br>Ing.</br>Obra</th>
                    <th>Fecha</br>Recep</br>Obra</th>
                    <th>Observaciones</th>
                </tr>
            </thead>
            <tbody>
                    <?php echo $this->listaCargoPlan?>
            </tbody>
        </table>
    </div>
    <script src="<?php echo constant('URL');?>public/js/jquery.js"></script>
    <script src="<?php echo constant('URL');?>public/js/funciones.js?<?php echo constant('VERSION')?>"></script>
    <script src="<?php echo constant('URL');?>public/js/cargoplanner.js?<?php echo constant('VERSION')?>"></script>
</body>
</html>