<?php
    class reporteguias extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaGuias = $this->model->listarGuias();
            $this->view->render('reporteguias/index');
        }
        
    }
?>