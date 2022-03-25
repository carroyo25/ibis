<?php
    class Main extends Controller {
        function __construct() {
            parent::__construct();
        }

        function render() {
            $this->view->render("main/index");
        }

        function accesoUsuario(){
            $user = $_POST['usuario'];
            $clave = $_POST['clave'];

            $result = $this->model->ingresarSistema($user,$clave);

            echo $result;
        }
    }
?>