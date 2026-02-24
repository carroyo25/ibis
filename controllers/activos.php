<?php
    class Activos extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaCostosSelect = $this->model->costosPorUsuarioSelect($_SESSION['iduser']);
            $this->view->render('activos/index');
        }

        function lista_costos(){
            echo json_encode($this->model->listar_costos); 
        }
        
        function buscaCodigo(){
            echo json_encode($this->model->buscarCodigos($_POST));
        }
        
    }
?>