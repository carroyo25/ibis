<?php
    class Activos extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaCostosSelect = $this->model->costosPorUsuarioSelect($_SESSION['iduser']);
            $this->view->render('activos/index');
        }

        function lista_costos(){
            echo json_encode($this->model->listar_costos); 
        }
        
        function buscaCodigo(){
            echo json_encode($this->model->buscarCodigos($_POST));
        }

        function registros(){
            echo json_encode($this->model->buscarIngresos($_POST));
        }
        
        function inventarios(){
            echo json_encode($this->model->buscarInventarios($_POST));
        }

        function asignados(){
            echo json_encode($this->model->buscarAsignados($_POST));
        }

        function registro(){
            echo json_encode($this->model->registrarActivos($_POST));
        }

        function modifica(){
            echo json_encode($this->model->modificarActivos($_POST));
        }

        function registrosXls(){
            echo json_encode($this->model->registrarDeArchivo($_POST));
        }

        function consultaEquipos(){
            echo json_encode($this->model->consultarEquipos($_POST));
        }

        function editaEquipo(){
            echo json_encode($this->model->consultarIDEquipo($_POST));
        }

        function certificados(){
            echo json_encode($this->model->subirCertificados($_POST['codigo'],$_FILES));
        }

    }
?>