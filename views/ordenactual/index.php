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
                    <input type="hidden" name="direccion_almacen" id="direccion_almacen">
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
                    <input type="hidden" name="nivel_autorizacion" id="nivel_autorizacion">
                    <input type="hidden" name="codigo_pago" id="codigo_pago">
                    <input type="hidden" name="sw" id="sw" value="0">
                    <input type="hidden" name="detalle" id="detalle">
                    <input type="hidden" name="total_numero" id="total_numero">
                    <input type="hidden" name="total_adicional" id="total_adicional" value="0">
                    <input type="hidden" name="nro_pedido" id="nro_pedido">
                    <input type="hidden" name="user_modifica" id="user_modifica" value="">
                    <input type="hidden" name="user_genera" id="user_genera" value="">
                    <input type="hidden" name="dias" id="dias">

                    <input type="hidden" name="procura" id="procura" value="1">
                    <input type="hidden" name="finanzas" id="finanzas" value="1">
                    <input type="hidden" name="operaciones" id="operaciones" value="1">
                    
                    <div class="barraOpciones primeraBarra">
                        <span>Datos Generales</span>
                        <div>
                            <button type="button" id="preview" title="Vista Previa" class="boton3">
                                <i class="far fa-file-pdf"></i> Vista Previa
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
                                <input type="text" name="concepto" id="concepto" class="cerrarLista mayusculas">
                            </div>
                        </div>
                        <div class="seccion_medio">
                            <div class="column4_55">
                                <div class="column2_3957">
                                    <label for="moneda">Moneda :</label>
                                    <input type="text" name="moneda" id="moneda" class="mostrarLista busqueda" placeholder="Elija una opcion" readonly>
                                    <div class="lista" id="listaMoneda">
                                        <ul>
                                            <?php echo $this->listaMonedas?>
                                        </ul> 
                                    </div>
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
                                    <input type="text" name="cpago" id="cpago" class="mostrarLista busqueda" placeholder="Elija una opcion" readonly>
                                    <div class="lista" id="listaPago">
                                        <ul>
                                            <?php echo $this->listaPagos?>
                                        </ul> 
                                    </div>
                                </div>
                                <div class="column2_46">
                                    <label for="estado">Estado:</label>
                                    <input type="text" name="estado" id="estado" class="textoCentro estado procesando" readonly value="EN PROCESO">
                                </div>
                            </div>
                            <div class="column4_55">
                                <div class="column2_46">
                                    <label for="tcambio">Incluye IGV.</label>
                                    <div class="igv">
                                        <input type="radio" name="radioIgv" id="si" value="0.18">
                                        <label for="si">Si</label>
                                        <input type="radio" name="radioIgv" id="no" value="0" checked>
                                        <label for="no">No</label>
                                    </div>
                                </div>
                                <div class="column2_46">
                                    <label for="tcambio">T. Cambio:</label>
                                    <input type="text" name="tcambio" id="tcambio" class="textoDerecha pr20px">
                                </div>
                            </div>
                            
                        </div>
                        <div class="seccion_derecha">
                            <div class="column2">
                                <label for="entidad">Entidad:</label>
                                <input type="text" name="entidad" id="entidad" class="mostrarLista busqueda" placeholder="Elija una opcion">
                                <div class="lista" id="listaEntidad">
                                    <ul>
                                        <?php echo $this->listaEntidades?>
                                    </ul> 
                                </div>
                            </div>
                            <div class="column2">
                                <label for="atencion">Atención:</label>
                                <input type="text" name="atencion" id="atencion" readonly>
                            </div>
                            <div class="column4_55">
                                <div class="column2">
                                    <label for="ncotiz">N° Cot.:</label>
                                    <input type="text" name="ncotiz" id="ncotiz" class="cerrarLista">
                                </div>
                                <div class="column2">
                                    <label for="dscto">Referencia</label>
                                    <input type="text" name="referencia" id="referencia" class="cerrarLista">
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
                        <div>
                            <button type="button" id="uploadCotiz" title="Adjuntar Coizacion" class="cerrarLista boton3">
                                <i class="far fa-file-pdf"></i> Adjuntar archivos
                                <span class="button__atach cookie_info" id="atach_counter"></span>
                            </button> 
                        </div>
                    </div>
                    <div class="tablaInterna mininoTablaInterna">
                        <table class="tabla" id="tablaDetalles">
                            <thead>
                                <tr class="stickytop">
                                    <th width="3%">...</th>
                                    <th>Item</th>
                                    <th width="7%">Codigo</th>
                                    <th>Descripcion</th>
                                    <th>Und.</th>
                                    <th width="7%">Cant.</th>
                                    <th width="8%">Precio</th>
                                    <th width="8%">Total</th>
                                    <th>Nro.</br>Parte</th>
                                    <th>Pedido</th>
                                    <th width="20%">Detalles</th>
                                    <th>...</th>
                                    <th>...</th>
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
    <div class="modal" id="archivos">
        <div class="ventanaArchivos">
            <form action="#" id="fileAtachs" enctype='multipart/form-data'>
                <input type="hidden" name="nroordenatach" id="nroordenatach">
                <input type="file" name="uploadAtach" id="uploadAtach" multiple class="oculto">
                <div class="tituloArchivos">
                    <h3>Adjuntar Archivos</h3>
                    <a href="#" id="openArch" title="Adjuntar Archivos"><i class="fas fa-file-medical"></i></a>
                </div>            
                <ul class="listaArchivos" ondrop="dropHandler(event);" ondragover="dragOverHandler(event);">
                </ul>
                <div class="opcionesArchivos">
                    <button type="button" class="boton3" id="btnConfirmAtach">Aceptar</button>
                    <button type="button" class="boton3" id="btnCancelAtach">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
    <div class="cabezaModulo">
        <h1>Descargar Ordenes</h1>
        <div>
            <a href="#" id="nuevoRegistro"><i class="far fa-file"></i><p>Nuevo</p></a>
            <a href="#" id="irInicio"><i class="fas fa-home"></i><p>Inicio</p></a>
        </div>
    </div>
    <div class="barraTrabajo">
        <form action="#" id="formConsulta">
            <div class="variasConsultas">
                    <div>
                        <label for="tipo">Nro.Orden : </label>
                        <input type="text" name="ordenSearch" id="ordenSearch">
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
                            <option value="01">Enero</option>
                            <option value="02">Febrero</option>
                            <option value="03">Marzo</option>
                            <option value="04">Abril</option>
                            <option value="05">Mayo</option>
                            <option value="06">Junio</option>
                            <option value="07">Julio</option>
                            <option value="08">Agosto</option>
                            <option value="09">Setiembre</option>
                            <option value="10">Octubre</option>
                            <option value="11">Noviembre</option>
                            <option value="12">Diciembre</option>
                        </select>
                    </div>
                    <div>
                        <label for="anio">Año :</label>
                        <input type="number" name="anioSearch" id="anioSearch" class="textoCentro">
                    </div>
                    <button type="button" id="btnConsult">Procesar</button> 
            </div>
        </form>
    </div>
    <div class="itemsTabla">
        <table id="tablaPrincipal">
            <thead class="stickytop">
                    <tr>
                    <th rowspan="2" data-idcol="0" class="datafiltro">Num.</th>  
                    <th rowspan="2">Emision</th>
                    <th rowspan="2" width="25%" data-idcol="2" class="datafiltro">Descripción</th>
                    <th rowspan="2" data-idcol="3" class="datafiltro">Centro Costos</th> 
                    <th rowspan="2" data-idcol="4" class="datafiltro">Area</th>
                    <th rowspan="2" width="15%" data-idcol="5" class="datafiltro">Proveedor</th>
                    <th rowspan="2" width="5%">Precio Soles</th>
                    <th rowspan="2" width="5%">Precio Dólares</th>
                    <th rowspan="2" data-idcol="8" class="datafiltro">Atencion</th>
                    <th rowspan="2">Estado</th>
                    <th rowspan="2">Operador</th>
                    <th rowspan="2">Descarga</th>
                    <th colspan="3" width="16%">Firmas</th>
                    <tr>
                        <th>Procura</th>
                        <th>Finanzas</th>
                        <th>Operaciones</th>
                    </tr>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table>
    </div>
    <script src="<?php echo constant('URL');?>public/js/jquery.js"></script>
    <script src="<?php echo constant('URL');?>public/js/funciones.js?<?php echo constant('VERSION')?>"></script>
    <script src="<?php echo constant('URL');?>public/js/ordenactual.js?<?php echo constant('VERSION')?>"></script>
</body>
</html>