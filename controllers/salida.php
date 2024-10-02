<?php
    class Salida extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaNotasSalidas = $this->model->listarNotasDespacho();
            $this->view->listaEnvio = $this->model->listarParametros('08');
            $this->view->listaPlacas = $this->model->listarParametros('24');
            $this->view->listaAprueba = $this->model->apruebaRecepción();
            $this->view->listaAlmacen = $this->model->listarAlmacenGuia();
            $this->view->listaEntidad = $this->model->listarEntidadesMTC();
            $this->view->listaConductores = $this->model->listarConductores();
            $this->view->listaModalidad = $this->model->listarParametros(14);
            $this->view->listaTransporte = $this->model->listarParametros(23);
            $this->view->listaPersonal = $this->model->listarPersonalRol(4);
            $this->view->listaMovimiento = $this->model->listarParametros(12);
            $this->view->listaCostos = $this->model->costosPorUsuario($_SESSION['iduser']);
            $this->view->listaCostosSelect = $this->model->costosPorUsuarioSelect($_SESSION['iduser']);
            $this->view->listaDepartamento = $this->model->getUbigeoSelect(1,"%");

            $this->view->render('salida/index');
        }

        function ordenes(){
            echo $this->model->listarOrdenes(2);
        }

        function actualizaDespachos(){
            echo $this->model->listarNotasDespacho();
        }

        function documentopdf(){
            echo $this->model->generarPdfSalida($_POST['cabecera'],$_POST['detalles'],$_POST['condicion']);
        }

        function vistaPreviaGuiaRemision(){
            echo json_encode($this->model->generarVistaPrevia($_POST['cabecera'],$_POST['detalles'],$_POST['proyecto']));
        }

        function preImpreso(){
            echo json_encode($this->model->imprimirFormato($_POST['cabecera'],$_POST['detalles'],$_POST['proyecto'],$_POST['despacho'],$_POST['operacion']));
        }

        function GrabaGuia(){
            echo json_encode($this->model->grabarGuia($_POST['cabecera'],
                                                      $_POST['detalles'],
                                                      $_POST['proyecto'],
                                                      $_POST['despacho'],
                                                      $_POST['operacion'],
                                                      $_POST['guia']));
        }

        function nuevasalida(){
            echo json_encode($this->model->grabarDespacho($_POST['cabecera'],$_POST['detalles']));
        }

        function ordenId() {
            echo json_encode($this->model->pasarDetallesOrden($_POST['id'],$_POST['costo']));
        }
        
        function filtraOrden() {
            echo $this->model->filtrarOrdenesID($_POST['id']);
        }

        function nuevoDespacho(){
            echo json_decode($this->model->insertarDespacho($_POST['cabecera'],$_POST['detalles']));
        }

        function salidaId(){
            echo json_encode($this->model->consultarSalidaId($_POST['id']));
        }

        function filtraDespachos() {
            echo $this->model->filtrarNotasDespacho($_POST);
        }

        function modificarSalida() {
            echo $this->model->modificar($_POST['cabecera'],$_POST['detalles'],$_POST['iddespacho']);
        }
        
        function existeObra() {
            echo $this->model->verificarItem($_POST['id']);
        }

        function marcaItem(){
            echo $this->model->marcarItemDespacho($_POST['id']);
        }

        function guiaSunat() {
            echo json_encode($this->model->enviarSunatSalida($_POST['cabecera'],$_POST['detalles']));
        }

        function generaNumero(){
            echo json_encode($this->model->numeroGuia());
        }

        function ubigeoGuias(){
            echo json_encode($this->model->getUbigeoSelect($_POST['nivel'],$_POST['prefijo']));
        }

        function numeroSunat(){
            echo json_encode($this->model-> numeroGuiaSunat($_POST['guia_interna'],$_POST['peso']));
        }
    }
?>