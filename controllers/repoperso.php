<?php
    class Repoperso extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaCostosSelect = $this->model->costosPorUsuarioSelect($_SESSION['iduser']);
            $this->view->render('repoperso/index');
        }

        function datosapi(){
            echo json_encode($this->model->consultarDatos($_POST['documento'],$_POST['costos'],$_POST['codigo']));
        }

        function buscaCodigo(){
            echo $this->model->grupoProyectos($_POST['documento'],$_POST['codigo']);
        }
        
    }
?>