<?php
    class RegistroTi extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaCostosSelect = $this->model->costosPorUsuarioSelect($_SESSION['iduser']);
            $this->view->render('registroti/index');
        }

        function buscaCatalogo(){
            echo $this->model->listarProductosSoporte($_POST['tipo']);
        }

        function registrokardex(){
            echo json_encode($this->model->registrarEquipo($_POST));
        }
        
        function filtraItemsTi() {
            echo $this->model->filtrarItemsTi($_POST['codigo'],$_POST['descripcion'],$_POST['tipo']);
        }

        function firmaTi(){
            echo $this->model->subirFirmaTi($_POST['detalles'],$_POST['correo'],$_POST['nombre'],$_POST['cc']);
        }
    }
?>