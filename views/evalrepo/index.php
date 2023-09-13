<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div class="cabezaModulo">
        <h1>Consultar Ordenes</h1>
        <div>
            <a href="#" id="btnExporta"><i class="fas fa-file-excel"></i><p>Exportar</p></a>
            <a href="#" id="irInicio"><i class="fas fa-home"></i><p>Inicio</p></a>
        </div>
    </div>
    <div class="barraTrabajo">
        <form action="#" id="formConsulta">
            <div class="variasConsultas">
                    <div>
                        <label for="tipo">Nro.Orden : </label>
                        <input type="text" name="ordenSearch" id="ordenSearch">
                    </div>
                    <div>
                        <label for="costosSearch">Centro de Costos: </label>
                        <select name="costosSearch" id="costosSearch" class="item4">
                            <?php echo $this->listaCostosSelect ?>
                        </select>
                    </div>
                    <div>
                        <label for="mes">Mes</label>
                        <select name="mesSearch" id="mesSearch">
                            <option value="-1">Mes</option>
                            <option value="01">Enero</option>
                            <option value="02">Febrero</option>
                            <option value="03">Marzo</option>
                            <option value="04">Abril</option>
                            <option value="05">Mayo</option>
                            <option value="06">Junio</option>
                            <option value="07">Julio</option>
                            <option value="08">Agosto</option>
                            <option value="09">Setiembre</option>
                            <option value="10">Octubre</option>
                            <option value="11">Noviembre</option>
                            <option value="12">Diciembre</option>
                        </select>
                    </div>
                    <div>
                        <label for="anio">Año :</label>
                        <input type="number" name="anioSearch" id="anioSearch" class="textoCentro">
                    </div>
                    <button type="button" id="btnConsult">Procesar</button> 
            </div>
        </form>
    </div>
    <div class="itemsCargoPlanner">
        <table id="cargoPlanDescrip">
            <thead class="stickytop">
                <tr>
                    <th rowspan="3" class="filter">Orden</th>  
                    <th rowspan="3">Emision</th>
                    <th rowspan="3" width="15%" class="filter">Descripción</th>
                    <th rowspan="3" class="filter">Centro Costos</th> 
                    <th rowspan="3" width="15%" class="filter">Proveedor</th>
                    <th colspan="25" class="filter">Bienes</th>
                    <th colspan="18" class="filter">Servicios</th>
                    <th rowspan="3" class="filter">Total</th>
                </tr>
                <tr>
                    <th colspan="5">ALMACÉN RECEPCIÓN</th>
                    <th colspan="7">QA/QC</th> 
                    <th colspan="5">ALMACEN OBRA</th>
                    <th colspan="8">COMPRAS</th> 

                    <th colspan="3">USUARIO / GERENTE DE PROYECTO</th>
                    <th colspan="8">QA/QC</th>
                    <th colspan="7">COMPRAS</th> 
                </tr>
                <tr>
                    <th>Fecha de Entrega</th>
                    <th>Condiciones de Llegada</th>
                    <th>Embalaje de Proveedor</th>
                    <th>Cantidad Entregada</th>
                    <th>Documentación</th>

                    <th>Cumplimiento Técnico</th>
                    <th>Documentación</th>
                    <th>Inspeccion Visual</th>
                    <th>Trazabilidad</th>
                    <th>Comunicación</th>
                    <th>Aceptación de Reclamos</th>
                    <th>Aceptación de Reclamos</th>

                    <th>Cantidad Entregada</th>
                    <th>Condiciones de Llegada</th>
                    <th>Embalaje</th>
                    <th>Garantía del Material</th>
                    <th>Documentación</th>

                    <th>Precio Competitivo</th>
                    <th>Descuento</th>
                    <th>Delivery</th>
                    <th>Aceptación de Reclamos</th>
                    <th>Forma de Pago</th>
                    <th>Comunicación</th>
                    <th>Responsabilidad</th>
                    <th>Capacitación</th>

                    <th>Fecha de Atención</th>
                    <th>Calidad</th>
                    <th>Cantidad de Procesos</th>

                    <th>Documentacion</th>
                    <th>Documentacion</th>
                    <th>Documentacion</th>
                    <th>Requisitos</th>
                    <th>Aceptación de Reclamos</th>
                    <th>Comunicación</th>
                    <th>Documentacion</th>
                    <th>Comunicación</th>

                    <th>Precio</th>
                    <th>Descuento</th>
                    <th>Notificaciones</th>
                    <th>Aceptación de Reclamos</th>
                    <th>Forma de Pago</th>
                    <th>Responsabilidad</th>
                    <th>Comunicación</th>
                </tr>
            </thead>
            <tbody>
                <?php echo $this->listarOrdenes?>
            </tbody>
        </table>
    </div>
    <script src="<?php echo constant('URL');?>public/js/jquery.js"></script>
    <script src="<?php echo constant('URL');?>public/js/funciones.js?<?php echo constant('VERSION')?>"></script>
    <script src="<?php echo constant('URL');?>public/js/ordenconsult.js?<?php echo constant('VERSION')?>"></script>
    <script src="<?php echo constant('URL');?>public/js/exceltable.js?<?php echo constant('VERSION')?>"></script>
</body>
</html>