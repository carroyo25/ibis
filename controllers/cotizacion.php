<?php
    class Cotizacion extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaPedidos = $this->model->listaPedidos();
            $this->view->render('cotizacion/index');
        }

        function consultaId(){
            echo json_encode($this->model->consultarReqId($_POST['id'],54,55,54));
        }
        
        function proveedores(){
            echo $this->model->listarProveedores();
        }

        function mensajeCorreo(){
            echo json_encode($this->model->enviarCorreo($_POST['pedido'],
                                                        $_POST['detalles'],
                                                        $_POST['correos'],
                                                        $_POST['asunto'],
                                                        $_POST['mensaje'],
                                                        $_POST['estado']));
        }
    }
?>