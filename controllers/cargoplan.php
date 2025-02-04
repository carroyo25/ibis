<?php
    class Cargoplan extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaCostos = $this->model->costosPorUsuarioSelect($_SESSION['iduser']);
            $this->view->render('cargoplan/index');
        }

        function filtroCargoPlanConPrecio(){
            echo $this->model->listarCargoPlanPrecio($_POST);
        }

        function crearExcel() {
            echo json_encode($this->model->crearExcelPrecio($_POST));
        }

        function crearCSV() {
            echo json_encode($this->model->crearExcelPrecio($_POST));
        }

        function resumen() {
            echo json_encode($this->model->consultaResumen($_POST['orden'],$_POST['refpedido']));
        }
    }
?>