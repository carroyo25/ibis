<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ingreso Almacen</title>
    <link rel="stylesheet" href="<?php echo constant('URL'); ?>public/css/cargoplan.css">
    <link rel="stylesheet" href="<?php echo constant('URL'); ?>public/css/bienes.css">
    <link rel="stylesheet" href="<?php echo constant('URL'); ?>public/css/forms.css">
    <link rel="stylesheet" href="<?php echo constant('URL'); ?>public/css/dialogos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</head>
<body>
    <div class="modal" id="proceso">
        <div class="ventanaProceso">
            <div class="cabezaProceso">
                <form action="#" id="formProceso" autocomplete="off">
                    <input type="hidden" name="codigo_costos" id="codigo_costos"> 
                    <input type="hidden" name="codigo_area" id="codigo_area">
                    <input type="hidden" name="codigo_almacen_origen" id="codigo_almacen_origen">
                    <input type="hidden" name="codigo_almacen_destino" id="codigo_almacen_destino">
                    <input type="hidden" name="codigo_estado" id="codigo_estado">
                    <input type="hidden" name="codigo_despacho" id="codigo_despacho">
                    <input type="hidden" name="codigo_autoriza" id="codigo_autoriza">
                    <input type="hidden" name="codigo_ingreso" id="codigo_ingreso">
                    <input type="hidden" name="codigo_recepcion" id="codigo_recepcion" value="<?php echo $_SESSION['iduser']?>">
                    
                    <!-- BARRA 1 -->
                    <div class="barraOpciones primeraBarra">
                        <span><i class="fas fa-file-invoice"></i> Datos Generales</span>
                        <div>
                            <!--<button type="button" id="atachDocs" title="Adjuntar documentos" class="btn-moderno btn-glass">
                                <i class="fas fa-paperclip"></i> Adjuntar
                            </button>-->
                            <button type="button" id="updateDocument" title="Grabar Ingreso" class="btn-moderno btn-primary">
                                <i class="fas fa-save"></i> Grabar
                            </button>
                            <button type="button" id="cancelDocument" title="Anular Ingreso" class="btn-moderno btn-danger">
                                <i class="fas fa-ban"></i> Anular
                            </button>
                            <button type="button" id="closeProcess" title="Cerrar" class="btn-cerrar">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>

                    <!-- FORMULARIO -->
                    <div class="dataProceso_2">
                        
                        <div class="seccion_izquierda borde-secciones">
                            <div class="col-doble">
                                <div class="col">
                                    <label for="fecha">Fecha :</label>
                                    <input type="date" name="fecha" id="fecha" value="<?php echo date("Y-m-d");?>" readonly>
                                </div>
                                <div class="col">
                                    <label for="numero">Número Registro:</label>
                                    <input type="text" name="numero" id="numero" readonly>
                                </div>
                            </div>
                            
                            <div class="col">
                                <label for="costos">CCostos:</label>
                                <input type="text" name="costos" id="costos" class="mostrarLista busqueda" placeholder="Elija una opción">
                                <div class="lista" id="listaCostos">
                                <ul><?php echo $this->listaCostos?></ul> 
                                </div>
                            </div>
                        </div>

                        <div class="seccion_medio borde-secciones">
                            <div class="col">
                                <label for="almacen_origen_ingreso">Almacén Origen:</label>
                                <input type="text" name="almacen_origen_ingreso" id="almacen_origen_ingreso" class="busqueda" readonly>
                            </div>
                            <div class="col">
                                <label for="almacen_destino_ingreso">Almacén Destino:</label>
                                <input type="text" name="almacen_destino_ingreso" id="almacen_destino_ingreso" class="mostrarLista busqueda" readonly>
                            </div>
                        </div>

                        <div class="seccion_derecha borde-secciones">
                            <div class="col">
                                <label for="autoriza">Autoriza:</label>
                                <input type="text" name="autoriza" id="autoriza" class="mostrarLista busqueda" placeholder="Elija opción" readonly>
                                <div class="lista uno rowFive" id="listaRecepciona">
                                    <ul><?php echo $this->listaRecepciona?></ul> 
                                </div>
                            </div>
                            <div class="col-doble">
                                <div class="col">
                                    <label for="guia">Guia/Trans/Comp.:</label>
                                    <input type="text" name="cnumguia" id="guia">
                                </div>
                                <div class="col">
                                    <label for="RS">R.S.:</label>
                                    <input type="text" name="referido" id="referido">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- BARRA 2 -->
                    <div class="barraOpciones">
                        <span><i class="fas fa-list"></i> Detalles</span>
                        <div>
                            <button type="button" id="uploadDocs" title="Documentos Adjuntos" class="btn-moderno btn-glass">
                                <i class="fas fa-eye"></i> Ver / Adjuntar Arhivos
                                <span class="button__atach cookie_info" id="atach_counter"></span>
                            </button>
                            <button type="button" id="itemsImport" title="Importar Guias de Remision" class="btn-moderno btn-glass">
                                <i class="fas fa-truck"></i> Guías Remisión
                            </button>
                            <button type="button" id="itemsTransfer" title="Importar Transferencias" class="btn-moderno btn-glass">
                                <i class="fas fa-exchange-alt"></i> Notas Transf.
                            </button>
                            <button type="button" id="itemsLocals" title="Importar compras Locales" class="btn-moderno btn-glass">
                                <i class="fas fa-shopping-cart"></i> Compras Locales
                            </button>
                        </div>
                    </div>

                    <!-- TABLA -->
                    <div class="tablaInterna">
                        <div class="table-container">
                            <table class="tablaNewDesing" id="tablaDetalles">
                                <thead>
                                    <tr class="stickytop">
                                        <th width="50ch">Item</th>
                                        <th width="110ch">Codigo</th>
                                        <th width="350ch">Descripcion</th>
                                        <th width="50ch">UND</th>
                                        <th width="100ch">Cant. Env.</th>
                                        <th width="100ch">Cant. Rec.</th>
                                        <th idth="400ch">Observaciones</th>
                                        <th idth="150ch">Area</th>
                                        <th width="100ch">F. Venc.</th>
                                        <th width="100ch">Condicion</th>
                                        <th width="100ch">Ubicación</th>
                                        <th width="100ch">Pedido</th>
                                        <th width="100ch">Orden</th>
                                        <th width="100ch">G. Rem.</th>
                                        <th width="100ch">Serie</th>
                                        <th width="100ch">F. Calib.</th>
                                        <th width="100ch">Cert. Calib.</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Aquí tu PHP hace el foreach de los items -->
                                </tbody>
                            </table>
                        </div>
                        
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal" id="busqueda">
        <div class="ventanaBusqueda w75por">
            <div class="tituloVentana">
                <span id="tituloBusqueda">Guias de Remision</span>
                <div>
                    <a href="#" id="closeSearch"><i class="fas fa-window-close"></i></a>
                </div>
            </div>
            <div class="textoBusqueda">
                <input type="text" name="txtBuscar" id="txtBuscar" placeholder="Buscar" class="w90por">
                <button type="button" class="boton3" id="btnAceptItems">Aceptar</button>
            </div>
            <div class="tablaBusqueda">
                <table class="tablaWrap" id="despachos">
                    <thead>
                        <tr class="stickytop" >
                            <th>Despacho</th>
                            <th>Fecha</th>
                            <th>Origen</th>
                            <th>Destino</th>
                            <th>Costos/Proyecto</th>
                            <th>Año</th>
                            <th>Guia</th>
                            <th>RS</th>
                            <th>Orden</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="modal" id="transferencias">
        <div class="ventanaBusqueda w75por">
            <div class="tituloVentana">
                <span id="tituloBusqueda">Notas de Transferencia</span>
                <div>
                    <a href="#" id="closeSearchTrans"><i class="fas fa-window-close"></i></a>
                </div>
            </div>
            <div class="textoBusqueda">
                <input type="text" name="txtBuscarTrans" id="txtBuscarTrans" placeholder="Buscar" class="w90por">
                <button type="button" class="boton3" id="btnAceptItemsTrans">Aceptar</button>
            </div>
            <div class="tablaBusqueda">
                <table class="tablaWrap" id="transferencias">
                    <thead>
                        <tr class="stickytop" >
                            <th>Nro</th>
                            <th>Guia</th>
                            <th>Fecha</th>
                            <th>Origen</th>
                            <th>Destino</th>
                            <th>Costos/Proyecto Origen</th>
                            <th>Año</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="modal" id="archivos">
        <div class="wrap_modal">
            <div class="upload-adjuntos-remitos">
            <!-- Header -->
                <div class="upload-header">
                    <h3><i class="fas fa-paperclip"></i> Adjuntar Archivos</h3>
                    <button class="modal-close" id="cerrarModal">&times;</button>
                </div>
            <!-- Body con dos columnas -->
                <div class="upload-body modal-split-layout" id="modalSplitLayout">
                
                    <!-- ===== COLUMNA IZQUIERDA (TU CONTENIDO ACTUAL) ===== -->
                    <div class="columna-izquierda">
                        <!-- ===== ARCHIVOS YA SUBIDOS ===== -->
                        <div class="archivos-subidos">
                            <div class="subtitulo">
                                <i class="fas fa-check-circle" style="color:#34a853;"></i>
                                Archivos Adjuntos (<span id="contadorAdjuntos">0</span>)
                            </div>
                            <div class="lista-subidos" id="listaSubidos">
                                <div class="mensaje-vacio">No hay archivos adjuntos</div>
                            </div>
                        </div>

                        <!-- ===== SEPARADOR ===== -->
                        <div class="separador">
                            <span>Agregar nuevos archivos</span>
                        </div>

                        <!-- Drop Zone -->
                        <div class="drop-zone" id="dropZone">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p>Arrastra tus archivos aquí o haz clic para seleccionar</p>
                            <span class="drop-zone-text">PDF, JPG, PNG, DOC (max 4MB)</span>
                            <input type="file" id="fileInput" multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                        </div>

                        <!-- Lista de archivos temporales -->
                        <div class="lista-archivos" id="listaArchivos"></div>
                    </div>

                    <!-- ===== COLUMNA DERECHA (EL VISOR) ===== -->
                    <div class="columna-derecha" id="panelVisor">
                        <div class="visor-header">
                            <span id="nombreVisor" class="texto-truncado">Visualizando archivo...</span>
                            <button type="button" id="cerrarVisor" class="btn-cerrar-visor">&times;</button>
                        </div>
                        <div class="visor-body">
                            <!-- Iframe para PDFs -->
                            <iframe id="iframeVisor" src="" style="width:100%; height:100%; border:none; display:none;"></iframe>
                            <!-- Imagen para JPG/PNG -->
                            <img id="imgVisor" src="" style="max-width:100%; max-height:100%; object-fit:contain; display:none;">
                            <!-- Mensaje para otros formatos -->
                            <div id="mensajeNoSoportado" style="display:none; text-align:center; padding:20px; color:#666;">
                                    <i class="fas fa-file-alt" style="font-size:3rem; color:#ccc; margin-bottom:10px;"></i>
                                    <p>Este tipo de archivo no se puede previsualizar.</p>
                                    <button class="btn btn-primary" id="btnDescargarVisor" style="margin-top:10px;">
                                        <i class="fas fa-download"></i> Descargar archivo
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Footer -->
                    <div class="upload-footer">
                    <button class="btn btn-secondary" id="cancelarModal">Cancelar</button>
                    <button class="btn btn-primary" id="subirArchivos">
                        <i class="fas fa-upload"></i> Subir Archivos
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal" id="vistaCertificado">
        <div class="ventanaVistaPrevia">
            <div class="tituloVista">
                <h3>Vista Previa</h3>
                <a href="#" id="closePreview" title="Cerrar Ventana"><i class="fas fa-window-close"></i></a>
            </div>
            <iframe src=""></iframe>
        </div>
    </div>
    <div class="modal" id="vistaAdjuntos">
        <div class="ventanaAdjuntos">
            <div class="tituloAdjuntos">
                <h3>Adjuntos</h3>
                <a href="#" id="closeAtach" title="Cerrar Ventana"><i class="fas fa-window-close"></i></a>
            </div>
            <ul id="listaAdjuntos">

            </ul>
            <iframe src=""></iframe>
        </div>
    </div>
    <div class="modal" id="pregunta">
        <div class="ventanaPregunta">
            <h3>¿ Registrar los items ?</h3>
            <div>
                <button type="button" id="btnAceptarPregunta">Si</button>
                <button type="button" id="btnCancelarPregunta">Cancelar</button>
            </div>
        </div>
    </div>
    <div class="cabezaModulo">
        <h1>Registro Almacen</h1>
        <div>
            <a href="#" id="nuevoRegistro"><i class="far fa-file"></i><p>Nuevo</p></a>
            <a href="#" id="irInicio"><i class="fas fa-home"></i><p>Inicio</p></a>
        </div>
    </div>
    <div class="barraTrabajo">
        <form action="#" id="formConsulta">
            <div class="filtros-container">
                    <div class="filtro-grupo">
                        <label for="tipo">Guia N°. </label>
                        <input type="text" id="guiaSearch" name="guiaSearch" class="form-control">
                    </div>
                    <div class="filtro-grupo">
                        <label for="costosSearch">Centro de Costos: </label>
                        <select name="costosSearch" id="costosSearch" class="form-control">
                            <?php echo $this->listaCostosSelect ?>
                        </select>
                    </div>
                    <div class="filtro-grupo">
                        <label for="mes">Mes</label>
                        <select name="mesSearch" id="mesSearch" class="form-control">
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
                    <div class="filtro-grupo">
                        <label for="anio">Año :</label>
                        <input type="number" name="anioSearch" id="anioSearch" value="<?php echo date("Y")?>" class="form-control">
                    </div>
                    <div class="filtro-grupo">
                        <label for="nota">Nro. Nota :</label>
                        <input type="number" name="notaSearch" id="notaSearch" class="form-control">
                    </div>

                    <div class="filtro-accion">
                        <button type="button" class="btn-consultar" id="btnConsulta">Consultar</button>
                    </div>
            </div>
        </form>
    </div>
    <div class="itemsTabla">
        <table id="tablaPrincipal">
            <thead class="stickytop">
                <tr>
                    <th>N°. Nota</th>
                    <th width="10%">F.Emisión</th>
                    <th>Almacen Origen</th>
                    <th>Almacen Destino</th>
                    <th width="30%">Centro de Costos</th>
                    <th width="10%">Guia Remision</th>
                    <th width="5%">R.S.</th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table>
    </div>
    <div class="paginador_cp">
        <div class="pagination">
        
        </div>
    </div>
    <script src="<?php echo constant('URL');?>public/js/jquery.js"></script>
    <script src="<?php echo constant('URL');?>public/js/funciones.js?<?php echo constant('VERSION')?>"></script>
    <script src="<?php echo constant('URL');?>public/js/registros.js?<?php echo constant('VERSION')?>"></script>
</body>
</html>