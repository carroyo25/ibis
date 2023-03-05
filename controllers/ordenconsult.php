<?php
    class OrdenConsult extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaAlmacenes = $this->model->listarAlmacen();
            $this->view->listaTransportes = $this->model->listarParametros("08");
            $this->view->listaMonedas =  $this->model->listarParametros("03");
            $this->view->listaPagos = $this->model->listarParametros("11");
            $this->view->listaOrdenes = $this->model->listarOrdenes($_SESSION['iduser']);
            $this->view->listaEntidades = $this->model->listarEntidades();
            $this->view->listaCostosSelect = $this->model->costosPorUsuarioSelect($_SESSION['iduser']);
            $this->view->render('ordenconsult/index');
        }

        function pedidos(){
            echo $this->model->importarPedidos();
        }

        function datosPedido(){
            echo json_encode($this->model->verDatosCabecera($_POST['pep']));
        }

        function vistaPreliminar(){
            echo $this->model->generarDocumento($_POST['cabecera'],$_POST['condicion'],$_POST['detalles']);
        }

        function ordenId(){
            echo json_encode($this->model->consultarOrdenId($_POST['id']));
        }

        function listaFiltrada() {
            echo $this->model->ordenfiltrar($_POST);
        }
    }
?>