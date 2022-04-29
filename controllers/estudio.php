<?php
    class Estudio extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaPedidos = $this->model->listarPedidosCotizados();
            $this->view->render('estudio/index');
        }

        function actualizaListado(){
            echo $this->model->listarPedidosCotizados();
        }

        function consultaId(){
            echo json_encode($this->model->consultarReqId($_POST['id'],56,56,56,$_POST['item']));
        }
        
    }
?>