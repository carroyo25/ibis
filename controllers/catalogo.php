<?php
    class Catalogo extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaItems = $this->model->listarItemsScroll(1,15);
            $this->view->render('catalogo/index');
        }

        function buscaPalabra(){
            echo $this->model->buscarItemsPalabra($_POST['criterio']);
        }

        function buscaCodigo(){
            echo json_encode($this->model->buscarItemsCodigo($_POST['criterio']));
        }

        function catalogoXls(){
            echo $this->model->exportarCatalogo();
        }

        function listaScroll(){
            $pagina = $_POST['pagina'] ?? 1;
	        $cantidad = 35;

            echo json_encode([$this->model->listarItemsScroll($pagina,$cantidad)]);
        }
        
    }
?>