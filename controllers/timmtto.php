<?php
    class TiMmtto extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaCostosSelect = $this->model->costosPorUsuarioSelect($_SESSION['iduser']);
            $this->view->listaMantenimientos = $this->model->listarMantenimientos(-1,"");
            $this->view->render('timmtto/index');
        }

        function mantenimiento(){
            echo json_encode($this->model->registrarMmtto($_POST));
        }

        function anteriores(){
            echo json_encode($this->model->mantenimientosAnteriores($_POST));
        }

        function filtro(){
            echo json_encode($this->model->listarMantenimientos($_POST['costos'],$_POST['serie'],$_POST['usuario']));
        }

        function notificar(){
            echo json_encode($this->model->enviarNotificacion($_POST));
        }

        function cambiofechas(){
            echo json_encode($this->model->actualizarFechas($_POST));
        }

        function mmttospendientes() {
            echo json_encode($this->model->mmttoUltimoPendiente($serie,$documento));
        }

        function listaMmttos() {
            echo json_encode($this->model->listarMantenimientos($_POST['costos'],$_POST['serie']));
        }
        
    }
?>