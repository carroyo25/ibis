<?php
    class Cargoplan extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaItems = $this->model->listarCargoPlan();
            $this->view->listaAlmacen = $this->model->selectAlmacen();
            $this->view->listaCostos = $this->model->selectCostos();
            $this->view->render('cargoplan/index');
        }
        
    }
?>