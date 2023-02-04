<?php
    class Transferencias extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaCostos = $this->model->costosPorUsuario($_SESSION['iduser']);
            $this->view->listaAprueba = $this->model->apruebaRecepción();
            $this->view->listaAlmacen = $this->model->listarAlmacenGuia();
            $this->view->listaMovimiento = $this->model->listarParametros(12);

            $this->view->render('transferencias/index');
        }

        function existencias(){
            echo $this->model->consultarStocks($_POST['cc'],$_POST['codigo'],$_POST['descripcion']);
        }
        
    }
?>