<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<div class="cabezaModulo">
        <h1>Reporte Valorizado</h1>
        <div>
            <a href="#" id="irInicio"><i class="fas fa-home"></i></a>
        </div>
    </div>
    <div class="barraTrabajo">
        <form action="#" id="formConsulta">
            <div class="variasConsultas">
                    <div>
                        <label for="tipo">Tipo : </label>
                        <select name="tipoSearch" id="tipoSearch">
                            <option value="37">Bienes</option>
                            <option value="38">Servicios</option>
                        </select>
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
                    <div>
                        <label for="anio">Año :</label>
                        <input type="number" name="anioSearch" id="anioSearch" value="<?php echo date("Y")?>" class="textoCentro">
                    </div>
                    <div>
                        <button type="button" id="btnConsulta" class="boton3">Consultar</button> 
                        <button type="button" id="btnExporta" class="boton3">Exportar</button> 
                    </div>
            </div>
        </form>
    </div>
    <div class="itemsValorizado">
        <table id="tableValorizado">
            <thead>
                <tr class="stickytop">
                    <th width="30px" data-titulo="item">Item</th>
                    <th style="background:#40D1FB; color:#000" data-titulo="codigoproyecto">Codigo</br>Proyecto</th>
                    <th style="background:#40D1FB; color:#000" data-titulo="descripcionproyecto" with="10%">Descripcion Proyecto/Obra</th>
                    <th style="background:#40D1FB; color:#000" data-titulo="area">Area</th>
                    <th style="background:#40D1FB; color:#000" data-titulo="fecharegistro">Fecha</br>Registro</th>
                    <th style="background:#40D1FB; color:#000" data-titulo="anioorden">Año</br>Orden</th>
                    <th style="background:#40D1FB; color:#000" data-titulo="tipo">Tipo</th>
                    <th style="background:#FBD341; color:#000" data-titulo="aniopedido">Año</br> Pedido</th>
                    <th style="background:#FBD341; color:#000" data-titulo="nroorden">N°</br>Orden</th>
                    <th style="background:#FBD341; color:#000" data-titulo="nropedido">N°</br>Pedido</th>
                    <th style="background:#A6CAF0; color:#000" data-titulo="codigo_producto">Codigo del</br>Bien/Servicio</th>
                    <th style="background:#A6CAF0; color:#000" data-titulo="descripcion" with="15%">Descripcion del Bien/Servicio</th>
                    <th style="background:#A6CAF0; color:#000" data-titulo="unidad">Unidad</br>Medida</th>
                    <th style="background:#AAFFAA; color:#000" data-titulo="proveedor">Proveedor</th>
                    <th style="background:#AAFFAA; color:#000" data-titulo="cantidad">Cantidad</th>
                    <th style="background:#AAFFAA; color:#000" data-titulo="precio">Precio</th>
                    <th style="background:#AAFFAA; color:#000" data-titulo="moneda">Tipo<br>Moneda</th>
                    <th style="background:#AAFFAA; color:#000" data-titulo="total">Importe Total</th>
                    <th style="background:#AB7FAB; color:#fff" data-titulo="cambio">Tipo<br>Cambio</th>
                    <th data-titulo="dolares">Contable ME<br> Total Dólares</th>
                    <th data-titulo="soles">Contable MN<br>Total Soles</th>
                    <th data-titulo="aprobacion">Fecha de <br> Aprobación</th>
                    <th data-titulo="grupo" >Clasificación<br>Grupo</th>
                    <th data-titulo="clase">Clasificación</br>Clase</th>
                    <th data-titulo="direccion">Direccion de Proveedor</th>
                    <th data-titulo="pago" style="background:#3E5555; color:#000">Forma de Pago</th>
                    <th data-titulo="entrega" style="background:#3E5555; color:#000">Fecha de </br>Entrega</th>
                    <th data-titulo="dias" style="background:#3E5555; color:#000">N°<br>Dias</th>
                    <th data-titulo="ruc" style="background:#25AFF3; color:#000">N°</br>R.U.C</th>
                    <th data-titulo="cotizacion" style="background:#25AFF3; color:#000">N° de</br>Cotización Adjudicado</th>
                    <th data-titulo="parte" style="background:#25AFF3; color:#000">N° de Parte<br>Maq. Edquipo</th>
                    <th data-titulo="equipo" style="background:#DA500B; color:#000">Código<br>Maq. Equipo</th>
                    <th data-titulo="estado" style="background:#DA500B; color:#000">Estado</th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table>
    </div>
    <script src="<?php echo constant('URL');?>public/js/jquery.js"></script>
    <script src="<?php echo constant('URL');?>public/js/funciones.js"></script>
    <script src="<?php echo constant('URL');?>public/js/valorizado.js?<?php echo constant('VERSION')?>"></script>
</body>
</html>