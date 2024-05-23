<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div class="wrap_compras">
        <h1>Reporte de Compras</h1>
        <div class="wrap_compras_workarea">
            <div class="filtros">
                <div class="item-filtro">
                    <a href="#" class="item-filtro-click">Proyecto <i class="fas fa-angle-down"></i></a>
                </div>
                <div class="item-filtro">
                    <a href="#" class="item-filtro-click">Año <i class="fas fa-angle-down"></i></a>
                </div>    
            </div>
            <div class="indicadores">
                <div class="indicadoresExterno">
                    <h4>Estado Actual de Items</h4>
                    <div class="indicadoresInterno">
                        <table id="itemsSolicitados">
                            <thead>
                                <tr>
                                    <th>ITEM</th>
                                    <th>DESCRIPCION</th>
                                    <th>% AVANCE</th>
                                    <th>Sub- Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>ANULADO</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>EN COTIZACION</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>FIRMA</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>4</td>
                                    <td>COMPRADO</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>5</td>
                                    <td>ATENCION x STOCK-EN OBRA</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>6</td>
                                    <td>ENTREGA PARCIAL</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>7</td>
                                    <td>EN ALMACEN LURIN</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>8</td>
                                    <td>TRANSITO</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>9</td>
                                    <td>CREADO</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>10</td>
                                    <td>EN OBRA PARCIAL</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>11</td>
                                    <td>EN OBRA</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Total</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                        <div id="graficoIndicadores">
                            
                        </div>
                    </div>
                </div>
                <div class="indicadoresExterno">
                    <h4>Prioridad de Atenciones</h4>
                    <div class="indicadoresInterno">
                        <table id="itemsSolicitados">
                            <thead>
                                <tr>
                                    <th>ITEM</th>
                                    <th>DESCRIPCION</th>
                                    <th>% AVANCE</th>
                                    <th>Sub- Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>URGENTE</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>PRIORIDAD</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>NORMAL</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Total</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                        <div id="graficoAtenciones">
                            
                        </div>
                    </div>
                </div>
                <div class="indicadoresExterno">
                    <h4>Porcentaje de Avance</h4>
                    <div class="indicadoresInterno">
                        <table id="itemsSolicitados">
                            <thead>
                                <tr>
                                    <th>ITEM</th>
                                    <th>DESCRIPCION</th>
                                    <th>% AVANCE</th>
                                    <th>Sub- Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>CSALAZAR</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>ASALAZAR</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>MVIDEIRA</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>4</td>
                                    <td>MTUNOQUE</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>5</td>
                                    <td>EN COTIZACION</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>6</td>
                                    <td>ANULADO</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Total</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                        <div id="graficoAvance">
                            
                        </div>
                    </div>
                </div>
                <div class="indicadoresExterno">
                    <h4>Segmentación de Ordenes</h4>
                    <div class="indicadoresInterno">
                        <table id="itemsSolicitados">
                            <thead>
                                <tr>
                                    <th>ITEM</th>
                                    <th>DESCRIPCION</th>
                                    <th>% AVANCE</th>
                                    <th>Sub- Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>BIENES</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>SERVICIOS</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Total</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                        <div id="graficoSegmentacion">
                            
                        </div>
                    </div>
                </div>
                <div class="indicadoresExterno">
                    <h4>Estado de los ordenes</h4>
                    <div class="indicadoresInterno">
                        <table id="itemsSolicitados">
                            <thead>
                                <tr>
                                    <th>ITEM</th>
                                    <th>DESCRIPCION</th>
                                    <th>% AVANCE</th>
                                    <th>Sub- Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>EN COTIZACION</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>EN FIRMA</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>ENTREGADO</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>4</td>
                                    <td>ANULADO</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Total</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                        <div id="graficoAtenciones">
                            
                        </div>
                    </div>
                </div>
                <div class="indicadoresExterno">
                    <h4>Cantidad de ordenes por proveedor</h4>
                    <div class="indicadoresInterno">
                        <table id="itemsSolicitados">
                            <thead>
                                <tr>
                                    <th>ITEM</th>
                                    <th>DESCRIPCION</th>
                                    <th>CANTIDAD</th>
                                    <th>PROYECTO</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>SERVICIOS</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>BIENES</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Total</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                        <div id="graficoAtenciones">
                            
                        </div>
                    </div>
                </div>
            </div> 
        </div>
        
    </div>
    <script src="<?php echo constant('URL');?>public/js/jquery.js"></script>
    <script src="<?php echo constant('URL');?>public/js/funciones.js"></script>
    <script src="<?php echo constant('URL');?>public/js/infocompras.js?"></script>
</body>
</html>