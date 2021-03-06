<?php
    class Familias extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaGrupo = $this->model->obtenerGrupos();
            $this->view->listaFamilias = $this->model->listarGrupos();
            $this->view->render('familias/index');
        }

        function listaClases() {
            echo $this->model->obtenerClases($_POST['id']);
        }

        function nuevaFamilia() {
            echo json_encode($this->model->insertarFamilia($_POST['datos']));
        }

        function modificaFamilia() {
            echo json_encode($this->model->modificarFamilia($_POST['datos']));
        }

        function familiaId(){
            echo json_encode($this->model->consultaId($_POST['id']));
        }

        function actualizaTabla(){
            echo $this->model->listarGrupos();
        }

        function desactivaFamilia(){
            echo $this->model->eliminaFamilia($_POST['id']);
        }

        function codigo(){
            echo $this->model->crearCodigoClase($_POST['grupo'],$_POST['clase']);
        }
        
    }
?>