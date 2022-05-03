<?php
    class Verificacion extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaPedidos = $this->model->listarPedidos();
            $this->view->render('verificacion/index');
        }

        function consultaId(){
            echo json_encode($this->model->consultarReqId($_POST['id'],57,57,57,null));
        }

        function actualizaListado(){
            echo $this->model->listarPedidos();
        }

        function actualizaPedido(){
            echo json_encode($this->model->actCabecera($_POST['detalles'],$_POST['id']));
        }
        
    }
?>