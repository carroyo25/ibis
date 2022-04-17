<?php
    class Pedidos extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaCostos = $this->model->costosPorUsuario($_SESSION['iduser']);
            $this->view->listaAreas = $this->model->obtenerAreas();
            $this->view->listaTipos = $this->model->listarParametros("07");
            $this->view->listaTransportes = $this->model->listarParametros("08");
            $this->view->listaAquarius  = $this->model->listarAquarius();
            $this->view->listaPedidos = $this->model->listarPedidosUsuario();
            $this->view->render('pedidos/index');
        }

        function numeroDocumento(){
            $sql = "SELECT COUNT(idreg) AS numero FROM tb_pedidocab WHERE tb_pedidocab.idcostos =:cod";
            echo json_encode($this->model->generarNumero($_POST['cc'],$sql));
        }

        function llamaProductos(){
            echo $this->model->listarProductos($_POST['tipo']);
        }

        function adjuntos(){
            echo $this->model->subirAdjuntos($_POST['nropedidoatach'],$_FILES['uploadAtach']);
        }

        function vistaPrevia(){
            echo $this->model->generarDocumento($_POST['cabecera'],$_POST['detalles']);
        }

        function nuevoPedido(){
            echo json_encode($this->model->insertar($_POST['cabecera'],$_POST['detalles']));
        }

        function modificaPedido(){
            echo json_encode($this->model->modificar($_POST['cabecera'],$_POST['detalles']));
        }

        function actualizaListado(){
            echo $this->model->listarPedidosUsuario();
        }

        function consultaId(){
            echo json_encode($this->model->consultarReqId($_POST['id'],49,50,49));
        }

        function buscaRol(){
            echo $this->model->buscarRol($_POST['rol'],$_POST['cc']);
        }

        function envioCorreos(){
            echo json_encode($this->model->enviarMensajes($_POST['subject'],
                                                        $_POST['mensaje'],
                                                        $_POST['correos'],
                                                        $_FILES['mailAtach'],
                                                        $_POST['pedido'],
                                                        $_POST['detalles'],
                                                        $_POST['estadoPedido'],
                                                        $_POST['emitido']));
        }

    }
?>