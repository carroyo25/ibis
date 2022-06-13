<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<div class="modal" id="vistadocumento">
        <div class="ventanaDocumento">
            <form method="post" id="cargoplan">
                <div class="tituloDocumento">
                    <div>
                        <p class="titulo_seccion"><strong> Descripcion Item : </strong></p>
                            
                    </div>
                    
                </div>
                <hr>
                <div>
                    
                </div>
            </form>
        </div>
    </div>
    <div class="cabezaModulo">
        <h1>Cargo Planner</h1>
        <div>
            <a href="#" id="irInicio"><i class="fas fa-home"></i></a>
        </div>
    </div>
    <div class="barraTrabajo">
        <form action="#" id="formConsulta">
            <div class="variasConsultasColumna">
                <div class="datosConsulta">
                        <label for="tipo" class="item1">Tipo : </label>
                        <select name="tipoSearch" id="tipoSearch" class="item2">
                            <option value="37">Bienes</option>
                            <option value="38">Servicios</option>
                        </select>
                        <label for="costosSearch" class="item3">Centro de Costos </label>
                        <select name="costosSearch" id="costosSearch" class="item4">
                            <?php echo $this->listaCostos ?>
                        </select>
                        <label for="mes" class="item5">Mes</label>
                        <input type="number" name="mesSearch" id="mesSearch" value="<?php echo date("m")?>" class="textoCentro item6">
                        <label for="anio" class="item7">Año :</label>
                        <input type="number" name="anioSearch" id="anioSearch" value="<?php echo date("Y")?>" class="textoCentro item8">
                        <label for="ordenSearch" class="item9">Orden :</label>
                        <input type="text" name="ordenSearch" id="ordenSearch" class="item10">
                        <label for="almacenSearch" class="item11">Almacen :</label>
                        <select name="almacenSearch" id="almacenSearch" class="item12">
                            <?php echo $this->listaAlmacen ?>
                        </select>
                        <label for="conceptoSearch" class="item13">Concepto : </label>
                        <input type="text" name="conceptoSearch" id="conceptoSearch" class="item14">
                    
                </div>

                <button type="button">Procesar</button> 
            </div>
        </form>
    </div>
    <div class="itemsTabla">
        <table id="tablaPrincipal">
            <thead>
                <tr>
                    <th width="3%">Items</th>
                    <th width="7%">Estado</br>Actual</th>
                    <th>Codigo</br>Costos</th>
                    <th width="5%">Atencion</th>
                    <th width="5%">Tipo</th>
                    <th>Año</th>
                    <th>Pedido</th>
                    <th>Und.</th>
                    <th>Descripcion</th>
                    <th>Orden</th>
                </tr>
            </thead>
            <tbody>
                <?php echo $this->listaItems;?>
            </tbody>
        </table>
    </div>
    <div class="modal" id="series">
        <div class="ventanaArchivos">
            <table id="tablaSeries" class="tabla">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Serie</th>
                    </tr>
                </thead>
                <tbody>
                    
                </tbody>
            </table>
            </br>
            <div class="opcionesArchivos">
                <button type="button" class="boton3" id="btnConfirmSeries">Aceptar</button>
                <button type="button" class="boton3" id="btnCancelSeries">Cancelar</button>
            </div>
        </div>
    </div>
    <script src="<?php echo constant('URL');?>public/js/jquery.js"></script>
    <script src="<?php echo constant('URL');?>public/js/funciones.js?<?php echo constant('VERSION')?>"></script>
    <script src="<?php echo constant('URL');?>public/js/cargoplan.js?<?php echo constant('VERSION')?>"></script>
</body>
</html>