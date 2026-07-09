<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Existencias Mínimas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo constant('URL'); ?>public/css/minimos.css">
</head>
<body>
    <!-- MODAL -->
    <div class="modal" id="dialogo_registro">
        <div class="ventanaConsumo">
            <h3>Registrar Mínimos</h3>
            <div class="contenedor">
                <div class="cabecera_dialogo">
                    <label for="codigoSearch">Codigo</label>
                    <input type="text" name="codigoSearch" id="codigoSearch">
                    <label for="descripSearch">Descripcion</label>
                    <input type="text" name="descripSearch" id="descripSearch" readonly>
                </div>
                
                <div class="cuerpo_dialogo">
                    <div class="datos_cuerpo_minimo">
                        <label for="unidad">Unidad</label>
                        <input type="text" name="unidad" id="unidad" value="" readonly>
                        <label for="fecha">Fecha</label>
                        <input type="date" name="fecha" id="fecha" value="<?php echo date('Y-m-d'); ?>">
                        <label for="cant_personal">Cant. Personal</label>
                        <input type="number" name="cant_personal" id="cant_personal">
                        <label for="porcentaje_minimo">Porcent. Min.</label>
                        <input type="number" name="porcentaje_minimo" id="porcentaje_minimo">
                        <label for="total_minimo">Total Minimo</label>
                        <input type="number" name="total_minimo" id="total_minimo">
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
    
    <!-- HEADER -->
    <div class="cabezaModulo">
        <h1>Control de Existencias Minimas</h1>
        <div>
            <a href="#" id="newRegister"><i class="far fa-file"></i><p>Nuevo</p></a>
            <a href="#" id="excelFile"><i class="fas fa-file-excel"></i><p>Exportar</p></a>
            <a href="#" id="irInicio"><i class="fas fa-home"></i><p>Inicio</p></a>
        </div>
    </div>
    
    <!-- BARRA DE BÚSQUEDA -->
    <div class="barraTrabajo">
        <form action="#" id="formConsulta">
            <div class="variasConsultas4campos">
                <div>
                    <label for="costosSearch">Centro Costos: </label>
                    <select name="costosSearch" id="costosSearch">
                        <?php echo $this->listaCostosSelect; ?>
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
    
    <!-- TABLA -->
    <div class="itemsTabla">
        <table id="tablaPrincipal">
            <thead class="stickytop">
                <tr>
                    <th>Item</th>
                    <th>Codigo</th>
                    <th width="50%">Descripcion</th>
                    <th>Unidad</th>
                    <th>Cantidad <br>Ingreso</th>
                    <th>Cantidad <br>Consumida</th>
                    <th>Fecha<br> Registro</th>
                    <th>Cantidad<br> Mínima</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody id="tablaBodyMinimos">
                <tr>
                    <td colspan="9" style="text-align:center; padding:40px; color:#999;">
                        <i class="fas fa-search" style="font-size:24px; display:block; margin-bottom:10px;"></i>
                        Ingrese criterios de búsqueda y presione Consultar
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <!-- PAGINADOR -->
    <div id="paginador"></div>
    <!-- SCRIPTS -->
    <input type="hidden" id="id_user" value="<?php echo $_SESSION['id_user'] ?? 1; ?>">
    <input type="hidden" id="url_base" value="<?php echo constant('URL'); ?>">
    <script src="<?php echo constant('URL'); ?>public/js/jquery.js"></script>
    <script src="<?php echo constant('URL'); ?>public/js/exceljs.min.js"></script>
    <script src="<?php echo constant('URL'); ?>public/js/funciones.js?<?php echo constant('VERSION'); ?>"></script>
    <script src="<?php echo constant('URL'); ?>public/js/minimos.js?<?php echo constant('VERSION'); ?>"></script>
</body>
</html>