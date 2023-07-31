<?php
    class Repoager extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaCostosSelect = $this->model->costosPorUsuarioSelector($_SESSION['iduser']);
            $this->view->clases     = $this->model->listarClasesReporte();
            $this->view->tipos      = $this->model->listarTipos(43);
            $this->view->familias   = $this->model->tablaFamilias(43,118);
            $this->view->items      = $this->model->tablaItems(43,118,437);
            $this->view->mes        = $this->model->mesActual();
            $this->view->render('repoager/index');
        }
        
        function tipos(){
            echo $this->model->listarTipos($_POST['id']);
        }

        function clases(){
            echo json_encode($this->model->tablaFamilias($_POST['grupo'],$_POST['clase']),JSON_NUMERIC_CHECK);
        }

        function items(){
            echo json_encode($this->model->tablaItems($_POST['grupo'],$_POST['clase'],$_POST['familia']));
        }

        function graficoLineas() {
            echo json_encode($this->model->dibujarLineas($_POST['grupo'],$_POST['clase'],$_POST['familia'],$_POST['producto']),JSON_NUMERIC_CHECK);
        }

        function consultaGrupos() {
            echo json_encode($this->model->consultarGrupos($_POST['cc'],$_POST['anio'],$_POST['mes']),JSON_NUMERIC_CHECK);
        }

        function consultaClases() {
            echo json_encode($this->model->consultarClases($_POST['cc'],$_POST['gr'],$_POST['anio'],$_POST['mes']),JSON_NUMERIC_CHECK);
        }

        function consultaFamilias() {
            echo json_encode($this->model->consultarFamilias($_POST['cc'],$_POST['gr'],$_POST['cl'],$_POST['anio'],$_POST['mes']),JSON_NUMERIC_CHECK);
        }

        function consultaItems() {
            echo json_encode($this->model->consultarItems($_POST['cc'],$_POST['fam'],$_POST['an'],$_POST['mm']),JSON_NUMERIC_CHECK);
        }
    }
?>