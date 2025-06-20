<?php
    class CargoPlanLogistic extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaCostos = $this->model->costosPorUsuarioSelect($_SESSION['iduser']);
            $this->view->render('cargoplanlogistic/index');
        }

        function filtroCargoPlanLogistica(){
            echo $this->model->listarCargoPlanLogistica($_POST);
        }
        
    }
?>