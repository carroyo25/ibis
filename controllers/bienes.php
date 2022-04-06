<?php
    class Bienes extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaItems = $this->model->listarItems();
            $this->view->listaTipos = $this->model->listarParametros("07");
            $this->view->render('bienes/index');
        }

        function grupos(){
            echo $this->model->mostrarGrupos($_POST['id']);
        }

        function clases(){
            echo $this->model->obtenerClases($_POST['id']);
        }

        function familias(){
            echo $this->model->obtenerFamilias($_POST['grupo'],$_POST['clase']);
        }

        function unidades(){
            echo $this->model->obtenerUnidades();
        }

        function codigo(){
            echo $this->model->generarCodigo($_POST['codigo']);
        }

        function nuevoItem(){
            echo json_encode($this->model->insertar($_POST['datos']));
        }

        function modificaItem(){
           echo json_encode($this->model->modificar($_POST['datos']));
        }

        function foto(){
            $archivo	= $_FILES['image_product'];
            $codigo     = $_POST['codigo'];

            $this->model->subirFoto($archivo,$codigo);
        }

        function itemsId(){
            echo json_encode($this->model->consultarId($_POST['id']));
        }

        public function desactivaItem(){
            echo $this->model->eliminaItem($_POST['id']);
        }

        public function actualizaTabla(){
            echo  $this->model->listarItems();
        }
        
    }
?>