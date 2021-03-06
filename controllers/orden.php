<?php
    class Orden extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaAlmacenes = $this->model->listarAlmacen();
            $this->view->listaTransportes = $this->model->listarParametros("08");
            $this->view->listaOrdenes = $this->model->listarOrdenes($_SESSION['iduser']);
            $this->view->render('orden/index');
        }

        function pedidos(){
            echo $this->model->importarPedidos();
        }

        function datosPedido(){
            echo json_encode($this->model->verDatosCabecera($_POST['pep'],$_POST['prof'],$_POST['ent']));
        }

        function vistaPreliminar(){
            echo $this->model->generarDocumento($_POST['cabecera'],$_POST['condicion'],$_POST['detalles']);
        }

        function nuevoRegistro(){
            echo json_encode($this->model->insertarOrden($_POST['cabecera'],$_POST['detalles'],$_POST['comentarios']));
        }

        function modificaRegistro(){
            echo json_encode($this->model->modificarOrden($_POST['cabecera'],$_POST['detalles'],$_POST['comentarios']));
        }
        
        function ordenId(){
            echo json_encode($this->model->consultarOrdenId($_POST['id']));
        }

        function buscaRol(){
            echo $this->model->buscarFirmas($_POST['rol']);
        }

        function correo(){
            echo json_encode($this->model->enviarCorreo($_POST['cabecera'],
                                                        $_POST['detalles'],
                                                        $_POST['correos'],
                                                        $_POST['asunto'],
                                                        $_POST['mensaje']));
        }

        function comentarios(){
            echo $this->model->grabarComentarios($_POST['codigo'],$_POST['comentarios']);
        }

        function envioOrden(){
            echo json_encode($this->model->enviarCorreoProveedor($_POST['cabecera'],$_POST['detalles']));
        }

        function actualizaListado(){
            echo $this->model->listarOrdenes($_SESSION['iduser']);
        }
    }
?>