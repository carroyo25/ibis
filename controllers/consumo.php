<?php
    class Consumo extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaCostosSelect = $this->model->costosPorUsuarioSelect($_SESSION['iduser']);
            $this->view->render('consumo/index');
        }

        function datosapi(){
            echo json_encode($this->model->buscarDatos($_POST['documento']));
        }

        function productos(){
            echo json_encode($this->model->buscarProductos($_POST['codigo']));
        }
        
    }
?>