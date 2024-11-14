<?php
    class Stocks extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaCostosSelect = $this->model->costosPorUsuarioSelect($_SESSION['iduser']);
            $this->view->render('stocks/index');
        }

        function nuevoRegistro(){
            echo json_encode($this->model->nuevoRegistro());
        }

        function grabaRegisto() {
            echo json_encode($this->model->grabarRegistro($_POST['cabecera'],$_POST['detalles']));
        }

        function importarItems(){
            echo json_encode($this->model->importFromXsl($_FILES['fileUpload']));
        }

        function resumen() {
            echo json_encode($this->model->obtenerResumen($_POST['codigo'],$_POST['cc']));
        }

        function exporta() {
            echo json_encode($this->model->exportarExcel($_POST['detalles']));
        }

        function consulta(){
            echo $this->model->listarItems($_POST);
        }

        function minimo(){
            echo json_encode($this->model->registrarMinimo($_POST));
        }

        function conteo(){
            echo $this->model->contarRegistros();
        }

        function vueltas() {
            echo $this->model->nrovueltas($_POST);
        }

        function pedidos() {
            echo json_encode($this->model->registroPedidos($_POST['cc'],$_POST['id']));
        }
        
    }
?>