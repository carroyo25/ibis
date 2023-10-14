<?php
    class SegPedGen extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaCostos = $this->model->costosPorUsuario($_SESSION['iduser']);
            $this->view->listaCostosSelect = $this->model->costosPorUsuarioSelect($_SESSION['iduser']);
            $this->view->render('segpedgen/index');
        }

        function seguimientoID(){
            echo json_encode($this->model->consultarReqId($_POST['id'],49,90,49,null));
        }

        function infoPedido(){
            echo json_encode($this->model->consultarInfo($_POST['id']));
        }

        function datosOrden(){
            echo $this->model->generarVistaOrden($_POST['id']);
        }

        function filtroPedidosAdmin(){
            echo $this->model->listarPedidosFiltrados($_POST);
        }

        function listaScroll(){
            $pagina = $_POST['pagina'] ?? 1;
	        $cantidad = 30;

            echo json_encode([$this->model->listarPedidosConsultaScroll($pagina,$cantidad)]);
        }
        
    }
?>