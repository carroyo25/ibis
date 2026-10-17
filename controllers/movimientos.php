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

        function almacen(){
            echo json_encode($this->model->buscarAlmacen($_POST));
        }

        function buscar(){
            echo json_encode($this->model->buscarValores($_POST));
        }

        function guia(){
            try {
                // 1. Leer el JSON que envía el front
                $raw  = file_get_contents('php://input');
                $data = json_decode($raw, true);

                if (!$data) {
                    echo json_encode(['success' => false, 'error' => 'JSON inválido']);
                    return;
                }

                // 2. Pasar los datos al modelo
                $resultado = $this->model->emitirGuia($data);

                // 3. Devolver la respuesta al front
                echo json_encode($resultado);

            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
        }
        
    }
?>