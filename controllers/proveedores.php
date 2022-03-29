<?php
    class Proveedores extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaProveedores = "";
            $this->view->listaBancos = $this->model->llamarParametrosSelect("02");
            $this->view->listaMonedas = $this->model->llamarParametrosSelect("03");
            $this->view->listaTipos = $this->model->listarParametros("05");
            $this->view->listaDocumentos = $this->model->listarParametros("04");
            $this->view->listaPais = $this->model->listarPais();
            $this->view->render('proveedores/index');
        }

        function obtenerValores(){
            $bancos = $_POST['bancos'];
            $tipo = $_POST['tipo'];

            $return = array("bancos"=>$this->model->llamarParametrosSelect($bancos),
                            "monedas"=>$this->model->llamarParametrosSelect($tipo));

            echo json_encode($return);
        }
        
    }
?>