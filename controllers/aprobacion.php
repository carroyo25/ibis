<?php
    class Aprobacion extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaPedidos = $this->model->listarPedidos();
            $this->view->listaCostosSelect = $this->model->costosPorUsuarioSelect($_SESSION['iduser']);
            $this->view->render('aprobacion/index');
        }

        function consultaId(){
            echo json_encode($this->model->consultarReqId($_POST['id'],53,53,53,null));
        }

        function adjuntos(){
            echo json_encode($this->model->llamarAdjuntos($_POST['id']));
        }

        function buscaRol(){
            echo $this->model->buscarRol($_POST['rol'],$_POST['cc']);
        }
        
        function confirma(){
            echo json_encode($this->model->aprobarPedido($_POST['cabecera'],
                                                        $_POST['detalles'],
                                                        $_POST['estado'],
                                                        $_POST['pedido']));
        }

        function actualizaListado(){
            echo $this->model->listarPedidos();
        }

        function filtroPedidos(){
            echo $this->model->filtroAprobados($_POST);
        }

        function anulapedido() {
            echo $this->model->anularPedido($_POST['id']);
        }
    }
?>