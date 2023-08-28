<?php
    class Series extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaCostosSelect = $this->model->costosPorUsuarioSelect($_SESSION['iduser']);
            $this->view->render('series/index');
        }

        function consulta(){
            echo $this->model->grupoProyectosSerie($_POST['costos'],$_POST['serie'],$_POST['descripcion']);
        }
        
    }
?>