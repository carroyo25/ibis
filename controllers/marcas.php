<?php
    class Marcas extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaCostos = $this->model->costosPorUsuario($_SESSION['iduser']);
            $this->view->listaCostosSelect = $this->model->costosPorUsuarioSelect($_SESSION['iduser']);
            $this->view->render('marcas/index');
        }

        function listaScroll(){
            $pagina = $_POST['pagina'] ?? 1;
	        $cantidad = 30;

            echo json_encode([$this->model->listarPedidosScrollMarca($pagina,$cantidad)]);
        }

        function marcaAtencion(){
            echo json_encode($this->model->marcarItems($_POST['cabecera'],$_POST['detalles'],$_POST['user']));
        }

        function filtro() {
            echo $this->model->listarPedidosMarca($_POST);
        }
        
    }
?>