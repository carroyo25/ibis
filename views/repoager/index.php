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
    <div class="barraTrabajo">
        <form action="#" id="formConsulta">
            <div class="variasConsultasColumna">
                <div class="datosConsultaCargoPlan">
                    <div class="w5por">
                        <label for="tipo">Tipo : </label>
                        <select name="tipoSearch" id="tipoSearch">
                            <option value="-1">Seleccione una opcion</option>
                            <option value="37">Bienes</option>
                            <option value="38">Servicios</option>
                        </select>    
                    </div>
                    <div>
                        <label for="costosSearch">Centro de Costos </label>
                        <select name="costosSearch" id="costosSearch">
                            <?php echo $this->listaCostos ?>
                        </select>
                    </div>
                    <div>
                        <label for="codigo">Clase:</label>
                        <input type="text" name="codigoSearch" id="codigoSearch" class="textoCentro">
                    </div>
                    <div  class="w5por">
                        <label for="ordenSearch">Tipo :</label>
                        <input type="text" name="ordenSearch" id="ordenSearch">
                    </div>
                </div>
                <div class="botonesConsulta">
                        <button type="button" id="btnProcesa">Procesar</button>
                        <button type="button" id="btnExporta">Exportar</button>
                    </div>
            </div>
        </form>
    </div>
    <div class="itemsCargoPlanner">
        <div id="repogen">
            <div id="repogencabecera">
                  <div id="filtros">
                        <div>
                            <p>Mes</p>
                            <select name="mes" id="mes"></select>
                        </div>
                        <div>
                            <p>Clase</p>
                            <select name="clase" id="clase"></select>
                        </div>
                        <div>
                            <p>Tipo</p>
                            <select name="tipo" id="tipo"></select>
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
                    <table id="tablaClases" class="tablareporte">
                        <thead class="stickytop">
                            <tr>
                                <th>Tipo</th>
                                <th>suma<br/>Cantidad</th>
                                <th>Suma<br/>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>ROPA</td>
                                <td>569</td>
                                <td>S/ 76,790.10</td>
                            </tr>
                            <tr>
                                <td>BOTAS SEG</td>
                                <td>95</td>
                                <td>S/ 14,870.28</td>
                            </tr>
                            <tr>
                                <td>GUANTES</td>
                                <td>1140</td>
                                <td>S/. 10,962.26</td>
                            </tr>
                            <tr>
                                <td>FILTROS</td>
                                <td>192</td>
                                <td>S/. 6,393.99</td>
                            </tr>

                        </tbody>
                    </table>
                </div>
                <div id="div_items">
                    <table id="tablaItems" class="tablareporte">
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