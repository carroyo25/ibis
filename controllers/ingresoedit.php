<?php
    class IngresoEdit extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaNotasIngreso = $this->model->listarNotas();
            $this->view->listaCostosSelect = $this->model->costosPorUsuarioSelect($_SESSION['iduser']);
            $this->view->render('ingresoedit/index');
        }

        function archivos(){
            echo json_encode($this->model->subirArchivosGuiasIngreso($_POST['codigo'],$_FILES));
        }

        function verAdjuntos(){
            echo json_encode($this->model->verAdjuntosDocs($_POST['id'],$_POST['tipo']));
        }

        function series(){
            echo json_encode($this->model->grabarSeriesIngreso($_POST['id'],$_POST['series']));
        }

        function seriesConsulta(){
            echo $this->model->mostrarSeries($_POST['id']);
        }
        
    }
?>