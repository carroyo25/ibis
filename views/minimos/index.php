<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div class="modal" id="dialogo_registro">
        <div class="ventanaConsumo">
            <h3>Registrar Mínimos</h3>
            <div class="contenedor">
                <div class="cabecera_dialogo">
                    <label for="codigoSearch">Codigo</label>
                    <input type="text" name="codigoSearch" id="codigoSearch" readOnly>
                    <label for="descripSearch">Descripcion</label>
                    <input type="text" name="descripSearch" id="descripSearch" readOnly>
                </div>
                
                <div class="cuerpo_dialogo">
                    <div class="datos_cuerpo">
                        <label for="fecha">Fecha</label>
                        <input type="date" name="fecha" id="fecha">
                        <label for="cant_personal">Cantidad Personal</label>
                        <input type="number" name="cant_personal" id="cant_personal">
                        <label for="porcentaje_minimo">Porcent. Min.</label>
                        <input type="number" name="porcentaje_minimo" id="porcentaje_minimo">
                        <label for="total_minimo">Total Minimo</label>
                        <input type="number" name="total_minimo" id="total_minimo" readOnly>
                    </div>
                    <div class="datos_cuerpo_observaciones">
                        <label for="observaciones_dialogo">Observaciones</label>
                        <textarea name="observaciones_dialogo" id="observaciones_dialogo" placeholder="Observaciones"></textarea>
                    </div>
                </div>
                <div class="opciones_dialogo">
                    <button type="button" id="btnAceptarDialogoMinimo">Aceptar</button>
                    <button type="button" id="btnCancelarDialogoMinimo">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="cabezaModulo">
        <h1>Control de Existencias Minimas</h1>
        <div>
            <a href="#" id="excelFile"><i class="fas fa-file-excel"></i><p>Exportar</p></a>
            <a href="#" id="irInicio"><i class="fas fa-home"></i><p>Inicio</p></a>
        </div>
    </div>
    <div class="barraTrabajo">
        <form action="#" id="formConsulta">
            <div class="variasConsultas4campos">
                    <div>
                        <label for="costosSearch">Centro Costos: </label>
                        <select name="costosSearch" id="costosSearch">
                            <?php echo $this->listaCostosSelect ?>
                        </select>
                    </div>
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
                    <button type="button" id="btnConsulta" class="boton3">Consultar</button> 
            </div>
        </form>
    </div>
    <div class="itemsTabla">
        <table id="tablaPrincipal">
            <thead class="stickytop">
                <tr>
                    <th rowspan="2">Item</th>
                    <th rowspan="2">Codigo</th>
                    <th rowspan="2" width="50%">Descripcion</th>
                    <th rowspan="2">Unidad</th>
                    <th rowspan="2">Cantidad<br>Ingreso</th>
                    <th rowspan="2">Ingreso<br>Salida</th>
                    <th rowspan="2">Cantidad<br>Mínima</th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table>
    </div>
    <script src="<?php echo constant('URL');?>public/js/jquery.js"></script>
    <script src="<?php echo constant('URL');?>public/js/funciones.js?<?php echo constant('VERSION')?>"></script>
    <script src="<?php echo constant('URL');?>public/js/minimos.js?<?php echo constant('VERSION')?>"></script>
</body>
</html>