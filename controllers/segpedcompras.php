<?php
    class SegPedCompras extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaCostos = $this->model->costosPorUsuarioSelect($_SESSION['iduser']);
            //$this->view->listaPedidos = $this->model->listarPedidosSeguimientoCompras();
            $this->view->listaOperadores = $this->model->listarOperadores();
            $this->view->estadosCompra = $this->model->estadosCompras();
            $this->view->listaCostosSelect = $this->model->costosPorUsuarioSelect($_SESSION['iduser']);
            $this->view->render('segpedcompras/index');
        }

        function consultarPedidos(){
            echo json_encode($this->model->listarPedidosSeguimientoCompras($_POST));
        }

        function consultaId(){
            echo json_encode($this->model->consultarReqId($_POST['id'],54,230,54,null));
        }

        function estadocompra(){
            echo json_encode($this->model->actualizarEstado($_POST));
        }
    }
?>