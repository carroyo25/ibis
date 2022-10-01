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
                    <input type="hidden" name="ubigeo" id="ubigeo">
                    <input type="hidden" name="codigo" id="codigo">
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
                    <div class="dataProceso_flex direccion_columna">
                        <div class="seccion_izquierda">
                            <div  class="column2">
                                <label for="clave">Nombre. :</label>
                                <input type="text" name="descripcion" id="descripcion" class="mayusculas cerrarLista obligatorio" autocomplete="off">
                            </div>
                        </div>
                        <div class="barraOpciones">
                            <span>Dirección</span>
                        </div>
                        <div class="seccion_medio">
                            <div class="column2">
                                <label for="vtipo">Via Tipo :</label>
                                <input type="text" name="vtipo" id="vtipo" class="cerrarLista mayusculas">
                            </div>
                            <div class="column2">
                                <label for="vnombre">Via Nombre :</label>
                                <input type="text" name="vnombre" id="vnombre" class="cerrarLista mayusculas">
                            </div>
                            <div class="column2">
                                <label for="numero">Nro. :</label>
                                <input type="text" name="numero" id="numero" class="cerrarLista mayusculas">
                            </div>
                            <div class="column2">
                                <label for="zona">Zona :</label>
                                <input type="text" name="zona" id="zona" class="cerrarLista mayusculas">
                            </div>
                        </div>
                        <div class="barraOpciones">
                            <span>Ubicación</span>
                        </div>
                        <div class="seccion_derecha">
                            <div class="column2">
                                <label for="dpto">Dpto. :</label>
                                <input type="text" name="dpto" id="dpto" class="mostrarLista">
                                <div class="lista" id="listaDepartamento">
                                    <ul>
                                        <?php echo $this->listaDepartamento?>
                                    </ul> 
                                </div>
                            </div>
                            <div class="column2">
                                <label for="prov">Prov. :</label>
                                <input type="text" name="prov" id="prov" class="mostrarLista">
                                <div class="lista" id="listaProvincia">
                                    <ul id="provincias">
                                        
                                    </ul> 
                                </div>
                            </div>
                            <div class="column2">
                                <label for="dist">Dist. :</label>
                                <input type="text" name="dist" id="dist" class="mostrarLista">
                                <div class="lista" id="listaDistrito">
                                    <ul id="distritos">
                                        
                                    </ul> 
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            
        </div>
    </div>
    <div class="cabezaModulo">
        <h1>Catalogo Almacénes</h1>
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
        <table id="tablaPrincipal" class="tabla3columnas">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Denominación</th>
                    <th>...</th>
                </tr>
            </thead>
            <tbody>
                <?php echo $this->listaAlmacenes;?>
            </tbody>
        </table>
    </div>
    <script src="<?php echo constant('URL');?>public/js/jquery.js"></script>
    <script src="<?php echo constant('URL');?>public/js/funciones.js"></script>
    <script src="<?php echo constant('URL');?>public/js/almacen.js?"></script>
</body>
</html>