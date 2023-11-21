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
            $this->view->listaCostos = $this->model->costosPorUsuario($_SESSION['iduser']);
            $this->view->render('registros/index');
        }

        function consultaID(){
            echo json_encode($this->model->importarDespacho($_POST['indice']));
        }
        
        function nuevoRegistro(){
            echo json_encode($this->model->grabarRegistros($_POST['cabecera'],
                                                            $_POST['detalles'],
                                                            $_POST['tipo']));
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

        function transferencias() {
            echo $this->model->listarTransferencias($_POST['nt']);
        }

        function transferenciasId() {
            echo json_encode($this->model->consultarTransferenciaID($_POST['id']));
        }

        function adjuntos(){
            echo json_encode($this->model->subirAdjuntos($_POST['codigo'],$_FILES));
        }
    }
?>