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
            echo json_encode($this->model->consultarNotaID($_POST['id'],13));
        }

        function grabaCalidad(){
            echo $this->model->grabarCalidad($_POST['detalles']);
        }

        function actualizaNotas(){
            echo $this->model->listarNotasCalidad();
        }

        function liberaNota(){
            echo $this->model->liberar_nota($_POST['id'],$_POST['estado'],$_POST['detalles']);
        }
        
    }
?>