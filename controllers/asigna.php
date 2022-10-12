<?php
    class Asigna extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaPedidos = $this->model->listarPedidosAprobados();
            $this->view->render('asigna/index');
        }
        
    }
?>