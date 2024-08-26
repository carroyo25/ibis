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
        
    }
?>