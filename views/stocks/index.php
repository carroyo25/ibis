<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div class="modal" id="proceso">
        <div class="ventanaProceso tamanioProceso">
            <div class="cabezaProceso">
                <form action="#" id="formProceso" autocomplete="off">
                    <input type="hidden" name="codigo_costos" id="codigo_costos"> 
                    <input type="hidden" name="codigo_area" id="codigo_area">
                    <input type="hidden" name="codigo_almacen_origen" id="codigo_almacen_origen">
                    <input type="hidden" name="codigo_almacen_destino" id="codigo_almacen_destino">
                    <input type="hidden" name="codigo_estado" id="codigo_estado">
                    <input type="hidden" name="codigo_despacho" id="codigo_despacho">
                    <input type="hidden" name="codigo_autoriza" id="codigo_autoriza">
                    <input type="hidden" name="codigo_ingreso" id="codigo_ingreso">
                    <input type="hidden" name="codigo_recepcion" id="codigo_recepcion" value="<?php echo $_SESSION['iduser']?>">


                    <div class="barraOpciones primeraBarra">
                        <span>Datos Generales</span>
                        <div>
                            <button type="button" id="updateDocument" title="Cerrar Salida" class="boton3">
                                <i class="far fa-save"></i> Grabar Ingreso
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
                                    <label for="numero">Numero Registro:</label>
                                    <input type="text" name="numero" id="numero" class="cerrarLista textoDerecha pr20px" readonly>
                                </div>
                            </div>
                            <div class="column2">
                                <label for="costos">Ccostos:</label>
                                <input type="text" name="costos" id="costos" readonly>
                            </div>
                        </div>
                        <div class="seccion_medio">
                            <div class="column2">
                                <label for="almacen_origen_despacho">Almacen Origen:</label>
                                <input type="text" name="almacen_origen_ingreso" id="almacen_origen_ingreso" class="busqueda" readonly>
                            </div>
                            <div class="column2">
                                <label for="almacen_destino_despacho">Almacen Destino:</label>
                                <input type="text" name="almacen_destino_ingreso" id="almacen_destino_ingreso" class="mostrarLista busqueda" readonly>
                            </div>
                            
                        </div>
                        <div class="seccion_derecha">
                            <div class="column2">
                                <label for="recepciona">Autoriza:</label>
                                <input type="text" name="autoriza" id="autoriza" class="mostrarLista busqueda" placeholder="Elija opción" readonly>
                                <div class="lista uno rowFive" id="listaRecepciona">
                                <ul>
                                    <?php echo $this->listaRecepciona?>
                                </ul> 
                            </div>
                            </div>
                            <div class="column4_55">
                                <div class="column2_3957">
                                    <label for="guia">N° Guia :</label>
                                    <input type="text" name="cnumguia" id="guia">
                                </div>
                                <div class="column2_46">
                                    <label for="RS :">R.S.:</label>
                                    <input type="text" name="referido" id="referido">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="barraOpciones">
                        <span>Detalles</span>
                        <div>
                            <button type="button" id="itemsImport" title="Importar Items" class="cerrarLista boton3">
                                <i class="fas fa-upload"></i> Buscar Guias
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
                                        <th width="7%">Cantidad <br/> Enviada</th>
                                        <th width="7%">Cantidad <br/> Recep.</th>
                                        <th class="">Observaciones</th>
                                        <th class="">Area</th>
                                        <th class="">Fecha </br> Vencimiento</th>
                                        <th class="">Ubicación</th>
                                        <th class="">Pedido</th>
                                        <th class="">Orden</th>
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
        <div class="ventanaBusqueda w50por">
            <div class="tituloVentana">
                <span id="tituloBusqueda">Catálogo Bienes/Servicios</span>
                <div>
                    <a href="#"><i class="fas fa-window-close"></i></a>
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
        <h1>Control de Almacen</h1>
        <div>
            <a href="#" id="nuevoRegistro"><i class="far fa-file"></i><p>Nuevo</p></a>
            <a href="#" id="irInicio"><i class="fas fa-home"></i><p>Inicio</p></a>
        </div>
    </div>
    <div class="barraTrabajo">
        <form action="#" id="formConsulta">
            <div class="variasConsultas">
                    <div>
                        <label for="tipo">Codigo : </label>
                        <input type="text" name="codigoBusqueda" id="codigoBusqueda">
                    </div>
                    <div>
                        <label for="costosSearch">Centro de Costos: </label>
                        <select name="costosSearch" id="costosSearch" class="item4">
                            <?php echo $this->listaCostosSelect ?>
                        </select>
                    </div>
                    <div>
                        <label for="almacenSearch">Almacen</label>
                        <select name="almacenSearch" id="almacenSearch">
                            
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
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Codigo</th>
                    <th>Descripcion</th>
                    <th>Unidad</th>
                    <th>Almacen</th>
                    <th>Centro Costos</th>
                    <th>Cantidad<br>Ingreso</th>
                    <th>Cantidad<br>Salida</th>
                    <th>Saldo</th>
                </tr>
            </thead>
            <tbody>
                <?php echo $this->listaItems;?>
            </tbody>
        </table>
    </div>
    <script src="<?php echo constant('URL');?>public/js/jquery.js"></script>
    <script src="<?php echo constant('URL');?>public/js/funciones.js"></script>
    <script src="<?php echo constant('URL');?>public/js/stocks.js?<?php echo constant('VERSION')?>"></script>
</body>
</html>