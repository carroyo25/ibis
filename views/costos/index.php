<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<div class="modal" id="proceso">
        <div class="ventanaProceso w35por">
            <div class="cabezaProceso">
                <form action="#" autocomplete="off" id="formProceso">
                    <input type="hidden" name="ubigeo" id="ubigeo">
                    <input type="hidden" name="codproy" id="codproy">
                    <div class="barraOpciones">
                        <span>Datos Generales</span>
                        <div>
                            <button type="button" id="grabarItem" title="Grabar Datos">
                                <span><i class="far fa-save"></i> Grabar Registro</span> 
                            </button>
                            <button type="button" id="cancelarItem" title="Cancelar">
                                <i class="fas fa-ban"></i> Cancelar Registro
                            </button>
                            <button type="button" id="cerrarVentana" title="Cerrar">
                                <i class="fas fa-window-close"></i>
                            </button>
                        </div>
                    </div>
                    <div class="dataProceso direccion_columna">
                        <div class="seccion_izquierda">
                            <div class="column2">
                                <label for="clave">codigo. :</label>
                                <input type="text" name="codigo" id="codigo" class="mayusculas obligatorio" autocomplete="off">
                                <label for="clave">Nombre. :</label>
                                <input type="text" name="descripcion" id="descripcion" class="mayusculas obligatorio" autocomplete="off">
                            </div>
                        </div>
                        <div class="barraOpciones">
                            <span>Datos Proyecyo</span>
                        </div>
                        <div class="seccion_medio">
                            <div class="column2">
                                <label for="abreviatura">Abreviado :</label>
                                <input type="text" name="abreviatura" id="abreviatura" class="cerrarLista mayusculas">
                            </div>
                        </div>
                        <div class="seccion_derecha">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="cabezaModulo">
        <h1>Centro de Costos</h1>
        <div>
            <a href="#" id="nuevoRegistro"><i class="far fa-file"></i></a>
            <a href="#" id="irInicio"><i class="fas fa-home"></i></a>
        </div>
    </div>
    <div class="barraTrabajo">
        <div class="unaConsulta">
            <label for="consulta">Nombre : </label>
            <input type="text" name="consulta" id="consulta">
        </div>
    </div>
    <div class="itemsTabla">
        <table id="tablaPrincipal" class="tabla4columnas">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Codigo</th>
                    <th>Denominación</th>
                    <th>...</th>
                </tr>
            </thead>
            <tbody>
                <?php echo $this->listaCostos;?>
            </tbody>
        </table>
    </div>
    <script src="<?php echo constant('URL');?>public/js/jquery.js"></script>
    <script src="<?php echo constant('URL');?>public/js/funciones.js"></script>
    <script src="<?php echo constant('URL');?>public/js/costos.js?<?php echo constant('VERSION')?>"></script>
</body>
</html>