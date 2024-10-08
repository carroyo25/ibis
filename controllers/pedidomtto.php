<?php
    class PedidoMtto extends Controller{
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
            $this->view->listaPedidos = $this->model->listarPedidosUsuario();
            $this->view->render('pedidomtto/index');
        }

        function registroEquipos(){
            echo json_encode($this->model->listarEquipos());
        }

        function nuevoPedido(){
            echo json_encode($this->model->insertarMtto($_POST['cabecera'],$_POST['detalles']));
        }

        function consultaId(){
            echo json_encode($this->model->consultarReqIdMtto($_POST['id'],49,50,49,null));
        }

        function modificaPedido(){
            echo json_encode($this->model->modificar($_POST['cabecera'],$_POST['detalles']));
        }
        
    }
?>