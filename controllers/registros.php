<?php
    class Registros extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaRecepciona = $this->model->listarPersonalRol(4);
            $this->view->listaGuias = $this->model->listarGuias();
            $this->view->render('registros/index');
        }

        function despachosID(){
            echo json_encode($this->model->importarDespacho($_POST['id']));
        }
        
        function ingresoAlmacen(){
            echo json_encode($this->model->actualizarStocks($_POST['detalles'],
                                                            $_POST['almacen'],
                                                            $_POST['pedido'],
                                                            $_POST['orden'],
                                                            $_POST['recepciona'],
                                                            $_POST['salida']));
        }

        function actualizarDespachos(){
            echo $this->model->listarGuias();
        }
    }
?>