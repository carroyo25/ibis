<?php
    class Contratos extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaAlmacenes = $this->model->listarAlmacen();
            $this->view->listaTransportes = $this->model->listarParametros("08");
            $this->view->listaMonedas =  $this->model->listarParametros("03");
            $this->view->listaPagos = $this->model->listarParametros("11");
            $this->view->listaOrdenes = $this->model->listarOrdenes($_SESSION['iduser']);
            $this->view->listaEntidades = $this->model->listarEntidades();
            $this->view->listaCostosSelect = $this->model->costosPorUsuarioSelect($_SESSION['iduser']);
            $this->view->fechaOrden = $this->model->fechaOrden();
            $this->view->render('contratos/index');
        }
        
    }
?>