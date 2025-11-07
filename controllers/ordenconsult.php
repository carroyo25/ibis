<?php
    class OrdenConsult extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
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

        function exporta() {
            echo json_encode($this->model->exportar($_POST['detalles']));
        }

        function listaScroll(){
            $pagina = $_POST['pagina'] ?? 1;
            $cantidad = 30;
        
            echo json_encode([$this->model->listarOrdenConsultScroll($pagina,$cantidad)]);
        }

        function listaOrdenesPaginador(){
            echo json_encode($this->model->listarOrdenes( $_SESSION['iduser'] ));
        }
    }
?>