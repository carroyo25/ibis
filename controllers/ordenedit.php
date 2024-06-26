<?php
    class OrdenEdit extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaAlmacenes = $this->model->listarAlmacen();
            $this->view->listaTransportes = $this->model->listarParametros("08");
            $this->view->listaMonedas =  $this->model->listarParametros("03");
            $this->view->listaPagos = $this->model->listarParametros("11");
            $this->view->listaOrdenes = "";
            $this->view->listaEntidades = $this->model->listarEntidades();
            $this->view->listaCostosSelect = $this->model->costosPorUsuarioSelect($_SESSION['iduser']);
            $this->view->render('ordenedit/index');
        }

        function pedidos(){
            echo $this->model->importarPedidos();
        }

        function datosPedido(){
            echo json_encode($this->model->verDatosCabecera($_POST['pep']));
        }

        function vistaPreliminar(){
            echo $this->model->generarDocumento($_POST['cabecera'],$_POST['condicion'],$_POST['detalles']);
        }


        function modificaRegistro(){
            echo json_encode($this->model->modificarOrden($_POST['cabecera'],$_POST['detalles'],$_POST['comentarios']));
        }
        
        function ordenId(){
            echo json_encode($this->model->consultarOrdenEditId($_POST['id']));
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

        function detallesEntidad(){
            echo json_encode($this->model->datosEntidad($_POST['codigo']));
        }

        function actualizaItem(){
            $this->model->eliminarItem($_POST['itemOrden'],$_POST['itemPedido'],$_POST['itemCantPed']);
        }

        function modificaOrden(){
            echo json_encode($this->model->modificarOrden($_POST['cabecera'],$_POST['detalles']));
        }

        function numeraItems() {
            echo json_encode($this->model->ordenarItems($_POST['items']));
        }

        function anula(){
            echo json_encode($this->model->anularOrden($_POST['id']));
        }

        function listaScroll(){
            $pagina = $_POST['pagina'] ?? 1;
            $cantidad = 100;
        
            echo json_encode([$this->model->listarOrdenScroll($pagina,$cantidad)]);
        }

        function mmttoItem(){
            echo json_encode($this->model->modificarItem($_POST));
        }
    }
?>