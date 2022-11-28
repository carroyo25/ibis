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
            $this->view->listaEntidad = $this->model->listarEntidades();
            $this->view->listaModalidad = $this->model->listarParametros(14);
            $this->view->listaPersonal = $this->model->listarPersonalRol(4);
            $this->view->listaMovimiento = $this->model->listarParametros(12);
            $this->view->listaCostos = $this->model->costosPorUsuario($_SESSION['iduser']);
            $this->view->listaCostosSelect = $this->model->costosPorUsuarioSelect($_SESSION['iduser']);

            $this->view->render('salida/index');
        }

        function ordenes(){
            echo $this->model->listarOrdenes(2);
        }

        function actualizaDespachos(){

        }

        function documentopdf(){
            echo $this->model->generarPdfSalida($_POST['cabecera'],$_POST['detalles'],$_POST['condicion']);
        }

        function vistaPreviaGuiaRemision(){
            echo json_encode($this->model->generarVistaPrevia($_POST['cabecera'],$_POST['detalles'],$_POST['proyecto']));
        }

        function preImpreso(){
            echo json_encode($this->model->imprimirFormato($_POST['cabecera'],$_POST['detalles'],$_POST['proyecto']));
        }

        function nuevasalida(){
            echo json_encode($this->model->grabarDespacho($_POST['cabecera'],$_POST['detalles']));
        }

        function ordenId() {
            echo json_encode($this->model->pasarDetallesOrden($_POST['id'],$_POST['costo']));
        }
        
        function filtraOrden() {
            echo $this->model->filtrarOrdenesID($_POST['id']);
        }
    }
?>