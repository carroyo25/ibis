<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo constant('URL'); ?>public/css/dialogos.css">
    <title>Document</title>
</head>
<body>
    <div class="mensaje">
        <p></p>
    </div>
    <!-- ============================================= -->
    <!-- MODAL PARA AGREGAR/EDITAR -->
    <!-- ============================================= -->
    <div class="modal" id="movimientos">
        <div class="modalWrap">
            <div id="modalTraspaso" class="modal-overlay">
                <div class="modal-box">

                    <!-- HEADER -->
                    <div class="modal-header">
                    <h3>🔄 Confirmar Traspaso de Materiales</h3>
                    <button type="button" class="modal-close" id="closeDialog">&times;</button>
                    </div>

                    <!-- BODY -->
                    <div class="modal-body">

                    <div class="modal-row">
                        <div class="modal-field">
                        <label>Fecha:</label>
                        <input type="date" id="fechaTraspaso" readonly>
                        </div>
                        <div class="modal-field">
                        <label>Autorizado por:</label>
                        <input type="text" id="autorizadoTraspaso" placeholder="Nombre de quien autoriza">
                        </div>
                    </div>

                    <div class="modal-row">
                        <div class="modal-field">
                        <label>Responsable:</label>
                        <input type="text" id="responsableTraspaso" placeholder="Nombre de quien ejecuta">
                        </div>
                        <div class="modal-field">
                        <label>Total de ítems a traspasar:</label>
                        <input type="text" id="itemsTraspaso" readonly class="highlight">
                        </div>
                    </div>

                    <div class="modal-row">
                        <div class="modal-field">
                        <label>Centro de Costos Origen:</label>
                        <input type="text" id="ccOrigenTraspaso" readonly>
                        </div>
                        <div class="modal-field">
                        <label>Centro de Costos Destino:</label>
                        <select id="ccDestinoTraspaso">
                            <?php echo $this->listaCostosSelectDestino ?>
                        </select>
                        </div>
                    </div>

                    <!-- Advertencia -->
                    <div class="modal-alert">
                        ⚠️ <strong>Importante:</strong> Revise los datos enviados antes de grabar. Esta acción no se puede deshacer.
                    </div>

                    </div>

                    <!-- FOOTER -->
                    <div class="modal-footer">
                    <button type="button" class="btn btn-cancel" id="closeDialogButton">
                        Cancelar
                    </button>
                    <button type="button" class="btn btn-confirm" id="btnDocument">
                        📑 Guia de Remision
                    </button>
                    <button type="button" class="btn btn-confirm" id="btnConfirmarTraspaso">
                        ✅ Confirmar Traspaso
                    </button>
                    </div>

                </div>
        </div>
        </div>
    </div>
    <div class="cabezaModulo">
        <h1>Traspaso de Materiales</h1>
        <div>
            <a href="#" id="transferItems"><i class="fas fa-exchange-alt"></i><p>Transferir</p></a>
            <a href="#" id="excelFile"><i class="fas fa-file-excel"></i><p>Exportar</p></a>
            <a href="#" id="irInicio"><i class="fas fa-home"></i><p>Inicio</p></a>
        </div>
    </div>
    <div class="barraTrabajo">
        <form action="#" id="formConsulta">
            <div class="variasConsultas4campos">
                    <div>
                        <label for="costosSearch">Centro de Costos Origen: </label>
                        <select name="costosSearch" id="costosSearch">
                            <?php echo $this->listaCostosSelectOrigen ?>
                        </select>
                    </div>
                    <div>
                        <label for="codigoBusqueda">Codigo : </label>
                        <input type="text" name="codigoBusqueda" id="codigoBusqueda">
                    </div>
                    <div>
                        <label for="descripcionSearch">Descripcion: </label>
                        <input type="text" name="descripcionSearch" id="descripcionSearch">
                    </div>
                    <div>
                    </div>
                    <button type="button" id="btnConsulta" class="boton3">Consultar</button> 
            </div>
        </form>
    </div>
    <div class="itemsTabla">
        <table id="tablaPrincipal">
            <thead class="stickytop">
                <tr>
                    <th rowspan="2">Item</th>
                    <th rowspan="2">Codigo</th>
                    <th rowspan="2" width="50%">Descripcion</th>
                    <th rowspan="2">Unidad</th>
                    <th rowspan="2">Saldo</th>
                    <th rowspan="2">Cantidad <br> Transferencia</th>
                    <th rowspan="2">Observaciones</th>
                    <th colspan="9">Condicion</th>
                </tr>
                <tr>
                    <th>1A</th>
                    <th>1B</th>
                    <th>2A</th>
                    <th>2B</th>
                    <th>3A</th>
                    <th>3B</th>
                    <th>3C</th>
                </tr>
            </thead>
            <tbody id="cuerpoTabla">
                
            </tbody>
        </table>
    </div>
    <script src="<?php echo constant('URL');?>public/js/jquery.js"></script>
    <script src="<?php echo constant('URL');?>public/js/funciones.js?<?php echo constant('VERSION')?>"></script>
    <script src="<?php echo constant('URL');?>public/js/movimientos.js?<?php echo constant('VERSION')?>"></script>
</body>
</html>