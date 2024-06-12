<?php
    class Combustible extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaAlmacen = $this->model->selectAlmacen();
            $this->view->listaCostosSelect = $this->model->costosPorUsuarioSelect($_SESSION['iduser']);
            $this->view->listaAreas = $this->model->listaAreas();
            $this->view->listaEquipos = $this->model->listaEquiposMmtto();
            $this->view->listaItemsCombustible = $this->model->listaConsumosCombustibles('%','%',2024);
            $this->view->render('combustible/index');
        }

        function codigo(){
            echo json_encode($this->model->consultarCodigo($_POST['codigo']));
        }

        function documento(){
            echo json_encode($this->model->buscarDocumento($_POST['documento']));
        }

        function registro(){
            echo json_encode($this->model->registrarCombustible($_POST));
        }
        
    }
?>