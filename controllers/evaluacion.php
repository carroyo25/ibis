<?php
    class Evaluacion extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            //$this->view->listaOrdenes = $this->model->listarOrdenesEval($nroSearch="",$costosSearch = -1,$mesSearch = -1,$anioSearch=2023);
            $this->view->listaOrdenes = "";
            $this->view->listaCostosSelect = $this->model->costosPorUsuarioSelect($_SESSION['iduser']);
            $this->view->render('evaluacion/index');
        }
        
        function criterios(){
            echo json_encode($this->model->llamarOrdenID($_POST['tipo'],$_POST['id'],$_POST['rol']));
        }

        function evaluar(){
            echo json_encode($this->model->grabarEvaluacion($_POST['items']));
        }

        function actualizaTabla(){
            echo $this->model->listarOrdenesAprueba();
        }

        function listaFiltrada(){
            echo $this->model->listarOrdenesEval($_POST['nroSearch'],$_POST['costosSearch'],$_POST['mesSearch'],$_POST['anioSearch'],$_POST['tipoSearch']);
        }

        function listaScroll(){
            $pagina = $_POST['pagina'] ?? 1;
            $cantidad = 30;
        
            echo json_encode([$this->model->listarOrdenScrollEvaluacion($pagina,$cantidad)]);
        }
    }
?>