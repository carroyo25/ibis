<?php
    class Consumo extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaCostosSelect = $this->model->costosPorUsuarioSelect($_SESSION['iduser']);
            $this->view->render('consumo/index');
        }

        function datosapi(){
            echo json_encode($this->model->buscarDatos($_POST['documento'],$_POST['costos']));
        }

        function productos(){
            echo json_encode($this->model->buscarProductos($_POST['codigo']));
        }

        function firma(){
            echo $this->model->subirFirma($_POST['detalles']);
        }

        function consulta(){
            echo $this->model->cosultarAnteriores($_POST['costos'],$_POST['documentos']);
        }

        function buscaCodigo(){
            echo $this->model->buscarConsumoPersonal($_POST['codigo'],$_POST['documento'],$_POST['costos']);
        }

        function borraFila() {
            echo json_encode($this->model->eliminar($_POST));
        }

        function reporte() {
            echo json_encode($this->model->generarReporte($_POST['cc']));
        }

        function anulaItem() {
            echo $this->model->anularItem($_POST['item']);
        }

        function kardex() {
            echo $this->model->generarKardex($_POST);
        }
        
    }
?>