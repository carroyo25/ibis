<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
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
                    <input type="hidden" name="archivo" id="archivo">
                    <input type="file" name="fileUpload" id="fileUpload" accept=".xls,.xlsx,.ods" class="oculto" >

                    <div class="barraOpciones primeraBarra">
                        <span>Datos Generales</span>
                        <div>
                            <button type="button" id="processRequest" title="Cerrar Salida" class="boton3">
                                <i class="far fa-save"></i> Aprobar Inventario
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
                        <div class="oculto">
                            <button type="button" id="itemsAdd" title="Agregar Items" class="cerrarLista boton3">
                                <i class="fas fa-upload"></i> Agregar Item
                            </button>
                            <button type="button" id="itemsImport" title="Agregar Items" class="cerrarLista boton3">
                                <i class="fas fa-file-excel"></i> Importar Items
                            </button>
                            <button type="button" id="itemsVerify" title="Verificar Codigos" class="cerrarLista boton3">
                                <i class="fas fa-wrench"></i> Exportar Items
                            </button>
                        </div>
                    </div>
                    <div class="tablaInterna mininoTablaInterna">
                        <table class="tabla" id="tablaDetalles">
                            <thead class="stickytop">
                                <tr >
                                        <th rowspan="2" data-titulo="item">Item</th>
                                        <th rowspan="2" data-titulo="codigo">Codigo</th>
                                        <th rowspan="2" data-titulo="descripcion">Descripcion</th>
                                        <th rowspan="2" data-titulo="unidad">Unidad</th>
                                        <th rowspan="2" data-titulo="Marca">Marca</th>
                                        <th rowspan="2" data-titulo="Cantidad" width="7%">Cantidad</th>
                                        <th rowspan="2" data-titulo="Orden" width="7%">Orden</th>
                                        <th rowspan="2" data-titulo="Colada/Lote">N° Colada/Lote</th>
                                        <th rowspan="2" data-titulo="TAG">N° TAG</th>
                                        <th rowspan="2" data-titulo="Serie">Serie</th>
                                        <th rowspan="2" data-titulo="N° Cert. Calidad">Nro. Cert </br> Calidad</th>
                                        <th rowspan="2" data-titulo="Fecha Calibración">Fecha </br> Calibración</th>
                                        <th rowspan="2" data-titulo="Fecha Vencimiento">Fecha </br> Vencimiento</th>
                                        <th rowspan="2" data-titulo="Nro. Registro">Nro.Registro</br> Liberación </th>
                                        <th rowspan="2" data-titulo="Estado">Estado</th>
                                        <th rowspan="2" data-titulo="Condicion">Condicion</th>
                                        <th colspan="3" data-titulo="Ubicacion">Ubicación</th>
                                        <th rowspan="2" data-titulo="Observaciones">Observaciones</th>
                                </tr>
                                <tr>
                                        <th data-titulo="contenedor">Contenedor</th>
                                        <th data-titulo="estante">Estante</th>
                                        <th data-titulo="fila">Fila/Col</th>
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
    <div class="modal" id="pregunta">
        <div class="ventanaPregunta">
            <h3>¿Autorizar el Procesos de Ajuste?</h3>
            <div>
                <button type="button" id="btnAceptarAjuste">Aceptar</button>
                <button type="button" id="btnCancelarAjuste">Cancelar</button>
            </div>
        </div>
    </div>
    <div class="mensaje">
        <p></p>
    </div>
    <div class="cabezaModulo">
        <h1>Aprobación de Ajustes de Almacén</h1>
        <div>
            <a href="#" id="nuevoRegistro"><i class="far fa-file"></i><p>Nuevo</p></a>
            <a href="#" id="irInicio"><i class="fas fa-home"></i><p>Inicio</p></a>
            <a href="#" id="closeSession" class="oculto">xxx<p>Inicio</p></a>
        </div>
    </div>
    <div class="barraTrabajo">
        <form action="#" id="formConsulta">
            <div class="variasConsultas">
                    <div>
                        <label for="numberSearch">Numero : </label>
                        <input type="text" name="numberSearch" id="numberSearch">
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
                    <button type="button" id="btnConsulta" class="boton3">Consultar</button> 
            </div>
        </form>
    </div>
    <div class="itemsTabla">
        <table id="tablaPrincipal">
            <thead>
                <tr class="stickytop">
                    <th>Item</th>
                    <th>Fecha Registro</th>
                    <th>Fecha Inventario</th>
                    <th>Registrado</th>
                    <th>Almacen</th>
                    <th>Centro Costos</th>
                </tr>
            </thead>
            <tbody>
                <?php echo $this->listaAjustes;?>
            </tbody>
        </table>
    </div>
    <script src="<?php echo constant('URL');?>public/js/jquery.js"></script>
    <script src="<?php echo constant('URL');?>public/js/funciones.js?<?php echo constant('VERSION')?>"></script>
    <script src="<?php echo constant('URL');?>public/js/autorizaajuste.js?<?php echo constant('VERSION')?>"></script>
</body>
</html>