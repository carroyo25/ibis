<?php
    class AutorizaTraslado extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaCostos = $this->model->costosPorUsuario($_SESSION['iduser']);
            $this->view->listaCostosSelect = $this->model->costosPorUsuarioSelect($_SESSION['iduser']);
            $this->view->listaTraslados = $this->model->listarTrasladosAprobados(278);
            $this->view->render('autorizatraslado/index');
        }

        function actualizaListado(){
            echo ($this->model->listarTrasladosAprobados(278));
        }

        function aprueba(){
            echo json_encode($this->model->aprobarTraslado($_POST['id'],$_POST['user']));
        }
        
    }
?>