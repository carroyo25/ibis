<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ingreso Almacen</title>
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
                    <input type="hidden" name="codigo_comprobante" id="codigo_comprobante">
                    <input type="hidden" name="codigo_movimiento" id="codigo_movimiento" value="230">
                    <input type="hidden" name="codigo_recepcion" id="codigo_recepcion" value="<?php echo $_SESSION['iduser']?>">
                    <input type="hidden" name="codigo_registra" id="codigo_registra">
                    <div class="barraOpciones primeraBarra">
                        <span>Datos Generales</span>
                        <div>
                            <button type="button" id="saveRegister" title="Cerrar Salida" class="boton3">
                                <i class="far fa-save"></i> Grabar Compra
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
                                <label for="costos">CCostos:</label>
                                <input type="text" name="costos" id="costos" class="mostrarLista busqueda" placeholder="Elija una opcion">
                                <div class="lista" id="listaCostos">
                                   <ul>
                                       <?php echo $this->listaCostos?>
                                   </ul> 
                                </div>
                            </div>
                        </div>
                        <div class="seccion_medio">
                            <div class="column2">
                                <label for="registrado">Registrado :</label>
                                <input type="text" name="registrado" id="registrado" readonly>
                            </div>
                            <div class="column2">
                                <label for="registra">Movimiento:</label>
                                <input type="text" name="movimiento" id="movimiento"  value="COMPRA LOCAL" readonly>
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
                                    <label for="documento">Documento :</label>
                                    <input type="text" name="documento" id="documento" class="mostrarLista busqueda" placeholder="Elija opción" readonly>
                                    <div class="lista" id="listaComprobantes">
                                        <ul>
                                            <?php echo $this->listaComprobantes?>
                                        </ul> 
                                    </div>
                                </div>
                                <div class="column2_46">
                                    <label for="nrdoc">Nro :</label>
                                    <input type="text" name="nrodoc" id="nrodoc">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="barraOpciones">
                        <span>Detalles</span>
                        <div>
                            <button type="button" id="itemsTransfer" title="Adjuntar Archivo" class="cerrarLista boton3">
                                <i class="fas fa-paperclip"></i> Adjuntar Documento
                            </button>
                            <button type="button" id="importRequest" title="Importar Pedidos" class="cerrarLista boton3">
                                <i class="fas fa-upload"></i>importar Pedido
                            </button>
                        </div>
                    </div>
                    <div class="tablaInterna mininoTablaInterna">
                        <table class="tabla" id="tablaDetalles">
                            <thead>
                                <tr class="stickytop">
                                        <th class="">...</th>
                                        <th class="">...</th>
                                        <th class="">Item</th>
                                        <th class="">Codigo</th>
                                        <th class="">Descripcion</th>
                                        <th class="">Unidad</th>
                                        <th width="7%">Cantidad</th>
                                        <th class="">Observaciones</th>
                                        <th class="">Pedido</th>
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
        <div class="ventanaBusqueda w75por">
            <div class="tituloVentana">
                <span id="tituloBusqueda">Guias de Remision</span>
                <div>
                    <a href="#" id="closeSearch"><i class="fas fa-window-close"></i></a>
                </div>
            </div>
            <div class="textoBusqueda">
                <input type="text" name="txtBuscar" id="txtBuscar" placeholder="Buscar" class="w90por">
                <button type="button" class="boton3" id="btnAceptItems">Aceptar</button>
            </div>
            <div class="tablaBusqueda">
                <table class="tablaWrap" id="despachos">
                    <thead>
                        <tr class="stickytop" >
                            <th>Despacho</th>
                            <th>Fecha</th>
                            <th>Origen</th>
                            <th>Destino</th>
                            <th>Costos/Proyecto</th>
                            <th>Año</th>
                            <th>Guia</th>
                            <th>RS</th>
                            <th>Orden</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="modal" id="pedidos">
        <div class="ventanaBusqueda w50por">
            <div class="tituloVentana">
                <span id="tituloBusqueda">Pedidos</span>
                <div>
                    <a href="#"><i class="fas fa-window-close"></i></a>
                </div>
            </div>
            <div class="textoBusqueda">
                <input type="text" name="txtBuscarPedido" id="txtBuscarPedido" placeholder="Buscar Pedido">
            </div>
            <div class="tablaBusqueda">
                <table class="tabla " id="tablaPedidos">
                    <thead >
                        <tr class="stickytop">
                            <th width="10%">Pedido</th>
                            <th width="10%">Fecha</th>
                            <th>Descripcion</th>
                            <th>CC</th>
                            <th>Solicitante</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="cabezaModulo">
        <h1>Compras Locales</h1>
        <div>
            <a href="#" id="nuevoRegistro"><i class="far fa-file"></i><p>Nuevo</p></a>
            <a href="#" id="irInicio"><i class="fas fa-home"></i><p>Inicio</p></a>
        </div>
    </div>
    <div class="barraTrabajo">
        <form action="#" id="formConsulta">
            <div class="variasConsultas">
                    <div>
                        <label for="tipo">N°. Registro</label>
                        <input type="text" id="guiaSearch" name="guiaSearch">
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
                    <button type="button" class="boton3" id="btnConsulta">Consultar</button> 
            </div>
        </form>
    </div>
    <div class="itemsTabla">
        <table id="tablaPrincipal">
            <thead class="stickytop">
                <tr>
                    <th>Item</th>
                    <th>F.Emisión</th>
                    <th>Centro de Costos</th>
                    <th>Nro Pedido</th>
                    <th>Nro. Ducumento</th>
                </tr>
            </thead>
            <tbody>
                <?php echo $this->listaCompras;?>
            </tbody>
        </table>
    </div>
    <script src="<?php echo constant('URL');?>public/js/jquery.js"></script>
    <script src="<?php echo constant('URL');?>public/js/funciones.js?<?php echo constant('VERSION')?>"></script>
    <script src="<?php echo constant('URL');?>public/js/locales.js?<?php echo constant('VERSION')?>"></script>
</body>
</html>