<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo constant('URL'); ?>public/css/minimos.css">
    <link rel="stylesheet" href="<?php echo constant('URL'); ?>public/css/modalcards.css">
    <link rel="stylesheet" href="<?php echo constant('URL'); ?>public/css/clases.css">
    <title>Document</title>
</head>
<body>
    <div class="mensaje">
        <p></p>
    </div>
    <div class="modal" id="esperar">
    </div>
    <div class="modal" id="pregunta">
        <div class="ventanaPregunta">
            <h3>Desea eliminar el registro?</h3>
            <div>
                <button type="button" id="btnAceptarPregunta">Aceptar</button>
                <button type="button" id="btnCancelarPregunta">Cancelar</button>
            </div>
        </div>
    </div>
   
    <!-- ============================================= -->
    <!-- MODAL PARA AGREGAR/EDITAR -->
    <!-- ============================================= -->
    <div class="modal" id="proceso">
        <div class="modalWrap">
            <div class="dialog_register">
               <div class="modal-header-register">
                    <h2 id="modalTitulo">
                        <i class="fas fa-plus-circle"></i> Agregar Clase
                    </h2>
                    <button class="cerrar" onclick="cerrarModal()">&times;</button>
                </div>
                <form id="formClase" onsubmit="guardarClase(event)">
                    <!-- Campo oculto para ID en edición -->
                    <input type="hidden" id="editId" value="">
                    <input type="hidden" name="codgrupo" id="codgrupo">
                    <input type="hidden" name="codclase" id="codclase">
                    
                    <!-- Grupo -->
                    <label class="obligatorio">Grupo</label>
                    <select id="grupoSelect" required>
                        <option value="">Seleccione...</option>
                    </select>
                    
                    <!-- CC (Código de Categoría) -->
                    <label class="obligatorio">CG (Código de Grupo)</label>
                    <input type="text" id="ccInput" placeholder="Ej: B01" readOnly>
                    <span class="helper">Código de la categoría (ej: B01, B02, etc.)</span>
                    
                    <!-- Código Clase -->
                    <label class="obligatorio">Código Clase</label>
                    <input type="text" id="codigoInput" placeholder="Ej: B0104" required oninput="validarCodigo(this)">
                    <span class="helper">Formato: Letra + 4 dígitos (ej: B0104)</span>
                    
                    <!-- Nombre Clase -->
                    <label class="obligatorio">Nombre</label>
                    <input type="text" id="nombreInput" placeholder="Nombre de la clase" required>
                    
                    <!-- Vista previa -->
                    <div style="background:#f0f7ff; border:2px dashed #2a7de1; border-radius:8px; padding:8px 15px; margin:10px 0;">
                        <i class="fas fa-code" style="color:#2a7de1;"></i>
                        <span style="font-weight:700;font-family:monospace;">
                            <span id="previewGrupo">B??</span> - <span id="previewCodigo">XXXX</span>
                        </span>
                    </div>
                    
                    <!-- Botones -->
                    <div class="acciones">
                        <button type="button" class="btn btn-danger" id="btnEliminar" style="display:none;" onclick="eliminarClase()">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                        <button type="button" class="btn btn-cancel" onclick="cerrarModal()">
                            Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary" id="btnGuardar">
                            <i class="fas fa-save"></i> <span id="labelGuardar">Guardar</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
    </div>
    <div class="cabezaModulo">
        <h1>Catálogo Clases</h1>
        <div>
            <a href="#" id="nuevoRegistro"><i class="far fa-file"></i><p>Nuevo</p></a>
            <a href="#" id="irInicio"><i class="fas fa-home"></i><p>Inicio</p></a>
        </div>
    </div>
    <div class="barraTrabajo">
        <div class="unaConsulta">
            <label for="consulta">Nombre : </label>
            <input type="text" name="consulta" id="consulta">
        </div>
    </div>
    <div class="itemsTabla">
        <table id="tablaPrincipal" class="table-container">
            <thead class="stickytop">
                <tr>
                    <th width="10%">Codigo</th>
                    <th>Denominación</th>
                    <th width="3%">...</th>
                </tr>
            </thead>
            <tbody id="clasesTbody">
                <tr>
                    <td colspan="3" style="text-align:center; padding:30px; color:#999;">
                        <i class="fas fa-inbox" style="font-size:40px; display:block; margin-bottom:10px;"></i>
                        No se encontraron resultados
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <!-- PAGINADOR -->
     <div class="paginador">
        <div class="info" id="infoPaginador">Mostrando <strong>0</strong> - <strong>0</strong> de <strong>0</strong></div>
        <div class="botones" id="botonesPaginador"></div>
    </div>
    <!-- SCRIPTS -->
    <script src="<?php echo constant('URL');?>public/js/jquery.js"></script>
    <script src="<?php echo constant('URL');?>public/js/funciones.js"></script>
    <script src="<?php echo constant('URL');?>public/js/clases.js?<?php echo constant('VERSION')?>"></script>
</body>
</html>