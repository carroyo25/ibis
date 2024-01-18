<?php
    class Evalrepo extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaCostos = "";
            $this->view->listarOrdenes = "";
            $this->view->listaCostosSelect = $this->model->costosPorUsuarioSelect($_SESSION['iduser']);
            $this->view->render('evalrepo/index');
        }

        function evaluaciones(){
            echo $this->model->crearReporte($_POST);
        }

        function evaluacionesExcel(){
            echo json_encode($this->model->crearExcel($_POST));
        }
        
    }
?>