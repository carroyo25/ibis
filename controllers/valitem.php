<?php
    class Valitem extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaCostosSelect = $this->model->costosPorUsuarioSelect($_SESSION['iduser']);
            $this->view->render('valitem/index');
        }

        function consulta(){
            echo $this->model->consultarItems($_POST);
        }

        function exportar(){
            echo json_encode($this->model->exportarValorizadoItem($_POST['detalles']));  
        }
        
    }
?>