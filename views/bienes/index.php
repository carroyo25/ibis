<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo Bienes/Servicios</title>
    
    <!-- Fuentes e Iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Estilos del Sistema -->
    <link rel="stylesheet" href="<?php echo constant('URL'); ?>public/css/minimos.css">
    <link rel="stylesheet" href="<?php echo constant('URL'); ?>public/css/modalcards.css">
    <link rel="stylesheet" href="<?php echo constant('URL'); ?>public/css/clases.css">
    <link rel="stylesheet" href="<?php echo constant('URL'); ?>public/css/bienes.css">
    <link rel="stylesheet" href="<?php echo constant('URL'); ?>public/css/select.css"> <!-- Estilos del buscador -->
</head>
<body>
    <!-- Mensajes flotantes -->
    <div class="mensaje">
        <p></p>
    </div>

    <!-- Modal de espera -->
    <div class="modal" id="esperar"></div>

    <!-- Modales de Pregunta (Eliminar / Restaurar) -->
    <div class="modal" id="preguntaEliminar">
        <div class="ventanaPregunta">
            <h3>Desea desactivar el registro?</h3>
            <div>
                <button type="button" id="btnAceptarPregunta">Aceptar</button>
                <button type="button" id="btnCancelarPregunta">Cancelar</button>
            </div>
        </div>
    </div>
    
    <div class="modal" id="preguntaRestaurar">
        <div class="ventanaPregunta">
            <h3>Desea activar el registro?</h3>
            <div>
                <button type="button" id="btnAceptarActivar">Aceptar</button>
                <button type="button" id="btnCancelarActivar">Cancelar</button>
            </div>
        </div>
    </div>

    <!-- ============================================= -->
    <!-- MODAL PRINCIPAL DE REGISTRO -->
    <!-- ============================================= -->
    <div class="modal" id="proceso">
        <div class="ventanaProceso w60por">
            <div class="cabezaProceso">
                <form action="" autocomplete="off" id="formProceso">
                    
                    <!-- Campos Ocultos -->
                    <input type="hidden" name="codigo_item" id="codigo_item">
                    <input type="hidden" name="codigo_tipo" id="codigo_tipo">
                    <input type="hidden" name="codigo_grupo" id="codigo_grupo">
                    <input type="hidden" name="codigo_clase" id="codigo_clase">
                    <input type="hidden" name="codigo_familia" id="codigo_familia">
                    <input type="hidden" name="codigo_catalogo" id="codigo_catalogo">
                    <input type="hidden" name="codigo_unidad" id="codigo_unidad">

                    <!-- Barra de Acciones -->
                    <div class="barraOpciones primeraBarra">
                        <span>Datos Generales</span>
                        <div>
                            <button type="button" id="grabarItem" title="Grabar Datos">
                                <p><i class="far fa-save"></i> Grabar Registro</p> 
                            </button>
                            <button type="button" id="cerrarVentana" title="Cerrar">
                                <i class="fas fa-window-close"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Cuerpo del Formulario -->
                    <div class="dataProceso">
                        <div class="seccion_izquierda">
                            
                            <div class="column2">
                                <label for="codigo">Codigo :</label>
                                <input type="text" name="codigo" id="codigo" class="cerrarLista mayusculas w50por resaltado" readonly placeholder="Autogenerado">
                            </div>

                            <div class="column2">
                                <label for="tipo_item">Tipo:</label>
                                <select id="tipo_item" name="tipo_item" style="width: 100%;" required>
                                    <option value="">Seleccione...</option>
                                    <?php echo $this->listaTipos; ?> 
                                </select>
                            </div>

                            <div class="column2">
                                <label for="grupo">Grupo :</label>
                                <select id="grupo" name="grupo" style="width: 100%;" required>
                                    <option value="">Seleccione...</option>
                                </select>
                            </div>

                            <div class="column2">
                                <label for="clase">Clase:</label>
                                <select id="clase" name="clase" style="width: 100%;" required>
                                    <option value="">Seleccione...</option>
                                </select>
                            </div>

                            <div class="column2">
                                <label for="familia">Familia:</label>
                                <select id="familia" name="familia" style="width: 100%;" required>
                                    <option value="">Seleccione...</option>
                                </select>
                            </div>

                            <div class="column2">
                                <label for="descripcion">Nombre :</label>
                                <input type="text" name="descripcion" id="descripcion" class="mayusculas cerrarLista obligatorio" required placeholder="Nombre del producto">
                            </div>

                            <div class="column2">
                                <label for="unidad">Unidad:</label>
                                <select id="unidad" name="unidad" style="width: 100%;" required>
                                    <option value="">Seleccione...</option>
                                </select>
                            </div>

                            <div class="column4_7_1">
                                <label for="nro_parte">Nro.Parte:</label>
                                <input type="text" name="nro_parte" id="nro_parte" class="mayusculas cerrarLista">
                                <label for="cod_pat">Codigo Patrimonial:</label>
                                <input type="text" name="cod_pat" id="cod_pat" class="mayusculas cerrarLista">
                            </div>

                            <div class="column4_1" style="margin-top: 15px; display: flex; gap: 20px;">
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
                    </div>

                    <!-- ============================================= -->
                    <!-- CARRITO DE CÓDIGOS CREADOS -->
                    <!-- ============================================= -->
                    <div class="seccion_carrito">
                        <h4 style="margin-top: 0; color: #2a7de1; display: flex; align-items: center; gap: 8px;">
                            <i class="fas fa-list-ul"></i> Códigos creados en esta sesión
                        </h4>
                        
                        <table class="tabla-carrito">
                            <thead>
                                <tr>
                                    <th style="width: 20%;">Código</th>
                                    <th style="width: 65%;">Descripción</th>
                                    <th style="width: 15%; text-align: center;">Quitar</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyCarrito">
                                <tr>
                                    <td colspan="3" style="text-align: center; padding: 15px; color: #999;">
                                        Aún no has creado códigos en esta sesión.
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <div style="text-align: right; margin-top: 15px;">
                            <button type="button" id="btnEnviarCorreo" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Enviar Lista por Correo
                            </button>
                        </div>
                    </div>
                    <!-- FIN DEL CARRITO -->

                </form>
            </div>
        </div>
    </div>

    <!-- ============================================= -->
    <!-- VISTA PRINCIPAL (TABLA) -->
    <!-- ============================================= -->
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
    
    <div class="itemsTabla">
        <table id="tablaPrincipal">
            <thead class="stickytop">
                <tr>
                    <th width="3%">Item</th>
                    <th width="10%">Codigo</th>
                    <th>Tipo</th>
                    <th>Denominación</th>
                    <th>Unidad</th>
                    <th width="3%">...</th>
                </tr>
            </thead>
            <tbody id="tbodyBienes">
                <?php echo $this->listaItems;?>
            </tbody>
        </table>
    </div>
    
    <!-- SCRIPTS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="<?php echo constant('URL');?>public/js/selectBuscador.js"></script>
    <script src="<?php echo constant('URL');?>public/js/funciones.js?<?php echo constant('VERSION')?>"></script>
    <script src="<?php echo constant('URL');?>public/js/bienes.js?<?php echo constant('VERSION')?>"></script>
</body>
</html>