<?php
    class Firmas extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaOrdenes = $this->model->listarOrdenesFirmas();
            $this->view->render('firmas/index');
        }

        function ordenId(){
            echo json_encode($this->model->consultarOrdenId($_POST['id']));
        }
        
        function comentarios(){
            echo $this->model->grabarComentarios($_POST['codigo'],$_POST['comentarios']);
        }

        function autoriza(){
            echo json_encode($this->model->firmar($_POST['id']));
        }

        function actualizaListado() {
            echo $this->model->listarOrdenesFirmas();
        }
    }
?>