<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignación de Logística</title>
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
                    <input type="hidden" name="codigo_atencion" id="codigo_asigna">
                    <input type="hidden" name="elabora" id="elabora">
                    <input type="hidden" name="emitido" id="emitido">
                    <input type="hidden" name="espec_items" id="espec_items">
                    <input type="hidden" name="user_asigna" id="user_asigna">
                    <div class="barraOpciones primeraBarra">
                        <span>Datos Generales</span>
                        <div>
                            <button type="button" id="viewAtach" title="Ver Adjunto" class="boton3">
                                <p><i class="fas fa-paperclip"></i> ver Adjuntos</p> 
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
                                <input type="text" name="concepto" id="concepto" class="mayusculas">
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
                        <span>Detalles</span>
                    </div>
                    <div class="tablaInterna mininoTablaInterna">
                        <table class="tabla" id="tablaDetalles">
                            <thead class="stickytop">
                                <tr>
                                    <th width="5%">Item</th>
                                    <th width="7%">Codigo</th>
                                    <th width="20%">Descripcion</th>
                                    <th>Und.</th>
                                    <th width="7%">Cant.Ped.</th>
                                    <th width="7%">Cant.Aten.</th>
                                    <th width="7%">Cant.</br>Aprobada</th>
                                    <th>Nro.</br>Orden</th>
                                    <th>Observaciones</th>
                                    <th>Bien/Activo</th>
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
    <div class="modal" id="cambioestado">
        <div class="ventanaOperador">
            <input type="hidden" name="estadoCompra" id="estadoCompra" value="0">
            <h3>Cambiar Estado</h3>
            <input type="hidden" name="operador_asignado" id="operador_asignado">
            <hr>
            <div class="contenedor_estados">
                <ul id="operadores">
                    <?php echo $this->estadosCompra ?>
                </ul>
                <div>
                    <p>Observaciones</p>
                    <textarea name="comentarioEstado" id="comentarioEstado"></textarea>
                </div>
                <div>
                    <span>Fecha Entrega Obra</span>
                    <input type="date" name="entregaObra" id="entregaObra">
                </div>
            </div>
            <div class="opciones">
                <button type="button" id="aceptaEstado">Aceptar</button>
                <button type="button" id="cancelaEstado">Cancelar</button>
            </div>
        </div>
    </div>
    <div class="modal" id="vistaAdjuntos">
        <div class="ventanaAdjuntos">
            <div class="tituloAdjuntos">
                <h3>Adjuntos Pedido</h3>
                <a href="#" id="closeAtach" title="Cerrar Ventana"><i class="fas fa-window-close"></i></a>
            </div>
            <ul id="listaAdjuntos">

            </ul>
            <iframe src=""></iframe>
        </div>
    </div>
     <div class="cabezaModulo">
        <h1>Seguimiento de Pedidos (Compras)</h1>
        <div>
            <a href="#" id="reportExport"><i class="fas fa-file-excel"></i><p>Reporte</p></a>
            <a href="#" id="irInicio"><i class="fas fa-home"></i><p>Inicio</p></a>
        </div>
    </div>
    <div class="barraTrabajo">
        <form action="#" id="formConsulta">
            <div class="variasConsultas">
                    <div>
                        <label for="tipo">Numero : </label>
                        <input type="text" name="numeroSearch" id="numeroSearch">
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
                    <th data-idcol="0" class="datafiltro">Num.</th>
                    <th>Emision</th>
                    <th>Tipo</th>
                    <th>Descripción</th>
                    <th data-idcol="4" class="datafiltro">Centro Costos</th>
                    <th data-idcol="5" class="datafiltro">Usuario</th>
                    <th data-idcol="6" class="datafiltro">Estado</th>
                    <th data-idcol="7" class="datafiltro">Asignado</th>
                    <th data-idcol="8">Fecha Entrega <br/> Obra</th>
                    <th data-idcol="9" class="datafiltro" width="10%">Estado Compras</th>
                    <th width="5%">Avance</th>
                    <th>...</th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table>
    </div>
    <script src="<?php echo constant('URL');?>public/js/exceljs.min.js"></script>
    <script src="<?php echo constant('URL');?>public/js/jquery.js"></script>
    <script src="<?php echo constant('URL');?>public/js/funciones.js?<?php echo constant('VERSION')?>"></script>
    <script src="<?php echo constant('URL');?>public/js/segpedcompras.js?<?php echo constant('VERSION')?>"></script>
</body>
</html>