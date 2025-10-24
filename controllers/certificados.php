<?php
    class Certificados extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaCostosSelect = $this->model->costosPorUsuarioSelect($_SESSION['iduser']);
            $this->view->listaCostos = $this->model->costosPorUsuario($_SESSION['iduser']);
            $this->view->carpetasCertificados = $this->model->listaCertificados($_SESSION['iduser'],"");
            $this->view->render('certificados/index');
        }

        function adjuntos(){
            echo json_encode($this->model->listarAdjuntos($_POST['id']));
        }

        function filtroCertificado(){
            echo json_encode($this->view->carpetasCertificados = $this->model->listaCertificados($_POST['user'],$_POST['orden']));
        }
    }
?>