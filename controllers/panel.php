<?php
    class Panel extends Controller{
        function __construct(){
            parent::__construct();
        }

        function render() {
            if (isset($_SESSION['user'])) {
                $this->view->id = $_SESSION['user'];
                $this->view->rol = $_SESSION['rol'];
                $this->view->iniciales = strtoupper($_SESSION['inicial']);
                $this->view->acordeon = $this->model->acordeon($_SESSION['iduser']);
            }else {
                header('Location: '.constant('URL'));
            }
            
            $this->view->render('panel/index');
        }

        /*$_SESSION['iduser'];
        $_SESSION['user'];
        $_SESSION['nombres'];
        $_SESSION['correo'];
        $_SESSION['cargo'];
        $_SESSION['inicial'];
        $_SESSION['rol'];*/

        function pedidos(){
            echo json_encode($this->model->listarPanelPedidos());
        }

        function ordenes(){
            echo json_encode($this->model->listarPanelOrdenes());
        }

        function pedidosxAprobar(){
            echo json_encode($this->model->listarPedidosPendientesAprobacion());
        }

        function compras(){
            echo json_encode($this->model->resumenCompras());
        }

        function resumenAlmacenSedes(){
            echo json_encode($this->model->resumenAlmacenRecepcion());
        }

        function resumenAlmacenObra(){

        }

        function cambiaClave(){
            echo $this->model->cambiarClave($_POST['clave']);
        }

        function pdfPedido() {
            echo $this->model->generateRequestPDF($_POST['pedido']);
        }

        function marcaRegistro() {
            echo $this->model->marcarAsignacion($_POST['user'],$_POST['id']);
        }
    }
?>