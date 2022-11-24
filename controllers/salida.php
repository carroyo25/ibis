<?php
    class Salida extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaNotasSalidas = $this->model->listarNotasDespacho();
            $this->view->listaEnvio = $this->model->listarParametros('08');
            $this->view->listaAprueba = $this->model->apruebaRecepción();
            $this->view->listaAlmacen = $this->model->listarAlmacenGuia();
            //$this->view->listaEntidad = $this->model->listarEntidades();
            //$this->view->listaModalidad = $this->model->listarParametros(14);
            //$this->view->listaPersonal = $this->model->listarPersonalRol(4);
            $this->view->listaMovimiento = $this->model->listarParametros(12);
            //$this->view->listaCostos = $this->model->costosPorUsuario($_SESSION['iduser']);
            $this->view->listaCostosSelect = $this->model->costosPorUsuarioSelect($_SESSION['iduser']);

            $this->view->render('salida/index');
        }

        function ingresos() {
            echo $this->model->listarIngresos();
        }

        function actualizaDespachos(){

        }

        function filtraIngreso(){
            echo $this->model->filtrarIngresos($_POST['id']);
        }

        function llamarData() {
            echo $this->model->importarItems($_POST);
        }
  
    }
?>