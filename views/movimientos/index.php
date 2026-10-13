<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo constant('URL'); ?>public/css/dialogos.css">
    <title>Document</title>
</head>
<body>
    <div class="mensaje">
        <p></p>
    </div>
    <!-- ============================================= -->
    <!-- MODAL PARA AGREGAR/EDITAR -->
    <!-- ============================================= -->
    <div class="modal" id="movimientos">
        <div class="modalWrap">
            <div class="modalWrap">
                <div id="modalTraspaso" class="modal-overlay">
                    <div class="modal-box">

                        <!-- HEADER -->
                        <div class="modal-header">
                        <h3>🔄 Confirmar Traspaso de Materiales</h3>
                        <button type="button" class="modal-close" id="closeDialog">&times;</button>
                        </div>

                        <!-- BODY -->
                        <div class="modal-body">

                        <div class="modal-row">
                            <div class="modal-field">
                            <label>Fecha:</label>
                            <input type="date" id="fechaTraspaso" readonly>
                            </div>
                            <div class="modal-field">
                            <label>Autorizado por:</label>
                            <input type="text" id="autorizadoTraspaso" placeholder="Nombre de quien autoriza">
                            </div>
                        </div>

                        <div class="modal-row">
                            <div class="modal-field">
                            <label>Responsable:</label>
                            <input type="text" id="responsableTraspaso" placeholder="Nombre de quien ejecuta">
                            </div>
                            <div class="modal-field">
                            <label>Total de ítems a traspasar:</label>
                            <input type="text" id="itemsTraspaso" readonly class="highlight">
                            </div>
                        </div>

                        <div class="modal-row">
                            <div class="modal-field">
                            <label>Centro de Costos Origen:</label>
                            <input type="text" id="ccOrigenTraspaso" readonly>
                            </div>
                            <div class="modal-field">
                            <label>Centro de Costos Destino:</label>
                            <select id="ccDestinoTraspaso">
                                <?php echo $this->listaCostosSelectDestino ?>
                            </select>
                            </div>
                        </div>

                        <!-- Advertencia -->
                        <div class="modal-alert">
                            ⚠️ <strong>Importante:</strong> Revise los datos enviados antes de grabar. Esta acción no se puede deshacer.
                        </div>

                        </div>

                        <!-- FOOTER -->
                        <div class="modal-footer">
                        <button type="button" class="btn btn-cancel" id="closeDialogButton">
                            Cancelar
                        </button>
                        <button type="button" class="btn btn-confirm" id="btnDocument">
                            📑 Guia de Remision
                        </button>
                        <button type="button" class="btn btn-confirm" id="btnConfirmarTraspaso">
                            ✅ Confirmar Traspaso
                        </button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal" id="vistaDocumento">
        <!-- ============================================================
        MODAL PRINCIPAL
        ============================================================ -->
        <div class="modalWrap">
                <div class="gr-modal-box">
                    <!-- HEADER -->
                    <div class="gr-modal-header">
                        <h2>📋 Guía de Remisión y Despacho</h2>

                        <div class="gr-doc-number">
                            <span>N°</span>
                            <input type="text" value="T001" readonly>
                            <span>-</span>
                            <input type="text" value="" readonly>
                        </div>

                    </div>

                    <!-- FECHAS -->
                    <div class="gr-fecha-bar">
                        <div class="gr-field">
                            <label>Fecha Emisión:</label>
                            <input type="date" value="" id="grFechaEmision">
                        </div>
                        <div class="gr-field">
                            <label>Fecha Traslado:</label>
                            <input type="date" id="grFechaTraslado">
                        </div>
                        <div class="gr-field">
                            <label>Tipo Guía:</label>
                            <select id="grTipoGuia">
                                <option>DESTINATARIO</option>
                                <option>REMITENTE</option>
                            </select>
                        </div>
                    </div>

                    <!-- CUERPO — 2 COLUMNAS -->
                    <div class="gr-modal-body">

                        <!-- COLUMNA IZQUIERDA: FORMULARIO -->
                        <div class="gr-col-form">

                            <div class="gr-section">
                                <div class="gr-section-title">Datos del Destinatario</div>
                                <div class="gr-field-row">
                                    <label>R.U.C.:</label>
                                    <input type="text" value="20504988173" readonly>
                                </div>
                                <div class="gr-field-row">
                                    <label>Razón Social:</label>
                                    <input type="text" value="SERVICIOS PETROLEROS Y CONSTRUCCIONES SEPCON S.A.C" readonly>
                                </div>
                                <div class="gr-field-row">
                                    <label>Dirección:</label>
                                    <input type="text" value="AV. SAN BORJA N° 445 - SAN BORJA - LIMA - PERU" readonly>
                                </div>
                            </div>

                            <div class="gr-section">
                                <div class="gr-section-title">Domicilio de Partida</div>
                                <div class="gr-field-row">
                                    <label>Almacén Origen:</label>
                                    <input type="text">
                                    <button class="gr-btn-search" title="Buscar">+</button>
                                </div>
                                <div class="gr-field-row">
                                    <label>Dirección:</label>
                                    <input type="text">
                                </div>
                            </div>

                            <div class="gr-section">
                                <div class="gr-section-title">Domicilio de Llegada</div>
                                <div class="gr-field-row">
                                    <label>Almacén Destino:</label>
                                    <input type="text" readonly>
                                    <button class="gr-btn-search" title="Buscar">+</button>
                                </div>
                                <div class="gr-field-row">
                                    <label>Dirección:</label>
                                    <input type="text">
                                </div>
                            </div>

                            <div class="gr-section">
                                <div class="gr-section-title">Empresa de Transporte</div>
                                <div class="gr-field-row">
                                    <label>Razón Social:</label>
                                    <input type="text">
                                    <button class="gr-btn-search" title="Buscar">+</button>
                                </div>
                                <div class="gr-field-row">
                                    <label>Dirección:</label>
                                    <input type="text">
                                </div>
                                <div class="gr-field-row">
                                    <label>R.U.C.:</label>
                                    <input type="text">
                                </div>
                            </div>

                            <div class="gr-section">
                                <div class="gr-section-title">Motivo</div>
                                <div class="gr-field-row">
                                    <label>Modalidad:</label>
                                    <input type="text">
                                    <button class="gr-btn-search" title="Buscar">+</button>
                                </div>
                                <div class="gr-field-row">
                                    <label>Tipo Envío:</label>
                                    <input type="text">
                                    <button class="gr-btn-search" title="Buscar">+</button>
                                </div>
                                <div class="gr-field-row">
                                    <label>Autoriza:</label>
                                    <input type="text">
                                    <button class="gr-btn-search" title="Buscar">+</button>
                                </div>
                                <div class="gr-field-row">
                                    <label>Destinatario:</label>
                                    <input type="text">
                                    <button class="gr-btn-search" title="Buscar">+</button>
                                </div>
                                <div class="gr-field-row">
                                    <label>Observaciones:</label>
                                    <textarea></textarea>
                                </div>
                            </div>

                            <div class="gr-section">
                                <div class="gr-section-title">Datos del Conductor</div>
                                <div class="gr-field-row">
                                    <label>Nombre:</label>
                                    <input type="text" id="grConductor">
                                </div>
                                <div class="gr-field-row">
                                    <label>N° Licencia:</label>
                                    <input type="text" id="grLicencia">
                                </div>
                            </div>

                            <div class="gr-section">
                                <div class="gr-section-title">Datos del Vehículo</div>
                                <div class="gr-field-row">
                                    <label>Marca:</label>
                                    <input type="text" id="grMarca">
                                </div>
                                <div class="gr-field-row">
                                    <label>Nro. Placa:</label>
                                    <input type="text" id="grPlaca">
                                </div>
                            </div>

                            <div class="gr-section">
                                <div class="gr-section-title">Datos Adicionales</div>
                                <div class="gr-field-row">
                                    <label>Peso:</label>
                                    <input type="number" step="0.01" placeholder="0.00">
                                </div>
                                <div class="gr-field-row">
                                    <label>Nro. Bultos:</label>
                                    <input type="number" placeholder="0">
                                </div>
                            </div>

                        </div>

                        <!-- COLUMNA DERECHA: VISOR PDF -->
                        <div class="gr-col-pdf">

                            <div class="gr-col-pdf-header">
                                <span class="gr-titulo">📄 Vista Previa del PDF</span>
                                <a href="#" id="grBtnDescargarPDF" class="gr-btn-download" download>
                                    ⬇️ Descargar
                                </a>
                            </div>

                            <div class="gr-col-pdf-body">
                                <div class="gr-pdf-placeholder" id="grPdfPlaceholder">
                                    <div class="gr-icono">📄</div>
                                    <div class="gr-mensaje">
                                        Complete el formulario y presione<br>
                                        <strong>"👁️ Vista Previa"</strong><br>
                                        para ver el PDF aquí
                                    </div>
                                </div>

                                <iframe id="grIframePDF" style="display:none;"></iframe>
                            </div>

                        </div>

                    </div>

                    <!-- FOOTER -->
                    <div class="gr-modal-footer">
                        <button class="gr-btn gr-btn-secondary" onclick="grCerrarModal()">
                            ✖️ Cancelar
                        </button>
                        <button class="gr-btn gr-btn-primary" onclick="grVistaPrevia()">
                            👁️ Vista Previa
                        </button>
                        <button class="gr-btn gr-btn-success" onclick="grEmitirGuia()">
                            ✅ Emitir Guía
                        </button>
                    </div>

                </div>
        </div>
    </div>
    <div class="cabezaModulo">
        <h1>Traspaso de Materiales</h1>
        <div>
            <a href="#" id="transferItems"><i class="fas fa-exchange-alt"></i><p>Transferir</p></a>
            <a href="#" id="excelFile"><i class="fas fa-file-excel"></i><p>Exportar</p></a>
            <a href="#" id="irInicio"><i class="fas fa-home"></i><p>Inicio</p></a>
        </div>
    </div>
    <div class="barraTrabajo">
        <form action="#" id="formConsulta">
            <div class="variasConsultas4campos">
                    <div>
                        <label for="costosSearch">Centro de Costos Origen: </label>
                        <select name="costosSearch" id="costosSearch">
                            <?php echo $this->listaCostosSelectOrigen ?>
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
                    <th rowspan="2">Saldo</th>
                    <th rowspan="2">Cantidad <br> Transferencia</th>
                    <th rowspan="2">Observaciones</th>
                    <th colspan="9">Condicion</th>
                </tr>
                <tr>
                    <th>1A</th>
                    <th>1B</th>
                    <th>2A</th>
                    <th>2B</th>
                    <th>3A</th>
                    <th>3B</th>
                    <th>3C</th>
                </tr>
            </thead>
            <tbody id="cuerpoTabla">
                
            </tbody>
        </table>
    </div>
    <script src="<?php echo constant('URL');?>public/js/jquery.js"></script>
    <script src="<?php echo constant('URL');?>public/js/funciones.js?<?php echo constant('VERSION')?>"></script>
    <script src="<?php echo constant('URL');?>public/js/movimientos.js?<?php echo constant('VERSION')?>"></script>
</body>
</html>