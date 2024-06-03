<?php
    class Combustible extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaItemsCombustible = "";
            $this->view->listaCostos = "";
            $this->view->render('combustible/index');
        }
        
    }
?>