<?php
    class Terceros extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaCostosSelect = $this->model->costosPorUsuarioSelect($_SESSION['iduser']);
            $this->view->render('terceros/index');
        }

        function datosapi(){
            echo json_encode($this->model->buscarDatosTerceros($_POST['documento'],$_POST['costos']));
        }

        function productos(){
            echo json_encode($this->model->buscarProductosTerceros($_POST['codigo']));
        }

        function excelfile(){
            echo json_encode($this->model->createExcelReport($_POST['nombre'],$_POST['documento'],$_POST['empresa'],$_POST['detalles']));
        }

        function firma(){
            echo $this->model->subirFirmaTerceros($_POST['detalles']);
        }

        function kardex() {
            echo $this->model->generarKardexTerceros($_POST);
        }
        
    }
?>