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
            echo json_encode($this->model->exportarValorizado($_POST['detalles']));  
        }

        function adjuntosArchivos() {
            echo json_encode($this->model->listarAdjuntos($_POST['orden']));
        }

        function adjuntosCarpeta(){
            echo json_encode($this->model->listarAdjuntosCarpeta($_POST));
        }
    }
?>