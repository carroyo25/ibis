<?php
    class PedidoEdit extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaCostos = $this->model->costosPorUsuario($_SESSION['iduser']);
            $this->view->listaCostosSelect = $this->model->costosPorUsuarioSelect($_SESSION['iduser']);
            $this->view->listaAreas = $this->model->obtenerAreas();
            $this->view->listaTipos = $this->model->listarParametros("07");
            $this->view->listaTransportes = $this->model->listarParametros("08");
            $this->view->listaAquarius  = $this->model->listarAquarius();

            $this->view->render('pedidoedit/index');
        }

        function consultaRqAdmin(){
            echo json_encode($this->model->consultarReqIdAdmin($_POST['id'],49,54,49,null));
        }

        function adjuntos(){
            echo $this->model->subirAdjuntos($_POST['nropedidoatach'],$_FILES['uploadAtach']);
        }

        function vistaPrevia(){
            echo $this->model->generarPedido($_POST['cabecera'],$_POST['detalles']);
        }

        function grabaPedidoAdmin(){
            echo json_encode($this->model->grabarPedidoAdmin($_POST['cabecera'],$_POST['detalles']));
        }

        function actualizaListado(){
            echo $this->model->listarPedidosUsuario("");
        }

        function filtraItems(){
            echo $this->model->filtrarItemsPedido($_POST['codigo'],$_POST['descripcion'],$_POST['tipo']);
        }

        function cambiaPedido() {
            echo json_encode($this->model->cambiarPedidoAdmin($_POST['id'],$_POST['valor']));
        }

        function accionItem(){
            echo json_encode($this->model->itemActualizarAdmin($_POST));
        }

        function filtro() {
            echo $this->model->listarPedidosUsuario($_POST);
        }

        function listaScroll(){
            $pagina = $_POST['pagina'] ?? 1;
	        $cantidad = 30;

            echo json_encode([$this->model->listarPedidosScroll($pagina,$cantidad)]);
        }
    }