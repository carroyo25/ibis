<?php
    class OrdenDescarga extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaCostos = "";
            $this->view->render('ordendescarga/index');
        }

        function pdf(){
            echo json_encode($this->model->descargarPdf($_POST));
        }
        
    }
?>