<?php
    class Calidad extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaNotasIngreso = $this->model->listarNotasCalidad();
            $this->view->render('calidad/index');
        }

        function consultaId(){
            echo json_encode($this->model->consultarNotaID($_POST['id']));
        }
        
    }
?>