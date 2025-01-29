<?php
    class GuiasServicios extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaNotasSalidas = $this->model->listarGuiasServicio(null,-1,2025);
            $this->view->listaEnvio = $this->model->listarParametros('08');
            $this->view->listaAprueba = $this->model->apruebaRecepción();
            $this->view->listaAlmacen = $this->model->listarAlmacenGuia();
            $this->view->listaEntidad = $this->model->listarEntidades();
            $this->view->listaModalidad = $this->model->listarParametros(14);
            $this->view->listaPersonal = $this->model->listarPersonalRol(4);
            $this->view->listaMovimiento = $this->model->listarParametros(12);
            $this->view->listaCostos = $this->model->costosPorUsuario($_SESSION['iduser']);
            $this->view->listaCostosSelect = $this->model->costosPorUsuarioSelect($_SESSION['iduser']);
            $this->view->render('guiasservicios/index');
        }

        function nroguia(){
            echo json_encode($this->model->nuevonumeroguia());
        }

        function grabaGuiaManual(){
            echo json_encode($this->model->grabarGuiaManual($_POST['guiaCab'],$_POST['formCab'],$_POST['detalles'],$_POST['operacion']));
        }

        function vistaPreviaGuia(){
            echo json_encode($this->model->generarGuiaServicioPdf($_POST['cabecera'],$_POST['detalles'],$_POST['proyecto']));
        }

        function guiaManualId(){
            echo json_encode($this->model->consultarGuiaManualId($_POST['indice'],$_POST['guia']));
        }

        function listaFiltrada() {
            echo $this->model->listarGuiasManuales($_POST['guia'],$_POST['costos'],$_POST['anio'],);
        }

        function filtraOrden() {
            echo $this->model->filtrarOrdenesServicioID($_POST['id']);
        }

        function ordenId(){
            echo json_encode($this->model->consultarOrdenIdServicio($_POST['id']));
        }

        function grabaGuiaServicio(){
            echo json_encode($this->model->grabarGuiaServicios($_POST['guiaCab'],$_POST['formCab'],$_POST['detalles'],$_POST['operacion']));
        }

        function guiaServiciosId(){
            echo json_encode($this->model->consultarGuiaServicioId($_POST['indice'],$_POST['guia']));
        }

        function filtraPedido(){
            echo $this->model->filtrarPedidoServicioID($_POST['id'],$_POST['costos']);
        }

        function detallesPedido(){
            echo $this->model->detallesPedidoServicio($_POST['id']);
        }
        
    }
?>