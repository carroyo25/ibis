<?php
    class repoProve extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaOrdenes = $this->model->listarOrdenesProveedor("");
            $this->view->render('repoprove/index');
        }
        
    }
?>