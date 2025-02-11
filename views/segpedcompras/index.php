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
                            <button type="button" id="asingRequest" title="Asignar Pedido" class="boton3">
                                <p><i class="fas fa-wrench"></i> Asignar Operador</p> 
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

                        <button type="button" id="quitarAsigna" title="Libera el pedido" class="boton3">
                            <p><i class="fas fa-street-view"></i>  Liberar Pedido</p> 
                        </button>
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
                                    <th>Nro.</br>Parte</th>
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
    <div class="modal" id="comentarios">
        <div class="ventanaOperador">
            <h3>Asignar Operador</h3>
            <input type="hidden" name="operador_asignado" id="operador_asignado">
            <hr>
            <div class="contenedor_operadores">
                <ul id="operadores">
                    <?php echo $this->listaOperadores ?>
                </ul>
            </div>
            <div>
                <button type="button" id="aceptaAsigna">Asignar</button>
                <button type="button" id="cancelaAsigna">Cancelar</button>
            </div>
        </div>
    </div>
    <div class="modal" id="cambioestado">
        <div class="ventanaOperador">
            <h3>Asignar Operador</h3>
            <input type="hidden" name="operador_asignado" id="operador_asignado">
            <hr>
            <div class="contenedor_operadores">
                <ul id="operadores">
                    <?php echo $this->listaOperadores ?>
                </ul>
            </div>
            <div>
                <button type="button" id="aceptaAsigna">Asignar</button>
                <button type="button" id="cancelaAsigna">Cancelar</button>
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
            <a href="#" id="nuevoRegistro"><i class="far fa-file"></i><p>Nuevo</p></a>
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
                    <th data-idcol="5" class="datafiltro">Solicitante</th>
                    <th>Estado</th>
                    <th>Atencion</th>
                    <th data-idcol="8" class="datafiltro">Asignado</th>
                    <th data-idcol="9" class="datafiltro" width="10%">Estado<br/>Compras</th>
                    <th>...</th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table>
    </div>
    <script src="<?php echo constant('URL');?>public/js/jquery.js"></script>
    <script src="<?php echo constant('URL');?>public/js/funciones.js?<?php echo constant('VERSION')?>"></script>
    <script src="<?php echo constant('URL');?>public/js/segpedcompras.js?<?php echo constant('VERSION')?>"></script>
</body>
</html>