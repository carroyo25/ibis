<?php
class Minimos extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function render(){
        $this->view->listaCostosSelect = $this->model->costosPorUsuarioSelect($_SESSION['iduser']);
        $this->view->render('minimos/index');
    }

    function consultaProductos(){
        echo json_encode($this->model->listarMinimos($_POST));
    }

    function consultaProductosPaginado(){
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $limit = 10;
        
        $resultado = $this->model->listarMinimosPaginado($_POST, $page, $limit);
        echo json_encode($resultado);
    }

    function registro(){
        echo json_encode($this->model->regristrarMinimo($_POST));
    }

    function permisos(){
        echo json_encode($this->model->verificarPermiso($_POST));
    }

    function exportarExcel(){    
        $costos = $_POST['costos'] ?? '-1';
        $codigo = $_POST['codigo'] ?? '';
        $descripcion = $_POST['descripcion'] ?? '';

        // Obtener TODOS los datos sin paginación
        echo json_encode($this->model->listarMinimosExportar($costos, $codigo, $descripcion));
    }
}
?>