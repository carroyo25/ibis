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

        function registros(){
            echo json_encode($this->model->buscarIngresos($_POST));
        }
        
        function inventarios(){
            echo json_encode($this->model->buscarInventarios($_POST));
        }
    }
?>