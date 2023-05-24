<?php
    class Repoager extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaCostos = "";
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
    }
?>