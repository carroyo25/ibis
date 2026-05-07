<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div class="modal" id="esperar">
        <div class="loadingio-spinner-spinner-5ulcsi06hlf">
            <div class="ldio-fifgg00y5y">
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
            </div>
        </div>
    </div>
    <div class="modal" id="dialogo_registro">
        <div class="registroti" id="registroti">
            <input type="hidden" name="idmmtto" id="idmmtto">
            <input type="hidden" name="idlastmmtto" id="idlastmmtto">

            <div class="titulo_dialogo" id="ventanaHeader">
                <h3>🔧 Registrar Mantenimiento</h3>
                <a href="#" id="sendNotify" title="Enviar notificación por correo">
                    <i>📧</i> <p>Notificar</p>
                </a>
            </div>

            <div class="contenedor">
                <!-- Datos fijos del equipo -->
                <div class="cabecera_dialogo">
                    <label for="serie">🔢 Serie</label>
                    <input type="text" name="serie" id="serie" readonly placeholder="Ej: ABC-1234">

                    <label for="descripcion">📝 Descripción</label>
                    <input type="text" name="descripcion" id="descripcion" readonly placeholder="Ej: Laptop Dell Latitude">
                </div>

                <!-- Tabla de mantenimientos previos -->
                <div class="tabla_dialogo">
                    <table id="tabla_detalles_mttos" class="tabla">
                        <thead>
                            <tr>
                                <th>📅 Fecha Mantenimiento</th>
                                <th>📋 Observaciones</th>
                                <th>👨‍🔧 Técnico</th>
                                <th>🗳 Eliminar</th>
                                <th>🎞 Foto</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="3" style="text-align:center;">Sin registros previos</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Formulario principal -->
                <div class="cuerpo_dialogo">
                    <div class="datos_cuerpo">
                        <label for="fecha_sugerida">📅 Fecha Sugerida</label>
                        <input type="text" name="fecha_sugerida" id="fecha_sugerida" readonly placeholder="Automático">

                        <label for="fecha_mmto">🔧 Fecha Mantenimiento *</label>
                        <input type="date" name="fecha_mmto" id="fecha_mmto" required>

                        <label for="usuario">👤 Usuario *</label>
                        <input type="text" name="usuario" id="usuario" placeholder="Nombre completo" required>

                        <label for="correo_usuario">📧 Correo *</label>
                        <input type="email" name="correo_usuario" id="correo_usuario" placeholder="usuario@ejemplo.com" required>

                        <label for="tipo_mmtto">⚙️ Tipo Mantenimiento</label>
                        <select name="tipo_mmtto" id="tipo_mmtto">
                            <option value="1">📆 Mantenimiento Programado</option>
                            <option value="2">🛡️ Mantenimiento Preventivo</option>
                            <option value="3">⚠️ Mantenimiento Correctivo</option>
                        </select>
                    </div>

                    <div class="datos_cuerpo_observaciones">
                        <label for="observaciones_dialogo">📝 Observaciones del servicio</label>
                        <textarea name="observaciones_dialogo" id="observaciones_dialogo" rows="2" placeholder="Describa las tareas realizadas..."></textarea>
                    </div>

                    <div class="datos_cuerpo">
                        <label for="procesador">💻 Procesador</label>
                        <input type="text" name="procesador" id="procesador" placeholder="Ej: Intel i5-11400">

                        <label for="ram">🧠 Memoria RAM</label>
                        <input type="text" name="ram" id="ram" placeholder="Ej: 16GB DDR4">

                        <label for="hdd">💾 Disco Duro</label>
                        <input type="text" name="hdd" id="hdd" placeholder="Ej: SSD 512GB">

                        <label for="estado_equipo">🏷️ Estado Equipo</label>
                        <select name="estado_equipo" id="estado_equipo">
                            <option value="1">✨ Nuevo</option>
                            <option value="2" selected>🟢 Usado Nivel 1</option>
                            <option value="3">🟡 Usado Nivel 2</option>
                            <option value="4">🟠 Usado Nivel 3</option>
                            <option value="5">🔴 Inoperativo</option>
                            <option value="6">⚫ Obsoleto</option>
                        </select>
                    </div>

                    <div class="datos_cuerpo_observaciones">
                        <label for="otros">🛠️ Especificaciones adicionales</label>
                        <textarea name="otros" id="otros" rows="2" placeholder="Tarjeta gráfica, fuente de poder, observaciones técnicas..."></textarea>
                    </div>
                </div>

                <!-- Botones -->
                <div class="opciones_dialogo">
                    <button type="button" id="btnAceptarDialogo">✅ Aceptar</button>
                    <button type="button" id="btnCancelarDialogo">❌ Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal" id="cambio_fecha">
        <div class="ventanaPregunta">
            <div>
                <p>Nueva fecha de entrega :</p>
                <input type="date" name="fecha_nueva" id="fecha_nueva">
            </div>
            <div>
                <button type="button" id="btnAceptarGrabar">Aceptar</button>
                <button type="button" id="btnCancelarGrabar">Cancelar</button>
            </div>
        </div>
    </div>
    <div class="modal" id="cambio_serie">
        <div class="ventanaPregunta">
            <div>
                <p>Nueva Serie :</p>
                <input type="text" name="serie_nueva" id="serie_nueva">
            </div>
            <div>
                <button type="button" id="btnAceptarSerie">Aceptar</button>
                <button type="button" id="btnCancelarSerie">Cancelar</button>
            </div>
        </div>
    </div>
    <div class="cabezaModulo">
        <h1>Registro de Mantenimientos - TI</h1>
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
                        <label for="serieBusqueda">Serie : </label>
                        <input type="text" name="serieBusqueda" id="serieBusqueda">
                    </div>
                    <div>
                        <label for="usuarioBusqueda">Usuario: </label>
                        <input type="text" name="usuarioBusqueda" id="usuarioBusqueda">
                    </div>
                    <div>
                    </div>
                    <button type="button" id="btnConsulta" class="boton3">Consultar</button> 
            </div>
        </form>
    </div>
    <div class="itemsTabla">
        <table id="tablaPrincipal" class="main-table">
            <thead class="stickytop">
                <tr>
                    <th>Nro. Serie</th>
                    <th>Nombre</th>
                    <th>Nro. Documento</th>
                    <th>Equipo</th>
                    <th>Fecha Entrega</th>
                    <th>Estado</th>
                    <th>...</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                <tr>
                    <td colspan="4" style="text-align: center; color: #94a3b8; padding: 30px;">
                        ✨ No hay registros para mostrar, seleccione un centro de costos y precione <b>Consultar</b>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <script src="<?php echo constant('URL');?>public/js/jquery.js"></script>
    <script src="<?php echo constant('URL');?>public/js/funciones.js?<?php echo constant('VERSION')?>"></script>
    <script src="<?php echo constant('URL');?>public/js/timmtto.js?<?php echo constant('VERSION')?>"></script>
</body>
</html>