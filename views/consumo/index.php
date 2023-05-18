<!DOCTYPE html>
<html lang="es" sigplusextliteextension-installed="true" sigwebext-installed="true">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
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
    <div class="modal" id="pregunta">
        <div class="ventanaPregunta">
            <h3>¿Datos Correctos?</h3>
            <div>
                <button type="button" id="btnAceptarGrabar">Aceptar</button>
                <button type="button" id="btnCancelarGrabar">Cancelar</button>
            </div>
        </div>
    </div>
    <div class="modal" id="borrarFila">
        <div class="ventanaPregunta">
            <h3>¿Borrar la fila?</h3>
            <div>
                <button type="button" id="btnAceptarBorrar">Aceptar</button>
                <button type="button" id="btnCancelarBorrar">Cancelar</button>
            </div>
        </div>
    </div>
    <div class="modal" id="dialogo">
        <div class="ventanaPregunta">
            <h3>Ingrese Codigo</h3>
            <div>
                <input type="text" name="codigoSearch" id="codigoSearch">
            </div>
            <div>
                <button type="button" id="btnAceptarDialogo">Aceptar</button>
                <button type="button" id="btnCancelarDialogo">Cancelar</button>
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
    <div class="cabezaModulo">
        <h1>Kardex Personal</h1>
        <div>
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
            <button id="btnGrabarKardex" class="oculto boton3">Aceptar</button>
            <button type="button" class="boton3" id="btnFirmar" onclick="StartSign()">Firmar</button>
            <div>
                <label for="codeRead">Codigo: </label>
                <input type="text" name="codeRead" id="codeRead" style="opacity:1;position:relative" value=""> 
            </div>
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
                    <th>Estado</th>
                    <th width="20px">Firma</th>
                    <th>...</th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table>
    </div>
    <canvas id="cnv" name="cnv" width="500" height="100" ></canvas>
    <canvas name="SigImg" id="SigImg" width="500" height="100" s></canvas>
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
