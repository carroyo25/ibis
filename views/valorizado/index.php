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
        <h1>Cargo Plan</h1>
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
                    <button type="button" id="btnConsulta" class="boton3">Consultar</button> 
            </div>
        </form>
    </div>
    <div class="itemsValorizado">
        <table id="table_valorizado">
            <thead>
                <tr class="stickytop">
                    <th width="30px">Item</th>
                    <th style="background:#40D1FB; color:#000">Codigo</br>Proyecto</th>
                    <th style="background:#40D1FB; color:#000" with="10%">Descripcion Proyecto/Obra</th>
                    <th style="background:#40D1FB; color:#000">Area</th>
                    <th style="background:#40D1FB; color:#000">Fecha</br>Registro</th>
                    <th style="background:#40D1FB; color:#000">Año</br>Orden</th>
                    <th style="background:#40D1FB; color:#000">Tipo</th>
                    <th style="background:#FBD341; color:#000">Año</br> Pedido</th>
                    <th style="background:#FBD341; color:#000">N°</br>Orden</th>
                    <th style="background:#FBD341; color:#000" >N°</br>Pedido</th>
                    <th style="background:#A6CAF0; color:#000">Codigo del</br>Bien/Servicio</th>
                    <th style="background:#A6CAF0; color:#000" with="15%">Descripcion del Bien/Servicio</th>
                    <th style="background:#A6CAF0; color:#000">Unidad</br>Medida</th>
                    <th style="background:#AAFFAA; color:#000">Proveedor</th>
                    <th style="background:#AAFFAA; color:#000">Cantidad</th>
                    <th style="background:#AAFFAA; color:#000">Precio</th>
                    <th style="background:#AAFFAA; color:#000">Tipo<br>Moneda</th>
                    <th style="background:#AAFFAA; color:#000">Importe Total</th>
                    <th style="background:#AB7FAB; color:#fff">Tipo<br>Cambio</th>
                    <th>Contable ME<br> Total Dólares</th>
                    <th>Contable MN<br>Total Soles</th>
                    <th>Fecha de <br> Aprobación</th>
                    <th >Clasificación<br>Grupo</th>
                    <th>Clasificación</br>Clase</th>
                    <th>Direccion de Proveedor</th>
                    <th style="background:#3E5555; color:#000">Forma de Pago</th>
                    <th style="background:#3E5555; color:#000">Fecha de </br>Entrega</th>
                    <th style="background:#3E5555; color:#000">N°<br>Dias</th>
                    <th style="background:#25AFF3; color:#000">N°</br>R.U.C</th>
                    <th style="background:#25AFF3; color:#000">N° de</br>Cotización Adjudicado</th>
                    <th style="background:#25AFF3; color:#000">N° de Parte<br>Maq. Edquipo</th>
                    <th style="background:#DA500B; color:#000">Código<br>Maq. Equipo</th>
                    <th style="background:#DA500B; color:#000">Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php echo $this->valorizado?>
            </tbody>
        </table>
    </div>
    <script src="<?php echo constant('URL');?>public/js/jquery.js"></script>
    <script src="<?php echo constant('URL');?>public/js/funciones.js"></script>
    <script src="<?php echo constant('URL');?>public/js/valorizado.js?<?php echo constant('VERSION')?>"></script>
</body>
</html>