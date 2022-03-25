<?php
    class Proyecto extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaProyectos=$this->model->listarProyectos();
            $this->view->listaDepartamento = $this->model->getUbigeo(1,"%");
            $this->view->render('proyecto/index');
        }

        function nuevoProyecto(){
            $datos = $_POST['datos'];
            $costos = $_POST['costos'];

            echo json_encode($this->model->crearProyecto($datos,$costos));
        }

        function actualizaTabla(){
            echo $this->model->listarProyectos(); 
        }

        function consultaId() {
            $id = $_POST['id'];

            echo json_encode($this->model->consultarProyectoId($id));
        }

        function modificaProyecto(){
            $datos = $_POST['datos'];
            $costos = $_POST['costos'];

            echo json_encode($this->model->modificarProyecto($datos,$costos));
        }

        function desactivaProyecto(){
            $id = $_POST['id'];

            echo $this->model->borrarProyecto($id);
        }

        function desactivaCostos() {
            $id = $_POST['id'];

            echo $this->model->borrarCostos($id);
        }
        
    }
?>