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
    <div class="modal" id="vistaprevia">
        <div class="ventanaVistaPrevia">
            <div class="tituloVista">
                <h3>Vista Previa</h3>
                <a href="#" id="closePreview" title="Cerrar Ventana"><i class="fas fa-window-close"></i></a>
            </div>
            <iframe src=""></iframe>
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
    <div class="cabezaModulo">
        <h1>Libre Adeudo</h1>
        <div>
            <a href="#" id="btnAdeudo"><i class="far fa-file-alt"></i><p>Libre Adeudo</p></a>
            <a href="#" id="irInicio"><i class="fas fa-home"></i><p>Inicio</p></a>
        </div>
    </div>
    <input type="text" name="codeRead" id="codeRead" style="opacity:0;position:fixed" value=""> 
    <div class="barraTrabajo">
            <div class="variasConsultas4campos">
                    <input type="hidden" name="cut" id="cut">
                    <input type="hidden" name="correo" id="correo">
                    
                    <div>
                        <label for="costosSearch">Centro de Costos: </label>
                        <select name="costosSearch" id="costosSearch" class="item4">
                            <?php echo $this->listaCostosSelect ?>
                        </select>
                    </div>
                    <div>
                        <label for="docident">N°. Documento </label>
                        <input type="text" id="docident" name="docident">
                    </div>
                    <div>
                        <label for="nombre">Nombre</label>
                        <input type="text" id="nombre" name="nombre">
                    </div>
                    <div>
                        <label for="cargo">Cargo</label>
                        <input type="cargo" id="cargo" name="cargo">
                    </div>
                    <button id="btnGrabarKardex" class="boton3">Aceptar</button>
                    <button type="button" class="boton3" id="btnFirmar" onclick="StartSign()">Firmar</button>
            </div>
    </div>
    <div class="itemsTabla">
        <table id="tablaPrincipal">
            <thead class="stikystop">
                <tr>
                    <th>Item</th>
                    <th>Codigo</th>
                    <th>Descripcion</th>
                    <th>UND.</th>
                    <th>Cant.</th>
                    <th width="10px">Cant.</br>Devolucion</th>
                    <th>Fecha</br>Salida</th>
                    <th>Fecha</br>Devolucion</th>
                    <th>N° Hoja</th>
                    <th>Isometricos</th>
                    <th>Observaciones</th>
                    <th>Serie</th>
                    <th>Patrimonio</th>
                    <th>Estado</br>Devolucion</th>
                    <th width="20px">Firma</th>
                    <th width="20px">Firma Almacen</th>
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
    <script src="<?php echo constant('URL');?>public/js/adeudo.js?<?php echo constant('VERSION')?>"></script>
</body>
</html>
