<?php
    class Cargoplanner extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaCostos = $this->model->costosPorUsuarioSelect($_SESSION['iduser']);
            $this->view->render('cargoplanner/index');
            $_SESSION['progreso'] = 0;
        }

        function filtroCargoPlan(){
            echo $this->model->listarCargoPlan($_POST);
        }

        function export() {
            echo json_encode($this->model->exportExcel($_POST['registros']));
        }

        function resumen() {
            echo json_encode($this->model->consultaResumen($_POST['orden'],$_POST['refpedido']));
        }

        function dataExcelTotalCargoPlan(){
            echo json_encode($this->model->exportarTotal());
        }

        function totalItemsCargoPlan(){
            echo $this->model->contarItemsCargoPlan();
        }

        function itemsProcesados() {
            echo $_SESSION['progreso']++;
            session_write_close();
        }

        function vistaIngreso() {
            echo $this->model->gererarNotaIngreso($_POST['id']);
        }
    }
?>