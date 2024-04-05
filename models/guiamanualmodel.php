<?php
    class GuiaManualModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarGuiasManuales(){
            try {
                $salida = ""; 

                $sql = $this->db->connect()->prepare("SELECT
                                                        alm_desplibrescab.id_regalm,
                                                        DATE_FORMAT( alm_desplibrescab.ffecdoc, '%d/%m/%Y' ) AS fechaDocumento,
                                                        tb_proyectos.ccodproy,
                                                        UPPER( origen.cdesalm ) AS almacen_origen,
                                                        UPPER( alm_desplibrescab.id_centi ) AS proveedor,
                                                        destino.cdesalm AS almacen_destino,
                                                        alm_desplibrescab.cnumguia 
                                                    FROM
                                                        alm_desplibrescab
                                                        INNER JOIN tb_proyectos ON alm_desplibrescab.ncodpry = tb_proyectos.nidreg
                                                        INNER JOIN tb_almacen AS origen ON alm_desplibrescab.ncodalm1 = origen.ncodalm
                                                        INNER JOIN tb_almacen AS destino ON alm_desplibrescab.ncodalm2 = destino.ncodalm 
                                                    WHERE
                                                        alm_desplibrescab.nflgactivo = 1");
                $sql->execute();
                $rowCount = $sql->rowCount();

               

                if ($rowCount > 0) {
                   

                    while ($rs = $sql->fetch()){
                        $salida .='<tr data-indice="'.$rs['id_regalm'].'" class="pointer">
                                        <td class="textoCentro">'.str_pad($rs['id_regalm'],6,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.$rs['fechaDocumento'].'</td>
                                        <td class="textoCentro">'.$rs['almacen_origen'].'</td>
                                        <td class="pl20px">'.$rs['almacen_destino'].'</td>
                                        <td class="pl20px">'.$rs['ccodproy'].'</td>
                                        <td class="textoCentro">'.$rs['cnumguia'].'</td>
                                    </tr>';
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function nuevonumeroguia(){
            $guiaAutomatica = $this->numeroGuia();
            $mensaje = "numero de guia creado";

            return array("mensaje"=>$mensaje,"guia"=>$guiaAutomatica); 
        }

        public function grabarGuiaManual($guia,$form,$detalles,$operacion){
            $mensaje = "error de creacion";
            $guiaAutomatica = "";

            try {
                if ( $operacion == 'n' ){
                    $guiaAutomatica = $this->numeroGuia();
                    $mensaje = "Se grabo la guia de remision";
                    
                    $this->grabarDatosDocumento($form,$detalles,$guiaAutomatica);
                    $this->grabarDatosGuia($guia,$form,$guiaAutomatica);

                }else if( $operacion == 'u' ){
                    $mensaje = "Se actualizo la guia de remision";
                }

                return array("mensaje"=>$mensaje,"guia"=>$guiaAutomatica);

            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function grabarDatosDocumento($formCab,$detalles,$guia){
            try {
                
                $fecha = explode("-",$formCab['fecha']);

                $sql = $this->db->connect()->prepare("INSERT INTO alm_desplibrescab SET ntipmov = :ntipmov,
                                                                                        nnromov = :nnromov,
                                                                                        cper = :cper,
                                                                                        cmes = :cmes,
                                                                                        ncodalm1 = :ncodalm1,
                                                                                        ncodalm2 = :ncodalm2,
                                                                                        ffecdoc = :ffecdoc,
                                                                                        ncodpry = :ncodpry,
                                                                                        nnronota=:nnronota,
                                                                                        id_userAprob = :id_userAprob,
                                                                                        id_userElabora = :id_user,
                                                                                        nEstadoDoc = :nEstadoDoc,
                                                                                        nflgactivo = :nflgactivo,
                                                                                        cnumguia =:guia");

                $sql->execute(["ntipmov"=>$formCab['codigo_movimiento'],
                                "nnromov"=>null,
                                "cper"=>$fecha[0],
                                "cmes"=>$fecha[1],
                                "ncodalm1"=>$formCab['codigo_almacen_origen'],
                                "ncodalm2"=>$formCab['codigo_almacen_destino'],
                                "ffecdoc"=>$formCab['fecha'],
                                "ncodpry"=>$formCab['codigo_costos'],
                                "nnronota"=>null,
                                "id_userAprob"=>$formCab['codigo_aprueba'],
                                "nEstadoDoc"=>62,
                                "nflgactivo"=>1,
                                "id_user"=>$_SESSION['iduser'],
                                "guia"=>$guia]);

                $rowCount = $sql->rowCount();
                
                //var_dump($sql->errorInfo());

                if ($rowCount > 0) {
                    $indice = $this->lastInsertId("SELECT COUNT(id_regalm) AS id FROM alm_desplibrescab");
                    $this->grabarDetalles($indice,$detalles,$formCab['codigo_almacen_origen']);
                }
                
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function grabarDetalles($indice,$detalles,$almacen){
            try {
                $datos = json_decode($detalles);
                $nreg = count($datos);

                for ($i=0; $i < $nreg; $i++) { 
                    try {
                        $sql=$this->db->connect()->prepare("INSERT INTO alm_desplibresdet SET id_regalm=:cod,
                                                                                            ncodalm1=:ori,
                                                                                            cCodigo=:cpro,
                                                                                            ncantidad=:cant,
                                                                                            niddetaPed=:idpedido,
                                                                                            niddetaOrd=:idorden,
                                                                                            nflgactivo=:flag,
                                                                                            nestadoreg=:estadoItem,
                                                                                            ingreso=:ingreso,
                                                                                            ncodalm2=:destino,
                                                                                            niddetaIng=:itemIngreso,
                                                                                            nroorden=:orden,
                                                                                            nropedido=:pedido,
                                                                                            ndespacho=:candesp,
                                                                                            cobserva=:observac,
                                                                                            cDescripcion=:descripcion");
                         $sql->execute(["cod"=>$indice,
                                        "ori"=>$almacen,
                                        "cpro"=>$datos[$i]->codigo,
                                        "cant"=>$datos[$i]->cantidad,
                                        "idpedido"=>$datos[$i]->iddetped,
                                        "idorden"=>$datos[$i]->iddetorden,
                                        "flag"=>1,
                                        "estadoItem"=>32,
                                        "ingreso"=>null,
                                        "destino"=>$datos[$i]->destino,
                                        "candesp"=>$datos[$i]->cantdesp,
                                        "itemIngreso"=>null,
                                        "pedido"=>$datos[$i]->pedido,
                                        "orden"=>$datos[$i]->orden,
                                        "observac"=>$datos[$i]->obser,
                                        "descripcion"=>$datos[$i]->descripcion]);
                    } catch (PDOException $th) {
                        echo $th->getMessage();
                        return false;
                    }
                }

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function grabarDatosGuia($guiaCab,$formCab,$nroguia){
            try {
                $sql = $this->db->connect()->prepare("INSERT INTO lg_guias SET id_regalm=:despacho,cnumguia=:guia,corigen=:origen,
                                                                                cdirorigen=:direccion_origen,cdestino=:destino,
                                                                                cdirdest=:direccion_destino,centi=:entidad,centidir=:direccion_entidad,
                                                                                centiruc=:ruc_entidad,ctraslado=:traslado,cenvio=:envio,
                                                                                cautoriza=:autoriza,cdestinatario=:destinatario,cobserva=:observaciones,
                                                                                cnombre=:nombres,cmarca=:marca,clicencia=:licencia,cplaca=:placa,
                                                                                ftraslado=:fecha_traslado,fguia=:fecha_guia,cserie=:serie");

                $sql->execute([ "despacho"=>null,
                                "guia"=>$nroguia,
                                "origen"=>$guiaCab['almacen_origen'],
                                "direccion_origen"=>$guiaCab['almacen_origen_direccion'],
                                "destino"=>$guiaCab['almacen_destino'],
                                "direccion_destino"=>$guiaCab['almacen_destino_direccion'],
                                "entidad"=>$guiaCab['empresa_transporte_razon'],
                                "direccion_entidad"=>$guiaCab['direccion_proveedor'],
                                "ruc_entidad"=>$guiaCab['ruc_proveedor'],
                                "traslado"=>$guiaCab['modalidad_traslado'],
                                "envio"=>$guiaCab['tipo_envio'],
                                "autoriza"=>$guiaCab['autoriza'],
                                "destinatario"=>$guiaCab['destinatario'],
                                "observaciones"=>$guiaCab['observaciones'],
                                "nombres"=>$guiaCab['nombre_conductor'],
                                "marca"=>$guiaCab['marca'],
                                "licencia"=>$guiaCab['licencia_conducir'],
                                "placa"=>$guiaCab['placa'],
                                "fecha_traslado"=>$guiaCab['ftraslado'],
                                "fecha_guia"=>$guiaCab['fgemision'],
                                "serie"=>'F001']);
                
                $rowCount = $sql->rowcount();

                if ($rowCount > 0) {
                    $mensaje = "Registro grabado";
                }else {
                    $mensaje = "Error al crear el registro";
                }

                return $mensaje;                
            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }
    }
?>