<?php
    class Vence extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaCostosSelect = $this->model->costosPorUsuarioSelect($_SESSION['iduser']);
            $this->view->render('vence/index');
        }
        

        function consulta(){
            echo $this->model->mostrarvencimento($_POST['cc'],$_POST['codigo']);
        }
    }
?>