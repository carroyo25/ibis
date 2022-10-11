<?php
    class Catalogo extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaItems = $this->model->listarItems();
            $this->view->render('catalogo/index');
        }

        function buscaPalabra(){
            echo $this->model->buscarItemsPalabra($_POST['criterio']);
        }

        function buscaCodigo(){
            echo $this->model->buscarItemsCodigo($_POST['criterio']);
        }
        
    }
?>