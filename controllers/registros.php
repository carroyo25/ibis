<?php
    class Registros extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaCostosSelect = $this->model->costosPorUsuarioSelect($_SESSION['iduser']);
            $this->view->listaRecepciona = $this->model->listarPersonalRol(4);
            $this->view->listaIngresos = $this->model->listarIngresos("");
            $this->view->render('registros/index');
        }

        function consultaID(){
            echo json_encode($this->model->importarDespacho($_POST['indice']));
        }
        
        function nuevoRegistro(){
            echo json_encode($this->model->grabarRegistros($_POST['cabecera'],
                                                            $_POST['detalles']));
        }

        function actualizarRegistros(){
            echo $this->model->listarIngresos("");
        }

        function despachos() {
            echo $this->model->listarDespachos($_POST['guia']);
        }

        function registroID(){
            echo json_encode($this->model->consultarID($_POST['id']));
        }

        function filtro(){
            echo $this->model->listarIngresos($_POST);
        }
    }
?>