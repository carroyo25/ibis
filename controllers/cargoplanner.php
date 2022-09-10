<?php
    class Cargoplanner extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaCargoPlan = $this->model->listarCargoPlan();
            $this->view->render('cargoplanner/index');
        }
    }
?>