<?php
    class reporteguias extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->render('reporteguias/index');
        }

        public function listaGuias(){
            // Verificar si los datos vienen en JSON
            $input = json_decode(file_get_contents('php://input'), true);
            
            if ($input) {
                $datos = $input;
            } else {
                $datos = $_POST;
            }
            
            echo json_encode($this->model->listarGuias($datos));
        }

        // En reporteguias.php - método itemsConsulta()
        public function itemsConsulta() {
            // Obtener parámetros POST
            $datos = json_decode(file_get_contents('php://input'), true);
            
            // Pasar los filtros al modelo
            $total = $this->model->contarGuias($datos); // Envía los datos como argumento
            
            echo json_encode(["total" => $total]);
        }
        
    }
?>