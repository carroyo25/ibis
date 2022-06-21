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
        <div class="ventanaProceso w25por">
            <div class="cabezaProceso">
                <form action="#" autocomplete="off" id="formProceso">
                    <input type="hidden" name="codgrupo" id="codgrupo">
                    <input type="hidden" name="codclase" id="codclase">
                    
                    <div class="barraOpciones primeraBarra">
                        <span>Datos del grupo</span>
                        <div>
                            <button type="button" id="grabarItem" title="Grabar Datos">
                                <span><i class="far fa-save"></i> Grabar Registro</span> 
                            </button>
                            
                            <button type="button" id="cerrarVentana" title="Cerrar">
                                <i class="fas fa-window-close"></i>
                            </button>
                        </div>
                    </div>
                    <div class="dataProceso direccion_columna">
                        <div class="seccion_izquierda">
                            <div class="column2_9">
                                <label for="clase">Grupo:</label>
                                <input type="text" name="grupo" id="grupo" class="mostrarLista obligatorio busqueda" placeholder="Seleccione una opcion">
                                <div class="lista" id="listaClase">
                                    <ul>
                                        <?php echo $this->listaGrupos?>
                                    </ul> 
                                </div>
                            </div>
                        </div>
                        <div class="barraOpciones">
                            <span>Datos de la Clase</span>
                        </div>
                        <div class="seccion_derecha">
                        <div class="column2_9">
                                <label for="codigo">Codigo Clase:</label>
                                <input type="text" name="codigo" id="codigo" class="mayusculas obligatorio" autocomplete="off">
                                <label for="clave">Nombre Clase:</label>
                                <input type="text" name="descripcion" id="descripcion" class="mayusculas obligatorio" autocomplete="off">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="cabezaModulo">
        <h1>Catálogo Clases</h1>
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
                    <th>Denominación</th>
                    <th width="3%">...</th>
                </tr>
            </thead>
            <tbody>
                <?php echo $this->listaClases;?>
            </tbody>
        </table>
    </div>
    <script src="<?php echo constant('URL');?>public/js/jquery.js"></script>
    <script src="<?php echo constant('URL');?>public/js/funciones.js"></script>
    <script src="<?php echo constant('URL');?>public/js/clases.js?<?php echo constant('VERSION')?>"></script>
</body>
</html>