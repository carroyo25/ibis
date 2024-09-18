<?php
    class Ajustes extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaRecepciona = $this->model->listarPersonalRol(4);
            $this->view->listaCostosSelect = $this->model->costosPorUsuarioSelect($_SESSION['iduser']);
            $this->view->listaAlmacen = $this->model->listarAlmacenGuia();
            $this->view->listaMovimiento = $this->model->listarParametros(12);
            $this->view->listaSalidas = $this->model->listarAjustes("-1");
            $this->view->listaCostos = $this->model->costosPorUsuario($_SESSION['iduser']);
            $this->view->render('ajustes/index');
        }

        function importarItemsAjustes(){
            echo json_encode($this->model->importFromXslAjustes($_FILES['fileUpload']));
        }

        function grabaRegistroAjustes() {
            echo json_encode($this->model->grabarRegistroAjustes($_POST['cabecera'],$_POST['detalles']));
        }

        function filtroAjustes(){
            echo $this->model->listarAjustes($_POST['costosSearch']);
        }

        function consulta(){
            echo json_encode($this->model->consultarAjuste($_POST['id']));
        }
    }
?>