<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Gerencial - 01</title>
</head>
<body>
    <div class="cabezaModulo">
        <h1>Resumen de Almacen</h1>
        <div>
            <a href="#" id="export"><i class="fas fa-file-excel"><p>Reporte</p></i></a>
            <a href="#" id="irInicio"><i class="fas fa-home"><p>Inicio</p></i></a>
        </div>
    </div>
    <div class="itemsReporte">
        <div id="repogen">
            <div id="repogencabecera">
                  <div id="filtros">
                        <div>
                            <p>Mes</p>
                            <select name="mes" id="mes">
                                <?php echo $this->mes ?>
                            </select>
                        </div>
                        <div>
                            <p>Año</p>
                            <input type="text" name="anio" id="anio" value="2023">
                        </div>
                        <div>
                            <p>Centro de Costos</p>
                            <select name="costos" id="costos" class="w75por">
                                <?php echo $this->listaCostosSelect ?>
                            </select>
                        </div>
                        <div>
                            <p>Grupos</p>
                            <select name="grupo" id="grupo">
                                <?php echo $this->clases ?>
                            </select>
                        </div>
                        <div>
                            <p>Clases</p>
                            <select name="clase" id="clase">
                                <?php echo $this->tipos ?>
                            </select>
                        </div>
                        <div>
                            <p>Familias</p>
                            <select name="familia" id="familia">
                                <?php echo $this->tipos ?>
                            </select>
                        </div>
                        <div id="calculado">
                            <span>S/.  </span> 
                        </div>
                  </div>
                    
            </div>
            <div id="graficos">
                <div id="grupos">
                </div>
                <div id="clases">
                </div>
                <div id="familias">
                </div>
            </div>
            <div id="tablas">
                <div id="div_clase">
                    <table id="tablaClases" class="tablareporte w100por">
                        <thead class='stickytop'>
                            <tr >
                                <th>Tipo</th>
                                <th>Suma<br/>Cantidad</th>
                                <th>Suma<br/>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                        <tfoot>
                            <tr>
                                <td>Total</td>
                                <td class="textoDerecha">0</td>
                                <td class="textoDerecha">S/. 0.00</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div id="div_items">
                    <table id="tablaItems" class="tablareporte w100por">
                        <thead class='stickytop'>
                            <tr >
                                <th>Tipo</th>
                                <th>Total<br/>Orden</th>
                                <th>Suma<br/>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                        <tfoot>
                            
                        </tfoot>
                    </table>
                </div>
                <div id="barras">

                </div>
            </div>
        </div>
    </div>
    <script src="<?php echo constant('URL');?>public/js/jquery.js"></script>
    <script src="<?php echo constant('URL');?>public/js/funciones.js"></script>
    <script src="<?php echo constant('URL');?>public/js/repoager.js?"></script>
</body>
</html>