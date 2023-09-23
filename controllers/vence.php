<?php
    class Vence extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaCostosSelect = $this->model->costosPorUsuarioSelect($_SESSION['iduser']);
            $this->view->listaVencimientos = $this->model->listarVencimientos("","","");
            $this->view->render('vence/index');
        }
        

        function consulta(){
            echo $this->model->listarVencimientos($_POST['cc'],$_POST['codigo'],$_POST['descripcion']);
        }

        function consultaItem(){
            echo $this->model->detallarItem($_POST['item'],$_POST['costos']);
        }
    }
?>