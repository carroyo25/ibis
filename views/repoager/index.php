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
            <a href="#" id="irInicio"><i class="fas fa-home"></i></a>
        </div>
    </div>
    
    <div class="itemsReporte">
        <div id="repogen">
            <div id="repogencabecera">
                  <div id="filtros">
                        <div>
                            <p>Mes</p>
                            <select name="mes" id="mes" class="w50por">

                            </select>
                        </div>
                        <div>
                            <p>Clase</p>
                            <select name="clase" id="clase" class="w100por">
                                <?php echo $this->clases ?>
                            </select>
                        </div>
                        <div>
                            <p>Tipo</p>
                            <select name="tipo" id="tipo" class="w100por">
                                <?php echo $this->tipos ?>
                            </select>
                        </div>
                  </div>
                  <div id="calculado">
                    <span> S/. 136,980.94 </span> 
                  </div>  
            </div>
            <div id="graficos">
                <div id="torta">
                </div>
                <div id="lineas">

                </div>
            </div>
            <div id="tablas">
                <div id="div_clase">
                    <table id="tablaClases" class="tablareporte w100por">
                        <?php echo $this->familias?>
                    </table>
                </div>
                <div id="div_items">
                    <table id="tablaItems" class="tablareporte w100por">
                        <thead class="stickytop">
                            <tr>
                                <th>Descripción</th>
                                <th>Cantidad</th>
                                <th>Precio<br/>Unitario</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>CASACA POLAR TALLA M</td>
                                <td>1</td>
                                <td>S/. 569.35</td>
                            </tr>
                            <tr>
                                <td>CASACA POLAR TALLA XL</td>
                                <td>1</td>
                                <td>S/. 311.02</td>
                            </tr>
                            <tr>
                                <td>CHALECO INGNIFUGO COLOR AZULMARINO TALLA L</td>
                                <td>53</td>
                                <td>S/. 311.02</td>
                            </tr>
                            <tr>
                                <td>CHALECO INGNIFUGO COLOR AZULMARINO TALLA M</td>
                                <td>165</td>
                                <td>S/. 229.67</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th scope="row">Totals</th>
                                <td>21,000</td>
                                <td>21,000</td>
                            </tr>
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