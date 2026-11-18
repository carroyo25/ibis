<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo Bienes/Servicios</title>
    
    <!-- Iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- CSS del Módulo -->
    <link rel="stylesheet" href="<?php echo constant('URL'); ?>public/css/bienes.css">
</head>
<body>
    <div class="mensaje"><p></p></div>
    <div class="modal" id="esperar"></div>
    
    <!-- MODALES DE PREGUNTA -->
    <div class="modal" id="preguntaEliminar">
        <div class="ventanaPregunta">
            <h3>¿Desea desactivar el registro?</h3>
            <div>
                <button type="button" id="btnAceptarPregunta">Aceptar</button>
                <button type="button" id="btnCancelarPregunta">Cancelar</button>
            </div>
        </div>
    </div>

    <div class="modal" id="preguntaRestaurar">
        <div class="ventanaPregunta">
            <h3>¿Desea activar el registro?</h3>
            <div>
                <button type="button" id="btnAceptarActivar">Aceptar</button>
                <button type="button" id="btnCancelarActivar">Cancelar</button>
            </div>
        </div>
    </div>

    <!-- MODAL PRINCIPAL DE REGISTRO (2 COLUMNAS) -->
    <div class="modal" id="proceso">
        <div class="ventanaProceso w85por">
            <div class="cabezaProceso">
                <form action="" autocomplete="off" id="formProceso">
                    
                    <div class="barraOpciones primeraBarra">
                        <span><i class="fas fa-edit" style="color: var(--b-primary); margin-right: 8px;"></i> Registro de Productos</span>
                        <div>
                            <button type="button" id="grabarItem" title="Grabar Datos">
                                <i class="far fa-save"></i> Grabar Registro
                            </button>
                            <button type="button" id="cerrarVentana" title="Cerrar">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- CONTENEDOR DE LAS 2 COLUMNAS -->
                    <div class="modal-body-grid">
                        
                        <!-- COLUMNA IZQUIERDA: DATOS DEL PRODUCTO -->
                        <div class="col-formulario">
                            <div class="column1">
                                <label for="codigo">CÓDIGO :</label>
                                <input type="text" name="codigo" id="codigo" class="mayusculas" readonly placeholder="Autogenerado">
                            </div>
                            <div class="column1">
                                <label for="tipo_item">TIPO:</label>
                                <select id="tipo_item" name="tipo_item" required>
                                    <option value="">Buscar tipo...</option>
                                </select>
                            </div>
                            <div class="column1">
                                <label for="grupo">GRUPO :</label>
                                <select id="grupo" name="grupo" required>
                                    <option value="">Buscar grupo...</option>
                                </select>
                            </div>
                            <div class="column1">
                                <label for="clase">CLASE:</label>
                                <select id="clase" name="clase" required>
                                    <option value="">Seleccione...</option>
                                </select>
                            </div>
                            <div class="column1">
                                <label for="familia">FAMILIA:</label>
                                <select id="familia" name="familia" required>
                                    <option value="">Seleccione...</option>
                                </select>
                            </div>
                            <div class="column1">
                                <label for="descripcion">NOMBRE :</label>
                                <input type="text" name="descripcion" id="descripcion" class="mayusculas" required placeholder="Nombre del producto">
                            </div>
                            <div class="column1">
                                <label for="unidad">UNIDAD:</label>
                                <select id="unidad" name="unidad" required>
                                    <option value="">Buscar unidad...</option>
                                </select>
                            </div>
                            <!-- Fila de dos inputs juntos -->
                            <div style="display: flex; gap: 15px;">
                                <div class="column1" style="flex: 1;">
                                    <label for="nro_parte">NRO.PARTE:</label>
                                    <input type="text" name="nro_parte" id="nro_parte" class="mayusculas">
                                </div>
                                <div class="column1" style="flex: 1;">
                                    <label for="cod_pat">CÓD. PATRIMONIAL:</label>
                                    <input type="text" name="cod_pat" id="cod_pat" class="mayusculas">
                                </div>
                            </div>
                            
                            <!-- Checkboxes -->
                            <div class="checkboxes-group">
                                <div>
                                    <input type="checkbox" name="serie" id="serie" value="1">
                                    <label for="serie">Registra Series</label>
                                </div>
                                <div>
                                    <input type="checkbox" name="detraccion" id="detraccion" value="1">
                                    <label for="detraccion">Afecto a detracción</label>
                                </div>
                                <div>
                                    <input type="checkbox" name="actfij" id="actfij" value="1">
                                    <label for="actfij">Activo Fijo</label>
                                </div>
                            </div>
                        </div>

                        <!-- COLUMNA DERECHA: CARRITO Y CORREO -->
                        <div class="col-carrito">
                            <h4><i class="fas fa-list-ul"></i> Códigos en esta sesión</h4>
                            
                            <table class="tabla-carrito">
                                <thead>
                                    <tr>
                                        <th style="width: 25%;">CÓDIGO</th>
                                        <th style="width: 60%;">DESCRIPCIÓN</th>
                                        <th style="width: 15%; text-align: center;">X</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyCarrito">
                                    <tr>
                                        <td colspan="3" style="text-align: center; padding: 20px; color: #94a3b8;">
                                            Aún no has creado códigos.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <!-- Cajita para enviar el correo -->
                            <div class="envio-correo-box">
                                <label for="correoDestino">CORREO DE ENVÍO:</label>
                                <div class="input-group-correo">
                                    <input type="email" id="correoDestino" placeholder="ejemplo@correo.com" required>
                                    <button type="button" id="btnEnviarCorreo" title="Enviar Correo">
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- VISTA PRINCIPAL -->
    <div class="cabezaModulo">
        <h1>Catálogo Bienes/Servicios</h1>
        <div>
            <a href="#" id="nuevoRegistro"><i class="far fa-file"></i><p>Nuevo</p></a>
            <a href="#" id="irInicio"><i class="fas fa-home"></i><p>Inicio</p></a>
        </div>
    </div>
    
    <div class="barraTrabajo">
        <div class="unaConsulta">
            <label for="consulta">Nombre : </label>
            <input type="text" name="consulta" id="consulta" placeholder="Buscar por nombre...">
        </div>
    </div>
    
    <!-- CONTENEDOR DE TABLA Y PAGINADOR -->
    <div class="tabla-wrapper">
        <div class="itemsTabla">
            <table id="tablaPrincipal">
                <thead class="stickytop">
                    <tr>
                        <th width="8%">Item</th>
                        <th width="18%">Codigo</th>
                        <th width="15%">Tipo</th>
                        <th width="40%">Denominación</th>
                        <th width="12%">Unidad</th>
                        <th width="7%">...</th>
                    </tr>
                </thead>
                <tbody id="tbodyBienes"></tbody>
            </table>
        </div>
        
        <div class="paginador" id="paginadorBienes">
            <div class="info" id="infoPaginador">Mostrando <strong>0</strong> - <strong>0</strong> de <strong>0</strong></div>
            <div class="botones" id="botonesPaginador"></div>
        </div>
    </div>
    
    <!-- SCRIPTS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="<?php echo constant('URL');?>public/js/selectBuscador.js"></script>
    <script src="<?php echo constant('URL');?>public/js/funciones.js?<?php echo constant('VERSION')?>"></script>
    <script src="<?php echo constant('URL');?>public/js/bienes.js?<?php echo constant('VERSION')?>"></script>
</body>
</html>