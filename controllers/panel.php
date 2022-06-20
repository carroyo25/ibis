<?php
    class Panel extends Controller{
        function __construct(){
            parent::__construct();
        }

        function render() {
            $this->view->id = $_SESSION['user'];
            $this->view->rol = $_SESSION['rol'];
            $this->view->iniciales = strtoupper($_SESSION['inicial']);
            $this->view->acordeon = $this->model->acordeon($_SESSION['iduser']);
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
    }
?>