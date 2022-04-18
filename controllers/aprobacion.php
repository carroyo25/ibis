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
        
    }
?>