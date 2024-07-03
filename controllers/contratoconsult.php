<?php
    class ContratoConsult extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaCostosSelect = $this->model->costosPorUsuarioSelect($_SESSION['iduser']);
            $this->view->listaContratos = $this->model->listarContratosConsulta($_SESSION['iduser']);
            $this->view->render('contratoconsult/index');
        }

        function actualizaListado() {
            echo $this->model->listarContratosConsulta($_SESSION['iduser']);
        }

        function adjuntos(){
            echo json_encode($this->model->mostrarAdjuntosContratos($_POST['codigoOrden']));
        }
        
    }
?>