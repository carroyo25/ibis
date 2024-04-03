<?php
    class Transferencias extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaCostos = $this->model->costosPorUsuario($_SESSION['iduser']);
            $this->view->listaAprueba = $this->model->apruebaRecepción();
            $this->view->listaAlmacen = $this->model->listarAlmacenGuia();
            $this->view->listaMovimiento = $this->model->listarParametros(12);
            $this->view->listaModalidad = $this->model->listarParametros(14);
            $this->view->listaPersonal = $this->model->listarPersonalRol(4);
            $this->view->listaEnvio = $this->model->listarParametros('08');
            $this->view->listaAtencion = $this->model->listarPedidosAtendidos();
            $this->view->listaEntidad = $this->model->listarEntidades();
            $this->view->listaPedidos = $this->model->listarPedidos();

            $this->view->render('transferencias/index');
        }

        function existencias(){
            echo $this->model->consultarStocks($_POST['cc'],$_POST['codigo'],$_POST['descripcion']);
        }

        function pedidos(){
            echo $this->model->listarPedidosAtencion($_POST['cc'],$_POST['pedido']);
        }

        function items(){
            echo json_encode($this->model->consultarPedidos($_POST['indice'],$_POST['origen']));
        }

        function registro() {
            echo json_encode($this->model->insertarTransferencia($_POST['cabecera'],
                                                    $_POST['detalles'],
                                                    $_POST['idpedido'],
                                                    $_POST['atendidos'],
                                                    $_POST['estado']));
        }

        function consultID(){
            echo json_encode($this->model->consultarTransferencia($_POST['id'],$_POST['guia']));
        }

        function grabaGuia(){
            echo json_encode($this->model->grabarGuiaTransferencia($_POST['cabecera'],
                                                    $_POST['nota'],
                                                    $_POST['operacion']));
        }

        function vistaPreviaGuiaRemisioNotas(){
            echo json_encode($this->model->generarVistaPreviaGuiaNota($_POST['cabecera'],$_POST['detalles'],$_POST['proyecto']));
        }

        function preImpresoGuiasTransf(){
            echo json_encode($this->model->imprimirFormatoGuiaTransf($_POST['cabecera'],$_POST['detalles'],$_POST['proyecto'],$_POST['nota'],$_POST['operacion']));
        }

        function consultaId(){
            echo json_encode($this->model->consultarReqId($_POST['id'],51,54,49,null));
        }
        
    }
?>