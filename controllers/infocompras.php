<?php
    class infoCompras extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaCostos = "";
            $this->view->render('infocompras/index');
        }
        
    }
?>