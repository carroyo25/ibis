<?php 
    require_once("acciones.php");

    $version = rand(0, 9999);

    $ruc = $_GET['codenti'];
    $pedido = $_GET['codped'];

    $monedas = parametros($pdo,'03');
    $pagos = parametros($pdo,'11');
    $proveedor = nombre_entidad($pdo,$ruc);
    $items = itemsPedido($pdo,$pedido,$ruc);
    $verificar = verificaParticipa($pdo,$pedido,$ruc);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="../img/logo.png" />
    <link rel="stylesheet" href="../css/all.css">
    <link rel="stylesheet" href="../css/ibis.css?<?php echo $version?>">
    <link rel="stylesheet" href="../css/cotizacion.css?<?php echo $version?>">
    
    <title>Sepcon .. Cotizaciones</title>
</head>
<body>
    <div class="mensaje">
        <p></p>
    </div>
    <div class="modal" id="esperar">
    </div>
    <div class="modal" id="pregunta">
        <div class="ventanaPregunta">
            <h3>Enviar el documento?</h3>
            <div>
                <button type="button" id="btnAceptarPregunta">Aceptar</button>
                <button type="button" id="btnCancelarPregunta">Cancelar</button>
            </div>
        </div>
    </div>
    <div class="wrapcot">
        <form action="" method="post" id="proforma">
            <input type="hidden" name="pedido" id="pedido" value="<?php echo $pedido?>">
            <input type="hidden" name="st" id="st">
            <input type="hidden" name="si" id="si">
            <input type="hidden" name="to" id="to">
            <input type="file" name="cotizacion" id="cotizacion" class="oculto">
            <input type="file" name="itemAtach" id="itemAtach" class="oculto">
            <div class="cabeceracot">
                <div class="logo">
                    <img src="../img/logo.png" alt="">
                </div>
                <div class="nombre_ent">
                    <p><?php echo $proveedor?></p>
                    <p id="ruc"><?php echo $ruc?></p>
                </div>
                <div class="fecha">
                    <p><?php echo date("d/m/y")?></p>
                </div>
            </div>
            <div class="cuerpo">
                <div class="datos_cot">
                    <div class="documento">
                        <p>SOLICITUD DE COTIZACION</p>
                    </div>
                    <div class="datos_documento">
                        <label for="fecha_emision">Fecha Emisión</label>
                        <input type="date" name="fecha_emision" id="fecha_emision" value="<?php echo date('Y-m-d'); ?>">
                        <label for="fecha_vence">Fecha Vencimiento</label>
                        <input type="date" name="fecha_vence" id="fecha_vence">
                        <label for="nro_cot">N° Cotización</label>
                        <input type="text" name="nro_cot" id="nro_cot">
                        <label for="moneda">Moneda</label>
                        <select name="moneda" id="moneda">
                            <?php echo $monedas ?>
                        </select>
                        <label for="cond_pago">Condición Pago</label>
                        <select name="cond_pago" id="cond_pago">
                            <?php echo $pagos ?>
                        </select>
                        <label>Incluye IGV</label>
                        <div class="igv">
                            <input type="radio" name="radioIgv" id="si" value="0.18">
                            <label for="si">Si</label>
                            <input type="radio" name="radioIgv" id="no" value="0">
                            <label for="no">No</label>
                        </div>
                    </div>
                    <div class="detalles_cot">
                        <div class="detalles_titulo">
                            <p>DETALLES DEL PEDIDO</p>
                        </div>
                        <div class="items">
                            <p>Sirvase cotizar los productos descritos lineas abajo</p>
                            <div class="tablas_items">
                                <table>
                                    <thead>
                                        <tr>
                                            <th width="4%">Item</th>
                                            <th width="10%">Código</th>
                                            <th width="35%">Descripción</th>
                                            <th width="5%">Und</th>
                                            <th width="5%">Cant.</th>
                                            <th width="6%">Precio.</th>
                                            <th width="8%">Total</th>
                                            <th width="10%">N°.Parte</th>
                                            <th width="15%">Observaciones</th>
                                            <th>Entrega</th>
                                            <th width="10%">...</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php echo $items ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="totales">
                            <label>Sub. Total</label>
                            <input type="text" name="stotal" id="stotal" readonly>
                            <label>I.G.V.</label>
                            <input type="text" name="igv" id="igv" readonly>
                            <label>Sub. Total</label>
                            <input type="text" name="total" id="total" readonly>
                        </div>
                        <div class="comentarios">
                            <div>
                                <label for="observaciones">Observaciones</label>
                                <textarea name="observaciones" id="observaciones" cols="30" rows="10"></textarea>
                            </div>
                            <div class="especificaciones">
                                <p>Especificaciones :</p>
                                <ul>
                                    <li>Cotizar lo items descritos</li>
                                    <li>* Especificar al moneda de la cotización</li>
                                    <li>Dejar libre, en caso de tener los items especificados</li>
                                    <li>Adjuntar las manuales,hojas msds por item, de ser necesario</li>
                                    <li>* Adjuntar la cotizacion en formato PDF</li>
                                    <li>* Especificar la fecha de validez de la propuesta</li>
                                    <li>* Indicar la fecha de entrega por item cotizado</li>
                                </ul>
                            </div>
                        </div>
                        <?php if (!$verificar) {?>
                            <div class="opciones">
                                <button type="button" id="btnAtach"><i class="fas fa-file-pdf"></i>Adjuntar Cotización</button>
                                <button type="button" id="btnSend"><i class="far fa-calendar-check"></i>Enviar Documento</button>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <script src="../js/jquery.js"></script>
    <script src="../js/funciones.js"></script>
    <script src="../js/proforma.js?v<?php echo $version?>"></script>
</body>
</html>