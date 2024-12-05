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
    <div class="mensaje">
        <p></p>
    </div>
    <div class="modal" id="esperarCargo">
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
            <h1>Espere... Este proceso demora unos minutos</h1>
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
                            <input type="text" name="emision_pedido" id="emision_pedido" class="centro unstyled" readonly>
                        </div>
                        <div>
                            <label for="aprobacion_pedido">Fecha</br>Aprobación</label>
                            <input type="text" name="aprobacion_pedido" id="aprobacion_pedido" class="centro unstyled" readonly>
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
                        <table id="tablaOrdenes">
                            <caption>Ordenes</caption>
                            <thead>
                                <th>Nro. Orden</th>
                                <th>Fecha</th>
                                <th>Proveedor</th>
                                <th>CC</th>
                                <th>...</th>
                            </thead>
                            <tbody>
                                 
                            </tbody>
                        </table>
                    </section>
                    <section class="seccion4">
                        <table id="tablaIngresos">
                            <caption>Ingresos</caption>
                            <thead>
                                <th>Nro. Ingreso</th>
                                <th>Fecha Ingreso</th>
                                <th>Guia Proveedor</th>
                                <th>...</th>
                            </thead>
                            <tbody>
                                 
                            </tbody>
                        </table>
                    </section>
                    <section class="seccion5">
                        <table id="tablaDespachos">
                            <caption>Despachos</caption>
                            <thead>
                                <th>Nro. Salida</th>
                                <th>Fecha Salida</th>
                                <th>Nro. Guia</th>
                                <th>Nro. Referido</th>
                                <th>...</th>
                            </thead>
                            <tbody>
                                 
                            </tbody>
                        </table>
                    </section>
                    <section class="seccion5">
                        <table id="tablaObra">
                            <caption>Registros Obra</caption>
                            <thead>
                                <th>Nro. Nota</th>
                                <th>Fecha Ingreso</th>
                                <th>...</th>
                            </thead>
                            <tbody>
                                 
                            </tbody>
                        </table>
                    </section>
                </div>
            </form>
        </div>
    </div>
    <div class="modal" id="filtros">
        <div class="ventanaFiltros">
            <div   div class="tituloDocumento">
                <div>
                    <p class="titulo_seccion"><strong> Filtros Avanzados : </strong></p>
                </div>
                <div>
                    <a href="#" id="closeFilters" title="Cerrar Ventana"><i class="fas fa-window-close"></i></a>
                </div>
            </div>
            <hr>
            <div class="cuerpoDocumento">
                <div class="proyectos">
                    <ul class="listaCostos" id="costos">
                        
                    </ul>
                </div>
                <div class="fechas">
                    <div>
                        <label for="desde">Fecha Inicio:</label>
                        <input type="date" name="fecha_inicio" id="fecha_inicio">
                    </div>
                    <div>
                        <label for="desde">Fecha Final:</label>
                        <input type="date" name="fecha_final" id="fecha_final">
                    </div>
                </div>
                <div class="porcentajes">
                </div>
                <div class="opciones">
                    <button type="button" id="btnAceptarFiltro" class="boton5">Aceptar</button>
                    <button type="button" id="btnCancelarFiltro" class="boton5">Cancelar</button>
                </div>
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
    <div class="modal" id="vistaAdjuntos">
        <div class="ventanaAdjuntos">
            <div class="tituloAdjuntos">
                <h3>Adjuntos Orden</h3>
                <a href="#" id="closeAtach" title="Cerrar Ventana"><i class="fas fa-window-close"></i></a>
            </div>
            <ul id="listaAdjuntos">

            </ul>
            <iframe src=""></iframe>
        </div>
    </div>
    <div class="cabezaModulo">
        <h1>Cargo Plan</h1>
        <div>
            <a href="#" id="filtrosAvanzados" class="oculto"><i class="fab fa-searchengin"></i><p>Filtros</p></a>
            <a href="1" id="excelFile" class="exportReport"><i class="fas fa-file-excel"></i><p>Exportar Excel</p></a>
            <a href="2" id="csvFile" class="exportReport oculto"><i class="fas fa-file-csv"></i><p>Exportar CSV</p></a>
            <a href="3" id="excelSpoutFile" class="exportReport oculto"><i class="fas fa-file-excel"></i><p>Exportar Total Rapido</p></a>
            <a href="#" id="irInicio"><i class="fas fa-home"></i><p>Inicio</p></a>
        </div>
    </div>
    <div class="barraTrabajo">
        <form action="#" id="formConsulta">
            <input type="hidden" name="estado_item" id="estado_item">
            <div class="variasConsultasColumna">
                <div class="datosConsultaCargoPlan">
                    <div class="parametrosConsulta">
                        <div>
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
                        <div>
                            <label for="ordenSearch">N° Orden :</label>
                            <input type="text" name="ordenSearch" id="ordenSearch">
                        </div>
                        <div>
                            <label for="ordenSearch">N° Pedido :</label>
                            <input type="text" name="pedidoSearch" id="pedidoSearch">
                        </div>
                        <div>
                            <label for="descripSearch">Descripción Item:</label>
                            <input type="text" name="descripSearch" id="descripSearch">
                        </div>
                        <div>
                            <label for="conceptoSearch">Concepto : </label>
                            <input type="text" name="conceptoSearch" id="conceptoSearch">
                        </div>
                    </div>
                    <div class="procesos">
                        <div class="item_anulado"><a href="105" title="Anulado">0%<p>Anulado</p></a></div>
                        <div class="pedidoCreado"><a href="49" title="Pedido Creado">10%<p>Creado</p></a></div>
                        <div class="item_aprobado"><a href="54" title="Pedido Aprobado">15%<p>Aprob.</p></div>
                        <div class="stock"><a href="52" title="Atencion x Stock">20%<p>Stock</p></a></div>
                        <div class="item_orden"><a href="#" title="con OC/OS">25%<p>OC/OS</p></a></div>
                        <div class="item_parcial"><a href="#" title="Enviado Proveedor">30%<p>Enviado</p></a></div>
                        <div class="item_ingreso_parcial" title="Atencion Parcial"><a href="#">40%<p>Ing.Parcial</p></a></div>
                        <div class="item_ingreso_total" title="Atención Total"><a href="#">50%<p>At.Total</p></a></div>
                        <div class="item_registro_salida" title="Atencion cx compras locales"><a href="230">60%<p>Com.Local</p></a></div>
                        <div class="item_registro_gerencia" title="Pedido Gerencia"><a href="#">70%<p>P.Gerencia</p></a></div>
                        <div class="item_transito" title="En transito"><a href="#">75%<p>Transito</p></a></div>
                        <div class="item_ingreso_parcial" title="Parcial Obra"><a href="#">85%<p>Rec.Parcial</p></a></div>
                        <div class="item_obra" title="En Obra"><a href="#">100%<p>Obra</p></a></div>
                    </div>
                </div>
                <div class="botonesConsulta">
                        <button type="button" id="btnProcesa">Procesar</button>
                        <button type="button" id="btnExporta">Exportar</button>
                    </div>
            </div>
        </form>
    </div>
    <div class="itemsCargoPlanner" id="demo">
        <table id="cargoPlanDescrip">
            <thead>
                <tr class="stickytop">
                    <th width="30px">Items</th>
                    <th style="background:#40D1FB; color:#000; position:relative" data-idcol="1" class="datafiltro">Estado</br>Actual</th>
                    <th style="background:#40D1FB; color:#000">Codigo</br>Proyecto</th>
                    <th style="background:#40D1FB; color:#000" data-idcol="3" class="datafiltro">Area</th>
                    <th style="background:#40D1FB; color:#000" data-idcol="6" class="datafiltro">Tipo</th>
                    <th style="background:#FBD341; color:#000">Cantidad</br>Pedida</th>
                    <th style="background:#FBD341; color:#000">Cantidad</br>Aprobada</th>
                    <th style="background:#FBD341; color:#000">Cantidad</br>para compra</th>
                    <th style="background:#A6CAF0; color:#000" data-idcol="12" class="datafiltro">Codigo del</br>Bien/Servicio</th>
                    <th style="background:#A6CAF0; color:#000">Unidad</br>Medida</th>
                    <th style="background:#A6CAF0; color:#000" width="10%" data-idcol="14" class="datafiltro">Descripcion del Bien/Servicio</th>
                    <th style="background:#AAFFAA; color:#000" width="40px">Tipo</br>Orden</th>
                    <th style="background:#AAFFAA; color:#000" width="50px">Año</br>Orden</th>
                    <th style="background:#AAFFAA; color:#000" data-idcol="17" class="datafiltro">N°</br>Orden</th>
                    <th style="background:#AAFFAA; color:#000">Fecha</br>Orden</th>
                    <th style="background:#AAFFAA; color:#000">Cantidad</br>Orden</th>
                    <th style="background:#AB7FAB; color:#fff" width="10%" data-idcol="22" class="datafiltro">Descripcion Proveedor</th>
                    <th>Fecha Entrega</br>Proveedor</th>
                    <th width="50px">Cantidad</br>Recibida</th>
                    <th width="50px" data-idcol="25" class="datafiltro">Nota</br>Ingreso</th>
                    <th width="50px">Fecha</br>Recepcion Proveedor</th>
                    <th>Saldo por</br>Recibir</th>
                    <th width="50px">Días</br>Entrega</th>
                    <th>Días</br>Atrazo</th>
                    <th>Semaforo</th>
                    <th>Operador</br>Logístico</th>
                    <th data-idcol="40" class="datafiltro">Observaciones/Concepto</th>
                    <th style="background:#40D1FB; color:#000">Tipo</br>Moneda</th>
                    <th style="background:#40D1FB; color:#000">Tipo</br>Cambio</th>
                    <th style="background:#40D1FB; color:#000">Precio</br>Dolares</th>
                    <th style="background:#40D1FB; color:#000">Precio</br>Soles</th>
                    <th style="background:#40D1FB; color:#000">Importe</br>Total</th>
                    <th style="background:#40D1FB; color:#000">Forma</br>Pago</th>
                    <th width="150px" style="background:#40D1FB; color:#000">Referencia</br>Pago</th>
                    <th style="background:#A6CAF0; color:#000">Familia</th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table>
    </div>
    <script src="<?php echo constant('URL');?>public/js/jquery.js"></script>
    <script src="<?php echo constant('URL');?>public/js/funciones.js?<?php echo constant('VERSION')?>"></script>
    <script src="<?php echo constant('URL');?>public/js/cargoplan.js?<?php echo constant('VERSION')?>"></script>
</body>
</html>