<?php
    class AutorizaAjuste extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaCostosSelect = $this->model->costosPorUsuarioSelect($_SESSION['iduser']);
            $this->view->listaAjustes = $this->model->listarAjustesAprobados("-1");
            $this->view->render('autorizaajuste/index');
        }

        function autoriza() {
            echo json_encode($this->model->autorizarAjuste($_POST));
        }

        function actualizaPanel(){
            echo $this->model->listarAjustesAprobados("-1");
        }
        
    }
?>