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
            echo json_encode($this->model->insertarFamilia());
        }

        function modificaFamilia() {
            echo json_encode($this->model->modificarFamilia());
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

        function listaJerarquia(){
            // Le decimos al navegador que la respuesta es un JSON
            header('Content-Type: application/json; charset=utf-8');
            
            // Imprimimos lo que devuelve el modelo
            echo $this->model->listarJerarquia();
        }

        function clasesGrupo(){
            // Le decimos al navegador que la respuesta es un JSON
            header('Content-Type: application/json; charset=utf-8');

            echo $this->model->clasesPorGrupo();
        }

        function nuevoCodigo(){
            echo json_encode($this->model->generarCodigoFamilia());
        }
        
    }
?>