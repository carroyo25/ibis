<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div class="modal" id="esperar">
        <div class="loadingio-spinner-spinner-5ulcsi06hlf">
            <div class="ldio-fifgg00y5y">
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
            </div>
        </div>
    </div>
    <div class="modal" id="dialogo_registro">
        <div class="ventanaConsumo">
            <input type="hidden" name="idmmtto" id="idmmtto">
            <input type="hidden" name="idlastmmtto" id="idlastmmtto">
            
            <div class="titulo_dialogo">
                <h3>Registrar Mantenimiento</h3>
                <a href="#" id="sendNotify" title="Enviar Programación"><i class="far fa-envelope"></i><p>Notificar</p></a>
            </div>
            <div class="contenedor">
                <div class="cabecera_dialogo">
                    <label for="serie">Serie</label>
                    <input type="text" name="serie" id="serie" readonly>
                    <label for="descripcion">Descripcion</label>
                    <input type="text" name="descripcion" id="descripcion" readonly>
                </div>
                <div class="tabla_dialogo">
                    <table id="tabla_detalles_mttos" class="tabla">
                        <thead class="stickytop">
                            <tr>
                                <th>Fecha<br>Mantenimiento</th>
                                <th>Observaciones</th>
                                <th>Técnico</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
                <div class="cuerpo_dialogo">
                    <div class="datos_cuerpo">
                        <label for="fecha_sugerida">Fecha Sugerida</label>
                        <input type="text" name="fecha_sugerida" id="fecha_sugerida" readonly>
                        <label for="fecha_mmto">Fecha Mtto</label>
                        <input type="date" name="fecha_mmto" id="fecha_mmto">
                        <label for="usuario">Usuario</label>
                        <input type="text" name="usuario" id="usuario">
                        <label for="correo_usuario">Correo</label>
                        <input type="mail" name="correo_usuario" id="correo_usuario">
                        <label for="tipo_mmtto">Mantenimiento</label>
                        <select name="tipo_mmtto" id="tipo_mmtto">
                            <option value="1">Mantenimiento Programado</option>
                            <option value="2">Mantenimiento Preventivo</option>
                            <option value="3">Mantenimiento Correctivo</option>
                        </select>
                    </div>
                    <div class="datos_cuerpo_observaciones">
                        <label for="observaciones_dialogo">Observaciones</label>
                        <textarea name="observaciones_dialogo" id="observaciones_dialogo"></textarea>
                    </div>
                    <br><br>
                    <div class="datos_cuerpo">
                        <label for="procesador">Procesador :</label>
                        <input type="text" name="procesador" id="procesador">
                        <label for="ram">Memoria RAM :</label>
                        <input type="text" name="ram" id="ram">
                        <label for="hdd">Disco Duro:</label>
                        <input type="text" name="hdd" id="hdd">
                        <label for="estado_equipo">Estado Equipo</label>
                        <select name="estado_equipo" id="estado_equipo">
                            <option value="1">Nuevo</option>
                            <option value="2" selected>Usado Nivel 1</option>
                            <option value="3">Usado Nivel 2</option>
                            <option value="4">Usado Nivel 3</option>
                            <option value="5">Inoperativo</option>
                            <option value="6">Obsoleto </option>
                            <option value="7">Inoperativo</option>
                        </select>
                    </div>
                    <div class="datos_cuerpo_observaciones">
                        <label for="otros">Especificaciones: </label>
                        <textarea name="otros" id="otros"></textarea>
                    </div>
                </div>
                <div class="opciones_dialogo">
                    <button type="button" id="btnAceptarDialogo">Aceptar</button>
                    <button type="button" id="btnCancelarDialogo">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal" id="cambio_fecha">
        <div class="ventanaPregunta">
            <h3>Fecha de Entrega</h3>
            <div>
                <input type="date" name="fecha_nueva" id="fecha_nueva">
            </div>
            <div>
                <button type="button" id="btnAceptarGrabar">Aceptar</button>
                <button type="button" id="btnCancelarGrabar">Cancelar</button>
            </div>
        </div>
    </div>
    <div class="cabezaModulo">
        <h1>Registro de Mantenimientos - TI</h1>
        <div>
            <a href="#" id="excelFile"><i class="fas fa-file-excel"></i><p>Exportar</p></a>
            <a href="#" id="irInicio"><i class="fas fa-home"></i><p>Inicio</p></a>
        </div>
    </div>
    <div class="barraTrabajo">
        <form action="#" id="formConsulta">
            <div class="variasConsultas4campos">
                    <div>
                        <label for="costosSearch">Centro Costos: </label>
                        <select name="costosSearch" id="costosSearch">
                            <?php echo $this->listaCostosSelect ?>
                        </select>
                    </div>
                    <div>
                        <label for="serieBusqueda">Serie : </label>
                        <input type="text" name="serieBusqueda" id="serieBusqueda">
                    </div>
                    <div>
                        <label for="usuarioBusqueda">Usuario: </label>
                        <input type="text" name="usuarioBusqueda" id="usuarioBusqueda">
                    </div>
                    <div>
                    </div>
                    <button type="button" id="btnConsulta" class="boton3">Consultar</button> 
            </div>
        </form>
    </div>
    <div class="itemsTabla">
        <table id="tablaPrincipal">
            <thead class="stickytop">
                <tr>
                    <th>Item</th>
                    <th>Descripcion</th>
                    <th>Usuario</th>
                    <th>Serie</th>
                    <th>Fecha Entrega</th>
                    <th>Centro Costos</th>
                    <th>1er MMTTO</th>
                    <th>Estado</th>
                    <th>2do MMTTO</th>
                    <th>Estado</th>
                    <th>3er MMTTO</th>
                    <th>Estado</th>
                    <th>4to MMTTO</th>
                    <th>Estado</th>
                    <th>...</th>
                </tr>
            </thead>
            <tbody>
                <?php   //if ( count($this->listarMantenimientos['datos']) > 0 ) {
                            $item = 1; 
                            foreach($this->listaMantenimientos['datos'] as $registro) { ?> 
                
                            <tr class="pointer click_tr" data-id="<?php echo $registro['idreg']; ?>" 
                                                data-correo="<?php foreach($this->listaMantenimientos['usuarios'] as $usuario ){if ( $usuario['dni'] == $registro['nrodoc'] ){ echo $usuario['correo'];}}?>"
                                                data-documento="<?php echo $registro['nrodoc']; ?>"
                                                data-costos="<?php echo $registro['nidreg']; ?>"
                                                data-serie="<?php echo $registro['cserie']; ?>"
                                                data-procesador="<?php echo $registro['cprocesador']; ?>"
                                                data-ram="<?php echo $registro['cram']; ?>"
                                                data-hdd="<?php echo $registro['chdd']; ?>"
                                                data-otros ="<?php echo $registro['totros']; ?>">
                                <td class="pl20px"><?php echo $item++; ?></td>
                                <td class="pl20px"><?php echo $registro['cdesprod']; ?></td>
                                <td class="pl20px"><?php foreach($this->listaMantenimientos['usuarios'] as $usuario ){if ( $usuario['dni'] == $registro['nrodoc'] ){ echo $usuario['usuario'];}}?></td>
                                <td class="pl20px"><?php echo $registro['cserie']; ?></td>
                                <td class="textoCentro"><?php echo $registro['fentrega']; ?></td>
                                <td class="textoCentro"><?php echo $registro['ccodproy']; ?></td>
                                <td class="textoCentro"><?php echo $registro['fmtto1']; ?></td>
                                <td class="textoCentro <?php echo $registro['est1'] == 0 ? 'semaforoNaranja':'semaforoVerde'; ?>"><?php echo $registro['est1'] == 0 ? 'Pendiente':'Realizado'; ?></td>
                                <td class="textoCentro"><?php echo $registro['fmtto2']; ?></td>
                                <td class="textoCentro <?php echo $registro['est2'] == 0 ? 'semaforoNaranja':'semaforoVerde'; ?>"><?php echo $registro['est2'] == 0 ? 'Pendiente':'Realizado';?></td>
                                <td class="textoCentro"><?php echo $registro['fmtto3']; ?></td>
                                <td class="textoCentro <?php echo $registro['est3'] == 0 ? 'semaforoNaranja':'semaforoVerde'; ?>"><?php echo $registro['est3'] == 0 ? 'Pendiente':'Realizado';?></td>
                                <td class="textoCentro"><?php echo $registro['fmtto4']; ?></td>
                                <td class="textoCentro <?php echo $registro['est4'] == 0 ? 'semaforoNaranja':'semaforoVerde'; ?>"><?php echo $registro['est4'] == 0 ? 'Pendiente':'Realizado';?></td>
                                <td class="textoCentro click_link">
                                    <a href="<?php echo $registro['cserie'];?>" data-fecha ="<?php echo $registro['entrega']; ?>" data-documento ="<?php echo $registro['nrodoc']; ?>">
                                        <i class="fas fa-calendar-alt"></i>
                                    </a>
                                </td>
                            </tr>
                <?php }//}; ?>
            </tbody>

            
        </table>
    </div>
    <script src="<?php echo constant('URL');?>public/js/jquery.js"></script>
    <script src="<?php echo constant('URL');?>public/js/funciones.js?<?php echo constant('VERSION')?>"></script>
    <script src="<?php echo constant('URL');?>public/js/timmtto.js?<?php echo constant('VERSION')?>"></script>
</body>
</html>