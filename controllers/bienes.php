<?php
    class Bienes extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaBienes = "";
            $this->view->render('bienes/index');
        }
        
    }
?>