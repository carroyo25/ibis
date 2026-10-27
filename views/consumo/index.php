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
            <div class="modal-content">
                <div class="modal-header">
                    <div class="header-title">
                        <div class="header-icon">📄</div>
                        <div>
                            <h2>Registrar Consumo</h2>
                            <p>Seleccione productos, especifique cantidad y confirme para añadir al Kardex</p>
                        </div>
                    </div>
                    <button class="btn-close">✕</button>
                </div>
                <div class="modal-body">
                    <div class="column-left">
                        <div class="section-title">1. CATALOGO DE PRODUCTOS <span id="count-disponibles" style="font-weight:normal; color:#64748b;">- disponibles</span></div>
                        
                        <!-- FILTROS: Búsqueda General, Orden de Compra y N° Pedido -->
                        <div class="filters-container">
                            <div class="input-group" style="flex: 2;">
                                <input type="text" id="filtroTexto" class="form-control" placeholder="🔍 Código o descripción...">
                            </div>
                            <div class="input-group" style="flex: 1;">
                                <input type="text" id="filtroOrdenCompra" class="form-control" placeholder="Nota Ingreso">
                            </div>
                        </div>

                        <div class="table-container">
                            <table id="tablaProductos">
                                <thead>
                                    <tr>
                                        <th>CÓDIGO <span class="sort-icon">▼</span></th>
                                        <th>DESCRIPCIÓN <span class="sort-icon">▼</span></th>
                                        <th>ORDEN</th>
                                        <th>PEDIDO</th>
                                        <th>UND</th>
                                        <th>STOCK <span class="sort-icon">▼</span></th>
                                    </tr>
                                </thead>
                                <tbody id="tabla-productos">
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="column-right">
                        <div class="section-title" style="margin-top: 0;">2. PRODUCTO SELECCIONADO <span style="background:#dcfce7; color:#166534; padding:2px 8px; border-radius:4px; font-size:10px;">Disponible para entrega</span></div>
                        
                        <div class="product-card-selected" id="cardProductoSeleccionado">
                            <div class="product-icon">📦</div>
                            <div class="product-info">
                                <span id="cardCodigo" style="background:#fef3c7; padding:2px 6px; border-radius:4px; font-size:10px; font-weight:bold;"></span>
                                <h4 id="cardDescripcion"></h4>
                            </div>
                            <div class="badge-stock">Stock actual: <span id="cardStock">-</span> -</div>
                        </div>

                        <div class="section-card">
                            <div class="section-title">3. DATOS DEL MOVIMIENTO</div>
                            
                            <div class="form-grid">
                                <div class="input-group">
                                    <label style="color:#0f172a; font-weight:bold;">Cantidad a Registrar *</label>
                                    <input type="number" id="inputCantidad" class="form-control input-cantidad-destacado" value="1" min="1">
                                </div>
                                <div class="input-group">
                                    <label>Motivo de Cambio</label>
                                    <select id="inputMotivo" class="form-control">
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
                                
                                <div class="input-group">
                                    <label>N° Serie</label>
                                    <input type="text" id="inputSerie" class="form-control" placeholder="Ej: SN-90210">
                                </div>
                                <div class="input-group">
                                    <label>Estado</label>
                                    <input type="text" id="inputEstado" class="form-control" placeholder="Ej: 1A|1B|2B..">
                                </div>

                                <div class="input-group">
                                    <label>N° Hoja</label>
                                    <input type="text" id="inputHoja" class="form-control" placeholder="N° Hoja">
                                </div>
                                <div class="input-group" style="display:flex; align-items:center; margin-top:15px;">
                                    <input type="checkbox" id="patrimonio" style="margin-right:8px;">
                                    <label for="patrimonio" style="margin:0;">¿Es Patrimonio?</label>
                                </div>

                                <div class="input-group" style="grid-column: span 2;">
                                    <label>Isométrico</label>
                                    <input type="text" id="inputIsometrico" class="form-control" placeholder="Isométrico">
                                </div>

                                <!-- TEXTAREA DE OBSERVACIONES -->
                                <div class="input-group" style="grid-column: span 2;">
                                    <label>Observaciones</label>
                                    <textarea id="inputObservaciones" class="form-control" placeholder="Ingrese observaciones adicionales aquí..."></textarea>
                                </div>
                            </div>

                            <button class="btn-add" onclick="agregarItem()">+ Añadir a la Lista de Consumos de la Sesión</button>
                        </div>

                        <div class="section-card" style="border-color:#fcd34d; background:#fffdf5;">
                            <div class="section-title">
                                4. CONSUMOS AGREGADOS EN ESTA SESIÓN 
                                <span style="background:#fef3c7; color:#92400e; padding:2px 8px; border-radius:12px; font-size:10px;" id="contador-items">0 items por guardar</span>
                            </div>
                            <div class="items-added-box" id="lista-items">
                                Aún no ha añadido ítems. Complete la cantidad y presione "+ Añadir a la Lista".
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="footer-note">
                        <span style="color:#22c55e; font-size:16px;">✔</span> Haga clic en "Guardar Consumo Final" para impactar en el Kardex.
                    </div>
                    <div class="footer-actions">
                        <!-- NUEVO BOTÓN DE CONSUMO INICIAL -->
                        <button class="btn-inicial" id="btnConsumoInicial">📦 Consumo Inicial</button>
                        
                        <button class="btn-cancel">Cancelar</button>
                        <button class="btn-save" id="btnGuardarFinal">💾 Guardar Consumo Final (0)</button>
                    </div>
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
            <tbody id="cuerpoTablaPrincipal">
                
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
