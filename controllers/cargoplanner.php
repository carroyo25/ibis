<?php
    class Cargoplanner extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaCostos = $this->model->costosPorUsuarioSelect($_SESSION['iduser']);
            $this->view->render('cargoplanner/index');
        }

        function filtroCargoPlan(){
            echo $this->model->listarCargoPlan($_POST);
        }

        function export() {
            echo json_encode($this->model->exportExcel($_POST['registros']));
        }

        function resumen() {
            echo json_encode($this->model->consultaResumen($_POST['orden']));
        }
    }
?>