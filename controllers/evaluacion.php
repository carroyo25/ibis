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

        function subeArchivos(){
            try {
                error_log("=== CONFIGURACIÓN PHP ===");
                error_log("upload_max_filesize: " . ini_get('upload_max_filesize'));
                error_log("post_max_size: " . ini_get('post_max_size'));
                error_log("max_file_uploads: " . ini_get('max_file_uploads'));
    
                // Verificar que lleguen archivos
                if (empty($_FILES)) {
                    echo json_encode([
                        'success' => false,
                        'mensaje' => 'No se recibieron archivos',
                        'debug' => $_FILES,
                        'php_config' => [
                            'upload_max_filesize' => ini_get('upload_max_filesize'),
                            'post_max_size' => ini_get('post_max_size')
                        ]
                    ]);
                    return;
                }

                // Verificar si existe 'file' en $_FILES
                if (!isset($_FILES['file'])) {
                    echo json_encode([
                        'success' => false,
                        'mensaje' => 'No se encontró el campo "file" en FILES',
                        'debug' => $_FILES
                    ]);
                    return;
                }

                $codigo = $_POST['codigo'] ?? null;
                
                if (!$codigo) {
                    echo json_encode([
                        'success' => false,
                        'mensaje' => 'Código de orden no proporcionado'
                    ]);
                    return;
                }
                
                $resultado = $this->model->subirArchivos($codigo, $_FILES);
                echo json_encode($resultado);
                
            } catch (Exception $e) {
                echo json_encode([
                    'success' => false,
                    'mensaje' => $e->getMessage()
                ]);
            }
        }

        function listarAdjuntosEvaluacion(){
            echo json_encode($this->model->verAdjuntosEvaluacion($_POST['orden']));
        }
    }
?>