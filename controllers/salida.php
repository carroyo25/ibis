<?php
    class Salida extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaNotasSalidas = $this->model->listarNotasDespacho();
            $this->view->listaEnvio = $this->model->listarParametros('08');
            $this->view->listaAprueba = $this->model->apruebaRecepción();
            $this->view->listaAlmacen = $this->model->listarAlmacenGuia();
            $this->view->listaEntidad = $this->model->listarEntidades();
            $this->view->listaModalidad = $this->model->listarParametros(14);
            $this->view->listaPersonal = $this->model->listarPersonalRol(4);
            $this->view->listaMovimiento = $this->model->listarParametros(12);
            $this->view->listaCostos = $this->model->costosPorUsuario($_SESSION['iduser']);

            $this->view->render('salida/index');
        }

        function ingresos(){
            echo $this->model->importarItemIngresos($_POST['ccostos']);
        }

        function notaId(){
            echo json_encode($this->model->llamarNotaIngresoId($_POST['id']));
        }
        

        function nuevasalida(){
            echo json_encode($this->model->grabarDespacho($_POST['cabecera'],$_POST['detalles']));
        }

        function documentopdf(){
            echo $this->model->generarPdfSalida($_POST['cabecera'],$_POST['detalles'],$_POST['condicion']);
        }

        function salidaId(){
            echo json_encode($this->model->consultarSalidaId($_POST['id']));
        }

        function guiaremision(){
            echo $this->model->grabarGuiaRemision($_POST['cabecera'],
                                                    $_POST['detalles'],
                                                    $_POST['despacho'],
                                                    $_POST['pedido'],
                                                    $_POST['orden'],
                                                    $_POST['ingreso']);
        }

        function actualizaDespachos(){
            echo $this->model->listarNotasDespacho();
        }

        function cerrarNota(){
            echo $this->model->actualizarProcesos($_POST['detalles'],
                                                    $_POST['despacho'],
                                                    $_POST['pedido'],
                                                    $_POST['orden'],
                                                    $_POST['ingreso']);
        }

        function almacenes(){
            echo json_encode($this->model->consultarAlmacenes($_POST['origen'],$_POST['destino']));
        }

        function buscarItem(){
            echo $this->model->buscarItemRecepcion($_POST['indice']);
        }

        //esto se usara para todos los documentos
        function ultimoIndice(){
            $indice = $this->model->lastInsertId("SELECT MAX(id_regalm) AS id FROM alm_despachocab"); 
            $indice = gettype($indice) == "NULL" ? 1 : $indice;

            echo str_pad($indice,6,0,STR_PAD_LEFT);
        }

    }
?>