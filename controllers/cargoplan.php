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

        function export() {
            echo json_encode($this->model->exportExcel($_POST['registros']));
        }

        function resumen() {
            echo json_encode($this->model->consultaResumen($_POST['orden'],$_POST['refpedido']));
        }
    }
?>