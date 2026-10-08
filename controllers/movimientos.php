<?php
    class Movimientos extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaCostosSelectOrigen = $this->model->costosPorUsuarioSelectMovimientos($_SESSION['iduser']);
            $this->view->listaCostosSelectDestino = $this->model->costosPorUsuarioSelect($_SESSION['iduser']);
            $this->view->render('movimientos/index');
        }

        function consulta(){
            echo json_encode($this->model->listarStocks($_POST));
        }

        function transfiere(){
            header('Content-Type: application/json');
            $data = json_decode(file_get_contents('php://input'), true);

            echo json_encode($this->model->transferir($data));
        }
        
    }
?>