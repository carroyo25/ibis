<?php
    class Grupos extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaGrupos = $this->model->listarGrupos();
            $this->view->render('grupos/index');
        }

        function nuevoGrupo(){
            echo json_encode($this->model->insertar($_POST['datos']));
        }

        function modificaGrupo(){
            echo json_encode($this->model->modificar($_POST['datos']));
        }

        function actualizaTabla(){
            echo $this->model->listarGrupos();
        }

        function consultaId(){
            echo json_encode($this->model->consultarId($_POST['id']));
        }

        function desactivaGrupo(){
            echo $this->model-> eliminar($_POST['id']);
        }
        
    }
?>