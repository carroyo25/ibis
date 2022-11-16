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
            $costos = $_POST['costos'];
            $almacenes = $_POST['almacenes'];

            $resultado = $this->model->insertarUsuario($cabecera,$modulos,$costos,$almacenes);

            echo json_encode($resultado);
        }

        function actualizaUsuario(){
            $cabecera = $_POST['cabecera'];
            $modulos = $_POST['modulos'];
            $costos = $_POST['costos'];
            $almacenes = $_POST['almacenes'];

            $resultado = $this->model->actualizarUsuario($cabecera,$modulos,$costos,$almacenes);

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

        function desactivaItem(){
            $id     = $_POST['id'];
            $modulo = $_POST['modulo'];
            $usuario = $_POST['user'];

            echo $this->model->quitarItem($id,$modulo,$usuario);
        }

        function clave(){
            echo $this->model->mostrarClave($_POST['id']);
        }
    }
?>