<?php
    class repoProve extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaOrdenes = $this->model->listarOrdenesProveedor("");
            $this->view->render('repoprove/index');
        }

        function consultarValoresLista() {
            echo json_encode($this->model->valoresFiltros($_POST['campo']));
        }

        function filtros(){
            echo json_encode($this->model->listaFiltradas($_POST));
        }

        function archivoExcel() {
            echo json_encode($this->model->crearExcelProveedores($_POST['detalles']));
        }
        
    }
?>