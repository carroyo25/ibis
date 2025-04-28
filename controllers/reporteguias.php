<?php
    class reporteguias extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            //$this->view->listaGuias = 
            $this->view->render('reporteguias/index');
        }

        function listaGuias(){
            echo json_encode($this->model->listarGuias($_POST));
        }
        
    }
?>