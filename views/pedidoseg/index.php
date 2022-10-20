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
                    <input type="hidden" name="codigo_transporte" id="codigo_transporte" value="39">
                    <input type="hidden" name="codigo_solicitante" id="codigo_solicitante">
                    <input type="hidden" name="codigo_tipo" id="codigo_tipo">
                    <input type="hidden" name="codigo_partida" id="codigo_partida">
                    <input type="hidden" name="codigo_pedido" id="codigo_pedido">
                    <input type="hidden" name="codigo_estado" id="codigo_estado" value="49">
                    <input type="hidden" name="codigo_verificacion" id="codigo_verificacion">
                    <input type="hidden" name="dias_atencion" id="dias_atencion" value="10">
                    <input type="hidden" name="codigo_atencion" id="codigo_atencion" value="47">
                    <input type="hidden" name="vista_previa" id="vista_previa">
                    <input type="hidden" name="emitido" id="emitido">
                    <input type="hidden" name="transporte" id="transporte" value="">

                    <div class="barraOpciones primeraBarra">
                        <span>Datos Generales</span>
                        <div>
                            <button type="button" id="saveItem" title="Grabar Pedido" class="boton3">
                                <p><i class="far fa-save"></i> Grabar Pedido</p> 
                            </button>
                            <button type="button" id="upAttach" title="Importar Adjuntos" class="boton3">
                                <i class="fas fa-upload"></i> Adjuntar Archivos
                            </button>
                            <button type="button" id="preview" title="Vista Previa" class="boton3">
                                <i class="fab fa-wpexplorer"></i> Vista Previa
                            </button>
                            <button type="button" id="sendItem" data-rol="109" data-estado="51" title="Enviar Pedido" class="boton3 desactivado">
                                <i class="far fa-paper-plane"></i> Enviar Almacen
                            </button>
                            <button type="button" id="requestAprob" data-rol="3" data-estado="53" title="Solicitar Aprobacion" class="boton3 desactivado">
                                <i class="fas fa-award"></i> Solicitar Aprobacion
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
                                    <label for="numero">Número:</label>
                                    <input type="text" name="numero" id="numero" class="cerrarLista" readonly>
                                </div>
                                <div class="column2_46">
                                    <label for="emision">Emisión:</label>
                                    <input type="date" name="emision" id="emision" class="cerrarLista" value="<?php echo date("Y-m-d");?>">
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
                            <div class="column2">
                                <label for="area">Partida:</label>
                                <input type="text" name="partida" id="partida" class="mostrarLista busqueda" placeholder="Elija una opcion">
                                <div class="lista" id="listaPartidas">
                                   <ul>
                                       
                                   </ul>
                                </div>
                            </div>
                        </div>
                        <div class="seccion_medio">
                            <div class="column2">
                                <label for="area">Area:</label>
                                <input type="text" name="area" id="area" class="mostrarLista busqueda" placeholder="Elija una opcion">
                                <div class="lista" id="listaAreas">
                                   <ul>
                                       <?php echo $this->listaAreas?>
                                   </ul>
                                </div>
                            </div>
                            <div class="column2">
                                <label for="concepto">Concepto:</label>
                                <input type="text" name="concepto" id="concepto" class="cerrarLista">
                            </div>
                            <div class="column2">
                                <label for="solicitante">Solicitante:</label>
                                <input type="text" name="solicitante" id="solicitante" class="mostrarLista busqueda" placeholder="Elija una opcion">
                                <div class="lista" id="listaSolicitantes">
                                   <ul>
                                       <?php echo $this->listaAquarius?>
                                   </ul>
                                </div>
                            </div>
                        </div>
                        <div class="seccion_derecha">
                            <div class="column4_55">
                                <div class="column2_3957">
                                    <label for="tipo">Tipo :</label>
                                    <input type="text" name="tipo" id="tipo" class="mostrarLista" placeholder="Elija una opcion">
                                    <div class="lista" id="listaTipo">
                                        <ul>
                                            <?php echo $this->listaTipos?>
                                        </ul>
                                    </div>
                                </div>
                                <div class="column2_46">
                                    <label for="vence">Vence :</label>
                                    <input type="date" name="vence" id="vence" class="cerrarLista" value="<?php echo date("Y-m-d");?>">
                                </div>
                            </div>
                            <div class="column2">
                                <label for="estado">Estado:</label>
                                <input type="text" name="estado" id="estado" class="textoCentro w35por estado procesando" readonly value="EN PROCESO">
                            </div>
                        </div>
                    </div>
                    <div class="textAreaEnter oculto">
                        <textarea name="espec_items" id="espec_items" rows="2" class="w100p" readonly></textarea>
                    </div>
                    <div class="barraOpciones">
                        <span>Detalles</span>
                        <button type="button" id="addItem" title="Añadir Item" class="cerrarLista boton3">
                            <i class="far fa-plus-square"></i> Agregar
                        </button>
                    </div>
                    <div class="tablaInterna mininoTablaInterna">
                        <table class="tabla" id="tablaDetalles">
                            <thead>
                                <tr>
                                    <th width="3%">...</th>
                                    <th width="5%">Item</th>
                                    <th width="8%">Codigo</th>
                                    <th>Descripcion</th>
                                    <th width="5%">Und.</th>
                                    <th width="6%">Cant.</th>
                                    <th width="30%">Especificaciones</th>
                                    <th>Nro. Parte</th>
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
    <div class="modal" id="esperar">
    </div>
    <div class="modal" id="detalles">
        <div class="ventanaResumen">
        </div>
    </div>
    <div class="cabezaModulo">
        <h1>Seguimiento Pedidos</h1>
        <div>
            <a href="#" id="irInicio"><i class="fas fa-home"></i><p>Inicio</p></a>
        </div>
    </div>
    <div class="barraTrabajo">
        <form action="#" id="formConsulta">
            <div class="variasConsultas">
                    <div>
                        <label for="tipo">Tipo : </label>
                        <select name="tipoSearch" id="tipoSearch">
                            <option value="37">Bienes</option>
                            <option value="38">Servicios</option>
                        </select>
                    </div>
                    <div>
                        <label for="costosSearch">Centro de Costos: </label>
                        <select name="costosSearch" id="costosSearch" class="item4">
                            <?php echo $this->listaCostosSelect ?>
                        </select>
                    </div>
                    <div>
                        <label for="mes">Mes</label>
                        <input type="number" name="mesSearch" id="mesSearch" value="<?php echo date("m")?>" class="textoCentro">
                    </div>
                    <div>
                        <label for="anio">Año :</label>
                        <input type="number" name="anioSearch" id="anioSearch" value="<?php echo date("Y")?>" class="textoCentro">
                    </div>
                    <button type="button">Procesar</button> 
            </div>
        </form>
    </div>
    <div class="itemsTabla">
        <table id="tablaPrincipal">
            <thead>
                <tr>
                    <th>Num.</th>
                    <th>Emision</th>
                    <th>Vencimiento</th>
                    <th>Descripción</th>
                    <th>Centro Costos</th>
                    <th>Resposable</th>
                    <th>Estado</th>
                    <th>Atencion</th>
                    <th>...</th>
                </tr>
            </thead>
            <tbody>
                <?php echo $this->listaPedidos;?>
            </tbody>
        </table>
    </div>
    <script src="<?php echo constant('URL');?>public/js/jquery.js"></script>
    <script src="<?php echo constant('URL');?>public/js/funciones.js?<?php echo constant('VERSION')?>"></script>
    <script src="<?php echo constant('URL');?>public/js/pedidoseg.js?<?php echo constant('VERSION')?>"></script>
</body>
</html>