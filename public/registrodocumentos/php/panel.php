<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Documentos</title>
    <link rel="stylesheet" href="../../css/registrodocumentospanel.css?<?php echo $version = rand(0, 9999); ?>">
    <link rel="stylesheet" href="../../css/all.css">
    <link rel="stylesheet" href="../../css/notify.css">
</head>
<body>
    <div class="modal oculto" id="loader">
        <div class="wrap_modal">
            <div class="loader"></div>
        </div>
    </div>
    <div class="wrap">
        <form action="#" id="fileAtachs" enctype='multipart/form-data'>
            <input type="file" name="uploadAtach" id="uploadAtach" multiple class="oculto">
        </form>
        <div class="wrap_header">
            <div class="logo"></div>
            <div class="entidad_datos">
                <p id="entidad"></p>
                <p id="ruc"></p>
            </div>
        </div>
        <nav class="wrap_nav">
            <div class="acciones_archivo">
                <a href="click_upload" class="botones__click_accion"><i class="fas fa-upload"></i> <p>Subir Documentos</p></a>
                <a href="click_download" class="botones__click_accion"><i class="fas fa-download"></i><p>Descargar Archivos</p></a>
                <a href="click_send" class="botones__click_accion"><i class="fas fa-mail-bulk"></i><p>Enviar Archivos</p></a>
            </div>
            <div class="acciones_sistema">
                <a href="#" class="botones__click_accion"><i class="fas fa-sign-out-alt"></i><p>Cerrar Session</p></a>
            </div>
        </nav>
        <div class="wrap_orders">
            <h2>Registro de Ordenes</h2>
            <input type="hidden" name="id_ent" id="id_ent">
            <input type="search" placeholder="buscar n° de Orden">
            <div class="contenedor_ordenes">
                <ul id="listaOrdenes" class="lista_ul">
                    
                </ul>
            </div>
        </div>
        <div class="wrap_atachs">
            <h2>Adjuntos</h2>
            <div class="body_atachs">
                <div class="contenedor_adjuntos">
                    <div class="atach_list_empty">
                        <h1 class="empty_documents">No se registraron documentos</h1>
                        <p>Seleccione el Nro de Orden y presione el icono de subir Doumentos o Arrastre los archivos Aqui</p>
                    </div>
                    <div class="atach_list_documents">
                        <ul id="list_files_atachs">

                        </ul>
                    </div>
                </div>
                <div class="mensaje_adjuntos">
                    <h3>Observaciones para la presentacion de la documentacion:</h3>
                    <h4>Ordenes de compra se deberán presentar, los siguientes documentos:</h4>
                    <p><i class="fas fa-pen-alt"></i> <span>Factura</span></p>
                    <p><i class="fas fa-pen-alt"></i> <span>Guia de Remisión</span></p>
                    <p><i class="fas fa-pen-alt"></i> <span>Nota de Ingreso</span></p>

                    <h4>Ordenes de Servicio se deberán presentar,los siguientes documentos:</h4>
                    <p><i class="fas fa-pen-alt"></i>  <span>Factura</span></p>
                    <p><i class="fas fa-pen-alt"></i>  <span>Guia de Remisión</span></p>
                    <p><i class="fas fa-pen-alt"></i>  <span>Nota de Ingreso</span></p>
                    <p><i class="fas fa-pen-alt"></i>  <span>Valorización</span></p>

                    <h4 class="parrafo_importante">Los documentos seran ingresados par su aprobacion de pago los días <strong>MARTES y JUEVES</strong></h4>
                </div>
            </div>
        </div>
        <div class="wrap_status">
            <h2>Estados del Documento</h2>
            <div class="body_status">
               <div class="legajo_estado tabla_estado">
                    <table>
                        <caption>Estado Presentacion</caption>
                        <thead>
                            <tr>
                                <th>Descripcion</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Archivos Presentados</td>
                                <td><span id="archivos_presentados"></span></td>
                            </tr>
                            <tr>
                                <td>Fecha Presentación</td>
                                <td><span id="fecha_presentacion_legajo"></span></td>
                            </tr>
                            <tr>
                                <td>Hora Presentación</td>
                                <td><span id="hora_presentacion_legajo"></span></td>
                            </tr>
                            <tr>
                                <td>Fecha Revision</td>
                                <td><span id="fecha_revision_legajo"></span></td>
                            </tr>
                            <tr>
                                <td>Estado Revision</td>
                                <td><span id="estado_revision_legajo"></span></td>
                            </tr>
                        </tbody>
                    </table>
               </div>
               <div class="archivo_estado tabla_estado">
                    <table>
                        <caption>Estado Archivos</caption>
                        <thead>
                            <tr>
                                <th>Descripcion</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Nombre Archivo</td>
                                <td><span id="nombre_archivo"></span></td>
                            </tr>
                            <tr>
                                <td>Tipo Archivo</td>
                                <td><span id="tipo_archivo"></span></td>
                            </tr>
                            <tr>
                                <td>Fecha Presentación</td>
                                <td><span id="fecha_presentación"></span></td>
                            </tr>
                            <tr>
                                <td>Hora Presentación</td>
                                <td><span id="hora_presentación"></span></td>
                            </tr>
                            <tr>
                                <td>Fecha Recepción</td>
                                <td><span id="fecha_recepcion"></span></td>
                            </tr>
                        </tbody>
                    </table>
               </div>
               <div class="leyenda_estado tabla_estado">
                    <table>
                        <caption>Leyenda:</caption>
                        <thead>
                            <tr>
                                <th>Estado</th>
                                <th>Descripcion</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><div style="background:gray;">Presentado</div></td>
                                <td>Presentado</td>
                            </tr>
                            <tr>
                                <td><div style="background:orange;">Revision</div></td>
                                <td>Revision</td>
                            </tr>
                            <tr>
                                <td><div style="background:blue;">Aceptado</div></td>
                                <td>Aceptado</td>
                            </tr>
                            <tr>
                                <td><div style="background:red;">Rechazado</div></td>
                                <td>Rechazado</td>
                            </tr>
                        </tbody>
                    </table>
               </div>
            </div>
        </div>
        <div class="wrap_footer">
            <h5>Sepcon - Derechos Reservados</h5>
        </div>
    </div>
    <script src="../../js/index.var.js"></script>
    <script src="../../js/date-fns.js"></script>
    <script src="../../js/registrodocumentospanel.js?<?php echo $version = rand(0, 9999); ?>"" type="module"></script>
</body>
</html>