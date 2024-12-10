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
            echo json_encode($this->model->consultaResumen($_POST['orden'],$_POST['refpedido'],$_POST['despacho']));
        }

        function dataExcelTotalCargoPlan(){
            echo json_encode($this->model->exportarTotal($_POST['estado']));
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

        function vistaDespachos() {
            echo $this->model->generarGuiaRemision($_POST['id']);
        }

        function vistaRegistros(){
            echo json_encode($this->model->verAdjuntosDocs($_POST['id'],$_POST['tipo']));
        }

        function proyectos(){
            echo $this->model->listarProyectosFiltro();
        }

        function archivocvs(){
            echo json_encode($this->model->exportarcsv($_POST['usuario']));
        }

        function filtroCargoPlanExporta(){
            echo json_encode($this->model->filtrarExportarTotal($_POST));
        }

        function exceljs() {
            echo json_encode($this->model->exportarRapido());
        }
    }
?>