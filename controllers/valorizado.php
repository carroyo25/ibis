<?php
    class Valorizado extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaCostosSelect = $this->model->costosPorUsuarioSelect($_SESSION['iduser']);
            $this->view->valorizado = $this->model->listarOrdenes(30);
            $this->view->render('valorizado/index');
        }
        
    }
?>