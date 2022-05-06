<?php
    class Orden extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaAlmacenes = $this->model->listarAlmacen();
            $this->view->listaOrdenes = $this->model->listarOrdenes($_SESSION['iduser']);
            $this->view->render('orden/index');
        }

        function pedidos(){
            echo $this->model->importarPedidos();
        }
        
    }
?>