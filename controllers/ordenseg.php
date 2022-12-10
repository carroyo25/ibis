<?php
    class Ordenseg extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaCostosSelect = $this->model->costosPorUsuarioSelect($_SESSION['iduser']);
            $this->view->listaOrdenes = $this->model->listarOrdenesSeguimiento($_SESSION['iduser']);
            $this->view->render('ordenseg/index');
        }
        

        function ordenId(){
            echo json_encode($this->model->consultarOrdenId($_POST['id']));

            
        }

        function datosOrden(){
            echo $this->model->generarVistaOrden($_POST['id']);
        }

        function consulta() {
            echo json_encode($this->model->consultarDetalles($_POST['id']));
        }

        function vistaPedido() {
            echo $this->model->generateRequestPDF($_POST['id']);
        }
    }
?>