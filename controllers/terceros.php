<?php
    class Terceros extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaCostosSelect = $this->model->costosPorUsuarioSelect($_SESSION['iduser']);
            $this->view->render('terceros/index');
        }

        function datosapi(){
            echo json_encode($this->model->buscarDatosTerceros($_POST['documento'],$_POST['costos']));
        }
        
    }
?>