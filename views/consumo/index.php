<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
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
                <button type="button" id="btnAceptarDialogo">Aceptar</button>
            </div>
        </div>
    </div>
    <div class="cabezaModulo">
        <h1>Consumos</h1>
        <div>
            <a href="#" id="irInicio"><i class="fas fa-home"></i><p>Inicio</p></a>
        </div>
    </div>
    <div class="barraTrabajo">
        <form action="#" id="formConsulta">
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
                    <button type="button" class="boton3" id="btnFirma">Firmar</button>
            </div>
            <input type="text" name="codeRead" id="codeRead" style="opacity:1" value="B110700090010"> 
        </form>
    </div>
    <div class="itemsTabla">
        <table id="tablaPrincipal">
            <thead>
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
                    <th>Estado</th>
                    <th>...</th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table>
    </div>
    <script src="<?php echo constant('URL');?>public/js/jquery.js"></script>
    <script src="<?php echo constant('URL');?>public/js/funciones.js?<?php echo constant('VERSION')?>"></script>
    <script src="<?php echo constant('URL');?>public/js/consumo.js?<?php echo constant('VERSION')?>"></script>
</body>
</html>