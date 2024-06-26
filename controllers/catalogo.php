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
            echo json_encode($this->model->buscarItemsPalabra($_POST['criterio']));
        }

        function buscaCodigo(){
            echo json_encode($this->model->buscarItemsCodigo($_POST['criterio']));
        }

        function catalogoXls(){
            echo json_encode($this->model->exportarCatalogo());
        }

        function listaScroll(){
            $pagina = $_POST['pagina'] ?? 1;
	        $cantidad = 30;

            echo json_encode([$this->model->listarItemsScroll($pagina,$cantidad)]);
        }
        
    }
?>