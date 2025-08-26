<?php
    class CajaChica extends Controller{
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
            $this->view->listaPedidos = $this->model->listarPedidosUsuario(null);
            $this->view->listaEntidades = $this->model->listarEntidades();

            $this->view->render('cajachica/index');
        }

        function consultaIdCompras(){
            echo json_encode($this->model->consultarReqIdCaja($_POST['id'],230,230,230,null));
        }

        function nuevoPedido(){
            echo json_encode($this->model->insertarCompra($_POST['cabecera'],$_POST['detalles']));
        }

        function modificaPedido(){
            echo json_encode($this->model->modificarCompra($_POST['cabecera'],$_POST['detalles']));
        }

        function actualizaListado(){
            echo $this->model->listarPedidosUsuario(null);
        }

        function filtroPedidos(){
            echo $this->model->listarPedidosUsuario($_POST);
        }
    }
?>