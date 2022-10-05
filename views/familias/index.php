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
        <div class="ventanaProceso w35por">
            <div class="cabezaProceso">
                <form action="#" autocomplete="off" id="formProceso">
                    <input type="hidden" name="codgrupo" id="codgrupo">
                    <input type="hidden" name="codclase" id="codclase">
                    <input type="hidden" name="codfamilia" id="codfamilia">
                    <input type="hidden" name="codigo_clase_catalogo" id="codigo_clase_catalogo">

                    <div class="barraOpciones primeraBarra">
                        <span>Datos del grupo</span>
                        <div>
                            <button type="button" id="grabarItem" title="Grabar Datos">
                                <p><i class="far fa-save"></i> Grabar Registro</p> 
                            </button>
                            
                            <button type="button" id="cerrarVentana" title="Cerrar">
                                <i class="fas fa-window-close"></i>
                            </button>
                        </div>
                    </div>
                    <div class="dataProceso_flex_columna">
                        <div class="seccion_izquierda">
                            <div class="column2_9">
                                <label for="clase">Grupo:</label>
                                <input type="text" name="grupo" id="grupo" class="mostrarLista obligatorio busqueda" placeholder="Seleccione una opcion">
                                <div class="lista" id="listaGrupo">
                                    <ul>
                                        <?php echo $this->listaGrupo?>
                                    </ul> 
                                </div>
                            </div>
                        </div>
                        <div class="barraOpciones">
                            <span>Datos de la Clase</span>
                        </div>
                        <div class="seccion_derecha">
                            <div class="column2_9">
                                <label for="clave">Clase:</label>
                                <input type="text" name="clase" id="clase" class="mostrarLista obligatorio busqueda" placeholder="Seleccione una opcion">
                                <div class="lista" id="listaClase">
                                    <ul>
                                        
                                    </ul> 
                                </div>
                            </div>
                        </div>
                        <div class="barraOpciones">
                            <span>Datos de la Familia</span>
                        </div>
                        <div class="seccion_derecha entradaDatos">
                            <div class="column2_9">
                                <label for="codigo">Codigo familia:</label>
                                <input type="text" name="codigo" id="codigo" class="mayusculas obligatorio" autocomplete="off">
                                <label for="clave">Nombre familia:</label>
                                <input type="text" name="descripcion" id="descripcion" class="mayusculas obligatorio" autocomplete="off">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="cabezaModulo">
        <h1>Catálogo Familias</h1>
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
        <table id="tablaPrincipal">
            <thead>
                <tr>
                    <th width="10%">Codigo</th>
                    <th>Denominación</th>
                    <th width="3%">...</th>
                </tr>
            </thead>
            <tbody>
                <?php echo $this->listaFamilias;?>
            </tbody>
        </table>
    </div>
    <script src="<?php echo constant('URL');?>public/js/jquery.js"></script>
    <script src="<?php echo constant('URL');?>public/js/funciones.js"></script>
    <script src="<?php echo constant('URL');?>public/js/familias.js?<?php echo constant('VERSION')?>"></script>
</body>
</html>