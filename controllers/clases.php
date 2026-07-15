<?php
    class Clases extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaGrupos = $this->model->obtenerGrupos();
            //$this->view->listaClases = $this->model->listarTitulosGrupos();
            $this->view->render('clases/index');
        }

        function nuevaClase(){
            echo json_encode($this->model->insertar($_POST['datos']));
        }

        function modificaClase(){
            echo json_encode($this->model->modificar($_POST['datos']));
        }

        function claseId(){
            echo json_encode($this->model->consultarGrupoId($_POST['id']));
        }

        function actualizaTabla(){
            echo $this->model->listarTitulosGrupos();
        }

        function desactivaClase(){
            echo $this->model->desactivar($_POST['id']);
        }

        function listarClases(){
            $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
            $limit = 15;
            
            $resultado = $this->model->listarGruposConClases($_POST, $page, $limit);
            echo json_encode($resultado);
        }
        
    }
?>