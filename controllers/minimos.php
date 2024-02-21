<?php
    class Minimos extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaCostosSelect = $this->model->costosPorUsuarioSelect($_SESSION['iduser']);
            $this->view->render('minimos/index');
        }

        function consultaProductos(){
            echo json_encode($this->model->listarMinimos($_POST));
        }
        
    }
?>