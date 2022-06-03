<?php
    class Recepcion extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaNotasIngreso = $this->model->listarNotas();
            $this->view->listaAlmacen = $this->model->listarAlmacen();
            $this->view->listaAprueba = $this->model->apruebaRecepción();
            $this->view->listaMovimiento = $this->model->listarParametros(12);
            $this->view->render('recepcion/index');
        }

        function actualizaNotas(){
            echo $this->model->listarNotas();
        }

        function numeroIngreso(){
            $sql ="SELECT COUNT( alm_recepcab.id_regalm ) AS numero FROM alm_recepcab WHERE ncodalm1 =:cod";
            echo json_encode($this->model->generarNumero($_POST['id'],$sql));
        }
        
        function items(){
            echo $this->model->importarItems();
        }

        function ordenId(){
            echo json_encode($this->model->consultarOrdenIdRecepcion($_POST['id']));
        }

        function ordenes(){
            echo $this->model->listarOrdenes();
        }

        function nuevoIngreso(){
            //echo json_encode($this->model->insertar($_POST['cabecera'],$_POST['detalles'],$_POST['series']));
            echo $this->model->insertar($_POST['cabecera'],$_POST['detalles'],$_POST['series']);
        }

        function adjuntos(){
            echo $this->model->subirAdjuntos($_POST['nroIngreso'],$_FILES['uploadAtach']);
        }

        function documentopdf(){
            echo $this->model->generarPdf($_POST['cabecera'],$_POST['detalles'],$_POST['condicion']);
        }

        function consultaId(){
            echo json_encode($this->model->consultarNotaID($_POST['id'],13));
        }

        function cierraIngreso(){
            echo $this->model->cerrar($_POST['cabecera'],$_POST['detalles']);
        }
    }
?>