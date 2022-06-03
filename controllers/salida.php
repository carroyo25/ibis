<?php
    class Salida extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaNotasSalidas = "";
            $this->view->render('salida/index');
        }
        
    }
?>