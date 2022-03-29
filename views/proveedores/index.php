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
        <div class="ventanaProceso">
            <div class="cabezaProceso">
                <form action="#" id="formProceso" autocomplete="off">
                    <input type="hidden" name="codigo_entidad" id="codigo_entidad">
                    <input type="hidden" name="codigo_documento" id="codigo_documento">
                    <input type="hidden" name="codigo_tipo" id="codigo_tipo">
                    <input type="hidden" name="codigo_pais" id="codigo_pais" value="135">

                    <input type="hidden" name="activeTab" id="activeTab" value="tab1">
                    <div class="barraOpciones primeraBarra">
                        <span>Datos Generales</span>
                        <div>
                            <button type="button" id="saveItem" title="Grabar Datos">
                                <span><i class="far fa-save"></i> Grabar Registro</span> 
                            </button>
                            <button type="button" id="closeProcess" title="Cerrar">
                                <i class="fas fa-window-close"></i>
                            </button>
                        </div>
                    </div>
                
                <div class="dataProceso">
                    <div class="seccion_izquierda">
                        <div class="column2">
                            <label for="razon">Razón Social:</label>
                            <input type="text" name="razon" id="razon" class="mayusculas cerrarLista obligatorio">
                        </div>
                        <div class="column2">
                            <label for="tipo_ent">Tipo:</label>
                            <input type="text" name="tipo_ent" id="tipo_ent" class="mostrarLista obligatorio" placeholder="Seleccione una opcion">
                            <div class="lista" id="listaTipo">
                                <ul>
                                    <?php echo $this->listaTipos?>
                                </ul> 
                            </div>
                        </div>
                        <div class="column4">
                            <div class="column2">
                                <label for="tipo_doc">Tipo Doc.:</label>
                                <input type="text" name="tipo_doc" id="tipo_doc" class="mostrarLista obligatorio" placeholder="Seleccione una opcion">
                                <div class="lista" id="listaTipo">
                                    <ul>
                                        <?php echo $this->listaDocumentos?>
                                    </ul> 
                                </div>
                            </div>
                            <div class="column2">
                                <label for="nrodoc">Numero:</label>
                                <input type="text" name="nrodoc" id="nrodoc" class="mayusculas cerrarLista obligatorio">
                            </div>
                        </div>
                    </div>
                    <div class="seccion_medio">
                        <div class="column2">
                            <label for="direccion">Direccion:</label>
                            <input type="text" name="direccion" id="direccion" class="mayusculas cerrarLista obligatorio">
                        </div>
                        <div class="column2">
                            <label for="correo">Email :</label>
                            <input type="text" name="correo" id="correo" class="minusculas cerrarLista obligatorio">
                        </div>
                        <div class="column4">
                            <div class="column2">
                                <label for="telefono">Teléfono:</label>
                                <input type="text" name="telefono" id="telefono" class="mayusculas cerrarLista obligatorio">
                            </div>
                            <div class="column2">
                                <label for="pais">Pais:</label>
                                <input type="text" name="pais" id="pais" class="mostrarLista obligatorio" placeholder="Seleccione una opcion">
                                <div class="lista" id="listaTipo">
                                    <ul>
                                        <?php echo $this->listaPais?>
                                    </ul> 
                                </div>
                            </div>
                        </div>  
                    </div>
                    <div class="seccion_derecha">
                        <div class="column4items">
                            <input type="radio" name="agente" id="percepcion" value="1">
                            <label for="percepcion">Agente Percepción</label>
                            <input type="radio" name="agente" id="retencion" value="2">
                            <label for="retencion">Agente Retención</label>
                        </div>
                        <div class="column2">
                            <label for="estado">Estado:</label>
                            <input type="text" name="estado" id="estado" class="mayusculas cerrarLista readonly">
                        </div>
                    </div>
                </div>
                </div>
            </form>
            <div class="barraOpciones">
                <span>Detalles</span>
                <button type="button" id="addItem" title="Añadir Item" class="cerrarLista">
                    <i class="far fa-plus-square"></i> Agregar
                </button>
            </div>
            <div class="pestanasUsuario">
                <ul class="tabs_labels">
                    <li><a href="#" class="seleccionado" data-tab="tab1">Contactos</a></li>
                    <li><a href="#" data-tab="tab2">Bancos</a></li>
                </ul>
                <div class="tab" id="tab1">
                    <table class="tabla" id="contactos">
                        <thead>
                            <tr>
                                <th>...</th>
                                <th>Item</th>
                                <th>Nombre</th>
                                <th>Telefono</th>
                                <th>Email</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="tab oculto" id="tab2">
                    <table class="tabla" id="bancos">
                        <thead>
                            <tr>
                                <th>...</th>
                                <th>Item</th>
                                <th>Nombre Banco</th>
                                <th>Tipo Cuenta</th>
                                <th>Nro. Cuenta</th>
                                <th>Activo</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!--Ventana Princpal-->
    <div class="cabezaModulo">
        <h2>Catalogo Proveedores</h2>
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
        <table id="tablaPrincipal tabla7columnas">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Nombre</th>
                    <th>Dirección</th>
                    <th>Correo</th>
                    <th>Estado</th>
                    <th>...</th>
                    <th>...</th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table>
    </div>
    <script src="<?php echo constant('URL');?>public/js/jquery.js"></script>
    <script src="<?php echo constant('URL');?>public/js/funciones.js"></script>
    <script src="<?php echo constant('URL');?>public/js/proveedores.js?<?php echo constant('VERSION')?>"></script>
</body>
</html>