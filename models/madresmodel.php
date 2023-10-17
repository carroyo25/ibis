<?php
    class MadresModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function importarGuias($cc,$guia){
            try {
                $salida = "";

                $g = $guia == "" ? "%": "%".$guia."%";

                $sql = $this->db->connect()->prepare("SELECT
                                                lg_guias.cnumguia,
                                                alm_despachocab.ncodpry,
                                                tb_proyectos.ccodproy,
                                                UPPER(tb_proyectos.cdesproy) AS cdesproy,
	                                            lg_guias.id_regalm,
                                                DATE_FORMAT(alm_despachocab.ffecdoc,'%d/%m/%Y') AS ffecdoc  
                                            FROM
                                                lg_guias
                                                LEFT JOIN alm_despachocab ON lg_guias.id_regalm = alm_despachocab.id_regalm
                                                INNER JOIN tb_proyectos ON alm_despachocab.ncodpry = tb_proyectos.nidreg 
                                            WHERE
                                                lg_guias.cnumguia <> ''
                                                AND alm_despachocab.nflgactivo = 1 
                                                AND lg_guias.cnumguia LIKE :guia 
                                                AND alm_despachocab.ncodpry LIKE :costos 
                                                AND lg_guias.flgmadre = 0");
                
                $sql->execute(["guia"=>$g,"costos"=>$cc]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()) {
                        $salida .='<tr data-despacho="'.$rs['id_regalm'].'" data-guia="'.$rs['cnumguia'].'">
                                        <td class="textoCentro">'.$rs['cnumguia'].'</td>
                                        <td class="textoCentro">'.$rs['ffecdoc'].'</td>
                                        <td class="pl10px">'.$rs['cdesproy'].'</td>
                                        <td class="textoCentro"><button>Seleccionar</button></td>
                                    </tr>';
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function importarItemsDespacho($idx){
            try {
                $salida = "";

                $sql = $this->db->connect()->prepare("SELECT
                                                        UPPER( cm_producto.ccodprod ) AS cccodprod,
                                                        UPPER( cm_producto.cdesprod ) AS cdesprod,
                                                        alm_despachodet.ncantidad,
                                                        tb_unimed.cabrevia,
                                                        alm_despachodet.id_regalm,
                                                        alm_despachodet.niddeta,
                                                        alm_despachocab.cnumguia 
                                                    FROM
                                                        alm_despachodet
                                                        INNER JOIN cm_producto ON alm_despachodet.id_cprod = cm_producto.id_cprod
                                                        INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed
                                                        INNER JOIN alm_despachocab ON alm_despachodet.id_regalm = alm_despachocab.id_regalm 
                                                    WHERE
                                                        alm_despachodet.id_regalm = :id 
                                                        AND alm_despachodet.nflgactivo = 1");
                
                $sql->execute(["id"=>$idx]);
                $rowCount = $sql->rowCount();

                $item = 0;

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()) {
                        $salida .='<tr data-despacho="'.$rs['id_regalm'].'" data-itemdespacho="'.$rs['niddeta'].'">
                                        <td class="textoCentro">'.str_pad($item++,3,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.$rs['cccodprod'].'</td>
                                        <td class="pl10px">'.$rs['cdesprod'].'</td>
                                        <td class="textoCentro">'.$rs['cabrevia'].'</td>
                                        <td class="textoDerecha">'.$rs['ncantidad'].'</td>
                                        <td class="textoDerecha">'.$rs['cnumguia'].'</td>
                                    </tr>';
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function grabarGuia($datos){
            $detalles = json_decode($datos['detalles']);
            $nreg = count($detalles);

            $mensaje = "Error, no se grabaron los datos";
            $clase = "mensaje error";

           
            try {
                $sql = $this->db->connect()->prepare("INSERT INTO lg_guiamadre SET ffecdoc =:emision,
                                                                            ffectraslado = :traslado,
                                                                            cnroguia = :guia,
                                                                            ncostos =:costos,
                                                                            nlamorigen =:origen,
                                                                            nalmdestino = :destino,
                                                                            nentitrans = :transporte,
                                                                            nmottranp = :motivo,
                                                                            ntipmov = :tipo,
                                                                            clincencia =:licencia,
                                                                            ndni =:dni,
                                                                            cmarca =:marca,
                                                                            cplaca =:placa,
                                                                            npeso =:peso,
                                                                            nbultos =:bultos,
                                                                            useremit =:user,
                                                                            idaprueba =:aprueba");
            
                $sql->execute(["emision"=>$datos['emision'],
                                "traslado"=>$datos['traslado'],
                                "guia"=>$datos['guia'],
                                "costos"=>$datos['costos'],
                                "origen"=>$datos['alm_origen'],
                                "destino"=>$datos['alm_destino'],
                                "transporte"=>$datos['transportista'],
                                "motivo"=>$datos['modalidad'],
                                "tipo"=>$datos['tipo'],
                                "licencia"=>$datos['licencia'],
                                "dni"=>$datos['dni'],
                                "marca"=>$datos['marca'],
                                "placa"=>$datos['placa'],
                                "peso"=>$datos['peso'],
                                "bultos"=>$datos['bultos'],
                                "user"=>$datos['useremit'],
                                "aprueba"=>$datos['aprueba']]);

                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    $indice = $this->generarNumeroSunat();
                    $this->grabarDetallesGuia($indice,$detalles,$datos['guia']);
                    $mensaje = "Guia grabada correctamente";
                    $clase = "mensaje_correcto";
                }

                return array("guia"=>$datos['guia'],"mensaje"=>$mensaje,"clase" => $clase );
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function grabarDetallesGuia($indice,$detalles,$guiaMadre){
            $nreg = count($detalles);

            for ($i=0; $i < $nreg; $i++) { 
                try {
                    $sql=$this->db->connect()->prepare("INSERT INTO lg_detallemadres SET ndespacho=:despacho,
                                                                                          itemdespacho=:item,
                                                                                          idguiasunat=:indice");
                    
                    $sql->execute(["indice"=>$indice,"item"=>$detalles[$i]->iddespacho,"despacho"=>$detalles[$i]->despacho]);

                    $rowCount = $sql->rowCount();

                    if ($rowCount > 0) {
                        $this->actualizaEstadoGuia($detalles[$i]->despacho,$guiaMadre);
                    }
                } catch (PDOException $th) {
                    echo $th->getMessage();
                    return false;
                }
            }
        }

        private function generarNumeroSunat(){
            try {
                $sql = $this->db->connect()->query("SELECT MAX(idreg) AS numero FROM lg_guiamadre");
                $sql->execute();

                $result = $sql->fetchAll();
                
                return $result[0]['numero'];
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function listarGuiasScroll($pagina,$cantidad){
            try {
                $inicio = ($pagina - 1) * $cantidad;
                $limite = $this->contarItems();

                if ($limite < 30) {
                    $cantidad = $limite;
                }

                $sql = $this->db->connect()->prepare("SELECT
                                                            lg_guiamadre.idreg,
                                                            lg_guiamadre.cnroguia,
                                                            lg_guiamadre.nflgSunat,
                                                            DATE_FORMAT( lg_guiamadre.ffecdoc, '%d/%m/%Y' ) AS emision,
                                                            DATE_FORMAT( lg_guiamadre.ffectraslado, '%d/%m/%Y' ) AS traslado,
                                                            UPPER( origen.cdesalm ) AS almacen_origen,
                                                            UPPER( destino.cdesalm ) AS almacen_destino 
                                                        FROM
                                                            lg_guiamadre
                                                            LEFT JOIN tb_almacen AS origen ON lg_guiamadre.nlamorigen = origen.ncodalm
                                                            LEFT JOIN tb_almacen AS destino ON lg_guiamadre.nalmdestino = destino.ncodalm
                                                        WHERE lg_guiamadre.nflgActivo = 1
                                                        LIMIT $inicio,$cantidad");
                
                $sql->execute();

                $rc = $sql->rowcount();

                if ($rc > 0){
                    while( $rs = $sql->fetch()) {
                        $guias[] = $rs;
                    }
                }

                if ($limite  > 30) {
                    if ( ($inicio + $cantidad) < $limite ){
                        $quedan = true;
                    }
                }else {
                    $quedan = false;
                }

                

                return array("guias"=>$guias,
                            'quedan'=> $quedan);

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function contarItems(){
            try {
                $sql = $this->db->connect()->query("SELECT COUNT(*) AS regs FROM lg_guiamadre WHERE nflgActivo = 1");
                $sql->execute();
                $filas = $sql->fetch();

                return $filas['regs'];
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function actualizaEstadoGuia($despacho,$guiaMadre){
            try {
                $sql = $this->db->connect()->prepare("UPDATE lg_guias SET lg_guias.flgmadre = 1,lg_guias.cnumadre=:guia WHERE id_regalm =:despacho");
                $sql->execute(["guia"=>$guiaMadre,"despacho"=>$despacho]);
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function guiaMadreID($id) {
            try {
                $sql= $this->db->connect()->prepare("SELECT
                                                    lg_guiamadre.idreg,
                                                    tb_user.cnombres,
                                                    lg_guiamadre.ffecdoc,
                                                    lg_guiamadre.ffectraslado,
                                                    UPPER( origen.cdesalm ) AS origen,
                                                    UPPER( destino.cdesalm ) AS destino,
                                                    lg_guiamadre.nflgSunat 
                                                FROM
                                                    lg_guiamadre
                                                    LEFT JOIN tb_user ON lg_guiamadre.idaprueba = tb_user.iduser
                                                    INNER JOIN tb_almacen AS origen ON lg_guiamadre.nlamorigen = origen.ncodalm
                                                    INNER JOIN tb_almacen AS destino ON lg_guiamadre.nlamorigen = destino.ncodalm 
                                                WHERE
                                                    lg_guiamadre.nflgActivo = 1
                                                    AND lg_guiamadre.idreg = 1");
                
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function datosGuia($id){

        }
    }
?>