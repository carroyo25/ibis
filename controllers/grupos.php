<?php
    class Grupos extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->render('grupos/index');
        }

        function nuevoGrupo(){
            echo json_encode($this->model->insertar($_POST['datos']));
        }

        function modificaGrupo(){
            echo json_encode($this->model->modificar($_POST['datos']));
        }

        function actualizaTabla(){
            $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
            $limit = 15;
            
            $resultado = $this->model->listarGruposPaginados($_POST, $page, $limit);
            echo json_encode($resultado);
        }

        function consultaId(){
            echo json_encode($this->model->consultarId($_POST['id']));
        }

        function desactivaGrupo(){
            echo $this->model-> eliminar($_POST['id']);
        }
        
    }
?>