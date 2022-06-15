<?php
    class Evaluacion extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaOrdenes = "";
            $this->view->render('evaluacion/index');
        }
        
    }
?>