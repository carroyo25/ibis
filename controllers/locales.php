<?php
    class Locales extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaCostosSelect = $this->model->costosPorUsuarioSelect($_SESSION['iduser']);
            $this->view->listaRecepciona = $this->model->listarPersonalRol(4);
            $this->view->listaCompras = $this->model->listarCompras("");
            $this->view->listaCostos = $this->model->costosPorUsuario($_SESSION['iduser']);
            $this->view->render('locales/index');
        }

        function pedidos(){
            echo $this->model->listarPedidosComprasLocales($_POST['cc'],$_POST['pedido']);
        }

        function items(){
            echo json_encode($this->model->itemsCompra($_POST['indice'],$_POST['origen']));
        }
        
    }
?>