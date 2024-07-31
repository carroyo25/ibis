<?php
    class Firmas extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaOrdenes = $this->model->listarOrdenesFirmas();
            $this->view->listaCostosSelect = $this->model->costosPorUsuarioSelect($_SESSION['iduser']);
            $this->view->render('firmas/index');
        }

        function ordenId(){
            echo json_encode($this->model->consultarOrdenId($_POST['id']));
        }
        
        function comentarios(){
            echo $this->model->grabarComentarios($_POST['codigo'],$_POST['comentarios'],$_POST['usuario']);
        }

        function autoriza(){
            echo json_encode($this->model->firmar($_POST['id']));
        }

        function autorizaExpress(){
            echo json_encode($this->model->firmarExpress($_POST['id'],$_POST['numero'],$_POST['proveedor'],$_POST['pago']));
        }

        function actualizaListado() {
            echo $this->model->listarOrdenesFirmas();
        }

        function precios(){
            echo $this->model->consultarPrecios($_POST['codigo'],$_POST['descripcion']);
        }

        function vistaEmitida() {
            echo $this->model->generarDocumento($_POST['cabecera'],$_POST['condicion'],$_POST['detalles']);
        }

        function filtroFirmas(){
            echo $this->model->filtrarOrdenesFirmas($_POST);
        }
    }
?>