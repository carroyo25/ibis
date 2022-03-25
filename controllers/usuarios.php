<?php
    class Usuarios extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaUsuarios  = $this->model->listarUsuarios();
            $this->view->listaModulos   = $this->model->listarModulos();
            $this->view->listaAquarius  = $this->model->listarAquarius();
            $this->view->listaNivel     = $this->model->listarParametros('00');
            $this->view->listaEstado    = $this->model->listarParametros('01');
            $this->view->render('usuarios/index');
        }

        function nuevoUsuario(){
            $cabecera = $_POST['cabecera'];
            $modulos = $_POST['modulos'];

            $resultado = $this->model->insertarUsuario($cabecera,$modulos);

            echo json_encode($resultado);
        }

        function actualizaUsuario(){
            $cabecera = $_POST['cabecera'];
            $modulos = $_POST['modulos'];

            $resultado = $this->model->actualizarUsuario($cabecera,$modulos);

            echo json_encode($resultado);
        }

        function consultaUsuario() {
            $id = $_POST['id'];

            $resultado = $this->model->consultarUsuario($id);

            echo json_encode($resultado);
        }
        

        function actualizaListado(){
            $resultado = $this->model->listarUsuarios();

            echo $resultado;
        }

        function modulos(){
            echo $this->model->listarModulos();
        }

        function costos(){
            echo $this->model->listarCostos();
        }

        function almacen(){
            echo $this->model->listarAlmacen();
        }
    }
?>