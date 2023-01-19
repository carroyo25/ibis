<?php
    class Valorizado extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaCostosSelect = $this->model->costosPorUsuarioSelect($_SESSION['iduser']);
            $this->view->render('valorizado/index');
        }

        function consulta(){
            echo $this->model->listarOrdenes($_POST);
        }

        function exportar(){
            echo $this->model->exportarValorizado($detalles);
        }
    }
?>