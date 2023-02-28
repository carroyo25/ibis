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
            $this->view->listaEntidad = $this->model->listarEntidades();
            $this->view->listaModalidad = $this->model->listarParametros(14);
            $this->view->listaPersonal = $this->model->listarPersonalRol(4);
            $this->view->listaEnvio = $this->model->listarParametros('08');

            $this->view->render('transferencias/index');
        }

        function existencias(){
            echo $this->model->consultarStocks($_POST['cc'],$_POST['codigo'],$_POST['descripcion']);
        }

        function pedidos(){
            echo $this->model->listarPedidosAtencion();
        }

        function items(){
            echo $this->model->consultarPedidos($_POST['indice'],$_POST['origen']);
        }

        function registro() {
            echo $this->model->insertarTransferencia($_POST['cabecera'],$_POST['detalles']);
        }
        
    }
?>