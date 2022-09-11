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
                <div class="datosConsulta">
                        <label for="tipo" class="item1">Tipo : </label>
                        <select name="tipoSearch" id="tipoSearch" class="item2">
                            <option value="37">Bienes</option>
                            <option value="38">Servicios</option>
                        </select>
                        <label for="costosSearch" class="item3">Centro de Costos </label>
                        <select name="costosSearch" id="costosSearch" class="item4">
                            <?php echo $this->listaCostos ?>
                        </select>
                        <label for="mes" class="item5">Mes</label>
                        <input type="number" name="mesSearch" id="mesSearch" value="<?php echo date("m")?>" class="textoCentro item6">
                        <label for="anio" class="item7">Año :</label>
                        <input type="number" name="anioSearch" id="anioSearch" value="<?php echo date("Y")?>" class="textoCentro item8">
                        <label for="ordenSearch" class="item9">Orden :</label>
                        <input type="text" name="ordenSearch" id="ordenSearch" class="item10">
                        <label for="almacenSearch" class="item11">Almacen :</label>
                        <select name="almacenSearch" id="almacenSearch" class="item12">
                            <?php echo $this->listaAlmacen ?>
                        </select>
                        <label for="conceptoSearch" class="item13">Concepto : </label>
                        <input type="text" name="conceptoSearch" id="conceptoSearch" class="item14">
                    
                </div>

                <button type="button">Procesar</button> 
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
                    <th>Pedido</th>
                    <th>Aprobacion</br>Pedido</th>
                    <th>Codigo</th>
                    <th>UND</th>
                    <th>Descripcion</th>
                    <th>Cant</br>Sol.</th>
                    <th>Orden</th>
                    <th>Fecha</br>Orden</th>
                    <th>Proveedor</th>
                    <th>Cant.</br>Recibida</th>
                    <th>Saldo</br>Recibir</th>
                    <th>Fecha</br>Envio</br>Proveedor</th>
                    <th>Dias</br>Entrega</th>
                    <th>Fecha</br>Entrega</br>Proveedor</th>
                    <th>Dias</br>Atraso</th>
                    <th>Semaforo</th>
                    <th>Nota</br>Ingreso</th>
                    <th>Guia</br>Ingreso</th>
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