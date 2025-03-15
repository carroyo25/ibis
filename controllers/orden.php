<?php
    class Orden extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaAlmacenes = $this->model->listarAlmacen();
            $this->view->listaTransportes = $this->model->listarParametros("08");
            $this->view->listaMonedas =  $this->model->listarParametros("03");
            $this->view->listaPagos = $this->model->listarParametros("11");
            $this->view->listaOrdenes = $this->model->listarOrdenes($_SESSION['iduser']);
            $this->view->listaEntidades = $this->model->listarEntidades();
            $this->view->listaCostosSelect = $this->model->costosPorUsuarioSelect($_SESSION['iduser']);
            $this->view->fechaOrden = $this->model->fechaOrden();
            $this->view->render('orden/index');
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

        function nuevoRegistro(){
            echo json_encode($this->model->insertarOrden($_POST['cabecera'],
                                                        $_POST['detalles'],
                                                        $_POST['comentarios'],
                                                        $_POST['adicionales'],
                                                        $_FILES,
                                                        $_POST['usuario']));
        }

        function modificaRegistro(){
            echo json_encode($this->model->modificarOrden($_POST['cabecera'],$_POST['detalles'],$_POST['comentarios'],$_POST['usuario']));
        }
        
        function ordenId(){
            echo json_encode($this->model->consultarOrdenId($_POST['id']));
        }

        function buscaRol(){
            echo $this->model->buscarFirmas($_POST['rol'],$_POST['documento']);
        }

        function correo(){
            echo json_encode($this->model->enviarCorreo($_POST['cabecera'],
                                                        $_POST['detalles'],
                                                        $_POST['correos'],
                                                        $_POST['asunto'],
                                                        $_POST['mensaje']));
        }
        
        function aprobacion(){
            echo json_encode($this->model->solicitarAprobacion($_POST['cabecera'],$_POST['detalles']));
        }


        function comentarios(){
            echo $this->model->grabarComentarios($_POST['codigo'],$_POST['comentarios'],$_POST['usuario']);
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

        function marcaItem(){
            echo $this->model->itemMarcado($_POST['id'],$_POST['estado'],$_POST['io']);
        }

        function filtroOrden(){
            echo $this->model->ordenesFiltradas($_POST);
        }

        function ItemsPorCostos(){
            echo $this->model->importarPedidosCostos($_POST['costo']);
        }

        function listarAdjuntos(){
            echo json_encode($this->model->verAdjuntosOrden($_POST['orden']));
        }

        function archivos(){
            echo json_encode($this->model->subirArchivos($_POST['codigo'],$_FILES));
        }

        function descargaRapida(){
            echo json_encode($this->model->descargarOrdenPrincipal($_POST['id']));
        }

        function apruebaRapido(){
            echo json_encode($this->model->aprobacionRapida($_POST['id']));
        }

        function anulaItemPedido(){
            echo json_encode($this->model->anularItem($_POST));
        }
        
        function cambiaItemPedido(){
            echo json_encode($this->model->cambiarItem($_POST));
        }
    }    
?>