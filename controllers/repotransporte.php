<?php
    class RepoTransporte extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaCostosSelect = $this->model->costosPorUsuarioSelect($_SESSION['iduser']);
            $this->view->render('repotransporte/index');
        }

        function transportes() {
            echo json_encode($this->model->listarTransportes($_POST));
        }

        function adjuntos(){
            echo json_encode($this->model->listarAdjuntos($_POST['orden']));
        }
        
    }
?>