<?php
    class GuiaUpdate extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaNotasSalidas = $this->model->listarNotasDespacho();
            $this->view->listaCostos = $this->model->costosPorUsuario($_SESSION['iduser']);
            $this->view->listaCostosSelect = $this->model->costosPorUsuarioSelect($_SESSION['iduser']);
            $this->view->render('guiaupdate/index');
        }

        function archivos(){
            echo json_encode($this->model->subirArchivosGuias($_POST['codigo'],$_FILES));
        }
        
    }
?>