<?php
    class CargoPlanLogistic extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaCostos = $this->model->costosPorUsuarioSelect($_SESSION['iduser']);
            $this->view->render('cargoplanlogistic/index');
        }

        function filtroCargoPlanLogistica(){
            echo json_encode($this->model->listarCargoPlanLogistica($_POST));
        }

        function exceljs(){
            echo json_encode($this->model->exportarExcel($_POST));
        }
        
    }
?>