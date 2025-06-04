<?php
    class Main extends Controller {
        function __construct() {
            parent::__construct();
        }

        function render() {
            $this->view->render("main/index");
        }

        function accesoUsuario(){
            echo json_encode($this->model->ingresarSistema($_POST['usuario'],$_POST['clave']));
        }
    }
?>