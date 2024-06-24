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
            <div class="leyenda">
                <table class="table">
                    <thead class="stickytop">
                        <tr>
                            <th>Puntaje</th>
                            <th>Criterio</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="textoCentro">5</td>
                            <td class="textoCentro">Excelente</td>
                        </tr>
                        <tr>
                            <td class="textoCentro">4</td>
                            <td class="textoCentro">Bueno</td>
                        </tr>
                        <tr>
                            <td class="textoCentro">3</td>
                            <td class="textoCentro">Regular</td>
                        </tr>
                        <tr>
                            <td class="textoCentro">2</td>
                            <td class="textoCentro">Insuficiente</td>
                        </tr>
                        <tr>
                            <td class="textoCentro">1</td>
                            <td class="textoCentro">Deficiente</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="cabezaProceso">
                <form action="#" id="formProceso" autocomplete="off">
                    <input type="hidden" name="codigo_rol" id="codigo_rol">
                    <input type="hidden" name="codigo_orden" id="codigo_orden">
                    <input type="hidden" name="codigo_entidad" id="codigo_entidad">
                    <input type="hidden" name="tipo_orden" id="tipo_orden">
                    <input type="hidden" name="estado_evaluacion" id="estado_evaluacion">

                    <div class="barraOpciones primeraBarra">
                        <span>Datos Generales</span>
                        <div>
                            <button type="button" id="saveOrden" title="Grabar Orden" class="boton3">
                                <p><i class="far fa-save"></i> Grabar </p> 
                            </button>
                            <button type="button" id="cerrarVentana" title="Cerrar" class="boton3">
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
                        </div>
                        <div class="seccion_medio">
                            <div class="column2">
                                <label for="detalle">Detalle:</label>
                                <input type="text" name="detalle" id="detalle" class="cerrarLista" readonly>
                            </div>
                            
                            <div class="column4_55">
                                <div class="column2_3957">
                                    <label for="puntaje">Puntaje :</label>
                                    <input type="text" name="puntaje" id="puntaje" class="cerrarLista" readonly>
                                </div>
                                <div class="column2_46">
                                    <label for="estado">Estado:</label>
                                    <input type="text" name="estado" id="estado" class="textoCentro estado procesando" readonly value="EVALUACION">
                                </div>
                            </div>
                        </div>
                        <div class="seccion_derecha">
                            <div class="column2">
                                <label for="entidad">Entidad:</label>
                                <input type="text" name="entidad" id="entidad" readonly>
                            </div>
                            <div class="column2">
                                <label for="entidad">Observaciones:</label>
                                <input type="text" name="observaciones" id="observaciones">
                            </div>
                        </div>
                       
                    </div>
                    <div class="barraOpciones">
                        <span>Criterios de Evaluación</span>
                    </div>
                    <div class="tablaInterna mininoTablaInterna">
                        <table class="tabla" id="tablaDetalles">
                            <thead>
                                <tr class="stickytop">
                                    <th>Criterio</th>
                                    <th>Descripcion</th>
                                    <th width="4%">Puntaje</th>
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
    <div class="cabezaModulo">
        <h1>Evaluacion de proveedores</h1>
        <div>
            <a href="#" id="irInicio"><i class="fas fa-home"></i></a>
        </div>
    </div>
    <div class="barraTrabajo">
        <form action="#" id="formConsulta">
            <div class="variasConsultas6campos">
                    <div>
                        <label for="nroSearch">Nro : </label>
                        <input type="text" name="nroSearch" id="nroSearch">
                    </div>
                    <div>
                        <label for="costosSearch">Centro de Costos: </label>
                        <select name="costosSearch" id="costosSearch" class="item4">
                            <?php echo $this->listaCostosSelect ?>
                        </select>
                    </div>
                    <div>
                        <label for="tipo">Tipo</label>
                        <select name="tipoSearch" id="tipoSearch">
                            <option value="-1">Elija Tipo</option>
                            <option value="37">Bienes</option>
                            <option value="38">Servicios</option>
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
                    <button type="button" id="btnConsulta" class="boton3">Consultar</button> 
            </div>
        </form>
    </div>
    <div class="itemsTabla">
        <table id="tablaPrincipal">
            <thead class="stickytop">
                    <tr>
                        <th width="3%">Num.</th>  
                        <th width="3%"  data-idcol="1" class="datafiltro">Emision</th>
                        <th width="25%" data-idcol="2" class="datafiltro">Descripción</th>
                        <th width="5%"  data-idcol="3" class="datafiltro">Centro Costos</th> 
                        <th width="15%" data-idcol="4" class="datafiltro">Area</th>
                        <th width="15%" data-idcol="5" class="datafiltro">Proveedor</th>
                    </tr>
            </thead>
            <tbody>
                <?php echo $this->listaOrdenes;?>
            </tbody>
        </table>
    </div>
    <script src="<?php echo constant('URL');?>public/js/jquery.js"></script>
    <script src="<?php echo constant('URL');?>public/js/funciones.js?<?php echo constant('VERSION')?>"></script>
    <script src="<?php echo constant('URL');?>public/js/evaluacion.js?<?php echo constant('VERSION')?>"></script>
</body>
</html>