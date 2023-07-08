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
            <h3>Desea culminar el pedido?</h3>
            <div>
                <button type="button" id="btnAceptarPregunta">Aceptar</button>
                <button type="button" id="btnCancelarPregunta">Cancelar</button>
            </div>
        </div>
    </div>
    <div class="modal" id="dialogo">
        <div class="ventanaDialogo">
            <h3>Clave del sistema</h3>
            <div>
                <span id="claveUsuario">Aca ira la clave</span>
            </div>
            <div>
                <button type="button" id="btnAceptarDialogo">Aceptar</button>
            </div>
        </div>
    </div>
    <div class="modal" id="proceso">
        <div class="ventanaProceso w65por">
            <div class="cabezaProceso">
                <form action="#" autocomplete="off" id="formProceso">
                    <input type="hidden" name="cod_user" id="cod_user">
                    <input type="hidden" name="cod_resp" id="cod_resp">
                    <input type="hidden" name="cod_niv" id="cod_niv">
                    <input type="hidden" name="cod_est" id="cod_est">
                    <input type="hidden" name="cod_cargo" id="cod_cargo">
                    <input type="hidden" name="old_pass" id="old_pass">
                    <div class="barraOpciones">
                        <span>Datos Generales</span>
                        <div>
                            <button type="button" id="saveItem" title="Grabar Datos">
                                <p><i class="far fa-save"></i> Grabar Registro</p> 
                            </button>
                            <button type="button" id="closeProcess" title="Cerrar">
                                <i class="fas fa-window-close"></i>
                            </button>
                        </div>
                    </div>
                    <div class="dataProceso">
                        <div class="seccion_izquierda">
                            <div class="column2">
                                <label for="usuario">Usuario :</label>
                                <input type="text" name="usuario" id="usuario" class="mayusculas cerrarLista obligatorio">
                            </div>
                            <div  class="column2">
                                <label for="clave">Clave. :</label>
                                <input type="password" name="clave" id="clave" class="mayusculas cerrarLista obligatorio" autocomplete="off">
                            </div>
                            <div class="column2">
                                <label for="correo"">Correo :</label>
                                <input type="mail" name="correo" id="correo" class="minusculas cerrarLista obligatorio">
                            </div>
                        </div>
                        <div class="seccion_medio">
                            <div class="column2">
                                <label for="nombre">Nombre :</label>
                                <input type="text" name="nombre" id="nombre" class="mostrarLista obligatorio" placeholder="Seleccione una opcion">
                                <div class="lista" id="listaTrabajadores">
                                   <ul>
                                       <?php echo $this->listaAquarius?>
                                   </ul> 
                                </div>
                            </div>
                            <div class="column2">
                                <label for="cargo">Cargo :</label>
                                <input type="text" name="cargo" id="cargo" class="mostrarLista" readonly>
                            </div>
                            <div class="column4">
                                <div class="column2">
                                    <label for="nivel">Nivel</label>
                                    <input type="text" name="nivel" id="nivel" class="mostrarLista obligatorio" placeholder="Seleccione una opcion">
                                    <div class="lista" id="listaNivel">
                                        <ul>
                                            <?php echo $this->listaNivel?>
                                        </ul> 
                                    </div>
                                </div>
                                <div class="column2">
                                    <label for="estado" >Estado : </label>
                                    <input type="text" name="estado" id="estado" class="mostrarLista obligatorio" placeholder="Seleccione una opcion">
                                    <div class="lista" id="listaEstado">
                                        <ul>
                                            <?php echo $this->listaEstado?>
                                        </ul> 
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="seccion_derecha">
                            <div class="column2">
                                <label for="user_inic">Iniciales</label>
                                <input type="text" name="user_inic" id="user_inic" maxlength="2" class="mayusculas cerrarLista obligatorio">
                            </div>
                            <div class="column2">
                                <label for="desde">Desde :</label>
                                <input type="date" name="desde" id="desde" class="cerrarLista">
                            </div>
                            <div class="column2">
                                <label for="hasta">Hasta :</label>
                                <input type="date" name="hasta" id="hasta" class="cerrarLista">
                            </div>
                        </div>
                    </div>
                   <div class="barraOpciones">
                       <span>Detalles</span>
                       <button type="button" id="addItem" title="Añadir Item" class="cerrarLista">
                            <i class="far fa-plus-square"></i> Agregar
                        </button>
                   </div>
                    <div class="pestanasUsuario">
                        <ul class="tabs_labels">
                            <li><a href="#" class="seleccionado" data-tab="tab1">Modulos</a></li>
                            <li><a href="#" data-tab="tab2">Centro de Costos</a></li>
                            <li><a href="#" data-tab="tab3">Almacénes</a></li>
                            <li><a href="#" data-tab="tab4">Aprobación</a></li>
                        </ul>
                        <div class="tab" id="tab1">
                            <table class="tabla" id="modulos">
                                <thead>
                                    <tr>
                                        <th>...</th>
                                        <th>Item</th>
                                        <th>Módulo</th>
                                        <th>Agregar</th>
                                        <th>Modificar</th>
                                        <th>Eliminar</th>
                                        <th>Imprimir</th>
                                        <th>Procesar</th>
                                        <th>Visible</th>
                                        <th>Todos</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                        <div class="tab oculto" id="tab2">
                            <table class="tabla" id="costos">
                                <thead>
                                    <tr>
                                        <th class="con_borde w5p">...</th>
                                        <th class="con_borde"  width="5%">Codigo</th>
                                        <th class="con_borde">Descripcion</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                        <div class="tab oculto" id="tab3">
                            <table class="tabla" id="almacen">
                                <thead>
                                    <tr>
                                        <th class="con_borde w5p">...</th>
                                        <th class="con_borde" width="5%">Codigo</th>
                                        <th class="con_borde">Descripcion</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                        <div class="tab oculto" id="tab4">
                            <table class="tabla" id="aprobacion">
                                <thead>
                                    <tr>
                                        <th class="con_borde">...</th>
                                        <th class="con_borde">Item</th>
                                        <th class="con_borde">Codigo</th>
                                        <th class="con_borde">Módulo</th>
                                        <th class="con_borde">Nombre</th>
                                        <th class="con_borde">Correo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>
            </div>
            
        </div>
    </div>
    <div class="modal" id="busqueda">
        <div class="ventanaBusqueda w35por">
            <div class="tituloVentana">
                <span id="tituloBusqueda">Titulo Ventana</span>
                <div>
                    <a href="#"><i class="fas fa-window-close"></i></a>
                </div>
            </div>
            <div class="textoBusqueda">
                <input type="text" name="txtBuscar" id="txtBuscar" placeholder="Buscar">
            </div>
            <div class="tablaBusqueda">
                <table class="tabla " id="tablaModulos">
                    <thead >
                        <tr class="stickytop">
                            <th>Codigo</th>
                            <th>Descripcion</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- esta es la parte principal .. un bello error-->
    <div class="cabezaModulo">
        <h1>Administrar Usuarios</h1>
        <div>
            <a href="#" id="nuevoRegistro"><i class="far fa-file"></i><p>Nuevo</p></a>
            <a href="#" id="irInicio"><i class="fas fa-home"></i><p>Inicio</p></a>
        </div>
    </div>
    <div class="barraTrabajo">
        <div class="unaConsulta">
            <label for="consulta">Usuario</label>
            <input type="text" name="consulta" id="consulta">
        </div>
    </div>
    <div class="itemsTabla">
        <table id="tablaPrincipal">
            <thead>
                <tr>
                    <th>Item</th>
                    <th data-idcol="1" class="datafiltro">Usuario</th>
                    <th data-idcol="2" class="datafiltro">Nombres</th>
                    <th>Nivel</th>
                    <th>Estado</th>
                    <th>Desde</th>
                    <th>Hasta</th>
                    <th>...</th>
                    <th>...</th>
                    <th>...</th>
                </tr>
            </thead>
            <tbody>
                <?php echo $this->listaUsuarios;?>
            </tbody>
        </table>
    </div>
    <script src="<?php echo constant('URL');?>public/js/jquery.js"></script>
    <script src="<?php echo constant('URL');?>public/js/funciones.js"></script>
    <script src="<?php echo constant('URL');?>public/js/usuarios.js?<?php echo constant('VERSION')?>"></script>
</body>
</html>