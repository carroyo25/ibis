<?php
    class Costos extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaVales = "";
            $this->view->render('valesentrega/index');
        }
        
    }
?>