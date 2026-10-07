<?php
    class Movimientos extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaCostosSelect = $this->model->costosPorUsuarioSelectMovimientos($_SESSION['iduser']);
            $this->view->render('movimientos/index');
        }

        function consulta(){
            echo json_encode($this->model->listarStocks($_POST));
        }
        
    }
?>