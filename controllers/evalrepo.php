<?php
    class Evalrepo extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaCostos = "";
            $this->view->listarOrdenes = $this->model->listarEvaluaciones();
            $this->view->render('evalrepo/index');
        }
        
    }
?>