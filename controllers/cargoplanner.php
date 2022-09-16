<?php
    class Cargoplanner extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaCostos = $this->model->costosPorUsuarioSelect($_SESSION['iduser']);
            $this->view->listaCargoPlan = $this->model->listarCargoPlan();
            $this->view->render('cargoplanner/index');
        }
    }
?>