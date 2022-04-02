<?php
    class Familias extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaFamilias = "";
            $this->view->render('familias/index');
        }
        
    }
?>