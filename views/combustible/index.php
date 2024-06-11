<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div class="mensaje">
        <p></p>
    </div>
    <div class="modal" id="dialogo_registro">
        <div class="ventanaConsumo">
            <div class="titulo_dialogo">
                <h3>Registrar Combustible</h3>
            </div>
                <div class="ingreso_combustible">
                    <form id="form__combustible" class="form__combustible">
                    <input type="hidden" name="codigo_producto" id="codigo_producto">
                    <input type="hidden" name="codigo_almacen" id="codigo_almacen">
                    <input type="hidden" name="codigo_area" id="codigo_area">
                    <input type="hidden" name="codigo_proyecto" id="codigo_proyecto">
                    <input type="hidden" name="codigo_tipo" id="codigo_tipo">
                    <input type="hidden" name="codigo_unidad" id="codigo_unidad">
                    <input type="hidden" name="codigo_equipo" id="codigo_equipo">

                        <div class="grid3col">
                            <div>
                                <label for="fechaRegistro">Fecha de Registro</label>
                                <input type="date" id="fechaRegistro" name="fechaRegistro" value="<?php echo date("Y-m-d");?>" min="<?php echo date("Y-m-d")?>">
                            </div>
                            <div>
                                <label for="item">Almacen</label>
                                <select name="almacen" id="almacen">
                                    <?php echo $this->listaAlmacen ?>
                                </select>
                            </div>
                            <div>
                                <label for="item">Tipo</label>
                                <select name="tipo" id="tipo">
                                    <option value="-1">Elija opcion</option>
                                    <option value="1">Ingreso</option>
                                    <option value="2">Salida</option>
                                </select>
                            </div>
                        </div>
                        <div class="grid3col">
                            <div>
                                <label for="item">Codigo</label>
                                <input type="text" id="codigo" name="codigo">
                            </div>
                            <div>
                                <label for="item">Descripcion</label>
                                <input type="text" id="descripcion" name="descripcion" readonly>
                            </div>
                            <div>
                                <label for="item">Unidad</label>
                                <input type="text" id="unidad" name="unidad" readonly>
                            </div>
                        </div>
                        <div class="grid2col">
                            <div>
                                <label for="item">Cantidad</label>
                                <input type="number" id="item" name="cantidad" placeholder="0.00">
                            </div>
                            <div>
                                <label for="observacionesItem">Observaciones del Item :</label>
                                <input type="text" id="observacionesItem" name="observacionesItem">
                            </div>
                        </div>
                        <div class="grid2col">
                            <div>
                                <label for="documento">Documento :</label>
                                <input type="text" id="documento" name="documento">
                            </div>
                            <div>
                                <label for="item">Trabajador :</label>
                                <input type="text" id="trabajador" name="trabajador">
                            </div>
                        </div>
                        <div class="grid3col">
                            <div>
                                <label for="usuario">Usuario</label>
                                <input type="text" id="usuario" name="usuario" value ="<?php echo strtoupper($_SESSION['user']); ?>" readonly >
                            </div>
                            <div>
                                <label for="proyecto">Proyecto :</label>
                                <select name="proyecto" id="proyecto">
                                    <?php echo $this->listaCostosSelect ?>
                                </select>
                            </div>
                            <div>
                                <label for="guia">Guia de Remision :</label>
                                <input type="text" id="guia" name="guia">
                            </div>
                        </div>
                        <div>
                            <label for="observacionesDocumento">Observaciones del Documento :</label>
                            <input type="text" id="observacionesDocumento" name="observacionesDocumento">
                        </div>
                        <div class="grid2col">
                            <div>
                                <label for="referencia">Referencia Adicional</label>
                                <select name="referencia" id="referencia">
                                    <?php echo $this->listaEquipos ?>
                                </select>
                            </div>
                            <div>
                                <label for="area">Area</label>
                                <select name="area" id="area">
                                    <?php echo $this->listaAreas?>
                                </select>
                            </div>
                        </div>
                    </form>
                    <div class="opciones">
                        <button id="btn_consumo_aceptar">Aceptar</button>
                        <button id="btn_consumo_cancelar">Cancelar</button>
                    </div>
                </div>
                <div class="resumen_combustible">
                    <table>
                        <tbody>
                            <tr>
                                <td>STOCK INICIAL (MES ANTERIOR):</td>
                                <td>0</td>
                            </tr>
                            <tr>
                                <td>CANTIDAD DE INGRESO:</td>
                                <td>0</td>
                            </tr>
                            <tr>
                                <td>CANTIDAD DE CONSUMO:</td>
                                <td>0</td>
                            </tr>
                            <tr>
                                <td>STOCK FINAL:</td>
                                <td>0</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
        </div>
    </div>
    <div class="cabezaModulo">
        <h1>Control de Combustible</h1>
        <div>
            <a href="#" id="nuevoRegistro"><i class="far fa-file"></i><p>Nuevo</p></a>
            <a href="#" id="irInicio"><i class="fas fa-home"></i><p>Inicio</p></a>
        </div>
    </div>
    <div class="barraTrabajo">
        <form action="#" id="formConsulta">
            <div class="variasConsultas">
                    <div>
                        <label for="tipo">Nro. Guia</label>
                        <input type="text" id="ordenSearch" name="ordenSearch">
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
                    <th>Fecha<br>Registro</th>
                    <th>Almacen</th>
                    <th>Tipo</th>
                    <th>Codigo</th>
                    <th>Descripción</th>
                    <th>Unidad</th>
                    <th>Cantidad</th>
                    <th>Movimiento</th>
                    <th>Trabajador</th>
                    <th>Usuario</th>
                    <th>Proyecto</th>
                    <th>Guia Remision</th>
                    <th>Observaciones</th>
                    <th>Observacion<br> del documento<br> de almacen</th>
                    <th>Area</th>
                    <th>Referencia<br>Adicional</th>
                    <th>Mes</th>
                </tr>
            </thead>
            <tbody>
                <?php echo $this->listaItemsCombustible;?>
            </tbody>
        </table>
    </div>
    <script src="<?php echo constant('URL');?>public/js/jquery.js"></script>
    <script src="<?php echo constant('URL');?>public/js/funciones.js?<?php echo constant('VERSION')?>"></script>
    <script src="<?php echo constant('URL');?>public/js/combustible.js?<?php echo constant('VERSION')?>"></script>
</body>
</html>