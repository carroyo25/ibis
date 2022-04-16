<?php
    class Atencion extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaPedidos = $this->model->listarPedidos();
            $this->view->render('atencion/index');
        }
        
    }
?>