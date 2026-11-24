<?php
    class Bienes extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->render('bienes/index');
        }

        function listaPaginada() {
            $criterio = $_POST['criterio'] ?? "";
            $page = $_POST['page'] ?? 1;
            $porPagina = $_POST['porPagina'] ?? 15;

            $resultado = $this->model->listarItemsPaginado($criterio, $page, $porPagina);
            
            // Devolvemos JSON puro
            header('Content-Type: application/json');
            echo json_encode($resultado);
        }

        function jerarquia() {
            header('Content-Type: application/json');
            echo json_encode($this->model->obtenerJerarquiaCompleta());
        }

        function codigo(){
            echo $this->model->generarCodigo($_POST['codigo']);
        }

        function nuevoItem(){
            // Recibimos el string JSON que viene del JS
            $json = $_POST['datos'];
            
            // Lo decodificamos a un array asociativo de PHP
            $datos = json_decode($json, true);

            // Verificamos que la decodificación fue exitosa
            if ($datos === null) {
                echo json_encode(["respuesta" => false, "mensaje" => "Datos inválidos", "clase" => "mensaje_error"]);
                return;
            }

            // Pasamos el array al modelo
            echo json_encode($this->model->insertar($datos));
        }

        function enviarCorreo() {
            // Recibimos los datos
            $codigosJson = $_POST['codigos'];
            $correoDestino = $_POST['correo'];

            // Decodificamos el JSON de los códigos
            $codigos = json_decode($codigosJson, true);

            if (empty($codigos) || empty($correoDestino)) {
                echo json_encode(["respuesta" => false, "mensaje" => "Datos incompletos", "clase" => "mensaje_error"]);
                return;
            }

            // Aquí llamamos al modelo para que arme y envíe el correo
            $resultado = $this->model->enviarCorreoCodigos($codigos, $correoDestino);
            
            echo json_encode($resultado);
        }

        function modificaItem(){
           echo json_encode($this->model->modificar($_POST['datos']));
        }

        
        function itemsId(){
            echo json_encode($this->model->consultarId($_POST['id']));
        }

        function desactivaItem(){
            echo json_encode($this->model->eliminaItem($_POST['codigo']));
        }

        function activaItem(){
            echo $this->model->activarItem($_POST['id']);
        }

        function buscaPalabra(){
            echo json_encode($this->model->buscarItemsPalabra($_POST['criterio']));
        }
        
    }
?>