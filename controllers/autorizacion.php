<?php
    class Autorizacion extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaCostos = $this->model->costosPorUsuario($_SESSION['iduser']);
            $this->view->listaCostosSelect = $this->model->costosPorUsuarioSelect($_SESSION['iduser']);
            $this->view->listaAreas = $this->model->obtenerAreas();
            $this->view->listaTipos = $this->model->listarParametros("07");
            $this->view->listaTransportes = $this->model->listarParametros("08");
            $this->view->listaAutorizaciones = $this->model->listarParametros("25");
            $this->view->listaAquarius  = $this->model->listarAquarius();
            $this->view->listaTraslados = $this->model->listarTraslados();
            $this->view->listaAlmacen = $this->model->listarAlmacenSepcon();
            $this->view->listaTraslados = $this->model->listarTraslados();
            $this->view->listaPersonal = $this->model->listarPersonalRol(4);

            $this->view->listaEnvio = $this->model->listarParametros('08');
            $this->view->listaAlmacen = $this->model->listarAlmacenGuia();
            $this->view->listaEntidad = $this->model->listarEntidadesMTC();
            $this->view->listaModalidad = $this->model->listarParametros(14);
            $this->view->listaPersonal = $this->model->listarPersonalRol(4);
            $this->view->listaMovimiento = $this->model->listarParametros(12);
            $this->view->listaPlacas = $this->model->listarParametros('24');
            $this->view->listaConductores = $this->model->listarConductores();
            $this->view->listaTransporte = $this->model->listarParametros(23);
            $this->view->listaDepartamento = $this->model->getUbigeoSelect(1,"%");

            $this->view->render('autorizacion/index');
        }

        function nuevoDocumento(){
            echo json_encode($this->model->insertar($_POST['cabecera'],$_POST['detalles']));
        }

        function modificaDocumento(){
            echo json_encode($this->model->modificar($_POST['cabecera'],$_POST['detalles']));
        }

        function muestraTransferencias(){
            echo ($this->model->listarTransferencias());
        }

        function actualizaListado(){
            echo ($this->model->listarTraslados());
        }

        function documentoId(){
            echo json_encode($this->model->autorizacionId($_POST['indice']));
        }

        function vistaPrevia(){
            echo json_encode($this->model->vistaPreviaAutorizacion($_POST['cabecera'],$_POST['detalles']));
        }

        function recepcionCliente(){
            $this->model->recepcionCliente($_POST['id'],$_POST['estado']);
        }
        
    }
?>