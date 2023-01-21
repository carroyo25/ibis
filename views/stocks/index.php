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
        <div class="ventanaProceso ">
            <div class="cabezaProceso">
                <form action="#" id="formProceso" autocomplete="off" enctype='multipart/form-data'>
                    <input type="hidden" name="codigo_costos" id="codigo_costos"> 
                    <input type="hidden" name="codigo_almacen" id="codigo_almacen">
                    <input type="hidden" name="codigo_estado" id="codigo_estado">
                    <input type="hidden" name="codigo_autoriza" id="codigo_autoriza">
                    <input type="hidden" name="codigo_stock" id="codigo_stock">
                    <input type="hidden" name="codigo_tipo" id="codigo_tipo">
                    <input type="hidden" name="tipo" id="tipo" value="37">
                    <input type="file" name="fileUpload" id="fileUpload" accept=".xls,.xlsx" class="oculto" >

                    <div class="barraOpciones primeraBarra">
                        <span>Datos Generales</span>
                        <div>
                            <button type="button" id="saveDocument" title="Cerrar Salida" class="boton3">
                                <i class="far fa-save"></i> Grabar Control
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
                                <input type="text" name="costos" id="costos" class="mostrarLista busqueda" placeholder="Elija opción" readonly>
                                <div class="lista" id="listaCostos">
                                    <ul>
                                        <?php echo $this->listaCostos?>
                                    </ul> 
                                </div>
                            </div>
                        </div>
                        <div class="seccion_medio">
                            <div class="column2">
                                <label for="almacen">Almacen:</label>
                                <input type="text" name="almacen" id="almacen" class="mostrarLista busqueda" placeholder="Elija opción" readonly>
                                <div class="lista" id="listaAlmacen">
                                    <ul>
                                        <?php echo $this->listaAlmacen?>
                                    </ul> 
                                </div>
                            </div>
                            <div class="column2">
                                <label for="recepciona">Autoriza:</label>
                                <input type="text" name="registra" id="registra" class="mostrarLista busqueda" placeholder="Elija opción" readonly>
                                <div class="lista" id="listaRecepciona">
                                    <ul>
                                        <?php echo $this->listaRecepciona?>
                                    </ul> 
                                </div>
                            </div>
                        </div>
                        <div class="seccion_derecha">
                            <div class="column2">
                                    <label for="tipo">Tipo :</label>
                                    <input type="text" name="tipoMovimiento" id="tipoMovimiento" class="mostrarLista busqueda" placeholder="Elija opción">
                                    <div class="lista" id="listaTipo">
                                        <ul>
                                            <?php echo $this->listaMovimiento?>
                                        </ul> 
                                    </div>
                            </div>
                            <div class="column4_55">
                                <div class="column2_46">
                                    <label for="tipo">Fecha Ingreso :</label>
                                    <input type="date" name="fechaIngreso" id="fechaIngreso" class="cerrarLista" value="<?php echo date("Y-m-d");?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="barraOpciones">
                        <span>Detalles</span>
                        <div>
                            <button type="button" id="itemsAdd" title="Agregar Items" class="cerrarLista boton3">
                                <i class="fas fa-upload"></i> Agregar Item
                            </button>
                            <button type="button" id="itemsImport" title="Agregar Items" class="cerrarLista boton3">
                                <i class="fas fa-file-excel"></i> Importar Items
                            </button>
                            <button type="button" id="itemsVerify" title="Verificar Codigos" class="cerrarLista boton3">
                                <i class="fas fa-file-excel"></i> Verificar Items
                            </button>
                        </div>
                    </div>
                    <div class="tablaInterna mininoTablaInterna">
                        <table class="tabla" id="tablaDetalles">
                            <thead class="stickytop">
                                <tr >
                                        <th rowspan="2">Item</th>
                                        <th rowspan="2">Codigo</th>
                                        <th rowspan="2">Descripcion</th>
                                        <th rowspan="2">Unidad</th>
                                        <th rowspan="2">Marca</th>
                                        <th rowspan="2" width="7%">Cantidad</th>
                                        <th rowspan="2" width="7%">Orden</th>
                                        <th rowspan="2">N° Colada/Lote</th>
                                        <th rowspan="2">N° TAG</th>
                                        <th rowspan="2">Serie</th>
                                        <th rowspan="2">Nro. Cert </br> Calidad</th>
                                        <th rowspan="2">Fecha </br> Calibración</th>
                                        <th rowspan="2">Fecha </br> Vencimiento</th>
                                        <th rowspan="2">Nro.Registro</br> Liberación </th>
                                        <th rowspan="2">Estado</th>
                                        <th rowspan="2">Condicion</th>
                                        <th colspan="3">Ubicación</th>
                                        <th rowspan="2">Observaciones</th>
                                </tr>
                                <tr>
                                        <th>Cont.</th>
                                        <th>Estante</th>
                                        <th>Fila/Col</th>
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
        <div class="ventanaResumen tamanioProceso">
                <div class="tituloDocumento">
                    <div>
                        <p class="titulo_seccion"><strong> Detalle Almacen : </strong></p>
                    </div>
                    <div>
                        <a href="#" id="closeDocument" title="Cerrar Ventana"><i class="fas fa-window-close"></i></a>
                    </div>
                </div>
                <hr>
                <div class="cuerpoResumem">
                   <div class="area1">
                        <label>Codigo</label>
                        <label>:</label>
                        <label id="codigo_item"></label>
                        <label>Descripción del Material</label>
                        <label>:</label>
                        <label id="descripcion_item"></label>
                   </div>
                   <div class="area2">
                        <div>
                            <label>Pedidos Solicitados</label>
                            <label>:</label>
                            <label id="numero_pedidos"></label>
                            <label>Ordenes Solicitadas</label>
                            <label>:</label>
                            <label id="numero_ordenes"></label>
                        </div>
                        <div>
                            <label>Ingreso Inventario</label>
                            <label>:</label>
                            <label id="inventario"></label>
                            <label>Ingresos Almacen</label>
                            <label>:</label>
                            <label id="ingresos"></label>
                            <label>Salidas Cosumo</label>
                            <label>:</label>
                            <label id="consumo"></label>
                            <label>Saldo Actual</label>
                            <label>:</label>
                            <label id="saldo"></label>
                        </div>
                        <div>
                            <label>Solicitado OC</label>
                            <label>:</label>
                            <label id="pendiente_compra"></label>
                        </div>
                   </div>
                   <div class="area3">
                        <div>
                            <h4>Precios</h4>
                            <table id="tabla_precios">
                                <thead class="stickytop">
                                    <tr class="pointer">
                                        <th>Fecha</th>
                                        <th>Moneda</th>
                                        <th>T.C</th>
                                        <th>Precio</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                        <div>
                            <h4>Existencias Centro de costos</h4>
                            <table id="tabla_existencias">
                                <thead class="stickytop">
                                    <tr class="pointer">
                                        <th>Centro de Costos</th>
                                        <th>unidad</th>
                                        <th>Ingresos</th>
                                        <th>Salidas</th>
                                        <th>Saldo</th>
                                        <th>Almacen</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                </tbody>
                            </table>
                        </div>
                   </div>
                   <div class="area4">
                        <div>
                            <h4>Ingresos</h4>
                        </div>
                        <div>
                            <h4>Salidas</h4>
                        </div>
                        <div>
                            <h4>Saldos</h4>
                        </div>
                   </div>
                </div>
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
                        <label for="codigoBusqueda">Codigo : </label>
                        <input type="text" name="codigoBusqueda" id="codigoBusqueda">
                    </div>
                    <div>
                        <label for="descripcionSearch">Descripcion: </label>
                        <input type="text" name="descripcionSearch" id="descripcionSearch">
                    </div>
                    <div>
                    </div>
                    <div>
                    </div>
                    <button type="button" id="btnConsulta" class="boton3">Consultar</button> 
            </div>
        </form>
    </div>
    <div class="itemsTabla">
        <table id="tablaPrincipal">
            <thead>
                <tr class="stickytop">
                    <th>Item</th>
                    <th>Codigo</th>
                    <th>Descripcion</th>
                    <th>Unidad</th>
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
    <script src="<?php echo constant('URL');?>public/js/funciones.js?<?php echo constant('VERSION')?>"></script>
    <script src="<?php echo constant('URL');?>public/js/stocks.js?<?php echo constant('VERSION')?>"></script>
</body>
</html>