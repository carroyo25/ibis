<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div class="wrap__new">
        <div class="wrap__title">
            <h1>Reporte de Proveedores</h1>
            <div class="wrap__body">
                <div class="table_body">
                    <table id="tablaPrincipalProveedor">
                        <thead class="stickytop">
                                <tr>
                                    <th rowspan="2" class="headerTableFilter">Num.</th>  
                                    <th rowspan="2">Emision</th>
                                    <th rowspan="2" width="25%">Descripción</th>
                                    <th rowspan="2">Centro Costos</th> 
                                    <th rowspan="2" >Area</th>
                                    <th rowspan="2" width="15%">Proveedor</th>
                                    <th rowspan="2" >Precio Soles</th>
                                    <th rowspan="2">Precio Dólares</th>
                                    <th rowspan="2">Estado</th>
                                    <th colspan="3" width="16%">Firmas</th>
                                <tr>
                                    <th>Procura</th>
                                    <th>Finanzas</th>
                                    <th>Operaciones</th>
                                </tr>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($this->listaOrden['ordenes'] as $orden) {?>
                                <tr class="pointer">
                                    <td><?php echo $orden['cnumero']?></td>
                                    <td><?php echo $orden['ffechadoc']?></td>
                                    <td><?php echo $orden['concepto']?></td>
                                    <td><?php echo $orden['ccodproy']?></td>
                                    <td><?php echo $orden['area']?></td>
                                    <td><?php echo $orden['proveedor']?></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <div class="graphic_body">
                    2
                </div>
                <div class="account_body">
                    3
                </div>
            </div>
        </div>
    </div>
    <script src="<?php echo constant('URL');?>public/js/jquery.js"></script>
    <script src="<?php echo constant('URL');?>public/js/funciones.js"></script>
    <script src="<?php echo constant('URL');?>public/js/repoprove.js?"></script>
</body>
</html>