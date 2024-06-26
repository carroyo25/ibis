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
            <div class="header__title">
                <h1>Reporte de Proveedores</h1>
                <div>
                    <a href="#" id="excelFile"><i class="fas fa-file-excel"></i><p>Exportar</p></a>
                    <a href="#" id="irInicio"><i class="fas fa-home"></i><p>Inicio</p></a>
                </div>
            </div>
            <div class="wrap__body">
                <div class="table_body">
                    <table id="tablaPrincipalProveedor">
                        <thead class="stickytop">
                                <tr>
                                <th rowspan="2" width="3%" data-campo="cnumero">Num.</th>  
                                <th rowspan="2" class="headerTableFilter" data-campo="ffemision">Emision</th>
                                <th rowspan="2" width="25%" data-campo="cConcepto">Descripción</th>
                                <th rowspan="2" class="headerTableFilter" data-campo="cCostos">Centro Costos</th> 
                                <th rowspan="2" >Area</th>
                                <th rowspan="2" width="15%" class="headerTableFilter" data-campo="cEntidad">Proveedor</th>
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
                            <?php foreach($this->listaOrdenes['ordenes'] as $orden) {
                                $log = is_null($orden['nfirmaLog']) ? '<i class="far fa-square"></i>' : '<i class="far fa-check-square"></i>';
                                $ope = is_null($orden['nfirmaOpe']) ? '<i class="far fa-square"></i>' : '<i class="far fa-check-square"></i>';
                                $fin = is_null($orden['nfirmaFin']) ? '<i class="far fa-square"></i>' : '<i class="far fa-check-square"></i>';

                                if ( $orden['ncodmon'] == 20) {
                                    $montoSoles = "S/. ".number_format($orden['ntotal'],2);
                                    $montoDolares = "";
                                }else{
                                    $montoSoles = "";
                                    $montoDolares =  "$ ".number_format($orden['ntotal'],2);
                                }
        
                                if ( $orden['nEstadoDoc'] == 49) {
                                    $estado = "procesando";
                                }else if ( $orden['nEstadoDoc'] == 59 ) {
                                    $estado = "firmas";
                                }else if ( $orden['nEstadoDoc'] == 60 ) {
                                    $estado = "recepcion";
                                }else if ( $orden['nEstadoDoc'] == 62 ) {
                                    $estado = "despacho";
                                }else if ( $orden['nEstadoDoc'] == 105 ) {
                                    $estado = "anulado";
                                    $montoDolares = "";
                                    $montoSoles = "";
                                }
                            ?>
                                <tr class="pointer">
                                    <td class="textoCentro"><?php echo $orden['cnumero']?></td>
                                    <td class="textoCentro"><?php echo $orden['ffechadoc']?></td>
                                    <td class="pl20px"><?php echo $orden['concepto']?></td>
                                    <td class="pl20px"><?php echo $orden['ccodproy']?></td>
                                    <td class="pl20px"><?php echo $orden['area']?></td>
                                    <td class="pl20px"><?php echo $orden['proveedor']?></td>
                                    <td class="textoDerecha"><?php echo $montoSoles?></td>
                                    <td class="textoDerecha"><?php echo $montoDolares?></td>
                                    <td class="textoCentro <?php echo $estado ?>"><?php echo strtoupper($estado) ?></td>
                                    <td class="textoCentro"><?php echo $log?></td>
                                    <td class="textoCentro"><?php echo $fin?></td>
                                    <td class="textoCentro"><?php echo $ope?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <div class="graphic_body">
                    <div id="repo_graphic">

                    </div>
                </div>
                <div class="account_body">
                    <h1>Detalles</h1>

                    <div class="account_body_details">
                        <h2>Año(s) Consultado(s):</h2>
                        <span id="anios"></span>

                        <h2>Nro. Ordenes :</h2>
                        <span id="nroOrdenes">0</span>

                        <h2>Nro. Ordenes Compra:</h2>
                        <span id="orden_compra">0</span>

                        <h2>Nro. Ordenes Servicio:</h2>
                        <span id="orden_servicio">0</span>

                        <h2>Total Soles :</h2>
                        <span id="total_soles">0.00</span>

                        <h2>Total Dolares :</h2>
                        <span id="total_dolares">0.00</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="<?php echo constant('URL');?>public/js/jquery.js"></script>
    <script src="<?php echo constant('URL');?>public/js/funciones.js"></script>
    <script src="<?php echo constant('URL');?>public/js/repoprove.js?"></script>
</body>
</html>