<?php
    class Adeudo extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaCostosSelect = $this->model->costosPorUsuarioSelect($_SESSION['iduser']);
            $this->view->render('adeudo/index');
        }

        function datosapi(){
            echo json_encode($this->model->buscarDatosAdeudo($_POST['documento'],$_POST['costos']));
        }

        function firma(){
            echo $this->model->subirFirmaAlmacen($_POST['detalles'],$_POST['nombre'],$_POST['proyecto'],$_POST['correo']);
        }

        /*function consulta(){
            echo $this->model->cosultarAnteriores($_POST['costos'],$_POST['documentos']);
        }

        function buscaCodigo(){
            echo $this->model->buscarConsumoPersonal($_POST['codigo'],$_POST['documento'],$_POST['costos']);
        }*/

        function formato(){
            echo $this->model->generarAdeudo($_POST);
        }
        
    }
?>