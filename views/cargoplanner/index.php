<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="content-type" content="application/vnd.ms-excel; charset=UTF-8">
    <title>Document</title>
</head>
<body>
    <div class="modal" id="esperar">
        <div class="loadingio-spinner-spinner-5ulcsi06hlf">
            <div class="ldio-fifgg00y5y">
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
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
                                <option value="-1">Seleccione una opcion</option>
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
                            <div class="item_anulado"><a href="#">0%</a></div>
                            <div class="item_aprobado"><a href="#">15%</div>
                            <div class="item_orden"><a href="#">20%</a></div>
                            <div class="item_parcial"><a href="#">25%</a></div>
                            <div class="item_ingreso_parcial"><a href="#">40%</a></div>
                            <div class="item_ingreso_total"><a href="#">50%</a></div>
                            <div class="item_registro_salida"><a href="#">60%</a></div>
                            <div class="item_transito"><a href="#">75%</a></div>
                            <div class="item_obra"><a href="#">100%</a></div>
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
        <table id="cargoPlanDescrip">
            <thead>
                <tr class="stickytop">
                    <th width="30px">Items</th>
                    <th>Estado</br>Actual</th>
                    <th>Codigo</br>Proyecto</th>
                    <th width="150px">Area</th>
                    <th width="150px">Partida</th>
                    <th>Atencion</th>
                    <th>Tipo</th>
                    <th>Año</br> Pedido</th>
                    <th>N°</br>Pedido</th>
                    <th width="80px">Creación</br>Pedido</th>
                    <th width="80px">Aprobación</br>Pedido</th>
                    <th>Codigo del</br>Bien/Servicio</th>
                    <th>Unidad</br>Medida</th>
                    <th width="400px">Descripcion del Bien/Servicio</th>
                    <th>Cantidad</br>Pedida</th>
                    <th width="40px">Tipo</br>Orden</th>
                    <th width="50px">Año</br>Orden</th>
                    <th>N°</br>Orden</th>
                    <th>Fecha</br>Orden</th>
                    <th>Cantidad</br>Orden</th>
                    <th width="200px">Descripcion Proveedor</th>
                    <th>Fecha Entrega</br>Proveedor</th>
                    <th width="50px">Cantidad</br>Recibida</th>
                    <th>Saldo por</br>Recibir</th>
                    <th width="50px">Días</br>Entrega</th>
                    <th>Días</br>Atrazo</th>
                    <th>Semaforo</th>
                    <th>Nota</br>Ingreso</th>
                    <th width="100px">Guia</br>Ingreso</th>
                    <th>Fecha</br>Ingreso</th>
                    <th>Nota</br>Salida</th>
                    <th>Guia</br>Remision</th>
                    <th>Fecha Guia</br>Remision</th>
                    <th>Cantidad</br>Recibida</br>Obra</th>
                    <th>Nota</br>Ingreso</br>Obra</th>
                    <th>Fecha</br>Recep</br>Obra</th>
                    <th>Estado</br>Pedido</th>
                    <th>Estado</br>Item</th>
                    <th>N°</br>Parte</th>
                    <th width="150px">Codigo</br>Activo</th>
                    <th>Operador</br>Logístico</th>
                    <th>Tipo</br>Transporte</th>
                    <th>Observaciones/Concepto</th>
                </tr>
            </thead>
            <tbody>
                <?php //echo $this->listaCargoPlan?>
            </tbody>
        </table>
    </div>
    
    <script src="<?php echo constant('URL');?>public/js/jquery.js"></script>
    <script src="<?php echo constant('URL');?>public/js/funciones.js?<?php echo constant('VERSION')?>"></script>
    <script src="<?php echo constant('URL');?>public/js/tableToExcel.js"></script>
    <script src="<?php echo constant('URL');?>public/js/cargoplanner.js?<?php echo constant('VERSION')?>"></script>
</body>
</html>