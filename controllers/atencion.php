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

        function consultaId(){
            echo json_encode($this->model->consultarReqId($_POST['id'],51,51,51));
        }

        function existenciaProducto(){
            echo $this->model->almacenUsuario($_POST['id']);
        }

        function buscaRol(){
            echo $this->model->buscarRol($_POST['rol'],$_POST['cc']);
        }

        function correos(){
            echo json_encode($this->model->enviarMensajeAprobacion($_POST['asunto'],
                                                          $_POST['mensaje'],
                                                          $_POST['correos'],
                                                          $_POST['pedido'],
                                                          $_POST['detalles'],
                                                          $_POST['estado'],
                                                          $_POST['adjunto']));
        }

        function actualizaListado(){
            echo $this->model->listarPedidos();
        }

        function culminaPedido(){
            echo $this->model->cerrarPedido($_POST['id'],$_POST['estado'],$_POST['detalles']);
        }
        
    }
?>