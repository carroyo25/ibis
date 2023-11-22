<?php
    class OrdenActual extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaCostosSelect = $this->model->costosPorUsuarioSelect($_SESSION['iduser']);
            $this->view->render('ordenactual/index');
        }

        function listaScroll(){
            $pagina = $_POST['pagina'] ?? 1;
            $cantidad = 30;
        
            echo json_encode([$this->model->listarOrdenActualScroll($pagina,$cantidad)]);
        }
        
    }
?>