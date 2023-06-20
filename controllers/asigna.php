<?php
    class Asigna extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaCostos = $this->model->costosPorUsuarioSelect($_SESSION['iduser']);
            $this->view->listaPedidos = $this->model->listarPedidosAprobados();
            $this->view->listaOperadores = $this->model->listarOperadores();
            $this->view->listaCostosSelect = $this->model->costosPorUsuarioSelect($_SESSION['iduser']);
            $this->view->render('asigna/index');
        }

        function consultaId(){
            echo json_encode($this->model->consultarReqId($_POST['id'],54,54,54,null));
        }

        function actualizaListado(){
            echo $this->model->listarPedidosAprobados();
        }

        function asignaOperador(){
            echo $this->model->asignarOperador($_POST['pedido'],$_POST['detalles'],$_POST['asignado']);
        }

        function filtroPedidos(){
            echo $this->model->filtroAsigna($_POST);
        }

        function libera(){
            echo json_encode($this->model->modificarAsignacion($_POST['pedido']));
        }
        
    }
?>