<?php
    class Madres extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaGuias = "";
            $this->view->listaCostos = $this->model->costosPorUsuario($_SESSION['iduser']);
            $this->view->listaAprueba = $this->model->apruebaRecepción();
            $this->view->listaAlmacen = $this->model->listarAlmacenGuia();
            $this->view->listaMovimiento = $this->model->listarParametros(12);
            $this->view->listaEntidad = $this->model->listarEntidades();
            $this->view->listaModalidad = $this->model->listarParametros(14);
            $this->view->listaEnvio = $this->model->listarParametros('08');
            $this->view->listaPersonal = $this->model->listarPersonalRol(4);
            $this->view->render('madres/index');
        }

        function guias(){
            echo $this->model->importarGuias($_POST['cc'],$_POST['guia']);
        }

        function itemsDespacho(){
            echo $this->model->importarItemsDespacho($_POST['idx']);
        }

        function grabaGuiaMadre(){
            echo json_encode ($this->model->grabarGuia($_POST));
        }

        function listaScroll(){
            $pagina = $_POST['pagina'] ?? 1;
	        $cantidad = 1;

            echo json_encode([$this->model->listarGuiasScroll($pagina,$cantidad)]);
        }

        function guiasRemision(){
            echo json_encode($this->llamarDatosGuia($_POST['id']));
        } 
    }
?>