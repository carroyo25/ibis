<?php
    class Contratos extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaAlmacenes = $this->model->listarAlmacen();
            $this->view->listaTransportes = $this->model->listarParametros("08");
            $this->view->listaMonedas =  $this->model->listarParametros("03");
            $this->view->listaPagos = $this->model->listarParametros("11");
            $this->view->listaContratos = $this->model->listarContratos($_SESSION['iduser']);
            $this->view->listaEntidades = $this->model->listarEntidades();
            $this->view->listaCostosSelect = $this->model->costosPorUsuarioSelect($_SESSION['iduser']);
            $this->view->fechaOrden = $this->model->fechaOrden();
            $this->view->render('contratos/index');
        }

        function vistaPreliminar(){
            echo $this->model->generarContrato($_POST['cabecera'],$_POST['condicion'],$_POST['detalles'],$_POST['condiciones']);
        }

        function nuevoRegistro(){
            echo json_encode($this->model->insertarContrato($_POST['cabecera'],
                                                        $_POST['detalles'],
                                                        $_POST['comentarios'],
                                                        $_POST['adicionales'],
                                                        $_FILES,
                                                        $_POST['usuario'],
                                                        $_POST['condiciones']));
        }

        function modificaRegistro(){
            echo json_encode($this->model->modificarContrato($_POST['cabecera'],$_POST['detalles'],$_POST['comentarios'],$_POST['usuario'],$_POST['condiciones']));
        }

        function ordenId(){
            echo json_encode($this->model->consultarContratoId($_POST['id']));
        }

        function actualizaListado() {
            echo $this->model->listarContratos($_SESSION['iduser']);
        }

        function buscaRol(){
            echo $this->model->buscarFirmas($_POST['rol'],$_POST['vista']);
        }

        function correo(){
            echo json_encode($this->model->enviarCorreoContrato($_POST['cabecera'],
                                                        $_POST['detalles'],
                                                        $_POST['correos'],
                                                        $_POST['asunto'],
                                                        $_POST['mensaje'],
                                                        $_POST['condiciones']
                                                    ));
        }

        function envioContrato(){
            echo json_encode($this->model->descargarContrato($_POST['cabecera'],$_POST['detalles'],$_POST['condiciones']));
        }

        
    }
?>