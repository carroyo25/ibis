<?php
    class Recepcion extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaNotasIngreso = $this->model->listarNotas();
            $this->view->listaAlmacen = $this->model->listarAlmacen();
            $this->view->listaAprueba = $this->model->apruebaRecepción();
            $this->view->listaMovimiento = $this->model->listarParametros(12);
            $this->view->listaCostosSelect = $this->model->costosPorUsuarioSelect($_SESSION['iduser']);
            $this->view->render('recepcion/index');
        }

        function actualizaNotas(){
            echo $this->model->listarNotas();
        }

        function items(){
            echo $this->model->importarItems();
        }

        function ordenId(){
            echo json_encode($this->model->consultarOrdenIdRecepcion($_POST['id']));
        }

        function ordenes(){
            echo $this->model->listarOrdenes(1);
        }

        function nuevoIngreso(){
            echo json_encode($this->model->insertar($_POST['cabecera'],$_POST['detalles'],$_POST['series']));
        }

        function modificarRegistro(){
            echo json_encode($this->model->modificarRegistro($_POST['cabecera'],$_POST['detalles']));
        }

        function adjuntos(){
            echo $this->model->subirAdjuntos($_POST['nroIngreso'],$_FILES['uploadAtach']);
        }

        function documentopdf(){
            echo $this->model->generarPdf($_POST['cabecera'],$_POST['detalles'],$_POST['condicion']);
        }

        function consultaId(){
            echo json_encode($this->model->consultarNotaID($_POST['id'],13));
        }

        function cierraIngreso(){
            echo $this->model->cerrar($_POST['cabecera'],$_POST['detalles']);
        }

        function envioProveedor(){
            $this->model->enviarCorreIngreso($_POST['cabecera'],$_POST['detalles'],$_POST['condicion']);
        }

        function filtroRecepcion(){
            echo $this->model->filtrarNotasIngreso($_POST);
        }

        function ordenesPorCosto(){
            echo $this->model->mostrarOrdenes($_POST['costo']);
        }

        function filtraOrden() {
            echo $this->model->filtrarOrdenesID($_POST['id']);
        }

        function verAdjuntos(){
            echo json_encode($this->model->verAdjuntosOrden($_POST['id']));
        }
    }
?>