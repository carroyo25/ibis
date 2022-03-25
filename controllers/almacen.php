<?php
    class Almacen extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaDepartamento = $this->model->getUbigeo(1,"%");
            $this->view->listaAlmacenes = $this->model->listarAlmacen();
            $this->view->render('almacen/index');
        }

        function ubigeo(){
            $nivel = $_POST['nivel'];
            $prefijo = $_POST['prefijo'];

            echo $this->model->getUbigeo($nivel,$prefijo."%");
        }

        function nuevoAlmacen(){
            $datos = $_POST['datos'];

            echo json_encode($this->model->insertarAlmacen($datos));
        }

        function modificaAlmacen(){
            $datos = $_POST['datos'];

            echo json_encode($this->model->modificarAlmacen($datos));
        }

        function actualizaTabla(){
            echo $this->model->listarAlmacen(); 
        }

        function consultaId(){
            $id = $_POST['id'];

            echo json_encode($this->model->idAlmacen($id));
        }

        function desactivaAlmacen(){
            $id = $_POST['id'];

            echo $this->model->borrarAlmacen($id);
        }
    }
?>