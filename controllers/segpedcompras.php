<?php
    class SegPedCompras extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaCostos = $this->model->costosPorUsuarioSelect($_SESSION['iduser']);
            $this->view->listaPedidos = $this->model->listarPedidosSeguimientoCompras();
            $this->view->listaOperadores = $this->model->listarOperadores();
            $this->view->listaCostosSelect = $this->model->costosPorUsuarioSelect($_SESSION['iduser']);
            $this->view->render('segpedcompras/index');
        }

        function consultarPedidos(){
            echo json_encode($this->model->listarPedidosSeguimientoCompras());
        }
        
    }
?>