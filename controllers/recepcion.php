<?php
    class Recepcion extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaNotasIngreso = "";
            $this->view->render('recepcion/index');
        }
        
    }
?>