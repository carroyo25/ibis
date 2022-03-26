<?php
    class Proveedores extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->render('proveedores/index');
        }
        
    }
?>