<!DOCTYPE html>
<html lang="es">
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
    <div class="modal" id="proceso">
        <div class="ventanaProceso w50por">
            <div class="cabezaProceso">
                <form action="" autocomplete="off" id="formProceso">
                    <input type="hidden" name="codigo_item" id="codigo_item">
                    <input type="hidden" name="codigo_tipo" id="codigo_tipo">
                    <input type="hidden" name="codigo_grupo" id="codigo_grupo">
                    <input type="hidden" name="codigo_clase" id="codigo_clase">
                    <input type="hidden" name="codigo_familia" id="codigo_familia">
                    <input type="hidden" name="codigo_catalogo" id="codigo_catalogo">
                    <input type="hidden" name="codigo_unidad" id="codigo_unidad">
                    <input type="hidden" name="tipofoto" id="tipofoto">

                    <input type="file" id="image_product" name="image_product" multiple class="oculto" accept="image/jpg">

                    <div class="barraOpciones primeraBarra">
                        <span>Datos Generales</span>
                        <div>
                            <button type="button" id="grabarItem" title="Grabar Datos">
                                <p><i class="far fa-save"></i> Grabar Registro</p> 
                            </button>
                            <button type="button" id="cerrarVentana" title="Cerrar">
                                <i class="fas fa-window-close"></i>
                            </button>
                        </div>
                    </div>
                    <div class="dataProceso">
                        <div class="seccion_izquierda">
                            <div class="column2">
                                <label for="codigo">Codigo :</label>
                                <input type="text" name="codigo" id="codigo" class="cerrarLista mayusculas cerrarLista w50por resaltado">
                            </div>
                            <div class="column2">
                                <label for="tipo_item">Tipo:</label>
                                <input type="text" name="tipo_item" id="tipo_item" class="cerrarLista mostrarLista w50por" placeholder="Seleccione una opcion">
                                <div class="lista" id="listaTipo">
                                    <ul>
                                        <?php echo $this->listaTipos?>
                                    </ul> 
                                </div>
                            </div>
                            <div class="column2">
                                <label for="grupo">Grupo :</label>
                                <input type="text" name="grupo" id="grupo" class="cerrarLista mostrarLista" placeholder="Seleccione una opcion">
                                <div class="lista" id="listaGrupo">
                                    <ul>
                                    </ul> 
                                </div>
                            </div>
                            <div class="column2">
                                <label for="clase">Clase:</label>
                                <input type="text" name="clase" id="clase" class="cerrarLista mostrarLista" placeholder="Seleccione una opcion">
                                <div class="lista" id="listaClase">
                                    <ul>
                                    </ul> 
                                </div>
                            </div>
                            <div class="column2">
                                <label for="familia">Familia:</label>
                                <input type="text" name="familia" id="familia" class="cerrarLista mostrarLista obligatorio" placeholder="Seleccione una opcion">
                                <div class="lista" id="listaFamilia">
                                    <ul>
                                    </ul> 
                                </div>
                            </div>
                            <div class="column2">
                                <label for="descripcion">Nombre :</label>
                                <input type="text" name="descripcion" id="descripcion" class="mayusculas cerrarLista obligatorio">
                            </div>
                            <div class="column2">
                                <label for="unidad">Unidad:</label>
                                <input type="text" name="unidad" id="unidad" class="cerrarLista mostrarLista w50por" placeholder="Seleccione una opcion">
                                <div class="lista" id="listaUnidad">
                                    <ul>
                                    </ul> 
                                </div>
                            </div>
                            <div class="column4_7_1">
                                <label for="nro_parte">Nro.Parte:</label>
                                <input type="text" name="nro_parte" id="nro_parte" class="mayusculas cerrarLista">
                                <label for="cod_pat">Codigo Patrimonial:</label>
                                <input type="text" name="cod_pat" id="cod_pat" class="mayusculas cerrarLista obligatorio">
                            </div>
                            <div class="column4_1">
                                <div>
                                    <input type="checkbox" name="serie" id="serie" value="1">
                                    <label for="serie">Registra Series</label>
                                </div>
                                <div>
                                    <input type="checkbox" name="detraccion" id="detraccion">
                                    <label for="detraccion">Afecto a detracción</label>
                                </div>
                                <div>
                                    <input type="checkbox" name="actfij" id="actfij">
                                    <label for="actfij">Activo Fijo</label>
                                </div>
                            </div>
                        </div>
                        <div class="seccion_derecha flex_center">
                            <img src="public/img/noimagen.jpg" alt="" id="foto">
                        </div>
                    </div>
                    
                </form>
            </div>
        </div>
    </div>
    <div class="cabezaModulo">
        <h1>Catálogo Bienes/Servicios</h1>
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
        <table id="tablaPrincipal">
            <thead>
                <tr>
                    <th width="10%">Codigo</th>
                    <th>Tipo</th>
                    <th>Denominación</th>
                    <th>Unidad</th>
                    <th width="3%">...</th>
                </tr>
            </thead>
            <tbody>
                <?php echo $this->listaItems;?>
            </tbody>
        </table>
    </div>
    <script src="<?php echo constant('URL');?>public/js/jquery.js"></script>
    <script src="<?php echo constant('URL');?>public/js/funciones.js?<?php echo constant('VERSION')?>"></script>
    <script src="<?php echo constant('URL');?>public/js/bienes.js?<?php echo constant('VERSION')?>"></script>
</body>
</html>