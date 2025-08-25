<?php
    class CajaChica extends Controller{
        function __construct()
        {
            parent::__construct();
        }

        function render(){
            $this->view->listaCostos = $this->model->costosPorUsuario($_SESSION['iduser']);
            $this->view->listaCostosSelect = $this->model->costosPorUsuarioSelect($_SESSION['iduser']);
            $this->view->listaAreas = $this->model->obtenerAreas();
            $this->view->listaTipos = $this->model->listarParametros("07");
            $this->view->listaTransportes = $this->model->listarParametros("08");
            $this->view->listaAquarius  = $this->model->listarAquarius();
            $this->view->listaPedidos = $this->model->listarPedidosUsuario();
            $this->view->listaEntidades = $this->model->listarEntidades();

            $this->view->render('cajachica/index');
        }

        function nuevoPedido(){
            echo json_encode($this->model->insertarCompra($_POST['cabecera'],$_POST['detalles']));
        }

        function modificaPedido(){
            echo json_encode($this->model->modificarCompra($_POST['cabecera'],$_POST['detalles']));
        }

        private function saveItemsCompras($codigo,$estado,$atencion,$tipo,$costos,$area,$detalles,$indice){

            $datos = json_decode($detalles);
            $nreg = count($datos);
            
            $sql = $this->db->connect()->prepare("INSERT INTO tb_pedidodet 
                                                                    SET idpedido=:ped,idprod=:prod,idtipo=:tipo,unid=:und,
                                                                        cant_pedida=:cant,cant_aprob=:aprob,estadoItem=:est,tipoAten=:aten,
                                                                        verificacion=:ver,nflgqaqc=:qaqc,idcostos=:costos,idarea=:area,
                                                                        observaciones=:espec,item=:nropos,precio=:pu,total=:total");

            for ($i=0; $i < $nreg; $i++) { 
                try {
                        //if ( $existe == 0) {
                            $sql ->execute([
                                "ped"=>$indice,
                                "prod"=>$datos[$i]->idprod,
                                "tipo"=>$tipo,
                                "und"=>$datos[$i]->unidad,
                                "cant"=>$datos[$i]->cantidad,
                                "est"=>$estado,
                                "aten"=>$atencion,
                                "ver"=>$codigo,
                                "qaqc"=>$datos[$i]->calidad,
                                "costos"=>$costos,
                                "area"=>$area,
                                "espec"=>$datos[$i]->especifica,
                                "nropos"=>$datos[$i]->item,
                                "aprob"=>$datos[$i]->cantidad,
                                "pu"   =>$datos[$i]->precio,
                                "total"=>$datos[$i]->total]);
                        //}
                       
                   
                } catch (PDOException $th) {
                    echo "Error: ".$th->getMessage();
                    return false;
                }
            }
        }
        
    }
?>