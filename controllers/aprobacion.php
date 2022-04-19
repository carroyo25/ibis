<?php
    class Aprobacion extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaPedidos = $this->model->listarPedidos();
            $this->view->render('aprobacion/index');
        }

        function consultaId(){
            echo json_encode($this->model->consultarReqId($_POST['id'],53,53,53));
        }

        function adjuntos(){
            echo json_encode($this->model->llamarAdjuntos($_POST['id']));
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
                                                          $_POST['adjunto'],
                                                          $_POST['cabecera']));
        }
    }
?>