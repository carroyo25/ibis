<?php
    class Salida extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaNotasSalidas = "";
            //$this->view->listaMovimiento = $this->model->listarParametros(12);
            $this->view->render('salida/index');
        }

        function ingresos(){
            echo $this->model->importarIngresos();
        }

        function notaId(){
            echo json_encode($this->model->llamarNotaIngresoId($_POST['id']));
        }
        
    }
?>