<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignación de Logística</title>
</head>
<body>
     <div class="cabezaModulo">
        <h1>Asignación de Pedidos</h1>
        <div>
            <a href="#" id="nuevoRegistro"><i class="far fa-file"></i><p>Nuevo</p></a>
            <a href="#" id="irInicio"><i class="fas fa-home"></i><p>Inicio</p></a>
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
                        <label for="costosSearch">Centro de Costos</label>
                        <input type="text" name="costosSearch" id="costosSearch">
                    </div>
                    <div>
                        <label for="mes">Mes</label>
                        <input type="number" name="mesSearch" id="mesSearch" value="<?php echo date("m")?>" class="textoCentro">
                    </div>
                    <div>
                        <label for="anio">Año :</label>
                        <input type="number" name="anioSearch" id="anioSearch" value="<?php echo date("Y")?>" class="textoCentro">
                    </div>
                    <button type="button">Procesar</button> 
            </div>
        </form>
    </div>
    <div class="itemsTabla">
        <table id="tablaPrincipal">
            <thead>
                <tr>
                    <th>Num.</th>
                    <th>Emision</th>
                    <th>Vencimiento</th>
                    <th>Descripción</th>
                    <th>Centro Costos</th>
                    <th>Resposable</th>
                    <th>Estado</th>
                    <th>Atencion</th>
                    <th>...</th>
                </tr>
            </thead>
            <tbody>
                <?php echo $this->listaPedidos;?>
            </tbody>
        </table>
    </div>
    <script src="<?php echo constant('URL');?>public/js/jquery.js"></script>
    <script src="<?php echo constant('URL');?>public/js/funciones.js?<?php echo constant('VERSION')?>"></script>
    <script src="<?php echo constant('URL');?>public/js/asigna.js?<?php echo constant('VERSION')?>"></script>
</body>
</html>