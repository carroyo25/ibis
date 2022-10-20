<?php
    class PedidoSeg extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaCostos = $this->model->costosPorUsuario($_SESSION['iduser']);
            $this->view->listaCostosSelect = $this->model->costosPorUsuarioSelect($_SESSION['iduser']);
            $this->view->listaPedidos = $this->model->listarPedidosUsuario();
            $this->view->render('pedidoseg/index');
        }
        
    }
?>