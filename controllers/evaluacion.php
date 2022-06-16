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
            echo $this->model->evaluar($_POST['rol'],$_POST['tipo']);
        }
    }
?>