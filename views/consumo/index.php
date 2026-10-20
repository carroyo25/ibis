<!DOCTYPE html>
<html lang="es" sigplusextliteextension-installed="true" sigwebext-installed="true">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
    <link rel="stylesheet" href="<?php echo constant('URL'); ?>public/css/consumo.css">
    <link rel="stylesheet" href="<?php echo constant('URL'); ?>public/css/dialogos.css">
    <title>Document</title>
</head>
<body onload="ClearFormData();">
    <div class="mensaje">
        <p></p>
    </div>
    <div class="modal" id="comentarios">
        <div class="ventanaComentarios">
            <h3>Observaciones</h3>
            <hr>
            <div class="cuerpoComentarios">
                
            </div>
            <div>
                <button type="button" id="btnAceptarComentarios">Aceptar</button>
            </div>
        </div>
    </div>
    <div class="modal" id="preguntaAutoriza">
        <div class="modalWrap">
            <div class="ventanaWrapPregunta">
                <!-- ICONO -->
                <span class="modal-icon">🚀</span>
                
                <!-- TÍTULO -->
                <h2 class="modal-title">Registro de salida</h2>

                <!-- SUBTÍTULO -->
                <p class="modal-subtitle">
                    ¿Los <strong><span id="oc_autoriza">datos registrados</span></strong> son correctos?
                </p>

                 <!-- BOTONES -->
                <div class="modal-actions">
                    <button class="btn-modal accept" id="btnAceptarGrabar">✅ Aceptar</button>
                    <button class="btn-modal cancel" id="btnCancelarGrabar">❌ Cancelar</button>
                </div>
            </div>

        </div>
    </div>
    <div class="modal" id="cambiarFila">
        <div class="ventanaPregunta">
            <h3>Actualizar Registros</h3>
            <form class="ventana__actualizar" id="form_actualizar">
                <label for="codigo__cambio">Codigo</label>
                <input type="text" name="codigo__cambio" id="codigo__cambio" class="input__change">
                <label for="cantidad__cambio">Cantidad</label>
                <input type="text" name="cantidad__cambio" id="cantidad__cambio" class="input__change">
                <label for="patrimonio__cambio">Patrimonio</label>
                <input type="checkbox" name="patrimonio__cambio" id="patrimonio__cambio">
                <label for="hoja__cambio">Nro. Hoja</label>
                <input type="text" name="hoja__cambio" id="hoja__cambio" class="input__change">
                <label for="serie__cambio">Serie</label>
                <input type="text" name="serie_cambio" id="serie__cambio" class="input__change">
            </form>
            <div class="opciones__modifica__kardex">
                <button type="button" id="btnAceptarModificar" class="boton4">Aceptar</button>
                <button type="button" id="btnCancelarModificar" class="boton4">Cancelar</button>
                <button type="button" id="btnEliminarRegistro" class="boton4">Eliminar</button>
            </div>
        </div>
    </div>
    <div class="modal" id="dialogo_registro">
        <div class="modalWrap">
            <div class="rc-modal-container">
            
                <!-- Header -->
                <div class="rc-modal-header">
                    <h2 class="rc-modal-title">Registrar Consumo</h2>
                    <button class="rc-btn-close" id="btnCerrarModal">&times;</button>
                </div>

                <!-- Body -->
                <div class="rc-modal-body">
                    
                    <!-- Búsqueda -->
                    <div class="rc-search-section">
                        <div class="rc-form-group">
                            <label class="rc-label" for="codigoSearch">Codigo</label>
                            <input type="text" name="codigoSearch" class="rc-input" id="codigoSearch" placeholder="Ingrese codigo">
                        </div>
                        <div class="rc-form-group">
                            <label class="rc-label" for="descripSearch">Descripcion</label>
                            <input type="text" name="descripSearch" class="rc-input" id="descripSearch" placeholder="Ingrese Descripcion">
                            <input type="hidden" name="maximo_stock" id="maximo_stock">
                        </div>
                    </div>

                    <!-- Tabla -->
                    <div class="rc-table-wrapper">
                        <table id="tabla_detalles_productos" class="rc-table">
                            <thead>
                                <tr>
                                    <th style="width: 25%;">Código</th>
                                    <th style="width: 50%;">Descripción</th>
                                    <th style="width: 10%; text-align: center;">Und</th>
                                    <th style="width: 15%; text-align: right;">Stock</th>
                                </tr>
                            </thead>
                            <tbody id="cuerpoRegistros">
                                
                            </tbody>
                        </table>
                    </div>

                    <!-- Formulario Grid -->
                    <div class="rc-form-grid">
                        
                        <!-- Fila 1 -->
                        <div class="rc-form-group">
                            <label>Patrimonio</label>
                            <div class="rc-checkbox-container">
                                <input type="checkbox" id="patrimonio">
                                <label for="patrimonio">¿Es Patrimonio?</label>
                            </div>
                        </div>
                        <div class="rc-form-group">
                            <label class="rc-label">Motivo de Cambio</label>
                            <select class="rc-select" name="cambio_epp" id="cambio_epp">
                                <option value="-1">Seleccione una opcion</option>
                                <option value="239">PRIMERA ENTREGA </option>
                                <option value="240">CAMBIO EPP</option>
                                <option value="241">ROTURA</option>
                                <option value="242">PERDIDA</option>
                                <option value="243">DEFORMADO</option>
                                <option value="244">FALTA PARTES</option>
                                <option value="245">DESGASTE POR PROPIO USO</option>
                                <option value="246">OTROS</option>
                            </select>
                        </div>

                        <!-- Fila 2 -->
                        <div class="rc-form-group">
                            <label class="rc-label">Cantidad</label>
                            <input class="rc-input" name="cantidad_dialogo" id="cantidad_dialogo" type="number" placeholder="0.00" min="1">
                        </div>
                        <div class="rc-form-group">
                            <label class="rc-label">N° Serie</label>
                            <input class="rc-input" type="text" placeholder="Ingrese Serie" name="serie_dialogo" id="serie_dialogo">
                        </div>

                        <!-- Fila 3 -->
                        <div class="rc-form-group">
                            <label class="rc-label">N° Hoja</label>
                            <input class="rc-input" type="text" placeholder="Ingrese N° Hoja" name="nhoja_dialogo" id="nhoja_dialogo">
                        </div>
                        <div class="rc-form-group">
                            <label class="rc-label">Isométrico</label>
                            <input class="rc-input" type="text" placeholder="Ingrese Isométrico" name="isometricos_dialogo" id="isometricos_dialogo">
                        </div>

                        <!-- Fila 4 -->
                        <div class="rc-form-group">
                            <label for="estado_dialogo" class="rc-label">Estado</label>
                            <input class="rc-input" type="text" placeholder="Ingrese Estado" name="estado_dialogo" id="estado_dialogo">
                        </div>

                        <div></div> <!-- Espacio vacío para mantener la simetría -->

                        <!-- Fila 5 -->
                        <div class="rc-form-group rc-full">
                            <label class="rc-label" for="observaciones_dialogo">Observaciones</label>
                            <textarea class="rc-textarea" placeholder="Ingrese observaciones adicionales aquí..." name="observaciones_dialogo" id="observaciones_dialogo"></textarea>
                        </div>

                    </div>
                </div>

                <!-- Footer -->
                <div class="rc-modal-footer">
                    <button class="rc-btn rc-btn-primary" id="btnAceptarDialogoKardex">Aceptar</button>
                    <button class="rc-btn rc-btn-secondary" id="btnCancelarDialogoKardex">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal" id="exporta">
        <div class="ventanaPregunta">
            <h3>Seleccione el Centro de Costos</h3>
            <div>
                <select name="costosExport" id="costosExport">
                    <?php echo $this->listaCostosSelect ?>
                </select>
            </div>
            <div>
                <button type="button" id="btnDescarga">Descargar</button>
                <button type="button" id="btnCancelarExport">Cancelar</button>
            </div>
        </div>
    </div>
    <div class="modal" id="hojakardex">
        <div class="ventanaVistaPrevia">
            <div class="tituloVista">
                <h3>Kardex</h3>
                <a href="#" id="closePreview" title="Cerrar Ventana"><i class="fas fa-window-close"></i></a>
            </div>
            <iframe src=""></iframe>
        </div>
    </div>
    <div class="modal" id="registro">
        <div class="vistaPrevia">
            <div class="ventanaPregunta">
                <section>
                    <h3>Buscar</h3>
                    <input type="search" name="buscaKardex" id="buscaKardex">
                </section>
                <section>
                    <table>
                        <thead>
                            <tr>
                                <th>Codigo</th>
                                <th>Descripcion</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </section>
                <section>
                    <label for="motivo_cambio">Motivo de Cambio</label>
                    <select id="motivo_cambio">
                        <option value="-1">Seleccione una opcion</option>
                        <option value="239">PRIMERA ENTREGA </option>
                        <option value="240">CAMBIO EPP</option>
                        <option value="241">ROTURA</option>
                        <option value="242">PERDIDA</option>
                        <option value="243">DEFORMADO</option>
                        <option value="244">FALTA PARTES</option>
                        <option value="245">DESGASTE POR PROPIO USO</option>
                        <option value="246">OTROS</option>
                    </select>
                </section>
                <section>
                    
                </section>
            </div>
        </div>
    </div>
    <div class="cabezaModulo">
        <h1>Kardex Personal</h1>
        <div>
            <a href="#" id="btnRegister"><i class="fas fa-project-diagram"></i><p>Registrar</p></a>
            <a href="#" id="btnKardex"><i class="fas fa-file-pdf"></i><p>Kardex</p></a>
            <a href="#" id="excelFile"><i class="fas fa-file-excel"></i><p>Reporte</p></a>
            <a href="#" id="btnBuscar"><i class="fas fa-search-location"></i><p>Buscar</p></a>
            <a href="#" id="irInicio"><i class="fas fa-home"></i><p>Inicio</p></a>
        </div>
    </div>
    <div class="barraTrabajo">
        <div class="variasConsultas5campos">
            <input type="hidden" name="cut" id="cut">
            <input type="hidden" name="correo" id="correo">
            <canvas id="pdfCanvas" height="150" width="150" class="oculto"></canvas>
            <div>
                <label for="costosSearch">Centro de Costos: </label>
                <select name="costosSearch" id="costosSearch" class="item4">
                    <?php echo $this->listaCostosSelect ?>
                </select>
            </div>
            <div>
                <label for="docident">N°. Documento </label>
                <input type="text" id="docident" name="docident" class="ingreso">
            </div>
            <div>
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" class="ingreso">
            </div>
            <div>
                <label for="cargo">Cargo</label>
                <input type="cargo" id="cargo" name="cargo" class="ingreso">
            </div>
            <div>
                <img src="" id="vistafirma">
            </div> 
            <button id="btnGrabarKardex" class="boton3 oculto">Aceptar</button>
            <button type="button" class="boton3"id="btnFirmar"  onclick="StartSign()">Firmar</button>
            <!--<div>
                <label for="codeRead">Codigo: </label>
                <input type="text" name="codeRead" id="codeRead" style="opacity:1;position:relative" value=""> 
            </div>-->
        </div>
    </div>
    <div class="itemsTabla">
        <table id="tablaPrincipal">
            <thead class="stickytop">
                <tr>
                    <th>Item</th>
                    <th>Codigo</th>
                    <th>Descripcion</th>
                    <th>UND.</th>
                    <th>Cant.</th>
                    <th>Fecha</br>Salida</th>
                    <th>N° Hoja</th>
                    <th>Isometricos</th>
                    <th>Observaciones</th>
                    <th>Serie</th>
                    <th>Patrimonio</th>
                    <th>Concepto</br>Cambio</th>
                    <th>Estado</th>
                    <th>Firma</th>
                    <th>...</th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table>
    </div>
    <canvas id="cnv" name="cnv" width="500" height="100" ></canvas>
    <canvas name="SigImg" id="SigImg" width="500" height="100"></canvas>
    <form action="" name="FORM1">
        <input type="hidden" name="firmado" id="firmado">
	</form>

    <script src="<?php echo constant('URL');?>public/js/jquery.js"></script>
    <script src="<?php echo constant('URL');?>public/js/regfirma.js"></script>
    <script src="<?php echo constant('URL');?>public/js/funciones.js?<?php echo constant('VERSION')?>"></script>
    <script src="<?php echo constant('URL');?>public/js/consumo.js?<?php echo constant('VERSION')?>"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.4.456/pdf.min.js"></script>
</body>
</html>
