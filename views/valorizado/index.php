<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="<?php echo constant('URL'); ?>public/css/cargoplan.css">
    <link rel="stylesheet" href="<?php echo constant('URL'); ?>public/css/bienes.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="mensaje">
        <p></p>
    </div>
    <div class="modal" id="vistaCarpetas">
        <div class="ventanaCarpetas">
            <div class="tituloAdjuntos">
                <h3>Ordenes de Compra</h3>
                <div>
                    <a href="#" id="closeAtach" title="Cerrar Ventana"><i class="fas fa-window-close"></i></a>
                </div>
            </div>
            <div class="listaCarpetas">
                <div class="searchContainer">
                    <input type="search" name="ordenSearch" id="ordenSearch" placeholder="Buscar...">
                </div>
                <ul>
                </ul>
            </div>
        </div>
    </div>
    <div class="modal" id="vistaAdjuntos">
        <div class="ventanaAdjuntos">
            <div class="tituloAdjuntos">
                <h3>Adjuntos</h3>
                <a href="#" id="closeAtachFiles" title="Cerrar Ventana"><i class="fas fa-window-close"></i></a>
            </div>
            <ul id="listaAdjuntos">

            </ul>
            <iframe src=""></iframe>
        </div>
    </div>
    <div class="cabezaModulo">
        <h1>Reporte Valorizado</h1>
        <div>
            <a href="#" id="downFiles"><i class="fas fa-inbox" class="boton3"></i>
                <p>Descargar<br>Adjuntos</p>
            </a>
            <a href="#" id="irInicio"><i class="fas fa-home" class="boton3"></i>
                <p>Inicio</p>
            </a>
        </div>
    </div>
    <div class="barraTrabajo">
        <form action="#" id="formConsulta">
            <div class="filtros-container">
                    <div class="filtro-grupo">
                        <label for="tipo">Tipo : </label>
                        <select name="tipoSearch" id="tipoSearch" class="form-control">
                            <option value="37">Bienes</option>
                            <option value="38">Servicios</option>
                        </select>
                    </div>
                    <div class="filtro-grupo">
                        <label for="costosSearch">Centro de Costos: </label>
                        <select name="costosSearch" id="costosSearch" class="form-control" >
                            <?php echo $this->listaCostosSelect ?>
                        </select>
                    </div>
                    <div class="filtro-grupo">
                        <label for="mes">Mes</label>
                        <select name="mesSearch" id="mesSearch" class="form-control">
                            <option value="-1">Mes</option>
                            <option value="1">Enero</option>
                            <option value="2">Febrero</option>
                            <option value="3">Marzo</option>
                            <option value="4">Abril</option>
                            <option value="5">Mayo</option>
                            <option value="6">Junio</option>
                            <option value="7">Julio</option>
                            <option value="8">Agosto</option>
                            <option value="9">Setiembre</option>
                            <option value="10">Octubre</option>
                            <option value="11">Noviembre</option>
                            <option value="12">Diciembre</option>
                        </select>
                    </div>
                    <div class="filtro-grupo">
                        <label for="anio">Año :</label>
                        <input type="number" name="anioSearch" id="anioSearch" value="<?php echo date("Y")?>" class="form-control">
                    </div>
                    <div class="filtro-accion">
                        <button type="button" id="btnConsulta" class="btn-consultar">Consultar</button> 
                        <button type="button" id="btnExporta" class="btn-consultar">Exportar</button> 
                    </div>
            </div>
        </form>
    </div>
    <div class="itemsValorizado">
        <table id="tableValorizado" class="itemsCargoPlanner">
            <thead>
                <tr class="stickytop">
                    <th data-titulo="item" width="80ch">Item</th>
                    <th style="background:#40D1FB; color:#000" data-titulo="codigoproyecto" width="100ch">Codigo Proyecto</th>
                    <th style="background:#40D1FB; color:#000" data-titulo="descripcionproyecto" width="200ch">Centro Costos</th>
                    <th style="background:#40D1FB; color:#000" data-titulo="area" width="200ch">Area</th>
                    <th style="background:#40D1FB; color:#000" data-titulo="fecharegistro" width="3%">Fecha Registro</th>
                    <th style="background:#40D1FB; color:#000" data-titulo="anioorden" width="2%">Año Orden</th>
                    <th style="background:#40D1FB; color:#000" data-titulo="tipo">Tipo</th>
                    <th style="background:#FBD341; color:#000" data-titulo="aniopedido" width="80ch">Año Pedido</th>
                    <th style="background:#FBD341; color:#000" data-titulo="nroorden" width="80ch">N°. Pedido</th>
                    <th style="background:#FBD341; color:#000" data-titulo="nropedido" width="80ch">N°. Orden</th>
                    <th style="background:#A6CAF0; color:#000" data-titulo="codigo_producto" width="5%">Código Bien/Servicio</th>
                    <th style="background:#A6CAF0; color:#000" data-titulo="descripcion" width="450ch">Descripción Bien/Servicio</th>
                    <th style="background:#A6CAF0; color:#000" data-titulo="unidad">Unidad</th>
                    <th style="background:#AAFFAA; color:#000" data-titulo="proveedor" width="250ch">Proveedor</th>
                    <th style="background:#AAFFAA; color:#000" data-titulo="cantidad" width="150ch">Cantidad</th>
                    <th style="background:#AAFFAA; color:#000" data-titulo="precio" width="100ch">Precio</th>
                    <th style="background:#AAFFAA; color:#000" data-titulo="moneda" width="100ch">Moneda</th>
                    <th style="background:#AAFFAA; color:#000" data-titulo="total" width="100ch">Total</th>
                    <th style="background:#AB7FAB; color:#fff" data-titulo="cambio" width="100ch">Tipo Cambio</th>
                    <th data-titulo="dolares" width="150ch">Total Dólares</th>
                    <th data-titulo="soles" width="150ch">Total Soles</th>
                    <th data-titulo="aprobacion" width="80ch">Fecha Aprobación</th>
                    <th data-titulo="grupo" width="150ch">Grupo</th>
                    <th data-titulo="clase" width="150ch">Clase</th>
                    <th data-titulo="direccion" width="200ch">Direccion  Proveedor</th>
                    <th data-titulo="pago" style="background:#3E5555; color:#000" width="150ch">Forma Pago</th>
                    <th data-titulo="entrega" style="background:#3E5555; color:#000" width="100ch">Fecha Entrega</th>
                    <th data-titulo="dias" style="background:#3E5555; color:#000" width="80ch">N° Dias</th>
                    <th data-titulo="ruc" style="background:#25AFF3; color:#000" width="90ch">N° R.U.C</th>
                    <th data-titulo="cotizacion" style="background:#25AFF3; color:#000" width="100ch">N° Cotización</th>
                    <th data-titulo="parte" style="background:#25AFF3; color:#000" width="80ch">N° de Parte</th>
                    <th data-titulo="equipo" style="background:#DA500B; color:#000" width="80ch">Código<br>Maq. Equipo</th>
                    <th data-titulo="estado" style="background:#DA500B; color:#000" width="150ch">Estado</th>
                    <th data-titulo="concepto" style="background:#DA500B; color:#000" width="200ch">Descripcion</th>
                    <th data-titulo="sunat" style="background:#1476B8; color:#000" width="100ch">Guia<br>Sunat</th>
                    <th data-titulo="interna" style="background:#1476B8; color:#000" width="100ch">Guia<br>Interna</th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table>
    </div>
    <div class="paginador_cp">
        <div class="pagination">
        
        </div>
    </div>
    <script src="<?php echo constant('URL');?>public/js/jquery.js"></script>
    <script src="<?php echo constant('URL');?>public/js/funciones.js"></script>
    <script src="<?php echo constant('URL');?>public/js/valorizado.js?<?php echo constant('VERSION')?>"></script>
</body>
</html>