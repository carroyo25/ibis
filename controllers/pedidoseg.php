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

        function seguimientoID(){
            echo json_encode($this->model->consultarReqId($_POST['id'],49,90,49,null));
        }

        function infoPedido(){
            echo json_encode($this->model->consultarInfo($_POST['id']));
        }

        function datosOrden(){
            echo $this->model->generarVistaOrden($_POST['id']);
        }
        
    }
?>