<?php
    class Stocks extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaItems = $this->model->listarItems();
            $this->view->render('stocks/index');
        }
        
    }
?>