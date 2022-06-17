<?php
    class Evaluacion extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaOrdenes = $this->model->listarOrdenes();
            $this->view->render('evaluacion/index');
        }
        
        function criterios(){
            echo json_encode($this->model->llamarOrdenID($_POST['tipo'],$_POST['id']));
        }
    }
?>