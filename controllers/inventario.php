<?php
    class Inventario extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaEntradas = $this->model->listarEntradas();
            $this->view->listaRecepciona = $this->model->listarPersonalRol(4);
            $this->view->listaCostos = $this->model->costosPorUsuario($_SESSION['iduser']);
            $this->view->listaAlmacen = $this->model->listarAlmacenGuia();
            $this->view->listaMovimiento = $this->model->listarParametros(12);
            $this->view->render('inventario/index');
        }

        function nuevoRegistro(){
            echo json_encode($this->model->nuevoRegistro());
        }

        function quitarItem(){

        }

        function grabaRegistro() {
            echo json_encode($this->model->grabarRegistro($_POST['cabecera'],$_POST['detalles']));
        }

        function actualizaDetalles() {
            echo json_encode($this->model->actualizarInventario($_POST['detalles']));
        }

        function importarItems(){
            echo json_encode($this->model->importFromXsl($_FILES['fileUpload']));
        }

        function resumen() {
            echo json_encode($this->model->obtenerResumen($_POST['codigo']));
        }

        function consulta(){
            echo json_encode($this->model->consultarInventario($_POST['id']));
        }

        function xlsExport(){
            echo json_encode($this->model->exportar($_POST['detalles'])); 
        }

        function procesado() {
            echo $this->model->buscarProcesado($_POST['a']);
        }
        
    }
?>