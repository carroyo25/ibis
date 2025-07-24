<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div class="cabezaModulo">
        <h1>Cargo Plan Logística</h1>
        <div>
            <a href="#" id="filtrosAvanzados"><i class="fab fa-searchengin"></i><p>Filtros</p></a>
            <a href="3" id="excelJS"><i class="fas fa-file-excel"></i><p>Exportar</p></a>
            <a href="#" id="irInicio"><i class="fas fa-home"></i><p>Inicio</p></a>
        </div>
    </div>
    <div class="barraTrabajo">
        <form action="#" id="formConsulta">
            <input type="hidden" name="estado_item" id="estado_item">
            <div class="variasConsultasColumna">
                <div class="datosConsultaCargoPlan">
                    <div class="parametrosConsulta">
                        <div>
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
                            <label for="codigo">Codigo:</label>
                            <input type="text" name="codigoSearch" id="codigoSearch" class="textoCentro">
                        </div>
                        <div>
                            <label for="ordenSearch">N° Orden :</label>
                            <input type="text" name="ordenSearch" id="ordenSearch" value="6848">
                        </div>
                        <div>
                            <label for="ordenSearch">N° Pedido :</label>
                            <input type="text" name="pedidoSearch" id="pedidoSearch">
                        </div>
                        <div>
                            <label for="descripSearch">Descripción Item:</label>
                            <input type="text" name="descripSearch" id="descripSearch">
                        </div>
                        <div>
                            <label for="conceptoSearch">Concepto : </label>
                            <input type="text" name="conceptoSearch" id="conceptoSearch">
                        </div>
                         <div>
                            <label for="anioSearch">Año : </label>
                            <input type="text" name="anioSearch" id="anioSearch" value="2025">
                        </div>
                    </div>
                    <div class="procesos">
                        <div class="item_anulado"><a href="105" title="Anulado">0%<p>Anulado</p></a></div>
                        <div class="pedidoCreado"><a href="49" title="Pedido Creado">10%<p>Creado</p></a></div>
                        <div class="item_aprobado"><a href="54" title="Pedido Aprobado">15%<p>Aprob.</p></div>
                        <div class="stock"><a href="52" title="Atencion x Stock">20%<p>Stock</p></a></div>
                        <div class="item_orden"><a href="#" title="con OC/OS">25%<p>OC/OS</p></a></div>
                        <div class="item_parcial"><a href="#" title="Enviado Proveedor">30%<p>Enviado</p></a></div>
                        <div class="item_ingreso_parcial" title="Atencion Parcial"><a href="#">40%<p>Ing.Parcial</p></a></div>
                        <div class="item_ingreso_total" title="Atención Total"><a href="#">50%<p>At.Total</p></a></div>
                        <div class="item_registro_salida" title="Atencion cx compras locales"><a href="230">60%<p>Com.Local</p></a></div>
                        <div class="item_registro_gerencia" title="Pedido Gerencia"><a href="#">70%<p>P.Gerencia</p></a></div>
                        <div class="item_transito" title="En transito"><a href="#">75%<p>Transito</p></a></div>
                        <div class="item_ingreso_parcial" title="Parcial Obra"><a href="#">85%<p>Rec.Parcial</p></a></div>
                        <div class="item_en_embarque" title="Item Embarcado"><a href="#">95%<p>Embarcacion</p></a></div>
                        <div class="item_obra" title="En Obra"><a href="#">100%<p>Obra</p></a></div>
                    </div>
                </div>
                <div class="botonesConsulta">
                        <button type="button" id="btnProcesa">Procesar</button>
                        <button type="button" id="btnExporta">Exportar</button>
                    </div>
            </div>
        </form>
    </div>
    <div class="itemsCargoPlanner" id="demo" style="overflow: scroll;">
        <table id="cargoPlanDescrip">
            <thead>
                <tr class="stickytop">
                    <th width="30px">Items</th>
                    <th style="background:#40D1FB; color:#000; position:relative" data-idcol="1" class="datafiltro">Estado</br>Actual</th>
                    <th style="background:#40D1FB; color:#000" >Codigo</br>Proyecto</th>
                    <th style="background:#40D1FB; color:#000" data-idcol="3" class="datafiltro">Area</th>
                    <th style="background:#40D1FB; color:#000" data-idcol="4" class="datafiltro">Partida</th>
                    <th style="background:#40D1FB; color:#000">Atencion</th>
                    <th style="background:#40D1FB; color:#000" data-idcol="6" class="datafiltro">Tipo</th>
                    <th style="background:#FBD341; color:#000">Año</br> Pedido</th>
                    <th style="background:#FBD341; color:#000" data-idcol="8" class="datafiltro">N°</br>Pedido</th>
                    <th style="background:#FBD341; color:#000" width="80px">Creación</br>Pedido</th>
                    <th style="background:#FBD341; color:#000" width="80px">Aprobación</br>Pedido</th>
                    <th style="background:#FBD341; color:#000">Cantidad</br>Pedida</th>
                    <th style="background:#FBD341; color:#000">Cantidad</br>Aprobada</th>
                    <th style="background:#FBD341; color:#000">Cantidad</br>para compra</th>
                    <th style="background:#A6CAF0; color:#000" data-idcol="12">Codigo del</br>Bien/Servicio</th>
                    <th style="background:#A6CAF0; color:#000">Unidad</br>Medida</th>
                    <th style="background:#A6CAF0; color:#000" width="10%" data-idcol="14" class="sticky-column">Descripcion del Bien/Servicio</th>
                    <th style="background:#AAFFAA; color:#000" width="40px">Tipo</br>Orden</th>
                    <th style="background:#AAFFAA; color:#000" width="50px">Año</br>Orden</th>
                    <th style="background:#AAFFAA; color:#000" data-idcol="17" class="datafiltro">N°</br>Orden</th>
                    <th style="background:#AAFFAA; color:#000">Fecha</br>Orden</th>
                    <th style="background:#AAFFAA; color:#000">Cantidad</br>Orden</th>
                    <th style="background:#AAFFAA; color:#000">Item</br>Orden</th>
                    <th style="background:#AAFFAA; color:#000">Fecha</br>Autorización</th>
                    <th>Atencion</br>Almacen</th>
                    <th style="background:#AB7FAB; color:#fff" width="10%" data-idcol="22" class="datafiltro">Descripcion Proveedor</th>
                    <th>Fecha Entrega</br>Proveedor</th>
                    <th width="50px">Cantidad</br>Recibida</th>
                    <th width="50px" data-idcol="25" class="datafiltro">Nota</br>Ingreso</th>
                    <th width="50px">Fecha</br>Recepcion Proveedor</th>
                    <th>Saldo por</br>Recibir</th>
                    <th width="50px">Días</br>Entrega</th>
                    <th>Días</br>Atrazo</th>
                    <th>Semaforo</th>
                    <th style="background:#25AFF3; color:#000">Cantidad</br>Enviada</th>
                    <th style="background:#25AFF3; color:#000" data-idcol="31" class="datafiltro">Nro. Guia</th>
                    <th style="background:#25AFF3; color:#000" data-idcol="32" class="datafiltro">Nro. Guia Sunat</th>
                    <th style="background:#25AFF3; color:#000" data-idcol="33" class="datafiltro">Fecha Envio</th>
                    <th style="background:#127BDD; color:#000">N°. Nota</br>Transferencia</th>
                    <th style="background:#127BDD; color:#000">Fecha</br>Traslado</th>
                    <th style="background:#DA500B; color:#000">Registro</br>Almacen</th>
                    <th style="background:#DA500B; color:#000">Fecha</br>Ingreso Almacen</th>
                    <th style="background:#DA500B; color:#000">Cantidad</br>Recibida</br>Obra</th>
                    <th >N°</br>Fecha Embarque</th>
                    <th >Nombre Embarcacion</th>
                    <th >LURIN</th>
                    <th >PCL</th>
                    <th>Operador</br>Logístico</th>
                    <th>Tipo</br>Transporte</th>
                    <th data-idcol="44" class="datafiltro" style="background:#819830; color:#000">Pedido Asignado</th>
                    <th data-idcol="45" style="background:#819830; color:#000">Fecha Descarga</br>Orden</th>
                </tr>
            </thead>
            <tbody id="cargoPlanDescripBody">
                
            </tbody>
        </table>
    </div>
    <script src="<?php echo constant('URL');?>public/js/exceljs.min.js"></script>
    <script src="<?php echo constant('URL');?>public/js/jquery.js"></script>
    <script src="<?php echo constant('URL');?>public/js/funciones.js?<?php echo constant('VERSION')?>"></script>
    <script src="<?php echo constant('URL');?>public/js/cargoplanlogistic.js?<?php echo constant('VERSION')?>"></script>
</body>
</html>